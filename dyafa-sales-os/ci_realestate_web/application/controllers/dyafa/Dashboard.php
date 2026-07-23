<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_reservations_model');
        $this->load->model('dyafa/Dso_collections_model');
        $this->load->model('dyafa/Dso_activities_model');
        $this->load->model('dyafa/Dso_leads_model');
        $this->load->model('dyafa/Dso_targets_model');
        $this->load->model('dyafa/Dso_users_model');
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->model('dyafa/Dso_contracts_model');
        $this->load->config('dso_roles');
    }

    public function my_performance()
    {
        $uid = $this->dso_user_id();
        $month = date('Y-m');

        $data['performance']  = $this->Dso_targets_model->performance($uid, $month);
        $data['month']        = $month;
        $data['mtd_revenue']  = $this->Dso_reservations_model->sum_month_for_user($uid, $month);
        $data['mtd_reservations'] = $this->Dso_reservations_model->count_month_for_user($uid, $month);
        $data['mtd_collections']  = $this->Dso_collections_model->sum_collected_month_for_owner($uid, $month);
        $data['recent_leads'] = array_slice($this->Dso_leads_model->get_all(array('owner_id' => $uid)), 0, 5);

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/dashboard/my_performance', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function team_performance()
    {
        $this->require_role(array('HOD Sales', 'Sales Manager', 'Management'));

        $this->load->model('dyafa/Dso_teams_model');
        $month = date('Y-m');

        $data['month'] = $month;
        $data['teams'] = $this->Dso_teams_model->performance($month);

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/dashboard/team_performance', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function index()
    {
        $hod_roles = $this->config->item('dso_hod_roles');
        if (in_array($this->dso_role(), $hod_roles, true)) {
            redirect('dyafa/dashboard/hod');
        } else {
            redirect('dyafa/dashboard/daily');
        }
    }

    public function daily()
    {
        $uid = $this->dso_user_id();
        $month = date('Y-m');

        $data['today_revenue']    = $this->Dso_reservations_model->sum_today();
        $data['mtd_revenue']      = $this->Dso_reservations_model->sum_month_for_user($uid, $month);
        $data['reservations_today'] = $this->Dso_reservations_model->count_today_for_user($uid);
        $data['activities_today'] = $this->Dso_activities_model->count_today();
        $data['new_leads_today']  = $this->Dso_leads_model->count_by_owner_and_month($uid, $month);
        $data['performance']     = $this->Dso_targets_model->performance($uid, $month);
        $data['revenue_trend']   = $this->Dso_reservations_model->daily_revenue_trend($month, $uid);

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/dashboard/daily', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function hod()
    {
        $this->require_role($this->config->item('dso_hod_roles'));

        $month = date('Y-m');
        $execs = $this->Dso_users_model->sales_executives();
        $ranking = array();
        foreach ($execs as $u) {
            $perf = $this->Dso_targets_model->performance($u->id, $month);
            $ranking[] = array(
                'user' => $u,
                'revenue' => $this->Dso_reservations_model->sum_month_for_user($u->id, $month),
                'pct' => $perf ? $perf['overall_pct'] : null,
                'band' => $perf ? $perf['band'] : 'No Target Set',
            );
        }
        usort($ranking, function ($a, $b) { return $b['revenue'] <=> $a['revenue']; });

        $conversion = array();
        foreach ($this->Dso_leads_model->conversion_stats_by_owner() as $row) {
            $u = $this->Dso_users_model->get($row->lead_owner_id);
            $conversion[] = array(
                'owner' => $u ? $u->name : 'Unassigned',
                'total' => $row->total_leads,
                'won'   => $row->won_leads,
                'rate'  => $row->total_leads > 0 ? round(($row->won_leads / $row->total_leads) * 100, 1) : 0,
            );
        }

        $data['ranking']        = $ranking;
        $data['conversion']     = $conversion;
        $data['top_accounts']   = $this->Dso_accounts_model->top_accounts_by_revenue(5);
        $data['outstanding']    = $this->Dso_collections_model->outstanding_grand_total();
        $data['expiring']       = $this->Dso_contracts_model->expiring_within_days(30);
        $data['revenue_trend']  = $this->Dso_reservations_model->daily_revenue_trend($month);

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/dashboard/hod', $data);
        $this->load->view('dyafa/layout/footer');
    }
}
