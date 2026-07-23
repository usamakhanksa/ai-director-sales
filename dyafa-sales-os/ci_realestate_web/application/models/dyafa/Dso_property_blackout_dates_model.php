<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_property_blackout_dates_model extends CI_Model
{
    protected $table = 'dso_property_blackout_dates';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_property($property_id)
    {
        return $this->db->where('property_id', $property_id)->order_by('blackout_date', 'asc')->get($this->table)->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
