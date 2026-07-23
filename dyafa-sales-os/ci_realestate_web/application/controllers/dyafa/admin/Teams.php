<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Teams extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_permission('manage_teams');
        $this->load->model('dyafa/Dso_teams_model');
        $this->load->model('dyafa/Dso_users_model');
        $this->load->model('dyafa/Dso_properties_model');
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['teams'] = $this->Dso_teams_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/teams/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Team Name', 'required');
            if ($this->form_validation->run() !== FALSE) {
                $team_data = array(
                    'name'        => $this->input->post('name'),
                    'hod_user_id' => $this->input->post('hod_user_id') ?: null,
                    'created_at'  => date('Y-m-d H:i:s'),
                );
                $team_id = $this->Dso_teams_model->insert($team_data);
                $this->Dso_teams_model->set_properties($team_id, (array) $this->input->post('property_ids'));
                $this->Dso_teams_model->set_accounts($team_id, (array) $this->input->post('account_ids'));
                $this->audit('dso_teams', 'create', $team_id, null, $team_data);
                $this->session->set_flashdata('dso_success', 'Team created.');
                redirect('dyafa/admin/teams');
                return;
            }
        }
        $this->_form_view(null);
    }

    public function edit($id)
    {
        $team = $this->Dso_teams_model->get($id);
        if (!$team) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('name', 'Team Name', 'required');
            if ($this->form_validation->run() !== FALSE) {
                $update_data = array(
                    'name'        => $this->input->post('name'),
                    'hod_user_id' => $this->input->post('hod_user_id') ?: null,
                );
                $this->Dso_teams_model->update($id, $update_data);
                $this->Dso_teams_model->set_properties($id, (array) $this->input->post('property_ids'));
                $this->Dso_teams_model->set_accounts($id, (array) $this->input->post('account_ids'));
                $this->audit('dso_teams', 'update', $id, $team, $update_data);
                $this->session->set_flashdata('dso_success', 'Team updated.');
                redirect('dyafa/admin/teams');
                return;
            }
        }
        $this->_form_view($team);
    }

    public function delete($id)
    {
        $this->soft_delete_row($this->Dso_teams_model, 'dso_teams', $id);
        $this->session->set_flashdata('dso_success', 'Team deleted.');
        redirect('dyafa/admin/teams');
    }

    protected function _form_view($team)
    {
        $data['team'] = $team;
        $data['users'] = $this->Dso_users_model->all();
        $data['properties'] = $this->Dso_properties_model->get_all();
        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $data['assigned_properties'] = $team ? $this->Dso_teams_model->property_ids($team->id) : array();
        $data['assigned_accounts'] = $team ? $this->Dso_teams_model->account_ids($team->id) : array();
        $data['members'] = $team ? $this->Dso_teams_model->members($team->id) : array();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/teams/form', $data);
        $this->load->view('dyafa/layout/footer');
    }
}
