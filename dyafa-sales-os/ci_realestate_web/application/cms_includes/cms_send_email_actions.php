<?php


function cms_send_email_to_admin_callback($args =  array())
{

	$CI =  &get_instance();
	extract($args);


	if (!isset($email_template)) return false;

	$admin_emails = $CI->admin_emails;
	if (!empty($admin_emails)) {

		foreach ($admin_emails as $ak => $av) {
			$args['to_email'] = $av;
			$args['email_template'] = $email_template;
			if (count($args) > 0) $args = array_merge($args, $args);
			send_email_notification($args);
		}
		
		/*extract($args['post']);
		$datai = array(
			'user_type' => 'admin',
			'first_name' => $contact_name,
			'email' => $contact_email,
			'subject' => $contact_subject,
			'message' => $contact_message,
			'created_at' => time(),
		);
		$CI->Common_model->commonInsert('form_enquiries', $datai);
		*/
	}
}

add_action('send_email_to_admin', 'cms_send_email_to_admin_callback', 10, 0);


function cms_send_email_to_user_callback($args =  array())
{

	$CI =  &get_instance();
	extract($args);
	$user_type = $CI->user_type;
	$user_id = $CI->user_id;

	if (!isset($email_template)) return false;
	if (count($user_ids) == 0) return false;

	$user_emails = $CI->global_lib->get_user_emails_by_user_ids(implode(",", $user_ids));
	if (!empty($user_emails)) {

		foreach ($user_emails as $ak => $av) {
			if (empty($av)) continue;

			$args['to_email'] = $av;
			$args['email_template'] = $email_template;
			if (count($args) > 0) $args = array_merge($args, $args);

			send_email_notification($args);
		}
	}
}
add_action('send_email_to_user', 'cms_send_email_to_user_callback', 10, 0);

function cms_send_email_to_user_email_callback($args =  array())
{

	$CI =  &get_instance();
	extract($args);

	if (!isset($email_template)) return false;
	if (empty($user_email)) return false;

	$args['to_email'] = $user_email;
	$args['email_template'] = $email_template;
	if (count($args) > 0) $args = array_merge($args, $args);

	send_email_notification($args);
}
add_action('send_email_to_user_email', 'cms_send_email_to_user_email_callback', 10, 0);


function cms_send_email_contact_agent_callback($args =  array())
{

	$CI =  &get_instance();
	extract($args);

	if (empty($to_email)) return false;
	send_email_notification($args);

	/*extract($args['post']);
	$datai = array(
		'user_id' => $user_id,
		'property_id' => DecryptClientID($p_id),
		'first_name' => $name,
		'email' => $email,
		'subject' => 'Property Contact From Submitted',
		'message' => $message,
		'created_at' => time(),
	);
	$CI->Common_model->commonInsert('form_enquiries', $datai);
	*/
	
}
add_action('send_email_contact_agent', 'cms_send_email_contact_agent_callback', 10, 0);


function send_email_notification($args = array())
{

	$CI = &get_instance();
	$CI->load->library('Email_lib');

	if (isset($args['sending']) && $args['sending'] == 'general_message')
		$CI->email_lib->send_email_message($args);
	else
		$CI->email_lib->send_email_notification($args);
}
