<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Global_lib
{

	var $site_options = "";
	
	
	public function add_user_permission($args)
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
				

				$site_user_access[$user_type]['view']['all_items'] = array_merge($site_user_access[$user_type]['view']['all_items'], $view_items);
			}
		}
		$CI->site_user_access = $site_user_access;

		/*if(isset($_GET['az'])){
			echo "<pre> 1212 ";	print_r($CI->site_user_access);	echo "</pre>";
		}*/

		$CI->config->set_item('site_user_access', $site_user_access);

		
	}

	public function add_site_users($args)
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
	public function append_menu_items($sidebar = 'sidebar_left', $new_sidebar_config ="")
	{

		$CI = &get_instance();
		$sidebar = $CI->config->item($sidebar);


		/*$new_sidebar_config = current($new_sidebar_config);*/
		if (array_key_exists("sidebar_left_items", $new_sidebar_config))
			$new_sidebar_config = $new_sidebar_config["sidebar_left_items"];
		else
			return $sidebar;


		foreach ($new_sidebar_config as $sidebar_key => $items) {

			if (array_key_exists($sidebar_key, $sidebar)) {
				foreach ($items['item'] as $item) {
					$sidebar[$sidebar_key]['item'][] = $item;
				}
			} else {
				$sidebar[$sidebar_key] = $items;
			}
		}
		$CI->config->set_item("sidebar_left", $sidebar);


		return $sidebar;
	}

	
	public function append_config($config_key = 'homepage_contents', $content_config = array())
	{

		$CI = &get_instance();
		$content_sections = $CI->config->item($config_key);
		echo "<pre>";print_r($content_sections);
		/*$content_section_item = $content_config['content_section_item'];*/

		/*foreach ($content_section_item as $sidebar_key => $items) {*/
		foreach ($content_config as $cc_key => $items) {

			$content_sections[$cc_key] = $items;
			
			print_r($cc_key);
			print_r($items);

			/*$content_field_slug = $cc_key . '_fields';
			if (isset($content_section_item[$content_field_slug])) {
				$content_sections[$content_field_slug] = $content_section_item[$content_field_slug];
			}*/
		}
		print_r($content_sections);
		echo "</pre>";
		//$CI->config->set_item($config_key, $content_sections);

		return $content_sections;
	}
	
	public function append_homepage_contents_NO_NEED($config_key = 'homepage_contents', $content_config = array())
	{

		$CI = &get_instance();
		$content_sections = $CI->config->item($config_key);
		
		/*print_r($content_sections);*/
		/*$content_section_item = $content_config['content_section_item'];*/

		/*foreach ($content_section_item as $sidebar_key => $items) {*/
		foreach ($content_config as $cc_key => $items) {

			
			if($cc_key == 'homepage_contents')
			{
				foreach($items as  $hc_key => $homepage_content_sections){
						
						
						$f_key = $hc_key."_fields";
						if(array_key_exists($f_key, $content_config)){
							$content_sections[$hc_key] = $homepage_content_sections;	
							$section_fields = $content_config[$f_key] ;
							/*echo "<pre>";print_r($section_fields); echo "</pre>";*/
						
						/*$content_sections[$hc_key][$f_key] = $section_fields;*/
						$CI->config->set_item($f_key, $section_fields);
						}
				}
				
				
				/*print_r($cc_key);
				print_r($items);*/
			}
			/*$content_field_slug = $cc_key . '_fields';
			if (isset($content_section_item[$content_field_slug])) {
				$content_sections[$content_field_slug] = $content_section_item[$content_field_slug];
			}*/
		}
		
		$CI->config->set_item($config_key, $content_sections);
		
		
		return $content_sections;
	}
	
	public function append_dashboard_widgets( $content_config = array())
	{

		$CI = &get_instance();
		$config_key = 'dashboard_widgets';
		$content_sections = $CI->config->item($config_key);
		
		foreach ($content_config as $user => $widgets) {
			if(array_key_exists($user, $content_sections)){
				$content_sections[$user] = array_merge( $content_sections[$user] , $widgets);
			}else{
				$content_sections[$user] =  $widgets;
			}	
		}
		$CI->config->set_item($config_key, $content_sections);
		return $content_sections;
	}
	
	
	
	/****	deprecated since 3.2	***/
	public function append_content_section_fields($config_key = 'content_sections', $content_section_item = array())
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
	
	/****	deprecated since 3.2	***/
	public function append_content_sections($sidebar = 'content_sections', $content_section_item = array())
	{

		$CI = &get_instance();
		/*$content_sections = $CI->config->item('content_sections');*/
		$content_sections = $CI->config->item($sidebar);

		$content_section_item = $content_section_item['content_section_item'];

		foreach ($content_section_item as $sidebar_key => $items) {

			$content_sections[$sidebar_key] = $items;


			$content_field_slug = $sidebar_key . '_fields';
			if (isset($content_section_item[$content_field_slug])) {
				$content_sections[$content_field_slug] = $content_section_item[$content_field_slug];
			}
		}

		/*$CI->config->set_item("content_sections", $content_sections);*/
		$CI->config->set_item($sidebar, $content_sections);

		return $content_sections;
	}


	

	public function get_timestamp_from_date_OLD($date, $type = 'start')
	{
		$CI = &get_instance();
		$default_date_format = get_option('default_date_format');
		if (empty($default_date_format) || $default_date_format == '') {
			$default_date_format = 'mm/dd/yyyy';
		}
		$date_timestamp = '';
		if ($default_date_format == 'mm/dd/yyyy') {
			$date_explode = explode('/', $date);
			if ($type == 'end')
				$date_timestamp = mktime(23, 59, 59, $date_explode[0], $date_explode[1], $date_explode[2]);
			else
				$date_timestamp = mktime(0, 0, 0, $date_explode[0], $date_explode[1], $date_explode[2]);
		} else if ($default_date_format == 'dd/mm/yyyy') {
			$date_explode = explode('/', $date);
			if ($type == 'end')
				$date_timestamp = mktime(23, 59, 59, $date_explode[1], $date_explode[0], $date_explode[2]);
			else
				$date_timestamp = mktime(0, 0, 0, $date_explode[1], $date_explode[0], $date_explode[2]);
		}
		return $date_timestamp;
	}

	public function get_timestamp_from_date($date, $type = 'start', $prev_format = '')
	{
		$CI = &get_instance();
		$default_date_format = get_option('default_date_format');
		if (empty($default_date_format) || $default_date_format == '') {
			$default_date_format = 'mm/dd/yyyy';
		}
		$date_timestamp = '';

		if ($prev_format != '' && $prev_format == 'yyyy/mm/dd') {
			$date_explode = explode('/', $date);
			if ($type == 'end')
				$date_timestamp = mktime(23, 59, 59, $date_explode[1], $date_explode[2], $date_explode[0]);
			else
				$date_timestamp = mktime(0, 0, 0, $date_explode[1], $date_explode[2], $date_explode[0]);
		} else if ($default_date_format == 'mm/dd/yyyy') {
			$date_explode = explode('/', $date);
			if ($type == 'end')
				$date_timestamp = mktime(23, 59, 59, $date_explode[0], $date_explode[1], $date_explode[2]);
			else
				$date_timestamp = mktime(0, 0, 0, $date_explode[0], $date_explode[1], $date_explode[2]);
		} else if ($default_date_format == 'dd/mm/yyyy') {
			$date_explode = explode('/', $date);
			if ($type == 'end')
				$date_timestamp = mktime(23, 59, 59, $date_explode[1], $date_explode[0], $date_explode[2]);
			else
				$date_timestamp = mktime(0, 0, 0, $date_explode[1], $date_explode[0], $date_explode[2]);
		}
		return $date_timestamp;
	}


	public function get_default_date_format($has_short = false)
	{
		$CI = &get_instance();
		$default_date_format = get_option('default_date_format');
		if (empty($default_date_format) || $default_date_format == '') {
			$default_date_format = 'mm/dd/yyyy';
		}
		if ($has_short) {
			$default_date_format = str_replace('mm', 'm', $default_date_format);
			$default_date_format = str_replace('dd', 'd', $default_date_format);
			$default_date_format = str_replace('yyyy', 'yy', $default_date_format);
		}
		return $default_date_format;
	}

	public function get_date_from_timestamp($retun_type = null, $date = null)
	{
		$CI = &get_instance();
		$default_date_format = $this->get_option('default_date_format');
		if (empty($default_date_format) || $default_date_format == '') {
			$default_date_format = 'mm/dd/yyyy';
		}

		$default_date = '';
		if ($default_date_format == 'mm/dd/yyyy') {
			if ($retun_type != null && $retun_type == 'start_date') {
				if ($date != null) {
					$default_date = date('m/04/Y', $date);
				} else {
					$default_date = date('m/04/Y', time());
				}
			} else if ($retun_type != null && $retun_type == 'end_date') {
				if ($date != null) {
					$default_date = date('m/03/Y', $date);
				} else {
					$default_date = date('m/03/Y', time());
				}
			} else {
				if ($date == null) {
					$default_date = date('m/d/Y', time());
				} else {
					$default_date = date('m/d/Y', $date);
				}
			}
		} else if ($default_date_format == 'dd/mm/yyyy') {

			if ($retun_type != null && $retun_type == 'start_date') {

				if ($date != null) {
					$default_date = date('04/m/Y', $date);
				} else {
					$default_date = date('04/m/Y', time());
				}
			} else if ($retun_type != null && $retun_type == 'end_date') {

				if ($date != null) {
					$default_date = date('03/m/Y', $date);
				} else {
					$default_date = date('03/m/Y', time());
				}
			} else {
				if ($date == null) {
					$default_date = date('d/m/Y', time());
				} else {
					$default_date = date('d/m/Y', $date);
				}
			}
		}
		return $default_date;
	}

	public function get_dates_between_2_dates($date1, $date2, $format = 'm/d/Y')
	{
		$dates = array();
		$current = strtotime($date1);
		$date2 = strtotime($date2);
		$stepVal = '+1 day';
		while ($current <= $date2) {
			$dates[] = date($format, $current);
			$current = strtotime($stepVal, $current);
		}
		return $dates;
	}

	public function get_org_country_state_city_title_callback_func($slug, $type)
	{

		extract($_POST);
		$CI = &get_instance();

		if (!empty($slug) && !empty($type)) {

			$locations = $this->get_option('locations');

			if (!empty($locations)) {
				$location_array = json_decode($locations, true);

				if (isset($location_array['countries'])) {
					foreach ($location_array['countries'] as $ck => $cv) {

						if ($type == 'country' && $slug == mlx_get_norm_string($cv['loc_title']))
							return $cv['loc_title'];
						else if (isset($cv['states']) && !empty($cv['states'])) {

							foreach ($cv['states'] as $sk => $sv) {
								if ($type == 'state' && $slug == mlx_get_norm_string($sv['loc_title']))
									return $sv['loc_title'];

								if ($type == 'city' && isset($sv['cities']) && !empty($sv['cities'])) {
									foreach ($sv['cities'] as $cck => $ccv) {
										if ($slug == mlx_get_norm_string($ccv['loc_title']))
											return $ccv['loc_title'];
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function update_language_country_option($key, $val)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from options where option_key = '$key' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$option_id = $row->option_id;
			$option_value = $row->option_value;

			$exp_option_value = array();
			if (!empty($option_value)) {
				$exp_option_value = explode(',', $option_value);
				if (!in_array($val, $exp_option_value))
					$exp_option_value[] = $val;
			} else {
				$exp_option_value[] = $val;
			}

			$datai = array('option_value' => implode(',', $exp_option_value));
			$CI->Common_model->commonUpdate('options', $datai, 'option_id', $option_id);
			return $option_id;
		} else {
			$exp_option_value = array();
			$exp_option_value[] = $val;

			$datai = array('option_key' => $key,	'option_value' => implode(',', $exp_option_value));
			return $CI->Common_model->commonInsert('options', $datai);
		}
	}

	public function remove_language_country_option($key, $val)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from options where option_key = '$key' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$option_id = $row->option_id;
			$option_value = $row->option_value;

			$exp_option_value = array();
			if (!empty($option_value)) {
				$exp_option_value = explode(',', $option_value);
				if (($key = array_search($val, $exp_option_value)) !== false) {
					unset($exp_option_value[$key]);
				}
			}

			$datai = array('option_value' => implode(',', $exp_option_value));
			$CI->Common_model->commonUpdate('options', $datai, 'option_id', $option_id);
			return $option_id;
		}
	}


	public function add_user_permission123($args)
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

				$site_user_access[$user_type]['view']['all_items'] = array_merge($site_user_access[$user_type]['view']['all_items'], $view_items);
			}
		}
		$CI->config->set_item('site_user_access', $site_user_access);
	}

	public function append_menu_items123($sidebar = 'sidebar_left', $new_sidebar_config = array())
	{

		$CI = &get_instance();
		$sidebar = $CI->config->item($sidebar);

		/*echo "<pre>";print_r($new_sidebar_config); echo "</pre>";
		if(isset($_GET['a'])){*/
		$new_sidebar_config = current($new_sidebar_config);
		foreach ($new_sidebar_config as $sidebar_key => $items) {

			if (array_key_exists($sidebar_key, $sidebar)) {
				foreach ($items['item'] as $item) {
					$sidebar[$sidebar_key]['item'][] = $item;
				}
			} else {
				$sidebar[$sidebar_key] = $items;
			}
		}
		/*echo "<pre>";print_r($sidebar); echo "</pre>"; exit;
		
		}*/
		/*return $sidebar;*/
		$CI->config->set_item("sidebar_left", $sidebar);
		return $sidebar;
	}


	public function get_property_url($property_id, $property = '')
	{

		$CI = &get_instance();
		$CI->load->library('Property_lib');
		return $CI->property_lib->get_url($property_id, $property);
	}


	public function uri_check_front()
	{
		$CI = &get_instance();
		$str = uri_string();
		$strs = explode("/", $str);
		$data['class'] = 'main';
		$multi_lang = $CI->enable_multi_lang;

		if (count($strs) == 3 && $multi_lang) {
			$data['func'] = $strs[2];
			switch ($strs[2]) {
				case 'contact':
					$data['func'] = 'contact';
					break;
			}
		} else if (count($strs) == 2 && $multi_lang) {
			$data['func'] = $strs[1];
			if ($strs[1] == $CI->default_language) {
				$data['func'] = 'home';
				$data['class'] = 'home';
			}
			switch ($strs[0]) {
				case 'contact':
					$data['func'] = 'contact';
					$data['class'] = 'main';
					break;
				case 'property':
					$data['func'] = 'property';
					break;
			}
		} else if (!$multi_lang  && isset($strs[1])) {
			$data['func'] = $strs[1];
			switch ($strs[1]) {
				case 'home':
					$data['class'] = 'home';
					break;

					/*case 'view_article': 
				$data['class']='articles';break;*/
			}
		} else {
			$data['func'] = 'home';
			$data['class'] = 'home';
		}

		return $data;
	}

	public function uri_check()
	{
		$CI = &get_instance();	
		$str = uri_string();
		$strs = explode("/", $str);
		if (isset($strs[2])) {
			$data['func'] = $strs[2];
			$data['class'] = $strs[1];
		} else {
			if($CI->router->fetch_class() == '')			$data['class'] = 'home';
			else	$data['class'] = 	$CI->router->fetch_class();
			
			if($CI->router->fetch_method() == '') $data['func'] = 'home';
			else	$data['func'] = $CI->router->fetch_method();	
		}

		return $data;
	}

	public function get_options()
	{
		$CI = &get_instance();

		if (empty($this->site_options)) {
			$sql  = "select * from options";
			$options_list = $CI->Common_model->commonQuery($sql);

			$options = array();
			if (isset($options_list) && $options_list->num_rows() > 0) {
				foreach ($options_list->result() as $row) {
					$options[$row->option_key] = $row->option_value;
				}
			}

			$this->site_options = $options;
		}

		return 		$this->site_options;
	}

	public function get_option($option = "")
	{

		$result = "";
		if (!empty($option)) {
			$options = $this->get_options();
			if (array_key_exists($option, $options))
				$result = $options[$option];
		}

		return 	$result;
	}

	public function get_option_lang($key, $lang = 'en')
	{
		$CI = &get_instance();

		if ($CI->enable_multi_lang) {
			$query = $CI->Common_model->commonQuery("select lang_text from options as opt
			inner join options_lang_details as old on old.opt_id = opt.option_id
			where opt.option_key = '$key' and old.language = '$lang' and old.lang_text != ''");
			if ($query->num_rows() > 0) {
				$row = $query->row();
				return $row->lang_text;
			} else {
				return $this->get_option($key);
			}
		} else {
			return $this->get_option($key);
		}
	}


	public function clear_option($key = "")
	{
		$CI = &get_instance();
		$result = "";
		if (!empty($key)) {
			/*$CI->Common_model->commonQuery("delete from options where option_key = '$option' ");*/

			$query = $CI->Common_model->commonQuery("select * from options where option_key = '$key' ");
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$option_id = $row->option_id;
				$datai = array('option_value' => '');
				$CI->Common_model->commonUpdate('options', $datai, 'option_id', $option_id);
			}
		}
	}

	public function get_property_image($id = NULL, $type = NULL)
	{

		$CI = &get_instance();
		if ($type == NULL)
		$type = 'thumbnail';
		$query = $CI->Common_model->commonQuery("select * from properties where p_id = '$id' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			if (!empty($row->property_images)) {
				$img_exp = explode(',', $row->property_images);

				foreach ($img_exp as $k => $v) {

					$img_query = $CI->Common_model->commonQuery("select p1.* from post_images pi 
								inner join post_images as p1 on p1.parent_image_id = pi.image_id
								and p1.image_type = '$type'
								where pi.image_id = '$v'");
					$image_meta = array();

					if ($img_query->num_rows() > 0) {
						$img_row = $img_query->row();
						if (file_exists($img_row->image_path . $img_row->image_name)) {
							$image_meta[] = $img_row->image_path . $img_row->image_name;
						}
					}
					return $image_meta;
				}
			} else
				return false;
		} else
		return false;
	}


	public function update_option($key, $val)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from options where option_key = '$key' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$option_id = $row->option_id;
			$datai = array('option_value' => $val);
			$CI->Common_model->commonUpdate('options', $datai, 'option_id', $option_id);
			return $option_id;
		} else {
			$datai = array('option_key' => $key,	'option_value' => $val);
			return $CI->Common_model->commonInsert('options', $datai);
		}
	}


	public function get_property_lang($p_id, $col, $lang = 'en')
	{
		if ($p_id == '' || empty($p_id))
			return '';
		if ($col == '' || empty($col))
			return $col;

		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select $col from property_lang_details as pld
		where pld.p_id = '$p_id' and pld.language = '$lang' and pld.$col != ''");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->$col;
		} else {
			return '';
		}
	}

	public function get_seo_settings($type = "", $option = "")
	{

		$result = "";
		if (!empty($option) && !empty($type)) {
			$seo_settings = $this->get_option('seo_settings');
			if (!empty($seo_settings)) {
				$seo_settings_meta = json_decode($seo_settings, true);
				if (isset($seo_settings_meta[$type][$option]))
					$result = $seo_settings_meta[$type][$option];
			}
		}

		return 	$result;
	}

	public function get_skin_class()
	{
		$CI = &get_instance();

		$skin_default = 'skin-blue';
		$skin_class = 'primary';
		$skin = $this->get_option('skin');
		if (!empty($skin))
			$skin_default = $skin;

		if ($skin_default == 'skin-blue' || $skin_default == 'skin-blue-light') {
			$skin_class = 'primary';
		} else if ($skin_default == 'skin-black' || $skin_default == 'skin-black-light') {
			$skin_class = 'default';
		} else if ($skin_default == 'skin-purple' || $skin_default == 'skin-purple-light') {
			$skin_class = 'purple';
		} else if ($skin_default == 'skin-green' || $skin_default == 'skin-green-light') {
			$skin_class = 'success';
		} else if ($skin_default == 'skin-red' || $skin_default == 'skin-red-light') {
			$skin_class = 'danger';
		} else if ($skin_default == 'skin-yellow' || $skin_default == 'skin-yellow-light') {
			$skin_class = 'warning';
		}
		return $skin_class;
	}
	
	/**	deprecated  3.3.4 **/
	public function get_image_type($img_path = null, $img_name = null, $return_type = 'thumb')
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



	public function get_property_price_by_lang($p_id, $lang)
	{
		if ($p_id == '' || $lang == '')
			return '';

		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select price from property_lang_details as pld
		where pld.p_id = '$p_id' and pld.language = '$lang' and pld.price != ''");
		if ($query->num_rows() > 0) {

			$site_language = $this->get_option('site_language');
			$site_language_array = json_decode($site_language, true);
			$currency = '$';
			foreach ($site_language_array as $k => $v) {
				if (strpos($v['language'], '~' . $lang) !== false) {
					$currency_code = $v['currency'];
					$currency_symbols = $CI->config->item('currency_symbols');
					if (array_key_exists($currency_code, $currency_symbols))
						$currency =  $currency_symbols[$currency_code];
				}
			}

			$row = $query->row();
			return array('price' => $row->price, 'currency' => $currency);
		}
	}

	public function get_lang_title_by_code($lang_code)
	{
		if ($lang_code == '')
			return '';

		$site_language = $this->get_option('site_language');
		$site_language_array = json_decode($site_language, true);
		foreach ($site_language_array as $k => $v) {
			if (strpos($v['language'], '~' . $lang_code) !== false) {
				$lang_exp = explode('~', $v['language']);
				return ucfirst($lang_exp[0]);
			}
		}
		return '';
	}

	public function get_property_description_by_lang($p_id, $lang)
	{
		if ($p_id == '' || $lang == '')
			return '';

		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select description from property_lang_details as pld
		where pld.p_id = '$p_id' and pld.language = '$lang' and pld.description != ''");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->description;
		}
	}

	public function get_property_address_by_lang($p_id, $lang)
	{
		if ($p_id == '' || $lang == '')
			return '';

		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select address from property_lang_details as pld
		where pld.p_id = '$p_id' and pld.language = '$lang' and pld.address != ''");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->address;
		}
	}

	public function update_post_meta($post_id, $key, $val)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from post_meta where post_id = '$post_id' AND meta_key = '$key' ");

		if ($query->num_rows() > 0) {
			$row = $query->row();
			$meta_id = $row->meta_id;
			$datai = array('meta_value' => $val);

			return $metaid = $CI->Common_model->commonUpdate('post_meta', $datai, 'meta_id', $meta_id);
		} else {
			$datai = array('meta_key' => $key,	'meta_value' => $val, 'post_id' => $post_id);

			return $metaid = $CI->Common_model->commonInsert('post_meta', $datai);
		}
	}

	public function get_post_meta($id = NULL, $key = NULL)
	{
		$CI = &get_instance();

		$query = $CI->Common_model->commonQuery("select * from post_meta where post_id = '$id' AND meta_key = '$key' ");

		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $val = $row->meta_value;
		} else
			return false;
	}

	public function get_include_contents($filename)
	{
		
		if (is_file($filename)) {
			ob_start(); 
				header('Content-Type: text/html; charset=ISO-8859-1');
				include $filename;
			$output =  ob_get_contents();
			ob_end_clean();
			return $output;
			/*return ob_get_clean();*/
		}
		return false;
	}
	
	public function strtoarray($a, $t = '')
	{

		$arr = array();
		$a = str_replace('$lang[\'', '', $a);

		$tempArr = explode(";", $a);
		foreach ($tempArr as $k => $v) {
			$tempArr2 = explode("'] = ", $v);
			if (count($tempArr2) > 1)
				$arr[trim($tempArr2[0])] = trim(str_replace("'", "", $tempArr2[1]));
		}
		
		return $arr;
	}

	public function get_post_metadata($id = NULL)
	{
		$CI = &get_instance();

		$query = $CI->Common_model->commonQuery("select * from post_meta where post_id = '$id'");

		if ($query->num_rows() > 0) {
			$metadata_array = array();
			foreach ($query->result() as $row) {
				$metadata_array[$row->meta_key] = $row->meta_value;
			}
			return $metadata_array;
		} else
			return false;
	}


	public function get_slug($input_string = NULL, $seperator = "_")
	{
		$slug = trim($input_string);
		$slug	=	preg_replace('/[^A-Za-z0-9 ]/', '', $slug);
		$aslug = explode(" ", $slug);

		foreach ($aslug as $k => $v) {

			$aslug[$k] = strtolower($aslug[$k]);

			if (!$aslug[$k]) unset($aslug[$k]);
		}

		$slug = implode($seperator, $aslug);



		return $slug;
	}

	public function get_property_gallery($id = NULL, $type = 'original')
	{

		$CI = &get_instance();
		$image_meta = array();
		$query = $CI->Common_model->commonQuery("select * from properties where p_id = '$id' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();

			if (!empty($row->property_images)) {
				$img_exp = explode(',', $row->property_images);

				foreach ($img_exp as $k => $v) {
					if ($type == 'original') {
						$img_query = $CI->Common_model->commonQuery("select pi.* from post_images pi 
								where pi.image_type = '$type' and pi.image_id = '$v'");

						if ($img_query->num_rows() > 0) {
							foreach ($img_query->result() as $img_row) {
								if (file_exists($img_row->image_path . $img_row->image_name)) {
									$image_meta[$img_row->image_id][$img_row->image_type] = $img_row->image_path . $img_row->image_name;
								}
							}
						}
					} else {
						$img_query = $CI->Common_model->commonQuery("select pi.* from post_images pi 
								where ( pi.image_type = '$type' ) and pi.parent_image_id = '$v'");

						if ($img_query->num_rows() > 0) {
							foreach ($img_query->result() as $img_row) {
								if (file_exists($img_row->image_path . $img_row->image_name)) {
									$image_meta[$img_row->parent_image_id][$img_row->image_type] = $img_row->image_path . $img_row->image_name;
								}
							}
						}
					}
				}
			}
		}
		return $image_meta;
	}

	public function EncryptClientId($id)
	{
		return substr(md5($id), 0, 8) . dechex($id);
	}

	public function DecryptClientId($id)
	{
		$md5_8 = substr($id, 0, 8);
		$real_id = hexdec(substr($id, 8));
		return ($md5_8 == substr(md5($real_id), 0, 8)) ? $real_id : 0;
	}

	public function generate_random_string($length = 20)
	{
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));

		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}

		return $key;
	}
	public function get_post_title_by_slug($slug)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from posts where post_slug = '$slug' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$post_title = $row->post_title;
			return $post_title;
		} else {
			return false;
		}
	}


	public function get_cat_title_by_slug($slug)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from categories where cat_slug = '$slug' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$cat_title = $row->cat_title;
			return $cat_title;
		} else {
			return false;
		}
	}

	public function get_page_slug_by_id($page_id)
	{
		$CI = &get_instance();
		$enc_id = DecryptClientID($page_id);
		$query = $CI->Common_model->commonQuery("select page_slug from pages where page_id = '$enc_id' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$page_slug = $row->page_slug;
			return $page_slug;
		} else {
			return false;
		}
	}

	public function get_user_meta($id = NULL, $key = NULL)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from user_meta where user_id = '$id' AND meta_key = '$key' ");

		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $val = $row->meta_value;
		} else
			return false;
	}

	public function update_user_meta($user_id, $key, $val)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from user_meta where user_id = '$user_id' AND meta_key = '$key' ");

		if ($query->num_rows() > 0) {
			$row = $query->row();
			$meta_id = $row->meta_id;
			$datai = array('meta_value' => $val);

			return $metaid = $CI->Common_model->commonUpdate('user_meta', $datai, 'meta_id', $meta_id);
		} else {
			$datai = array('meta_key' => $key,	'meta_value' => $val, 'user_id' => $user_id);

			return $metaid = $CI->Common_model->commonInsert('user_meta', $datai);
		}
	}

	public function get_user_metadata($id = NULL)
	{
		$CI = &get_instance();

		$query = $CI->Common_model->commonQuery("select * from user_meta where user_id = '$id'");

		if ($query->num_rows() > 0) {
			$metadata_array = array();
			foreach ($query->result() as $row) {
				$metadata_array[$row->meta_key] = $row->meta_value;
			}
			return $metadata_array;
		} else
			return false;
	}

	public function get_admin_user_emails()
	{
		$CI = &get_instance();
		$user_emails = array();
		$query = $CI->Common_model->commonQuery("select user_email from users where user_type = 'admin'");
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$user_emails[] = $row->user_email;
			}
		}
		return $user_emails;
	}

	public function get_product_image($id = NULL, $type = NULL, $count = NULL)
	{

		$CI = &get_instance();
		if ($type == NULL)
			$type = 'thumbnail';
		$query = $CI->Common_model->commonQuery("select * from products where product_id = '$id' ");
		if ($query->num_rows() > 0) {
			$row = $query->row();
			if (!empty($row->product_images)) {
				$img_exp = explode(',', $row->product_images);

				$n = 0;
				$image_meta = array();
				foreach ($img_exp as $k => $v) {
					if ($count != NULL && $count == $n) {
						break;
					}

					$img_query = $CI->Common_model->commonQuery("select p1.* from post_images pi 
								inner join post_images as p1 on p1.parent_image_id = pi.image_id
								and p1.image_type = '$type'
								where pi.image_id = '$v'");


					if ($img_query->num_rows() > 0) {
						$img_row = $img_query->row();
						$image_meta[] = $img_row->image_path . $img_row->image_name;
						$n++;
					}
				}
				return $image_meta;
			} else
				return false;
		} else
			return false;
	}

	public function get_get_title_by_ids($ids = NULL)
	{
		$cat_title_string = '';
		if ($ids == NULL)
			return $cat_title_string;
		$CI = &get_instance();

		$query = $CI->Common_model->commonQuery("select * from categories where cat_id in ($ids) ");
		if ($query->num_rows() > 0) {

			foreach ($query->result() as $row) {
				$cat_title_string .= $row->cat_slug . ' ';
			}
		}
		return $cat_title_string;
	}

	public function truncate_string($string, $length, $stopanywhere = false)
	{

		$words = explode(" ", $string);
		if (count($words) > $length) {
			return implode(" ", array_splice($words, 0, $length)) . '...';
		}
		return $string;
	}

	function getVisitorIP_func()
	{
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if (filter_var($client, FILTER_VALIDATE_IP)) {
			$ip = $client;
		} elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return $ip;
	}

	function relativeTime($time, $short = false)
	{
		$SECOND = 1;
		$MINUTE = 60 * $SECOND;
		$HOUR = 60 * $MINUTE;
		$DAY = 24 * $HOUR;
		$MONTH = 30 * $DAY;
		$before = time() - $time;

		if ($before < 0) {
			return mlx_get_lang("Not Yet");
		}

		if ($short) {
			if ($before < 1 * $MINUTE) {
				return ($before < 5) ? mlx_get_lang("Just Now") : $before . mlx_get_lang(" Ago");
			}

			if ($before < 2 * $MINUTE) {
				return mlx_get_lang("1 Min Ago");
			}

			if ($before < 45 * $MINUTE) {
				return floor($before / 60) . " " . mlx_get_lang("Min Ago");
			}

			if ($before < 90 * $MINUTE) {
				return mlx_get_lang("1 Hour Ago");
			}

			if ($before < 24 * $HOUR) {

				return floor($before / 60 / 60) . " " . mlx_get_lang("Hour Ago");
			}

			if ($before < 48 * $HOUR) {
				return mlx_get_lang("1 Day Ago");
			}

			if ($before < 30 * $DAY) {
				return floor($before / 60 / 60 / 24) . " " . mlx_get_lang('Day Ago');
			}


			if ($before < 12 * $MONTH) {
				$months = floor($before / 60 / 60 / 24 / 30);
				return $months <= 1 ? mlx_get_lang("1 Month Ago") : $months . " " . mlx_get_lang("Month Ago");
			} else {
				$years = floor($before / 60 / 60 / 24 / 30 / 12);
				return $years <= 1 ? mlx_get_lang("1 Year Ago") : $years . " " . mlx_get_lang("Year Ago");
			}
		}

		if ($before < 1 * $MINUTE) {
			return ($before <= 1) ? mlx_get_lang("Just Now") : $before . " " . mlx_get_lang("Seconds Ago");
		}

		if ($before < 2 * $MINUTE) {
			return mlx_get_lang("A Minute Ago");
		}

		if ($before < 45 * $MINUTE) {
			return floor($before / 60) . " " . mlx_get_lang("Minutes Ago");
		}

		if ($before < 90 * $MINUTE) {
			return mlx_get_lang("An Hour Ago");
		}

		if ($before < 24 * $HOUR) {

			return (floor($before / 60 / 60) == 1 ? mlx_get_lang('About an Hour') : floor($before / 60 / 60) . ' ' . mlx_get_lang('Hours')) . " " . mlx_get_lang("Ago");
		}

		if ($before < 48 * $HOUR) {
			return mlx_get_lang("Yesterday");
		}

		if ($before < 30 * $DAY) {
			return floor($before / 60 / 60 / 24) . " " . mlx_get_lang("Days Ago");
		}

		if ($before < 12 * $MONTH) {

			$months = floor($before / 60 / 60 / 24 / 30);
			return $months <= 1 ? mlx_get_lang("One Month Ago") : $months . " " . mlx_get_lang("Months Ago");
		} else {
			$years = floor($before / 60 / 60 / 24 / 30 / 12);
			return $years <= 1 ? mlx_get_lang("One Year Ago") : $years . " " . mlx_get_lang("Years Ago");
		}

		return "$time";
	}

	public function get_currency_symbol_OLD()
	{

		$CI = &get_instance();

		$currency_symbols = $CI->config->item('currency_symbols');
		$selected_currency = get_option('currency');

		if (isset($CI->site_currency) && !empty($CI->site_currency)) {
			if (array_key_exists($CI->site_currency, $currency_symbols))
				return $currency_symbols[$CI->site_currency];
		} else if (isset($currency_symbols) && !empty($currency_symbols) && $selected_currency && $selected_currency != '') {
			if (array_key_exists($selected_currency, $currency_symbols))
				return $currency_symbols[$selected_currency];
		}
		return '';
	}
	/****	deprecated 3.2	***/
	public function get_currency_symbol($currency_code = null)
	{
		
		$CI =& get_instance();
		
		$currency_symbols = $CI->config->item('currency_symbols');
		
		$selected_currency = $CI->site_currency;
		if($currency_code != null)
		{
			if(array_key_exists($currency_code,$currency_symbols))
				return $currency_symbols[$currency_code];
		}
		else if(isset($currency_symbols) && !empty($currency_symbols) && $selected_currency && $selected_currency != '')
		{
			
			if(array_key_exists($selected_currency,$currency_symbols))
				return $currency_symbols[$selected_currency];
		}
		return '';
	}
	

	public function get_currency_symbol_by_property($p_id = null)
	{

		$CI = &get_instance();

		$currency_symbols = $CI->config->item('currency_symbols');

		if ($CI->enable_multi_lang && $p_id != null) {
			$site_language = get_option('site_language');
			$default_language = $CI->default_language;
			$default_language_title = $CI->default_language_title;
			$query = $CI->Common_model->commonQuery("select price from property_lang_details where p_id = '$p_id' and language = '$default_language' and price != ''");
			if ($query->num_rows() > 0 && !empty($site_language)) {
				$site_language_array = json_decode($site_language, true);
				foreach ($site_language_array as $slak => $slav) {
					if ($slav['language'] == $default_language_title . '~' . $default_language) {
						$sel_currency = $slav['currency'];
						if (array_key_exists($sel_currency, $currency_symbols))
							return $currency_symbols[$sel_currency];
					}
				}
			} else if (!empty($site_language)) {
				$default_language = get_option('default_language');
				$site_language_array = json_decode($site_language, true);
				foreach ($site_language_array as $slak => $slav) {
					if ($slav['language'] == $default_language) {
						$sel_currency = $slav['currency'];
						if (array_key_exists($sel_currency, $currency_symbols))
							return $currency_symbols[$sel_currency];
					}
				}
			}
		} else if (isset($CI->site_currency) && !empty($CI->site_currency)) {
			if (array_key_exists($CI->site_currency, $currency_symbols))
				return $currency_symbols[$CI->site_currency];
		}

		return '';
	}

	/****	deprecated 3.2	***/
	public function moneyFormatDollar($num, $args = array())
	{
		
		extract($args);
		$CI = &get_instance();

		if (isset($CI->currency_pos)) $currency_pos =  $CI->currency_pos;
		else $currency_pos = 'left';
		if (isset($CI->thousand_sep)) $thousand_sep =  $CI->thousand_sep;
		else $thousand_sep = ',';
		if (isset($CI->decimal_sep))  $decimal_sep =  $CI->decimal_sep;
		else $decimal_sep = '.';
		if (isset($CI->num_decimals)) $num_decimals =  $CI->num_decimals;
		else $num_decimals = '2';

		/*$amount = number_format($num, $num_decimals, $decimal_sep, $thousand_sep);*/
		$amount = $num;
		
		if (isset($currency_symbol)) {
			if ($currency_pos == 'left')
				$amount = $currency_symbol . $amount;
			if ($currency_pos == 'left_space')
				$amount = $currency_symbol . " " . $amount;

			if ($currency_pos == 'right')
				$amount .= $currency_symbol;
			if ($currency_pos == 'right_space')
				$amount .= " " . $currency_symbol;
		}
		return $amount;
		
	}


	

	public function get_bookmarks($id = NULL)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from favorite_table where user_id = '$id'");
		//var_dump($query->result());
		if ($query->num_rows() > 0) {
			$bookmark_contents = array();
			foreach ($query->result() as $row) {
				//$bookmark_contents['page_id'] = $row->p_id;
				//$bookmark_contents['page_title'] = $row->title;

				$bookmark_contents[$row->p_id] = array('page_id' => $row->p_id, 'page_title' => $row->title);
			}
			//print_r($bookmark_contents);
			return $bookmark_contents;
		} else {
			return false;
		}
	}

	public function get_dates_between_2_timestamp($date1, $date2, $format = 'm/d/Y')
	{
		$dates = array();
		$current = $date1;
		$date2 = $date2;
		$stepVal = '+1 day';
		while ($current <= $date2) {
			$dates[] = date($format, $current);
			$current = strtotime($stepVal, $current);
		}
		return $dates;
	}


	public function get_user_emails_by_user_ids($user_ids)
	{
		$CI =& get_instance();
		$user_emails = array();
		$query = $CI->Common_model->commonQuery("select user_email from users where user_id in ($user_ids)");	
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$user_emails[] = $row->user_email;
			}
			
		}
		return $user_emails;
	}


	/****	deprecated since 3.2	***/
	public function send_email_notification($args = array())
	{

		$CI = &get_instance();
		$CI->load->library('Email_lib');
		$CI->email_lib->send_email_notification($args);
	}

	/****	deprecated since 3.2	***/
	public function send_email_notifications_to_admin($email_template = "", $em_args = array())
	{
		$CI = &get_instance();

		if (empty($email_template)) return false;

		$admin_emails = $this->get_admin_user_emails();
		if (!empty($admin_emails)) {

			foreach ($admin_emails as $ak => $av) {
				$args['to_email'] = $av;

				$args['email_template'] = $email_template;
				if (count($em_args) > 0) $args = array_merge($args, $em_args);

				$this->send_email_notification($args);
			}
		}
	}
}
