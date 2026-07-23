<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Property_custom_fields_lib {

	
	public function Index(){
		
	}
	
	
	
	public function load_menu_items(){
		
		
		$CI =  & get_instance();
		$CI->load->config("property_custom_fields/property_custom_fields_config");
		
	}
	
	public function load_admin_header_scripts(){
		
		$CI =  & get_instance();

	}
	
	public function load_admin_footer_scripts(){
		
		$CI =  & get_instance();
		
	}
	
	public function load_property_custom_metaboxes(){
		
		$CI =  & get_instance();
		$data = array("CI" =>$CI);
		
		if($CI->global_lib->get_option('property_custom_fields'))
		{
			$data['custom_field_list'] = json_decode($CI->global_lib->get_option('property_custom_fields'),true);
		}	
		
		ob_start();
		$CI->load->view("property_custom_fields/admin/property_custom_field_metabox" , $data);
		$custom_metabox = ob_get_contents();
		ob_end_clean();

		$CI->property_custom_metaboxes [] =  $custom_metabox;
		
	}
	
	
}
