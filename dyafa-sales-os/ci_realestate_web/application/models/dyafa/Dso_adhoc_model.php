<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_adhoc_model extends CI_Model
{
    protected $table = 'dso_adhoc_sales';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = array())
    {
        $this->db->from($this->table)->where('deleted_at', null);
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (!empty($filters['event_type'])) {
            $this->db->where('event_type', $filters['event_type']);
        }
        if (!empty($filters['sort']) && $filters['sort'] === 'value') {
            $this->db->order_by('estimated_value', 'desc');
        } else {
            $this->db->order_by('created_at', 'desc');
        }
        return $this->db->get()->result();
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

    /**
     * Used by the Opportunities Board (drag-drop) AJAX move endpoint.
     * Thin wrapper around the generic update() so callers don't need to
     * build the $data array themselves for this single-field mutation.
     */
    public function update_status($id, $status)
    {
        return $this->update($id, array('status' => $status));
    }

    public function sum_month_for_user($user_id, $month)
    {
        $row = $this->db->select_sum('estimated_value')
            ->where('owner_id', $user_id)
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", $month)
            ->where('status', 'Confirmed')
            ->where('deleted_at', null)
            ->get($this->table)->row();
        return (float) ($row->estimated_value ? $row->estimated_value : 0);
    }

    public function sum_today()
    {
        return (float) $this->db->select_sum('estimated_value')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('deleted_at', null)
            ->get($this->table)->row()->estimated_value;
    }

    public function today_list()
    {
        return $this->db->where('DATE(created_at)', date('Y-m-d'))->where('deleted_at', null)->get($this->table)->result();
    }

    public function counts_by_status()
    {
        return $this->db->select('status, count(*) as cnt')
            ->where('deleted_at', null)
            ->group_by('status')
            ->get($this->table)->result();
    }
}
