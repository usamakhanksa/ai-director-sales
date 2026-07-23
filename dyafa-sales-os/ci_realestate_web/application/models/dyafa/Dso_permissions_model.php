<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_permissions_model extends CI_Model
{
    protected $table = 'dso_permissions';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->order_by('group_name, label', 'asc')->get($this->table)->result();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function role_has_permission($role_id, $permission_key)
    {
        return (int) $this->db
            ->from('dso_role_permissions rp')
            ->join('dso_permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $role_id)
            ->where('p.permission_key', $permission_key)
            ->count_all_results() > 0;
    }

    public function keys_for_role($role_id)
    {
        $rows = $this->db
            ->select('p.permission_key')
            ->from('dso_role_permissions rp')
            ->join('dso_permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $role_id)
            ->get()->result();
        return array_map(function ($r) { return $r->permission_key; }, $rows);
    }
}
