<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_reporting_integration
 *
 * EXTENSION POINT: no real BI/reporting platform (e.g. Power BI, Metabase,
 * Looker) is contracted yet. Behavior is controlled by dso_reporting_mode in
 * application/config/dso_integrations.php:
 *   'live' - attempts a real HTTP POST of the report rows to
 *            dso_reporting_endpoint; falls back to 'mock' on any failure.
 *   'mock' (default) - Dso_reporting_mock simulates a successful export and
 *            returns a reference id, no network call made.
 *   'off'  - log-only stub, no reference generated.
 * Invoked from application/controllers/dyafa/Reports.php via an optional
 * "Push to Reporting Platform" action once a report's rows are already
 * loaded (reuses the same $rows array CSV export uses).
 */
class Dso_reporting_integration
{
    /** @return array{success:bool, export_reference:?string, message:string} */
    public function push($report_name, array $rows)
    {
        $ci = &get_instance();
        $ci->load->config('dso_integrations');
        $mode = $ci->config->item('dso_reporting_mode');
        $mode = $mode ? $mode : 'mock';
        $row_count = count($rows);

        if ($mode === 'live') {
            $endpoint = $ci->config->item('dso_reporting_endpoint');
            if ($endpoint) {
                $ci->load->model('dyafa/Dso_integration_credentials_model');
                $result = $this->_attempt_http_post(
                    $endpoint,
                    $ci->Dso_integration_credentials_model->get_key('dso_reporting'),
                    $ci->config->item('dso_reporting_timeout'),
                    array('report' => $report_name, 'rows' => $rows)
                );
                if ($result === true) {
                    return array('success' => true, 'export_reference' => null, 'message' => 'Report "' . $report_name . '" (' . $row_count . ' rows) pushed to the configured reporting platform.');
                }
                log_message('error', 'DSO REPORTING: live push failed for report ' . $report_name . ' - ' . $result . '. Falling back to mock.');
            }
        }

        if ($mode === 'off') {
            log_message('info', 'DSO REPORTING STUB: would push report ' . $report_name . ' (' . $row_count . ' rows) to external BI platform.');
            return array('success' => true, 'export_reference' => null, 'message' => 'Report "' . $report_name . '" recorded locally. Reporting platform integration is not implemented; no external system was called.');
        }

        $ci->load->library('Dso_reporting_mock');
        $response = $ci->dso_reporting_mock->push($report_name, $row_count);
        return array(
            'success'          => true,
            'export_reference' => $response['export_reference'],
            'message'          => 'Report "' . $report_name . '" (' . $row_count . ' rows) pushed to mock reporting platform (reference ' . $response['export_reference'] . '). No real BI platform is contracted yet - this is simulated.',
        );
    }

    /** Same short-timeout, non-blocking HTTP POST helper as the other integrations. */
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
