<?php

$CI = &get_instance();

extract($_GET); 

$loc_tax_settings = get_option('loc_tax_settings');
$locations = get_option('locations');


$find = "city";
if (isset($city)  &&   preg_match("/" . $find . "/", $city)) {
	$city = str_replace($find . "-", "", urldecode($city));
	
	
}

$find = "state";
if (isset($state)  &&   preg_match("/" . $find . "/", $state)) {
	$state = str_replace($find . "-", "", urldecode($state));
}

$is_state_enable = $is_city_enable = true;

if (!empty($loc_tax_settings)) {
	$loc_tax_setting_array = json_decode($loc_tax_settings, true);

	

	if (isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
		$is_state_enable = true;
	else
		$is_state_enable = false;

	if (isset($loc_tax_setting_array['city']['enabled']) && $loc_tax_setting_array['city']['enabled'] == true)
		$is_city_enable = true;
	else
		$is_state_enable = false;
}

$country_name = '';
$state_list = '';
$city_list = '';
$zipcode_list = '';
$subarea_list = '';
global $search_col_class, $states, $cities;


if (isset($enable_property_for_cities) && $enable_property_for_cities == 'Y' && !empty($property_for_cities))
	$cities = true;

if (isset($enable_property_for_states) && $enable_property_for_states == 'Y' && !empty($property_for_states))
	$states = true;

$states = true;
$cities = true;

$country_code = $state_code = $city_code = '';
$lang_code = $CI->default_language;


$subarea = (isset($_GET['subarea']))?$_GET['subarea']:'';
$zipcode = (isset($_GET['zipcode']))?$_GET['zipcode']:'';

if (!empty($locations) && $is_state_enable) {
	$location_array = json_decode($locations, true);

	foreach ($location_array['countries'] as $key => $value) {

		$country_code = $key;
		if (!empty($country_code) && isset($location_array['countries'][$country_code])) {
			$country_name = $location_array['countries'][$country_code]['loc_title'];

			if (isset($location_array['countries'][$country_code]['states'])) {
				if (count($value) > 1)
					$state_list .= '<optgroup label="' . mlx_get_lang(ucwords($country_name)) . '">';
				foreach ($location_array['countries'][$country_code]['states'] as $skey => $sval) {

					if ($skey != 'no_state') {
						$state_list .= '<option 
												data-country_code="' . $country_code . '" 
												data-country_title="' . ucwords($country_name) . '" 
												data-state_code="' . $skey . '" 
												data-full_value="' . $sval['loc_title'] . '"
												value="' . mlx_get_norm_string($sval['loc_title']) . '"';
						if (
							isset($state) && urldecode($state) == mlx_get_norm_string($sval['loc_title'])
						) 
						{
							$state_code = $skey;
							$state_list .= ' selected="selected" ';

							if (isset($location_array['countries'][$country_code]['states'][$state_code]['cities'])) {
								$cities = $location_array['countries'][$country_code]['states'][$state_code]['cities'];
								if (!empty($cities)) {
									foreach ($cities as $ckey => $cval) {
										$city_list .= '<option data-country_code="' . $country_code . '" 
																		   data-state_code="' . $state_code . '" 
																		   data-city_code="' . $ckey . '" 
																		   value="' . mlx_get_norm_string($cval['loc_title']) . '"
																		   data-full_value="' . $cval['loc_title'] . '"
																		   ';

										$current_city = mlx_get_norm_string($cval['loc_title']);
										$sExp = explode('~',$current_city);
										if(count($sExp) > 1)
											$current_city = $sExp[0];
										
										/*if (isset($city) && urldecode($city) == mlx_get_norm_string($cval['loc_title'])) */
										if (isset($city) && urldecode($city) == strtolower($current_city) ) 
										{
											$city_list .= ' selected="selected" ';

											if (isset($cval['zipcodes']) && !empty($cval['zipcodes'])) {
												foreach ($cval['zipcodes'] as $city_zipcode) {
													$zipcode_list .= '<option value="' . $zipcode . '"';
													if (isset($zipcode) && urldecode($zipcode) == $city_zipcode)
														$zipcode_list .= ' selected="selected" ';
													$zipcode_list .= '>' . mlx_get_lang($city_zipcode) . '</option>';
												}
											}
											if (isset($cval['sub_areas']) && !empty($cval['sub_areas'])) {
												foreach ($cval['sub_areas'] as $city_subarea) {
													$subarea_list .= '<option value="' . $city_subarea . '"';
													if (isset($subarea) && urldecode($subarea) == $city_subarea)
														$subarea_list .= ' selected="selected" ';
													$subarea_list .= '>' . mlx_get_lang($city_subarea) . '</option>';
												}
											}
										}
										$city_list .= '>' . mlx_get_lang($cval['loc_title']) . '</option>';
									}
								}
							}
						}
						$state_list .= '>' . mlx_get_lang($sval['loc_title']) . '</option>';
					}
				}
				if (count($value) > 1)
					$state_list .= '</optgroup>';
			}
		}
	}
} else if (!empty($locations)) {
	$location_array = json_decode($locations, true);

	/*echo "<pre>";print_r($location_array);	echo "</pre>";*/

	/*$lc_val = $country_code;
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
	*/
	if (	isset($location_array['countries'])) {
		
		foreach($location_array['countries']  as $country => $c_lists){
			/*echo $country;
			print_r($c_lists);*/
			$country_name = $location_array['countries'][$country]['loc_title'];
			
			if (isset($location_array['countries'][$country]['states']['no_state']['cities'])) {
				foreach ($location_array['countries'][$country]['states']['no_state']['cities'] as $skey => $sval) {
					$city_list .= '<option data-country_code="' . $country . '" data-state_code="no_state" data-city_code="' . 
						$skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
						
					
					$current_city = mlx_get_norm_string($sval['loc_title']);
					$sExp = explode('~',$current_city);
					if(count($sExp) > 1)
						$current_city = $sExp[0];
					
					/*if (isset($lang_city) && $lang_city == $sval['loc_title'])*/
					if (isset($city) && urldecode($city) == strtolower($current_city) ){ 
						$city_list .= ' selected="selected" ';
						
						if (isset($sval['zipcodes']) && !empty($sval['zipcodes'])) {
							foreach ($sval['zipcodes'] as $city_zipcode) {
								$zipcode_list .= '<option value="' . $city_zipcode . '"';
								if (isset($zipcode) && urldecode($zipcode) == $city_zipcode)
									$zipcode_list .= ' selected="selected" ';
								$zipcode_list .= '>' . mlx_get_lang($city_zipcode) . '</option>';
							}
						}
						if (isset($sval['sub_areas']) && !empty($sval['sub_areas'])) {
							foreach ($sval['sub_areas'] as $city_subarea) {
								$subarea_list .= '<option value="' . $city_subarea . '"';
								if (isset($subarea) && urldecode($subarea) == $city_subarea)
									$subarea_list .= ' selected="selected" ';
								$subarea_list .= '>' . mlx_get_lang($city_subarea) . '</option>';
							}
						}
					
					
					}	
					$city_list .= '>' . $sval['loc_title'] . '</option>';
				}
			}	
		}
		/*$country_name = $location_array['countries'][$lc_val]['loc_title'];

		if (isset($location_array['countries'][$lc_val]['states']['no_state']['cities'])) {
			foreach ($location_array['countries'][$lc_val]['states']['no_state']['cities'] as $skey => $sval) {
				$city_list .= '<option data-country_code="' . $lc_val . '" data-state_code="no_state" data-city_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
				if (isset($lang_city) && $lang_city == $sval['loc_title'])
					$city_list .= ' selected="selected" ';
				$city_list .= '>' . $sval['loc_title'] . '</option>';
			}
		}*/
	}
}

?>

<input type="hidden" class="country_list" value="<?php echo $country_name; ?>">

<?php if ($is_state_enable) { ?>
	<div class="<?php echo $search_col_class; ?> search-filter-block">
		<label for="select-state"><?php echo mlx_get_lang('Select State'); ?></label>
		<div class="select-wrap">
			<span class="icon icon-arrow_drop_down"></span>
			<select name="state" id="select-state" class="form-control d-block rounded-0 loc_state_list">
				<option value=""><?php echo mlx_get_lang('Select Any State'); ?></option>
				<?php echo $state_list; ?>
			</select>
		</div>
	</div>
<?php } ?>

<?php if ($is_city_enable) {
?>
	<div class="<?php echo $search_col_class; ?> search-filter-block">
		<label for="select-city"><?php echo mlx_get_lang('Select City'); ?></label>
		<div class="select-wrap">
			<span class="icon icon-arrow_drop_down"></span>
			<select name="city" id="select-city" class="form-control d-block rounded-0 loc_city_list">
				<option value=""><?php echo mlx_get_lang('Select Any City'); ?></option>
				<?php echo $city_list; ?>
			</select>
		</div>
	</div>
	
	<?php 
	
	$enable_zipcode_on_search = get_option('enable_zipcode_on_search');
	$enable_subarea_on_search = get_option('enable_subarea_on_search');


	if ($enable_zipcode_on_search == 'Y') {	
	?>
	<div class="<?php echo $search_col_class; ?> search-filter-block">
		<label for="select-zipcode"><?php echo mlx_get_lang('Select Zipcode'); ?></label>
		<div class="select-wrap">
			<span class="icon icon-arrow_drop_down"></span>
			<select name="zipcode" id="select-zipcode" class="form-control d-block rounded-0 zipcode_list">
				<option value=""><?php echo mlx_get_lang('Select Any Zipcode'); ?></option>
				<?php echo $zipcode_list; ?>
			</select>
		</div>
	</div>
	
	<?php	
	}	
	
	if ($enable_subarea_on_search == 'Y') {	
	?>
	<div class="<?php echo $search_col_class; ?> search-filter-block">
		<label for="select-subarea"><?php echo mlx_get_lang('Select Sub-area'); ?></label>
		<div class="select-wrap">
			<span class="icon icon-arrow_drop_down"></span>
			<select name="subarea" id="select-subarea" class="form-control d-block rounded-0 sub_area_list">
				<option value=""><?php echo mlx_get_lang('Select Any Sub-area'); ?></option>
				<?php echo $subarea_list; ?>
			</select>
		</div>
	</div>
	
	<?php	
	}
	
	?>
	
<?php } ?>