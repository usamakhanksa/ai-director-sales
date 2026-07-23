<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_property_rates_model extends CI_Model
{
    protected $table = 'dso_property_rates';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_for_property($property_id)
    {
        return $this->db->where('property_id', $property_id)->order_by('rate_type', 'asc')->get($this->table)->result();
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
