<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_roles_model extends CI_Model
{
    protected $table = 'dso_roles';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->where('deleted_at', null)->order_by('name', 'asc')->get($this->table)->result();
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

    /** Soft delete (system roles still cannot be removed) - sets deleted_at instead of removing the row. */
    public function delete($id)
    {
        return $this->db->where('id', $id)->where('is_system', 0)->update($this->table, array('deleted_at' => date('Y-m-d H:i:s')));
    }

    public function set_permissions($role_id, array $permission_ids)
    {
        $this->db->where('role_id', $role_id)->delete('dso_role_permissions');
        foreach ($permission_ids as $permission_id) {
            $this->db->insert('dso_role_permissions', array(
                'role_id'       => $role_id,
                'permission_id' => $permission_id,
            ));
        }
    }

    public function permission_ids_for_role($role_id)
    {
        $rows = $this->db->select('permission_id')->where('role_id', $role_id)->get('dso_role_permissions')->result();
        return array_map(function ($r) { return (int) $r->permission_id; }, $rows);
    }
}
