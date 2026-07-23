<?php 



function homepage_properties_section_search_where_fields_callback($settings = array(), $where_lang_cond="" , $where_cond = "")
{
    $CI = &get_instance();
	
	
	$def_lang_code = $CI->default_language;
	if(isset($settings['property_for_lang']) &&  $settings['property_for_lang'] != 'all')
	{
		$where_lang_cond = " and pld.language = '$def_lang_code' ";
		
		$locations = $CI->global_lib->get_option('locations');
		$location_array = json_decode($locations,true);
		
		$country_code = $settings['property_country'];
		if(!empty($country_code) && $country_code != 'all')
		{
			$exp_country_code = explode('~',$country_code);
			if(!empty($exp_country_code))
			{
				
				$where_cond .= " and pld.country = '".$exp_country_code[0]."' ";
			}	
		}
			
	}	
	if(isset($settings['property_state']) && !empty($settings['property_state']) && $settings['property_state'] != 'all')
	{
		$pse = explode('~',$settings['property_state']);
		$where_cond .= " AND prop.state = '".$pse[0]."' ";
	}
	if(isset($settings['property_city']) && !empty($settings['property_city']) && $settings['property_city'] != 'all')
	{
		$pse = explode('~',$settings['property_city']);
		$where_cond .= " AND prop.city = '".$pse[0]."' ";
	}
	if(isset($settings['property_zipcode']) && !empty($settings['property_zipcode']) && $settings['property_zipcode'] != 'all')
	{
		$where_cond .= " AND prop.zip_code = '".$settings['property_zipcode']."' ";
	}
	if(isset($settings['property_sub_area']) && !empty($settings['property_sub_area']) && $settings['property_sub_area'] != 'all')
	{
		$where_cond .= " AND prop.sub_area = '".$settings['property_sub_area']."' ";
	}
	
	$where = array("where_lang_cond" =>	$where_lang_cond, "where_cond" =>	$where_cond);
	return $where;
    
}
add_filter('dynamic_properties_section_search_where_fields', 'homepage_properties_section_search_where_fields_callback', 10, 3);/**/



add_filter("homepage_sections_get_section_field", "homepage_sections_get_section_field_dynamic_country");
function homepage_sections_get_section_field_dynamic_country($section_field = ""){



	$CI =  &get_instance();
	
	if($section_field == 'dynamic_location_country')
	{	
	
		$locations = $CI->global_lib->get_option('locations');
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
		}
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		$is_city_enable = false;
		$is_zipcode_enable = false;
		$is_subarea_enable = false;
		if(!empty($loc_tax_settings))
		{
			$loc_tax_setting_array = json_decode($loc_tax_settings,true);
			
			if(isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
			
			if(isset($loc_tax_setting_array['city']['enabled']) && $loc_tax_setting_array['city']['enabled'] == true)
				$is_city_enable = true;
				
			if(isset($loc_tax_setting_array['zipcode']['enabled']) && $loc_tax_setting_array['zipcode']['enabled'] == true)
				$is_zipcode_enable = true;
				
			if(isset($loc_tax_setting_array['sub-area']['enabled']) && $loc_tax_setting_array['sub-area']['enabled'] == true)
				$is_subarea_enable = true;
		}
	
	
	
		if(!property_exists($CI , "homepage_sections_fields")) 
		{	
			$CI->homepage_sections_fields = $homepage_sections_fields = array();
		}else{
			$homepage_sections_fields = $CI->homepage_sections_fields;	
		}
		
		if(!array_key_exists($section_field , $homepage_sections_fields)){
			
			$property_country_option = array();
			$property_country_option['all'] = 'All Countries';
			
			if(isset($location_array['countries']) && count($location_array['countries']) > 0){
				foreach($location_array['countries'] as $country_code => $options)
				{
					$country_name = $options['loc_title'];
					
					$property_country_option[mlx_get_norm_string($country_name).'~'.$country_code] = array(
						'title' => $country_name, 'attributes' => array( 'country_code' => $country_code,),	  );
				}	
			}
			
			$single_field_options = $property_country_option;
			$homepage_sections_fields[$section_field] = $single_field_options;
			
		
		}else{
				$single_field_options = $homepage_sections_fields[$section_field];
			 }
		
			$CI->homepage_sections_fields =  $homepage_sections_fields;
			return $single_field_options;
		
		

	}else return false;		

}


add_filter("homepage_sections_get_section_field", "homepage_sections_get_section_field_dynamic_state");
function homepage_sections_get_section_field_dynamic_state($section_field = "" , $meta_content = array()){



	$CI =  &get_instance();
	
	if($section_field == 'dynamic_location_state')
	{	
	
		
		
		$locations = $CI->global_lib->get_option('locations');
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
		}
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		$is_city_enable = false;
		$is_zipcode_enable = false;
		$is_subarea_enable = false;
		if(!empty($loc_tax_settings))
		{
			$loc_tax_setting_array = json_decode($loc_tax_settings,true);
			
			if(isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
			
			if(isset($loc_tax_setting_array['city']['enabled']) && $loc_tax_setting_array['city']['enabled'] == true)
				$is_city_enable = true;
				
			if(isset($loc_tax_setting_array['zipcode']['enabled']) && $loc_tax_setting_array['zipcode']['enabled'] == true)
				$is_zipcode_enable = true;
				
			if(isset($loc_tax_setting_array['sub-area']['enabled']) && $loc_tax_setting_array['sub-area']['enabled'] == true)
				$is_subarea_enable = true;
		}
	
	
	
		if(!property_exists($CI , "homepage_sections_fields")) 
		{	
			$CI->homepage_sections_fields = $homepage_sections_fields = array();
		}else{
			$homepage_sections_fields = $CI->homepage_sections_fields;	
		}
			
	
	
	

		$state_code = 'all';
		$property_state_option = array();
		$property_state_option['all'] = 'All States';
		
		if(!array_key_exists($section_field , $homepage_sections_fields)){
		
		if(	isset($meta_content['property_country']) && $meta_content['property_country'] != 'all')
		{
			
			$pc = $meta_content['property_country'];
			$pce = explode('~',$pc);
			$cc = $pce[1];
			$cn = $pce[0];
			
			if(!empty($cc) && isset($location_array['countries'][$cc]))
			{
				$country_name = $location_array['countries'][$cc]['loc_title'];
				
				if(isset($location_array['countries'][$cc]['states']))
				{
					
					foreach($location_array['countries'][$cc]['states'] as $skey=>$sval)
					{
						if($skey != 'no_state')
						{
							$property_state_option[mlx_get_norm_string($sval['loc_title']).'~'.$skey] = 
								array(	'title' => $sval['loc_title'],	'attributes' => array(	'country_code' => $cc,	'state_code' => $skey, ),  );
							
						}
					}
				}
			}
			
			
		}
		
			$single_field_options = $property_state_option;
			$homepage_sections_fields[$section_field] = $single_field_options;
			
		
		}else{
				$single_field_options = $homepage_sections_fields[$section_field];
			 }
		
			$CI->homepage_sections_fields =  $homepage_sections_fields;
			return $single_field_options;

	
	}else return false;

}









add_filter("homepage_sections_get_section_field", "homepage_sections_get_section_field_dynamic_city");
function homepage_sections_get_section_field_dynamic_city($section_field = "", $meta_content = array()){



	$CI =  &get_instance();
	
	if($section_field == 'dynamic_location_city')
	{	
	
	
		$locations = $CI->global_lib->get_option('locations');
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
		}
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		$is_city_enable = false;
		$is_zipcode_enable = false;
		$is_subarea_enable = false;
		if(!empty($loc_tax_settings))
		{
			$loc_tax_setting_array = json_decode($loc_tax_settings,true);
			
			if(isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
			
			if(isset($loc_tax_setting_array['city']['enabled']) && $loc_tax_setting_array['city']['enabled'] == true)
				$is_city_enable = true;
				
			if(isset($loc_tax_setting_array['zipcode']['enabled']) && $loc_tax_setting_array['zipcode']['enabled'] == true)
				$is_zipcode_enable = true;
				
			if(isset($loc_tax_setting_array['sub-area']['enabled']) && $loc_tax_setting_array['sub-area']['enabled'] == true)
				$is_subarea_enable = true;
		}
	
	
	
		if(!property_exists($CI , "homepage_sections_fields")) 
		{	
			$CI->homepage_sections_fields = $homepage_sections_fields = array();
		}else{
			$homepage_sections_fields = $CI->homepage_sections_fields;	
		}
			
	
	
	

		$state_code = 'all';
		$property_city_option = array();
		$property_city_option['all'] = 'All Cities';
		
		if(!array_key_exists($section_field , $homepage_sections_fields)){
		
			
			
			
			$state_code = 'all';
			$property_city_option = array();
			$property_city_option['all'] = 'All Cities';
			if(
			isset($meta_content['property_state']) && !empty($meta_content['property_state']) && $meta_content['property_state'] != 'all' && 
			isset($meta_content['property_country']) && !empty($meta_content['property_country']) && $meta_content['property_country'] != 'all')
			{
				$sc_exp = explode('~',$meta_content['property_state']);
				if(count($sc_exp) > 1)
					$state_code = $sc_exp[1];
				else
					$state_code = $sc_exp[0];
				
				
				$pc = $meta_content['property_country'];
				$pce = explode('~',$pc);
				$lc_val = $cc = $pce[1];
				$cn = $pce[0];
				
				if($is_state_enable)
				{
					if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states'][$state_code]['cities']))
					{
						foreach($location_array['countries'][$lc_val]['states'][$state_code]['cities'] as $skey=>$sval)
						{
							$property_city_option[mlx_get_norm_string($sval['loc_title']).'~'.$skey] = array(
																					'title' => $sval['loc_title'],
																					'attributes' => array(
																											'country_code' => $lc_val,
																											'state_code' => $state_code,
																											'city_code' => $skey
																										 ),
																				  );
								
							
						}
					}
				}
				else
				{
					if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states']['no_state']['cities']))
					{
						foreach($location_array['countries'][$lc_val]['states']['no_state']['cities'] as $skey=>$sval)
						{
							$property_city_option[mlx_get_norm_string($sval['loc_title']).'~'.$skey] = array(
																					'title' => $sval['loc_title'],
																					'attributes' => array(
																											'country_code' => $lc_val,
																											'state_code' => 'no_state',
																											'city_code' => $skey
																										 ),
																				  );
								
							
						}
					}
				}
			}
			else if(			!$is_state_enable && 
					isset($meta_content['property_country']) && 
					!empty($meta_content['property_country']) && $meta_content['property_country'] != 'all')
			{
				
				
				
				
				$pc = $meta_content['property_country'];
				$pce = explode('~',$pc);
				$lc_val = $cc = $pce[1];
				$cn = $pce[0];
				
				
				if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states']['no_state']['cities']))
				{
					foreach($location_array['countries'][$lc_val]['states']['no_state']['cities'] as $skey=>$sval)
					{
						$property_city_option[mlx_get_norm_string($sval['loc_title']).'~'.$skey] = array(
																				'title' => $sval['loc_title'],
																				'attributes' => array(
																										'country_code' => $lc_val,
																										'state_code' => 'no_state',
																										'city_code' => $skey
																									 ),
																			  );
							
						
					}
				}
				
			}
			
		
			$single_field_options = $property_city_option;
			$homepage_sections_fields[$section_field] = $single_field_options;
			
		
		}else{
				$single_field_options = $homepage_sections_fields[$section_field];
			 }
		
		$CI->homepage_sections_fields =  $homepage_sections_fields;
			
			
		return $single_field_options;

	
	}else return false;

}














add_filter("homepage_sections_get_section_field", "homepage_sections_get_section_field_dynamic_zipcode");
function homepage_sections_get_section_field_dynamic_zipcode($section_field = "", $meta_content = array()){



	$CI =  &get_instance();
	
	if($section_field == 'dynamic_location_zipcode')
	{	
	
	
		$locations = $CI->global_lib->get_option('locations');
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
		}
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		$is_city_enable = false;
		$is_zipcode_enable = false;
		$is_subarea_enable = false;
		if(!empty($loc_tax_settings))
		{
			$loc_tax_setting_array = json_decode($loc_tax_settings,true);
			
			if(isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
			
			if(isset($loc_tax_setting_array['city']['enabled']) && $loc_tax_setting_array['city']['enabled'] == true)
				$is_city_enable = true;
				
			if(isset($loc_tax_setting_array['zipcode']['enabled']) && $loc_tax_setting_array['zipcode']['enabled'] == true)
				$is_zipcode_enable = true;
				
			if(isset($loc_tax_setting_array['sub-area']['enabled']) && $loc_tax_setting_array['sub-area']['enabled'] == true)
				$is_subarea_enable = true;
		}
	
	
	
		if(!property_exists($CI , "homepage_sections_fields")) 
		{	
			$CI->homepage_sections_fields = $homepage_sections_fields = array();
		}else{
			$homepage_sections_fields = $CI->homepage_sections_fields;	
		}
			
	
	
	

		$state_code = 'all';
		$property_zipcode_option = array();
		$property_zipcode_option['all'] = 'All Zipcodes';
		
		if(!array_key_exists($section_field , $homepage_sections_fields)){
		
			$property_zipcode_option = array();
			$property_zipcode_option['all'] = 'All Zipcodes';
							
		if(
		isset($meta_content['property_state']) && !empty($meta_content['property_state'])  && $meta_content['property_state'] != 'all' && 
		isset($meta_content['property_city']) && !empty($meta_content['property_city'])  && $meta_content['property_city'] != 'all' && 
		isset($meta_content['property_country']) && !empty($meta_content['property_country']) && $meta_content['property_country'] != 'all')
		{
			
			$sc_exp = explode('~',$meta_content['property_state']);
			if(count($sc_exp) > 1)
				$state_code = $sc_exp[1];
			else
				$state_code = $sc_exp[0];
			
			$cc_exp = explode('~',$meta_content['property_city']);
			if(count($cc_exp) > 1)
				$city_code = $cc_exp[1];
			else
				$city_code = $cc_exp[0];
			
			$pfl = $meta_content['property_for_lang'];
			
			
			$pc = $meta_content['property_country'];
			$pce = explode('~',$pc);
			$lc_val = $cc = $pce[1];
			$cn = $pce[0];
			
			if($is_state_enable)
			{
				if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states'][$state_code]['cities'][$city_code]['zipcodes']))
				{
					foreach($location_array['countries'][$lc_val]['states'][$state_code]['cities'][$city_code]['zipcodes'] as $skey=>$sval)
					{
						$property_zipcode_option[$sval] = $sval;
					}
				}
			}
			else
			{
				if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code]['zipcodes']))
				{
					foreach($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code]['zipcodes'] as $skey=>$sval)
					{
						$property_zipcode_option[$sval] = $sval;
					}
				}
			}
		}
		else if(
			!$is_state_enable && 
			isset($meta_content['property_city']) && !empty($meta_content['property_city'])  && $meta_content['property_city'] != 'all' && 
			isset($meta_content['property_country']) && !empty($meta_content['property_country']) && $meta_content['property_country'] != 'all')
		{
			$cc_exp = explode('~',$meta_content['property_city']);
			if(count($cc_exp) > 1)
				$city_code = $cc_exp[1];
			else
				$city_code = $cc_exp[0];
			
			$pfl = $meta_content['property_for_lang'];
			
			
			$pc = $meta_content['property_country'];
			$pce = explode('~',$pc);
			$lc_val = $cc = $pce[1];
			$cn = $pce[0];
			
			if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code]['zipcodes']))
			{
				foreach($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code]['zipcodes'] as $skey=>$sval)
				{
					$property_zipcode_option[$sval] = $sval;
				}
			}
			
		}
			
			$single_field_options = $property_zipcode_option;
			$homepage_sections_fields[$section_field] = $single_field_options;
			
		
		}else{
			
				$single_field_options = $homepage_sections_fields[$section_field];
			}
		
			$CI->homepage_sections_fields =  $homepage_sections_fields;
			
			
			return $single_field_options;

	
	}else return false;

}





add_filter("homepage_sections_get_section_field", "homepage_sections_get_section_field_dynamic_sub_area");
function homepage_sections_get_section_field_dynamic_sub_area($section_field = "", $meta_content = array()){



	$CI =  &get_instance();
	
	if($section_field == 'dynamic_location_sub_area')
	{	
	
	
		$locations = $CI->global_lib->get_option('locations');
		if(!empty($locations))
		{
			$location_array = json_decode($locations,true);
		}
		$loc_tax_settings = $CI->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		$is_city_enable = false;
		$is_zipcode_enable = false;
		$is_subarea_enable = false;
		if(!empty($loc_tax_settings))
		{
			$loc_tax_setting_array = json_decode($loc_tax_settings,true);
			
			if(isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
			
			if(isset($loc_tax_setting_array['city']['enabled']) && $loc_tax_setting_array['city']['enabled'] == true)
				$is_city_enable = true;
				
			if(isset($loc_tax_setting_array['zipcode']['enabled']) && $loc_tax_setting_array['zipcode']['enabled'] == true)
				$is_zipcode_enable = true;
				
			if(isset($loc_tax_setting_array['sub-area']['enabled']) && $loc_tax_setting_array['sub-area']['enabled'] == true)
				$is_subarea_enable = true;
		}
	
	
	
		if(!property_exists($CI , "homepage_sections_fields")) 
		{	
			$CI->homepage_sections_fields = $homepage_sections_fields = array();
		}else{
			$homepage_sections_fields = $CI->homepage_sections_fields;	
		}
			
	
	
	

		$state_code = 'all';
		$property_subarea_option = array();
		$property_subarea_option['all'] = 'All Subareas';
		
		if(!array_key_exists($section_field , $homepage_sections_fields)){
		
		$property_subarea_option = array();
		$property_subarea_option['all'] = 'All Subareas';
		
		if(
		isset($meta_content['property_state']) && !empty($meta_content['property_state'])  && $meta_content['property_state'] != 'all' && 
		isset($meta_content['property_city']) && !empty($meta_content['property_city'])  && $meta_content['property_city'] != 'all' && 
		isset($meta_content['property_country']) && !empty($meta_content['property_country']) && $meta_content['property_country'] != 'all')
		{
			
			$sc_exp = explode('~',$meta_content['property_state']);
			if(count($sc_exp) > 1)
				$state_code = $sc_exp[1];
			else
				$state_code = $sc_exp[0];
			
			$cc_exp = explode('~',$meta_content['property_city']);
			if(count($cc_exp) > 1)
				$city_code = $cc_exp[1];
			else
				$city_code = $cc_exp[0];
			
			
			
			$pc = $meta_content['property_country'];
			$pce = explode('~',$pc);
			$lc_val = $cc = $pce[1];
			$cn = $pce[0];
			
			if($is_state_enable)
			{
				if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states'][$state_code]['cities'][$city_code]['sub_areas']))
				{
					foreach($location_array['countries'][$lc_val]['states'][$state_code]['cities'][$city_code]['sub_areas'] as $skey=>$sval)
					{
						$property_subarea_option[$sval] = $sval;
					}
				}
			}
			else
			{
				if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code]['sub_areas']))
				{
					foreach($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code]['sub_areas'] as $skey=>$sval)
					{
						$property_subarea_option[$sval] = $sval;
					}
				}
			}
		}
		if(
			!$is_state_enable && 
			isset($meta_content['property_city']) && !empty($meta_content['property_city'])  && $meta_content['property_city'] != 'all' && 
			isset($meta_content['property_country']) && !empty($meta_content['property_country']) && $meta_content['property_country'] != 'all')
		{
			
			
			$cc_exp = explode('~',$meta_content['property_city']);
			if(count($cc_exp) > 1)
				$city_code = $cc_exp[1];
			else
				$city_code = $cc_exp[0];
			
			
			
			$pc = $meta_content['property_country'];
			$pce = explode('~',$pc);
			$lc_val = $cc = $pce[1];
			$cn = $pce[0];
			
			
			if(!empty($lc_val) && isset($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code]['sub_areas']))
			{
				foreach($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code]['sub_areas'] as $skey=>$sval)
				{
					$property_subarea_option[$sval] = $sval;
				}
			}
			
		}
							
		
			$single_field_options = $property_subarea_option;
			$homepage_sections_fields[$section_field] = $single_field_options;
			
		
		}else{
				$single_field_options = $homepage_sections_fields[$section_field];
			}
		
			$CI->homepage_sections_fields =  $homepage_sections_fields;
			
			
			return $single_field_options;

	
	}else return false;

}



