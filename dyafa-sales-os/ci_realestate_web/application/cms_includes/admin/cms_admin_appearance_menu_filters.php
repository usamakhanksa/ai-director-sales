<?php


function cms_app_menu_static_pages_callback($args =  array()){
	
	$CI =  &get_instance();
	
	ob_start(); 
		$CI->load->view($CI->theme.'/appearance/includes/cms_app_menu_static_pages',$args);
	echo  $meta_settings = ob_get_clean();
			
	/*print_r($args);*/
	
	
}
add_action('cms_app_menu_static_pages', 'cms_app_menu_static_pages_callback', 10, 0);



add_filter("app_menu_static_pages_append_menu_items", "cms_app_menu_static_pages_append_menu_item");

function cms_app_menu_static_pages_append_menu_item($app_menu_static_pages ='' ){
	
	$CI =  &get_instance();
	
	$app_menu_static_pages = $CI->config->item("app_menu_static_pages");
	
	return $app_menu_static_pages;
}
/**/
