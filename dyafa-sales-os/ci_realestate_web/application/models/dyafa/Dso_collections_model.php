<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_collections_model extends CI_Model
{
    protected $table = 'dso_collections';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = array())
    {
        $this->db->from($this->table)->where('deleted_at', null);
        if (!empty($filters['account_id'])) {
            $this->db->where('account_id', $filters['account_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        $this->db->order_by('due_date', 'asc');
        return $this->db->get()->result();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->where('deleted_at', null)->get($this->table)->row();
    }

    public function get_by_account($account_id)
    {
        return $this->db->where('account_id', $account_id)->where('deleted_at', null)->order_by('due_date', 'asc')->get($this->table)->result();
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

    public function outstanding_total_for_account($account_id)
    {
        $row = $this->db->select('SUM(amount - paid_amount) as outstanding')
            ->where('account_id', $account_id)
            ->where('deleted_at', null)
            ->where('status !=', 'Paid')
            ->get($this->table)->row();
        return (float) ($row->outstanding ? $row->outstanding : 0);
    }

    public function aging_buckets()
    {
        $sql = "SELECT a.company_name, c.account_id,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), c.due_date) BETWEEN 0 AND 30 THEN (c.amount-c.paid_amount) ELSE 0 END) as b_0_30,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), c.due_date) BETWEEN 31 AND 60 THEN (c.amount-c.paid_amount) ELSE 0 END) as b_31_60,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), c.due_date) BETWEEN 61 AND 90 THEN (c.amount-c.paid_amount) ELSE 0 END) as b_61_90,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), c.due_date) > 90 THEN (c.amount-c.paid_amount) ELSE 0 END) as b_90_plus,
                    SUM(c.amount - c.paid_amount) as total_outstanding
                FROM dso_collections c
                JOIN dso_accounts a ON a.id = c.account_id
                WHERE c.status != 'Paid' AND c.deleted_at IS NULL
                GROUP BY c.account_id, a.company_name
                ORDER BY total_outstanding DESC";
        return $this->db->query($sql)->result();
    }

    public function sum_collected_today()
    {
        // paid today is not separately tracked (no paid_date column); approximate using
        // collections whose status is Paid and due_date is today, plus a simple heuristic.
        // This is documented as an approximation in implementation.md.
        return (float) $this->db->select_sum('paid_amount')
            ->where('DATE(due_date)', date('Y-m-d'))
            ->where('deleted_at', null)
            ->get($this->table)->row()->paid_amount;
    }

    public function sum_collected_month_for_owner($user_id, $month)
    {
        $sql = "SELECT COALESCE(SUM(c.paid_amount),0) as total
                FROM dso_collections c
                JOIN dso_accounts a ON a.id = c.account_id
                WHERE a.account_owner_id = ? AND c.deleted_at IS NULL
                AND DATE_FORMAT(c.created_at, '%Y-%m') = ?";
        $row = $this->db->query($sql, array($user_id, $month))->row();
        return (float) ($row ? $row->total : 0);
    }

    public function outstanding_grand_total()
    {
        $row = $this->db->select('SUM(amount - paid_amount) as outstanding')
            ->where('status !=', 'Paid')
            ->where('deleted_at', null)
            ->get($this->table)->row();
        return (float) ($row->outstanding ? $row->outstanding : 0);
    }

    public function due_or_overdue_not_notified_today()
    {
        return $this->db->where_in('status', array('Pending', 'Overdue'))
            ->where('due_date <=', date('Y-m-d'))
            ->where('deleted_at', null)
            ->get($this->table)->result();
    }

    /**
     * Credit Limits report (BRD Collections gap item): every account that has
     * a contract, its credit_limit/credit_days from dso_contracts, and its
     * current outstanding balance (SUM of amount - paid_amount across
     * non-Paid dso_collections rows), so Finance/Sales can see who is
     * approaching or over their contractual credit limit.
     */
    public function credit_limit_report()
    {
        $sql = "SELECT a.id as account_id, a.company_name,
                    c.credit_limit, c.credit_days,
                    COALESCE(SUM(CASE WHEN col.status != 'Paid' THEN (col.amount - col.paid_amount) ELSE 0 END), 0) as outstanding
                FROM dso_contracts c
                JOIN dso_accounts a ON a.id = c.account_id
                LEFT JOIN dso_collections col ON col.account_id = a.id AND col.deleted_at IS NULL
                WHERE c.deleted_at IS NULL
                GROUP BY a.id, a.company_name, c.credit_limit, c.credit_days
                ORDER BY outstanding DESC";
        return $this->db->query($sql)->result();
    }
}
