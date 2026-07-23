<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cron - scheduled background jobs for Dyafa Sales OS.
 *
 * Extends CI_Controller directly (NOT Dso_Controller / MY_Controller) since
 * this is invoked from CLI (php index.php cron dso_generate_notifications)
 * or a scheduled URL hit with no logged-in session.
 *
 * NOTE ON DELIVERY CHANNEL: this only writes to the in-app dso_notifications
 * table. A real implementation would additionally call an email/SMS/push
 * provider here to actually deliver the notification - that delivery step
 * is out of scope for this build and is intentionally not implemented.
 */
class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function dso_generate_notifications()
    {
        $this->_notify_new_leads();
        $this->_notify_collections_due();
        $this->_notify_contracts_expiring();
        $this->_notify_target_achievement();
        $this->_notify_guest_complaints();
        $this->_notify_proposal_pending();
        $this->_notify_vip_arrival();

        echo "DSO notifications generation run complete." . PHP_EOL;
    }

    /**
     * Generates AI Sales Assistant recommendations. Thin wrapper: all loop
     * logic (find candidates, de-dup, build recommendation, optional LLM
     * enhancement, insert) lives in
     * Dso_sales_assistant::generate_all_recommendations() so this cron job
     * and the "Generate Now" admin action (AiAssistant::generate()) share
     * one implementation with no duplicated logic.
     */
    public function dso_generate_ai_recommendations()
    {
        $this->load->library('dso_sales_assistant');
        $counts = $this->dso_sales_assistant->generate_all_recommendations();

        echo "DSO AI recommendation generation run complete "
            . "(inactive_accounts=" . $counts['inactive_accounts']
            . ", contract_renewals=" . $counts['contract_renewals']
            . ", predictions=" . $counts['predictions']
            . ", next_best_actions=" . $counts['next_best_actions'] . ")." . PHP_EOL;
    }

    /**
     * AI Lead Generation (BRD Section 7). SYNTHETIC PLACEHOLDER - see the
     * class doc-block on Dso_lead_generator.php: no real data-acquisition
     * provider is contracted, this synthesizes candidates from the seeded
     * dso_market_intelligence signals and scores them through the existing
     * Dso_lead_scoring heuristic unchanged. De-duped by company_name so
     * repeated cron runs don't pile up duplicate synthetic leads.
     */
    public function dso_generate_leads()
    {
        $this->load->library('dso_lead_generator');
        $counts = $this->dso_lead_generator->generate();

        echo "DSO AI lead generation run complete (inserted=" . $counts['inserted']
            . " of " . $counts['candidates'] . " candidates, synthetic placeholder data - see Dso_lead_generator.php)." . PHP_EOL;
    }

    /** (a) leads created in last 24h, status New, not yet notified. */
    protected function _notify_new_leads()
    {
        $leads = $this->db->where('status', 'New')
            ->where('notified', 0)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->get('dso_leads')->result();

        foreach ($leads as $lead) {
            if ($lead->lead_owner_id) {
                $this->db->insert('dso_notifications', array(
                    'user_id'    => $lead->lead_owner_id,
                    'role'       => null,
                    'type'       => 'new_lead',
                    'message'    => 'New lead assigned: ' . $lead->company_name . ' (score ' . $lead->lead_score . ', ' . $lead->lead_category . ').',
                    'is_read'    => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ));
            }
            $this->db->where('id', $lead->id)->update('dso_leads', array('notified' => 1));
        }
    }

    /** (b) collections pending/overdue due today or earlier, de-duped by invoice_no in message. */
    protected function _notify_collections_due()
    {
        $rows = $this->db->where_in('status', array('Pending', 'Overdue'))
            ->where('due_date <=', date('Y-m-d'))
            ->get('dso_collections')->result();

        foreach ($rows as $c) {
            $exists = $this->db->where('type', 'payment_pending')
                ->like('message', $c->invoice_no)
                ->count_all_results('dso_notifications') > 0;
            if ($exists) {
                continue;
            }
            $account = $this->db->where('id', $c->account_id)->get('dso_accounts')->row();
            if (!$account || !$account->account_owner_id) {
                continue;
            }
            $this->db->insert('dso_notifications', array(
                'user_id'    => $account->account_owner_id,
                'role'       => null,
                'type'       => 'payment_pending',
                'message'    => 'Payment pending for invoice ' . $c->invoice_no . ' (account: ' . $account->company_name . '), due ' . $c->due_date . '.',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }

    /** (c) contracts expiring within 30 days, de-duped by contract_number in message. */
    protected function _notify_contracts_expiring()
    {
        $sql = "SELECT * FROM dso_contracts
                WHERE status = 'Active'
                AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        $rows = $this->db->query($sql)->result();

        foreach ($rows as $c) {
            $exists = $this->db->where('type', 'contract_expiring')
                ->like('message', $c->contract_number)
                ->count_all_results('dso_notifications') > 0;
            if ($exists || !$c->account_manager_id) {
                continue;
            }
            $this->db->insert('dso_notifications', array(
                'user_id'    => $c->account_manager_id,
                'role'       => null,
                'type'       => 'contract_expiring',
                'message'    => 'Contract ' . $c->contract_number . ' (' . $c->company_name . ') expires on ' . $c->expiry_date . '.',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }

    /** (d) target achievement >= 100%, once per month per user. */
    protected function _notify_target_achievement()
    {
        $this->load->model('dyafa/Dso_targets_model');
        $month = date('Y-m');
        $targets = $this->db->where('month', $month)->get('dso_targets')->result();

        foreach ($targets as $t) {
            $needle = 'user #' . $t->user_id . ' month ' . $month;
            $exists = $this->db->where('type', 'target_achieved')
                ->like('message', $needle)
                ->count_all_results('dso_notifications') > 0;
            if ($exists) {
                continue;
            }
            $perf = $this->Dso_targets_model->performance($t->user_id, $month);
            if ($perf && $perf['overall_pct'] >= 100) {
                $this->db->insert('dso_notifications', array(
                    'user_id'    => $t->user_id,
                    'role'       => null,
                    'type'       => 'target_achieved',
                    'message'    => 'Congratulations! You achieved ' . $perf['overall_pct'] . '% of your target for ' . $month . ' (user #' . $t->user_id . ' month ' . $month . ').',
                    'is_read'    => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ));
            }
        }
    }

    /** (e) guest complaints logged as an activity, de-duped by "activity #<id>" in message. */
    protected function _notify_guest_complaints()
    {
        $rows = $this->db->where('activity_type', 'Complaint')
            ->get('dso_activities')->result();

        foreach ($rows as $a) {
            $needle = 'activity #' . $a->id;
            $exists = $this->db->where('type', 'guest_complaint')
                ->like('message', $needle)
                ->count_all_results('dso_notifications') > 0;
            if ($exists) {
                continue;
            }
            $account = $this->db->where('id', $a->account_id)->get('dso_accounts')->row();
            if (!$account || !$account->account_owner_id) {
                continue;
            }
            $this->db->insert('dso_notifications', array(
                'user_id'    => $account->account_owner_id,
                'role'       => null,
                'type'       => 'guest_complaint',
                'message'    => 'Complaint logged for ' . $account->company_name . ' (activity #' . $a->id . '): ' . mb_substr((string) $a->notes, 0, 150) . '.',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }

    /** (f) leads/adhoc opportunities sitting at ProposalSent, awaiting approval/response. */
    protected function _notify_proposal_pending()
    {
        $leads = $this->db->where('status', 'ProposalSent')->get('dso_leads')->result();
        foreach ($leads as $lead) {
            $needle = 'lead #' . $lead->id;
            $exists = $this->db->where('type', 'proposal_pending')
                ->like('message', $needle)
                ->count_all_results('dso_notifications') > 0;
            if ($exists || !$lead->lead_owner_id) {
                continue;
            }
            $this->db->insert('dso_notifications', array(
                'user_id'    => $lead->lead_owner_id,
                'role'       => null,
                'type'       => 'proposal_pending',
                'message'    => 'Proposal pending approval for lead #' . $lead->id . ' (' . $lead->company_name . ').',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }

        $adhoc = $this->db->where('status', 'ProposalSent')->get('dso_adhoc_sales')->result();
        foreach ($adhoc as $event) {
            $needle = 'adhoc #' . $event->id;
            $exists = $this->db->where('type', 'proposal_pending')
                ->like('message', $needle)
                ->count_all_results('dso_notifications') > 0;
            if ($exists || !$event->owner_id) {
                continue;
            }
            $this->db->insert('dso_notifications', array(
                'user_id'    => $event->owner_id,
                'role'       => null,
                'type'       => 'proposal_pending',
                'message'    => 'Proposal pending approval for adhoc #' . $event->id . ' (' . $event->event_type . ' on ' . $event->event_date . ').',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }

    /** (g) VIP account arrivals checking in today, de-duped by "reservation #<id>" in message. */
    protected function _notify_vip_arrival()
    {
        $sql = "SELECT r.* FROM dso_reservations r
                INNER JOIN dso_accounts a ON a.id = r.account_id
                WHERE a.is_vip = 1
                AND r.check_in = CURDATE()
                AND r.status NOT IN ('Cancelled', 'NoShow')";
        $rows = $this->db->query($sql)->result();

        foreach ($rows as $r) {
            $needle = 'reservation #' . $r->id;
            $exists = $this->db->where('type', 'vip_arrival')
                ->like('message', $needle)
                ->count_all_results('dso_notifications') > 0;
            if ($exists) {
                continue;
            }
            $account = $this->db->where('id', $r->account_id)->get('dso_accounts')->row();
            if (!$account || !$account->account_owner_id) {
                continue;
            }
            $this->db->insert('dso_notifications', array(
                'user_id'    => $account->account_owner_id,
                'role'       => null,
                'type'       => 'vip_arrival',
                'message'    => 'VIP guest arrival today: ' . $account->company_name . ' at ' . $r->property . ' (reservation #' . $r->id . ').',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }
}
