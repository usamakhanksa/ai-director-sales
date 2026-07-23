<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/Llm_base_adapter.php';
require_once __DIR__ . '/Llm_adapter_interface.php';

/**
 * Llm_openai_compatible_adapter - covers every provider whose chat API is a
 * drop-in match for OpenAI's POST {base_url}/chat/completions shape:
 * OpenAI, Groq, OpenRouter, Mistral, DeepSeek, xAI, Ollama (native
 * OpenAI-compatible endpoint). Azure OpenAI uses the same JSON body/response
 * but a different URL pattern and header - toggled via extra_params.is_azure.
 */
class Llm_openai_compatible_adapter extends Llm_base_adapter implements Llm_adapter_interface
{
    public function send_prompt($system_prompt, $user_prompt, $model, array $config)
    {
        $timeout = isset($config['timeout']) ? (int) $config['timeout'] : 8;
        $headers = array();

        if (!empty($config['is_azure'])) {
            $deployment = !empty($config['azure_deployment']) ? $config['azure_deployment'] : $model;
            $api_version = !empty($config['api_version']) ? $config['api_version'] : '2024-05-01-preview';
            $url = rtrim($config['base_url'], '/') . '/openai/deployments/' . rawurlencode($deployment)
                . '/chat/completions?api-version=' . rawurlencode($api_version);
            if (!empty($config['api_key'])) {
                $headers[] = 'api-key: ' . $config['api_key'];
            }
        } else {
            $url = rtrim($config['base_url'], '/') . '/chat/completions';
            if (!empty($config['api_key'])) {
                $headers[] = 'Authorization: Bearer ' . $config['api_key'];
            }
        }

        $body = array(
            'model'       => $model,
            'messages'    => array(
                array('role' => 'system', 'content' => $system_prompt),
                array('role' => 'user', 'content' => $user_prompt),
            ),
            'temperature' => isset($config['temperature']) ? (float) $config['temperature'] : 0.3,
            'max_tokens'  => isset($config['max_tokens']) ? (int) $config['max_tokens'] : 300,
        );

        $decoded = $this->post_json($url, $headers, $body, $timeout);

        if (!isset($decoded['choices'][0]['message']['content'])) {
            throw new Llm_adapter_exception('Unexpected response shape from provider.');
        }
        return trim($decoded['choices'][0]['message']['content']);
    }
}
