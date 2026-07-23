<?php 
$CI = &get_instance();

$loc_tax_settings = get_option('loc_tax_settings');
$is_state_enable = false;
$is_city_enable = false;
$is_zipcode_enable = false;
$is_subarea_enable = false;

$locations = get_option('locations');	
/*
$locations = json_decode($locations , true);
echo "<pre>"; print_r($locations); echo "</pre>";
*/


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
$country_name = '';
$country_list = '';
$state_list = '';
$city_list = '';
$zipcode_list = '';
$subarea_list = '';

$country_code = $state_code = $city_code = '';
if(!empty($locations))
{
	$location_array = json_decode($locations,true);
	
	if(isset($location_array['countries']) && !empty($location_array['countries']))
	{
		foreach($location_array['countries'] as $country_code=>$cv)
		{
			$country_name = $location_array['countries'][$country_code]['loc_title'];
			
			$country_list .= '<option data-country_code="'.$country_code.'" value="'.mlx_get_norm_string($country_name).'"';
						
			if(isset($sel_country) && $sel_country == mlx_get_norm_string($country_name))
			{
				$country_list .= ' selected="selected" ';
				
				if($is_state_enable)
				{
					if(isset($location_array['countries'][$country_code]))
					{
						if(isset($location_array['countries'][$country_code]['states']))
						{
							foreach($location_array['countries'][$country_code]['states'] as $skey=>$sval)
							{
								if($skey != 'no_state')
									$state_list .= '<option data-country_code="'.$country_code.'" data-state_code="'.$skey.'" data-full_value="'.$sval['loc_title'].'" value="'.mlx_get_norm_string($sval['loc_title']).'"';
									if(isset($sel_state) && $sel_state == mlx_get_norm_string($sval['loc_title']))
									{
										$state_code = $skey;
										$state_list .= ' selected="selected" ';
									}
									$state_list .= '>'.$sval['loc_title'].'</option>';
							}
						}
					}
					if(isset($is_edit) && !empty($state_code))
					{
						if(isset($location_array['countries'][$country_code]['states'][$state_code]['cities']) &&
						$is_city_enable)
						{
							$cities = $location_array['countries'][$country_code]['states'][$state_code]['cities'];
							if(!empty($cities))
							{
								foreach($cities as $skey=>$sval)
								{
									$city_list .= '<option data-country_code="'.$country_code.'" data-state_code="'.$state_code.'" 
													data-city_code="'.$skey.'" data-full_value="'.$sval['loc_title'].'" 
														value="'.mlx_get_norm_string(trim($sval['loc_title'])).'"';
									if(isset($sel_city) && $sel_city == mlx_get_norm_string($sval['loc_title']))
									{
										$city_list .= ' selected="selected" ';
										
										if(isset($sval['zipcodes']) && !empty($sval['zipcodes']) && $is_zipcode_enable)
										{
											foreach($sval['zipcodes'] as $zipcode)
											{
												$zipcode_list .= '<option value="'.trim($zipcode).'"';
												if(isset($sel_zip_code) && $sel_zip_code == trim($zipcode))
													$zipcode_list .= ' selected="selected" ';
												$zipcode_list .= '>'.$zipcode.'</option>';
											}
										}
										
										if(isset($sval['sub_areas']) && !empty($sval['sub_areas']) && $is_subarea_enable)
										{
											foreach($sval['sub_areas'] as $subarea)
											{
												
												$subarea_list .= '<option value="'.trim($subarea).'"';
												if(isset($sel_sub_area) && $sel_sub_area == trim($subarea))
												{
													$subarea_list .= ' selected="selected" ';
												}
												$subarea_list .= '>'.$subarea.'</option>';
											}
										}
										
									}
									$city_list .= '>'.$sval['loc_title'].'</option>';
								}
							}
						}
					}
				}
				else
				{
					$location_array = json_decode($locations,true);
					
					if(isset($location_array['countries'][$country_code]))
					{
						if(isset($location_array['countries'][$country_code]['states']['no_state']['cities']))
						{
							foreach($location_array['countries'][$country_code]['states']['no_state']['cities'] as $skey=>$sval)
							{
								$city_list .= '<option data-country_code="'.$country_code.'" data-state_code="no_state" data-city_code="'.$skey.'" 
									data-full_value="'.$sval['loc_title'].'" value="'.mlx_get_norm_string(trim($sval['loc_title'])).'"';
								
								
								
								if(isset($sel_city) && 
									strtolower(mlx_get_norm_string($sel_city)) == strtolower(mlx_get_norm_string($sval['loc_title']))  
									){
										$city_list .= ' selected="selected" ';
								
									if(isset($sval['zipcodes']) && !empty($sval['zipcodes']) && $is_zipcode_enable)
									{
										foreach($sval['zipcodes'] as $zipcode)
										{
											$zipcode_list .= '<option value="'.trim($zipcode).'"';
											if(isset($sel_zip_code) && $sel_zip_code == trim($zipcode))
												$zipcode_list .= ' selected="selected" ';
											$zipcode_list .= '>'.$zipcode.'</option>';
										}
									}
									
									if(isset($sval['sub_areas']) && !empty($sval['sub_areas']) && $is_subarea_enable)
									{
										foreach($sval['sub_areas'] as $subarea)
										{
											
											$subarea_list .= '<option value="'.trim($subarea).'"';
											if(isset($sel_sub_area) && $sel_sub_area == trim($subarea))
											{
												$subarea_list .= ' selected="selected" ';
											}
											$subarea_list .= '>'.$subarea.'</option>';
										}
									}
								
								
								
								
								}		
										
								/**
								 here '=' . $sel_city.
								*/		
								$city_list .= '>'.$sval['loc_title']. '</option>';
							}
						}
					}
				}
				
			}
			
			$country_list .= '>'.$country_name.'</option>';
		}
	}
		
}
?>

<div class="row location-fields">
	
	<div class="col-md-6 form-group">
		<label for="country"><?php echo mlx_get_lang('Country'); ?> 
		
		</label>
		<select class="form-control select2_elem loc_country_list" name="country" id="country" >
			<option value=""><?php echo mlx_get_lang('Select Any Country'); ?></option>
			<?php echo $country_list; ?>
		</select>
	</div>
	
	<?php if($is_state_enable) { ?>
	<div class="col-md-6 form-group">
		<label for="state"><?php echo mlx_get_lang('State'); ?> 
		</label>
		<select class="form-control select2_elem loc_state_list" name="state" id="state" >
			<option value=""><?php echo mlx_get_lang('Select Any State'); ?></option>
			<?php echo $state_list; ?>
		</select>
	</div>
	<div class="clearfix"></div>
	<?php } ?>
	
	<?php if($is_city_enable) { ?>
	<div class="col-md-6 form-group">
		<label for="city"><?php echo mlx_get_lang('City'); ?> 
		</label>
		<select class="form-control select2_elemSS loc_city_list" name="city" id="city" >
			<option value=""><?php echo mlx_get_lang('Select Any City'); ?></option>
			<?php echo $city_list; ?>
		</select>
	</div>
	<?php } ?>
	
	<?php if(!$is_state_enable && $is_city_enable) { ?>
		<div class="clearfix"></div>
	<?php } ?>
	
	<?php if($is_zipcode_enable) { ?>
	<div class="col-md-6 form-group">
		<label for="zipcode"><?php echo mlx_get_lang('Zipcode'); ?> </label>
		<select class="form-control select2_elem zipcode_list" name="zipcode" id="zipcode" >
			<option value=""><?php echo mlx_get_lang('Select Any Zipcode'); ?></option>
			<?php echo $zipcode_list; ?>
		</select>
	</div>
	<?php } ?>
	
	<?php if($is_state_enable && $is_city_enable && $is_zipcode_enable) { ?>
		<div class="clearfix"></div>
	<?php } ?>
	
	<?php if($is_subarea_enable) { ?>
	<div class="col-md-6 form-group">
		<label for="sub_area"><?php echo mlx_get_lang('Sub Area'); ?> </label>
		<select class="form-control select2_elem sub_area_list" name="sub_area" id="sub_area">
			<option value=""><?php echo mlx_get_lang('Select Any Sub Area'); ?></option>
			<?php echo $subarea_list; ?>
		</select>
	</div>
	<?php } ?>
	
</div>
