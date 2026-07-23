<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notifications extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_notifications_model');
    }

    public function index()
    {
        $total_rows = $this->Dso_notifications_model->count_for_user($this->dso_user_id(), $this->dso_role());
        $page = $this->paginate(base_url($this->uri->uri_string()), $total_rows, 25);
        $data['notifications'] = $this->Dso_notifications_model->get_for_user($this->dso_user_id(), $this->dso_role(), $page['per_page'], $page['offset']);
        $data['dso_pagination'] = $page['links'];

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/notifications/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function mark_read($id)
    {
        $this->Dso_notifications_model->mark_read($id);
        redirect('dyafa/notifications');
    }
}
