<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Targets extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_targets_model');
        $this->load->model('dyafa/Dso_users_model');
        $this->load->library('form_validation');
    }

    public function index($scope = null)
    {
        $data['scope'] = $scope ?: 'mine';
        $data['team_message'] = null;

        $filters = array(
            'month' => $this->input->get('period'),
        );

        if ($scope === 'team') {
            $this->require_role(array('HOD Sales', 'Sales Manager', 'Management'));
            $data['scope'] = 'team';
            $data['targets'] = $this->Dso_targets_model->get_all($filters);
        } else {
            $data['scope'] = 'mine';
            $filters['user_id'] = $this->dso_user_id();
            $data['targets'] = $this->Dso_targets_model->get_all($filters);
        }

        $data['dso_tabs'] = array(
            array('label' => 'My Targets', 'url' => base_url('dyafa/targets'), 'active' => ($data['scope'] === 'mine')),
            array('label' => 'Team Targets', 'url' => base_url('dyafa/targets/index/team'), 'active' => ($data['scope'] === 'team')),
            array('label' => 'Achievement Report', 'url' => base_url('dyafa/targets/performance'), 'active' => false),
        );
        $data['dso_filter_fields'] = array(
            array('name' => 'period', 'label' => 'Month', 'type' => 'text'),
        );

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/targets/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        $this->require_role(array('HOD Sales', 'Sales Manager', 'Management'));

        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = $this->Dso_targets_model->insert($data);
                $this->audit('dso_targets', 'create', $new_id, null, $data);
                $this->session->set_flashdata('dso_success', 'Target created.');
                redirect('dyafa/targets');
                return;
            }
        }
        $data['target'] = null;
        $data['users'] = $this->Dso_users_model->all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/targets/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $this->require_role(array('HOD Sales', 'Sales Manager', 'Management'));

        $target = $this->Dso_targets_model->get($id);
        if (!$target) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $post = $this->_collect_post();
                $this->Dso_targets_model->update($id, $post);
                $this->audit('dso_targets', 'update', $id, $target, $post);
                $this->session->set_flashdata('dso_success', 'Target updated.');
                redirect('dyafa/targets');
                return;
            }
        }
        $data['target'] = $target;
        $data['users'] = $this->Dso_users_model->all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/targets/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->require_role(array('HOD Sales', 'Sales Manager', 'Management'));
        $this->soft_delete_row($this->Dso_targets_model, 'dso_targets', $id);
        $this->session->set_flashdata('dso_success', 'Target deleted.');
        redirect('dyafa/targets');
    }

    public function performance($user_id = null, $month = null)
    {
        $user_id = $user_id ?: $this->dso_user_id();
        $month   = $month ?: date('Y-m');

        $data['perf'] = $this->Dso_targets_model->performance($user_id, $month);
        $data['user'] = $this->Dso_users_model->get($user_id);
        $data['month'] = $month;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/targets/performance', $data);
        $this->load->view('dyafa/layout/footer');
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('user_id', 'User', 'required');
        $this->form_validation->set_rules('month', 'Month', 'required');
        $this->form_validation->set_rules('revenue_target', 'Revenue Target', 'required|numeric');
    }

    protected function _collect_post()
    {
        return array(
            'user_id'              => $this->input->post('user_id'),
            'month'                => $this->input->post('month'),
            'revenue_target'       => $this->input->post('revenue_target') ?: 0,
            'room_nights_target'   => $this->input->post('room_nights_target') ?: 0,
            'reservations_target'  => $this->input->post('reservations_target') ?: 0,
            'collections_target'   => $this->input->post('collections_target') ?: 0,
            'adhoc_revenue_target' => $this->input->post('adhoc_revenue_target') ?: 0,
            'meetings_target'      => $this->input->post('meetings_target') ?: 0,
            'visits_target'        => $this->input->post('visits_target') ?: 0,
            'calls_target'         => $this->input->post('calls_target') ?: 0,
            'new_leads_target'     => $this->input->post('new_leads_target') ?: 0,
            'new_contracts_target' => $this->input->post('new_contracts_target') ?: 0,
        );
    }
}
