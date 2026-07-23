<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_users_model');
        $this->load->library('form_validation');
    }

    public function login()
    {
        if ($this->session->userdata('dso_user_id')) {
            redirect('dyafa/dashboard');
            return;
        }
        $data['error'] = $this->session->flashdata('dso_login_error');
        $this->load->view('dyafa/layout/guest_header');
        $this->load->view('dyafa/auth/login', $data);
        $this->load->view('dyafa/layout/guest_footer');
    }

    public function authenticate()
    {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('dso_login_error', validation_errors());
            redirect('dyafa/login');
            return;
        }

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $user = $this->Dso_users_model->get_by_username($username);

        if (!$user || $user->status !== 'Active' || !password_verify($password, $user->password)) {
            $this->session->set_flashdata('dso_login_error', 'Invalid username or password.');
            redirect('dyafa/login');
            return;
        }

        if ($user->role === 'Corporate Client') {
            // Corporate clients use the dedicated portal login flow instead.
            $this->session->set_flashdata('dso_login_error', 'Corporate clients should use the client portal login.');
            redirect('dyafa/login');
            return;
        }

        $this->session->set_userdata(array(
            'dso_user_id' => $user->id,
            'dso_name'    => $user->name,
            'dso_role'    => $user->role,
            'dso_role_id' => isset($user->role_id) ? $user->role_id : null,
            'dso_team_id' => isset($user->team_id) ? $user->team_id : null,
        ));

        redirect('dyafa/dashboard');
    }

    public function logout()
    {
        $this->session->unset_userdata('dso_user_id');
        $this->session->unset_userdata('dso_name');
        $this->session->unset_userdata('dso_role');
        $this->session->unset_userdata('dso_role_id');
        $this->session->unset_userdata('dso_team_id');
        redirect('dyafa/login');
    }
}
