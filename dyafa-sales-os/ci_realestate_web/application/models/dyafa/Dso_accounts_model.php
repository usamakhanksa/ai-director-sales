<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_accounts_model extends CI_Model
{
    protected $table = 'dso_accounts';

    public function __construct()
    {
        parent::__construct();
    }

    /** $account_ids: optional territory scope (Teams) - null means no restriction. */
    public function get_all($account_ids = null, $filters = array())
    {
        $this->db->from($this->table)->where('deleted_at', null);
        if ($account_ids !== null) {
            $this->db->where_in('id', $account_ids);
        }
        if (!empty($filters['industry'])) {
            $this->db->like('industry', $filters['industry']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        return $this->db->order_by('created_at', 'desc')->get()->result();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->where('deleted_at', null)->get($this->table)->row();
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

    public function top_accounts_by_revenue($limit = 5)
    {
        $sql = "SELECT a.id, a.company_name, COALESCE(SUM(r.total_amount),0) as total_revenue
                FROM dso_accounts a
                LEFT JOIN dso_reservations r ON r.account_id = a.id AND r.status NOT IN ('Cancelled','NoShow')
                WHERE a.deleted_at IS NULL
                GROUP BY a.id, a.company_name
                ORDER BY total_revenue DESC
                LIMIT ?";
        return $this->db->query($sql, array($limit))->result();
    }

    public function performance()
    {
        $sql = "SELECT a.id, a.company_name, a.status, a.is_vip,
                    COALESCE(SUM(CASE WHEN r.status NOT IN ('Cancelled','NoShow') THEN r.total_amount ELSE 0 END),0) as total_revenue,
                    COALESCE(SUM(CASE WHEN r.status NOT IN ('Cancelled','NoShow') THEN r.room_nights ELSE 0 END),0) as total_room_nights,
                    COUNT(CASE WHEN r.status NOT IN ('Cancelled','NoShow') THEN r.id ELSE NULL END) as reservation_count
                FROM dso_accounts a
                LEFT JOIN dso_reservations r ON r.account_id = a.id
                WHERE a.deleted_at IS NULL
                GROUP BY a.id, a.company_name, a.status, a.is_vip
                ORDER BY total_revenue DESC";
        return $this->db->query($sql)->result();
    }
}
