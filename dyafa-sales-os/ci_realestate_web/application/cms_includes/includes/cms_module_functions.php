<?php 




function add_action($action_hook, $action_callback, $priority = 10, $accepted_args = 1)
{
	return add_filter($action_hook, $action_callback  , $priority , $accepted_args );
}


function do_action($hook_name,   ...$arg)
{

	$args = func_get_args();
	$CI = &get_instance();
	unset($args[0]);

	$return = "";
	
	if (property_exists($CI, "site_filters")   && isset($CI->site_filters[$hook_name])) {
		$hook_callbacks = $CI->site_filters[$hook_name];
		
		foreach ($hook_callbacks as $hook_callback) {
			if (isset($hook_callback['callback'])) {

				call_user_func_array($hook_callback['callback'], $args);
			}
		}
	}
}

function add_filter($filter, $filter_callback, $priority = 10, $accepted_args = 1)
{

	$CI = &get_instance();
	
	$CI->site_filters[$filter][] = array("callback" => $filter_callback ,
										 "priority" => $priority ,
										 "accepted_args" => $accepted_args ,
										);
}


function apply_filters($hook_name, ...$value)
{



	$CI = &get_instance();

	$args = func_get_args();

	
	array_shift($args);
	
	if (property_exists($CI, "site_filters")   && isset($CI->site_filters[$hook_name])) {
		$hook_callbacks = $CI->site_filters[$hook_name];
		/*	echo " hook name ".$hook_name;	*/
		foreach ($hook_callbacks as $hook_callback) {
			$filtered_val = call_user_func_array($hook_callback['callback'], $args);
			
			if ($filtered_val) {
				if (isset($args[0])) $args[0] = $filtered_val;
				$filtered = $filtered_val;
			}
		}
	}
	if (isset($filtered))
		return $filtered;
	else if (isset($args[0])  && !empty($args[0]))
		return $args[0];
	else 
		return false;
}

function register_activation_hook($module, $function){
	
	$CI = &get_instance();
	add_action( 'activate_' . $module, $function );
}


function register_deactivation_hook($module, $function){
	
	$CI = &get_instance();
	add_action( 'deactivate_' . $module, $function );
}





function add_site_users($args)
	{

		$CI = &get_instance();
		$site_users = $CI->config->item('site_users');
		$site_user_access = $CI->config->item('site_user_access');

		$default_site_user_access = array(

			"menu" => array(
				"has_access" => "limited",
				"menu_items" => array(
					"home",
					"settings", "settings||change_password", "settings||profile",
				)
			),
			"controller" => array(
				"has_access" => "limited",
				"all_items" => array("settings",)
			),
			"view" => array(
				"has_access" => "limited",
				"all_items" => array(
					"settings" => array("change_password", "profile"),
				)
			),
			"content" => array(
				"has_access" => "access_all",
				"all_items" => array(),
				"default_status" => "publish_all"
			),
			"widget" => array("has_access" => "access_all"),
		);

		$new_site_user_access_arr = array();
		foreach ($args as $user_type => $data) {
			if (in_array($user_type, $site_users)) {
				//echo 'already Exists';
				continue;
			} else {
				$site_users[$user_type] = $data;
				$new_site_user_access_arr[$user_type] = $default_site_user_access;
			}
		}

		$CI->site_users = $site_users;
		$CI->config->set_item('site_users', $site_users);

		$new_site_user_access = array_merge($site_user_access, $new_site_user_access_arr);
		$CI->site_user_access = $new_site_user_access;
		$CI->config->set_item('site_user_access', $new_site_user_access);
		// echo "<pre>";
		// print_r($new_site_user_access);
		// exit;
	}


function add_user_permission($args)
	{

		$CI = &get_instance();
		$site_user_access = $CI->config->item('site_user_access');
		foreach ($args as $user_type => $permissions) {
			if (isset($permissions['menu']['menu_items'])) {
				$menu_items = $permissions['menu']['menu_items'];

				foreach ($menu_items as $item) {
					$site_user_access[$user_type]['menu']['menu_items'][] = $item;
				}
			}

			if (isset($permissions['controller']['all_items'])) {
				$permission_items = $permissions['controller']['all_items'];

				foreach ($permission_items as $permission_item) {
					$site_user_access[$user_type]['controller']['all_items'][] = $permission_item;
				}
			}

			if (isset($permissions['view']['all_items'])) {
				$view_items = $permissions['view']['all_items'];
				// echo "<pre>";
				// print_r($site_user_access);

				$site_user_access[$user_type]['view']['all_items'] = array_merge($site_user_access[$user_type]['view']['all_items'], $view_items);
			}
		}
		$CI->site_user_access = $site_user_access;


		$CI->config->set_item('site_user_access', $site_user_access);

		
	}



function add_menu_items($menu_items= array() , $menu_position = 99){

	$CI = &get_instance();

	if(is_array($menu_items) && count($menu_items)){
		foreach($menu_items as $item_key => $items){
			/*echo $item_key;
			echo "<pre>";print_r($items);echo "</pre>";*/

			if(isset($items['menu_item'])){
				$menu_position = add_menu_item($items['menu_item'] , $menu_position);
			}

			if(isset($items['sub_item'])){
				$sub_items = $items['sub_item'];
				if(is_array($sub_items) && count($sub_items)){
					foreach($sub_items as $sub_item_key => $sub_item){	
						add_submenu_item($menu_position ,  $sub_item);
					}
				}		
			}	
		}
	}

}


function get_new_menu_position($menu_position ,$sidebar_items ){
	
	if(!array_key_exists($menu_position ,$sidebar_items ))
		return $menu_position;
	
	/*echo $menu_position . " exists "; print_r($menu_item);	*/
	return $menu_position = get_new_menu_position($menu_position+1 ,$sidebar_items );	
	
}

function insertElement(&$array, $key, $value) {
    if (array_key_exists($key, $array)) {
        $temp = array();
        foreach ($array as $k => $v) {
            if ($k == $key) {
                $temp[$key] = $value;
			}
			else
	            $temp[$k] = $v;
        }
        $array = $temp;
    } else {
        $array[$key] = $value;
    }
}

function add_menu_item($menu_item= array(), $menu_position = 99 , $sidebar = "sidebar_left" ){

	$CI = &get_instance();

	
	
	$sidebar_items = $CI->config->item($sidebar);
	/*if(array_key_exists($menu_position ,$sidebar_items )) $menu_position +=1;
	$sidebar_items[$menu_position] = $menu_item;
	*/
	/*if($menu_position == 47)print_r($menu_item);*/
	$menu_position = get_new_menu_position($menu_position ,$sidebar_items );
	
	//insertElement($sidebar_items, $menu_position , $menu_item);

	/*if(array_key_exists($menu_position ,$sidebar_items )){
		echo $menu_position . " exists "; print_r($menu_item);
		//array_splice($sidebar_items , $menu_position , 0 , $menu_item );
	}else*/
	
		$sidebar_items[$menu_position] = $menu_item;
	
	/*echo "<pre>";print_r($sidebar);echo "</pre>";*/
	$CI->config->set_item($sidebar, $sidebar_items);
	
	return $menu_position;
	
}

function add_submenu_item($parent_menu_position = 99 ,  $submenu_item = array() , $sidebar = "sidebar_left" ){

	$CI = &get_instance();
	/*echo "<pre>";print_r($submenu_item);echo "</pre>";*/

	$sidebar_items = $CI->config->item($sidebar);
	//echo "<pre>  123 ";print_r($sidebar_items);echo " 123 </pre>";
	$sidebar_items[$parent_menu_position]['item'][] = $submenu_item;
	/*echo "<pre>";print_r($sidebar_items);echo "</pre>";*/
	$CI->config->set_item($sidebar, $sidebar_items);
	//echo "<pre>";print_r($sidebar_items);echo "</pre>";/**/
}









function append_content_section_fields($config_key = 'content_sections', $content_section_item =  array())
{

	$CI = &get_instance();
	
	$content_sections = $CI->config->item($config_key);
	
	foreach ($content_section_item[$config_key] as $sidebar_key => $items) {
		
		$content_sections[$sidebar_key] = $items;
		$content_field_slug = $sidebar_key . '_fields';
		if (isset($content_sections[$content_field_slug])) 
		{
			$CI->config->set_item($content_field_slug , 
					array_merge($content_sections[$content_field_slug] , 	$content_section_item[$content_field_slug]));
			
		}else{			
			$CI->config->set_item($content_field_slug , $content_section_item[$content_field_slug]);
		}
	}

	
	$CI->config->set_item($config_key, $content_sections);
	return $content_sections;
}

function append_config($config_key = 'config_key', $config = array())
{

	$CI = &get_instance();
	$content_sections = $CI->config->item($config_key);

	foreach ($config[$config_key] as $key => $items) {
		$content_sections[$key] = $items;
	}	
	
	$CI->config->set_item($config_key, $content_sections);

	return $content_sections;
}

function get_media_gallery($media_images = "", $type = "original")
{

	$CI = &get_instance();
	$image_meta = array();


	if (!empty($media_images)) {
		$img_exp = explode(',', $media_images);
		$type = 'original';
		foreach ($img_exp as $k => $v) {
			$img_query = $CI->Common_model->commonQuery("select pi.* from post_images pi 
																where  pi.image_id = '$v'  or pi.parent_image_id = '$v' ");
			if ($img_query->num_rows() > 0) {
				foreach ($img_query->result() as $img_row) {
					if (file_exists($img_row->image_path . $img_row->image_name)) {
						if ($img_row->parent_image_id == 0)
							$image_meta[$img_row->image_id][$img_row->image_type] = $img_row->image_path . $img_row->image_name;
						else
							$image_meta[$img_row->parent_image_id][$img_row->image_type] = $img_row->image_path . $img_row->image_name;
					}
				}
			}
		}
	}

	return $image_meta;
}

	function get_image_type($img_path = null, $img_name = null, $return_type = 'thumb')
	{
		if ($img_path != null && $img_name != null) {
			$explod = explode(".", $img_name);
			$extension = end($explod);
			$name = str_replace('.' . $extension, '', $img_name);
			$thumb_img_name = $name . '-300X300.' . $extension;
			$medium_img_name = $name . '-500X300.' . $extension;

			$returned_file = '';

			if ($return_type == 'thumb' && file_exists($img_path . $thumb_img_name)) {
				$returned_file = $thumb_img_name;
			} else if ($return_type == 'medium' && file_exists($img_path . $medium_img_name) || ($return_type == 'thumb' && !file_exists($img_path . $thumb_img_name) && file_exists($img_path . $medium_img_name))) {
				$returned_file = $medium_img_name;
			} else if (file_exists($img_path . $img_name) || $return_type == 'full') {
				$returned_file = $img_name;
			}
			return $returned_file;
		}
		return '';
	}
