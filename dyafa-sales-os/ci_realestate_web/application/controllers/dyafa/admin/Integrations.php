<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Administration > Integrations. mode/endpoint/timeout (non-secret) are
 * read/written directly to application/config/dso_integrations.php, same as
 * before. The API key itself is now encrypted-at-rest in
 * dso_integration_credentials (see Dso_integration_credentials_model) -
 * mirroring the Dso_ai_providers_model boundary - instead of being
 * var_export()'d as plaintext into a PHP file on disk. Only key_last4 is
 * ever shown in the form; a blank submitted key means "keep the existing one".
 */
class Integrations extends Dso_Controller
{
    protected $config_path;
    protected $keys = array('dso_pms', 'dso_finance', 'dso_maps', 'dso_payment', 'dso_reporting');

    public function __construct()
    {
        parent::__construct();
        $this->require_permission('manage_integrations');
        $this->config_path = APPPATH . 'config/dso_integrations.php';
        $this->load->config('dso_integrations');
        $this->load->model('dyafa/Dso_integration_credentials_model');
    }

    public function index()
    {
        if ($this->input->method() === 'post') {
            $this->_save();
            $this->session->set_flashdata('dso_success', 'Integration settings updated.');
            redirect('dyafa/admin/integrations');
            return;
        }

        $credentials = $this->Dso_integration_credentials_model->get_all_keyed();
        $data['integrations'] = array();
        foreach ($this->keys as $prefix) {
            $data['integrations'][$prefix] = array(
                'mode'      => $this->config->item($prefix . '_mode'),
                'endpoint'  => $this->config->item($prefix . '_endpoint'),
                'key_last4' => isset($credentials[$prefix]) ? $credentials[$prefix]->key_last4 : null,
                'timeout'   => $this->config->item($prefix . '_timeout'),
            );
        }
        $this->load->view('dyafa/layout/header');
        $this->load->view('dyafa/admin/integrations/form', $data);
        $this->load->view('dyafa/layout/footer');
    }

    protected function _save()
    {
        $values = array();
        foreach ($this->keys as $prefix) {
            $values[$prefix . '_mode']     = in_array($this->input->post($prefix . '_mode'), array('mock', 'live', 'off'), true) ? $this->input->post($prefix . '_mode') : 'mock';
            $values[$prefix . '_endpoint'] = (string) $this->input->post($prefix . '_endpoint');
            $values[$prefix . '_timeout']  = (int) $this->input->post($prefix . '_timeout') ?: 5;

            // Blank submitted key = keep the existing encrypted key untouched.
            $this->Dso_integration_credentials_model->upsert($prefix, $this->input->post($prefix . '_api_key'), $this->dso_user_id());
        }

        $lines = array();
        $lines[] = "<?php if (!defined('BASEPATH')) exit('No direct script access allowed');";
        $lines[] = '';
        $lines[] = '/*';
        $lines[] = ' * Dyafa Sales OS - external integration endpoints.';
        $lines[] = ' * Rewritten by Administration > Integrations UI (dyafa/admin/integrations).';
        $lines[] = ' * Each integration runs in live|mock|off mode - see original comment block';
        $lines[] = ' * preserved in git history / implementation.md for the full rationale.';
        $lines[] = ' * API keys are NOT stored here - see dso_integration_credentials (encrypted).';
        $lines[] = ' */';
        foreach ($this->keys as $prefix) {
            $lines[] = "\$config['{$prefix}_mode']     = " . var_export($values[$prefix . '_mode'], true) . ';';
            $lines[] = "\$config['{$prefix}_endpoint'] = " . var_export($values[$prefix . '_endpoint'], true) . ';';
            $lines[] = "\$config['{$prefix}_timeout']  = " . var_export($values[$prefix . '_timeout'], true) . ';';
            $lines[] = '';
        }
        file_put_contents($this->config_path, implode("\n", $lines) . "\n");
    }
}
