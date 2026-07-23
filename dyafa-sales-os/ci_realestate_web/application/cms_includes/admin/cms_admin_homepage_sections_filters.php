<?php




add_filter("homepage_sections_get_section_field", "homepage_sections_get_section_field_dynamic_property_type");

function homepage_sections_get_section_field_dynamic_property_type($section_field = ""){
	
	$CI =  &get_instance();
	
	/*echo $section_field . " DPT";*/
	if($section_field == 'dynamic_property_type')
	{	
		if(!property_exists($CI , "homepage_sections_fields")) 
		{	
			$CI->homepage_sections_fields = $homepage_sections_fields = array();
		}else{
			$homepage_sections_fields = $CI->homepage_sections_fields;	
		}
		
		if(!array_key_exists($section_field , $homepage_sections_fields)){
			
			
			$property_type_option = array();
			$property_type_option['all'] = 'All Types';
			$property_type_result = $CI->Common_model->commonQuery("select * from property_types where status = 'Y' order by title ASC");
			if($property_type_result->num_rows() > 0)
			{
				foreach($property_type_result->result() as $pt_row)
				{
					$property_type_option[$pt_row->pt_id] = $pt_row->title;
				}
			}
			
			$single_field_options = $property_type_option;
			$homepage_sections_fields[$section_field] = $single_field_options;
		}else{
			
			$single_field_options = $homepage_sections_fields[$section_field];
		}
		
		$CI->homepage_sections_fields =  $homepage_sections_fields;
		
		return $single_field_options;
		
	}else
		return false;
		
}
/*
add_filter("homepage_sections_get_section_field", "homepage_sections_get_section_field_dynamic_property_for_lang");
function homepage_sections_get_section_field_dynamic_property_for_lang($section_field = "")
*/

add_filter("homepage_sections_get_section_field", "homepage_sections_get_section_field_dynamic_site_language");
function homepage_sections_get_section_field_dynamic_site_language($section_field = "")

{
	
	$CI =  &get_instance();
	/*echo $section_field;*/
	if($section_field == 'site_language')
	{	
		if(!property_exists($CI , "homepage_sections_fields")) 
		{	
			$CI->homepage_sections_fields = $homepage_sections_fields = array();
		}else{
			$homepage_sections_fields = $CI->homepage_sections_fields;	
		}
		
		if(!array_key_exists($section_field , $homepage_sections_fields)){
		
			/*echo " here ";*/
			$property_lang_option = array();
			$site_language = 		get_option('site_language');	
			$default_language = 	get_option('default_language');		
			if(isset($site_language) && !empty($site_language))
			{ 
				$site_language_array = json_decode($site_language,true);
				if(!empty($site_language_array)) 
				{
					$property_lang_option['all'] = 'All Languages';
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
					foreach($site_language_array as $k=>$v) 
					{
						if($v['status'] != 'enable')
							continue;
						$lang_exp = explode('~',$v['language']);
						$lang_code = $lang_exp[1];
						$lang_title = $lang_exp[0];
						
						$property_lang_option[$lang_code] = $lang_title;
					}
				}
			}
			
			$single_field_options = $property_lang_option;
			$homepage_sections_fields[$section_field] = $single_field_options;
			}else{
			/*echo " there ".$section_field;	*/
			$single_field_options = $homepage_sections_fields[$section_field];
			}
		
			$CI->homepage_sections_fields =  $homepage_sections_fields;
			
			/*echo "<pre>";print_r($CI->homepage_sections_fields);echo "</pre>";*/
			return $single_field_options;
	
	
		/*return $section_field;*/
	}else
		return false;
}

/*
add_filter("homepage_sections_get_dynamic_property_type", "homepage_sections_get_dynamic_property_type_callback");*/

function homepage_sections_get_dynamic_property_type_callback($section_field = "")

{
	
	
	$CI =  &get_instance();
	/*ob_start(); 
		
	echo  $meta_settings = ob_get_clean();*/
	
	
	
	if(!property_exists($CI , "homepage_sections_fields")) 
	{	
		$CI->homepage_sections_fields = $homepage_sections_fields = array();
	}else{
		$homepage_sections_fields = $CI->homepage_sections_fields;	
	}
	
		if(!array_key_exists($section_field , $homepage_sections_fields)){
			
			/*echo " here ";*/
			$property_type_option = array();
			$property_type_option['all'] = 'All Types';
			$property_type_result = $CI->Common_model->commonQuery("select * from property_types where status = 'Y' order by title ASC");
			if($property_type_result->num_rows() > 0)
			{
				foreach($property_type_result->result() as $pt_row)
				{
					$property_type_option[$pt_row->pt_id] = $pt_row->title;
				}
			}
			
			$single_field_options = $property_type_option;
			$homepage_sections_fields[$section_field] = $single_field_options;
		}else{
			/*echo " there ";	*/
			$single_field_options = $homepage_sections_fields[$section_field];
		}
		
		$CI->homepage_sections_fields =  $homepage_sections_fields;
		/*echo "<pre>";print_r($CI->homepage_sections_fields);echo "</pre>";*/
		return $single_field_options;
	
}


/*add_filter("homepage_sections_get_dynamic_property_for_lang", "homepage_sections_get_dynamic_property_for_lang_callback");*/

function homepage_sections_get_dynamic_property_for_lang_callback($section_field = ""){
	
	
	$CI =  &get_instance();
	/*ob_start(); 
		
	echo  $meta_settings = ob_get_clean();*/
	
	
	if(!property_exists($CI , "homepage_sections_fields")) 
	{	
		$CI->homepage_sections_fields = $homepage_sections_fields = array();
	}else{
		$homepage_sections_fields = $CI->homepage_sections_fields;	
	}
	
	if(!array_key_exists($section_field , $homepage_sections_fields)){
		
		/*echo " here ";*/
		$property_lang_option = array();
		$site_language = $CI->global_lib->get_option('site_language');	
		$default_language = $CI->global_lib->get_option('default_language');		
		if(isset($site_language) && !empty($site_language))
		{ 
			$site_language_array = json_decode($site_language,true);
			if(!empty($site_language_array)) 
			{
				$property_lang_option['all'] = 'All Languages';
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
				foreach($site_language_array as $k=>$v) 
				{
					if($v['status'] != 'enable')
						continue;
					$lang_exp = explode('~',$v['language']);
					$lang_code = $lang_exp[1];
					$lang_title = $lang_exp[0];
					
					$property_lang_option[$lang_code] = $lang_title;
				}
			}
		}
		
		$single_field_options = $property_lang_option;
		$homepage_sections_fields[$section_field] = $single_field_options;
	}else{
		/*echo " there ".$section_field;	*/
		$single_field_options = $homepage_sections_fields[$section_field];
	}
	
	$CI->homepage_sections_fields =  $homepage_sections_fields;
	
	/*echo "<pre>";print_r($CI->homepage_sections_fields);echo "</pre>";*/
	return $single_field_options;
	
}







