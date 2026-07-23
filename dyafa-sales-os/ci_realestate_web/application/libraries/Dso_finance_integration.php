<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_finance_integration
 *
 * EXTENSION POINT: no real Finance/ERP system (e.g. SAP, Oracle Financials,
 * Zoho Books) is contracted yet. Behavior is controlled by dso_finance_mode
 * in application/config/dso_integrations.php:
 *   'live' - attempts a real HTTP POST to dso_finance_endpoint; falls back
 *            to 'mock' behavior on any failure/misconfiguration.
 *   'mock' (default) - Dso_finance_mock generates a realistic fake ledger
 *            sync response (reference/status) and persists it on the
 *            collection row, so statements/reports already show what a real
 *            ERP sync would look like.
 *   'off'  - original log-only stub, no reference data generated.
 * Invoked from Collections::edit() whenever a payment update results in
 * status Paid or PartiallyPaid. Swapping in a real ERP later is a config
 * change only - Dso_finance_mock's response shape matches what a real
 * endpoint call would need to provide.
 */
class Dso_finance_integration
{
    /**
     * Syncs the given collection/invoice to the external ERP (live), a
     * realistic mock (default), or the local-only stub, per dso_finance_mode.
     */
    public function sync_invoice($collection_id)
    {
        $ci = &get_instance();
        $ci->load->config('dso_integrations');
        $ci->load->database();
        $mode = $ci->config->item('dso_finance_mode');
        $mode = $mode ? $mode : 'mock';
        $collection = $ci->db->where('id', $collection_id)->get('dso_collections')->row();

        if ($mode === 'live') {
            $endpoint = $ci->config->item('dso_finance_endpoint');
            if ($endpoint) {
                $ci->load->model('dyafa/Dso_integration_credentials_model');
                $result = $this->_attempt_http_post(
                    $endpoint,
                    $ci->Dso_integration_credentials_model->get_key('dso_finance'),
                    $ci->config->item('dso_finance_timeout'),
                    (array) $collection
                );
                if ($result === true) {
                    $ci->db->insert('dso_notifications', array(
                        'user_id'    => null,
                        'role'       => 'Finance Team',
                        'type'       => 'finance_synced',
                        'message'    => 'Collection/invoice #' . $collection_id . ' synced to the configured Finance/ERP endpoint.',
                        'is_read'    => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ));
                    return true;
                }
                log_message('error', 'DSO FINANCE: live sync failed for collection id ' . $collection_id . ' - ' . $result . '. Falling back to mock.');
            }
        }

        if ($mode === 'off') {
            log_message('info', 'DSO FINANCE STUB: would sync invoice/collection #' . $collection_id . ' to external ERP system.');
            $ci->db->insert('dso_notifications', array(
                'user_id'    => null,
                'role'       => 'Finance Team',
                'type'       => 'finance_stub',
                'message'    => 'Collection/invoice #' . $collection_id . ' recorded locally. Finance/ERP integration is not implemented; no external system was called.',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
            return true;
        }

        $ci->load->library('Dso_finance_mock');
        $response = $ci->dso_finance_mock->sync_invoice($collection);
        $ci->db->where('id', $collection_id)->update('dso_collections', array(
            'finance_reference' => $response['finance_reference'],
            'finance_synced_at' => $response['synced_at'],
        ));
        $ci->db->insert('dso_notifications', array(
            'user_id'    => null,
            'role'       => 'Finance Team',
            'type'       => 'finance_synced_mock',
            'message'    => 'Collection/invoice #' . $collection_id . ' synced to mock Finance/ERP (reference ' . $response['finance_reference'] . '). No real ERP is contracted yet - this is simulated data.',
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    /** Same short-timeout, non-blocking HTTP POST helper as Dso_reservations_model::_attempt_http_post(). */
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
