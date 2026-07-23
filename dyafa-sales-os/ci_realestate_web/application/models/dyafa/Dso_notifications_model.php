<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_notifications_model extends CI_Model
{
    protected $table = 'dso_notifications';

    public function __construct()
    {
        parent::__construct();
    }

    /** Administration > Notification Center: all notifications across every user, most recent first. */
    public function get_all()
    {
        return $this->db
            ->select('n.*, u.name as user_name')
            ->from($this->table . ' n')
            ->join('dso_users u', 'u.id = n.user_id', 'left')
            ->order_by('n.created_at', 'desc')
            ->get()->result();
    }

    /** Shared WHERE-clause builder for get_for_user()/count_for_user() so pagination counts always match the listed rows. */
    protected function _apply_user_filter($user_id, $role)
    {
        $this->db->from($this->table)->group_start()->where('user_id', $user_id);
        if ($role) {
            $this->db->or_where('role', $role);
        }
        $this->db->group_end();
    }

    /** $limit/$offset: optional pagination (Notifications::index() list page); omitted = unbounded, unchanged for every other caller. */
    public function get_for_user($user_id, $role = null, $limit = null, $offset = 0)
    {
        $this->_apply_user_filter($user_id, $role);
        $this->db->order_by('created_at', 'desc');
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get()->result();
    }

    /** Row count for the same filters, used to build pagination links. */
    public function count_for_user($user_id, $role = null)
    {
        $this->_apply_user_filter($user_id, $role);
        return $this->db->count_all_results();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function mark_read($id)
    {
        return $this->db->where('id', $id)->update($this->table, array('is_read' => 1));
    }

    public function exists_containing($type, $needle)
    {
        return $this->db->where('type', $type)
            ->like('message', $needle)
            ->count_all_results($this->table) > 0;
    }

    public function exists_this_month($type, $needle)
    {
        return $this->db->where('type', $type)
            ->like('message', $needle)
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", date('Y-m'))
            ->count_all_results($this->table) > 0;
    }
}
