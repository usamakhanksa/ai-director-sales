<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Adhoc extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_adhoc_model');
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->model('dyafa/Dso_properties_model');
        $this->load->library('form_validation');
    }

    /**
     * $filter maps BRD sidebar entries onto existing enum columns rather than
     * a new schema column:
     *   - 'proposals' -> status = 'ProposalSent'  (Proposals sidebar item)
     *   - 'events'    -> event_type = 'Event'     (Events sidebar item)
     * Anything else (including null) falls back to the unfiltered list.
     */
    public function index($filter = null)
    {
        $filters = array();
        if ($filter === 'proposals') {
            $filters['status'] = 'ProposalSent';
        } elseif ($filter === 'events') {
            $filters['event_type'] = 'Event';
        }
        // GET status filter narrows further; when no scope segment set the status,
        // the GET value can also set it outright.
        $status_get = $this->input->get('status');
        if ($status_get) {
            $filters['status'] = $status_get;
        }
        if ($this->input->get('sort') === 'value') {
            $filters['sort'] = 'value';
        }
        $data['items'] = $this->Dso_adhoc_model->get_all($filters);

        $data['dso_tabs'] = array(
            array('label' => 'All', 'url' => base_url('dyafa/adhoc'), 'active' => $filter === null),
            array('label' => 'Proposals', 'url' => base_url('dyafa/adhoc/index/proposals'), 'active' => $filter === 'proposals'),
            array('label' => 'Events', 'url' => base_url('dyafa/adhoc/index/events'), 'active' => $filter === 'events'),
            array('label' => 'Opportunities Board', 'url' => base_url('dyafa/adhoc/board'), 'active' => false),
            // No dedicated Adhoc Revenue report exists yet; link to the list sorted by
            // estimated_value descending as a reasonable stand-in (see task summary).
            array('label' => 'Adhoc Revenue', 'url' => base_url('dyafa/adhoc?sort=value'), 'active' => $this->input->get('sort') === 'value'),
        );

        $status_options = array();
        foreach ($this->_status_enum() as $status) {
            $status_options[$status] = $status;
        }
        $data['dso_filter_fields'] = array(
            array('name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => $status_options),
        );

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/adhoc/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * Opportunities Board - kanban by status. Server-rendered on initial
     * load (same pattern as the reservation calendar); drag-drop only
     * mutates via AJAX (board_move) after the page has rendered.
     */
    public function board()
    {
        $statuses = $this->_status_enum();
        $items = $this->Dso_adhoc_model->get_all();

        $columns = array();
        foreach ($statuses as $status) {
            $columns[$status] = array();
        }
        foreach ($items as $item) {
            if (!isset($columns[$item->status])) {
                $columns[$item->status] = array();
            }
            $columns[$item->status][] = $item;
        }

        $data['statuses'] = $statuses;
        $data['columns'] = $columns;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/adhoc/board', $data);
        $this->load->view('dyafa/layout/footer');
    }

    /**
     * AJAX POST endpoint for the board's drag-drop. Matches the JSON
     * response convention used by AiConfig::test().
     */
    public function board_move($id)
    {
        $status = $this->input->post('status');
        $result = array('success' => false, 'message' => 'Invalid status.');

        if (in_array($status, $this->_status_enum(), true)) {
            $item = $this->Dso_adhoc_model->get($id);
            if (!$item) {
                $result['message'] = 'Adhoc sale not found.';
            } else {
                $this->Dso_adhoc_model->update_status($id, $status);
                $result = array('success' => true);
            }
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /** dso_adhoc_sales.status ENUM values, kept here (not queried from the DB) to match schema. */
    protected function _status_enum()
    {
        return array('Inquiry', 'ProposalSent', 'Negotiation', 'Confirmed', 'Completed', 'Cancelled', 'Lost');
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $data['owner_id']   = $this->dso_user_id();
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = $this->Dso_adhoc_model->insert($data);
                $this->audit('dso_adhoc_sales', 'create', $new_id, null, $data);
                $this->session->set_flashdata('dso_success', 'Adhoc sale added.');
                redirect('dyafa/adhoc');
                return;
            }
        }
        $data['item'] = null;
        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $data['venue_properties'] = $this->Dso_properties_model->get_all('Active');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/adhoc/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $item = $this->Dso_adhoc_model->get($id);
        if (!$item) {
            show_404();
            return;
        }
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $post = $this->_collect_post();
                $this->Dso_adhoc_model->update($id, $post);
                $this->audit('dso_adhoc_sales', 'update', $id, $item, $post);
                $this->session->set_flashdata('dso_success', 'Adhoc sale updated.');
                redirect('dyafa/adhoc');
                return;
            }
        }
        $data['item'] = $item;
        $data['accounts'] = $this->Dso_accounts_model->get_all();
        $data['venue_properties'] = $this->Dso_properties_model->get_all('Active');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/adhoc/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->soft_delete_row($this->Dso_adhoc_model, 'dso_adhoc_sales', $id);
        $this->session->set_flashdata('dso_success', 'Adhoc sale deleted.');
        redirect('dyafa/adhoc');
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('event_type', 'Event Type', 'required');
        $this->form_validation->set_rules('event_date', 'Event Date', 'required');
        $this->form_validation->set_rules('pax', 'Pax', 'required|integer');
        $this->form_validation->set_rules('estimated_value', 'Estimated Value', 'required|numeric');
    }

    protected function _collect_post()
    {
        return array(
            'account_id'         => $this->input->post('account_id') ?: null,
            'event_type'         => $this->input->post('event_type'),
            'venue_property_id'  => $this->input->post('venue_property_id') ?: null,
            'event_date'      => $this->input->post('event_date'),
            'pax'             => $this->input->post('pax'),
            'estimated_value' => $this->input->post('estimated_value'),
            'status'          => $this->input->post('status') ?: 'Inquiry',
            'notes'           => $this->input->post('notes'),
        );
    }
}
