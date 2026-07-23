<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_ai_recommendations_model extends CI_Model
{
    protected $table = 'dso_ai_recommendations';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($status = null)
    {
        $this->db->from($this->table);
        if ($status) {
            $this->db->where('status', $status);
        }
        $this->db->order_by('created_at', 'desc');
        return $this->db->get()->result();
    }

    public function get_for_user($user_id, $status = null)
    {
        $this->db->from($this->table)->where('assigned_to', $user_id);
        if ($status) {
            $this->db->where('status', $status);
        }
        $this->db->order_by('created_at', 'desc');
        return $this->db->get()->result();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function mark_status($id, $status)
    {
        return $this->db->where('id', $id)->update($this->table, array('status' => $status));
    }

    /** AI Sales Assistant > Predictions / Next Best Actions sub-views: filter by type, optionally also by status. */
    public function get_by_type($type, $status = null)
    {
        $this->db->from($this->table)->where('type', $type);
        if ($status) {
            $this->db->where('status', $status);
        }
        $this->db->order_by('created_at', 'desc');
        return $this->db->get()->result();
    }

    /** AI Sales Assistant > Analytics: count of recommendations grouped by type. */
    public function counts_by_type()
    {
        return $this->db->select('type, COUNT(*) as total')->from($this->table)->group_by('type')->get()->result();
    }

    /** AI Sales Assistant > Analytics: count of recommendations grouped by status. */
    public function counts_by_status()
    {
        return $this->db->select('status, COUNT(*) as total')->from($this->table)->group_by('status')->get()->result();
    }

    /** De-dup helper used by the Cron generator: has this account already got a fresh recommendation of this type? */
    public function exists_recent($account_id, $type, $days = 30)
    {
        return $this->db->where('account_id', $account_id)
            ->where('type', $type)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days')))
            ->count_all_results($this->table) > 0;
    }
}
