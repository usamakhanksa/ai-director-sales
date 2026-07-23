<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/Llm_base_adapter.php';
require_once __DIR__ . '/Llm_adapter_interface.php';

/** Llm_cohere_adapter - Cohere's POST {base_url}/v1/chat shape. */
class Llm_cohere_adapter extends Llm_base_adapter implements Llm_adapter_interface
{
    public function send_prompt($system_prompt, $user_prompt, $model, array $config)
    {
        $timeout = isset($config['timeout']) ? (int) $config['timeout'] : 8;
        $url = rtrim($config['base_url'], '/') . '/v1/chat';

        $headers = array();
        if (!empty($config['api_key'])) {
            $headers[] = 'Authorization: Bearer ' . $config['api_key'];
        }

        $body = array(
            'model'     => $model,
            'preamble'  => $system_prompt,
            'message'   => $user_prompt,
            'temperature' => isset($config['temperature']) ? (float) $config['temperature'] : 0.3,
        );

        $decoded = $this->post_json($url, $headers, $body, $timeout);

        if (!isset($decoded['text'])) {
            throw new Llm_adapter_exception('Unexpected response shape from provider.');
        }
        return trim($decoded['text']);
    }
}
