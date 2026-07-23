<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_reporting_mock
 *
 * Deterministic mock of pushing a report's rows to an external BI/reporting
 * platform (e.g. Power BI, Metabase, Looker). Nothing here calls a network
 * endpoint - it simulates a successful export/push and returns a reference id
 * so the UI can show that a "push" happened.
 *
 * Swap-out path: when a real reporting platform is integrated, replace the
 * call to Dso_reporting_mock::push() in Dso_reporting_integration with a
 * real HTTP call (same dso_reporting_mode 'live'/'mock'/'off' plumbing as
 * PMS/Finance/Payment). Keep the response array shape identical.
 */
class Dso_reporting_mock
{
    /**
     * @param string $report_name e.g. 'revenue_report'
     * @param int $row_count number of rows being pushed
     * @return array{success:bool, export_reference:string, synced_at:string, raw:array}
     */
    public function push($report_name, $row_count)
    {
        $reference = sprintf('RPT-%s-%s', strtoupper(substr(preg_replace('/[^a-z0-9]/i', '', $report_name), 0, 6)), date('YmdHis'));

        return array(
            'success'          => true,
            'export_reference' => $reference,
            'synced_at'        => date('Y-m-d H:i:s'),
            'raw'              => array(
                'reportName'  => $report_name,
                'rowCount'    => $row_count,
                'exportedAt'  => date('c'),
            ),
        );
    }
}
