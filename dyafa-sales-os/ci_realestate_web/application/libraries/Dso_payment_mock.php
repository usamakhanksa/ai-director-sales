<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_payment_mock
 *
 * Deterministic mock of a real payment gateway's charge/settlement response
 * (e.g. Moyasar/HyperPay/Stripe). Nothing here calls a network endpoint -
 * lets Collections show a realistic gateway reference before a real
 * provider is contracted.
 *
 * Swap-out path: when a real payment gateway is integrated, replace the call
 * to Dso_payment_mock::sync_payment() in Dso_payment_integration with a real
 * HTTP call (same dso_payment_mode 'live'/'mock'/'off' plumbing as PMS/
 * Finance). Keep the response array shape identical.
 */
class Dso_payment_mock
{
    /**
     * @param object $collection row from dso_collections
     * @return array{success:bool, payment_reference:string, payment_status:string, synced_at:string, raw:array}
     */
    public function sync_payment($collection)
    {
        $seed = (int) $collection->id;
        $year = date('Y');

        $reference = sprintf('PAY-%s-%06d', $year, $seed);

        return array(
            'success'           => true,
            'payment_reference' => $reference,
            'payment_status'    => $this->_map_status($collection->status),
            'synced_at'         => date('Y-m-d H:i:s'),
            'raw'               => array(
                'gatewayReference' => $reference,
                'invoiceNo'        => $collection->invoice_no,
                'amountCaptured'   => (float) $collection->paid_amount,
                'currency'         => 'SAR',
                'gatewayStatus'    => $this->_map_status($collection->status),
            ),
        );
    }

    protected function _map_status($dso_status)
    {
        $map = array(
            'Pending'       => 'AWAITING_PAYMENT',
            'PartiallyPaid' => 'PARTIALLY_CAPTURED',
            'Paid'          => 'CAPTURED',
            'Overdue'       => 'FAILED',
        );
        return isset($map[$dso_status]) ? $map[$dso_status] : 'UNKNOWN';
    }
}
