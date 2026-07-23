<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_finance_mock
 *
 * Deterministic mock of a real Finance/ERP system's invoice-sync response
 * (e.g. what SAP/Oracle Financials/Zoho Books would hand back). Lets the
 * rest of the app (notifications, statements, reports) be built and tested
 * against a realistic response *shape* before a real ERP contract/API
 * exists. Nothing here calls a network endpoint.
 *
 * Swap-out path: when a real Finance/ERP system is integrated, replace
 * calls to Dso_finance_mock::sync_invoice() in Dso_finance_integration with
 * real HTTP calls (the existing _attempt_http_post()/dso_finance_endpoint
 * plumbing already does this when dso_finance_mode is set to 'live'). Keep
 * the response array shape identical so calling code does not change.
 */
class Dso_finance_mock
{
    /**
     * Returns a mock ERP sync response for a collection/invoice row.
     *
     * @param object $collection row from dso_collections
     * @return array{success:bool, finance_reference:string, finance_status:string, synced_at:string, raw:array}
     */
    public function sync_invoice($collection)
    {
        $seed = (int) $collection->id;
        $year = date('Y');

        $reference = sprintf('ERP-INV-%s-%06d', $year, $seed);

        return array(
            'success'           => true,
            'finance_reference' => $reference,
            'finance_status'    => $this->_map_status($collection->status),
            'synced_at'         => date('Y-m-d H:i:s'),
            'raw'               => array(
                'ledgerReference' => $reference,
                'invoiceNo'       => $collection->invoice_no,
                'amount'          => (float) $collection->amount,
                'paidAmount'      => (float) $collection->paid_amount,
                'balance'         => (float) $collection->amount - (float) $collection->paid_amount,
                'dueDate'         => $collection->due_date,
                'ledgerStatus'    => $this->_map_status($collection->status),
            ),
        );
    }

    protected function _map_status($dso_status)
    {
        $map = array(
            'Pending'       => 'OPEN',
            'PartiallyPaid' => 'PARTIALLY_SETTLED',
            'Paid'          => 'SETTLED',
            'Overdue'       => 'OVERDUE',
        );
        return isset($map[$dso_status]) ? $map[$dso_status] : 'UNKNOWN';
    }
}
