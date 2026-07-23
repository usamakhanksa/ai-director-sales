<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dso_csv');
        $this->load->model('dyafa/Dso_reservations_model');
        $this->load->model('dyafa/Dso_adhoc_model');
        $this->load->model('dyafa/Dso_collections_model');
        $this->load->model('dyafa/Dso_leads_model');
        $this->load->model('dyafa/Dso_contracts_model');
        $this->load->model('dyafa/Dso_activities_model');
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->model('dyafa/Dso_properties_model');
        $this->load->model('dyafa/Dso_ai_recommendations_model');
    }

    /** If ?export=csv is present, streams $rows as CSV and terminates the request; otherwise returns FALSE so the caller renders its normal HTML view. */
    protected function _maybe_export($filename, array $rows)
    {
        if ($this->input->get('export') === 'csv') {
            dso_export_csv($rows, $filename);
        }
        return false;
    }

    /**
     * Pushes a named report's rows to the external Reporting Platform
     * (mock by default, see Dso_reporting_integration). HOD-level only.
     */
    public function push_to_reporting($report)
    {
        $this->require_role($this->config->item('dso_hod_roles'));

        $map = array(
            'reservations'         => array($this->Dso_reservations_model, 'get_all'),
            'contracts'            => array($this->Dso_contracts_model, 'get_all'),
            'opportunities'        => array($this->Dso_adhoc_model, 'get_all'),
            'activities'           => array($this->Dso_activities_model, 'get_all'),
            'corporate_accounts'   => array($this->Dso_accounts_model, 'get_all'),
            'property_performance' => array($this->Dso_properties_model, 'performance'),
            'ai_recommendations'   => array($this->Dso_ai_recommendations_model, 'get_all'),
            'daily_sales'          => array($this->Dso_reservations_model, 'today_list'),
            'revenue'              => array($this->Dso_reservations_model, 'revenue_by_month'),
            'aging'                => array($this->Dso_collections_model, 'aging_buckets'),
            'leads'                => array($this->Dso_leads_model, 'get_all'),
            'room_nights'          => array($this->Dso_reservations_model, 'room_nights_by_property'),
            'contract_renewals'    => array($this->Dso_contracts_model, 'expiring_within_days'),
            'adhoc_sales'          => array($this->Dso_adhoc_model, 'get_all'),
        );
        if (!isset($map[$report])) {
            show_404();
            return;
        }
        list($model, $method) = $map[$report];
        $rows = array_map(function ($r) { return (array) $r; }, $model->$method());

        $this->load->library('dso_reporting_integration');
        $result = $this->dso_reporting_integration->push($report, $rows);
        $this->session->set_flashdata('dso_success', $result['message']);
        redirect('dyafa/reports/' . $report);
    }

    public function daily_sales()
    {
        $data['reservations'] = $this->Dso_reservations_model->today_list();
        $data['adhoc'] = $this->Dso_adhoc_model->today_list();
        $data['collected_today'] = $this->Dso_collections_model->sum_collected_today();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/daily_sales', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function revenue()
    {
        $data['by_month']   = $this->Dso_reservations_model->revenue_by_month();
        $data['by_property'] = $this->Dso_reservations_model->revenue_by_property();
        $data['by_account'] = $this->Dso_reservations_model->revenue_by_account();
        $this->_maybe_export('revenue_report', $data['by_month']);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/revenue', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function aging()
    {
        $data['buckets'] = $this->Dso_collections_model->aging_buckets();
        $data['dso_tabs'] = array();
        $this->_maybe_export('collections_aging_report', $data['buckets']);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/collections/aging', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function leads()
    {
        $data['by_status']   = $this->Dso_leads_model->counts_by_status();
        $data['by_category'] = $this->Dso_leads_model->counts_by_category();
        $data['by_owner']    = $this->Dso_leads_model->counts_by_owner();
        $this->_maybe_export('leads_report', $data['by_status']);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/leads', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function reservations()
    {
        $rows = $this->Dso_reservations_model->get_all();
        $this->_maybe_export('reservations_report', $rows);
        $data['rows'] = $rows;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/reservations', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function room_nights()
    {
        $data['by_property'] = $this->Dso_reservations_model->room_nights_by_property();
        $data['by_month']    = $this->Dso_reservations_model->room_nights_by_month();
        $this->_maybe_export('room_nights_report', $data['by_property']);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/room_nights', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function contracts()
    {
        $rows = $this->Dso_contracts_model->get_all();
        $this->_maybe_export('contract_report', $rows);
        $data['rows'] = $rows;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/contracts', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function contract_renewals()
    {
        $rows = $this->Dso_contracts_model->expiring_within_days(60);
        $this->_maybe_export('contract_renewal_report', $rows);
        $data['rows'] = $rows;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/contract_renewals', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function opportunities()
    {
        $data['by_status'] = $this->Dso_adhoc_model->counts_by_status();
        $rows = $this->Dso_adhoc_model->get_all();
        $this->_maybe_export('opportunities_report', $rows);
        $data['rows'] = $rows;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/opportunities', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function adhoc_sales()
    {
        $rows = $this->Dso_adhoc_model->get_all();
        $this->_maybe_export('adhoc_sales_report', $rows);
        $data['rows'] = $rows;
        $data['by_status'] = $this->Dso_adhoc_model->counts_by_status();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/adhoc_sales', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function activities()
    {
        $rows = $this->Dso_activities_model->get_all();
        $this->_maybe_export('activities_report', $rows);
        $data['rows'] = $rows;
        $data['by_type'] = $this->Dso_activities_model->counts_by_type();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/activities', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function corporate_accounts()
    {
        $rows = $this->Dso_accounts_model->get_all();
        $this->_maybe_export('corporate_accounts_report', $rows);
        $data['rows'] = $rows;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/corporate_accounts', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function property_performance()
    {
        $rows = $this->Dso_properties_model->performance();
        $this->_maybe_export('property_performance_report', $rows);
        $data['rows'] = $rows;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/property_performance', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function ai_recommendations()
    {
        $rows = $this->Dso_ai_recommendations_model->get_all();
        $this->_maybe_export('ai_recommendation_report', $rows);
        $data['rows'] = $rows;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reports/ai_recommendations', $data);
        $this->load->view('dyafa/layout/footer');
    }
}
