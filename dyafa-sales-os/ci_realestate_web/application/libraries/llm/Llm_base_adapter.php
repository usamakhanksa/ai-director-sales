<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/Llm_adapter_exception.php';

/**
 * Llm_base_adapter - shared raw-curl HTTP helper for every concrete adapter.
 * No Guzzle/HTTP client dependency exists in this codebase, so this is a
 * thin wrapper around curl rather than pulling in a new library.
 */
abstract class Llm_base_adapter
{
    /**
     * POSTs JSON and returns the decoded response body as an array.
     * Throws Llm_adapter_exception on curl error, non-2xx status, or
     * non-JSON response.
     */
    protected function post_json($url, array $headers, array $body, $timeout)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => array_merge(array('Content-Type: application/json'), $headers),
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => min($timeout, 5),
        ));
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            throw new Llm_adapter_exception('cURL error: ' . $error);
        }
        if ($status < 200 || $status >= 300) {
            throw new Llm_adapter_exception('HTTP ' . $status . ': ' . substr((string) $response, 0, 300));
        }
        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new Llm_adapter_exception('Non-JSON response from provider.');
        }
        return $decoded;
    }

    public function build_test_prompt()
    {
        return array('system' => 'You are a connection test.', 'user' => 'Reply with the single word: OK');
    }
}
