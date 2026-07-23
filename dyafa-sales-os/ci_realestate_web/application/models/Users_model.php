<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Users_model extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function show_tutors()
    {
		$query = $this->db->query("select * from users where user_type='tutors' and user_verified='Y' and user_status='Y' ");
	    return $query; 
    }
	
	function show_students()
    {
		$query = $this->db->query("select * from users where user_type='students' and user_verified='Y' and user_status='Y' ");
	    return $query; 
    }
	
	function del_tutors()
    {
		$user_id=$this->uri->segment(3);
		if( isset($user_id) && !empty($user_id)  )
		{	$this->db->delete('users', array('user_id' => $user_id)); 	}
	}

	function del_users($users = null,$action = null)
    {
		if(!empty($users) && !empty($action) )
		{
			$user_ids = (isset($_POST['user_id'])) ?  $_POST['user_id'] : array() ;
			if(count($user_ids)>0)
			{
				$this->db->where_in('user_id', $user_ids);
				$this->db->delete('users'); 
			}
	   
		}
		else
		{
		  $user_id=$this->uri->segment(3);
		  if( isset($user_id) && !empty($user_id)  )
		  {	$this->db->delete('users', array('user_id' => $user_id)); 		}
		}
	}

    function insert_entry()
    {
        $this->title   = $_POST['title']; // please read the below note
        $this->content = $_POST['content'];
        $this->date    = time();

        $this->db->insert('entries', $this);
    }

    function update_entry()
    {
        $this->title   = $_POST['title'];
        $this->content = $_POST['content'];
        $this->date    = time();

        $this->db->update('entries', $this, array('id' => $_POST['id']));
    }

}