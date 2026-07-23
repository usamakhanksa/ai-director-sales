<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_groups_model extends CI_Model {

    var $cat_name   = '';
    var $parent_id = '';
    var $cat_type    = '';
	var $cat_level    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function show_groups($user_id = null)
    {
		if(is_null($user_id))
		    $add_str="";
		else
			$add_str=" and users.user_id='$user_id' ";
		$query = $this->db->query("select groups.* ,count(group_members.group_id) as members , users.user_name , users.user_type from groups 
							inner join users on users.user_id=groups.moderator
							left join group_members on groups.group_id =group_members.group_id
							where users.user_status='Y' ".$add_str." group by group_members.group_id");
	    return $query; 
    }

	
	
	function show_group_detail($user_id,$group_id)
    {
	
		/********** this is the new  query **********/
		$query = $this->db->query("select group_members.member_id,group_members.member_created,	group_members.member_status,
						users.user_name as member_name,users.user_type as member_type ,users.user_status,count(discussions.disc_type)as topics
						,count(d2.disc_type)as replies   						from group_members
						inner join groups on groups.group_id=group_members.group_id     and groups.group_id='$group_id'
						inner join users on users.user_id=group_members.user_id
						left join discussions on discussions.member_id=group_members.member_id  and discussions.disc_type='Topic'
						left join discussions d2 on d2.member_id=group_members.member_id   and d2.disc_type='Reply'
						where  users.user_status='Y'
						group by group_members.member_id");
							
							
	    return $query; 
    }
	
	function show_users_group_detail($user_id)
    {
		$query = $this->db->query("select group_members.member_id , group_members.group_id, groups.group_name from group_members 
				  inner join groups on groups.group_id = group_members.group_id   where user_id='$user_id'");
	    return $query; 
    }
	
	function show_post_detail($member_id,$group_id)
    {

	   $query = $this->db->query("select discussions.* ,count(d2.disc_parent) as reply , hit_count.hit_count from discussions 
				  left join discussions as d2 on d2.disc_parent = discussions.disc_id and d2.disc_type='reply'
				  left join hit_count on hit_count.field_id = discussions.disc_id and hit_count.field_name ='discussion' 
				  where discussions.member_id='$member_id' and discussions.group_id='$group_id'
				  and discussions.disc_type='Topic' and discussions.disc_parent='0' 
				  group by discussions.disc_id     ");
	   
	    return $query; 
    }
	function show_recent_discussions()
    {
		$query = $this->db->query("select discussions.*,count(d2.disc_id)as reply  ,hit_count.hit_count  from discussions 
			  left join discussions d2 on d2.disc_parent = discussions.disc_id 
			  left join hit_count on hit_count.field_id=discussions.disc_id   and hit_count.field_name='discussion'
			  where discussions.disc_type='Topic'  group by discussions.disc_id  order by hit_count.hit_count desc limit 10");
	    return $query; 
    }
	
	function show_popular_discussions()
    {
		$query = $this->db->query("select discussions.*,count(d2.disc_id)as reply ,hit_count.hit_count 	from discussions 
				left join discussions d2 on d2.disc_parent = discussions.disc_id 
				left join hit_count on hit_count.field_id=discussions.disc_id      and hit_count.field_name='discussion'
				where discussions.disc_type='Topic' 	group by discussions.disc_id  order by discussions.disc_date desc limit 10");
	    return $query; 
    }

	function show_recent_groups()
    {
		$query = $this->db->query("select *,count(groups.group_id) as members from groups
					  left join group_members on group_members.group_id=groups.group_id
					  group by groups.group_id order by group_created desc");
	    return $query; 
    }
	
	function show_popular_groups()
    {
		$query = $this->db->query("select *,count(groups.group_id) as members from groups
				left join group_members on group_members.group_id=groups.group_id
				group by groups.group_id order by members desc");
	    return $query; 
    }


	function show_my_posts($user_id)
    {
		/********** this is the  query **********/
		$query = $this->db->query(" select discussions.*,groups.*,count(d2.disc_parent) as reply ,coalesce( hit_count.hit_count,0)as hit_count from discussions 
					  inner join groups on groups.group_id = discussions.group_id
					  left join discussions d2 on d2.disc_parent= discussions.disc_id
					  left join hit_count on hit_count.field_id = discussions.disc_id and hit_count.field_name ='discussion'
					  where discussions.user_id='$user_id' and discussions.disc_type='Topic'
					  group by discussions.disc_id#,discussions.disc_type");
	    return $query; 
    }
	
	function show_disc_reply($disc_id)
    {
		/********** this is the  query **********/
		$query = $this->db->query("select * from discussions  
		inner join users on users.user_id = discussions.user_id and users.user_status='Y'
		where disc_parent='$disc_id' and disc_type='reply' order by disc_date desc		 ");
	    return $query; 
    }
	
	function get_discussion_detail($disc_id)
    {
		/********** this is the  query **********/
		$query = $this->db->query("select discussions.*,users.user_name,users.user_type from discussions
									inner join users on users.user_id = discussions.user_id
									where discussions.disc_id='$disc_id'");
	    return $query; 
    }
	
	
	function get_member_id($user_id,$group_id)
    {
		//////////      and groups.moderator='$user_id' 
//		echo "select * from group_members where group_id='$group_id' and user_id='$user_id'";
		$query = $this->db->query("select * from group_members where group_id='$group_id' and user_id='$user_id'");
		if($query->num_rows()>0)
		{
			$row= $query->row();	
			return $row->member_id;
		}
		else   return '0'; 
    }
	
	function can_user_reply($group_id,$user_id)
    {
		//////////      and groups.moderator='$user_id' 
//		echo "select * from group_members where group_id='$group_id' and user_id='$user_id'";
		$query = $this->db->query("select * from group_members where group_id='$group_id' and user_id='$user_id'");
		if($query->num_rows()>0)
			return TRUE;	
		else   
			return FALSE; 
    }
	
	function can_user_reply_discussion($disc_id,$user_id)
    {
		$query = $this->db->query("select * from group_members 
					where group_id=(select group_id from discussions where discussions.disc_id='$disc_id') and user_id='$user_id'");
		if($query->num_rows()>0)
			return TRUE;	
		else   
			return FALSE; 
    }
	
	function get_group_name($group_id)
    {
		//////////      and groups.moderator='$user_id' 
//		echo "select * from group_members where group_id='$group_id' and user_id='$user_id'";
		$query = $this->db->query("select * from groups where group_id='$group_id'");
		if($query->num_rows()>0)
		{
			$row= $query->row();	
			return $row->group_name;
		}
		else   return 'No Group'; 
    }
	
	function get_member_status($user_id,$group_id)
    {
		$query = $this->db->query("select * from group_members where group_id='$group_id' and user_id='$user_id'");
		if($query->num_rows()>0)
			return TRUE;
		else   
			return FALSE; 
    }
	
	function join_group($group_id,$user_id)
    {
		$query = $this->db->query("select * from group_members where group_id='$group_id' and user_id='$user_id'");
		if($query->num_rows()==0)
        {  $data = array( 'group_id' =>   $group_id ,	 'user_id' => $user_id ,	 'member_created' => time()	  );
  		  $this->db->insert('group_members', $data); 	
		}
    }
	
	
	function add_post_topic()
    {
          $data = array(
			 'disc_title' => addslashes(  $_POST['disc_title']) ,
			 'disc_desc' => addslashes(  $_POST['disc_desc']) ,
			 'member_id' =>   $_POST['member_id'] ,
			 'group_id' =>   $_POST['group_id'] ,
			 'disc_type' =>   $_POST['disc_type'] ,
			 'disc_parent' =>   $_POST['disc_parent'] ,
			 'user_id' => $this->session->userdata('user_id'),
			 'disc_date' => time(),
		  );
  		  $this->db->insert('discussions', $data); 	
    }
	function add_new_post_topic()
    {
          $data = array(
			 'disc_title' => addslashes(  $_POST['disc_title']) ,
			 'disc_desc' => addslashes(  $_POST['disc_desc']) ,
			 'member_id' => $this->get_member_id( $this->session->userdata('user_id'),$_POST['group']),
			 
			 'group_id' =>   $_POST['group'] ,
			 'disc_type' =>   $_POST['disc_type'] ,
			 'disc_parent' =>   $_POST['disc_parent'] ,
			 'user_id' => $this->session->userdata('user_id'),
			 'disc_date' =>  time(),
		  );
  		  $this->db->insert('discussions', $data); 	
    }

	
	function add_group()
    {
          $data = array(
			 'group_name' => addslashes( ucwords( $_POST['group_name'])) ,
			 'moderator' => $this->session->userdata('user_id'),
			 'group_created' =>  time()
		  );
  		  $this->db->insert('groups', $data); 	
		  
		  $group_id=$this->db->insert_id();
		  
		  $data = array(
			 'group_id' => $group_id ,
			 'user_id' => $this->session->userdata('user_id'),
			 'member_created' =>  time(),
		  );
  		  $this->db->insert('group_members', $data); 	
		  
    }

	
	
	function show_msg_detail($user_id,$msg_id)
    {
		$query = $this->db->query("select mb.*,us1.user_name as reciever, us2.user_name as sender  from mail_box as mb
									inner join users as us1 on us1.user_id = mb.user_id
									inner join users as us2 on us2.user_id = mb.sent_by  
									where mb.msg_id='$msg_id' and mb.user_id='$user_id'");
	    return $query; 
    }
	
	
	

   
}