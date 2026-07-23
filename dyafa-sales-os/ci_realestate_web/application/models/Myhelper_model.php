<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Myhelper_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function update_hit_count($field_name,$field_id)
    {
		
		$query = $this->db->get_where('hit_count', array('field_name' => $field_name , 'field_id' => $field_id));
		if($query->num_rows()>0)
		{
			$this->db->set('hit_count', 'hit_count + 1', FALSE);
			$this->db->where(array('field_name' => $field_name , 'field_id' => $field_id));
			$this->db->update('hit_count');
		}
		else
		{
			if($field_id!=0)
			{
			  $data = array(	 'field_name' => $field_name ,	'field_id' => $field_id,	'hit_count' => '1'		  );
			  $this->db->insert('hit_count', $data); 	
			}
		}
    }
}