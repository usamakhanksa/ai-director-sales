<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_locations_lib {

	
	public function Index(){}
	
	
	
	public function get_state_or_city_list_homepage_sections_callback()
	{

		extract($_POST);
		$CI = &get_instance();

		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		if (!empty($loc_tax_settings)) {
			$loc_tax_setting_array = json_decode($loc_tax_settings, true);
			if (isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
		}

		$locations = $CI->global_lib->get_option('locations');

		$country_name = '';
		$state_list = '<option value="all" selected>All States</option>';
		$city_list = '<option value="all" selected>All Cities</option>';
		$zipcode_list = '<option value="all" selected>All Zipcodes</option>';
		$subarea_list = '<option value="all" selected>All Subarea</option>';

		$state_code = $city_code = '';

		if (!empty($locations) && $is_state_enable) {
			$location_array = json_decode($locations, true);

			$lc_val = $country_code;
			if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
				$country_name = $location_array['countries'][$lc_val]['loc_title'];

				if (isset($location_array['countries'][$lc_val]['states'])) {
					foreach ($location_array['countries'][$lc_val]['states'] as $skey => $sval) {
						if ($skey != 'no_state')
							$state_list .= '<option data-country_code="' . $lc_val . '" data-state_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
						if (isset($lang_state) && $lang_state == $sval['loc_title']) {
							$state_code = $skey;
							$state_list .= ' selected="selected" ';
						}
						$state_list .= '>' . $sval['loc_title'] . '</option>';
					}
				}
			}
			if (isset($is_edit) && !empty($state_code)) {
				if (isset($location_array['countries'][$lc_val]['states'][$state_code]['cities'])) {
					$cities = $location_array['countries'][$lc_val]['states'][$state_code]['cities'];
					if (!empty($cities)) {
						foreach ($cities as $skey => $sval) {
							$city_list .= '<option data-country_code="' . $lc_val . '" data-state_code="' . $state_code . '" data-city_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
							if (isset($lang_city) && $lang_city == $sval['loc_title']) {
								$city_list .= ' selected="selected" ';

								if (isset($sval['zipcodes']) && !empty($sval['zipcodes'])) {
									foreach ($sval['zipcodes'] as $zipcode) {
										$zipcode_list .= '<option value="' . $zipcode . '"';
										if (isset($lang_zip_code) && $lang_zip_code == $zipcode)
											$zipcode_list .= ' selected="selected" ';
										$zipcode_list .= '>' . $zipcode . '</option>';
									}
								}
								if (isset($sval['sub_areas']) && !empty($sval['sub_areas'])) {
									foreach ($sval['sub_areas'] as $subarea) {
										$subarea_list .= '<option value="' . $subarea . '"';
										if (isset($lang_sub_area) && $lang_sub_area == $subarea)
											$subarea_list .= ' selected="selected" ';
										$subarea_list .= '>' . $subarea . '</option>';
									}
								}
							}
							$city_list .= '>' . $sval['loc_title'] . '</option>';
						}
					}
				}
			}
		} else if (!empty($locations)) {
			$location_array = json_decode($locations, true);

			$lc_val = $country_code;
			if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
				$country_name = $location_array['countries'][$lc_val]['loc_title'];

				if (isset($location_array['countries'][$lc_val]['states']['no_state']['cities'])) {
					foreach ($location_array['countries'][$lc_val]['states']['no_state']['cities'] as $skey => $sval) {
						$city_list .= '<option data-country_code="' . $lc_val . '" data-state_code="no_state" data-city_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
						if (isset($lang_city) && $lang_city == $sval['loc_title'])
							$city_list .= ' selected="selected" ';
						$city_list .= '>' . $sval['loc_title'] . '</option>';
					}
				}
			}
		}

		header('Content-type: application/json');
		echo json_encode(array('state_list' => $state_list, 'city_list' => $city_list, 'zipcode_list' => $zipcode_list, 'subarea_list' => $subarea_list));
	}
	
	
	
	public function get_state_list_from_country_homepage_sections_callback()
	{

		extract($_POST);
		$CI = &get_instance();

		$state_list = '<option value="all" selected>All States</option>';

		if (isset($country_code)) {
			$locations = $this->global_lib->get_option('locations');
			$location_array = json_decode($locations, true);

			$lc_val = $country_code;
			if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
				if (isset($location_array['countries'][$lc_val]['states'])) {
					foreach ($location_array['countries'][$lc_val]['states'] as $skey => $sval) {
						$state_list .= '<option data-country_code="' . $lc_val . '" data-state_code="' . $skey . '" 
										value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
						$state_list .= '>' . $sval['loc_title'] . '</option>';
					}
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode(array('state_list' => $state_list));
	}

	public function get_city_list_from_state_homepage_sections_callback()
	{

		extract($_POST);
		$CI = &get_instance();

		$locations = $CI->global_lib->get_option('locations');

		$city_list = '<option value="all" selected>All Cities</option>';

		$location_array = json_decode($locations, true);

		$lc_val = $country_code;
		if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
			if (isset($location_array['countries'][$lc_val]['states'][$state_code]['cities'])) {
				foreach ($location_array['countries'][$lc_val]['states'][$state_code]['cities'] as $skey => $sval) {
					$city_list .= '<option data-country_code="' . $lc_val . '" data-state_code="' . $state_code . '" data-city_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
					$city_list .= '>' . $sval['loc_title'] . '</option>';
				}
			}
		}

		header('Content-type: application/json');
		echo json_encode(array('city_list' => $city_list));
	}

	public function get_zip_subarea_list_from_city_homepage_sections_callback()
	{
		extract($_POST);
		$CI = &get_instance();

		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		if (!empty($loc_tax_settings)) {
			$loc_tax_setting_array = json_decode($loc_tax_settings, true);
			if (isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
		}

		$locations = $CI->global_lib->get_option('locations');

		$zipcode_list = '<option value="all" selected>All Zipcodes</option>';
		$subarea_list = '<option value="all" selected>All Sub Areas</option>';


		if (!empty($locations) && $is_state_enable) {

			$location_array = json_decode($locations, true);

			$lc_val = $country_code;

			if (!empty($state_code) && !empty($city_code)) {
				if (isset($location_array['countries'][$lc_val]['states'][$state_code]['cities'][$city_code])) {
					$city = $location_array['countries'][$lc_val]['states'][$state_code]['cities'][$city_code];


					if (isset($city['zipcodes']) && !empty($city['zipcodes'])) {
						foreach ($city['zipcodes'] as $zipcode) {
							$zipcode_list .= '<option value="' . $zipcode . '"';
							$zipcode_list .= '>' . $zipcode . '</option>';
						}
					}
					if (isset($city['sub_areas']) && !empty($city['sub_areas'])) {
						foreach ($city['sub_areas'] as $subarea) {
							$subarea_list .= '<option value="' . $subarea . '"';
							$subarea_list .= '>' . $subarea . '</option>';
						}
					}
				}
			}
		} else if (!empty($locations)) {
			$location_array = json_decode($locations, true);

			$lc_val = $country_code;
			if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
				if (isset($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code])) {
					$city = $location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code];

					if (isset($city['zipcodes']) && !empty($city['zipcodes'])) {
						foreach ($city['zipcodes'] as $zipcode) {
							$zipcode_list .= '<option value="' . $zipcode . '"';
							$zipcode_list .= '>' . $zipcode . '</option>';
						}
					}
					if (isset($city['sub_areas']) && !empty($city['sub_areas'])) {
						foreach ($city['sub_areas'] as $subarea) {
							$subarea_list .= '<option value="' . $subarea . '"';
							$subarea_list .= '>' . $subarea . '</option>';
						}
					}
				}
			}
		}

		header('Content-type: application/json');
		echo json_encode(array('zipcode_list' => $zipcode_list, 'subarea_list' => $subarea_list));
	}


	
	public function get_city_name_list_callback_func123()
	{
		
		extract($_POST);		
		$CI =& get_instance();	
		$cities_list = '<option value="">Select Any City</option>';
		
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$locations = $CI->global_lib->get_option('locations');
		
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
			if(isset($location_array['countries'][$country_code]['states'][$state_code]['cities']))
			{
				$cities = $location_array['countries'][$country_code]['states'][$state_code]['cities'];
				if(!empty($cities))
				{
					foreach($cities as $ck=>$cv)
					{
						$cities_list .= '<option data-country_code="'.$country_code.'" 
												 data-state_code="'.$state_code.'" 
												 data-city_code="'.$ck.'" 
												 data-full_value="'.$cv['loc_title'].'" 
												 value="'.mlx_get_norm_string($cv['loc_title']).'">'.mlx_get_lang_with_org(mlx_get_norm_string($cv['loc_title']),$cv['loc_title']).'</option>';
					}
					echo $cities_list; exit;
				}
					
			}
		}
		
		
	}
	
	
	public function get_zip_sub_area_name_list_callback_func123(){
		
		extract($_POST);		
		$CI =& get_instance();	
		$zipcode_list = '<option value="">Select Any Zipcode</option>';
		$subarea_list = '<option value="">Select Any Sub Area</option>';
		
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$locations = $CI->global_lib->get_option('locations');
		
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
			if(isset($location_array['countries'][$country_code]['states'][$state_code]['cities'][$city_code]['zipcodes']))
			{
				$zipcodes = $location_array['countries'][$country_code]['states'][$state_code]['cities'][$city_code]['zipcodes'];
				if(!empty($zipcodes))
				{
					foreach($zipcodes as $ck=>$cv)
					{
						$zipcode_list .= '<option value="'.$cv.'">'.$cv.'</option>';
					}
				}
			}
			
			if(isset($location_array['countries'][$country_code]['states'][$state_code]['cities'][$city_code]['sub_areas']))
			{
				$sub_areas = $location_array['countries'][$country_code]['states'][$state_code]['cities'][$city_code]['sub_areas'];
				if(!empty($sub_areas))
				{
					foreach($sub_areas as $ck=>$cv)
					{
						$subarea_list .= '<option value="'.$cv.'">'.$cv.'</option>';
					}
				}
			}
		}
		
		header('Content-type: application/json');			
		echo json_encode(array('zipcode_list' => $zipcode_list,'subarea_list' => $subarea_list));
		
	}
	
	
	
	public function remove_element_for_locations()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		
		$locations = $CI->global_lib->get_option('locations');
		if(!empty($locations) && isset($elem_type))
		{
			$locations = json_decode($locations,true);
			
			if($elem_type == 'country')
			{
				if(array_key_exists($elem , $locations['countries']))
					unset($locations['countries'][$elem]);
					
				$opt_result = $CI->Common_model->commonQuery("select * from options where find_in_set('$elem',option_value)
				and option_key like 'language_country_%'");
				if($opt_result->num_rows() > 0) 
				{ 
					foreach($opt_result->result() as $row)
					{
						$CI->global_lib->remove_language_country_option($row->option_key,$elem);
					}
				}
			}
			
			
			if($elem_type == 'state')
			{
				$countries = $locations['countries'];
				foreach($countries as $c_key => $c_vals)
				{
					if(array_key_exists("states",$c_vals))
					{
						foreach($c_vals['states'] as $s_key => $s_vals)
						{
							if($id != 0 && $id== $s_vals['state_id'])
							{
								unset($locations['countries'][$c_key]['states'][$s_key]);
							}
							else if(isset($elem) && $s_key == $elem){
								unset($locations['countries'][$c_key]['states'][$s_key]);
							}
						}
					}	
				}
			}
			
			if($elem_type == 'city')
			{
				$countries = $locations['countries'];
				foreach($countries as $c_key => $c_vals){
					if(array_key_exists("states",$c_vals))
					{
						foreach($c_vals['states'] as $s_key => $s_vals){
							if(array_key_exists("cities",$s_vals))
							{		
								foreach($s_vals['cities'] as $ct_key => $ct_vals){
							
									if($id== $ct_vals['city_id'])
									{
										unset($locations['countries'][$c_key]
														['states'][$s_key]
														['cities'][$ct_key]);
									}	
								}
							}	
						}
					}	
				}
				
			}
			
			if($elem_type == 'zipcodes' || $elem_type == 'sub_areas')
			{
				$countries = $locations['countries'];
				foreach($countries as $c_key => $c_vals){
					if(array_key_exists("states",$c_vals))
					{
						foreach($c_vals['states'] as $s_key => $s_vals){
							if(array_key_exists("cities",$s_vals))
							{		
								foreach($s_vals['cities'] as $ct_key => $ct_vals)
								{
									if(isset($ct_vals[$elem_type]))
									{
										if(array_key_exists($elem,$ct_vals[$elem_type]) && in_array($id,$ct_vals[$elem_type]))
										{
											unset($locations['countries'][$c_key]
														['states'][$s_key]
														['cities'][$ct_key]
														[$elem_type][$elem]);
										}
									}	
								}
							}	
						}
					}	
				}
				
			}
			
			
			$locations = json_encode($locations);
			$CI->global_lib->update_option('locations',$locations);
			
			die("success");
		}
		
	}	
	
	public function reset_location_meta_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		
		$CI->global_lib->clear_option('locations');
		
		$output = '
					<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						'.mlx_get_lang("Location Hierarchy Reset Successfully").'
					</div>
					';
		header('Content-type: application/json');			
		echo json_encode(array('output' => $output));
	}
	
	public function get_states_from_countries()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		
		if(isset($country) && !empty($country))
		{
			if(file_exists("locations/json/state/$country.json"))
			{
				$state_list = file_get_contents("locations/json/state/$country.json");
				
				
				if(!empty($state_list))
				{
					$states_str = '<option value="">'.mlx_get_lang("Select State").'</option>';
					$state_array = json_decode($state_list, true);
					
					foreach($state_array as $state)
					{
						$states_str .= '<option value="'.$state['state_code'].'~'.$state['name'].'~'.$state['id'].'">'.$state['name'].'</option>';
					}
					echo $states_str; return;
				}
			}
		}
		
		echo "state not found"; return;
	}
	
	public function get_cities_from_states(){
		
		extract($_POST);		
		$CI =& get_instance();	
		
		if(isset($country_code) && !empty($country_code))
		{
			if(file_exists("locations/json/city/$country_code/$state_id.json"))
			{
				$city_list = file_get_contents("locations/json/city/$country_code/$state_id.json");
				
				
				if(!empty($city_list))
				{
					$city_str = '';
					$city_array = json_decode($city_list, true);
					foreach($city_array as $city)
					{
						$city_str .= '<option value="'.$city['country_code'].'~'.$city['state_code'].'~'.$city['id'].'~'.$city['name'].'~'.$city['id'].'">'.$city['name'].'</option>';
					}
					echo $city_str; return;
				}
			}
		}
		
		echo "cities not found"; return;
	}
	
	public function get_zipcode_subarea_from_cities(){
		
		extract($_POST);		
		$CI =& get_instance();	
		
		$zipcode_list = '<option value="">Select Any Zipcode</option>';
		$sub_area_list = '<option value="">Select Any Sub Area</option>';
		
		if(isset($country_code) && !empty($country_code) && 
		isset($state_id) && !empty($state_id) && 
		isset($city_id) && !empty($city_id))
		{
			$locations = $CI->global_lib->get_option('locations');
			if(!empty($locations))
			{
				
				$locations = json_decode($locations, true);
				
				if(isset($locations['countries'][$country_code]['states'][$state_id]['cities'][$city_id]['zipcodes']))
				{
					foreach($locations['countries'][$country_code]['states'][$state_id]['cities'][$city_id]['zipcodes'] as $zipcode)
					{
						$zipcode_list .= '<option value="'.$zipcode.'">'.$zipcode.'</option>';
					}
				}
				if(isset($locations['countries'][$country_code]['states'][$state_id]['cities'][$city_id]['sub_areas']))
				{
					foreach($locations['countries'][$country_code]['states'][$state_id]['cities'][$city_id]['sub_areas'] as $subarea)
					{
						$sub_area_list .= '<option value="'.$subarea.'">'.$subarea.'</option>';
					}
				}
			}
		}
		
		header('Content-type: application/json');				
		echo json_encode(array('zipcode_list'=> $zipcode_list,'sub_area_list' => $sub_area_list));
		
	}
	
	public function get_cities_from_countries(){
		
		extract($_POST);		
		$CI =& get_instance();	
		
		if(isset($country_code) && !empty($country_code))
		{
			if(file_exists("locations/json/city/$country_code/all.json"))
			{
				$city_list = file_get_contents("locations/json/city/$country_code/all.json");
				
				if(!empty($city_list))
				{
					$city_str = '';
					$city_array = json_decode($city_list, true);
					asort($city_array);
					
					foreach($city_array as $city)
					{
						$city_str .= '<option value="'.$city['country_code'].'~'.$city['state_code'].'~'.$city['id'].'~'.$city['name'].'~'.$city['id'].'">'.$city['name'].'</option>';
					}
					echo $city_str; return;
				}
			}
		}
		
		echo "cities not found"; return;
	}
	
	public function get_current_language_list_callback_func()
	{
		$CI =& get_instance();	
		extract($_POST);
		$output = '';
		$site_language = $CI->global_lib->get_option('site_language');
		$default_language = $CI->global_lib->get_option('default_language');
		if(!empty($site_language))
		{
			$site_language_array = json_decode($site_language,true);
			
			foreach($site_language_array as $aak=>$aav)
			{
				if($aav['language'] == $default_language)
				{
					$new_value = $site_language_array[$aak];
					unset($site_language_array[$aak]);
					array_unshift($site_language_array, $new_value);
					break;
				}
			}
			
			foreach($site_language_array as $slak=>$slav)
			{
				$langExp = explode('~',$slav['language']);
				$lang_name = $langExp[0];
				$lang_code = $langExp[1];
				$language = $slav['language'];
				
				$checked_str = '';
				$lc_val = $CI->global_lib->get_option('language_country_'.$lang_code);
				if(!empty($lc_val))
				{
					$exp_lc_val = explode(',',$lc_val);
					if(in_array($country_code,$exp_lc_val))
						$checked_str = ' checked="checked" ';
				}
				
				$output .= '<div><label><input '.$checked_str.' type="checkbox" name="language_list[]" class="minimal" value="'.$lang_code.'"> &nbsp;&nbsp;'.$lang_name.'</label></div>';
			}
		}
		echo $output;
	}
	
	public function update_location_language_callback_func()
	{
		extract($_POST);
		$CI =& get_instance();	
		$site_language = $CI->global_lib->get_option('site_language');
		if(!empty($site_language))
		{
			$site_language_array = json_decode($site_language,true);
			foreach($site_language_array as $slak=>$slav)
			{
				$langExp = explode('~',$slav['language']);
				$lang_name = $langExp[0];
				$lang_code = $langExp[1];
				
				if(!empty($language_list) && !in_array($lang_code,$language_list))
				{
					$CI->global_lib->remove_language_country_option('language_country_'.$lang_code,$country_code);
				}
			}
		}
		
		$locations = $CI->global_lib->get_option('locations');
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
			if(isset($location_array['countries'][$country_code]))
			{
				$location_array['countries'][$country_code]['settings']['languages'] = $language_list; 
			}
			$CI->global_lib->update_option('locations',json_encode($location_array));
			
			if(!empty($language_list))
			{
				foreach($language_list as $lang)
				{
					$CI->global_lib->update_language_country_option('language_country_'.$lang,$country_code);
				}
			}
		}
	}
	
	public function get_state_city_name_list_callback_func()
	{
		extract($_POST);		
		$CI =& get_instance();	
		$state_list = '<option value="">'.mlx_get_lang("Select Any State").'</option>';
		$city_list = '<option value="">'.mlx_get_lang("Select Any City").'</option>';
		
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$locations = $CI->global_lib->get_option('locations');
		
		$is_state_enable = false;
		$is_city_enable = false;
		if(!empty($loc_tax_settings))
		{
			$loc_tax_setting_array = json_decode($loc_tax_settings,true);
			
			if(isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
			
			if(isset($loc_tax_setting_array['city']['enabled']) && $loc_tax_setting_array['city']['enabled'] == true)
				$is_city_enable = true;
				
		}
		
		if(!empty($locations) && isset($country_code))
		{
			$location_array = json_decode($locations,true);
			
			if(isset($location_array['countries'][$country_code]['states']) && $is_state_enable)
			{
				$states = $location_array['countries'][$country_code]['states'];
				if(!empty($states))
				{
					foreach($states as $ck=>$cv)
					{
						if($ck == 'no_state')
							continue;
						$state_list .= '<option data-country_code="'.$country_code.'" 
												data-state_code="'.$ck.'" 
												data-full_value="'.$cv['loc_title'].'" 
												value="'.mlx_get_norm_string($cv['loc_title']).'">'.ucfirst($cv['loc_title']).'</option>';
					}
					
				}
					
			}
			else if($is_city_enable && isset($location_array['countries'][$country_code]))
			{
				if(isset($location_array['countries'][$country_code]['states']['no_state']['cities']) && 
				$is_city_enable)
				{
					foreach($location_array['countries'][$country_code]['states']['no_state']['cities'] as $skey=>$sval)
					{
						$city_list .= '<option data-country_code="'.$country_code.'" 
											   data-state_code="no_state" 
											   data-city_code="'.$skey.'" 
											   data-full_value="'.$sval['loc_title'].'" 
											   value="'.mlx_get_norm_string($sval['loc_title']).'"';
						$city_list .= '>'.$sval['loc_title'].'</option>';
					}
				}
			}
		}
		
		header('Content-type: application/json');			
		echo json_encode(array('state_list' => $state_list, 'city_list' => $city_list));
	}
	
	public function get_city_name_list_callback_func(){
		
		extract($_POST);		
		$CI =& get_instance();	
		$cities_list = '<option value="">Select Any City</option>';
		
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$locations = $CI->global_lib->get_option('locations');
		
		if(!empty($locations) && isset($country_code) && isset($state_code))
		{
			$location_array = json_decode($locations,true);
			if(isset($location_array['countries'][$country_code]['states'][$state_code]['cities']))
			{
				$cities = $location_array['countries'][$country_code]['states'][$state_code]['cities'];
				if(!empty($cities))
				{
					foreach($cities as $ck=>$cv)
					{
						$cities_list .= '<option data-country_code="'.$country_code.'" data-state_code="'.$state_code.'" data-city_code="'.$ck.'" data-full_value="'.$cv['loc_title'].'" value="'.mlx_get_norm_string($cv['loc_title']).'">'.ucfirst($cv['loc_title']).'</option>';
					}
					
				}
					
			}
		}
		echo $cities_list; exit;
		
	}
	
	
	public function get_zip_sub_area_name_list_callback_func()
	{
		extract($_POST);		
		$CI =& get_instance();	
		$zipcode_list = '<option value="">Select Any Zipcode</option>';
		$subarea_list = '<option value="">Select Any Sub Area</option>';
		
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$locations = $CI->global_lib->get_option('locations');
		
		$is_city_enable = false;
		$is_zipcode_enable = false;
		$is_subarea_enable = false;
		if(!empty($loc_tax_settings))
		{
			$loc_tax_setting_array = json_decode($loc_tax_settings,true);
			
			if(isset($loc_tax_setting_array['city']['enabled']) && $loc_tax_setting_array['city']['enabled'] == true)
				$is_city_enable = true;
				
			if(isset($loc_tax_setting_array['zipcode']['enabled']) && $loc_tax_setting_array['zipcode']['enabled'] == true)
				$is_zipcode_enable = true;
				
			if(isset($loc_tax_setting_array['sub-area']['enabled']) && $loc_tax_setting_array['sub-area']['enabled'] == true)
				$is_subarea_enable = true;
		}
		
		if(!empty($locations) && isset($country_code) && isset($state_code) && isset($city_code) && $is_city_enable)
		{
			$location_array = json_decode($locations,true);
			if(isset($location_array['countries'][$country_code]['states'][$state_code]['cities'][$city_code]['zipcodes']) && $is_zipcode_enable)
			{
				$zipcodes = $location_array['countries'][$country_code]['states'][$state_code]['cities'][$city_code]['zipcodes'];
				if(!empty($zipcodes))
				{
					foreach($zipcodes as $ck=>$cv)
					{
						$zipcode_list .= '<option value="'.$cv.'">'.$cv.'</option>';
					}
				}
			}
			
			if(isset($location_array['countries'][$country_code]['states'][$state_code]['cities'][$city_code]['sub_areas']) && $is_subarea_enable)
			{
				$sub_areas = $location_array['countries'][$country_code]['states'][$state_code]['cities'][$city_code]['sub_areas'];
				if(!empty($sub_areas))
				{
					foreach($sub_areas as $ck=>$cv)
					{
						$subarea_list .= '<option value="'.$cv.'">'.$cv.'</option>';
					}
				}
			}
		}
		
		header('Content-type: application/json');			
		echo json_encode(array('zipcode_list' => $zipcode_list,'subarea_list' => $subarea_list));
	}
	
	public function get_location_language_list_callback_func()
	{
		extract($_POST);		
		$CI =& get_instance();	
		$output = '';
		$default_language = $CI->global_lib->get_option('default_language');
		$keywords = array();
		$locations = $CI->global_lib->get_option('locations');
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
			
			if(isset($location_array['countries']) && !empty($location_array['countries']))
			{
				foreach($location_array['countries'] as $lk=>$lv)
				{
					$keywords[] = $lv['loc_title'];
					if(isset($lv['states']) && !empty($lv['states']))
					{
						foreach($lv['states'] as $sk=>$sv)
						{
							$keywords[] = $sv['loc_title'];
							if(isset($sv['cities']) && !empty($sv['cities']))
							{
								foreach($sv['cities'] as $ck=>$cv)
								{
									$keywords[] = $cv['loc_title'];
									if(isset($cv['zipcodes']) && !empty($cv['zipcodes']))
									{
										foreach($cv['zipcodes'] as $zck=>$zcv)
										{
											if(!is_numeric($zcv))
												$keywords[] = $zcv;
										}
									}
									
									if(isset($cv['sub_areas']) && !empty($cv['sub_areas']))
									{
										foreach($cv['sub_areas'] as $sak=>$sav)
										{
											$keywords[] = $sav;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
		$site_language = $CI->global_lib->get_option('site_language');
		if(count($keywords) > 0 && isset($site_language) && !empty($site_language))
		{
			$site_language_array = json_decode($site_language,true);
			
			foreach($site_language_array as $aak=>$aav)
			{
				if($aav['language'] == $default_language)
				{
					$new_value = $site_language_array[$aak];
					unset($site_language_array[$aak]);
					array_unshift($site_language_array, $new_value);
					break;
				}
			}
			
			$output .= '<div class="nav-tabs-custom ">';
			$output .= '<ul class="nav nav-tabs ">';
			$n=0;
			foreach($site_language_array as $k=>$v) 
			{ 
				$n++; 
				$lang_exp = explode('~',$v['language']);
				$lang_code = $lang_exp[1];
				$lang_title = $lang_exp[0];
				$aClass = '';
				if($n == 1) 
					$aClass = 'active';
				$output .= '<li class="'.$aClass.'"><a href="#'.$lang_code.'" data-toggle="tab">'.ucfirst(mlx_get_lang($lang_title)).'</a></li>';
			}
					  
			$output .= '</ul><div class="tab-content row">';
			$n=0;
			foreach($site_language_array as $k=>$v) { 
				$n++; 
				$lang_exp = explode('~',$v['language']);
				$lang_code = $lang_exp[1];
				$lang_title = $lang_exp[0];
				
				$lang_slug = $CI->global_lib->get_slug($lang_title,'_');
				$tp_class = '';
				if($n == 1) 
					$tp_class = 'active'; 
				$output .= '<div class="'.$tp_class.' tab-pane col-md-12" id="'.$lang_code.'">';
				foreach($keywords as $keyword)
				{
					$keyword_val = '';
					
					$keyword_result = $CI->Common_model->commonQuery("select keyword,lang_id,$lang_slug from languages where lang_for = 'front'
					and keyword = '".mlx_get_norm_string($keyword)."'");
					if($keyword_result->num_rows() > 0) { 
						$keyword_val = $keyword_result->row()->$lang_slug;
					}
					else if($lang_slug == 'english')
						$keyword_val = $keyword;
					
					$output .= '<div class="form-group row">
								<label for="'.$keyword.'_'.$lang_code.'" class="col-sm-3 control-label">'.ucfirst($keyword).'</label>
								<div class="col-sm-9">
									<input type="text" value="'.$keyword_val.'" class="form-control" 
									name="loc_keywords['.str_replace(' ','_',mlx_get_norm_string($keyword)).']['.$lang_slug.']"
									id="'.$keyword.'_'.$lang_code.'">
								</div>
							  </div>';
				}
				$output .= '</div>';
				
			}
			$output .= '</div></div>';
			
			
		}
		else
		{
			$output = mlx_get_lang('No Keyword Currently Available');
		}
		
		header('Content-type: application/json');			
		echo json_encode(array('output' => $output));
		
	}
	
	public function update_location_lang_callback_func()
	{
		extract($_POST);
		$CI =& get_instance();	
		foreach($_POST as $k=>$v)
		{
			/*$_POST[$k] = $this->security->xss_clean($v);*/
			$_POST[$k] = str_replace('[removed]','',$_POST[$k]);
		}
		
		
		if(isset($loc_keywords) && !empty($loc_keywords))
		{
			$lang_list = array();
			
			foreach($loc_keywords as $keyword=>$lang_list)
			{
				$datai = array();
				$keyword = str_replace('_',' ',$keyword);
				
				foreach($lang_list as $lk=>$lv)
				{
					$datai[$lk] = addslashes($lv);
					$lang_list[$lk] = $lk;
				}
				
				$keyword_result = $CI->Common_model->commonQuery("select keyword,lang_id from languages where lang_for = 'front'
				and keyword = '$keyword'");
				if($keyword_result->num_rows() > 0) { 
					$lang_id = $keyword_result->row()->lang_id;
					$CI->Common_model->commonUpdate('languages',$datai,'lang_id',$lang_id);
				}
				else
				{
					$datai['keyword'] = $keyword;
					$datai['lang_for'] = 'front';
					$CI->Common_model->commonInsert('languages',$datai);
				}
				
				$keyword_result = $CI->Common_model->commonQuery("select keyword,lang_id from languages where lang_for = 'back'
				and keyword = '$keyword'");
				if($keyword_result->num_rows() > 0) { 
					$lang_id = $keyword_result->row()->lang_id;
					$CI->Common_model->commonUpdate('languages',$datai,'lang_id',$lang_id);
				}
				else
				{
					$datai['keyword'] = $keyword;
					$datai['lang_for'] = 'back';
					$CI->Common_model->commonInsert('languages',$datai);
				}
			}
			foreach($lang_list as $llk => $llv)
			{
				$lang_slug = $llv;
				/*front*/
				
				if(!is_dir("application/language"))
				{
					mkdir("application/language",0777);
				}
							
				if(!is_dir("application/language/$lang_slug"))
				{
					mkdir("application/language/$lang_slug",0777);
				}
				if(file_exists("application/language/$lang_slug/".$lang_slug."_lang.php"))
				{
					if($lang_slug!='english')
					unlink("application/language/$lang_slug/".$lang_slug."_lang.php");
				}
				
				$fp = fopen("application/language/$lang_slug/".$lang_slug."_lang.php","wb");
				if($fp)
				{
					$output = "<?php \n\n";
					$keyword_result = $CI->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'front'
										order by lang_id DESC");
				   if($keyword_result->num_rows() > 0) 
				   { 
						foreach($keyword_result->result() as $row)
						{
							//$output .= '$lang["'.$row->keyword.'"] = "'.$row->$lang_slug.'";'."\n";
							$output .= '$lang'."['".$row->keyword."'] = '".addslashes($row->$lang_slug)."';\n";
						}
				   }
					fwrite($fp,$output);
					fclose($fp);
				}
				
				/*back*/
				
				if(!is_dir("application/language"))
				{
					mkdir("application/language",0777);
				}
							
				if(!is_dir("application/language/$lang_slug"))
				{
					mkdir("application/language/$lang_slug",0777);
				}
				if(file_exists("application/language/$lang_slug/".$lang_slug."_lang.php"))
				{
					if($lang_slug!='english')
					unlink("application/language/$lang_slug/".$lang_slug."_lang.php");
				}
				
				$fp = fopen("application/language/$lang_slug/".$lang_slug."_lang.php","wb");
				if($fp)
				{
					$output = "<?php \n\n";
					$keyword_result = $CI->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'back'
										order by lang_id DESC");
				   if($keyword_result->num_rows() > 0) 
				   { 
						foreach($keyword_result->result() as $row)
						{
							//$output .= '$lang["'.$row->keyword.'"] = "'.$row->$lang_slug.'";'."\n";
							$output .= '$lang'."['".$row->keyword."'] = '".addslashes($row->$lang_slug)."';\n";
						}
				   }
					fwrite($fp,$output);
					fclose($fp);
				}
			
			}
		}
		
		$output = '
					<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						'.mlx_get_lang("Location Language Updated Successfully").'
					</div>
					';
		header('Content-type: application/json');			
		echo json_encode(array('output' => $output));
	}

	public function generate_state_id_callback_func(){
		
		extract($_POST);		
		$CI =& get_instance();	
		
		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
		$state_id = 'ST-'.substr(str_shuffle($str_result),0, 8); 
		
		header('Content-type: application/json');				
		echo json_encode(array('state_id'=> $state_id));
		
	}
	
	public function generate_city_id_callback_func(){
		
		extract($_POST);		
		$CI =& get_instance();	
		
		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
		$city_id = 'CT-'.substr(str_shuffle($str_result),0, 8); 
		
		header('Content-type: application/json');				
		echo json_encode(array('city_id'=> $city_id));
		
	}
	
	
	public function add_custom_state_callback_func()
	{
		extract($_POST);		
		$CI =& get_instance();	
		
		$locations = $CI->global_lib->get_option('locations');
		$locations = json_decode($locations,true);
		if(empty($locations))
		{
			$locations = array();
		}	
		
		if(isset($country_id) && !empty($country_id))
		{
			$loc_code = $state_code;
			$loc_title = $state_title;
			$loc_id = $state_id;
			
			if(!isset($locations['countries'] [$country_id]['states'][$loc_code]))
			{
				$locations['countries'] [$country_id]['states'][$loc_code] = 
					array(  "loc_title" => $loc_title , 
						"loc_type" => "state"  ,
						"state_id" => $loc_id,
						"state_type" => 'custom',
						);
			}
		}
		
		$locations = json_encode($locations);
		$CI->global_lib->update_option('locations',$locations);
		
	}
	
	public function add_custom_city_callback_func()
	{
		extract($_POST);		
		$CI =& get_instance();	
		
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$loc_tax_settings = json_decode($loc_tax_settings,true);
		
		$locations = $CI->global_lib->get_option('locations');
		$locations = json_decode($locations,true);
		if(empty($locations))
		{
			$locations = array();
		}	
		
		
		if($loc_tax_settings['state']['enabled'])
		{
			$cExp = explode('~',$state_id);
			$country_code = $country_id;
			$state_code = $cExp[0];
			
			$loc_id = $city_code;
			$loc_title = $city_title;
			$loc_code = $city_id;
			
			if(!isset($locations['countries'] [$country_code]['states'][$state_code] ['cities'][$loc_code]))
			{
			$locations['countries'] [$country_code]['states'][$state_code] ['cities'][$loc_code] = 
				array("loc_title" => $loc_title , 
						"loc_type" => "city"  ,
						"city_id" => $loc_code,
						"city_code" => $loc_id
						);
			}
		}
		else
		{
			
			$country_code = $country_id;
			
			$loc_id = $city_code;
			$loc_title = $city_title;
			$loc_code = $city_id;
			
			$cities_array[$loc_code] =  array(	"loc_title" => $loc_title , 
												"loc_type" 	=> "city"  ,
												"city_id" => $loc_code,
												"city_code" => $loc_id
											);
			
			
			if(isset($locations['countries'] [$country_code]['states']['no_state']['cities']) && 
				!empty($locations['countries'] [$country_code]['states']['no_state']['cities']))
			{
				$res = $locations['countries'] [$country_code]['states']['no_state']['cities'] + $cities_array;
				$locations['countries'] [$country_code]['states']['no_state']['cities'] = $res;
			}
			else
			{
				$locations['countries'] [$country_code]['states']['no_state'] = array('loc_title' => 'No State',
																					  'loc_type' => 'state',
																					  'state_id' => '0',
																					  'cities' => $cities_array
																					  );
			}
			
		}
		
		$locations = json_encode($locations);
		$CI->global_lib->update_option('locations',$locations);
		
	}
	

	
}
