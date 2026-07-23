<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_audit_log_model
 *
 * Backs dso_audit_log (migration 012). Written exclusively via
 * Dso_Controller::audit() - never queried by application logic other than
 * the Administration > Audit Log viewer below, so this model stays a thin
 * insert + list/filter pair.
 */
class Dso_audit_log_model extends CI_Model
{
    protected $table = 'dso_audit_log';

    public function __construct()
    {
        parent::__construct();
    }

    /** $before/$after may be a stdClass row, an array, or null. */
    public function record($user_id, $table_name, $row_id, $action, $before = null, $after = null)
    {
        $this->db->insert($this->table, array(
            'user_id'     => $user_id ?: null,
            'table_name'  => $table_name,
            'row_id'      => $row_id ?: null,
            'action'      => $action,
            'before_json' => $before !== null ? json_encode($before) : null,
            'after_json'  => $after !== null ? json_encode($after) : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ));
        return $this->db->insert_id();
    }

    public function get_all($filters = array(), $limit = 100)
    {
        $this->db->select('a.*, u.name as user_name')
            ->from($this->table . ' a')
            ->join('dso_users u', 'u.id = a.user_id', 'left');
        if (!empty($filters['table_name'])) {
            $this->db->where('a.table_name', $filters['table_name']);
        }
        if (!empty($filters['row_id'])) {
            $this->db->where('a.row_id', $filters['row_id']);
        }
        $this->db->order_by('a.created_at', 'desc')->limit($limit);
        return $this->db->get()->result();
    }
}
