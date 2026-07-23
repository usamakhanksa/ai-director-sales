<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_permission('manage_users');
        $this->load->model('dyafa/Dso_users_model');
        $this->load->model('dyafa/Dso_roles_model');
        $this->load->model('dyafa/Dso_teams_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['users'] = $this->Dso_users_model->all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/users/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->_validate(true);
            if ($this->form_validation->run() !== FALSE) {
                $role = $this->Dso_roles_model->get($this->input->post('role_id'));
                $this->Dso_users_model->insert(array(
                    'name'     => $this->input->post('name'),
                    'email'    => $this->input->post('email'),
                    'username' => $this->input->post('username'),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'role'     => $role ? $role->name : 'Sales Executive',
                    'role_id'  => $this->input->post('role_id'),
                    'team_id'  => $this->input->post('team_id') ?: null,
                    'status'   => 'Active',
                    'created_at' => date('Y-m-d H:i:s'),
                ));
                $this->session->set_flashdata('dso_success', 'User created.');
                redirect('dyafa/admin/users');
                return;
            }
        }
        $data['user'] = null;
        $data['roles'] = $this->Dso_roles_model->get_all();
        $data['teams'] = $this->Dso_teams_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/users/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $user = $this->Dso_users_model->get($id);
        if (!$user) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->_validate(false);
            if ($this->form_validation->run() !== FALSE) {
                $role = $this->Dso_roles_model->get($this->input->post('role_id'));
                $data = array(
                    'name'    => $this->input->post('name'),
                    'email'   => $this->input->post('email'),
                    'role'    => $role ? $role->name : $user->role,
                    'role_id' => $this->input->post('role_id'),
                    'team_id' => $this->input->post('team_id') ?: null,
                    'status'  => $this->input->post('status'),
                );
                if ($this->input->post('password')) {
                    $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
                }
                $this->Dso_users_model->update($id, $data);
                $this->session->set_flashdata('dso_success', 'User updated.');
                redirect('dyafa/admin/users');
                return;
            }
        }
        $data['user'] = $user;
        $data['roles'] = $this->Dso_roles_model->get_all();
        $data['teams'] = $this->Dso_teams_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/users/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function toggle_status($id)
    {
        $user = $this->Dso_users_model->get($id);
        if ($user) {
            $this->Dso_users_model->update($id, array('status' => $user->status === 'Active' ? 'Inactive' : 'Active'));
            $this->session->set_flashdata('dso_success', 'User status updated.');
        }
        redirect('dyafa/admin/users');
    }

    protected function _validate($is_new)
    {
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('role_id', 'Role', 'required');
        if ($is_new) {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        }
    }
}
