<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Categories_model extends CI_Model {

    var $cat_name   = '';
    var $parent_id = '';
    var $cat_type    = '';
	var $cat_level    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function show_masters()
    {
		$query = $this->db->query("select * from category where cat_type='master' and parent_id='0'");
	    return $query; 
    }
	
	
    function add_masters()
    {
        $this->cat_name   =  addslashes( ucwords( $_POST['cat_name'] )); // please read the below note
        $this->parent_id = '0';
		$this->cat_type = 'master';
		$this->cat_level = '1';

        $this->db->insert('category', $this);
    }
/***            Add group on the front of the site     **/
  function show_sub_cats($cat_type,$parent_id,$cat_level)
    {
		/********** this is the  query **********/
		$query = $this->db->query("select * from category where cat_type='$cat_type' and parent_id='$parent_id'");
	    return $query; 
    }
	 function get_sub_cat_count($cat_type,$parent_id)
    {
		/********** this is the  query **********/
		$query = $this->db->query("select count(*) as sub_cat from category where cat_type='$cat_type' and parent_id='$parent_id'");
		if($query->num_rows()>0)
		{
			$row= $query->row();
			return $row->sub_cat;	
		}   
		else
	    return 0; 
    }


    function add_group()
    {
        $data = array(
			 'group_name' => addslashes( ucwords( $_POST['group_name'])) ,
			 'moderator' => $this->session->userdata('user_id'),
			 'group_created' => date('Y-m-d H:i:s', time()),
		  );
  
		  $this->db->insert('groups', $data); 	
    }


    function update_entry()
    {
        $this->title   = $_POST['title'];
        $this->content = $_POST['content'];
        $this->date    = time();

        $this->db->update('entries', $this, array('id' => $_POST['id']));
    }

}