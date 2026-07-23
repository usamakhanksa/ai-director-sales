<?php




function cms_admin_user_profile_social_media_details_callback($args = array())
{
        $CI = &get_instance();
	
        ob_start(); 
        $CI->load->view("cms_default/users/cms_admin_social_media_details",$args); 
	   
	   
	
       echo $meta_settings = ob_get_clean();
}
add_action('cms_admin_user_profile_box', 'cms_admin_user_profile_social_media_details_callback', 10, 0);







function cms_admin_details_form_callback($args = array())
{
        $CI = &get_instance();
	
        ob_start(); 
        $CI->load->view("cms_default/users/cms_admin_details_form",$args); 
	
       echo $meta_settings = ob_get_clean();
}
add_action('cms_admin_details_form', 'cms_admin_details_form_callback', 10, 0);


function cms_admin_details_form_submit_callback($args = array())
{
    $CI = &get_instance();
	
	extract($_POST, EXTR_OVERWRITE);
	
	
	if(!isset($status)) $status = 'Y';
	
	$cur_time = time();
	$user_args = array(
		'user_name' => trim($UserName),
		'user_pass' => md5(trim($Password)),
		'user_email' => trim($UserEmail),
		'user_type' => trim($UserType),
		'user_registered_date' => $cur_time,
		'user_update_date' => $cur_time,
		'user_link_id' => '',
		'user_code' => '',
		'user_verified' => 'Y',
		'user_status' => $status,
	);
	
	$user_args ['user_meta'] = $user_meta;
	$new_user_id   = cms_create_user(  $user_args );
	
	
	$args = array('user_id' => $new_user_id);
	do_action("cms_admin_after_user_created",$args);
	
	//$args = array('user_id' => $new_user_id);
	//apply_filters("cms_admin_after_user_created",$args);
	
        /*ob_start(); 
        $CI->load->view("cms_default/users/cms_admin_details_form",$args); 
	
       echo $meta_settings = ob_get_clean();*/
}
add_action('cms_admin_details_form_submit', 'cms_admin_details_form_submit_callback', 10, 0);






function cms_admin_details_form_validation_callback($args = array())
{
        $CI = &get_instance();
	
        //ob_start(); 
        //$CI->load->view("cms_default/users/cms_admin_details_form",$args); 
	
		$CI->form_validation->set_rules('user_meta[first_name]', 'First Name', 'trim|required');
		$CI->form_validation->set_rules('user_meta[last_name]', 'Last Name', 'trim|required');
			
		$CI->form_validation->set_rules('UserName', 'User Name', 'trim|required');
		$CI->form_validation->set_rules('Password', 'Password', 'trim|required');
		$CI->form_validation->set_rules('RepeatPassword', 'Repeat Password', 'trim|required|matches[Password]');
	
	
       echo $meta_settings = ob_get_clean();
}
add_action('cms_admin_details_form_validation', 'cms_admin_details_form_validation_callback', 10, 0);

