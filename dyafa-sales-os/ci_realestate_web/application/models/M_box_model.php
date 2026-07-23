<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_box_model extends CI_Model {

    var $cat_name   = '';
    var $parent_id = '';
    var $cat_type    = '';
	var $cat_level    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function show_inbox($user_id)
    {
		$query = $this->db->query("select mb.*,us1.user_name as reciever, us2.user_name as sender  from mail_box as mb
				      inner join users as us1 on us1.user_id = mb.user_id
					  inner join users as us2 on us2.user_id = mb.sent_by where mb.user_id='$user_id' and mb.trash_status='N'");
	    return $query; 
    }

    function show_outbox($user_id)
    {
		$query = $this->db->query("select mb.*,us1.user_name as reciever, us2.user_name as sender  from mail_box as mb
				      inner join users as us1 on us1.user_id = mb.user_id
					  inner join users as us2 on us2.user_id = mb.sent_by where mb.sent_by='$user_id' ");
	    return $query; 
    }
    function show_trash($user_id)
    {
		$query = $this->db->query("select mb.*,us1.user_name as reciever, us2.user_name as sender  from mail_box as mb
				      inner join users as us1 on us1.user_id = mb.user_id
					  inner join users as us2 on us2.user_id = mb.sent_by where mb.user_id='$user_id' and mb.trash_status='Y'");
	    return $query; 
    }
	
	function show_msg_detail($user_id,$msg_id)
    {
		$query = $this->db->query("select mb.*,us1.user_name as reciever, us2.user_name as sender  from mail_box as mb
									inner join users as us1 on us1.user_id = mb.user_id
									inner join users as us2 on us2.user_id = mb.sent_by  
									where mb.msg_id='$msg_id' #and mb.user_id='$user_id'");
	    return $query; 
    }
	
	
	function read_msg($msg_id)
    {
		$data = array('read_status' => 'Y' );
		//$this->db->where('user_id', $user_id);
		$this->db->where('msg_id', $msg_id);
		$this->db->update('mail_box', $data);
	}
	function del_message()
    {
		
		$msg_ids = (isset($_POST['msg_id'])) ?  $_POST['msg_id'] : array() ;
		if(count($msg_ids)>0)
		{
			//$this->db->where('user_id', $user_id);
			$this->db->where_in('msg_id', $msg_ids);
			$this->db->delete('mail_box'); 
		//	echo $this->db->last_query();exit;
		}
		//else echo "sorry";exit;
	}
	
	function get_rec_ids()
    {
		$groups = (isset($_POST['groups'])) ?  $_POST['groups'] : array() ;
		
		if(count($groups)>0)
		{
			$query = $this->db->query("select distinct(user_id) as rec_ids from group_members 
			where group_id in(".implode(",",$groups).")");
			if($query->num_rows()>0)
			{
				$rec_ids=array();
				foreach ($query->result() as $row)
				{	$rec_ids[] = $row->rec_ids;				}
				return $rec_ids;
			}
			else 
				return 0;   
		}
		else
			return 0;
	}
	
	function send_trash($user_id)
    {
		$msg_ids = (isset($_POST['msg_id'])) ?  $_POST['msg_id'] : array() ;
		if(count($msg_ids)>0)
		{
			$data = array('trash_status' => 'Y' );
			//$this->db->where('user_id', $user_id);
			$this->db->where_in('msg_id', $msg_ids);
			$this->db->update('mail_box', $data);
		}
	}
	
	
	
	function send_message($sender_id,$reciever_id)
    {
		$data = array('user_id' =>   $reciever_id ,'subject' => $_POST['msg_title']	 ,'descript' => $_POST['msg_desc'],'sent_by' => $sender_id,'sent_date' =>  time());
  		$this->db->insert('mail_box', $data); 	
    }

	function send_new_message($rec_ids)
    {
		$data =array();
		foreach($rec_ids as $rec_id)
		{
			if($this->session->userdata('user_id')!=$rec_id)
			{
			  $data[] = array( 
			  'user_id' =>   $rec_id ,	 
			  'subject' => $_POST['msg_title']	 ,
			  'descript' => $_POST['msg_desc']	 ,
			  'sent_by' => $this->session->userdata('user_id')	 ,
			  'sent_date' =>  time(),
			   );
			}
		}
  		$this->db->insert_batch('mail_box', $data); 	
		$this->session->set_flashdata('flag_flash', 
			'<div class="valid_box">'.$this->db->affected_rows().' Message Sent Successfully </div>');
		
    }
}