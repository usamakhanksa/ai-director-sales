<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Activities extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_activities_model');
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->model('dyafa/Dso_teams_model');
        $this->load->library('form_validation');
    }

    public function index($scope = null)
    {
        $data['scope'] = $scope ?: 'mine';
        $data['team_message'] = null;
        $data['current_role'] = $this->dso_role();

        $filters = array();
        $activity_type = $this->input->get('activity_type');
        if ($activity_type) {
            $filters['activity_type'] = $activity_type;
        }
        $from = $this->input->get('from');
        if ($from) {
            $filters['activity_date_from'] = $from;
        }
        $to = $this->input->get('to');
        if ($to) {
            $filters['activity_date_to'] = $to;
        }

        if ($scope === 'team') {
            $this->require_role(array('HOD Sales', 'Sales Manager', 'Management'));

            $team_id = $this->dso_team_id();
            if (!$team_id) {
                $data['activities'] = array();
                $data['team_message'] = 'You are not assigned to a team yet.';
            } else {
                $members = $this->Dso_teams_model->members($team_id);
                $member_ids = array_map(function ($u) { return $u->id; }, $members);
                if (empty($member_ids)) {
                    $data['activities'] = array();
                    $data['team_message'] = 'Your team has no members yet.';
                } else {
                    $data['activities'] = $this->Dso_activities_model->get_by_team($member_ids, $filters);
                }
            }
        } else {
            $data['scope'] = 'mine';
            $data['activities'] = $this->Dso_activities_model->get_by_creator($this->dso_user_id(), $filters);
        }

        $data['dso_tabs'] = array(
            array('label' => 'My Activities', 'url' => base_url('dyafa/activities'), 'active' => $data['scope'] === 'mine'),
        );
        if (in_array($data['current_role'], array('HOD Sales', 'Sales Manager', 'Management'), true)) {
            $data['dso_tabs'][] = array('label' => 'Team Activities', 'url' => base_url('dyafa/activities/index/team'), 'active' => $data['scope'] === 'team');
        }

        $type_options = array();
        foreach (array('Call', 'Meeting', 'Visit', 'FollowUp', 'Reservation', 'Collection', 'Complaint', 'Opportunity') as $type) {
            $type_options[$type] = $type;
        }
        $data['dso_filter_fields'] = array(
            array('name' => 'activity_type', 'label' => 'Type', 'type' => 'select', 'options' => $type_options),
            array('name' => 'from', 'label' => 'From', 'type' => 'date'),
            array('name' => 'to', 'label' => 'To', 'type' => 'date'),
        );

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/activities/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $post = $this->_collect_post();
                $data = array_merge($post, array(
                    'created_by' => $this->dso_user_id(),
                    'created_at' => date('Y-m-d H:i:s'),
                ));
                $this->Dso_activities_model->insert($data);
                $this->session->set_flashdata('dso_success', 'Activity logged.');
                redirect('dyafa/activities');
                return;
            }
        }

        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/activities/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('activity_type', 'Activity Type', 'required');
        $this->form_validation->set_rules('notes', 'Notes', 'required');
        $this->form_validation->set_rules('activity_date', 'Activity Date', 'required');
    }

    protected function _collect_post()
    {
        return array(
            'account_id'    => $this->input->post('account_id') ?: null,
            'activity_type' => $this->input->post('activity_type'),
            'notes'         => $this->input->post('notes'),
            'activity_date' => $this->input->post('activity_date'),
        );
    }
}
