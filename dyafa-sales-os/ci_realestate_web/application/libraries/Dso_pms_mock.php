<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_pms_mock
 *
 * Deterministic mock of a real Property Management System's reservation-sync
 * response. This exists so the rest of the app (views, notifications, reports)
 * can be built and tested end-to-end against realistic PMS response *shape*
 * before a real PMS contract/API is signed. Nothing here calls a network
 * endpoint - it is pure PHP generating believable-looking confirmation data.
 *
 * Swap-out path: when a real PMS is integrated, replace calls to
 * Dso_pms_mock::sync_reservation() in Dso_reservations_model with real HTTP
 * calls (the existing _attempt_http_post()/dso_pms_endpoint plumbing already
 * does this when dso_pms_mode is set to 'live'). The response array shape
 * returned here should be kept identical so calling code does not change.
 */
class Dso_pms_mock
{
    /**
     * Returns a mock PMS confirmation response for a reservation row.
     *
     * @param object $reservation row from dso_reservations
     * @return array{success:bool, pms_reference:string, pms_room_no:string, pms_status:string, synced_at:string, raw:array}
     */
    public function sync_reservation($reservation)
    {
        $seed = (int) $reservation->id;
        $year = date('Y');

        $room_no = $this->_mock_room_no($seed, $reservation->property);
        $reference = sprintf('PMS-%s-%06d', $year, $seed);

        $response = array(
            'success'       => true,
            'pms_reference' => $reference,
            'pms_room_no'   => $room_no,
            'pms_status'    => $this->_map_status($reservation->status),
            'synced_at'     => date('Y-m-d H:i:s'),
            'raw'           => array(
                'confirmationNumber' => $reference,
                'propertyCode'       => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $reservation->property), 0, 3)),
                'roomNumber'         => $room_no,
                'checkIn'            => $reservation->check_in,
                'checkOut'           => $reservation->check_out,
                'guestBalance'       => (float) $reservation->total_amount,
                'status'             => $this->_map_status($reservation->status),
            ),
        );

        return $response;
    }

    /** Mock a check-in/check-out state transition call to the PMS. */
    public function sync_status_change($reservation, $new_status)
    {
        $result = $this->sync_reservation($reservation);
        $result['pms_status'] = $this->_map_status($new_status);
        $result['raw']['status'] = $result['pms_status'];
        return $result;
    }

    protected function _map_status($dso_status)
    {
        $map = array(
            'Pending'    => 'RESERVED',
            'Confirmed'  => 'CONFIRMED',
            'CheckedIn'  => 'IN_HOUSE',
            'Extended'   => 'IN_HOUSE_EXTENDED',
            'CheckedOut' => 'CHECKED_OUT',
            'Cancelled'  => 'CANCELLED',
            'NoShow'     => 'NO_SHOW',
        );
        return isset($map[$dso_status]) ? $map[$dso_status] : 'UNKNOWN';
    }

    /** Deterministic-looking room number: floor 1-9, room 01-40, based on reservation id. */
    protected function _mock_room_no($seed, $property)
    {
        $floor = ($seed % 9) + 1;
        $room = (($seed * 7) % 40) + 1;
        return sprintf('%d%02d', $floor, $room);
    }
}
