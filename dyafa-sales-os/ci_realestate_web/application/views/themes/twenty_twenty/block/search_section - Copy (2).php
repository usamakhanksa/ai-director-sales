<?php 
global $settings;
extract($settings);

$def_lang_code = $this->default_language;

/*$location_plugin_active = false;
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
*/
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
			global $search_col_class , $states , $cities;
			$search_col_class = "col-md-3";
			$states = $cities = false;
			/*if(isset($enable_property_for_cities) && $enable_property_for_cities == 'Y' && !empty($property_for_cities))
				$cities = true;
				
			if(isset($enable_property_for_states) && $enable_property_for_states == 'Y' && !empty($property_for_states))
				$states = true;
			
			if($location_plugin_active)
			{
				
				if($is_state_enable)
					$states = true;
				if($is_city_enable)
					$cities = true;
			}*/
			
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
			  
			  <?php do_action("location_search_fields");?>
			
			
			  
			  
			  <div class="<?php echo $search_col_class; ?>">
				<button type="submit" class="btn btn-success text-white btn-block rounded-0" ><?php echo mlx_get_lang('Search'); ?></button>
			  </div>
			</div>
			
			<?php 
			if($settings['show_advance_search'] == 'yes')
			{ 
				$this->load->view($themes.'/properties-advance-search.php'); 
			}
			?>	
			
		  </form>
		  </div>
		</div>
	
  </div>
</div>
<?php do_action("location_search_scripts");?>