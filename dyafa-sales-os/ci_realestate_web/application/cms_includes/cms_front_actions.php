<?php


/*include_once("cms_front_homepage_actions.php");*/



add_filter("cms_get_details", "cms_get_details_callback");
function cms_get_details_callback($default = "" ,$detail_key =''  ){

	
	$detail = get_option($detail_key);
	return $detail;
}


add_filter("set_global_assets", "set_set_global_assets_callback");


$image_path  = base_url("application/views/cms_default/assets/images/no-property-image.png");
apply_filters("set_global_assets", "no_property_image", $image_path);
function set_set_global_assets_callback($image_name = "", $image_path = "")
{

	$CI = &get_instance();

	
	if (!empty($image_name) && !empty($image_path)) {
		if (!property_exists($CI, $image_name)) $CI->{$image_name} = '';

		return $CI->$image_name = $image_path;
	}
}



add_filter("app_menu_static_pages_append_menu_items", "cms_app_menu_static_pages_append_menu_item");

function cms_app_menu_static_pages_append_menu_item($app_menu_static_pages ='' ){
	
	$CI =  &get_instance();
	
	$app_menu_static_pages = $CI->config->item("app_menu_static_pages");
	
	return $app_menu_static_pages;
}



add_action('property_after_sidebar_widgets', 'cms_property_after_sidebar_widgets',  10, 0);
function cms_property_after_sidebar_widgets( )
{


	
	$CI = &get_instance();
	$args = func_get_args();
	/*echo " <pre> 123";
	print_r($CI->site_filters['property_after_sidebar']);	echo " </pre>";*/

	/*$hook_callbacks = $CI->site_filters['property_after_sidebar'];
	
	foreach ($hook_callbacks as $hook_callback) {
		if (isset($hook_callback['callback'])) {

			call_user_func_array($hook_callback['callback'], $args);
		}
	}*/

}

//
add_action('property_main_content_widgets', 'cms_property_main_content_widgets_callback',  10, 0);
function cms_property_main_content_widgets_callback($args = array())
{
	$CI = &get_instance();

	
	$site_widgets = $CI->config->item("site_widgets");	


	global $prop_row;
	$default_widgets = false;
	
	if(isset($prop_row->created_by)){ 
		$sidebar_widgets = get_user_meta($prop_row->created_by , "property_content_widgets");
		if(empty($sidebar_widgets))
		{	
			$sidebar_widgets = get_option( "property_content_widgets");
			if(empty($sidebar_widgets))
				$default_widgets = true;
		}
		
		
		
	
	}else{
		
		$sidebar_widgets = get_option( "property_content_widgets");
		if(empty($sidebar_widgets))
			$default_widgets = true;
	}
	
	$sidebar_widgets = json_decode($sidebar_widgets , true);
	$show_all_widgets = false;
	if($default_widgets )
	{
		$show_all_widgets = true;	
		
	}
	
	
	
	if(!empty($site_widgets)){
		 $property_content_widgets = $site_widgets['property_contents'];
		 
		 if(!empty($sidebar_widgets))
			$property_content_widgets = sortArrayByArray($property_content_widgets , $sidebar_widgets );
		 
		 $show_widget = false;
		 foreach($property_content_widgets as $wkey => $widget){
			
			
			if($default_widgets) $show_widget =  true;
			if(array_key_exists($wkey , $sidebar_widgets)  && $sidebar_widgets[$wkey]['status'] == 'Y')
				$show_widget =  true;
			else
				$show_widget =  false;
			 
			if (cms_file_exists( $CI->theme . "/".$widget['widget_path'])) 
			{ 
				if($show_widget || $show_all_widgets)
					$CI->load->view($CI->theme ."/".$widget['widget_path'],$args);
				
			} else 
			{
				if($show_widget || $show_all_widgets)
					$CI->load->view($widget['widget_path'],$args);
			}
			 
			 
		 }
	 }
	
	
	
	
}

add_action('property_sidebar_widgets', 'cms_property_sidebar_widgets_callback',  10, 0);
function cms_property_sidebar_widgets_callback()
{
	$CI = &get_instance();

	
	$site_widgets = $CI->config->item("site_widgets");	


	//echo " here we ";
	global $prop_row;
	
	$default_widgets = false;
	//print_r($prop_row);
	//echo "<pre>"; print_r($site_widgets); echo "</pre>";
	if(isset($prop_row->created_by)){ 
		$sidebar_widgets = get_user_meta($prop_row->created_by , "property_sidebar_widgets");
		if(empty($sidebar_widgets))
		{	//$default_widgets = true;
			$sidebar_widgets = get_option( "property_sidebar_widgets");
			if(empty($sidebar_widgets))
				$default_widgets = true;
		}
		
		
		
	
	}else{
		
		$sidebar_widgets = get_option( "property_sidebar_widgets");
		if(empty($sidebar_widgets))
			$default_widgets = true;
	}
	
	$sidebar_widgets = json_decode($sidebar_widgets , true);
	/*print_r($sidebar_widgets);*/
	$show_all_widgets = false;
	if($default_widgets )
	{
		$show_all_widgets = true;	
		//do_action('property_before_sidebar'); 
		 /*if(!empty($site_widgets)){
			 $property_sidebar_widgets = $site_widgets['property_sidebar'];
			 
			 foreach($property_sidebar_widgets as $wkey => $widget){
				
				 
				if (cms_file_exists( $CI->theme . "/" . str_replace(' ', '', CMS_FRONT_WIDGETS)  . "/".$widget['widget_path'])) 
				{
					$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/".$widget['widget_path']);
				} else if (cms_file_exists(  "cms_default/" . str_replace(' ', '', CMS_FRONT_WIDGETS)  . "/".$widget['widget_path'])) 
				{
					
					$CI->load->view("cms_default/" . CMS_FRONT_WIDGETS . "/".$widget['widget_path']);
				}
			 }
		 }*/
		 
		 
		 
	}else{
		
		 /*echo " show specific  widgets ";
		print_r($sidebar_widgets);	*/
		
		
		 
	}
	
	
	if(!empty($site_widgets)){
		 $property_sidebar_widgets = $site_widgets['property_sidebar'];
		 
		 if(!empty($sidebar_widgets))
			$property_sidebar_widgets = sortArrayByArray($property_sidebar_widgets , $sidebar_widgets );
		 
		 $show_widget = false;
		 foreach($property_sidebar_widgets as $wkey => $widget){
			
			
			if($default_widgets) $show_widget =  true;
			if(array_key_exists($wkey , $sidebar_widgets)  && $sidebar_widgets[$wkey]['status'] == 'Y')
				$show_widget =  true;
			else
				$show_widget =  false;
			 
			if (cms_file_exists( $CI->theme . "/" . str_replace(' ', '', CMS_FRONT_WIDGETS)  . "/".$widget['widget_path'])) 
			{ 
				if($show_widget || $show_all_widgets)
					$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/".$widget['widget_path']);
				
			} else if (cms_file_exists(  "cms_default/" . str_replace(' ', '', CMS_FRONT_WIDGETS)  . "/".$widget['widget_path'])) 
			{
				if($show_widget || $show_all_widgets)
					$CI->load->view("cms_default/" . CMS_FRONT_WIDGETS . "/".$widget['widget_path']);
			}else 
			/*if (cms_file_exists( "application/modules/".$widget['widget_path'])) 	*/
			{
				/*print_r($sidebar_widgets[$wkey]);
				print_r($widget);*/
				if($show_widget || $show_all_widgets)
					$CI->load->view($widget['widget_path']);
			}
			 
			 
		 }
	 }
	
	
	
	
}


/*******
add_action('property_after_sidebar', 'cms_property_agent_details',  10, 0);
function cms_property_agent_details()
{
	$CI = &get_instance();
	
	if (cms_file_exists( $CI->theme . "/" . str_replace(' ', '', CMS_FRONT_WIDGETS)  . "/property_agent_details_widget")) 
	{
		$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_agent_details_widget");
	} else {
		$CI->load->view("cms_default/" . CMS_FRONT_WIDGETS . "/property_agent_details_widget");
	}
}


add_action('property_after_sidebar', 'property_share_details',  10, 0);
function property_share_details()
{
	$CI = &get_instance();

	if (cms_file_exists($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_share_details")) {
		$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_share_details");
	} else {
		$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_share_details");
	}
}

add_action('property_after_sidebar', 'property_recent_viewed',  10, 0);
function property_recent_viewed()
{
	$CI = &get_instance();

	if (cms_file_exists($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_recent_viewed")) {
		$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_recent_viewed");
	} else {
		$CI->load->view("cms_default/" . "/" . CMS_FRONT_WIDGETS . "/property_recent_viewed");
	}
}

add_action('property_after_sidebar', 'property_agent_contact_form',  10, 0);
function property_agent_contact_form()
{
	$CI = &get_instance();

	if (cms_file_exists($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_agent_contact_form")) {
		$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_agent_contact_form");
	} else {
		$CI->load->view("cms_default/" . "/" . CMS_FRONT_WIDGETS . "/property_agent_contact_form");
	}
}

add_action('property_after_sidebar', 'property_mortgage_calculator',  10, 6);
function property_mortgage_calculator()
{
	$CI = &get_instance();

	if (cms_file_exists($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_mortgage_calculator")) {
		$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_mortgage_calculator");
	} else {
		$CI->load->view("cms_default/" . "/" . CMS_FRONT_WIDGETS . "/property_mortgage_calculator");
	}
}
*******/


/**add_action('related_properties', 'related_properties',  10, 0);*/
/*function related_properties()
{
	$CI = &get_instance();

	if (cms_file_exists($CI->theme . "/" . CMS_FRONT_WIDGETS . "/related_properties")) {
		$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/related_properties");
	} else {
		$CI->load->view("cms_default/related_properties");
	}
}*/



/*add_action('property_after_sidebar', 'property_agent_whatsapp_details',  10, 0);

function property_agent_whatsapp_details()
{
	$CI = &get_instance();
	if (cms_file_exists($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_agent_whatsapp_details")) {
		$CI->load->view($CI->theme . "/" . CMS_FRONT_WIDGETS . "/property_agent_whatsapp_details");
	} else {
		$CI->load->view("cms_default/" . "/" . CMS_FRONT_WIDGETS . "/property_agent_whatsapp_details");
	}
} */
