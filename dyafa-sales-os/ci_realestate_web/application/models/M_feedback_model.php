<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class M_feedback_model extends CI_Model {

   
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
  
	
	function send_feedback()
    {
        
		$data = array( 
		'user_id' =>   $_POST['user_id'] ,	 
		'feedback_title' => $_POST['fdb_title']	 ,
		'feedback_desc' => $_POST['fdb_desc']	 ,
		'sender' => $_POST['sender']	 ,
		'sent_date' =>  time(),
		 );
  		$this->db->insert('feedbacks', $data); 	
		
    }

}