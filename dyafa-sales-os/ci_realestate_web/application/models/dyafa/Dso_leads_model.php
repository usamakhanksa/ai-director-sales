<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_leads_model extends CI_Model
{
    protected $table = 'dso_leads';

    public function __construct()
    {
        parent::__construct();
    }

    /** Shared WHERE-clause builder for get_all()/count_all() so pagination counts always match the listed rows. */
    protected function _apply_filters($filters)
    {
        $this->db->from($this->table)->where('is_deleted', 0);
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (!empty($filters['owner_id'])) {
            $this->db->where('lead_owner_id', $filters['owner_id']);
        }
        if (!empty($filters['unassigned'])) {
            $this->db->where('lead_owner_id IS NULL');
        }
        if (!empty($filters['source'])) {
            $this->db->like('source', $filters['source']);
        }
    }

    /** $limit/$offset: optional pagination (Leads::index() list page); omitted = unbounded, unchanged for every other caller. */
    public function get_all($filters = array(), $limit = null, $offset = 0)
    {
        $this->_apply_filters($filters);
        $this->db->order_by('created_at', 'desc');
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get()->result();
    }

    /** Row count for the same filters, used to build pagination links. */
    public function count_all($filters = array())
    {
        $this->_apply_filters($filters);
        return $this->db->count_all_results();
    }

    public function get($id)
    {
        return $this->db->where('id', $id)->where('is_deleted', 0)->get($this->table)->row();
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

    public function soft_delete($id)
    {
        return $this->db->where('id', $id)->update($this->table, array('is_deleted' => 1));
    }

    public function assign($id, $owner_id)
    {
        return $this->db->where('id', $id)->update($this->table, array('lead_owner_id' => $owner_id));
    }

    public function company_name_exists($company_name)
    {
        return $this->db->where('company_name', $company_name)->where('is_deleted', 0)->count_all_results($this->table) > 0;
    }

    public function count_by_owner_and_month($owner_id, $month)
    {
        return $this->db->where('lead_owner_id', $owner_id)
            ->where('is_deleted', 0)
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", $month)
            ->count_all_results($this->table);
    }

    public function counts_by_status()
    {
        return $this->db->select('status, count(*) as cnt')
            ->from($this->table)
            ->where('is_deleted', 0)
            ->group_by('status')
            ->get()->result();
    }

    public function counts_by_category()
    {
        return $this->db->select('lead_category, count(*) as cnt')
            ->from($this->table)
            ->where('is_deleted', 0)
            ->group_by('lead_category')
            ->get()->result();
    }

    public function counts_by_source()
    {
        return $this->db->select('source, count(*) as cnt, SUM(estimated_revenue) as total_revenue')
            ->from($this->table)
            ->where('is_deleted', 0)
            ->group_by('source')
            ->get()->result();
    }

    public function counts_by_owner()
    {
        return $this->db->select('u.name as owner_name, l.status, count(*) as cnt')
            ->from($this->table . ' l')
            ->join('dso_users u', 'u.id = l.lead_owner_id', 'left')
            ->where('l.is_deleted', 0)
            ->group_by('u.name, l.status')
            ->get()->result();
    }

    public function conversion_stats_by_owner()
    {
        // won leads / total leads * 100 per owner
        $sql = "SELECT lead_owner_id,
                    COUNT(*) as total_leads,
                    SUM(CASE WHEN status='Won' THEN 1 ELSE 0 END) as won_leads
                FROM dso_leads
                WHERE is_deleted = 0
                GROUP BY lead_owner_id";
        return $this->db->query($sql)->result();
    }
}
