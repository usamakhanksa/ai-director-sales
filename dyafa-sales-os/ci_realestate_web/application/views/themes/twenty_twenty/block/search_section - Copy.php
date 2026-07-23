<?php 
global $settings;

$def_lang_code = $this->default_language;

$location_plugin_active = false;
$isPlugAct = $myHelpers->isPluginActive('property_locations');
if($isPlugAct == true)
{
	$location_plugin_active = true;
	$locations = $myHelpers->global_lib->get_option('locations');
}

if($location_plugin_active)
{
	$loc_tax_settings = $this->global_lib->get_option('loc_tax_settings');
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
}
else
{
	$enable_property_for_cities = $myHelpers->global_lib->get_option('enable_property_for_cities');
	$property_for_cities = $myHelpers->global_lib->get_option('property_for_cities');

	$enable_property_for_states = $myHelpers->global_lib->get_option('enable_property_for_states');
	$property_for_states = $myHelpers->global_lib->get_option('property_for_states');
}
?>
<div class="site-section site-section-sm ">
<style type="text/css">
@media (min-width: 768px) {

	.col-md-3.cols-5 {
		-webkit-box-flex: 0;
		-ms-flex: 0 0 20%!important;
		flex: 0 0 20%!important;
		max-width: 20%!important;
	}
}
</style>
<script>
$(document).ready(function() {
	<?php if($location_plugin_active){ ?>
		
		$('.loc_state_list').change(function() {
			var thiss = $(this);
			var country_code = thiss.find('option:selected').attr('data-country_code');
			var state_name = thiss.val();
			var state_code = thiss.find('option:selected').attr('data-state_code');
			thiss.parents('.form-search').find('.loc_city_list').html('<option value="">Select Any City</option>');
			thiss.parents('.form-search').find('.zipcode_list').html('<option value="">Select Any Zipcode</option>');
			thiss.parents('.form-search').find('.sub_area_list').html('<option value="">Select Any Sub Area</option>');
			if(country_code != '' && state_code != '' && state_name != '')
			{
				$('.full_sreeen_overlay').show();
				
				thiss.parents('.form-search').find('.loc_city_list').html('<option value="">Loading...</option>');
				thiss.parents('.form-search').find('.zipcode_list').html('<option value="">Loading...</option>');
				thiss.parents('.form-search').find('.sub_area_list').html('<option value="">Loading...</option>');
				
				$.ajax({						
					url: base_url+'ajax_locations/get_city_name_list_callback_func',						
					type: 'POST',						
					success: function (res) 
					{	
						if(res == '')
							thiss.parents('.form-search').find('.loc_city_list').html('<option value="">Select Any City</option>');
						else
							thiss.parents('.form-search').find('.loc_city_list').html(res);
						$('.full_sreeen_overlay').hide();
					},						
					data: {	country_code : country_code, state_code : state_code},						
					cache: false					
				});
			}
			return false;
			
		});
		
		$('.loc_city_list').change(function() {
			var thiss = $(this);
			var country_code = thiss.find('option:selected').attr('data-country_code');
			var state_code = thiss.find('option:selected').attr('data-state_code');
			var city_code = thiss.find('option:selected').attr('data-city_code');
			var city_name = thiss.val();
			thiss.parents('.form-search').find('.zipcode_list').html('<option value="">Select Any Zipcode</option>');
			thiss.parents('.form-search').find('.sub_area_list').html('<option value="">Select Any Sub Area</option>');
			if(country_code != '' && state_code != '' && city_code != '' && city_name != '')
			{
				$('.full_sreeen_overlay').show();
				
				thiss.parents('.form-search').find('.zipcode_list').html('<option value="">Loading...</option>');
				thiss.parents('.form-search').find('.sub_area_list').html('<option value="">Loading...</option>');
				
				$.ajax({						
					url: base_url+'ajax_locations/get_zip_sub_area_name_list_callback_func',						
					type: 'POST',						
					success: function (res) 
					{		
						thiss.parents('.form-search').find('.zipcode_list').html(res.zipcode_list);
						thiss.parents('.form-search').find('.sub_area_list').html(res.subarea_list);
						$('.full_sreeen_overlay').hide();
					},						
					data: {	country_code : country_code, state_code : state_code, city_code : city_code},						
					cache: false					
				});
			}
			return false;
			
		});
		
		
	<?php } ?>
});
</script>
  <div class="container">
	
	<div class="row">
		<div class="col-md-12">
		  <?php 
		  
			$margin_top = 0;
			$attributes = array('name' => 'add_form_post',
								'class' => 'form-search col-md-12',
								'style' => 'margin-top: '.$margin_top.'px;', 
								'method' => 'get'
								);	
			$search = $myHelpers->menu_lib->get_url('search');						 			
			echo form_open_multipart($search,$attributes); 
			
			
			?>
			
			<div class="row  align-items-end">
			
			<?php 
			$search_col_class = "col-md-3";
			$states = $cities = false;
			if(isset($enable_property_for_cities) && $enable_property_for_cities == 'Y' && !empty($property_for_cities))
				$cities = true;
				
			if(isset($enable_property_for_states) && $enable_property_for_states == 'Y' && !empty($property_for_states))
				$states = true;
			
			if($location_plugin_active)
			{
				
				if($is_state_enable)
					$states = true;
				if($is_city_enable)
					$cities = true;
			}
			
			if($states && $cities)	
				$search_col_class = "col-md-3 cols-5";
			else if(!$states && !$cities)	
				$search_col_class = "col-md-4 ";
			else if($states || $cities)	
				$search_col_class = "col-md-3 ";
			
			
			
			?>
			
			  <div class="<?php echo $search_col_class; ?> search-filter-block">
				<label for="list-types"><?php echo mlx_get_lang('Listing Types'); ?></label>
				<div class="select-wrap">
				  <span class="icon icon-arrow_drop_down"></span>
				  <select name="type" id="list-types" class="form-control d-block rounded-0">
					<option value=""><?php echo mlx_get_lang('Select Property Type'); ?></option>
					<?php 
					if(isset($property_type_list) && $property_type_list->num_rows() > 0){ 
						foreach($property_type_list->result() as $prop_row){ 
						
						$prop_type_slug = $prop_row->slug;
						?>
							<option  
							<?php 
							if(isset($type) && $type == $prop_type_slug) echo ' selected="selected" ';
							 ?> 
							value="<?php echo $prop_type_slug ; ?>"><?php echo mlx_get_lang(ucfirst($prop_row->title)); ?></option>
					<?php } } ?>
				  </select>
				</div>
			  </div>
			  
			  <div class="<?php echo $search_col_class; ?> search-filter-block">
				<label for="offer-types"><?php echo mlx_get_lang('Offer Type'); ?></label>
				<div class="select-wrap">
				  <span class="icon icon-arrow_drop_down"></span>
				  
				  <select name="for" id="offer-types" class="form-control d-block rounded-0">
					<option value=""><?php echo mlx_get_lang('Select Property For'); ?></option>
					<option value="sale" 
					<?php if(isset($for) && $for == 'sale') echo ' selected="selected" '; ?>><?php echo mlx_get_lang('For Sale'); ?></option>
					<option value="rent" 
					<?php if(isset($for) && $for == 'rent') echo ' selected="selected" '; ?>><?php echo mlx_get_lang('For Rent'); ?></option>
					
				  </select>
				</div>
			  </div>
			
			<?php 
			if($location_plugin_active) { 
		
				$country_name = '';
				$state_list = '';
				$city_list = '';
				$zipcode_list = '';
				$subarea_list = '';

				$country_code = $state_code = $city_code = '';
				$lang_code = $this->default_language;
				
				if(isset($locations) && !empty($locations) && $is_state_enable)
				{
					$location_array = json_decode($locations,true);
					
					$lc_val = $this->global_lib->get_option('language_country_'.$lang_code);
					$exp_lc_val = explode(',',$lc_val);
					
					foreach($exp_lc_val as $cc)
					{
						$country_code = $cc;
						if(!empty($country_code) && isset($location_array['countries'][$country_code]))
						{
							$country_name = $location_array['countries'][$country_code]['loc_title'];
							
							if(isset($location_array['countries'][$country_code]['states']))
							{
								if(count($exp_lc_val) > 1)
									$state_list .= '<optgroup label="'.mlx_get_lang_with_org(mlx_get_norm_string($country_name),$country_name).'">';
								foreach($location_array['countries'][$country_code]['states'] as $skey=>$sval)
								{
									
									if($skey != 'no_state')
									{
										$state_list .= '<option data-country_code="'.$country_code.'" 
															    data-state_code="'.$skey.'" 
																data-full_value="'.$sval['loc_title'].'"
																value="'.mlx_get_norm_string($sval['loc_title']).'"' ;
										if(isset($state) && urldecode($state) == mlx_get_norm_string($sval['loc_title']))
										{
											$state_code = $skey;
											$state_list .= ' selected="selected" ';
											
											if(isset($location_array['countries'][$country_code]['states'][$state_code]['cities']))
											{
												$cities = $location_array['countries'][$country_code]['states'][$state_code]['cities'];
												if(!empty($cities))
												{
													foreach($cities as $ckey=>$cval)
													{
														$city_list .= '<option data-country_code="'.$country_code.'" 
																			   data-state_code="'.$state_code.'" 
																			   data-city_code="'.$ckey.'" 
																			   value="'.mlx_get_norm_string($cval['loc_title']).'"
																			   data-full_value="'.$cval['loc_title'].'"
																			   ';
														
														if(isset($city) && urldecode($city) == mlx_get_norm_string($cval['loc_title']))
														{
															$city_list .= ' selected="selected" ';
															
															if(isset($cval['zipcodes']) && !empty($cval['zipcodes']))
															{
																foreach($cval['zipcodes'] as $zipcode)
																{
																	$zipcode_list .= '<option value="'.$zipcode.'"';
																	if(isset($zip_code) && urldecode($zip_code) == $zipcode)
																		$zipcode_list .= ' selected="selected" ';
																	$zipcode_list .= '>'.mlx_get_lang($zipcode).'</option>';
																}
															}
															if(isset($cval['sub_areas']) && !empty($cval['sub_areas']))
															{
																foreach($cval['sub_areas'] as $subarea)
																{
																	$subarea_list .= '<option value="'.$subarea.'"';
																	if(isset($sub_area) && urldecode($sub_area) == $subarea)
																		$subarea_list .= ' selected="selected" ';
																	$subarea_list .= '>'.mlx_get_lang($subarea).'</option>';
																}
															}
															
														}
														$city_list .= '>'.mlx_get_lang_with_org(mlx_get_norm_string($cval['loc_title']),$cval['loc_title']).'</option>';
													}
												}
											}
											
										}
										$state_list .= '>'.mlx_get_lang_with_org(mlx_get_norm_string($sval['loc_title']),$sval['loc_title']).'</option>';
									}
									
								}
								if(count($exp_lc_val) > 1)
									$state_list .= '</optgroup>';
							}
						}
						
					}
				}
				
				else if(!empty($locations))
				{
					$location_array = json_decode($locations,true);
					$lang_code = $this->default_language;
					$lc_val = $this->global_lib->get_option('language_country_'.$lang_code);
					$exp_lc_val = explode(',',$lc_val);
					foreach($exp_lc_val as $cc)
					{
						$country_code = $cc;
						if(!empty($country_code) && isset($location_array['countries'][$country_code]))
						{
							$country_name = $location_array['countries'][$country_code]['loc_title'];
							
							if(isset($location_array['countries'][$country_code]['states']['no_state']['cities']))
							{
								if(count($exp_lc_val) > 1)
									$city_list .= '<optgroup label="'.ucwords(mlx_get_lang($country_name)).'">';
								foreach($location_array['countries'][$country_code]['states']['no_state']['cities'] as $skey=>$sval)
								{
									$city_list .= '<option data-country_code="'.$country_code.'" data-state_code="no_state" data-city_code="'.$skey.'" value="'.$sval['loc_title'].'"';
									if(isset($city) && urldecode($city) == $sval['loc_title'])
											$city_list .= ' selected="selected" ';
									$city_list .= '>'.mlx_get_lang($sval['loc_title']).'</option>';
								}
								if(count($exp_lc_val) > 1)
									$city_list .= '</optgroup>';
							}
							
							
						}
					}
				}
				
				
			?>
				
				<input type="hidden" class="country_list" value="<?php echo $country_name; ?>">
				
				<!--
				<select class="form-control" style="display:none;">
					<option value=""><?php //echo mlx_get_lang('Select Any Country'); ?></option>
				</select>
				-->
				
				<?php if($is_state_enable){ ?>
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
				
				<?php if($is_city_enable){ ?>
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
				<?php } ?>
				
			<?php } else { ?>
			  <?php if(isset($enable_property_for_states) && $enable_property_for_states == 'Y' && !empty($property_for_states)){ ?>
				  <div class="<?php echo $search_col_class; ?> search-filter-block">
					<label for="select-state"><?php echo mlx_get_lang('Select State'); ?></label>
					<div class="select-wrap">
					  <span class="icon icon-arrow_drop_down"></span>
					  <select name="state" id="select-state" class="form-control d-block rounded-0">
						<option value=""><?php echo mlx_get_lang('Select Any State'); ?></option>
						<?php 
						$property_for_states_array = json_decode($property_for_states,true);
						foreach($property_for_states_array as $pck=>$pcv)
						{
						?>
						<option value="<?php echo str_replace(' ','-',strtolower($pcv)); ?>"  
						<?php 
						if(isset($state) && $state == strtolower($pcv)) echo ' selected="selected" ';
						?>><?php echo ucfirst($pcv); ?></option>
						<?php } ?>
					  </select>
					</div>
				  </div>
			  <?php } ?>
			  
			  
			  <?php if(isset($enable_property_for_cities) && $enable_property_for_cities == 'Y' && !empty($property_for_cities)){ ?>
				  <div class="<?php echo $search_col_class; ?> search-filter-block">
					<label for="select-city"><?php echo mlx_get_lang('Select City'); ?></label>
					<div class="select-wrap">
					  <span class="icon icon-arrow_drop_down"></span>
					  <select name="city" id="select-city" class="form-control d-block rounded-0">
						<option value=""><?php echo mlx_get_lang('Select Any City'); ?></option>
						<?php 
						$property_for_cities_array = json_decode($property_for_cities,true);
						foreach($property_for_cities_array as $pck=>$pcv)
						{
							$accent_string = strtolower($pcv);
							//$norm_string = strtr($accent_string, $normalizeChars);
							$norm_string = $myHelpers->language_lib->get_normal_string($accent_string);
							$norm_string = str_replace(' ','-',$norm_string);
						?>
						<option value="<?php echo $norm_string; ?>"  
						<?php 
						if(isset($city) && $city == $norm_string) echo ' selected="selected" ';
						?>><?php echo ucfirst($pcv); ?></option>
						<?php } ?>
					  </select>
					</div>
				  </div>
			  <?php } ?>
			<?php } ?>  
			  
			  
			  <div class="<?php echo $search_col_class; ?>">
				<button type="submit" class="btn btn-success text-white btn-block rounded-0" ><?php echo mlx_get_lang('Search'); ?></button>
			  </div>
			</div>
			
			<?php 
			if($settings['show_advance_search'] == 'yes')
			{ 
				$this->load->view('default/properties-advance-search.php'); 
			}
			?>	
			
		  </form>
		  </div>
		</div>
	
  </div>
</div>
