<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ent_modules extends MY_Controller
{


	public function installed_modules()
	{

		$CI = &get_instance();

		$this->load->library('Plugins_lib');
		$plugin_json_list = $CI->plugins_lib->get_plugin_header_from_json();

		/*echo "<pre>";print_r($plugin_json_list);echo "</pre>";*/

		if (!empty($plugin_json_list)) {
			foreach ($plugin_json_list as $akk => $avv) {
				$plugin_slug = $akk;

				$modules_path = APPPATH . 'modules/';

				$plugin_lib = $plugin_slug . "_lib";

				

				/****	isset($avv['status']) && $avv['status'] == 'Y' && 	***/
				if (
					isset($avv['status']) && $avv['status'] == 'Y' &&
					file_exists($modules_path . $plugin_slug . '/libraries/' . ucfirst($plugin_lib) . '.php')
				) {

					/*echo "<pre>";print_r($avv);echo "</pre>";*/
					/*$this->load->library($plugin_slug . '/' . ucfirst($plugin_slug) . '_lib', null, $plugin_lib . '_obj');*/

					
					/*$cp_header = $this->plugins_lib->get_plugin_header($plugin_slug);
					
					if(array_key_exists("dependency",$cp_header)){
						$depends_plugins[$plugin_slug]  =  $plugin_slug;
						continue;
					}*/
					echo $plugin_slug . "   - ";

					if (file_exists($modules_path . $plugin_slug . "/$plugin_slug.php"))
						include($modules_path . $plugin_slug . "/$plugin_slug.php");


					$plugin_obj = $plugin_lib; //.'_obj';
					$CI->load->library($plugin_slug . '/' . ucfirst($plugin_slug) . '_lib', null, $plugin_obj);





					/*$plugin_obj = $plugin_lib . '_obj';


					if (method_exists($CI->$plugin_obj, 'load_menu_items'))
						$CI->$plugin_obj->load_menu_items();

					if (method_exists($CI->$plugin_obj, 'load_ajax_items'))
						$CI->$plugin_obj->load_ajax_items();


					if (method_exists($CI->$plugin_obj, 'load_header_scripts'))
						$CI->$plugin_obj->load_header_scripts();


					if (method_exists($CI->$plugin_obj, 'load_footer_scripts'))
						$CI->$plugin_obj->load_footer_scripts();

					$logged_in = $this->session->userdata('logged_in');
					if ($logged_in) {

						if (method_exists($CI->$plugin_obj, 'load_admin_header_scripts'))
							$CI->$plugin_obj->load_admin_header_scripts();

						if (method_exists($CI->$plugin_obj, 'load_admin_footer_scripts'))
							$CI->$plugin_obj->load_admin_footer_scripts();

						if (method_exists($CI->$plugin_obj, 'load_admin_header_top_nav_links'))
							$CI->$plugin_obj->load_admin_header_top_nav_links();

						if (method_exists($CI->$plugin_obj, 'load_property_custom_metaboxes'))
							$CI->$plugin_obj->load_property_custom_metaboxes();
					}

					if (method_exists($CI->$plugin_obj, 'load_checkout_footer_scripts'))
						$CI->$plugin_obj->load_checkout_footer_scripts();
				
				*/
				
				}
			}
		}
		
		print_r($CI->site_filters );
		
	}
}
