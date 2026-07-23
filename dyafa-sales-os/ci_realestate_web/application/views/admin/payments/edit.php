<?php
	
	if(isset($blog_meta) && $blog_meta->num_rows() > 0)
	{
		
		$row = $blog_meta->row();
		
		$p_id = $row->package_id;
		$title = $row->package_name;
		$price = $row->package_price;
		$currency = $row->package_currency;
		$number_listing_limit = $row->number_listing_limit;
		$auto_active_property = $row->auto_active_property;
		$days_limit_for_property_active = $row->days_limit_for_property_active;
		$package_life = $row->package_life;
		$number_featured_properties = $row->number_featured_properties;
		$days_limit__featured_properties = $row->days_limit__featured_properties;
		$applicable_for = $row->applicable_for;
		$limit_purchase_by_acc = $row->limit_purchase_by_acc;
		$purchase_button_text = $row->purchase_button_text;
		$package_order = $row->package_order;
		$is_default = $row->is_default;
	}
	
?>
      <?php $this->load->view("default/header-top");?>
      
	  <?php $this->load->view("default/sidebar-left");?>
   
      <div class="content-wrapper">
        <section class="content-header">
          <h1><?php echo mlx_get_lang('Edit Package'); ?> </h1>
          <?php echo validation_errors(); ?>
        </section>
		<section class="content">
             <?php  $attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('packages/edit',$attributes); ?>
			<input type="hidden" name="b_id" class="b_id" value="<?php echo $myHelpers->EncryptClientId($p_id); ?>">
			
				<div class="row">
					<div class="col-md-8">   
						<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
									<div class="box-header with-border">
										<h3 class="box-title"><?php echo mlx_get_lang('Package Details'); ?></h3>
										<div class="box-tools pull-right">
											<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
											
										</div>
									</div>
								
									<div class="box-body">
										
												<div class="form-group">
												<label for="package"><?php echo mlx_get_lang('Package Name'); ?> <span class="text-red">*</span></label>
												<input type="text" class="form-control" required="required" name="package" id="package" 
												value="<?php if(isset($title)){ echo $title; } ?>">
												</div>
												
												<div class="form-group">
												<label for="packages_price"><?php echo mlx_get_lang('Package Price'); ?><span class="text-red">*</span></label>

												<input type="number" class="form-control"  id="packages_price" name="packages_price" 
												value="<?php if(isset($price)){ echo $price; } ?>">
												</div>
												
												<div class="form-group">
											<label for="currency_code"><?php echo mlx_get_lang('Category'); ?> <span class="required">*</span></label>
											
											<select class="form-control select2_elem" name="currency_code" id="currency_code" required>
												<option value=""><?php echo mlx_get_lang('Select Currency Code'); ?></option>
												<?php 
												if(isset($blog_categories) && $blog_categories->num_rows() > 0)
												{
													foreach($blog_categories->result() as $b_row)
													{
														echo '<option value="'.$myHelpers->global_lib->EncryptClientId($b_row->c_id).'">'.ucfirst($b_row->title).'</option>';
													}
												}
												?>
											</select>
											</div>		
											<div class="form-group">
												<label for="number_of_listing"><?php echo mlx_get_lang('Number Of Listing'); ?><span class="text-red">*</span></label>
												<input type="number" class="form-control"  id="number_of_listing" name="number_of_listing" 
												value="<?php if(isset($number_listing_limit)){ echo $number_listing_limit; } ?>">
											</div>	
											<div class="form-group">
												<label for="auto_active"><?php echo mlx_get_lang('Auto Active Property'); ?><span class="text-red">*</span></label>
												&nbsp;&nbsp;&nbsp;<input type="checkbox" id="" name="auto_active" <?php if(isset($auto_active_property)){ echo 'checked  '; }?> /> 
											</div>		
											<div class="form-group">
												<label for="days_limit_for_property_property"><?php echo mlx_get_lang('Days Limit For Property'); ?><span class="text-red">*</span></label>
												<input type="number" class="form-control"  id="days_limit_for_property_property" name="days_limit_for_property_property" 
												value="<?php if(isset($days_limit_for_property_active)){ echo $days_limit_for_property_active; } ?>">
											</div>	

											<div class="form-group">
												<label for="package_lifetime"><?php echo mlx_get_lang('Package Life Time'); ?><span class="text-red">*</span></label></label>
												<input type="number" class="form-control"  id="package_lifetime" name="package_lifetime" 
												value="<?php if(isset($package_life)){ echo $package_life; } ?>">
											</div>	

											<div class="form-group">
												<label for="number_featered_property"><?php echo mlx_get_lang('Number Of Featured Property'); ?><span class="text-red">*</span></label>
												<input type="number" class="form-control"  id="number_featered_property" name="number_featered_property" 
												value="<?php if(isset($number_featured_properties)){ echo $number_featured_properties; } ?>">
											</div>	

											<div class="form-group">
												<label for="days_limit_for_featured_property"><?php echo mlx_get_lang('Days Limit For Featured Property'); ?><span class="text-red">*</span></label>
												<input type="number" class="form-control"  id="days_limit_for_featured_property" name="days_limit_for_featured_property" 
												value="<?php if(isset($days_limit__featured_properties)){ echo $days_limit__featured_properties; } ?>">
											</div>

											<div class="form-group">
												<label for="applicable_for"><?php echo mlx_get_lang('Applicable For'); ?><span class="text-red">*</span></label>
												&nbsp;&nbsp;&nbsp;<input type="checkbox" id="" name="applicable_for[]" value="agent"  <?php if(isset($applicable_for) && $applicable_for =='agent'){ echo 'checked'; }?>/> Agent
												&nbsp;&nbsp;&nbsp;<input type="checkbox" id="" name="applicable_for[]" value="builder" <?php if(isset($applicable_for) && $applicable_for =='builder'){ echo 'checked'; }?> /> Builder
											</div>
											

											<div class="form-group">
												<label for="limit_purchase_by_user"><?php echo mlx_get_lang('Limit Purchase By Account'); ?><span class="text-red">*</span></label></label>
												<input type="number" class="form-control"  id="limit_purchase_by_user" name="limit_purchase_by_user" 
												value="<?php if(isset($limit_purchase_by_acc)){ echo $limit_purchase_by_acc; } ?>">
											</div>

											<div class="form-group">
												<label for="purchase_button_text"><?php echo mlx_get_lang('Purchase button Text'); ?> <span class="text-red">*</span></label>
												<input type="text" class="form-control" required="required" name="purchase_button_text" id="purchase_button_text" 
												value="<?php if(isset($purchase_button_text)){ echo $purchase_button_text; } ?>">
											</div>
											<div class="form-group">
												<label for="package_order"><?php echo mlx_get_lang('Package Order'); ?><span class="text-red">*</span></label></label>
												<input type="number" class="form-control"  id="package_order" name="package_order" 
												value="<?php if(isset($package_order)){ echo $package_order; } ?>">
											</div>
										
										</div>
											
										
									</div>
							</div>
					 
			
					<div class="col-md-4">
						<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								
							</div>
							</div>
							<div class="box-body">
							
							<div class="form-group" >
								<label for="is_default">Is default </label>
									<div class="radio_toggle_wrapper">
									<input type="radio" id="is_default_yes" value="Y"   name="is_default" class="toggle-radio-button" <?php if(isset($is_default) && $is_default=='Y'){ echo 'checked="checked"';}?>>
									<label for="is_default_yes">Yes</label>
									
									<input type="radio" id="is_default_no"  Value="N" name="is_default"  class="toggle-radio-button" <?php if(isset($is_default) && $is_default=='N'){ echo 'checked="checked"';}?>>
									<label for="is_default_no">No</label>
								</div>
							</div> 
								
							</div>
							<div class="box-footer">
								<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
							</div>
						</div>
						</div>
						
					</div> 

				</div>				
			</form>
        </section>
      </div>