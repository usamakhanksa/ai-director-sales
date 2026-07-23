<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Locations_lib {

	
	public function Index(){}
	
	
	
	public function load_menu_items(){
		
		
		$CI = $thiss = & get_instance();
		$CI->load->config("locations/locations_config");
		
	}
	
	public function load_property_edit_scripts()
	{
		$CI = $thiss = & get_instance();
		$output = '';
		ob_start(); 
		
		$CI->load->view(LOCATIONS_DIR.'/admin/includes/property_edit_scripts');

		 $output = ob_get_contents();
		ob_end_clean();
		$CI->admin_property_edit_scripts[] = $output;
	}
	
}
