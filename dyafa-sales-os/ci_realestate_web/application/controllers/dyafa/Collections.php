<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Collections extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_collections_model');
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $filters = array(
            'account_id' => $this->input->get('account_id'),
            'status'     => $this->input->get('status'),
        );
        $data['collections'] = $this->Dso_collections_model->get_all($filters);
        $data['dso_tabs'] = $this->_tabs('outstanding');
        $data['dso_filter_fields'] = array(
            array(
                'name'    => 'account_id',
                'label'   => 'Account',
                'type'    => 'select',
                'options' => $this->_account_options(),
            ),
            array(
                'name'    => 'status',
                'label'   => 'Status',
                'type'    => 'select',
                'options' => array(
                    'Pending'       => 'Pending',
                    'PartiallyPaid' => 'Partially Paid',
                    'Paid'          => 'Paid',
                    'Overdue'       => 'Overdue',
                ),
            ),
        );
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/collections/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * Shared tab bar for the 5 Collections list-style views (Outstanding,
     * Aging Report, Credit Limits, Invoices, Statements). $active is one of
     * 'outstanding'/'aging'/'credit_limits'/'invoices'/'statements'.
     */
    protected function _tabs($active)
    {
        $tabs = array(
            'outstanding'   => array('label' => 'Outstanding Payments', 'url' => 'dyafa/collections'),
            'aging'         => array('label' => 'Aging Report', 'url' => 'dyafa/collections/aging'),
            'credit_limits' => array('label' => 'Credit Limits', 'url' => 'dyafa/collections/credit_limits'),
            'invoices'      => array('label' => 'Invoices', 'url' => 'dyafa/collections/invoices'),
            'statements'    => array('label' => 'Statements', 'url' => 'dyafa/collections/statements'),
        );
        $dso_tabs = array();
        foreach ($tabs as $key => $tab) {
            $dso_tabs[] = array(
                'label'  => $tab['label'],
                'url'    => base_url($tab['url']),
                'active' => ($key === $active),
            );
        }
        return $dso_tabs;
    }

    protected function _account_options()
    {
        $options = array();
        foreach ($this->Dso_accounts_model->get_all() as $a) {
            $options[$a->id] = $a->company_name;
        }
        return $options;
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = $this->Dso_collections_model->insert($data);
                $this->audit('dso_collections', 'create', $new_id, null, $data);
                $this->session->set_flashdata('dso_success', 'Collection record added.');
                redirect('dyafa/collections');
                return;
            }
        }
        $data['collection'] = null;
        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/collections/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        // Only Finance Team / HOD Sales / Management may record payments (manual entry).
        $this->require_role(array('Finance Team', 'HOD Sales', 'Management'));

        $collection = $this->Dso_collections_model->get($id);
        if (!$collection) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();

                // NOTE: A real payment gateway integration would receive a webhook here
                // and update paid_amount/status automatically. In this build, payment
                // recording is manual: the Finance Team enters paid_amount via this form.
                if ($data['paid_amount'] >= $data['amount']) {
                    $data['status'] = 'Paid';
                } elseif ($data['paid_amount'] > 0) {
                    $data['status'] = 'PartiallyPaid';
                }

                $this->Dso_collections_model->update($id, $data);
                $this->audit('dso_collections', 'update', $id, $collection, $data);

                // Fire the Finance/ERP sync extension point (config-gated -
                // see application/libraries/Dso_finance_integration.php and
                // application/config/dso_integrations.php).
                if (in_array($data['status'], array('Paid', 'PartiallyPaid'), true)) {
                    $this->load->library('dso_finance_integration');
                    $this->dso_finance_integration->sync_invoice($id);

                    $this->load->library('dso_payment_integration');
                    $this->dso_payment_integration->sync_payment($id);
                }

                $this->session->set_flashdata('dso_success', 'Collection updated.');
                redirect('dyafa/collections');
                return;
            }
        }
        $data['collection'] = $collection;
        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/collections/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->soft_delete_row($this->Dso_collections_model, 'dso_collections', $id);
        $this->session->set_flashdata('dso_success', 'Collection deleted.');
        redirect('dyafa/collections');
    }

    public function aging()
    {
        $data['buckets'] = $this->Dso_collections_model->aging_buckets();
        $data['dso_tabs'] = $this->_tabs('aging');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/collections/aging', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * Credit Limits report (BRD gap item): accounts with a contract, their
     * credit_limit/credit_days, and current outstanding balance, so staff can
     * see who is approaching or over their limit. Same access gate as index()
     * - any logged-in staff member.
     */
    public function credit_limits()
    {
        $data['rows'] = $this->Dso_collections_model->credit_limit_report();
        $data['dso_tabs'] = $this->_tabs('credit_limits');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/collections/credit_limits', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * Invoices (BRD gap item): the same dso_collections rows as index(), but
     * framed/labeled as an invoice list rather than the payment-recording
     * worklist.
     */
    public function invoices()
    {
        $data['collections'] = $this->Dso_collections_model->get_all();
        $data['dso_tabs'] = $this->_tabs('invoices');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/collections/invoices', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * Statements (BRD gap item): internal staff-side equivalent of
     * Portal::statement() - pick any account and view its full collections
     * history, instead of the portal's "my own account only" restriction.
     */
    public function statements()
    {
        $account_id = $this->input->get('account_id');
        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $data['selected_account_id'] = $account_id;
        $data['account'] = null;
        $data['collections'] = array();
        if ($account_id) {
            $data['account'] = $this->Dso_accounts_model->get($account_id);
            $data['collections'] = $this->Dso_collections_model->get_by_account($account_id);
        }
        $data['dso_tabs'] = $this->_tabs('statements');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/collections/statements', $data);
        $this->load->view('dyafa/layout/footer');
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('account_id', 'Account', 'required');
        $this->form_validation->set_rules('invoice_no', 'Invoice No', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('due_date', 'Due Date', 'required');
    }

    protected function _collect_post()
    {
        return array(
            'account_id'  => $this->input->post('account_id'),
            'invoice_no'  => $this->input->post('invoice_no'),
            'amount'      => $this->input->post('amount'),
            'due_date'    => $this->input->post('due_date'),
            'paid_amount' => $this->input->post('paid_amount') ?: 0,
            'status'      => $this->input->post('status') ?: 'Pending',
        );
    }
}
