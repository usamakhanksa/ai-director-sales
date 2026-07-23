<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_targets_model extends CI_Model
{
    protected $table = 'dso_targets';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_reservations_model');
        $this->load->model('dyafa/Dso_activities_model');
        $this->load->model('dyafa/Dso_leads_model');
        $this->load->model('dyafa/Dso_collections_model');
    }

    public function get_all($filters = array())
    {
        $this->db->select('t.*, u.name as user_name')
            ->from('dso_targets t')
            ->join('dso_users u', 'u.id = t.user_id')
            ->where('t.deleted_at', null);
        if (!empty($filters['user_id'])) {
            $this->db->where('t.user_id', $filters['user_id']);
        }
        if (!empty($filters['month'])) {
            $this->db->where('t.month', $filters['month']);
        }
        $this->db->order_by('t.month', 'desc')->order_by('u.name', 'asc');
        return $this->db->get()->result();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->where('deleted_at', null)->get($this->table)->row();
    }

    public function get_for_user_month($user_id, $month)
    {
        return $this->db->where('user_id', $user_id)->where('month', $month)->where('deleted_at', null)->get($this->table)->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /** Soft delete - sets deleted_at instead of removing the row (audit/compliance requirement). */
    public function delete($id)
    {
        return $this->db->where('id', $id)->update($this->table, array('deleted_at' => date('Y-m-d H:i:s')));
    }

    public function new_contracts_count($user_id, $month)
    {
        return $this->db->where('account_manager_id', $user_id)
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", $month)
            ->where('deleted_at', null)
            ->count_all_results('dso_contracts');
    }

    /**
     * Build target vs actual data for a user/month.
     * Returns array of metrics with target, actual, pct.
     */
    public function performance($user_id, $month)
    {
        $target = $this->get_for_user_month($user_id, $month);
        if (!$target) {
            return null;
        }

        $actual_revenue      = $this->Dso_reservations_model->sum_month_for_user($user_id, $month);
        $actual_room_nights  = $this->Dso_reservations_model->room_nights_month_for_user($user_id, $month);
        $actual_reservations = $this->Dso_reservations_model->count_month_for_user($user_id, $month);
        $actual_collections  = $this->Dso_collections_model->sum_collected_month_for_owner($user_id, $month);

        $actual_meetings = $this->Dso_activities_model->count_by_type_user_month($user_id, 'Meeting', $month);
        $actual_visits   = $this->Dso_activities_model->count_by_type_user_month($user_id, 'Visit', $month);
        $actual_calls    = $this->Dso_activities_model->count_by_type_user_month($user_id, 'Call', $month);
        $actual_new_leads = $this->Dso_leads_model->count_by_owner_and_month($user_id, $month);
        $actual_new_contracts = $this->new_contracts_count($user_id, $month);

        $metrics = array(
            'revenue'         => array('target' => (float)$target->revenue_target, 'actual' => $actual_revenue),
            'room_nights'     => array('target' => (int)$target->room_nights_target, 'actual' => $actual_room_nights),
            'reservations'    => array('target' => (int)$target->reservations_target, 'actual' => $actual_reservations),
            'collections'     => array('target' => (float)$target->collections_target, 'actual' => $actual_collections),
            'meetings'        => array('target' => (int)$target->meetings_target, 'actual' => $actual_meetings),
            'visits'          => array('target' => (int)$target->visits_target, 'actual' => $actual_visits),
            'calls'           => array('target' => (int)$target->calls_target, 'actual' => $actual_calls),
            'new_leads'       => array('target' => (int)$target->new_leads_target, 'actual' => $actual_new_leads),
            'new_contracts'   => array('target' => (int)$target->new_contracts_target, 'actual' => $actual_new_contracts),
        );

        $pct_sum = 0;
        $pct_count = 0;
        foreach ($metrics as $key => &$m) {
            $m['pct'] = $m['target'] > 0 ? round(($m['actual'] / $m['target']) * 100, 1) : 0;
            $pct_sum += $m['pct'];
            $pct_count++;
        }
        unset($m);

        $overall_pct = $pct_count > 0 ? round($pct_sum / $pct_count, 1) : 0;

        return array(
            'target'      => $target,
            'metrics'     => $metrics,
            'overall_pct' => $overall_pct,
            'band'        => dso_achievement_band($overall_pct),
        );
    }
}

if (!function_exists('dso_achievement_band')) {
    /**
     * Map an overall achievement percentage to a performance band.
     * >=100 Outstanding, >=90 Excellent, >=75 Good, >=60 Average, else NeedsAttention.
     */
    function dso_achievement_band($pct)
    {
        if ($pct >= 100) return 'Outstanding';
        if ($pct >= 90)  return 'Excellent';
        if ($pct >= 75)  return 'Good';
        if ($pct >= 60)  return 'Average';
        return 'NeedsAttention';
    }
}
