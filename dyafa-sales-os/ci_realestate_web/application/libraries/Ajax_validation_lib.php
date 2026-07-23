<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_validation_lib
{


	public function check_username_existence_callback()
	{
		extract($_POST);
		$CI = &get_instance();
		
		$CI->load->model('Common_model');
		
		$sql = "select * from users where user_name = '$user_name' ";
		$result = $CI->Common_model->commonQuery($sql);
		if ($result->num_rows() > 0) {
			echo 'error';
		} else {
			echo 'success';
		}
	}


	public function check_user_email_existence_callback()
	{
		extract($_POST);
		$CI = &get_instance();
		
		$CI->load->model('Common_model');
		
		$sql = "select * from users where user_email = '$user_email' ";
		$result = $CI->Common_model->commonQuery($sql);
		if ($result->num_rows() > 0) {
			echo 'error';
		} else {
			echo 'success';
		}
	}

	
}
