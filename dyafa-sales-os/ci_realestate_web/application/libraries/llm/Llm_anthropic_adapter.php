<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/Llm_base_adapter.php';
require_once __DIR__ . '/Llm_adapter_interface.php';

/** Llm_anthropic_adapter - Anthropic's POST {base_url}/v1/messages shape. */
class Llm_anthropic_adapter extends Llm_base_adapter implements Llm_adapter_interface
{
    public function send_prompt($system_prompt, $user_prompt, $model, array $config)
    {
        $timeout = isset($config['timeout']) ? (int) $config['timeout'] : 8;
        $url = rtrim($config['base_url'], '/') . '/v1/messages';

        $headers = array(
            'anthropic-version: 2023-06-01',
        );
        if (!empty($config['api_key'])) {
            $headers[] = 'x-api-key: ' . $config['api_key'];
        }

        $body = array(
            'model'      => $model,
            'system'     => $system_prompt,
            'messages'   => array(
                array('role' => 'user', 'content' => $user_prompt),
            ),
            'max_tokens' => isset($config['max_tokens']) ? (int) $config['max_tokens'] : 300,
        );
        if (isset($config['temperature'])) {
            $body['temperature'] = (float) $config['temperature'];
        }

        $decoded = $this->post_json($url, $headers, $body, $timeout);

        if (!isset($decoded['content'][0]['text'])) {
            throw new Llm_adapter_exception('Unexpected response shape from provider.');
        }
        return trim($decoded['content'][0]['text']);
    }
}
