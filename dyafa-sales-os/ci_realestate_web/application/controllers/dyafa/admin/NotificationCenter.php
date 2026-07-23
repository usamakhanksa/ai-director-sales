<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Administration > Notification Center. Admin-scope view over the existing
 * dso_notifications table (used end-user-side by dyafa/notifications) plus
 * a broadcast form that inserts one row per targeted user/role - no new
 * table needed.
 */
class NotificationCenter extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_permission('manage_notifications');
        $this->load->model('dyafa/Dso_notifications_model');
        $this->load->model('dyafa/Dso_users_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['notifications'] = $this->Dso_notifications_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/notification_center/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function broadcast()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('message', 'Message', 'required');
            $this->form_validation->set_rules('target', 'Target', 'required');
            if ($this->form_validation->run() !== FALSE) {
                $target = $this->input->post('target');
                $message = $this->input->post('message');
                if ($target === 'all') {
                    foreach ($this->Dso_users_model->all() as $u) {
                        $this->Dso_notifications_model->insert(array('user_id' => $u->id, 'type' => 'Broadcast', 'message' => $message));
                    }
                } elseif (strpos($target, 'role:') === 0) {
                    $role = substr($target, 5);
                    foreach ($this->Dso_users_model->all($role) as $u) {
                        $this->Dso_notifications_model->insert(array('user_id' => $u->id, 'type' => 'Broadcast', 'message' => $message));
                    }
                } else {
                    $this->Dso_notifications_model->insert(array('user_id' => (int) $target, 'type' => 'Broadcast', 'message' => $message));
                }
                $this->session->set_flashdata('dso_success', 'Notification broadcast sent.');
                redirect('dyafa/admin/notificationcenter');
                return;
            }
        }
        $data['users'] = $this->Dso_users_model->all();
        $this->load->config('dso_roles');
        $data['roles'] = $this->config->item('dso_roles');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/notification_center/broadcast', $data);
        $this->load->view('dyafa/layout/footer');
    }
}
