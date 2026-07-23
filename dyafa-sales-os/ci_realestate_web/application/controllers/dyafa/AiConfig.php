<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AiConfig - admin-only management of LLM providers used to enhance AI
 * Sales Assistant recommendations (see Dso_llm_client.php, dso_ai_providers
 * table). Entirely separate from AiAssistant.php (recommendation browsing)
 * since this is configuration, restricted to dso_hod_roles, mirroring how
 * Properties.php gates its write actions by role.
 */
class AiConfig extends Dso_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role($this->config->item('dso_hod_roles'));
        $this->load->model('dyafa/Dso_ai_providers_model');
        $this->load->library(array('form_validation', 'dso_llm_client'));
        $this->load->config('dso_llm');
    }

    public function index()
    {
        $data['providers'] = $this->Dso_ai_providers_model->get_all();
        $data['provider_meta'] = $this->config->item('dso_llm_providers');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/ai_config/list', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function add()
    {
        $error = null;
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $data['created_by'] = $this->dso_user_id();
                $this->Dso_ai_providers_model->insert($data);
                $this->session->set_flashdata('dso_success', 'Provider added.');
                redirect('dyafa/aiconfig');
                return;
            }
            $error = validation_errors();
        }
        $data['provider'] = null;
        $data['error'] = $error;
        $data['provider_meta'] = $this->config->item('dso_llm_providers');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/ai_config/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function edit($id)
    {
        $provider = $this->Dso_ai_providers_model->get($id);
        if (!$provider) {
            show_404();
            return;
        }
        $error = null;
        if ($this->input->method() === 'post') {
            $this->_validate();
            if ($this->form_validation->run() !== FALSE) {
                $data = $this->_collect_post();
                $this->Dso_ai_providers_model->update($id, $data);
                $this->session->set_flashdata('dso_success', 'Provider updated.');
                redirect('dyafa/aiconfig');
                return;
            }
            $error = validation_errors();
        }
        $data['provider'] = $provider;
        $data['error'] = $error;
        $data['provider_meta'] = $this->config->item('dso_llm_providers');
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/ai_config/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    public function delete($id)
    {
        $this->Dso_ai_providers_model->delete($id);
        $this->session->set_flashdata('dso_success', 'Provider deleted.');
        redirect('dyafa/aiconfig');
    }

    public function set_default($id)
    {
        $this->Dso_ai_providers_model->set_default($id);
        $this->session->set_flashdata('dso_success', 'Default provider updated.');
        redirect('dyafa/aiconfig');
    }

    /**
     * AJAX endpoint used by ai_config/list.php (saved provider, POSTs id)
     * and ai_config/form.php (unsaved form fields, POSTs raw values).
     */
    public function test()
    {
        $id = $this->input->post('id');
        if ($id) {
            $result = $this->dso_llm_client->test_connection($id);
            if ($result['success']) {
                $this->Dso_ai_providers_model->mark_test_result($id, 'Success', $result['message']);
            } else {
                $this->Dso_ai_providers_model->mark_test_result($id, 'Failed', $result['message']);
            }
        } else {
            $raw = array(
                'provider_key' => $this->input->post('provider_key'),
                'base_url'     => $this->input->post('base_url'),
                'model'        => $this->input->post('model'),
                'api_key'      => $this->input->post('api_key'),
                'extra_params' => array(),
            );
            $result = $this->dso_llm_client->test_connection($raw);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    protected function _validate()
    {
        $this->form_validation->set_rules('provider_key', 'Provider', 'required');
        $this->form_validation->set_rules('label', 'Label', 'required');
        $this->form_validation->set_rules('base_url', 'Base URL', 'required');
        $this->form_validation->set_rules('model', 'Model', 'required');
        $this->form_validation->set_rules('temperature', 'Temperature', 'permit_empty|numeric');
        $this->form_validation->set_rules('max_tokens', 'Max Tokens', 'permit_empty|integer');
    }

    protected function _collect_post()
    {
        $extra_params = array(
            'temperature' => $this->input->post('temperature') !== '' ? (float) $this->input->post('temperature') : 0.3,
            'max_tokens'  => $this->input->post('max_tokens') !== '' ? (int) $this->input->post('max_tokens') : 300,
        );
        $advanced = trim((string) $this->input->post('advanced_json'));
        if ($advanced !== '') {
            $decoded = json_decode($advanced, true);
            if (is_array($decoded)) {
                $extra_params = array_merge($extra_params, $decoded);
            }
        }

        $data = array(
            'provider_key'  => $this->input->post('provider_key'),
            'label'         => $this->input->post('label'),
            'base_url'      => rtrim($this->input->post('base_url'), '/'),
            'model'         => $this->input->post('model'),
            'extra_params'  => $extra_params,
            'is_enabled'    => $this->input->post('is_enabled') ? 1 : 0,
        );

        $api_key = $this->input->post('api_key');
        if ($api_key !== null && $api_key !== '') {
            $data['api_key'] = $api_key;
        }

        return $data;
    }
}
