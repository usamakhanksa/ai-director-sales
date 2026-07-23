<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_lead_scoring_config_model
 *
 * Backs the "AI Lead Generation > Lead Scoring Config" screen (Administration
 * -adjacent HOD tool) - lets an HOD tune the relative weight of each signal
 * that Dso_lead_scoring.php factors into a lead's score, without a code
 * deploy. See dyafa_sales_os_migration_008_admin_rbac_teams.sql for the
 * dso_lead_scoring_config table shape and seed rows.
 */
class Dso_lead_scoring_config_model extends CI_Model
{
    protected $table = 'dso_lead_scoring_config';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->order_by('id', 'asc')->get($this->table)->result();
    }

    /**
     * @return array signal_key => (int) weight, for Dso_lead_scoring to consume.
     * Empty array when the table doesn't exist yet or has no rows - callers
     * must fall back to hardcoded defaults in that case.
     */
    public function get_weights_map()
    {
        $map = array();
        if (!$this->db->table_exists($this->table)) {
            return $map;
        }
        $rows = $this->db->get($this->table)->result();
        foreach ($rows as $row) {
            $map[$row->signal_key] = (int) $row->weight;
        }
        return $map;
    }

    public function update_weight($signal_key, $weight)
    {
        return $this->db->where('signal_key', $signal_key)->update($this->table, array(
            'weight'     => (int) $weight,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }
}
