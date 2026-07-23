<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Leads extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_leads_model');
        $this->load->model('dyafa/Dso_users_model');
        $this->load->library('form_validation');
        $this->load->library('dso_lead_scoring');
    }

    /**
     * Passthrough methods for the sidebar tab links, which point at
     * dyafa/leads/mine|unassigned|ai (no /index/ segment) - under this
     * app's HMVC routing, a URI's 3rd segment resolves to a method name,
     * not automatically to index()'s $scope parameter, so without these
     * three one-line methods those links 404. Confirmed live (not just by
     * reading index()'s signature) via a real HTTP smoke test.
     */
    public function mine()
    {
        $this->index('mine');
    }

    public function unassigned()
    {
        $this->index('unassigned');
    }

    public function ai()
    {
        $this->index('ai');
    }

    public function index($scope = null)
    {
        $filters = array();
        if ($scope === 'mine') {
            $filters['owner_id'] = $this->dso_user_id();
        } elseif ($scope === 'unassigned') {
            $filters['unassigned'] = true;
        } elseif ($scope === 'ai') {
            $filters['source'] = 'AI Generated';
        }

        if ($this->input->get('status')) {
            $filters['status'] = $this->input->get('status');
        }
        if ($this->input->get('source')) {
            $filters['source'] = $this->input->get('source');
        }

        $data['dso_tabs'] = array(
            array('label' => 'All', 'url' => base_url('dyafa/leads'), 'active' => $scope === null),
            array('label' => 'My Leads', 'url' => base_url('dyafa/leads/mine'), 'active' => $scope === 'mine'),
            array('label' => 'Unassigned', 'url' => base_url('dyafa/leads/unassigned'), 'active' => $scope === 'unassigned'),
            array('label' => 'AI Generated', 'url' => base_url('dyafa/leads/ai'), 'active' => $scope === 'ai'),
        );
        $data['dso_filter_fields'] = array(
            array(
                'name'    => 'status',
                'label'   => 'Status',
                'type'    => 'select',
                'options' => array(
                    'New'          => 'New',
                    'Contacted'    => 'Contacted',
                    'Qualified'    => 'Qualified',
                    'ProposalSent' => 'ProposalSent',
                    'Negotiation'  => 'Negotiation',
                    'Won'          => 'Won',
                    'Lost'         => 'Lost',
                ),
            ),
            array(
                'name'    => 'source',
                'label'   => 'Source',
                'type'    => 'select',
                'options' => array(
                    'Referral'     => 'Referral',
                    'Website'      => 'Website',
                    'ColdCall'     => 'ColdCall',
                    'Event'        => 'Event',
                    'Partner'      => 'Partner',
                    'AI Generated' => 'AI Generated',
                    'Other'        => 'Other',
                ),
            ),
        );

        $total_rows = $this->Dso_leads_model->count_all($filters);
        $page = $this->paginate(base_url($this->uri->uri_string()), $total_rows, 25);
        $data['leads'] = $this->Dso_leads_model->get_all($filters, $page['per_page'], $page['offset']);
        $data['dso_pagination'] = $page['links'];

        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/leads/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function sources()
    {
        $data['sources'] = $this->Dso_leads_model->counts_by_source();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/leads/sources', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $hod = $this->Dso_users_model->first_by_role('HOD Sales');
                $post = $this->_collect_post();
                $scoring = $this->dso_lead_scoring->score_lead($post);

                $data = array_merge($post, array(
                    'lead_owner_id'         => $hod ? $hod->id : null,
                    'lead_score'            => $scoring['score'],
                    'lead_category'         => $scoring['category'],
                    'suggested_next_action' => $scoring['suggested_next_action'],
                    'created_at'            => date('Y-m-d H:i:s'),
                ));
                $this->Dso_leads_model->insert($data);
                $this->session->set_flashdata('dso_success', 'Lead added successfully.');
                redirect('dyafa/leads');
                return;
            }
        }
        $data['lead'] = null;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/leads/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $lead = $this->Dso_leads_model->get($id);
        if (!$lead) {
            show_404();
            return;
        }

        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $post = $this->_collect_post();
                $scoring = $this->dso_lead_scoring->score_lead($post);

                $data = array_merge($post, array(
                    'lead_score'            => $scoring['score'],
                    'lead_category'         => $scoring['category'],
                    'suggested_next_action' => $scoring['suggested_next_action'],
                    'updated_at'            => date('Y-m-d H:i:s'),
                ));
                $this->Dso_leads_model->update($id, $data);
                $this->session->set_flashdata('dso_success', 'Lead updated successfully.');
                redirect('dyafa/leads');
                return;
            }
        }
        $data['lead'] = $lead;
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/leads/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function view($id)
    {
        $data['lead'] = $this->Dso_leads_model->get($id);
        if (!$data['lead']) {
            show_404();
            return;
        }
        $data['owner'] = $this->Dso_users_model->get($data['lead']->lead_owner_id);
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/leads/view', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->Dso_leads_model->soft_delete($id);
        $this->session->set_flashdata('dso_success', 'Lead deleted.');
        redirect('dyafa/leads');
    }

    public function assign($id)
    {
        $this->require_role(array('HOD Sales'));

        $lead = $this->Dso_leads_model->get($id);
        if (!$lead) {
            show_404();
            return;
        }

        if ($this->input->method() === 'post') {
            $owner_id = $this->input->post('lead_owner_id');
            $this->Dso_leads_model->assign($id, $owner_id);
            $this->session->set_flashdata('dso_success', 'Lead reassigned.');
            redirect('dyafa/leads');
            return;
        }

        $data['lead'] = $lead;
        $data['users'] = $this->Dso_users_model->all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/leads/assign', $data);
        $this->load->view('dyafa/layout/footer');
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('company_name', 'Company Name', 'required');
        $this->form_validation->set_rules('contact_person', 'Contact Person', 'required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required');
        $this->form_validation->set_rules('estimated_revenue', 'Estimated Revenue', 'required|numeric');
        $this->form_validation->set_rules('estimated_room_nights', 'Estimated Room Nights', 'required|integer');
    }

    protected function _collect_post()
    {
        return array(
            'company_name'          => $this->input->post('company_name'),
            'industry'               => $this->input->post('industry'),
            'contact_person'         => $this->input->post('contact_person'),
            'mobile'                 => $this->input->post('mobile'),
            'email'                  => $this->input->post('email'),
            'city'                   => $this->input->post('city'),
            'estimated_revenue'      => $this->input->post('estimated_revenue'),
            'estimated_room_nights'  => $this->input->post('estimated_room_nights'),
            'priority'               => $this->input->post('priority'),
            'source'                 => $this->input->post('source'),
            'status'                 => $this->input->post('status') ?: 'New',
        );
    }
}
