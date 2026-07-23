<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_properties_model extends CI_Model
{
    protected $table = 'dso_properties';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Accepts either a plain status string (legacy shortcut, e.g.
     * get_all('Active') used by Portal/Adhoc/Dso_sales_assistant) or a
     * filters array (e.g. array('status' => 'Active', 'city' => 'Riyadh')).
     */
    public function get_all($filters = null)
    {
        if (is_string($filters)) {
            $filters = array('status' => $filters);
        } elseif (!is_array($filters)) {
            $filters = array();
        }

        $this->db->from($this->table)->where('deleted_at', null);
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (!empty($filters['city'])) {
            $this->db->where('city', $filters['city']);
        }
        $this->db->order_by('name', 'asc');
        return $this->db->get()->result();
    }

    /** Distinct list of cities in use, for the Properties list filter dropdown. */
    public function get_distinct_cities()
    {
        $rows = $this->db->distinct()
            ->select('city')
            ->where('city IS NOT NULL')
            ->where('city !=', '')
            ->where('deleted_at', null)
            ->order_by('city', 'asc')
            ->get($this->table)->result();
        return array_map(function ($r) { return $r->city; }, $rows);
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->where('deleted_at', null)->get($this->table)->row();
    }

    /** Flat list of Active property names, for reservation/contract dropdowns. */
    public function get_active_names()
    {
        $rows = $this->db->select('name')->where('status', 'Active')->where('deleted_at', null)->order_by('name', 'asc')->get($this->table)->result();
        return array_map(function ($r) { return $r->name; }, $rows);
    }

    public function name_exists($name, $exclude_id = null)
    {
        $this->db->where('name', $name)->where('deleted_at', null);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
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

    /** Property Management > Availability Settings: toggle the simple bookable flag. */
    public function set_bookable($id, $bool)
    {
        return $this->db->where('id', $id)->update($this->table, array('is_bookable' => $bool ? 1 : 0));
    }

    /** Revenue + room-nights per property, joined against reservations by property name (reservations store plain name strings, not an FK). */
    public function performance()
    {
        $sql = "SELECT p.id, p.name, p.city,
                       COALESCE(SUM(r.total_amount), 0) as total_revenue,
                       COALESCE(SUM(r.room_nights), 0) as total_room_nights,
                       COUNT(r.id) as total_reservations
                FROM dso_properties p
                LEFT JOIN dso_reservations r ON r.property = p.name AND r.status != 'Cancelled'
                WHERE p.deleted_at IS NULL
                GROUP BY p.id, p.name, p.city
                ORDER BY total_revenue DESC";
        return $this->db->query($sql)->result();
    }
}
