<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_activities_model extends CI_Model
{
    protected $table = 'dso_activities';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_account($account_id)
    {
        return $this->db->where('account_id', $account_id)->order_by('activity_date', 'desc')->get($this->table)->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function count_today()
    {
        return $this->db->where('DATE(activity_date)', date('Y-m-d'))->count_all_results($this->table);
    }

    public function count_by_type_user_month($user_id, $type, $month)
    {
        return $this->db->where('created_by', $user_id)
            ->where('activity_type', $type)
            ->where("DATE_FORMAT(activity_date, '%Y-%m') =", $month)
            ->count_all_results($this->table);
    }

    public function get_all()
    {
        return $this->db->order_by('activity_date', 'desc')->get($this->table)->result();
    }

    public function counts_by_type()
    {
        return $this->db->select('activity_type, count(*) as cnt')
            ->group_by('activity_type')
            ->get($this->table)->result();
    }

    /** Activities logged by a single user (My Activities), most recent first. */
    public function get_by_creator($user_id, $filters = array())
    {
        $this->db->where('created_by', $user_id);
        $this->_apply_filters($filters);
        return $this->db->order_by('activity_date', 'desc')->get($this->table)->result();
    }

    /** Activities logged by any of the given user ids (Team Activities), most recent first. */
    public function get_by_team(array $user_ids, $filters = array())
    {
        if (empty($user_ids)) {
            return array();
        }
        $this->db->where_in('created_by', $user_ids);
        $this->_apply_filters($filters);
        return $this->db->order_by('activity_date', 'desc')->get($this->table)->result();
    }

    protected function _apply_filters($filters)
    {
        if (!empty($filters['activity_type'])) {
            $this->db->where('activity_type', $filters['activity_type']);
        }
        if (!empty($filters['activity_date_from'])) {
            $this->db->where('activity_date >=', $filters['activity_date_from']);
        }
        if (!empty($filters['activity_date_to'])) {
            $this->db->where('activity_date <=', $filters['activity_date_to']);
        }
    }
}
