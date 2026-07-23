<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reservations extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_reservations_model');
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->model('dyafa/Dso_properties_model');
        $this->load->library('form_validation');
    }

    public function index($filter = null)
    {
        $filters = array();
        switch ($filter) {
            case 'pending':
                $filters['status'] = 'Pending';
                break;
            case 'checkins_today':
                $filters['check_in'] = date('Y-m-d');
                break;
            case 'checkouts_today':
                $filters['check_out'] = date('Y-m-d');
                break;
        }
        $property_filter = $this->input->get('property_id');
        if ($property_filter) {
            $filters['property'] = $property_filter;
        }
        $from_filter = $this->input->get('from');
        if ($from_filter) {
            $filters['check_in_from'] = $from_filter;
        }
        $to_filter = $this->input->get('to');
        if ($to_filter) {
            $filters['check_in_to'] = $to_filter;
        }

        $account_ids = $this->my_team_account_ids();
        $total_rows = $this->Dso_reservations_model->count_all($account_ids, $filters);
        $page = $this->paginate(base_url($this->uri->uri_string()), $total_rows, 25);
        $data['reservations'] = $this->Dso_reservations_model->get_all($account_ids, $filters, $page['per_page'], $page['offset']);
        $data['dso_pagination'] = $page['links'];

        $data['dso_tabs'] = array(
            array('label' => 'All', 'url' => base_url('dyafa/reservations'), 'active' => $filter === null),
            array('label' => 'Pending', 'url' => base_url('dyafa/reservations/index/pending'), 'active' => $filter === 'pending'),
            array('label' => "Today's Check-ins", 'url' => base_url('dyafa/reservations/index/checkins_today'), 'active' => $filter === 'checkins_today'),
            array('label' => "Today's Check-outs", 'url' => base_url('dyafa/reservations/index/checkouts_today'), 'active' => $filter === 'checkouts_today'),
        );

        $property_names = $this->Dso_properties_model->get_active_names();
        $property_options = array();
        foreach ($property_names as $name) {
            $property_options[$name] = $name;
        }
        $data['dso_filter_fields'] = array(
            array('name' => 'property_id', 'label' => 'Property', 'type' => 'select', 'options' => $property_options),
            array('name' => 'from', 'label' => 'From', 'type' => 'date'),
            array('name' => 'to', 'label' => 'To', 'type' => 'date'),
        );

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reservations/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /** Month-grid reservation calendar (server-rendered), current month by default. */
    public function calendar()
    {
        $month = $this->input->get('month');
        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }
        $reservations = $this->Dso_reservations_model->get_for_month($month);

        $first_ts = strtotime($month . '-01');
        $days_in_month = (int) date('t', $first_ts);
        $start_weekday = (int) date('N', $first_ts); // 1 (Mon) - 7 (Sun)

        // Build day => reservations map.
        $by_day = array();
        for ($d = 1; $d <= $days_in_month; $d++) {
            $by_day[$d] = array();
        }
        foreach ($reservations as $r) {
            $ci = max(1, (int) date('j', strtotime($r->check_in)));
            $co = (int) date('j', strtotime($r->check_out));
            if (date('Y-m', strtotime($r->check_in)) !== $month) {
                $ci = 1;
            }
            if (date('Y-m', strtotime($r->check_out)) !== $month) {
                $co = $days_in_month;
            }
            for ($d = $ci; $d <= min($co, $days_in_month); $d++) {
                $by_day[$d][] = $r;
            }
        }

        $prev_month = date('Y-m', strtotime($month . '-01 -1 month'));
        $next_month = date('Y-m', strtotime($month . '-01 +1 month'));

        $data['month'] = $month;
        $data['prev_month'] = $prev_month;
        $data['next_month'] = $next_month;
        $data['days_in_month'] = $days_in_month;
        $data['start_weekday'] = $start_weekday;
        $data['by_day'] = $by_day;

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reservations/calendar', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * AJAX endpoint for calendar drag-drop: moves a reservation's
     * check_in/check_out and reuses the same validate_against_contract()
     * path as edit() - never bypasses contract validation.
     */
    public function calendar_move($id)
    {
        $reservation = $this->Dso_reservations_model->get($id);
        if (!$reservation) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'success' => false, 'message' => 'Reservation not found.',
            )));
            return;
        }

        $check_in = $this->input->post('check_in');
        $check_out = $this->input->post('check_out');
        if (!$check_in || !$check_out) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'success' => false, 'message' => 'check_in and check_out are required.',
            )));
            return;
        }

        list($ok, $msg) = $this->Dso_reservations_model->validate_against_contract(
            $reservation->account_id, $reservation->property, $reservation->total_amount, $id
        );
        if (!$ok) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'success' => false, 'message' => $msg,
            )));
            return;
        }

        $this->Dso_reservations_model->update($id, array(
            'check_in'   => $check_in,
            'check_out'  => $check_out,
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        $this->output->set_content_type('application/json')->set_output(json_encode(array(
            'success' => true,
        )));
    }

    public function add()
    {
        $error = null;
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $post = $this->_collect_post();
                list($ok, $msg) = $this->Dso_reservations_model->validate_against_contract(
                    $post['account_id'], $post['property'], $post['total_amount']
                );
                if (!$ok) {
                    $error = $msg;
                } else {
                    $post['created_by'] = $this->dso_user_id();
                    $post['created_at'] = date('Y-m-d H:i:s');
                    $this->Dso_reservations_model->insert($post);
                    $this->session->set_flashdata('dso_success', 'Reservation created.');
                    redirect('dyafa/reservations');
                    return;
                }
            }
        }
        $data['reservation'] = null;
        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $data['properties'] = $this->Dso_properties_model->get_active_names();
        $data['error'] = $error;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reservations/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $reservation = $this->Dso_reservations_model->get($id);
        if (!$reservation) {
            show_404();
            return;
        }
        $error = null;
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $post = $this->_collect_post();
                list($ok, $msg) = $this->Dso_reservations_model->validate_against_contract(
                    $post['account_id'], $post['property'], $post['total_amount'], $id
                );
                if (!$ok) {
                    $error = $msg;
                } else {
                    $post['updated_at'] = date('Y-m-d H:i:s');
                    $this->Dso_reservations_model->update($id, $post);
                    if ($post['status'] !== $reservation->status) {
                        $this->Dso_reservations_model->sync_pms_status_change($id, $post['status']);
                    }
                    $this->session->set_flashdata('dso_success', 'Reservation updated.');
                    redirect('dyafa/reservations');
                    return;
                }
            }
        }
        $data['reservation'] = $reservation;
        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $data['properties'] = $this->Dso_properties_model->get_active_names();
        $data['error'] = $error;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/reservations/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function cancel($id)
    {
        $this->Dso_reservations_model->cancel($id);
        $this->Dso_reservations_model->sync_pms_status_change($id, 'Cancelled');
        $this->session->set_flashdata('dso_success', 'Reservation cancelled.');
        redirect('dyafa/reservations');
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('account_id', 'Account', 'required');
        $this->form_validation->set_rules('property', 'Property', 'required');
        $this->form_validation->set_rules('check_in', 'Check-in', 'required');
        $this->form_validation->set_rules('check_out', 'Check-out', 'required');
        $this->form_validation->set_rules('rate', 'Rate', 'required|numeric');
        $this->form_validation->set_rules('room_nights', 'Room Nights', 'required|integer');
        $this->form_validation->set_rules('total_amount', 'Total Amount', 'required|numeric');
    }

    protected function _collect_post()
    {
        return array(
            'account_id'   => $this->input->post('account_id'),
            'property'     => $this->input->post('property'),
            'check_in'     => $this->input->post('check_in'),
            'check_out'    => $this->input->post('check_out'),
            'rate'         => $this->input->post('rate'),
            'room_nights'  => $this->input->post('room_nights'),
            'total_amount' => $this->input->post('total_amount'),
            'status'       => $this->input->post('status') ?: 'Pending',
        );
    }
}
