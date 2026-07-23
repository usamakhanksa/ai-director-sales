<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_teams_model extends CI_Model
{
    protected $table = 'dso_teams';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db
            ->select('t.*, u.name as hod_name')
            ->from($this->table . ' t')
            ->join('dso_users u', 'u.id = t.hod_user_id', 'left')
            ->where('t.deleted_at', null)
            ->order_by('t.name', 'asc')
            ->get()->result();
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

    /** Soft delete - keeps the territory-cleanup cascade, but sets deleted_at on the team row itself instead of removing it (audit/compliance requirement). */
    public function delete($id)
    {
        $this->db->where('team_id', $id)->delete('dso_team_properties');
        $this->db->where('team_id', $id)->delete('dso_team_accounts');
        $this->db->where('team_id', $id)->update('dso_users', array('team_id' => null));
        return $this->db->where('id', $id)->update($this->table, array('deleted_at' => date('Y-m-d H:i:s')));
    }

    public function members($team_id)
    {
        return $this->db->where('team_id', $team_id)->order_by('name', 'asc')->get('dso_users')->result();
    }

    public function property_ids($team_id)
    {
        $rows = $this->db->select('property_id')->where('team_id', $team_id)->get('dso_team_properties')->result();
        return array_map(function ($r) { return (int) $r->property_id; }, $rows);
    }

    public function account_ids($team_id)
    {
        $rows = $this->db->select('account_id')->where('team_id', $team_id)->get('dso_team_accounts')->result();
        return array_map(function ($r) { return (int) $r->account_id; }, $rows);
    }

    public function set_properties($team_id, array $property_ids)
    {
        $this->db->where('team_id', $team_id)->delete('dso_team_properties');
        foreach ($property_ids as $pid) {
            $this->db->insert('dso_team_properties', array('team_id' => $team_id, 'property_id' => $pid));
        }
    }

    public function set_accounts($team_id, array $account_ids)
    {
        $this->db->where('team_id', $team_id)->delete('dso_team_accounts');
        foreach ($account_ids as $aid) {
            $this->db->insert('dso_team_accounts', array('team_id' => $team_id, 'account_id' => $aid));
        }
    }

    /** Aggregated Team Performance data: revenue/room-nights/collections actuals per team for a month. */
    public function performance($month)
    {
        $sql = "SELECT t.id, t.name,
                    COALESCE(SUM(r.total_amount), 0) as revenue,
                    COALESCE(SUM(r.room_nights), 0) as room_nights,
                    COALESCE(SUM(c.paid_amount), 0) as collections
                FROM dso_teams t
                LEFT JOIN dso_users u ON u.team_id = t.id
                LEFT JOIN dso_reservations r ON r.created_by = u.id AND DATE_FORMAT(r.created_at, '%Y-%m') = ?
                LEFT JOIN dso_collections c ON c.account_id IN (SELECT account_id FROM dso_team_accounts WHERE team_id = t.id) AND DATE_FORMAT(c.created_at, '%Y-%m') = ? AND c.deleted_at IS NULL
                WHERE t.deleted_at IS NULL
                GROUP BY t.id, t.name";
        return $this->db->query($sql, array($month, $month))->result();
    }
}
