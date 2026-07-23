<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Accounts extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->model('dyafa/Dso_activities_model');
        $this->load->model('dyafa/Dso_users_model');
        $this->load->model('dyafa/Dso_contracts_model');
        $this->load->library('form_validation');
    }

    public function view360($id)
    {
        $account = $this->Dso_accounts_model->get($id);
        if (!$account) {
            show_404();
            return;
        }
        $this->load->model('dyafa/Dso_reservations_model');
        $this->load->model('dyafa/Dso_collections_model');

        $data['account']      = $account;
        $data['contracts']    = $this->Dso_contracts_model->get_by_account($id);
        $data['reservations'] = $this->Dso_reservations_model->get_all($id);
        $data['collections']  = $this->Dso_collections_model->get_by_account($id);
        $data['activities']   = $this->Dso_activities_model->get_by_account($id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/accounts/view360', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function performance()
    {
        $data['accounts'] = $this->Dso_accounts_model->performance();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/accounts/performance', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function index()
    {
        $filters = array();
        if ($this->input->get('industry')) {
            $filters['industry'] = $this->input->get('industry');
        }
        if ($this->input->get('status')) {
            $filters['status'] = $this->input->get('status');
        }

        $data['accounts'] = $this->Dso_accounts_model->get_all($this->my_team_account_ids(), $filters);

        $data['dso_filter_fields'] = array(
            array(
                'name'    => 'industry',
                'label'   => 'Industry',
                'type'    => 'text',
            ),
            array(
                'name'    => 'status',
                'label'   => 'Status',
                'type'    => 'select',
                'options' => array(
                    'Active'   => 'Active',
                    'Inactive' => 'Inactive',
                ),
            ),
        );

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/accounts/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = $this->Dso_accounts_model->insert($data);
                $this->audit('dso_accounts', 'create', $new_id, null, $data);
                $this->session->set_flashdata('dso_success', 'Account created.');
                redirect('dyafa/accounts');
                return;
            }
        }
        $data['account'] = null;
        $data['users'] = $this->Dso_users_model->all();
        $data['contracts'] = $this->Dso_contracts_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/accounts/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $account = $this->Dso_accounts_model->get($id);
        if (!$account) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $this->Dso_accounts_model->update($id, $data);
                $this->audit('dso_accounts', 'update', $id, $account, $data);
                $this->session->set_flashdata('dso_success', 'Account updated.');
                redirect('dyafa/accounts');
                return;
            }
        }
        $data['account'] = $account;
        $data['users'] = $this->Dso_users_model->all();
        $data['contracts'] = $this->Dso_contracts_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/accounts/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function view($id)
    {
        $account = $this->Dso_accounts_model->get($id);
        if (!$account) {
            show_404();
            return;
        }
        $data['account'] = $account;
        $data['activities'] = $this->Dso_activities_model->get_by_account($id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/accounts/view', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /** First-class "Activities" tab for an account - full log (view360 only showed a short recent list). */
    public function activities($id)
    {
        $account = $this->Dso_accounts_model->get($id);
        if (!$account) {
            show_404();
            return;
        }
        $data['account'] = $account;
        $data['activities'] = $this->Dso_activities_model->get_by_account($id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/accounts/activities', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->soft_delete_row($this->Dso_accounts_model, 'dso_accounts', $id);
        $this->session->set_flashdata('dso_success', 'Account deleted.');
        redirect('dyafa/accounts');
    }

    public function add_activity($account_id)
    {
        $this->form_validation->set_rules('activity_type', 'Activity Type', 'required');
        $this->form_validation->set_rules('notes', 'Notes', 'required');
        $this->form_validation->set_rules('activity_date', 'Activity Date', 'required');

        if ($this->form_validation->run() !== FALSE) {
            $this->Dso_activities_model->insert(array(
                'account_id'    => $account_id,
                'activity_type' => $this->input->post('activity_type'),
                'notes'         => $this->input->post('notes'),
                'activity_date' => $this->input->post('activity_date'),
                'created_by'    => $this->dso_user_id(),
                'created_at'    => date('Y-m-d H:i:s'),
            ));
            $this->session->set_flashdata('dso_success', 'Activity logged.');
        } else {
            $this->session->set_flashdata('dso_login_error', validation_errors());
        }
        redirect('dyafa/accounts/view/' . $account_id);
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('company_name', 'Company Name', 'required');
    }

    protected function _collect_post()
    {
        return array(
            'contract_id'             => $this->input->post('contract_id') ?: null,
            'company_name'            => $this->input->post('company_name'),
            'industry'                => $this->input->post('industry'),
            'city'                    => $this->input->post('city'),
            'primary_contact_person'  => $this->input->post('primary_contact_person'),
            'primary_contact_mobile'  => $this->input->post('primary_contact_mobile'),
            'primary_contact_email'   => $this->input->post('primary_contact_email'),
            'account_owner_id'        => $this->input->post('account_owner_id') ?: null,
            'status'                  => $this->input->post('status') ?: 'Active',
            'is_vip'                  => $this->input->post('is_vip') ? 1 : 0,
        );
    }
}
