<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * dso_export_csv - streams an array of stdClass/array rows as a CSV download
 * and terminates the request. Used by application/controllers/dyafa/Reports.php
 * whenever a report is requested with ?export=csv.
 */
if (!function_exists('dso_export_csv')) {
    function dso_export_csv(array $rows, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename) . '.csv"');

        $out = fopen('php://output', 'w');
        if (!empty($rows)) {
            $first = (array) $rows[0];
            fputcsv($out, array_keys($first));
            foreach ($rows as $row) {
                fputcsv($out, array_values((array) $row));
            }
        }
        fclose($out);
        exit;
    }
}
