<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ajax_locations extends MY_Controller {
	
	
	public function get_city_name_list_callback_func()
	{
		
		extract($_POST);		
		$CI =& get_instance();	
		$cities_list = '<option value="">Select Any City</option>';
		
		$loc_tax_settings = $this->global_lib->get_option('loc_tax_settings');
		$locations = $this->global_lib->get_option('locations');
		
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
	
	
	public function get_zip_sub_area_name_list_callback_func(){
		
		extract($_POST);		
		$CI =& get_instance();	
		$zipcode_list = '<option value="">Select Any Zipcode</option>';
		$subarea_list = '<option value="">Select Any Sub Area</option>';
		
		$loc_tax_settings = $this->global_lib->get_option('loc_tax_settings');
		$locations = $this->global_lib->get_option('locations');
		
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
	
	
}
