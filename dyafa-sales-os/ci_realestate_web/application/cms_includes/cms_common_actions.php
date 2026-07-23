<?php

/*
function cms_user_register_callback($args =  array())
{

	$CI =  &get_instance();
	extract($args);

}

add_action('user_register', 'cms_user_register_callback', 10, 0);*/



/*add_filter("register_user", "cms_register_user_callback");

$user_id   = wp_create_user( $sanitized_user_login, $user_pass, $user_email );*/


function cms_widget_register_callback($args =  array())
{

	$CI =  &get_instance();
	extract($args);
	/*print_r($args);*/

}

add_action('widget_register', 'cms_widget_register_callback', 10, 0);
/*
do_action("widget_register", array("sidebar" => "property_after_sidebar" , "callback","cms_property_agent_details"));
*/

function cms_create_user($user_data = array()){

	$CI =  &get_instance();
	extract($user_data);

	if(!isset($user_email)) 		return false;		/***	must include ***/
	if(!isset($user_type)) 			return false;		/***	must include ***/
	if(!isset($user_verified)) 		$user_verified = 'N';		
	if(!isset($user_status)) 		$user_status = 'N';		
	if(!isset($user_pass)) {
		$user_pass = md5(trim($user_email));
	}
	if(!isset($user_link_id)) $user_link_id = ''; 
	if(!isset($user_code)) $user_code = '';
	
	
	/********************/
	
	$sql = "select *	   from users  as u 
		    where u.user_email = '$user_email' or u.user_name = '$user_name'";
	$user_result = $CI->Common_model->commonQuery($sql );
		
	if($user_result->num_rows() > 0)
	{
		$output = '<div class="alert alert-success alert-dismissable" >
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			This Email/Username Already Registered.Please add some differnt Email/Username for registration.
		</div>';	
		header('Content-type: application/json');
		echo json_encode(array('status' => 'failed', 'output' => $output, 'auto_redirect' => 'N'));
		exit;
	}
	
	
	
	/********************/
	
	
	$cur_time = time();

	$datai = array(
		'user_name' => 	$user_name,
		'user_pass' => 	$user_pass,
		'user_email' => trim($user_email),
		'user_type' => 	$user_type,
		'user_registered_date' => $cur_time,
		'user_update_date' => $cur_time,
		'user_verified' => $user_verified,
		'user_status' => $user_status,	
		'user_link_id' => $user_link_id,
		'user_code' => $user_code,
		);
		
		
		
	$user_id = $CI->Common_model->commonInsert('users', $datai);


	if(isset($user_meta) && is_array($user_meta) && count($user_meta) > 0 ){
		foreach ($user_meta as $key => $val) {
			
			if (is_array($val))
				$val = json_encode($val);
			
			$datai = array(
				'meta_key' => trim($key),
				'meta_value' => trim($val),
				'user_id' => $user_id
			);
			$CI->Common_model->commonInsert('user_meta', $datai);
		}
	}

	return $user_id;


}

function generate_random_user_name($user_name = ""){
	
	
	if(empty($user_name)) return false;
	
	$user_name = str_replace(' ','_',$user_name);
	
	$new_user_name = $user_name.generate_random_string(5);
	
	$CI =  &get_instance();
	$CI->load->model('Common_model');
		
	$sql = "select * from users where user_name = '$new_user_name' ";
	$result = $CI->Common_model->commonQuery($sql);
	if ($result->num_rows() == 0) {
		return $new_user_name;
	}else{
		generate_random_user_name($user_name);
	}
	
}


function cms_update_user($user_data = array()){

	$CI =  &get_instance();
	extract($user_data);
	if(!isset($user_id)) 		return false;		/***	must include ***/
	
	$datai = array(
		'user_email' => trim($user_email),
		'user_update_date' => $user_update_date,
		);
	$CI->Common_model->commonUpdate('users', $datai, 'user_id', $user_id);


	if(isset($user_meta) && is_array($user_meta) && count($user_meta) > 0 ){
		foreach ($user_meta as $key => $val) {
			
			if (is_array($val))
				$val = json_encode($val);
			
			update_user_meta( $user_id,$key,$val);
		}
	}

}



add_filter("get_user_account_id", "get_user_account_id_callback");

function get_user_account_id_callback()
{
	
	$CI = &get_instance();
	
	
	$user_id = $CI->user_id;
	$user_account_id = get_user_meta($user_id,"user_account_id");
	if($user_account_id) 
		return $user_account_id;
	
	return $user_id;
	
}

add_filter("partially_email", "cms_partially_email_callback");

function cms_partially_email_callback($email = '')
{
	$CI = &get_instance();

	/*

	// Hide text before @
	$minFill = 4;
	return preg_replace_callback(
		'/^(.)(.*?)([^@]?)(?=@[^@]+$)/u',
		function ($m) use ($minFill) {
			return $m[1]
					. str_repeat("*", max($minFill, mb_strlen($m[2], 'UTF-8')))
					. ($m[3] ?: $m[1]);
		},
		$email
	);
	*/

	
	if(filter_var($email, FILTER_VALIDATE_EMAIL))
	{

        list($first, $last) = explode('@', $email);

        $first = str_replace(substr($first, '2'), str_repeat('*', strlen($first)-2), $first);

        $last = explode('.', $last);

        $last_domain = str_replace(substr($last['0'], '1'), str_repeat('*', strlen($last['0'])-1), $last['0']);

        $hideEmailAddress = $first.'@'.$last_domain.'.'.$last['1'];

        return $hideEmailAddress;

    }
	
}

