<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_ajax extends MY_Controller {
	
	
	public function index() 
	{
		extract($_REQUEST);
	  
		$CI =& get_instance();
		
		$admin_ajax_items =  $CI->admin_ajax_items;
		
		/*echo "<pre>";print_r($admin_ajax_items);*/
		$return = json_encode(array("status" => 'Ajax error '));
		/*print_r($_POST);*/
		if(isset($callback) && !empty($callback)){
			
			if(array_key_exists($callback,$admin_ajax_items))
			{
				$current = $admin_ajax_items[$callback];
				
				
				$modules_path = APPPATH.'modules/';
				
				
				$callback_path_arr = explode("/",$current['callback_path']);
				
				
				if(count($callback_path_arr) == 3){
					$plugin_slug = $callback_path_arr[0];
					$plugin_lib = $callback_path_arr[1];
					$plugin_lib_callback = $callback_path_arr[2];
					
				
				/*print_r($callback_path_arr); exit;*/
				
				
					if(file_exists($modules_path.$plugin_slug.'/libraries/'.ucfirst($plugin_lib).'.php'))
					{
						
						
						/*$this->load->library($plugin_slug.'/'.ucfirst($plugin_lib),null,$plugin_lib.'_obj');*/
						
						$plugin_obj = $plugin_lib.'_obj';
						
						/*if(method_exists($CI->$plugin_obj, $plugin_lib_callback))
								$return = call_user_func_array(array($CI->$plugin_obj, $plugin_lib_callback), $_POST);	*/
								
						if(property_exists($CI, $plugin_lib))
						{
							//$this->load->library(ucfirst($plugin_lib),null,$plugin_lib.'_obj');
							 //echo "<pre>"; print_r($CI); //exit;
							if(method_exists($CI->$plugin_lib, $plugin_lib_callback))
								$return = call_user_func_array(array($CI->$plugin_lib, $plugin_lib_callback),  array_values( $_POST));	
						} 
						else 
						{
							
							$this->load->library($plugin_slug.'/'.ucfirst($plugin_lib),null,$plugin_lib.'_obj');
							
							/*if(method_exists($CI->$plugin_obj, $plugin_lib_callback))*/
							if(method_exists($CI->$plugin_obj, $plugin_lib_callback))
								$return = call_user_func_array(array($CI->$plugin_obj, $plugin_lib_callback),  array_values( $_POST));	
						}	

						
							
					}
				}else if(count($callback_path_arr) == 2){
					
					/*$plugin_slug = $callback_path_arr[0];*/
					 $plugin_lib = $callback_path_arr[0];
					 $plugin_lib_callback = $callback_path_arr[1];
					
				
				/*print_r($callback_path_arr); exit;
				echo "<pre>"; print_r($CI); exit;*/
					
					if(file_exists(APPPATH.'/libraries/'.ucfirst($plugin_lib).'.php'))
					{
						$plugin_obj = $plugin_lib;
						/*if(!isset($CI->$plugin_obj))
							$CI->load->library(ucfirst($plugin_lib),null,$plugin_lib.'_obj');
						else{ echo "set"; exit;}
						*/
						
						if(property_exists($CI, $plugin_lib))
						{
							
							//$this->load->library(ucfirst($plugin_lib),null,$plugin_lib.'_obj');
							 //echo "<pre>"; print_r($CI); //exit;
							if(method_exists($CI->$plugin_lib, $plugin_lib_callback))
								$return = call_user_func_array(array($CI->$plugin_lib, $plugin_lib_callback), array_values($_REQUEST));	
						} 
						else 
						{
							
							$CI->load->library(ucfirst($plugin_lib));
							
							if(method_exists($plugin_lib, $plugin_lib_callback))
							{
								$return = call_user_func_array(array($CI->$plugin_lib, $plugin_lib_callback), 
									array_values($_REQUEST));	
							}
							
							
						}
						/*if(property_exists($CI, $plugin_obj))
							echo "yes";
						else echo "no";	*/
						
						/*if(method_exists($CI->$plugin_obj, $plugin_lib_callback))
								$return = call_user_func_array(array($CI->$plugin_obj, $plugin_lib_callback), $_POST);	*/
							
							
					}
				}	
					
			}	
		}
		
		return $return;
		
	}
	
		
	
	
	
}
