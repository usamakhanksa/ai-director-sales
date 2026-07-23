<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_account_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
   
    function show_trash($user_id)
    {
		$query = $this->db->query("select mb.*,us1.user_name as reciever, us2.user_name as sender  from mail_box as mb
				      inner join users as us1 on us1.user_id = mb.user_id
					  inner join users as us2 on us2.user_id = mb.sent_by where mb.user_id='$user_id' and mb.trash_status='Y'");
	    return $query; 
    }
	

}