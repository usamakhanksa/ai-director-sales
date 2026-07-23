<?php $this->load->view("default/header-top");?>
<?php $this->load->view("default/sidebar-left");?>


<div class="content-wrapper">
<section class="content-header">
  <h1><?php echo mlx_get_lang('Add New Package'); ?> </h1>
  <?php echo validation_errors(); ?>
</section>

<section class="content">
	<?php 
	$attributes = array('name' => 'add_form_post','class' => 'form');		 			
	echo form_open_multipart('payments/add_new',$attributes); ?>
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-8">   
	   
	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('package Details'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		  </div>
		</div>
		  <div class="box-body">
				
				<div class="form-group">
				  <label for="package"><?php echo mlx_get_lang('Package Name'); ?> <span class="text-red">*</span></label>
				  <input type="text" class="form-control" required="required" name="package" id="package">
				</div>
				
				<div class="form-group">
				  <label for="packages_price"><?php echo mlx_get_lang('Package Price'); ?><span class="text-red">*</span></label>
				  <input type="number" class="form-control short-description-element"  id="packages_price" name="packages_price" 
				   maxlength="<?php //echo $short_desc_limit; ?>">
				  <!-- <span class="rchars" id="rchars"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?> -->
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
				<input type="number" class="form-control"  id="number_of_listing" name="number_of_listing">
			</div>	
			<div class="form-group">
				<label for="auto_active"><?php echo mlx_get_lang('Auto Active Property'); ?><span class="text-red">*</span></label>
				 &nbsp;&nbsp;&nbsp;<input type="checkbox" id="" name="auto_active" value="true" /> 
			</div>		
			<div class="form-group">
				<label for="days_limit_for_property_property"><?php echo mlx_get_lang('Days Limit For Property'); ?><span class="text-red">*</span></label>
				<input type="number" class="form-control"  id="days_limit_for_property_property" name="days_limit_for_property_property">
			</div>	

			<div class="form-group">
				<label for="package_lifetime"><?php echo mlx_get_lang('Package Life Time'); ?><span class="text-red">*</span></label></label>
				<input type="number" class="form-control"  id="package_lifetime" name="package_lifetime">
			</div>	

			<div class="form-group">
				<label for="number_featered_property"><?php echo mlx_get_lang('Number Of Featured Property'); ?><span class="text-red">*</span></label>
				<input type="number" class="form-control"  id="number_featered_property" name="number_featered_property">
			</div>	

			<div class="form-group">
				<label for="days_limit_for_featured_property"><?php echo mlx_get_lang('Days Limit For Featured Property'); ?><span class="text-red">*</span></label>
				<input type="number" class="form-control"  id="days_limit_for_featured_property" name="days_limit_for_featured_property">
			</div>

			<div class="form-group">
				<label for="applicable_for"><?php echo mlx_get_lang('Applicable For'); ?><span class="text-red">*</span></label>
				&nbsp;&nbsp;&nbsp;<input type="checkbox" id="" name="applicable_for[]" value="agent" /> Agent
				&nbsp;&nbsp;&nbsp;<input type="checkbox" id="" name="applicable_for[]" value="builder" /> Builder
			</div>
			

			<div class="form-group">
				<label for="limit_purchase_by_user"><?php echo mlx_get_lang('Limit Purchase By Account'); ?><span class="text-red">*</span></label></label>
				<input type="number" class="form-control"  id="limit_purchase_by_user" name="limit_purchase_by_user">
			</div>

			<div class="form-group">
				  <label for="purchase_button_text"><?php echo mlx_get_lang('Purchase button Text'); ?> <span class="text-red">*</span></label>
				  <input type="text" class="form-control" required="required" name="purchase_button_text" id="purchase_button_text">
			</div>
			<div class="form-group">
				<label for="package_order"><?php echo mlx_get_lang('Package Order'); ?><span class="text-red">*</span></label></label>
				<input type="number" class="form-control"  id="package_order" name="package_order">
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
			<label for="publish_on"><?php echo mlx_get_lang('Publish On'); ?> <span class="text-red">*</span></label>
			<input type="text" class="form-control publish_on" required="required" name="publish_on" readonly id="publish_on" 
			data-format="<?php echo $myHelpers->global_lib->get_option('default_date_format'); ?>"
			value="<?php echo $myHelpers->global_lib->get_date_from_timestamp();?>">
			
			<br/>
			<div class="form-group" >
				<label for="is_default">Is default <span class="text-red">*</span></label>
					<div class="radio_toggle_wrapper">
					<input type="radio" id="is_default_yes" value="Y"   name="is_default" class="toggle-radio-button">
					<label for="is_default_yes">Yes</label>
					
					<input type="radio" id="is_default_no"  Value="N" name="is_default" 	checked="checked" class="toggle-radio-button">
					<label for="is_default_no">No</label>
				</div>
			</div> 

		</div>
		<div class="box-footer">
			<button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right submit-form-btn" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
		</div>
	  </div>

	
	<!-- <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		  <div class="box-header with-border">
			  <h3 class="box-title"><?php echo mlx_get_lang('Blog Image'); ?></h3>
			  <div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				
			  </div>
			</div>
		<div class="box-body blog-image-container">
			<label class="custom-file-upload">
				<input type="file" accept="image/*" id="att_photo" name="attachments" data-type="photo" data-user-type="blog"/>
				<i class="fa fa-cloud-upload"></i> <?php echo mlx_get_lang('Upload Image'); ?>
			</label>
			<progress id="att_photo_progress" value="0" max="100" style="display:none;"></progress>
			<a id="att_photo_link" href="" download="" style="display:none;">
				<img src="">
			</a>
			<a class="remove_img" id="att_photo_remove_img" data-name="att_photo" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
			<input type="hidden" name="blog_image" value="" id="att_photo_hidden">
		</div>
	  </div> -->
	
  </div>
  
  </div>
	  </form>
</section>
</div>