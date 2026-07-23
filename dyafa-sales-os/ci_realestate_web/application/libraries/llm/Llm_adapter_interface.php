<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Llm_adapter_interface - every provider adapter in application/libraries/llm/
 * implements this. Kept to two methods so adding a new non-OpenAI-compatible
 * provider only ever requires one small new class.
 */
interface Llm_adapter_interface
{
    /**
     * Sends a single prompt and returns the completion text.
     * Must throw Llm_adapter_exception on any HTTP/parse/auth error - never
     * return null/false, so callers can rely on try/catch alone.
     *
     * @param string $system_prompt
     * @param string $user_prompt
     * @param string $model
     * @param array  $config array('api_key'=>?, 'base_url'=>, 'timeout'=>, 'temperature'=>, 'max_tokens'=>, ...extra_params)
     * @return string
     */
    public function send_prompt($system_prompt, $user_prompt, $model, array $config);

    /** A trivial prompt pair used by the "Test Connection" action. */
    public function build_test_prompt();
}
