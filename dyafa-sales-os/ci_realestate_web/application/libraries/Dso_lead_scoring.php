<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_lead_scoring
 *
 * HEURISTIC RULE-BASED PLACEHOLDER — replace with a real AI/ML scoring
 * service integration in future (e.g. call an external model API here);
 * interface kept stable so callers don't need to change.
 *
 * Scoring formula (deterministic, documented):
 *   revenue_component      = min(estimated_revenue / 500000, 1) * weight('estimated_revenue')
 *   room_nights_component  = min(estimated_room_nights / 500, 1) * weight('estimated_room_nights')
 *   priority_component     = (priority_ratio) * weight('priority')                where priority_ratio is
 *                            High=1.0, Medium=0.6, Low=0.25 (20/12/5 out of a max of 20, unchanged from before)
 *   source_component       = (source_ratio) * weight('market_intelligence_match') where source_ratio is
 *                            Referral=1.0, Partner=0.8, Event=0.667, Website=0.533, ColdCall=0.333, Other=0.2
 *                            (15/12/10/8/5/3 out of a max of 15, unchanged from before)
 *   total score = sum of the above, rounded, capped at 100.
 *
 * Weights are read from dso_lead_scoring_config (Administration > AI Lead
 * Generation > Lead Scoring Config, editable by an HOD) via
 * Dso_lead_scoring_config_model::get_weights_map(). If that table is empty
 * or doesn't exist yet (fresh install, migration 008 not yet run), we fall
 * back to $default_weights below, which reproduce today's hardcoded totals
 * exactly (40/25/20/15) - so behavior is completely unchanged until an HOD
 * edits a weight via the new screen.
 *
 * Category mapping: >=95 Hot, >=80 High, >=60 Medium, >=40 Low, else Discard.
 */
class Dso_lead_scoring
{
    protected $revenue_ceiling = 500000;
    protected $room_nights_ceiling = 500;

    /**
     * Default/fallback weights, keyed by dso_lead_scoring_config.signal_key.
     * These reproduce the original hardcoded component maxima exactly:
     * revenue=40, room_nights=25, priority=20, market_intelligence_match(=source)=15.
     */
    protected $default_weights = array(
        'estimated_revenue'          => 40,
        'estimated_room_nights'      => 25,
        'priority'                   => 20,
        'market_intelligence_match'  => 15,
    );

    /** Original priority component values (out of a max of 20) - kept as ratios of the configurable weight. */
    protected $priority_weights = array(
        'High'   => 20,
        'Medium' => 12,
        'Low'    => 5,
    );

    /** Original source component values (out of a max of 15) - kept as ratios of the configurable weight. */
    protected $source_weights = array(
        'Referral' => 15,
        'Partner'  => 12,
        'Event'    => 10,
        'Website'  => 8,
        'ColdCall' => 5,
        'Other'    => 3,
    );

    protected $weights;

    public function __construct()
    {
        $ci = &get_instance();
        $weights_map = array();
        try {
            $ci->load->model('dyafa/Dso_lead_scoring_config_model');
            $weights_map = $ci->Dso_lead_scoring_config_model->get_weights_map();
        } catch (Exception $e) {
            $weights_map = array();
        }

        // Merge over defaults so a partially-populated config table (or one
        // missing a signal_key) still has a sane weight for every signal.
        $this->weights = array_merge($this->default_weights, $weights_map);
    }

    /**
     * @param array $lead_data expects keys: estimated_revenue, estimated_room_nights, priority, source
     * @return array array('score' => int, 'category' => string, 'suggested_next_action' => string)
     */
    public function score_lead($lead_data)
    {
        $revenue     = isset($lead_data['estimated_revenue']) ? (float) $lead_data['estimated_revenue'] : 0;
        $room_nights = isset($lead_data['estimated_room_nights']) ? (float) $lead_data['estimated_room_nights'] : 0;
        $priority    = isset($lead_data['priority']) ? $lead_data['priority'] : 'Medium';
        $source      = isset($lead_data['source']) ? $lead_data['source'] : 'Other';

        $priority_points = isset($this->priority_weights[$priority]) ? $this->priority_weights[$priority] : 5;
        $source_points   = isset($this->source_weights[$source]) ? $this->source_weights[$source] : 3;

        $revenue_component     = min($revenue / $this->revenue_ceiling, 1) * $this->weights['estimated_revenue'];
        $room_nights_component = min($room_nights / $this->room_nights_ceiling, 1) * $this->weights['estimated_room_nights'];
        $priority_component    = ($priority_points / 20) * $this->weights['priority'];
        $source_component      = ($source_points / 15) * $this->weights['market_intelligence_match'];

        $score = round($revenue_component + $room_nights_component + $priority_component + $source_component);
        $score = max(0, min(100, $score));

        $category = $this->category_for_score($score);
        $action   = $this->next_action_for_category($category);

        return array(
            'score'                 => $score,
            'category'              => $category,
            'suggested_next_action' => $action,
        );
    }

    protected function category_for_score($score)
    {
        if ($score >= 95) return 'Hot';
        if ($score >= 80) return 'High';
        if ($score >= 60) return 'Medium';
        if ($score >= 40) return 'Low';
        return 'Discard';
    }

    protected function next_action_for_category($category)
    {
        $map = array(
            'Hot'     => 'Immediate call within 24 hours and schedule site visit',
            'High'    => 'Call within 48 hours and send tailored proposal',
            'Medium'  => 'Schedule a follow-up call within the week',
            'Low'     => 'Add to nurture campaign and re-check quarterly',
            'Discard' => 'Nurture via email campaign only',
        );
        return isset($map[$category]) ? $map[$category] : 'Review manually';
    }
}
