<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_lead_generator
 *
 * HEURISTIC / SYNTHETIC-DATA PLACEHOLDER for the BRD's "AI Lead Generation"
 * module (500 qualified leads from industries/locations/company size/market
 * intelligence). This is explicitly NOT a real data-acquisition integration:
 * no web scraping, no paid provider (Apollo/ZoomInfo-type API) is called.
 * Sourcing 500 genuinely real qualified leads requires either a paid data
 * provider or legally-reviewed scraping - a business/budget decision the
 * user has not made yet (see enhance.md's own caution on this point).
 *
 * What this DOES do: reads deterministic seed signals from
 * dso_market_intelligence (industry/city/company-size/avg revenue/avg room
 * nights/signal strength - all synthetic placeholder rows shipped in the
 * schema) and synthesizes plausible candidate dso_leads rows with
 * source = 'AI Generated', then scores them through the existing
 * Dso_lead_scoring heuristic unchanged - so the rest of the pipeline (lead
 * assignment, scoring bands, notifications) is exercised end-to-end with
 * clearly-labeled synthetic data until a real provider is contracted.
 *
 * Swap-out path: once a real lead-sourcing provider is chosen, replace
 * generate_candidates() with real API calls; keep the returned array shape
 * (company_name/industry/city/estimated_revenue/estimated_room_nights/
 * priority/source) identical so Cron::dso_generate_leads() doesn't change.
 */
class Dso_lead_generator
{
    /** Company name fragments used only to synthesize plausible-looking candidate names - not real company data. */
    protected $name_suffixes = array('Trading Co.', 'Holding Group', 'Est.', 'Contracting Co.', 'Industries', 'Group');

    /**
     * @param int $per_signal how many candidate leads to synthesize per active market_intelligence row
     * @return array of candidate lead data arrays (not yet scored, not yet inserted)
     */
    public function generate_candidates($per_signal = 2)
    {
        $ci = &get_instance();
        $ci->load->model('dyafa/Dso_market_intelligence_model');
        $signals = $ci->Dso_market_intelligence_model->get_active();

        $candidates = array();
        foreach ($signals as $signal) {
            for ($i = 0; $i < $per_signal; $i++) {
                $candidates[] = $this->_synthesize($signal);
            }
        }
        return $candidates;
    }

    /**
     * Full generation pipeline: synthesize candidates, de-dup by
     * company_name against existing dso_leads, score each candidate through
     * Dso_lead_scoring (discarding anything that scores 'Discard'), assign
     * to the first HOD Sales user found, and insert. Shared by
     * Cron::dso_generate_leads() (scheduled) and
     * LeadGeneration::generate() (synchronous "Generate Now" admin action)
     * so there is exactly one implementation of this logic.
     *
     * @param int $per_signal how many candidate leads to synthesize per active market_intelligence row
     * @return array array('inserted' => int, 'candidates' => int)
     */
    public function generate($per_signal = 2)
    {
        $ci = &get_instance();
        $ci->load->library('dso_lead_scoring');
        $ci->load->model('dyafa/Dso_leads_model');
        $ci->load->model('dyafa/Dso_users_model');

        $hod = $ci->Dso_users_model->first_by_role('HOD Sales');
        $candidates = $this->generate_candidates($per_signal);
        $inserted = 0;

        foreach ($candidates as $candidate) {
            if ($ci->Dso_leads_model->company_name_exists($candidate['company_name'])) {
                continue;
            }
            $scoring = $ci->dso_lead_scoring->score_lead($candidate);
            if ($scoring['category'] === 'Discard') {
                continue;
            }
            $data = array_merge($candidate, array(
                'lead_owner_id'         => $hod ? $hod->id : null,
                'lead_score'            => $scoring['score'],
                'lead_category'         => $scoring['category'],
                'suggested_next_action' => $scoring['suggested_next_action'],
                'notified'              => 0,
                'created_at'            => date('Y-m-d H:i:s'),
            ));
            $ci->Dso_leads_model->insert($data);
            $inserted++;
        }

        return array(
            'inserted'   => $inserted,
            'candidates' => count($candidates),
        );
    }

    protected function _synthesize($signal)
    {
        $suffix = $this->name_suffixes[array_rand($this->name_suffixes)];
        $company_name = $signal->industry . ' ' . $suffix . ' - ' . $signal->city . ' #' . rand(100, 999);

        // +/- 20% jitter around the signal's average, so candidates aren't identical to one another.
        $revenue_jitter = 1 + (rand(-20, 20) / 100);
        $nights_jitter = 1 + (rand(-20, 20) / 100);

        $priority = 'Medium';
        if ($signal->signal_strength >= 75) {
            $priority = 'High';
        } elseif ($signal->signal_strength < 45) {
            $priority = 'Low';
        }

        return array(
            'company_name'          => $company_name,
            'industry'              => $signal->industry,
            'city'                  => $signal->city,
            'estimated_revenue'     => round($signal->avg_estimated_revenue * $revenue_jitter, 2),
            'estimated_room_nights' => (int) round($signal->avg_estimated_room_nights * $nights_jitter),
            'priority'              => $priority,
            'source'                => 'AI Generated',
            'status'                => 'New',
        );
    }
}
