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
		

		$depends_plugins = array();	
		if (!empty($plugin_json_list)) {
			foreach ($plugin_json_list as $akk => $avv) {
				$plugin_slug = $akk;

				$modules_path = APPPATH . 'modules/';

				$plugin_lib = $plugin_slug . "_lib";

				

				/****	isset($avv['status']) && $avv['status'] == 'Y' && 	***/
				/*if (
					isset($avv['status']) && $avv['status'] == 'Y' &&
					file_exists($modules_path . $plugin_slug . '/libraries/' . ucfirst($plugin_lib) . '.php')
				) {*/


					
					/*$cp_header = $this->plugins_lib->get_plugin_header($plugin_slug);
					
					if(array_key_exists("dependency",$cp_header)){
						$depends_plugins[$plugin_slug]  =  $plugin_slug;
						continue;
					}*/
					
				if (isset($avv['status']) && $avv['status'] == 'Y') 
				{
					
					if(file_exists($modules_path . $plugin_slug . "/$plugin_slug.php")){  
					/*echo " - ".$plugin_slug;*/
						include($modules_path . $plugin_slug . "/$plugin_slug.php");
					}
					
				}
				/*}*/
			}
		}
		
		
		if(count($depends_plugins) > 0){
			
			foreach ($depends_plugins as $plugin_slug) {
				if (file_exists($modules_path . $plugin_slug . "/$plugin_slug.php")){
					
					include($modules_path . $plugin_slug . "/$plugin_slug.php");
				}	
			}	
			
		}

		/*echo "<pre>";print_r($CI->site_filters );*/

		if (isset($CI->site_filters) && isset($CI->site_filters['cms_init'])) {
			foreach ($CI->site_filters['cms_init'] as $cms_init_callbacks) {

				if (function_exists($cms_init_callbacks['callback'])) {
					$args = func_get_args();
					call_user_func($cms_init_callbacks['callback'], $args);
				}
			}
		}
		
		
		
		
	}
}
