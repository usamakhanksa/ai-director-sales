<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * LeadScoringConfig - AI Lead Generation > Lead Scoring Config.
 * Lets an HOD tune the relative weight each signal contributes to a lead's
 * score (see Dso_lead_scoring.php), backed by dso_lead_scoring_config.
 */
class LeadScoringConfig extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_permission('manage_lead_scoring');
        $this->load->model('dyafa/Dso_lead_scoring_config_model');
    }

    public function index()
    {
        $data['weights'] = $this->Dso_lead_scoring_config_model->get_all();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/lead_scoring_config/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function save()
    {
        $weights = $this->input->post('weights');
        if (is_array($weights)) {
            foreach ($weights as $signal_key => $weight) {
                $this->Dso_lead_scoring_config_model->update_weight($signal_key, $weight);
            }
        }
        $this->session->set_flashdata('dso_success', 'Lead scoring weights updated.');
        redirect('dyafa/leadscoringconfig');
    }
}
