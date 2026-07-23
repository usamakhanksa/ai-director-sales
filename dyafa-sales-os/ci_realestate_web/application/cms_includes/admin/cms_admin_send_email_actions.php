<?php


function cms_send_email_to_admin_callback($args =  array()){
	
	$CI =  &get_instance();
	extract($args);
	/*$user_type = $CI->user_type;
	$user_id = $CI->user_id;*/
	print_r($args);
	if (empty($email_template)) return false;
	
	$admin_emails = $CI->admin_emails;
	if (!empty($admin_emails)) {

		foreach ($admin_emails as $ak => $av) {
			$args['to_email'] = $av;
			$args['email_template'] = $email_template;
			if (count($args) > 0) $args = array_merge($args, $args);

			send_email_notification($args);
		}
	}
	
}
add_action('send_email_to_admin', 'cms_send_email_to_admin_callback', 10, 0);

function cms_send_email_to_user_callback($args =  array()){
	
	$CI =  &get_instance();
	extract($args);
	$user_type = $CI->user_type;
	$user_id = $CI->user_id;
	
	if(empty($email_template)) return false;
	if(count($user_ids) == 0 ) return false;
	
	$user_emails = $CI->global_lib->get_user_emails_by_user_ids(implode(",",$user_ids));
	if(!empty($user_emails))
	{
		
		foreach($user_emails as $ak => $av)
		{
			if(empty($av)) continue;
			
			$args['to_email'] = $av;
			$args['email_template'] = $email_template;	
			if(count($args) > 0) $args = array_merge($args,$args);
			
			send_email_notification($args);
		}	
	}
	
}
add_action('send_email_to_user', 'cms_send_email_to_user_callback', 10, 0);

function cms_send_email_to_user_email_callback($args =  array()){
	
	$CI =  &get_instance();
	extract($args);
	echo " here ";
	print_r($args);
	if(empty($email_template)) return false;
	if(empty($user_email)) return false;
	
	$args['to_email'] = $user_email;
	$args['email_template'] = $email_template;	
	if(count($args) > 0) $args = array_merge($args,$args);
	
	send_email_notification($args);
	
	
}
add_action('send_email_to_user_email', 'cms_send_email_to_user_email_callback', 10, 0);




function send_email_notification($args = array())
{

	$CI = &get_instance();
	$CI->load->library('Email_lib');
	$CI->email_lib->send_email_notification($args);
}
