<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_mailer extends MY_Controller {
	
	function __construct() 
	{
        parent::__construct();
		
        /*if(!$this->isLogin())
		{
			redirect('/logins','location');
		}*/
	}
	
	public function test_email_notifications($args =  array())	
	{
		
		extract($_POST);		
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		
		if(isset($to_email))
			$args['to_email'] = $to_email;
		
		if(isset($email_template))
			$args['email_template'] = $email_template;	
			
		$CI->load->library('Email_lib');
		$CI->email_lib->test_email_notifications($args);
		
		
		
	}
	
	
	
	
	
	
	
}
