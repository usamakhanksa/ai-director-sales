<?php





add_action('property_after_sidebar_widgets', 'cms_admin_property_after_sidebar_widgets',  10, 0);
function cms_admin_property_after_sidebar_widgets( )
{


	
	$CI = &get_instance();
	$args = func_get_args();
	/*echo " <pre> 123";
	print_r($CI->site_filters['property_after_sidebar']);	echo " </pre>";*/

	$hook_callbacks = $CI->site_filters['property_after_sidebar'];
	
	
}


function dashboard_update_scripts($script_page = "")
{
        $CI =  &get_instance();

	
        if($script_page == 'dashboard_updates'){
			ob_start(); 
				$CI->load->view($CI->theme.'/dashboard-widgets/dashboard_update_scripts');
			echo  $meta_settings = ob_get_clean();
		}
		
}
add_action('admin_footer_scripts', 'dashboard_update_scripts', 10, 1);


function homepage_edit_scripts($script_page = "")
{
        $CI =  &get_instance();

	
        if($script_page == 'homepage_updates'){
			ob_start(); 
				$CI->load->view($CI->theme.'/homepage-sections/homepage_edit_scripts');
			echo  $meta_settings = ob_get_clean();
		}
		
}
add_action('admin_footer_scripts', 'homepage_edit_scripts', 10, 1);


function homepage_section_header_edit_callback($args =  array()){
	
	$CI =  &get_instance();
	
	ob_start(); 
		$CI->load->view($CI->theme.'/homepage-sections/homepage_section_header',$args);
	echo  $meta_settings = ob_get_clean();
			
	/*print_r($args);*/
	
	
}
add_action('homepage_section_header_edit', 'homepage_section_header_edit_callback', 10, 1);



function homepage_section_clone_section_callback($args =  array()){
	
	$CI =  &get_instance();
	
	ob_start(); 
		$CI->load->view($CI->theme.'/homepage-sections/homepage_section_clone_section',$args);
	echo  $meta_settings = ob_get_clean();
			
	/*print_r($args);*/
	
	
}
add_action('homepage_section_clone_section', 'homepage_section_clone_section_callback', 10, 1);


function homepage_section_clone_section_buttons_callback($args =  array()){
	
	$CI =  &get_instance();
	
	ob_start(); 
		$CI->load->view($CI->theme.'/homepage-sections/homepage_section_clone_section_buttons',$args);
	echo  $meta_settings = ob_get_clean();
			
	/*print_r($args);*/
	
	
}
add_action('homepage_section_clone_section_buttons', 'homepage_section_clone_section_buttons_callback', 10, 1);


include_once(APPPATH."cms_includes/admin/cms_admin_homepage_sections_filters.php");
include_once(APPPATH."cms_includes/admin/cms_admin_appearance_menu_filters.php");

include_once(APPPATH."cms_includes/admin/cms_admin_package_credit_actions.php");
/*include_once(APPPATH."cms_includes/admin/cms_admin_send_email_actions.php");		this is now important for all, front and admin	*/





include_once(APPPATH."cms_includes/admin/cms_admin_user_actions.php");

