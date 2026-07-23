<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/Llm_base_adapter.php';
require_once __DIR__ . '/Llm_adapter_interface.php';

/** Llm_gemini_adapter - Google's POST {base_url}/models/{model}:generateContent?key=... shape. */
class Llm_gemini_adapter extends Llm_base_adapter implements Llm_adapter_interface
{
    public function send_prompt($system_prompt, $user_prompt, $model, array $config)
    {
        $timeout = isset($config['timeout']) ? (int) $config['timeout'] : 8;
        $url = rtrim($config['base_url'], '/') . '/models/' . rawurlencode($model) . ':generateContent';
        if (!empty($config['api_key'])) {
            $url .= '?key=' . rawurlencode($config['api_key']);
        }

        $body = array(
            'system_instruction' => array(
                'parts' => array(array('text' => $system_prompt)),
            ),
            'contents' => array(
                array('role' => 'user', 'parts' => array(array('text' => $user_prompt))),
            ),
            'generationConfig' => array(
                'temperature'     => isset($config['temperature']) ? (float) $config['temperature'] : 0.3,
                'maxOutputTokens' => isset($config['max_tokens']) ? (int) $config['max_tokens'] : 300,
            ),
        );

        $decoded = $this->post_json($url, array(), $body, $timeout);

        if (!isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Llm_adapter_exception('Unexpected response shape from provider.');
        }
        return trim($decoded['candidates'][0]['content']['parts'][0]['text']);
    }
}
