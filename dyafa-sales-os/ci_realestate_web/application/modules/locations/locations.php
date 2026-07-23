<?php 
/*
Plugin Name: Locations
Plugin URI: http://www.mindlogixtech.com
Version: 0.1
Description: Locations based on differend region.
Author: Mindlogixtech
Author URI: http://www.facebook.com/mindlogixtech
*/


define("LOCATIONS_DIR", "locations");
define("LOCATIONS_ASSETS_PATH", "application/modules/".LOCATIONS_DIR."/assets/");
define("LOCATIONS_PLUGIN_NAME", "Locations ");


add_action('cms_init', 'locations_init');

function locations_init()
{

    $CI = &get_instance();
    /*check_dependency("customer_review', 'foodie_web');*/


    $CI->load->config(LOCATIONS_DIR . "/locations_config");

	$CI->admin_ajax_items["loc_get_state_city_name_list"] = array(
        "callback_id" => "loc_get_state_city_name_list",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_state_city_name_list_callback_func"   ); 
	
	$CI->admin_ajax_items["loc_get_city_name_list"] = array(
        "callback_id" => "loc_get_city_name_list",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_city_name_list_callback_func"   ); 
	
	
	$CI->admin_ajax_items["loc_get_zip_sub_area_name_list"] = array(
        "callback_id" => "loc_get_zip_sub_area_name_list",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_zip_sub_area_name_list_callback_func"   ); 
	
	$CI->admin_ajax_items["get_current_language_list"] = array(
        "callback_id" => "get_current_language_list",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_current_language_list_callback_func"   );
        
        
        $CI->admin_ajax_items["update_location_language"] = array(
        "callback_id" => "update_location_language",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/update_location_language_callback_func"   );
        
        
        $CI->admin_ajax_items["get_location_language_list"] = array(
        "callback_id" => "get_location_language_list",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_location_language_list_callback_func"   );
        
        
        $CI->admin_ajax_items["update_location_lang"] = array(
        "callback_id" => "update_location_lang",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/update_location_lang_callback_func" );
        
	$CI->admin_ajax_items["reset_location_meta"] = array(
        "callback_id" => "reset_location_meta",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/reset_location_meta_callback_func"   ); 
	

		$CI->admin_ajax_items["remove_element_for_locations"] = array(
        "callback_id" => "remove_element_for_locations",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/remove_element_for_locations"   ); 
	
		
		
	
	
	$CI->admin_ajax_items["get_states_or_cities_list_homepage_sections"] = array(
        "callback_id" => "get_state_or_city_list_homepage_sections",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_state_or_city_list_homepage_sections_callback"   ); 
	
	
	$CI->admin_ajax_items["get_states_from_country_homepage_sections"] = array(
        "callback_id" => "get_state_or_city_list_homepage_sections",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_state_list_from_country_homepage_sections_callback"   ); 
	
	$CI->admin_ajax_items["get_cities_from_state_homepage_sections"] = array(
        "callback_id" => "get_state_or_city_list_homepage_sections",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_city_list_from_state_homepage_sections_callback"   ); 
	
	$CI->admin_ajax_items["get_zip_subareas_from_city_homepage_sections"] = array(
        "callback_id" => "get_state_or_city_list_homepage_sections",
        "callback_path" => LOCATIONS_DIR . "/ajax_locations_lib/get_zip_subarea_list_from_city_homepage_sections_callback"   ); 
	
	
	
}




function property_locations_admin_fields($args = array())
{
        $CI = &get_instance();
	
        ob_start();
        $CI->load->view(LOCATIONS_DIR."/admin/includes/property_location_admin_fields",$args); 
	
       echo $meta_settings = ob_get_clean();
}
add_action('admin_property_location_fields', 'property_locations_admin_fields', 10, 0);



function locations_property_edit_scripts($script_page = "")
{
        $CI =  &get_instance();

	
        if($script_page == 'location_updates'){
			ob_start(); 
				$CI->load->view(LOCATIONS_DIR.'/admin/includes/property_edit_scripts');
			echo  $meta_settings = ob_get_clean();
		}
		
}
add_action('admin_footer_scripts', 'locations_property_edit_scripts', 10, 0);


function locations_homepage_sections_edit_scripts($script_page = "")
{
        $CI =  &get_instance();

	
        if($script_page == 'homepage_updates'){
			ob_start(); 
				$CI->load->view(LOCATIONS_DIR.'/admin/includes/homepage_sections_edit_scripts');
			echo  $meta_settings = ob_get_clean();
		}
		
}
add_action('admin_footer_scripts', 'locations_homepage_sections_edit_scripts', 10, 0);



function save_property_location_meta_callback($post_args)
{
    $CI =  &get_instance();

    extract($post_args);
	
	if(!isset($country)) $country = '';
	if(!isset($state)) $state = '';
	if(!isset($city)) $city = '';
	if(!isset($zipcode)) $zipcode = '';
	if(!isset($sub_area)) $sub_area = '';
	
    
    $datai = array(
        'country' => $country,
        'state' => $state,
        'city' => $city,
        'zip_code' => $zipcode,
        'sub_area' => $sub_area,
    );
    $CI->Common_model->commonUpdate('properties', $datai, 'p_id', $p_id);

}
add_action('admin_save_property_location_meta', 'save_property_location_meta_callback', 10, 0);


include_once("search_locations.php");
include_once("homepage_sections_location_filters.php");