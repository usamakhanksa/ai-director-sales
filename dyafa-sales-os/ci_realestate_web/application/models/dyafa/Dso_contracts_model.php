<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_contracts_model extends CI_Model
{
    protected $table = 'dso_contracts';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->where('deleted_at', null)->order_by('created_at', 'desc')->get($this->table)->result();
    }

    /** $account_ids: optional territory scope (Teams) - null means no restriction. */
    public function get_filtered($filter = null, $account_ids = null, $filters = array())
    {
        $this->db->where('deleted_at', null);
        if ($filter === 'active') {
            $this->db->where('status', 'Active');
        } elseif ($filter === 'pending') {
            $this->db->where('status', 'Pending Approval');
        } elseif ($filter === 'expiring') {
            $this->db->where('status IN (\'Active\', \'Pending Renewal\')', null, false);
            $this->db->where('expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)', null, false);
        }
        if ($account_ids !== null) {
            $this->db->where_in('account_id', $account_ids);
        }
        if (!empty($filters['account_manager_id'])) {
            $this->db->where('account_manager_id', $filters['account_manager_id']);
        }
        $order = $filter === 'expiring' ? 'expiry_date' : 'created_at';
        return $this->db->order_by($order, $filter === 'expiring' ? 'asc' : 'desc')->get($this->table)->result();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->where('deleted_at', null)->get($this->table)->row();
    }

    public function get_by_account($account_id)
    {
        return $this->db->where('account_id', $account_id)->where('deleted_at', null)->order_by('created_at', 'desc')->get($this->table)->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /** Soft delete - sets deleted_at instead of removing the row (audit/compliance requirement). */
    public function delete($id)
    {
        return $this->db->where('id', $id)->update($this->table, array('deleted_at' => date('Y-m-d H:i:s')));
    }

    public function funnel_counts()
    {
        return $this->db->select('status, count(*) as cnt')
            ->from($this->table)
            ->where('deleted_at', null)
            ->group_by('status')
            ->get()->result();
    }

    public function expiring_within_days($days = 30)
    {
        $sql = "SELECT * FROM dso_contracts
                WHERE status = 'Active' AND deleted_at IS NULL
                AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)";
        return $this->db->query($sql, array($days))->result();
    }

    public function get_allowed_properties($contract_id)
    {
        $c = $this->get($contract_id);
        if (!$c || empty($c->allowed_properties)) {
            return array();
        }
        return array_map('trim', explode(',', $c->allowed_properties));
    }

    /** Decodes corporate_rates JSON ({"Property Name": rate, ...}) into an array; empty/invalid JSON returns array(). */
    public function get_corporate_rates($contract_id)
    {
        $c = $this->get($contract_id);
        if (!$c || empty($c->corporate_rates)) {
            return array();
        }
        $decoded = json_decode($c->corporate_rates, true);
        return is_array($decoded) ? $decoded : array();
    }
}
