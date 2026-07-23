<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Administration > Audit Log. Read-only viewer over dso_audit_log (migration
 * 012), written by Dso_Controller::audit()/soft_delete_row() from every
 * add()/edit()/delete() on Contracts, Accounts, Adhoc Sales, Properties,
 * Collections, Targets, Roles, and Teams. No add/edit/delete of its own -
 * an audit trail that could be edited from the UI would defeat its purpose.
 */
class AuditLog extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_permission('view_audit_log');
        $this->load->model('dyafa/Dso_audit_log_model');
    }

    public function index()
    {
        $filters = array(
            'table_name' => $this->input->get('table_name'),
            'row_id'     => $this->input->get('row_id'),
        );
        $data['entries'] = $this->Dso_audit_log_model->get_all($filters, 200);
        $data['tables'] = array(
            'dso_contracts', 'dso_accounts', 'dso_adhoc_sales', 'dso_properties',
            'dso_collections', 'dso_targets', 'dso_roles', 'dso_teams',
        );
        $data['selected_table'] = $filters['table_name'];
        $data['selected_row_id'] = $filters['row_id'];
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/audit_log/list', $data);
        $this->load->view('dyafa/layout/footer');
    }
}
