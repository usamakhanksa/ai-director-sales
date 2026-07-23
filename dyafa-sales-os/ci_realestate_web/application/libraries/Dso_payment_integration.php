<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_payment_integration
 *
 * EXTENSION POINT: no real payment gateway (e.g. Moyasar, HyperPay, Stripe)
 * is contracted yet. Behavior is controlled by dso_payment_mode in
 * application/config/dso_integrations.php:
 *   'live' - attempts a real HTTP POST to dso_payment_endpoint; falls back
 *            to 'mock' behavior on any failure/misconfiguration.
 *   'mock' (default) - Dso_payment_mock generates a realistic fake gateway
 *            reference/status and persists it on the collection row.
 *   'off'  - original log-only stub, no reference data generated.
 * Invoked from Collections::edit() alongside the existing Finance/ERP sync,
 * whenever a payment update results in status Paid or PartiallyPaid.
 */
class Dso_payment_integration
{
    public function sync_payment($collection_id)
    {
        $ci = &get_instance();
        $ci->load->config('dso_integrations');
        $ci->load->database();
        $mode = $ci->config->item('dso_payment_mode');
        $mode = $mode ? $mode : 'mock';
        $collection = $ci->db->where('id', $collection_id)->get('dso_collections')->row();

        if ($mode === 'live') {
            $endpoint = $ci->config->item('dso_payment_endpoint');
            if ($endpoint) {
                $ci->load->model('dyafa/Dso_integration_credentials_model');
                $result = $this->_attempt_http_post(
                    $endpoint,
                    $ci->Dso_integration_credentials_model->get_key('dso_payment'),
                    $ci->config->item('dso_payment_timeout'),
                    (array) $collection
                );
                if ($result === true) {
                    $ci->db->insert('dso_notifications', array(
                        'user_id'    => null,
                        'role'       => 'Finance Team',
                        'type'       => 'payment_synced',
                        'message'    => 'Collection/invoice #' . $collection_id . ' synced to the configured payment gateway.',
                        'is_read'    => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ));
                    return true;
                }
                log_message('error', 'DSO PAYMENT: live sync failed for collection id ' . $collection_id . ' - ' . $result . '. Falling back to mock.');
            }
        }

        if ($mode === 'off') {
            log_message('info', 'DSO PAYMENT STUB: would sync payment for collection #' . $collection_id . ' to external gateway.');
            $ci->db->insert('dso_notifications', array(
                'user_id'    => null,
                'role'       => 'Finance Team',
                'type'       => 'payment_stub',
                'message'    => 'Collection/invoice #' . $collection_id . ' recorded locally. Payment gateway integration is not implemented; no external system was called.',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
            return true;
        }

        $ci->load->library('Dso_payment_mock');
        $response = $ci->dso_payment_mock->sync_payment($collection);
        $ci->db->where('id', $collection_id)->update('dso_collections', array(
            'payment_reference' => $response['payment_reference'],
            'payment_synced_at' => $response['synced_at'],
        ));
        $ci->db->insert('dso_notifications', array(
            'user_id'    => null,
            'role'       => 'Finance Team',
            'type'       => 'payment_synced_mock',
            'message'    => 'Collection/invoice #' . $collection_id . ' synced to mock payment gateway (reference ' . $response['payment_reference'] . '). No real gateway is contracted yet - this is simulated data.',
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    /** Same short-timeout, non-blocking HTTP POST helper as Dso_finance_integration. */
    protected function _attempt_http_post($endpoint, $api_key, $timeout, array $payload)
    {
        if (!function_exists('curl_init')) {
            return 'curl extension not available';
        }
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            return $error;
        }
        if ($status < 200 || $status >= 300) {
            return 'HTTP ' . $status;
        }
        return true;
    }
}
