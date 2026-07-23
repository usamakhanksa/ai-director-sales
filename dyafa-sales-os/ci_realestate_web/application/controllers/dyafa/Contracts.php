<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contracts extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_contracts_model');
        $this->load->model('dyafa/Dso_users_model');
        $this->load->model('dyafa/Dso_properties_model');
        $this->load->library('form_validation');
    }

    public function index($status = null)
    {
        $filters = array();
        if ($this->input->get('account_manager_id')) {
            $filters['account_manager_id'] = $this->input->get('account_manager_id');
        }

        $data['contracts'] = $this->Dso_contracts_model->get_filtered($status, $this->my_team_account_ids(), $filters);

        $data['dso_tabs'] = array(
            array('label' => 'All', 'url' => base_url('dyafa/contracts'), 'active' => $status === null),
            array('label' => 'Active', 'url' => base_url('dyafa/contracts/index/active'), 'active' => $status === 'active'),
            array('label' => 'Pending Approval', 'url' => base_url('dyafa/contracts/index/pending'), 'active' => $status === 'pending'),
            array('label' => 'Expiring Soon', 'url' => base_url('dyafa/contracts/index/expiring'), 'active' => $status === 'expiring'),
        );

        $managers = $this->Dso_users_model->all();
        $manager_options = array();
        foreach ($managers as $m) {
            $manager_options[$m->id] = $m->name;
        }
        $data['dso_filter_fields'] = array(
            array(
                'name'    => 'account_manager_id',
                'label'   => 'Account Manager',
                'type'    => 'select',
                'options' => $manager_options,
            ),
        );

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/contracts/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = $this->Dso_contracts_model->insert($data);
                $this->audit('dso_contracts', 'create', $new_id, null, $data);
                $this->session->set_flashdata('dso_success', 'Contract created.');
                redirect('dyafa/contracts');
                return;
            }
        }
        $data['contract'] = null;
        $data['managers'] = $this->Dso_users_model->all();
        $data['all_properties'] = $this->Dso_properties_model->get_active_names();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/contracts/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $contract = $this->Dso_contracts_model->get($id);
        if (!$contract) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->Dso_contracts_model->update($id, $data);
                $this->audit('dso_contracts', 'update', $id, $contract, $data);
                $this->session->set_flashdata('dso_success', 'Contract updated.');
                redirect('dyafa/contracts');
                return;
            }
        }
        $data['contract'] = $contract;
        $data['managers'] = $this->Dso_users_model->all();
        $data['all_properties'] = $this->Dso_properties_model->get_active_names();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/contracts/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->soft_delete_row($this->Dso_contracts_model, 'dso_contracts', $id);
        $this->session->set_flashdata('dso_success', 'Contract deleted.');
        redirect('dyafa/contracts');
    }

    public function funnel()
    {
        $data['counts'] = $this->Dso_contracts_model->funnel_counts();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/contracts/funnel', $data);
        $this->load->view('dyafa/layout/footer');
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('company_name', 'Company Name', 'required');
        $this->form_validation->set_rules('contract_number', 'Contract Number', 'required');
        $this->form_validation->set_rules('credit_limit', 'Credit Limit', 'required|numeric');
    }

    protected function _collect_post()
    {
        return array(
            'account_id'         => $this->input->post('account_id') ?: null,
            'company_name'       => $this->input->post('company_name'),
            'contract_number'    => $this->input->post('contract_number'),
            'start_date'         => $this->input->post('start_date') ?: null,
            'expiry_date'        => $this->input->post('expiry_date') ?: null,
            'payment_terms'      => $this->input->post('payment_terms'),
            'credit_days'        => $this->input->post('credit_days') ?: 0,
            'credit_limit'       => $this->input->post('credit_limit'),
            'account_manager_id' => $this->input->post('account_manager_id') ?: null,
            'allowed_properties' => implode(',', (array) $this->input->post('allowed_properties')),
            'corporate_rates'    => $this->input->post('corporate_rates') ?: '{}',
            'status'             => $this->input->post('status') ?: 'Pending Approval',
        );
    }
}
