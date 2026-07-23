<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Roles extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_permission('manage_roles');
        $this->load->model('dyafa/Dso_roles_model');
        $this->load->model('dyafa/Dso_permissions_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['roles'] = $this->Dso_roles_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/roles/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Role Name', 'required');
            if ($this->form_validation->run() !== FALSE) {
                $role_data = array(
                    'name'      => $this->input->post('name'),
                    'is_system' => 0,
                );
                $role_id = $this->Dso_roles_model->insert($role_data);
                $this->Dso_roles_model->set_permissions($role_id, (array) $this->input->post('permissions'));
                $this->audit('dso_roles', 'create', $role_id, null, $role_data);
                $this->session->set_flashdata('dso_success', 'Role created.');
                redirect('dyafa/admin/roles');
                return;
            }
        }
        $data['role'] = null;
        $data['permissions'] = $this->Dso_permissions_model->get_all();
        $data['assigned'] = array();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/roles/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $role = $this->Dso_roles_model->get($id);
        if (!$role) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Role Name', 'required');
            if ($this->form_validation->run() !== FALSE) {
                $update_data = array('name' => $this->input->post('name'));
                $this->Dso_roles_model->update($id, $update_data);
                $this->Dso_roles_model->set_permissions($id, (array) $this->input->post('permissions'));
                $this->audit('dso_roles', 'update', $id, $role, $update_data);
                $this->session->set_flashdata('dso_success', 'Role updated.');
                redirect('dyafa/admin/roles');
                return;
            }
        }
        $data['role'] = $role;
        $data['permissions'] = $this->Dso_permissions_model->get_all();
        $data['assigned'] = $this->Dso_roles_model->permission_ids_for_role($id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/roles/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->soft_delete_row($this->Dso_roles_model, 'dso_roles', $id);
        $this->session->set_flashdata('dso_success', 'Role deleted.');
        redirect('dyafa/admin/roles');
    }
}
