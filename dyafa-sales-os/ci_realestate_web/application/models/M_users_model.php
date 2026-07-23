<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_users_model extends CI_Model {

    var $cat_name   = '';
    var $parent_id = '';
    var $cat_type    = '';
	var $cat_level    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function show_users_by_cat($cat_id )
    {
		$query = $this->db->query("select users.user_id,users.user_name,uid.*
				,tc.cat_name as user_cat ,el.cat_name as user_class ,es.cat_name as user_subject 	from users 
				inner join user_info_detail as uid on users.user_id = uid.user_id
				left join category as tc on tc.cat_id=uid.tution_cat      
				left join category as el on el.cat_id=uid.edu_class 
				left join category as es on es.cat_id=uid.edu_subject              
			where uid.tution_cat='$cat_id' ");
	    return $query; 
    }

    function show_all_users_by_cat($cat_id )
    {
		$query = $this->db->query("select users.user_id,users.user_name,uid.*
				,tc.cat_name as user_cat ,el.cat_name as user_class ,es.cat_name as user_subject	from users 
				inner join user_info_detail as uid on users.user_id = uid.user_id
				left join category as tc on tc.cat_id=uid.tution_cat      
				left join category as el on el.cat_id=uid.edu_class 
				left join category as es on es.cat_id=uid.edu_subject              
			where uid.tution_cat in(select cat_id from category where parent_id='$cat_id') ");
	    return $query; 
    }
	function show_user_info($user_id )
    {
		$query = $this->db->query("select users.user_id,users.user_name,uid.*
				,tc.cat_name as user_cat ,ec.cat_name as user_class ,es.cat_name as user_subject    
				,el.cat_name as user_level ,ee.cat_name as user_expertise   
				,user_detail.* 		from users 
				  inner join user_info_detail as uid on users.user_id = uid.user_id
				  left join category as tc on tc.cat_id=uid.tution_cat      
				  left join category as ec on ec.cat_id=uid.edu_class 
				  left join category as es on es.cat_id=uid.edu_subject
				  left join category as el on el.cat_id=uid.edu_level 
				  left join category as ee on ee.cat_id=uid.edu_expertise
				  left join user_detail on user_detail.user_id=users.user_id
				where users.user_id='$user_id' ");
	    return $query; 
    }
	
	
	
	function show_post_detail($member_id,$group_id)
    {
		/********** this is the  query **********/
		$query = $this->db->query("select discussions.* ,count(d2.disc_parent) as reply  from discussions 
			   left join discussions as d2 on d2.disc_parent = discussions.disc_id and d2.disc_type='reply'
			   where discussions.member_id='$member_id' and discussions.group_id='$group_id'
				  and discussions.disc_type='post' and discussions.disc_parent='0' group by discussions.disc_id	 ");
	    return $query; 
    }
	
/////////////////********************************///***********\\\\\\\\\\\\\\\\\\\\\\\\\\


	 function show_personal_info($user_id)
     {
		$query = $this->db->query("select users.user_name,user_detail.* from users 
                  left join user_detail on user_detail.user_id=users.user_id     where users.user_id='$user_id' ");
	    return $query; 
     }

    function show_educational_info($user_id)
    {
		$query = $this->db->query("select users.user_name  ,uid.* ,ec.cat_name as user_class ,es.cat_name as user_subject    
				,el.cat_name as user_level ,ee.cat_name as user_expertise               from users 
                  inner join user_info_detail as uid on users.user_id = uid.user_id
                  left join category as ec on ec.cat_id=uid.edu_class 
                  left join category as es on es.cat_id=uid.edu_subject
                  left join category as el on el.cat_id=uid.edu_level 
                  left join category as ee on ee.cat_id=uid.edu_expertise
                  left join user_detail on user_detail.user_id=users.user_id
                where users.user_id='$user_id' ");
	    return $query; 
    }	

   function show_onsite_info($user_id)
   {
	  $query = $this->db->query("select users.user_id,users.user_name  ,uid.tution_cat ,tc.cat_name as user_cat   from users 
				inner join user_info_detail as uid on users.user_id = uid.user_id
				left join category as tc on tc.cat_id=uid.tution_cat    where users.user_id='$user_id' ");
	  return $query; 
   }
	function show_password_info($user_id)
    {
		$query = $this->db->query("select * from users where user_id ='$user_id' ");
	    return $query; 
    }	
}