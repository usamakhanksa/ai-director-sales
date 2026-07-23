<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dso_market_intelligence_model extends CI_Model
{
    protected $table = 'dso_market_intelligence';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_active()
    {
        return $this->db->where('is_active', 1)->get($this->table)->result();
    }
}
