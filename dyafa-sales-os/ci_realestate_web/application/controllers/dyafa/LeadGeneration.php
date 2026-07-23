<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * LeadGeneration - AI Lead Generation > Generate Leads.
 * Synchronous, admin-triggered equivalent of Cron::dso_generate_leads():
 * both call Dso_lead_generator::generate() so there is exactly one
 * implementation of the synthesize/de-dup/score/insert pipeline. See
 * Dso_lead_generator.php's class doc-block for the synthetic-data caveat.
 */
class LeadGeneration extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_permission('generate_leads');
        $this->load->library('dso_lead_generator');
    }

    public function index()
    {
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/lead_generation/index');
        $this->load->view('dyafa/layout/footer');
    }

    public function generate()
    {
        $data['result'] = $this->dso_lead_generator->generate();
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/lead_generation/result', $data);
        $this->load->view('dyafa/layout/footer');
    }
}
