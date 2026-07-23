<?php 
$CI = &get_instance();

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

if(file_exists("locations/json/countries.json"))
{
	$countries_meta = file_get_contents(base_url("locations/json/countries.json"));	
	if(!empty($countries_meta))
		$countries = json_decode($countries_meta, true);
}
?>

<div class="row location-fields">
	
	<div class="col-md-6 form-group">
		<label for="country"><?php echo mlx_get_lang('Country'); ?> <span class="text-red">*</span></label>
		<select class="form-control select2_elem loc_country_list" name="country" id="country" required>
			<option value=""><?php echo mlx_get_lang('Select Any Country'); ?></option>
			<?php
			if(isset($countries))
			{
				foreach($countries as $country)
				{
					echo '<option value="'.$country['countryCode'].'~'.$country['countryName'].'~'.$country['geonameId'].'">'.mlx_get_lang($country['countryName']).'</option>';	
				}
			}
			?>
		</select>
	</div>
	
	<?php if($is_state_enable) { ?>
	<div class="col-md-6 form-group">
		<label for="state"><?php echo mlx_get_lang('State'); ?> <span class="text-red">*</span></label>
		<select class="form-control select2_elem loc_state_list" name="state" id="state" required>
			<option value=""><?php echo mlx_get_lang('Select Any State'); ?></option>
			<?php echo $state_list; ?>
		</select>
	</div>
	<?php } ?>
	
	<?php if($is_state_enable) { ?>
		<div class="clearfix"></div>
		<?php } ?>
	
	<?php if($is_city_enable) { ?>
	<div class="col-md-6 form-group">
		<label for="city"><?php echo mlx_get_lang('City'); ?> <span class="text-red">*</span></label>
		<select class="form-control select2_elem loc_city_list" name="city" id="city" required>
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