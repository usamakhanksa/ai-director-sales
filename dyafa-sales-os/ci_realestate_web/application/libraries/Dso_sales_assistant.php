<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_sales_assistant
 *
 * HEURISTIC RULE-BASED PLACEHOLDER — same documented pattern as
 * Dso_lead_scoring.php: deterministic SQL/formula logic standing in for a
 * future real AI/ML recommendation service. No external model is called.
 * Interface kept stable so callers (Cron::dso_generate_ai_recommendations())
 * don't need to change when a real service is introduced later.
 *
 * BRD example this mirrors: "ABC Construction — no reservation for 45 days —
 * recommend: arrange meeting, offer long stay package, suggested property,
 * estimated revenue, priority."
 */
class Dso_sales_assistant
{
    protected $revenue_fallback_ratio = 0.10; // documented heuristic: 10% of credit limit when no reservation history exists
    protected $revenue_flat_fallback  = 5000.00; // documented heuristic: used when there is neither reservation history nor a linked contract

    /**
     * Active accounts whose most recent reservation checkout (or account
     * creation date, if the account has never had a reservation) is at
     * least $days days in the past.
     *
     * @return array of stdClass account rows with an added ->days_inactive property
     */
    public function find_inactive_accounts($days = 45)
    {
        $ci = &get_instance();
        $sql = "SELECT a.*, MAX(r.check_out) AS last_checkout
                FROM dso_accounts a
                LEFT JOIN dso_reservations r ON r.account_id = a.id AND r.status NOT IN ('Cancelled','NoShow')
                WHERE a.status = 'Active'
                GROUP BY a.id";
        $rows = $ci->db->query($sql)->result();

        $today = strtotime(date('Y-m-d'));
        $result = array();
        foreach ($rows as $row) {
            $reference_date = $row->last_checkout ? $row->last_checkout : date('Y-m-d', strtotime($row->created_at));
            $days_inactive = (int) round(($today - strtotime($reference_date)) / 86400);
            if ($days_inactive >= $days) {
                $row->days_inactive = $days_inactive;
                $result[] = $row;
            }
        }
        return $result;
    }

    /** Thin wrapper reusing the existing contract-expiry query verbatim. */
    public function renewals_due($days = 30)
    {
        $ci = &get_instance();
        $ci->load->model('dyafa/Dso_contracts_model');
        return $ci->Dso_contracts_model->expiring_within_days($days);
    }

    /**
     * Trailing average of past reservation total_amount for the account
     * (excluding Cancelled/NoShow). Falls back to a documented percentage
     * of the linked contract's credit_limit, then to a flat default, when
     * there is no reservation history to average.
     */
    public function estimate_revenue($account_id)
    {
        $ci = &get_instance();
        $row = $ci->db->select_avg('total_amount')
            ->where('account_id', $account_id)
            ->where_not_in('status', array('Cancelled', 'NoShow'))
            ->get('dso_reservations')->row();

        if ($row && $row->total_amount !== null) {
            return round((float) $row->total_amount, 2);
        }

        $account = $ci->db->where('id', $account_id)->get('dso_accounts')->row();
        if ($account && $account->contract_id) {
            $contract = $ci->db->where('id', $account->contract_id)->get('dso_contracts')->row();
            if ($contract && $contract->credit_limit > 0) {
                return round($contract->credit_limit * $this->revenue_fallback_ratio, 2);
            }
        }
        return $this->revenue_flat_fallback;
    }

    /**
     * Suggests a property for the account: the property with the highest
     * historical revenue for that account, else the first property allowed
     * by its contract, else the first Active property in the master list.
     *
     * @return array('name' => string|null, 'id' => int|null)
     */
    public function suggest_property($account_id)
    {
        $ci = &get_instance();

        $row = $ci->db->select('property, SUM(total_amount) as total')
            ->where('account_id', $account_id)
            ->where_not_in('status', array('Cancelled', 'NoShow'))
            ->group_by('property')
            ->order_by('total', 'desc')
            ->limit(1)
            ->get('dso_reservations')->row();

        $name = $row ? $row->property : null;

        if (!$name) {
            $account = $ci->db->where('id', $account_id)->get('dso_accounts')->row();
            if ($account && $account->contract_id) {
                $contract = $ci->db->where('id', $account->contract_id)->get('dso_contracts')->row();
                if ($contract && !empty($contract->allowed_properties)) {
                    $allowed = array_values(array_filter(array_map('trim', explode(',', $contract->allowed_properties))));
                    if (!empty($allowed)) {
                        $name = $allowed[0];
                    }
                }
            }
        }

        if (!$name) {
            $p = $ci->db->where('status', 'Active')->order_by('name', 'asc')->limit(1)->get('dso_properties')->row();
            $name = $p ? $p->name : null;
        }

        $id = null;
        if ($name) {
            $p = $ci->db->where('name', $name)->get('dso_properties')->row();
            $id = $p ? $p->id : null;
        }

        return array('name' => $name, 'id' => $id);
    }

    /**
     * Active accounts with a computable booking cadence (2+ past
     * reservations) that are now overdue for their next stay relative to
     * their own historical average gap between checkouts. This is a real
     * prediction derived from the account's own reservation history -
     * accounts with fewer than 2 past reservations are skipped rather than
     * assigned a fabricated cadence.
     *
     * @return array of stdClass account rows with ->avg_gap_days and ->days_since_last
     */
    public function find_churn_risk_accounts($cadence_multiplier = 1.5)
    {
        $ci = &get_instance();
        $sql = "SELECT a.*, r.check_out
                FROM dso_accounts a
                JOIN dso_reservations r ON r.account_id = a.id AND r.status NOT IN ('Cancelled','NoShow')
                WHERE a.status = 'Active'
                ORDER BY a.id, r.check_out ASC";
        $rows = $ci->db->query($sql)->result();

        $by_account = array();
        foreach ($rows as $row) {
            if (!isset($by_account[$row->id])) {
                $by_account[$row->id] = array('account' => $row, 'checkouts' => array());
            }
            $by_account[$row->id]['checkouts'][] = $row->check_out;
        }

        $today = strtotime(date('Y-m-d'));
        $result = array();
        foreach ($by_account as $data) {
            $checkouts = $data['checkouts'];
            if (count($checkouts) < 2) {
                continue; // cannot compute a real cadence from a single data point - no fabricated prediction
            }
            $gaps = array();
            for ($i = 1; $i < count($checkouts); $i++) {
                $gaps[] = (strtotime($checkouts[$i]) - strtotime($checkouts[$i - 1])) / 86400;
            }
            $avg_gap = array_sum($gaps) / count($gaps);
            $days_since_last = ($today - strtotime(end($checkouts))) / 86400;

            if ($avg_gap > 0 && $days_since_last >= $avg_gap * $cadence_multiplier) {
                $account = $data['account'];
                $account->avg_gap_days = round($avg_gap, 1);
                $account->days_since_last = (int) round($days_since_last);
                $result[] = $account;
            }
        }
        return $result;
    }

    /**
     * Active accounts with no logged sales activity (call/meeting/visit/
     * follow-up) within $days days - a shorter, activity-based window than
     * find_inactive_accounts()'s 45-day reservation-based one, so the two
     * detectors surface different signals rather than duplicating each
     * other. Falls back to the account's created_at when it has no
     * activities logged at all.
     *
     * @return array of stdClass account rows with an added ->days_since_activity property
     */
    public function find_accounts_needing_next_action($days = 21)
    {
        $ci = &get_instance();
        $sql = "SELECT a.*, MAX(act.activity_date) AS last_activity_date
                FROM dso_accounts a
                LEFT JOIN dso_activities act ON act.account_id = a.id
                WHERE a.status = 'Active'
                GROUP BY a.id";
        $rows = $ci->db->query($sql)->result();

        $today = strtotime(date('Y-m-d'));
        $result = array();
        foreach ($rows as $row) {
            $reference_date = $row->last_activity_date ? $row->last_activity_date : $row->created_at;
            $days_since = (int) round(($today - strtotime($reference_date)) / 86400);
            if ($days_since >= $days) {
                $row->days_since_activity = $days_since;
                $result[] = $row;
            }
        }
        return $result;
    }

    /** Single assembly point for a predicted-churn-risk recommendation. */
    public function build_churn_risk_recommendation($account)
    {
        $estimated_revenue = $this->estimate_revenue($account->id);
        $property = $this->suggest_property($account->id);
        $priority = $this->priority_for($account->days_since_last, $estimated_revenue);

        return array(
            'account_id'            => $account->id,
            'type'                  => 'Prediction',
            'suggested_action'      => 'Reach out before the account\'s usual re-booking window closes.',
            'suggested_property_id' => $property['id'],
            'estimated_revenue'     => $estimated_revenue,
            'priority'              => $priority,
            'reason'                => 'Predicted churn risk: this account typically re-books every ' . $account->avg_gap_days
                . ' days on average, but it has been ' . $account->days_since_last . ' days since the last stay'
                . ($property['name'] ? '; suggested property: ' . $property['name'] : '') . '.',
        );
    }

    /** Single assembly point for a next-best-action recommendation. */
    public function build_next_best_action_recommendation($account)
    {
        $estimated_revenue = $this->estimate_revenue($account->id);
        $property = $this->suggest_property($account->id);
        $priority = $this->priority_for($account->days_since_activity, $estimated_revenue);

        return array(
            'account_id'            => $account->id,
            'type'                  => 'NextBestAction',
            'suggested_action'      => 'Log a follow-up call or visit to keep the relationship active.',
            'suggested_property_id' => $property['id'],
            'estimated_revenue'     => $estimated_revenue,
            'priority'              => $priority,
            'reason'                => 'No sales activity logged for ' . $account->days_since_activity . ' days'
                . ($property['name'] ? '; suggested property: ' . $property['name'] : '') . '.',
        );
    }

    /**
     * Priority banding (documented thresholds, same style as
     * Dso_lead_scoring::category_for_score()).
     */
    public function priority_for($days_inactive_or_to_expiry, $estimated_revenue)
    {
        if ($days_inactive_or_to_expiry >= 60 || $estimated_revenue >= 50000) {
            return 'High';
        }
        if ($days_inactive_or_to_expiry >= 30 || $estimated_revenue >= 20000) {
            return 'Medium';
        }
        return 'Low';
    }

    /** Single assembly point for an inactive-account recommendation. */
    public function build_inactive_account_recommendation($account)
    {
        $estimated_revenue = $this->estimate_revenue($account->id);
        $property = $this->suggest_property($account->id);
        $priority = $this->priority_for($account->days_inactive, $estimated_revenue);

        return array(
            'account_id'            => $account->id,
            'type'                  => 'InactiveAccount',
            'suggested_action'      => 'Arrange a meeting and offer a long-stay package.',
            'suggested_property_id' => $property['id'],
            'estimated_revenue'     => $estimated_revenue,
            'priority'              => $priority,
            'reason'                => 'No reservation for ' . $account->days_inactive . ' days' .
                ($property['name'] ? '; suggested property: ' . $property['name'] : '') . '.',
        );
    }

    /** Single assembly point for a contract-renewal recommendation. */
    public function build_contract_renewal_recommendation($contract, $account)
    {
        $days_to_expiry = (int) round((strtotime($contract->expiry_date) - strtotime(date('Y-m-d'))) / 86400);
        $estimated_revenue = $account ? $this->estimate_revenue($account->id) : (float) $contract->credit_limit * $this->revenue_fallback_ratio;
        $priority = $this->priority_for(60 - $days_to_expiry, $estimated_revenue); // fewer days left -> higher urgency

        return array(
            'account_id'            => $account ? $account->id : null,
            'type'                  => 'ContractRenewal',
            'suggested_action'      => 'Contact the account manager to begin contract renewal discussions.',
            'suggested_property_id' => null,
            'estimated_revenue'     => round($estimated_revenue, 2),
            'priority'              => $priority,
            'reason'                => 'Contract ' . $contract->contract_number . ' expires in ' . $days_to_expiry . ' days.',
            'days_to_expiry'        => $days_to_expiry,
        );
    }

    /**
     * Generates AI Sales Assistant recommendations end-to-end: the same two
     * loops formerly living in Cron::dso_generate_ai_recommendations()
     * (inactive accounts + contract renewals), moved here so both the cron
     * job and the "Generate Now" admin action (AiAssistant::generate())
     * share one implementation. Pure code motion - identical DB effects to
     * the original Cron.php loops, plus an added (optional, safe-by-default)
     * LLM enhancement pass on the free-text suggested_action/reason fields.
     *
     * The heuristic always computes every structured field first (account,
     * type, suggested_property_id, estimated_revenue, priority) exactly as
     * before - only the free-text fields are ever candidates for LLM
     * enhancement, and only when a default LLM provider is configured. See
     * Dso_llm_client::enhance_recommendation() for the always-falls-back-
     * safely contract: no provider / disabled / timeout / error -> the
     * heuristic's own text is used untouched.
     *
     * @return array('inactive_accounts' => int, 'contract_renewals' => int) counts inserted
     */
    public function generate_all_recommendations()
    {
        $ci = &get_instance();
        $ci->load->model('dyafa/Dso_ai_recommendations_model');
        $ci->load->model('dyafa/Dso_accounts_model');
        $ci->load->model('dyafa/Dso_contracts_model');
        $ci->load->library('dso_llm_client');

        $counts = array(
            'inactive_accounts'  => $this->_generate_inactive_account_recommendations(),
            'contract_renewals'  => $this->_generate_contract_renewal_recommendations(),
            'predictions'        => $this->_generate_churn_risk_recommendations(),
            'next_best_actions'  => $this->_generate_next_best_action_recommendations(),
        );
        return $counts;
    }

    /** (a) accounts with no reservation in 45+ days, de-duped by account+type within 30 days. */
    protected function _generate_inactive_account_recommendations()
    {
        $ci = &get_instance();
        $inserted = 0;
        $accounts = $this->find_inactive_accounts(45);
        foreach ($accounts as $account) {
            if ($ci->Dso_ai_recommendations_model->exists_recent($account->id, 'InactiveAccount', 30)) {
                continue;
            }
            $rec = $this->build_inactive_account_recommendation($account);
            $rec = $this->_enhance($ci, $rec, array(
                'type'              => $rec['type'],
                'account_name'      => $account->company_name,
                'days_figure'       => $account->days_inactive,
                'estimated_revenue' => $rec['estimated_revenue'],
                'property_name'     => null,
                'priority'          => $rec['priority'],
            ));
            unset($rec['days_to_expiry']);
            $rec['assigned_to'] = $account->account_owner_id;
            $rec['status'] = 'New';
            $rec['created_at'] = date('Y-m-d H:i:s');
            $ci->Dso_ai_recommendations_model->insert($rec);
            $inserted++;
        }
        return $inserted;
    }

    /** (b) contracts expiring within 30 days with a linked account, de-duped by account+type within 30 days. */
    protected function _generate_contract_renewal_recommendations()
    {
        $ci = &get_instance();
        $inserted = 0;
        $contracts = $this->renewals_due(30);
        foreach ($contracts as $contract) {
            if (!$contract->account_id) {
                // dso_ai_recommendations.account_id is NOT NULL - contracts with no linked
                // account (e.g. still in the sales pipeline) are skipped by design.
                continue;
            }
            if ($ci->Dso_ai_recommendations_model->exists_recent($contract->account_id, 'ContractRenewal', 30)) {
                continue;
            }
            $account = $ci->Dso_accounts_model->get($contract->account_id);
            $rec = $this->build_contract_renewal_recommendation($contract, $account);
            $days_to_expiry = $rec['days_to_expiry'];
            unset($rec['days_to_expiry']);
            $rec = $this->_enhance($ci, $rec, array(
                'type'              => $rec['type'],
                'account_name'      => $account ? $account->company_name : 'Unknown',
                'days_figure'       => $days_to_expiry,
                'estimated_revenue' => $rec['estimated_revenue'],
                'property_name'     => null,
                'priority'          => $rec['priority'],
            ));
            $rec['assigned_to'] = $contract->account_manager_id;
            $rec['status'] = 'New';
            $rec['created_at'] = date('Y-m-d H:i:s');
            $ci->Dso_ai_recommendations_model->insert($rec);
            $inserted++;
        }
        return $inserted;
    }

    /** (c) accounts overdue for their next stay per their own booking cadence, de-duped by account+type within 30 days. */
    protected function _generate_churn_risk_recommendations()
    {
        $ci = &get_instance();
        $inserted = 0;
        $accounts = $this->find_churn_risk_accounts();
        foreach ($accounts as $account) {
            if ($ci->Dso_ai_recommendations_model->exists_recent($account->id, 'Prediction', 30)) {
                continue;
            }
            $rec = $this->build_churn_risk_recommendation($account);
            $rec = $this->_enhance($ci, $rec, array(
                'type'              => $rec['type'],
                'account_name'      => $account->company_name,
                'days_figure'       => $account->days_since_last,
                'estimated_revenue' => $rec['estimated_revenue'],
                'property_name'     => null,
                'priority'          => $rec['priority'],
            ));
            $rec['assigned_to'] = $account->account_owner_id;
            $rec['status'] = 'New';
            $rec['created_at'] = date('Y-m-d H:i:s');
            $ci->Dso_ai_recommendations_model->insert($rec);
            $inserted++;
        }
        return $inserted;
    }

    /** (d) accounts overdue for a proactive sales touchpoint (activity-based, not reservation-based), de-duped by account+type within 30 days. */
    protected function _generate_next_best_action_recommendations()
    {
        $ci = &get_instance();
        $inserted = 0;
        $accounts = $this->find_accounts_needing_next_action();
        foreach ($accounts as $account) {
            if ($ci->Dso_ai_recommendations_model->exists_recent($account->id, 'NextBestAction', 30)) {
                continue;
            }
            $rec = $this->build_next_best_action_recommendation($account);
            $rec = $this->_enhance($ci, $rec, array(
                'type'              => $rec['type'],
                'account_name'      => $account->company_name,
                'days_figure'       => $account->days_since_activity,
                'estimated_revenue' => $rec['estimated_revenue'],
                'property_name'     => null,
                'priority'          => $rec['priority'],
            ));
            $rec['assigned_to'] = $account->account_owner_id;
            $rec['status'] = 'New';
            $rec['created_at'] = date('Y-m-d H:i:s');
            $ci->Dso_ai_recommendations_model->insert($rec);
            $inserted++;
        }
        return $inserted;
    }

    /**
     * Best-effort LLM enhancement of a single recommendation's free-text
     * fields. Resolves suggested_property_id -> a property name for the
     * prompt context, then delegates entirely to
     * Dso_llm_client::enhance_recommendation(), which never throws and
     * always returns a valid $rec (heuristic text untouched on any failure).
     */
    protected function _enhance($ci, array $rec, array $context)
    {
        $ci->load->model('dyafa/Dso_properties_model');
        if (!empty($rec['suggested_property_id'])) {
            $property = $ci->Dso_properties_model->get($rec['suggested_property_id']);
            $context['property_name'] = $property ? $property->name : null;
        }

        $rec = $ci->dso_llm_client->enhance_recommendation($rec, $context);
        $rec = $this->_apply_llm_candidate_fields($ci, $rec, $context);
        return $rec;
    }

    /**
     * Optional, always-validated LLM candidate for suggested_property_id/
     * priority (see Dso_llm_client::suggest_property_and_priority()). The
     * LLM never gets to invent estimated_revenue, and its property/priority
     * picks are only ever applied after being clamped against real data:
     * the property must be an actual Active property name, the priority
     * must be one of Low/Medium/High. Any mismatch is logged and the
     * heuristic's own value is kept untouched - same fail-safe contract as
     * enhance_recommendation().
     */
    protected function _apply_llm_candidate_fields($ci, array $rec, array $context)
    {
        $active_names = array_map(function ($p) { return $p->name; }, $ci->Dso_properties_model->get_all('Active'));

        $candidate = $ci->dso_llm_client->suggest_property_and_priority($rec, $context, $active_names);
        if (!$candidate) {
            return $rec;
        }

        if (!empty($candidate['property_name'])) {
            if (in_array($candidate['property_name'], $active_names, true)) {
                $property = current(array_filter($ci->Dso_properties_model->get_all('Active'), function ($p) use ($candidate) {
                    return $p->name === $candidate['property_name'];
                }));
                if ($property) {
                    $rec['suggested_property_id'] = $property->id;
                }
            } else {
                log_message('info', 'DSO LLM candidate property "' . $candidate['property_name'] . '" is not an active property - ignored, heuristic value kept.');
            }
        }

        if (!empty($candidate['priority'])) {
            if (in_array($candidate['priority'], array('Low', 'Medium', 'High'), true)) {
                $rec['priority'] = $candidate['priority'];
            } else {
                log_message('info', 'DSO LLM candidate priority "' . $candidate['priority'] . '" is not a valid priority - ignored, heuristic value kept.');
            }
        }

        return $rec;
    }
}
