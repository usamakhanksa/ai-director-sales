<div class="form-group">
                      <label for="no_of_property_in_search_page"><?php echo mlx_get_lang('No. of Property in Search Page'); ?></label>
                      <input type="number" class="form-control" 
					  name="options[no_of_property_in_search_page]" id="no_of_property_in_search_page" 
					  value="<?php if(isset($no_of_property_in_search_page)) echo $no_of_property_in_search_page; else echo '12'; ?>" min="1" step="1">
                    </div>
					
					<div class="form-group" >
						<label for="enable_advance_search"><?php echo mlx_get_lang('Enable Advance Search?'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_advance_search_yes" value="Y" 
							data-target="advance_search_yes" data-elem="advance_search_elem"
							<?php 
							if(isset($enable_advance_search) && $enable_advance_search == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_advance_search]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enable_advance_search_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_advance_search_no" 
							data-target="advance_search_no" data-elem="advance_search_elem"
							<?php 
							if((isset($enable_advance_search) && $enable_advance_search == 'N')|| 
							!isset($enable_advance_search))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_advance_search]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enable_advance_search_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_price_range_yes"><?php echo mlx_get_lang('Price Range'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_price_range_yes" value="Y" 
							<?php 
							if(isset($advance_search_price_range) && $advance_search_price_range == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_price_range]" 
							class="toggle-radio-button">
							<label for="advance_search_price_range_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_price_range_no" 
							<?php 
							if((isset($advance_search_price_range) && $advance_search_price_range == 'N')|| 
							!isset($advance_search_price_range))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_price_range]" 
							class="toggle-radio-button">
							<label for="advance_search_price_range_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_bath_yes"><?php echo mlx_get_lang('Bath'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_bath_yes" value="Y" 
							<?php 
							if(isset($advance_search_bath) && $advance_search_bath == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_bath]" 
							class="toggle-radio-button">
							<label for="advance_search_bath_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_bath_no" 
							<?php 
							if((isset($advance_search_bath) && $advance_search_bath == 'N')|| 
							!isset($advance_search_bath))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_bath]" 
							class="toggle-radio-button">
							<label for="advance_search_bath_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_bed_yes"><?php echo mlx_get_lang('Bed'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_bed_yes" value="Y" 
							<?php 
							if(isset($advance_search_bed) && $advance_search_bed == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_bed]" 
							class="toggle-radio-button">
							<label for="advance_search_bed_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_bed_no" 
							<?php 
							if((isset($advance_search_bed) && $advance_search_bed == 'N')|| 
							!isset($advance_search_bed))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_bed]" 
							class="toggle-radio-button">
							<label for="advance_search_bed_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_indoor_amenities_yes"><?php echo mlx_get_lang('Indoor Amenities'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_indoor_amenities_yes" value="Y" 
							<?php 
							if((isset($advance_search_indoor_amenities) && $advance_search_indoor_amenities == 'Y') || 
							!isset($advance_search_indoor_amenities))  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_indoor_amenities]" 
							class="toggle-radio-button">
							<label for="advance_search_indoor_amenities_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_indoor_amenities_no" 
							<?php 
							if(isset($advance_search_indoor_amenities) && $advance_search_indoor_amenities == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_indoor_amenities]" 
							class="toggle-radio-button">
							<label for="advance_search_indoor_amenities_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_outdoor_amenities_yes"><?php echo mlx_get_lang('Outdoor Amenities'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_outdoor_amenities_yes" value="Y" 
							<?php 
							if((isset($advance_search_outdoor_amenities) && $advance_search_outdoor_amenities == 'Y') || 
							!isset($advance_search_outdoor_amenities))  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_outdoor_amenities]" 
							class="toggle-radio-button">
							<label for="advance_search_outdoor_amenities_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_outdoor_amenities_no" 
							<?php 
							if(isset($advance_search_outdoor_amenities) && $advance_search_outdoor_amenities == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_outdoor_amenities]" 
							class="toggle-radio-button">
							<label for="advance_search_outdoor_amenities_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group">
                      <label for="advance_search_min_price"><?php echo mlx_get_lang('Frontend Advance Search Min Price'); ?></label>
                      <input type="number" min="0" step="1" class="form-control" 
					  name="options[advance_search_min_price]" id="advance_search_min_price" 
					  value="<?php if(isset($advance_search_min_price)) echo $advance_search_min_price; ?>">
                    </div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group">
                      <label for="advance_search_max_price"><?php echo mlx_get_lang('Frontend Advance Search Max Price'); ?></label>
                      <input type="number" min="0" step="1" class="form-control" 
					  name="options[advance_search_max_price]" id="advance_search_max_price" 
					  value="<?php if(isset($advance_search_max_price)) echo $advance_search_max_price; ?>">
                    </div>
				