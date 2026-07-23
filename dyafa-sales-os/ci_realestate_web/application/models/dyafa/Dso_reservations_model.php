<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_reservations_model extends CI_Model
{
    protected $table = 'dso_reservations';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dyafa/Dso_accounts_model');
        $this->load->model('dyafa/Dso_contracts_model');
        $this->load->model('dyafa/Dso_collections_model');
    }

    /** Shared WHERE-clause builder for get_all()/count_all() so pagination counts always match the listed rows. */
    protected function _apply_filters($account_id, $filters)
    {
        $this->db->from($this->table);
        if (is_array($account_id)) {
            $this->db->where_in('account_id', $account_id);
        } elseif ($account_id) {
            $this->db->where('account_id', $account_id);
        }
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (!empty($filters['check_in'])) {
            $this->db->where('check_in', $filters['check_in']);
        }
        if (!empty($filters['check_out'])) {
            $this->db->where('check_out', $filters['check_out']);
        }
        if (!empty($filters['property'])) {
            $this->db->where('property', $filters['property']);
        }
        if (!empty($filters['check_in_from'])) {
            $this->db->where('check_in >=', $filters['check_in_from']);
        }
        if (!empty($filters['check_in_to'])) {
            $this->db->where('check_in <=', $filters['check_in_to']);
        }
    }

    /**
     * $account_id: single account (Account 360) or array of ids (Teams territory scope).
     * $limit/$offset: optional pagination (Reservations::index() list page); omitted = unbounded, unchanged for every other caller.
     */
    public function get_all($account_id = null, $filters = array(), $limit = null, $offset = 0)
    {
        $this->_apply_filters($account_id, $filters);
        $this->db->order_by('created_at', 'desc');
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get()->result();
    }

    /** Row count for the same filters, used to build pagination links. */
    public function count_all($account_id = null, $filters = array())
    {
        $this->_apply_filters($account_id, $filters);
        return $this->db->count_all_results();
    }

    /**
     * Reservations whose stay overlaps the given month, for the calendar grid.
     * $month is 'YYYY-MM'.
     */
    public function get_for_month($month)
    {
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));
        $this->db->from($this->table)
            ->where('status !=', 'Cancelled')
            ->where('check_in <=', $end)
            ->where('check_out >=', $start)
            ->order_by('check_in', 'asc');
        return $this->db->get()->result();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    /**
     * Validate a reservation before insert/update.
     * Returns array(true) if ok, or array(false, 'message') if invalid.
     */
    public function validate_against_contract($account_id, $property, $total_amount, $exclude_reservation_id = null)
    {
        $account = $this->Dso_accounts_model->get($account_id);
        if (!$account) {
            return array(false, 'Account not found.');
        }
        if (empty($account->contract_id)) {
            // no contract linked - no restriction, allow
            return array(true, null);
        }
        $contract = $this->Dso_contracts_model->get($account->contract_id);
        if (!$contract) {
            return array(true, null);
        }

        // (a) allowed properties check
        $allowed = $this->Dso_contracts_model->get_allowed_properties($contract->id);
        if (!empty($allowed) && !in_array($property, $allowed, true)) {
            return array(false, 'Property "' . $property . '" is not part of the allowed properties for this account\'s contract.');
        }

        // (b) credit limit check: outstanding collections + this reservation vs credit_limit
        $outstanding = $this->Dso_collections_model->outstanding_total_for_account($account_id);
        $projected = $outstanding + (float) $total_amount;
        if ($contract->credit_limit > 0 && $projected > (float) $contract->credit_limit) {
            return array(false, 'This reservation would exceed the account\'s contract credit limit (' .
                'outstanding: ' . number_format($outstanding, 2) . ', credit limit: ' . number_format($contract->credit_limit, 2) . ').');
        }

        return array(true, null);
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        $id = $this->db->insert_id();
        // Fire the PMS extension-point stub (does not call any real external system).
        $this->create_pms_reservation($id);
        return $id;
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function cancel($id)
    {
        return $this->db->where('id', $id)->update($this->table, array('status' => 'Cancelled'));
    }

    /**
     * EXTENSION POINT: no real PMS (Property Management System) is
     * contracted yet. Behavior is controlled by dso_pms_mode in
     * application/config/dso_integrations.php:
     *   'live' - attempts a real HTTP POST to dso_pms_endpoint; falls back
     *            to 'mock' behavior on any failure/misconfiguration.
     *   'mock' (default) - Dso_pms_mock generates a realistic fake
     *            confirmation response (reference/room/status) and persists
     *            it on the reservation row, so the UI and reports already
     *            show what a real PMS sync would look like.
     *   'off'  - original log-only stub, no reference data generated.
     * Swapping in a real PMS later is a config change only; response shape
     * from Dso_pms_mock matches what a real endpoint call would need to
     * provide.
     */
    public function create_pms_reservation($reservation_id)
    {
        $this->load->config('dso_integrations');
        $mode = $this->config->item('dso_pms_mode');
        $mode = $mode ? $mode : 'mock';
        $reservation = $this->get($reservation_id);

        if ($mode === 'live') {
            $endpoint = $this->config->item('dso_pms_endpoint');
            if ($endpoint) {
                $this->load->model('dyafa/Dso_integration_credentials_model');
                $result = $this->_attempt_http_post(
                    $endpoint,
                    $this->Dso_integration_credentials_model->get_key('dso_pms'),
                    $this->config->item('dso_pms_timeout'),
                    (array) $reservation
                );
                if ($result === true) {
                    $this->db->insert('dso_notifications', array(
                        'user_id'    => null,
                        'role'       => 'Reservation Team',
                        'type'       => 'pms_synced',
                        'message'    => 'Reservation #' . $reservation_id . ' synced to the configured PMS endpoint.',
                        'is_read'    => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ));
                    return true;
                }
                log_message('error', 'DSO PMS: live sync failed for reservation id ' . $reservation_id . ' - ' . $result . '. Falling back to mock.');
            }
        }

        if ($mode === 'off') {
            log_message('info', 'DSO PMS STUB: would create PMS reservation for local reservation id ' . $reservation_id);
            $this->db->insert('dso_notifications', array(
                'user_id'    => null,
                'role'       => 'Reservation Team',
                'type'       => 'pms_stub',
                'message'    => 'Reservation #' . $reservation_id . ' created locally. PMS integration is not implemented; no external system was called.',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
            return true;
        }

        $this->load->library('Dso_pms_mock');
        $response = $this->Dso_pms_mock->sync_reservation($reservation);
        $this->db->where('id', $reservation_id)->update($this->table, array(
            'pms_reference'  => $response['pms_reference'],
            'pms_room_no'    => $response['pms_room_no'],
            'pms_status'     => $response['pms_status'],
            'pms_synced_at'  => $response['synced_at'],
        ));
        $this->db->insert('dso_notifications', array(
            'user_id'    => null,
            'role'       => 'Reservation Team',
            'type'       => 'pms_synced_mock',
            'message'    => 'Reservation #' . $reservation_id . ' synced to mock PMS (confirmation ' . $response['pms_reference'] . ', room ' . $response['pms_room_no'] . '). No real PMS is contracted yet - this is simulated data.',
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    /** Call after a status change (e.g. check-in/check-out) to refresh the mock/live PMS state. */
    public function sync_pms_status_change($reservation_id, $new_status)
    {
        $this->load->config('dso_integrations');
        $mode = $this->config->item('dso_pms_mode');
        $mode = $mode ? $mode : 'mock';
        if ($mode === 'off') {
            return true;
        }

        $reservation = $this->get($reservation_id);
        $this->load->library('Dso_pms_mock');
        $response = $this->Dso_pms_mock->sync_status_change($reservation, $new_status);
        $this->db->where('id', $reservation_id)->update($this->table, array(
            'pms_status'    => $response['pms_status'],
            'pms_synced_at' => $response['synced_at'],
        ));
        return true;
    }

    /**
     * Attempts a short-timeout, non-blocking HTTP POST of $payload to
     * $endpoint. Returns true on a 2xx response, or an error string on
     * any failure (unreachable host, timeout, non-2xx, no curl extension).
     * Never throws - callers always have a safe fallback path.
     */
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

    public function sum_today()
    {
        return (float) $this->db->select_sum('total_amount')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('status !=', 'Cancelled')
            ->get($this->table)->row()->total_amount;
    }

    public function sum_month_for_user($user_id, $month)
    {
        $row = $this->db->select_sum('total_amount')
            ->where('created_by', $user_id)
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", $month)
            ->where('status !=', 'Cancelled')
            ->get($this->table)->row();
        return (float) ($row->total_amount ? $row->total_amount : 0);
    }

    public function room_nights_month_for_user($user_id, $month)
    {
        $row = $this->db->select_sum('room_nights')
            ->where('created_by', $user_id)
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", $month)
            ->where('status !=', 'Cancelled')
            ->get($this->table)->row();
        return (int) ($row->room_nights ? $row->room_nights : 0);
    }

    public function count_month_for_user($user_id, $month)
    {
        return $this->db->where('created_by', $user_id)
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", $month)
            ->where('status !=', 'Cancelled')
            ->count_all_results($this->table);
    }

    public function count_today_for_user($user_id)
    {
        return $this->db->where('created_by', $user_id)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->count_all_results($this->table);
    }

    /**
     * Day-by-day revenue within one month (Dashboard MTD revenue trend
     * chart). $user_id filters to one sales owner (Daily Sales Dashboard);
     * null aggregates every reservation (HOD Sales Dashboard).
     * @return array day-of-month (1-31) => total revenue, only days with data included
     */
    public function daily_revenue_trend($month, $user_id = null)
    {
        $this->db->select("DAY(created_at) as d, SUM(total_amount) as total")
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", $month)
            ->where('status !=', 'Cancelled');
        if ($user_id) {
            $this->db->where('created_by', $user_id);
        }
        $rows = $this->db->group_by('d')->order_by('d', 'asc')->get($this->table)->result();

        $trend = array();
        foreach ($rows as $row) {
            $trend[(int) $row->d] = (float) $row->total;
        }
        return $trend;
    }

    public function revenue_by_property()
    {
        return $this->db->select('property, SUM(total_amount) as total')
            ->group_by('property')
            ->order_by('total', 'desc')
            ->get($this->table)->result();
    }

    public function revenue_by_month()
    {
        return $this->db->select("DATE_FORMAT(created_at,'%Y-%m') as ym, SUM(total_amount) as total")
            ->group_by('ym')
            ->order_by('ym', 'desc')
            ->get($this->table)->result();
    }

    public function revenue_by_account()
    {
        $sql = "SELECT a.company_name, SUM(r.total_amount) as total
                FROM dso_reservations r
                JOIN dso_accounts a ON a.id = r.account_id
                GROUP BY a.company_name
                ORDER BY total DESC";
        return $this->db->query($sql)->result();
    }

    public function today_list()
    {
        return $this->db->where('DATE(created_at)', date('Y-m-d'))->get($this->table)->result();
    }

    public function room_nights_by_property()
    {
        return $this->db->select('property, SUM(room_nights) as total_room_nights')
            ->where('status !=', 'Cancelled')
            ->group_by('property')
            ->order_by('total_room_nights', 'desc')
            ->get($this->table)->result();
    }

    public function room_nights_by_month()
    {
        return $this->db->select("DATE_FORMAT(created_at,'%Y-%m') as ym, SUM(room_nights) as total_room_nights")
            ->where('status !=', 'Cancelled')
            ->group_by('ym')
            ->order_by('ym', 'desc')
            ->get($this->table)->result();
    }
}
