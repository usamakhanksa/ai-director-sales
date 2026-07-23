<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Register_model extends CI_Model {

    var $user_name   = '';
    var $user_email   = '';
    var $user_type = '';
    var $user_registered    = '';
	var $user_link_id    = '';
	var $user_code    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
	function get_user_login_detail($username,$userpass)
    {
		$query = $this->db->query("select * from users where user_email='".$this->db->escape_str($username)."' and user_pass='".md5($this->db->escape_str($userpass))."' ");
	    return $query; 
    }

   
	function get_user_detail($lid = null)
    {
		if(is_null($lid))
			$link_id=$this->uri->segment(3);
		else
			$link_id=$lid;		
		$query = $this->db->query("select * from users where user_code='$link_id' and user_verified='N'  ");
	    return $query; 
    }
	
	function get_user_type($user_code = null)
    {
		if(is_null($user_code))
			$code=$this->uri->segment(3);
		else
			$code=$user_code;		
		$query = $this->db->query("select * from users where user_code='$code'  ");
		$row= $query->row();
		return $row->user_type;
    }

	function check_email($mail )
    {
		$query = $this->db->query("select * from users where user_email='$mail'  ");
		if($query->num_rows()>0)
			return true;
		else
			return false;			
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

    function do_register()
    {
        $this->user_name   = ucwords($_POST['user_name']); 
		$this->user_email   = $_POST['user_email']; // please read the below note
        $this->user_type = $_POST['user_type'];
		/// it is changed
		
        $this->user_registered    =   time();
		//echo "<br>".time()."<br>".date('d/m/Y');
		$this->user_code = md5($_POST['user_email']);
		$this->user_link_id = site_url('register/done_register/'.$this->user_code);

        $this->db->insert('users', $this);
		//$this->db->last_query();
		//exit;
    }
	
	function get_cat_data_by_name($cat_type)
    {
		$query = $this->db->query("select * from category where cat_type='$cat_type' and parent_id!='0'  ");
		//echo $this->db->last_query();
	    return $query; 
	}
	
	
	function save_user_detail()
    {

/****************  Insert of userdetail    ************/
		$data = array(
		   'user_id' => $_POST['user_id'] ,
		   'gender' => $_POST['gender'],
		   'dob' => $_POST['dob'],
		   'country' => $_POST['country'],
		   'state' =>$_POST['state'],
		   'city' => $_POST['city'],
   		   'mob_no' =>$_POST['mob_no']
		);

		$this->db->insert('user_detail', $data); 
		
		if($_POST['user_type']=='tutors')
		{
		  $data = array(
			 'user_id' => $_POST['user_id'] ,
			 'edu_level' => $_POST['edu_level'],
			 'edu_class' =>$_POST['edu_class'],
			 'edu_subject' => $_POST['edu_subject'],
			 'tution_timings' =>$_POST['tution_timings'],
			 'edu_qualification' => $_POST['edu_qualification'],
			 'edu_expertise' =>$_POST['edu_expertise'],
			 'tution_cat' =>$_POST['tution_cat']
		  );
  
		  $this->db->insert('user_info_detail', $data); 	
			
		}
		

/****************  Update of users    ************/
		$data = array(
  			   'user_pass' => md5($_POST['user_pass']),
               'user_verified' => 'Y',
               'user_status' => 'Y',
            );
		$this->db->where('user_id', $_POST['user_id'] );
		$this->db->update('users', $data); 

	}
	
	function update_user_info_detail()
	{
		 $data = array(
			 'edu_level' => $_POST['edu_level'],
			 'edu_class' =>$_POST['edu_class'],
			 'edu_subject' => $_POST['edu_subject'],
			 'tution_timings' =>$_POST['tution_timings'],
			 'edu_qualification' => $_POST['edu_qualification'],
			 'edu_expertise' =>$_POST['edu_expertise'],
		  );
		$this->db->where('user_id', $_POST['user_id'] );
		$this->db->update('user_info_detail', $data); 
	}
	
	function update_user_detail()
	{
		 $data = array(
		   'gender' => $_POST['gender'],
		   'dob' => strtotime( $_POST['dob']),
		   'country' => $_POST['country'],
		   'state' =>$_POST['state'],
		   'city' => $_POST['city'],
   		   'mob_no' =>$_POST['mob_no']
		  );
		$this->db->where('user_id', $_POST['user_id'] );
		$this->db->update('user_detail', $data); 
	}
	function update_user_tution_cat_detail()
	{
		 $data = array(
			 'tution_cat' =>$_POST['tution_cat'],
		  );
		$this->db->where('user_id', $_POST['user_id'] );
		$this->db->update('user_info_detail', $data); 
	}
	
	function update_user_password_detail()
	{
		 $data = array(
			 'user_pass' => md5($_POST['user_pass']),
		  );
		$this->db->where('user_id', $_POST['user_id'] );
		$this->db->update('users', $data); 
	}   

}