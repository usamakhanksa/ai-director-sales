<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/llm/Llm_adapter_exception.php';
require_once __DIR__ . '/llm/Llm_adapter_interface.php';
require_once __DIR__ . '/llm/Llm_base_adapter.php';
require_once __DIR__ . '/llm/Llm_openai_compatible_adapter.php';
require_once __DIR__ . '/llm/Llm_anthropic_adapter.php';
require_once __DIR__ . '/llm/Llm_gemini_adapter.php';
require_once __DIR__ . '/llm/Llm_cohere_adapter.php';

/**
 * Dso_llm_client - facade over the configured default LLM provider.
 *
 * enhance_recommendation() is the only entry point Dso_sales_assistant uses.
 * It NEVER throws and NEVER returns anything but a valid $rec array: on any
 * missing config / disabled provider / timeout / HTTP error / malformed
 * response, the original heuristic $rec is returned unchanged and the
 * failure is only visible in the PHP error log. This is a deliberate
 * reliability contract, not an oversight - see Dyafa_Sales_OS_BRD.md
 * section 19 and the "HEURISTIC RULE-BASED PLACEHOLDER" note in
 * Dso_sales_assistant.php: the AI Assistant must keep working even when no
 * LLM is configured or a provider call fails.
 */
class Dso_llm_client
{
    protected $adapter_map = array(
        'openai_compatible' => 'Llm_openai_compatible_adapter',
        'anthropic'          => 'Llm_anthropic_adapter',
        'gemini'             => 'Llm_gemini_adapter',
        'cohere'             => 'Llm_cohere_adapter',
    );

    public function __construct()
    {
        $ci = &get_instance();
        $ci->load->model('dyafa/Dso_ai_providers_model');
        $ci->load->config('dso_llm');
    }

    /**
     * @param array $rec     heuristic-built recommendation array (see
     *                        Dso_sales_assistant::build_*_recommendation())
     * @param array $context human-readable facts for prompt-building only,
     *                        e.g. account_name, property_name, days_figure,
     *                        estimated_revenue, recommendation_type
     * @return array $rec, with suggested_action/reason replaced only on a
     *               fully successful LLM call
     */
    public function enhance_recommendation(array $rec, array $context)
    {
        $ci = &get_instance();
        $provider = $ci->Dso_ai_providers_model->get_default();
        if (!$provider) {
            return $rec;
        }

        try {
            $text = $this->_call($provider, $rec, $context);
            $parsed = $this->_parse($text);
            if ($parsed) {
                $rec['suggested_action'] = $parsed['suggested_action'];
                $rec['reason'] = $parsed['reason'];
            }
        } catch (Exception $e) {
            log_message('error', 'DSO LLM enhance failed (' . $provider->provider_key . '): ' . $e->getMessage());
        }
        return $rec;
    }

    /**
     * Optional LLM candidate for suggested_property_id/priority - NOT part
     * of enhance_recommendation()'s free-text-only contract. Never returns
     * an estimated_revenue figure (the BRD/enhance.md caution against the
     * LLM inventing a number untethered from real reservation history - only
     * the heuristic's trailing-average/credit-limit-ratio logic in
     * Dso_sales_assistant::estimate_revenue() is ever used for that field).
     *
     * The caller (Dso_sales_assistant::_enhance()) is responsible for
     * validating the returned property name against the real active
     * property list and the priority against the Low/Medium/High enum
     * before applying either - this method itself does not touch $rec.
     *
     * @param array $rec     heuristic-built recommendation array
     * @param array $context same shape as enhance_recommendation()'s $context
     * @param array $allowed_property_names candidate list the LLM must choose from (or none)
     * @return array{property_name:?string, priority:?string}|null null on any failure/malformed response
     */
    public function suggest_property_and_priority(array $rec, array $context, array $allowed_property_names)
    {
        $ci = &get_instance();
        $provider = $ci->Dso_ai_providers_model->get_default();
        if (!$provider) {
            return null;
        }

        try {
            $adapter = $this->_get_adapter($provider->provider_key);
            if (!$adapter) {
                return null;
            }
            $api_key = $ci->Dso_ai_providers_model->decrypt_key($provider->api_key_encrypted);
            $config = $this->_build_config($provider, $api_key);

            $system_prompt = 'You are a sales assistant for a hospitality company. Given the account context, '
                . 'suggest which property to recommend and how urgent (priority) this is. '
                . 'Respond ONLY with two lines, no extra commentary: '
                . "PROPERTY: <one property name from the allowed list, or NONE>\nPRIORITY: <Low, Medium, or High>";

            $user_prompt = 'Recommendation type: ' . $context['type'] . "\n"
                . 'Account: ' . $context['account_name'] . "\n"
                . (isset($context['days_figure']) ? 'Days: ' . $context['days_figure'] . "\n" : '')
                . 'Current suggested property: ' . (!empty($context['property_name']) ? $context['property_name'] : 'none') . "\n"
                . 'Current priority: ' . $context['priority'] . "\n"
                . 'Allowed properties to choose from: ' . (!empty($allowed_property_names) ? implode(', ', $allowed_property_names) : 'none available');

            $text = $adapter->send_prompt($system_prompt, $user_prompt, $provider->model, $config);

            if (!preg_match('/PROPERTY:\s*(.+?)\s*(?:\n|$)/i', $text, $m1)) {
                return null;
            }
            if (!preg_match('/PRIORITY:\s*(.+?)\s*(?:\n|$)/i', $text, $m2)) {
                return null;
            }
            $property_name = trim($m1[1]);
            $priority = trim($m2[1]);

            return array(
                'property_name' => (strcasecmp($property_name, 'NONE') === 0) ? null : $property_name,
                'priority'      => $priority,
            );
        } catch (Exception $e) {
            log_message('error', 'DSO LLM property/priority suggestion failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Used by AiConfig's AJAX "Test Connection" action. Accepts either a
     * saved provider row id, or raw unsaved form field data so a provider
     * can be tested before it's saved.
     *
     * @return array('success' => bool, 'message' => string, 'latency_ms' => int)
     */
    public function test_connection($provider_id_or_data)
    {
        $ci = &get_instance();

        if (is_array($provider_id_or_data)) {
            $provider = (object) $provider_id_or_data;
            $api_key = isset($provider->api_key) ? $provider->api_key : null;
        } else {
            $provider = $ci->Dso_ai_providers_model->get($provider_id_or_data);
            if (!$provider) {
                return array('success' => false, 'message' => 'Provider not found.', 'latency_ms' => 0);
            }
            $api_key = $ci->Dso_ai_providers_model->decrypt_key($provider->api_key_encrypted);
        }

        $adapter = $this->_get_adapter($provider->provider_key);
        if (!$adapter) {
            return array('success' => false, 'message' => 'Unknown provider type.', 'latency_ms' => 0);
        }

        $config = $this->_build_config($provider, $api_key);
        $prompt = $adapter->build_test_prompt();

        $start = microtime(true);
        try {
            $text = $adapter->send_prompt($prompt['system'], $prompt['user'], $provider->model, $config);
            $latency = (int) round((microtime(true) - $start) * 1000);
            return array('success' => true, 'message' => 'OK: ' . $text, 'latency_ms' => $latency);
        } catch (Exception $e) {
            $latency = (int) round((microtime(true) - $start) * 1000);
            return array('success' => false, 'message' => $e->getMessage(), 'latency_ms' => $latency);
        }
    }

    protected function _call($provider, array $rec, array $context)
    {
        $ci = &get_instance();
        $api_key = $ci->Dso_ai_providers_model->decrypt_key($provider->api_key_encrypted);
        $adapter = $this->_get_adapter($provider->provider_key);
        if (!$adapter) {
            throw new Llm_adapter_exception('Unknown provider type: ' . $provider->provider_key);
        }
        $config = $this->_build_config($provider, $api_key);

        $system_prompt = 'You are a sales assistant for a hospitality/real-estate company. '
            . 'Refine the draft recommendation into a short, natural, actionable message for a '
            . 'sales account manager. Respond ONLY with two lines, no extra commentary: '
            . "ACTION: <one sentence, imperative>\nREASON: <one sentence, the justification>";

        $user_prompt = 'Recommendation type: ' . $context['type'] . "\n"
            . 'Account: ' . $context['account_name'] . "\n"
            . (isset($context['days_figure']) ? 'Days: ' . $context['days_figure'] . "\n" : '')
            . 'Estimated revenue: SAR ' . number_format($context['estimated_revenue'], 2) . "\n"
            . (!empty($context['property_name']) ? 'Suggested property: ' . $context['property_name'] . "\n" : '')
            . 'Priority: ' . $context['priority'] . "\n"
            . 'Draft action: ' . $rec['suggested_action'] . "\n"
            . 'Draft reason: ' . $rec['reason'];

        return $adapter->send_prompt($system_prompt, $user_prompt, $provider->model, $config);
    }

    /** Parses the ACTION:/REASON: two-line contract; returns null if malformed. */
    protected function _parse($text)
    {
        if (!preg_match('/ACTION:\s*(.+?)\s*(?:\n|$)/i', $text, $m1)) {
            return null;
        }
        if (!preg_match('/REASON:\s*(.+?)\s*(?:\n|$)/i', $text, $m2)) {
            return null;
        }
        $action = trim($m1[1]);
        $reason = trim($m2[1]);
        if ($action === '' || $reason === '') {
            return null;
        }
        return array('suggested_action' => $action, 'reason' => $reason);
    }

    protected function _get_adapter($provider_key)
    {
        $ci = &get_instance();
        $providers = $ci->config->item('dso_llm_providers');
        if (!isset($providers[$provider_key])) {
            return null;
        }
        $adapter_key = $providers[$provider_key]['adapter'];
        if (!isset($this->adapter_map[$adapter_key])) {
            return null;
        }
        $class = $this->adapter_map[$adapter_key];
        return new $class();
    }

    protected function _build_config($provider, $api_key)
    {
        $ci = &get_instance();
        $defaults = $ci->config->item('dso_llm_defaults');
        $meta = $ci->config->item('dso_llm_providers');
        $provider_meta = isset($meta[$provider->provider_key]) ? $meta[$provider->provider_key] : array();

        $extra = array();
        if (!empty($provider->extra_params)) {
            $decoded = is_array($provider->extra_params) ? $provider->extra_params : json_decode($provider->extra_params, true);
            if (is_array($decoded)) {
                $extra = $decoded;
            }
        }

        return array_merge($defaults, array(
            'base_url'    => $provider->base_url,
            'api_key'     => $api_key,
            'is_azure'    => !empty($provider_meta['is_azure']),
        ), $extra);
    }
}
