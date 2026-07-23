<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Widget_lib
{

	public function Index()
	{
	}

	public function save_widget_to_sidebar_callback_func()
	{
		$CI = &get_instance();
		
		echo "<pre>"; print_r($_POST);
	}


	public function admin_total_properties_callback()
	{
		$CI = &get_instance();
		$total_properties =  $CI->Common_model->commonQuery("select * from properties as prop where prop.deleted = 'N'");
		echo  $total_properties->num_rows();;
	}

	public function user_total_properties_callback()
	{
		$CI = &get_instance();
		$user_id = $CI->session->userdata('user_id');
		$total_properties =  $CI->Common_model->commonQuery("select * from properties prop where prop.created_by = $user_id and prop.deleted = 'N'");
		echo  $total_properties->num_rows();;
	}

	public function total_agents_callback()
	{
		$CI = &get_instance();
		$total_agents =  $CI->Common_model->commonQuery("select * from users where user_type = 'agent'");
		echo  $total_agents->num_rows();;
	}
	public function total_owners_callback()
	{
		$CI = &get_instance();
		$total_owners =  $CI->Common_model->commonQuery("select * from users where user_type = 'owner'");
		echo  $total_owners->num_rows();;
	}
	public function total_builders_callback()
	{
		$CI = &get_instance();
		$total_builders =  $CI->Common_model->commonQuery("select * from users where user_type = 'builder'");
		echo  $total_builders->num_rows();;
	}
	public function total_landlords_callback()
	{
		$CI = &get_instance();
		$total_landlords =  $CI->Common_model->commonQuery("select * from users where user_type = 'landlord'");
		echo  $total_landlords->num_rows();;
	}

	public function total_blogs_callback()
	{
		$CI = &get_instance();
		$total_blogs =  $CI->Common_model->commonQuery("select * from blogs");
		echo  $total_blogs->num_rows();
	}
}

/* End of file Myhelpers.php */