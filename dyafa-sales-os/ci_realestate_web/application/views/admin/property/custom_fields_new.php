<script>
	$(document).ready(function () { 
		$('.add_new_custom_field').click(function() {
			$('.select2_elem').select2('destroy');
			
			$('.custom_field_container ul').append('<li>'
				+'<div class="input-group">'
					+'<span class="input-group-addon handle"><i class="fa fa-arrows"></i></span>'
					+'<input type="text" name="custom_field_name[]" class="form-control cust_field_name" placeholder="Field Title">'
					+'<input type="text" name="custom_field_slug[]" readonly  class="form-control cust_field_slug" placeholder="Field Slug">'
				    +'<select name="custom_field_type[]"  class="select2_elem field_type">'
						+'<option value="">Select Field Type</option>'
						+'<option value="text">Text</option>'
						+'<option value="url">URL </option>'
						+'<option value="select">Select </option>'
						+'<option value="email">Email </option>'
						+'<option value="number">Number </option>'
						+'<option value="checkbox">Checkbox </option>'
						+'<option value="radio">Radio </option>'
						+'<option value="textarea">Textarea </option>'
					+'</select>'
					+'<input type="hidden" class="cf-field-options" name="custom_field_option[]">'
					+'<span class="input-group-addon remove-cf"><i class="fa fa-remove"></i></span>'
				+'</div>'
			+'</li>');
			
			
			$('.select2_elem').select2({
				width:'100%'
			});
			var li_len = $('.custom_field_container ul li').length;
			if(li_len > 1)
			{
				$('.custom_field_container ul li .remove-cf').css('visibility','visible');
			}
			return false;
		});
		
		$(document).delegate('.cust_field_name','keyup change click',function() {
			var title = $(this).val();
			$(this).parents('li').addClass('cur_field');
			var slug = '';
			if(title != '')
			{
				title = title.replace(/\s\s+/g, '_');
				slug = '_'+title.toLowerCase().replace(/ /g,'_').replace(/[^\w-]+/g,'');
				slug = slug.replace(/_+$/, "");
			}
			var has_exists = false;
			
			$('.custom_field_container ul li').each(function() {
				if(!$(this).hasClass('cur_field') && $(this).find('.cust_field_slug').val() == slug)
				{
					has_exists = true;
				}
			});
			
			$(this).parents('li').find('.cust_field_slug').val(slug);
			$(this).parents('li').removeClass('cur_field');
			if(!has_exists)
			{
				$(this).parents('li').find('.cust_field_slug').removeClass('has-error');
			}
			else if(title != '' && has_exists)
			{
				$(this).parents('li').find('.cust_field_slug').addClass('has-error');
			}
		});
		
		$(document).delegate('.remove-cf','click',function() {
			var opt = confirm('Do you realy want to delete this?');
			if(opt)
			{
				if(typeof $(this).attr('data-slug') !== 'undefined')
				{
					window.location.href = "<?php echo site_url().'property/delete_cf/'; ?>"+$(this).attr('data-slug');
				}
				else
				{
					$(this).parents('li').remove();
				}
				var n = $('.custom_field_container ul li').length;
				if(n < 2)
				{
					$('.custom_field_container ul li .remove-cf').css('visibility','hidden');
				}
			}
			return false;
		});
		
		var n = $('.custom_field_container ul li').length;
		if(n < 2)
		{
			$('.custom_field_container ul li .remove-cf').css('visibility','hidden');
		}
		
		$(document).delegate('.field_type','change',function() {
			
			var cur_val = $(this).val();
			$(this).parents('li').find('.cf-field-options').remove();
			if(cur_val == 'select' || cur_val == 'radio' || cur_val == 'checkbox'|| cur_val == 'radio-toggle')
			{
				var cloned_field = $('.select-field-option').clone(true);
				cloned_field.removeClass('select-field-option').show();
				$(this).parents('li').find('.remove-cf').before(cloned_field);
			}
			else
			{
				var cloned_field = $('.select-field-option-empty').clone(true);
				cloned_field.removeClass('select-field-option-empty');
				$(this).parents('li').find('.remove-cf').before(cloned_field);
			}
			
		});
		
		$(".todo-list").sortable({
			placeholder: "sort-highlight",
			handle: ".handle",
			forcePlaceholderSize: true,
			zIndex: 999999
		  });
		
	}); 
</script>

<style>
.custom_field_container .form-control {
	margin-bottom:5px;
}
.todo-list > li .text {
	margin-bottom: 0;
}
.todo-list > li .tools {
	margin: 6px 0;
}
.cust_field_slug.has-error{
	border-color: #dd4b39;
	box-shadow: none;
}
.todo-list .handle{
	margin:0px;
	display:table-cell;
}
.cf-field-options textarea{
	margin-bottom:0px !important;
}
</style>

<?php $this->load->view("default/header-top"); ?>
<?php $this->load->view("default/sidebar-left"); ?>
      
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-server"></i> <?php echo mlx_get_lang('Custom Fields'); ?> </h1>
		  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
				{
					echo $_SESSION['msg'];
					unset($_SESSION['msg']);
				}
		?> 
        </section>
		<?php 
		if(isset($this->site_user_settings [$user_type]))
		{
			$settings = $this->site_user_settings [$user_type];
			foreach($settings as $setting)
			{
				${$setting['name']} = $myHelpers->global->get_option($setting['name']);
			}
		}
		?>
        <section class="content">
		  <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('property/custom_fields',$attributes); ?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">

			<div class="row">
				<div class="col-md-8">   
				  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
					  <div class="box-body">
						
						<div class="custom_field_container">
							<?php if(isset($custom_field_list) && !empty($custom_field_list)) { 
							$n=-1;
							echo '<ul class="todo-list dynamic-fields">';
							foreach($custom_field_list as $cfk => $cfv)
							{
								$n++;
							?>
								<li>
									<div class="input-group">
									  <span class="input-group-addon handle"><i class="fa fa-arrows"></i></span>
									  <div>
											
											<div class="col-md-4 ">
												<label for=""><?php echo mlx_get_lang('Field Title'); ?></label>
												<input type="text" name="custom_field_name[]" class="form-control cust_field_name" value="<?php echo $cfv['title']; ?>" placeholder="Field Title">
											</div>
											<div class="col-md-4 ">
												<label for=""><?php echo mlx_get_lang('Field Slug'); ?></label>
												<input type="text" name="custom_field_slug[]" readonly  class="form-control cust_field_slug" placeholder="Field Slug" value="<?php echo $cfv['slug']; ?>" >
											</div> 
											<div class="col-md-4 ">
												<label for=""><?php echo mlx_get_lang('Is Required?'); ?></label>
											    <div class="radio_toggle_wrapper ">
													<input type="radio" id="custom_field_required_<?php echo $n; ?>_yes" value="Y" 
													<?php if($cfv['is_req'] == 'Y') echo 'checked="checked"'; ?>
													<?php 
													if(isset($enable_homepage_section) && $enable_homepage_section == 'Y')  
													{ echo ' checked="checked" '; }
													?> name="custom_field_required[<?php echo $n; ?>]" 
													class="toggle-radio-button">
													<label for="custom_field_required_<?php echo $n; ?>_yes"><?php echo mlx_get_lang('Yes'); ?></label>
													
													<input type="radio" id="custom_field_required_<?php echo $n; ?>_no" 
													<?php if(!isset($cfv['is_req']) || ($cfv['is_req'] == 'N') || $cfv['is_req'] == '' ) echo 'checked="checked"'; ?>
													<?php 
													if((isset($enable_homepage_section) && $enable_homepage_section == 'N')|| 
													!isset($enable_homepage_section))
													{ echo ' checked="checked" '; }
													?> value="N" name="custom_field_required[<?php echo $n; ?>]" 
													class="toggle-radio-button">
													<label for="custom_field_required_<?php echo $n; ?>_no"><?php echo mlx_get_lang('No'); ?></label>
												</div>
											</div>
											<div class="col-md-12">
												<label for=""><?php echo mlx_get_lang('Field Type'); ?></label>
												<select name="custom_field_type[]"  class="select2_elem field_type">
													<option value="">Select Field Type</option>
													<option value="text" <?php if($cfv['type'] == 'text') echo 'selected="selected"'; ?>>Text</option>
													<option value="url"<?php if($cfv['type'] == 'url') echo 'selected="selected"'; ?>>URL </option>
													<option value="select"<?php if($cfv['type'] == 'select') echo 'selected="selected"'; ?>>Select </option>
													<option value="email"<?php if($cfv['type'] == 'email') echo 'selected="selected"'; ?>>Email </option>
													<option value="number"<?php if($cfv['type'] == 'number') echo 'selected="selected"'; ?>>Number </option>
													<option value="checkbox"<?php if($cfv['type'] == 'checkbox') echo 'selected="selected"'; ?>>Checkbox </option>
													<option value="radio"<?php if($cfv['type'] == 'radio') echo 'selected="selected"'; ?>>Radio </option>
													<option value="textarea"<?php if($cfv['type'] == 'textarea') echo 'selected="selected"'; ?>>Textarea </option>
											  </select>
											  
											  <?php 
											  if($cfv['type'] ==  'select' || $cfv['type'] == 'radio' || $cfv['type'] == 'checkbox'|| $cfv['type'] == 'radio-toggle')
											  {
											  ?>
												<div class="cf-field-options" style="margin-top:5px;">
													<label for=""><?php echo mlx_get_lang('Field Values'); ?></label>
													<textarea class="form-control" name="custom_field_option[]" rows="3" placeholder="Option 1: Value 1, Option 2 : Value 2"><?php echo $cfv['options']; ?></textarea>
												</div>
											  <?php }else{ ?>
												<input type="hidden" class="select-field-option-empty cf-field-options" name="custom_field_option[]">
											  <?php } ?>
											</div>
										  
									  </div>
									  <span class="input-group-addon remove-cf" data-slug="<?php echo $cfv['slug']; ?>"><i class="fa fa-remove"></i></span>
									 </div>
								</li>
								
							<?php } echo '</ul>'; }else{ ?>
							
								<ul class="todo-list">
									<li>
										<div class="input-group">
										  <span class="input-group-addon handle"><i class="fa fa-arrows"></i></span>
										  
										  <input type="text" name="custom_field_name[]" class="form-control cust_field_name" placeholder="Field Title">
										  <input type="text" name="custom_field_slug[]" readonly  class="form-control cust_field_slug" placeholder="Field Slug">
										  
										  <select name="custom_field_type[]"  class="select2_elem field_type">
												<option value="">Select Field Type</option>
												<option value="text">Text</option>
												<option value="url">URL </option>
												<option value="select">Select </option>
												<option value="email">Email </option>
												<option value="number">Number </option>
												<option value="checkbox">Checkbox </option>
												<option value="radio">Radio </option>
												<option value="textarea">Textarea </option>
										  </select>
										  
										  <span class="input-group-addon remove-cf"><i class="fa fa-remove"></i></span>
										 </div>
									</li>
								</ul>
							<?php } ?>
						</div>
					  </div>
					  <div class="box-footer">
						<button class="btn btn-draft btn-default btn-flat add_new_custom_field pull-right">Add New</button>
					  </div>
				  </div>
				</div>
		  
			  <div class="col-md-4">
				  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
					<div class="box-header with-border">
					  <h3 class="box-title"> Status</h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					  </div>
					</div>
					<div class="box-footer">
						<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish">Save</button>
					</div>
				  </div>
			  </div>
		  </div>
	  </form>
	</section>
</div>
	  
<div class="select-field-option cf-field-options" style="margin-top:5px; display:none;">
	<textarea class="form-control" name="custom_field_option[]" rows="3" placeholder="Option 1: Value 1, Option 2 : Value 2"></textarea>
</div>


<input type="hidden" class="select-field-option-empty cf-field-options" name="custom_field_option[]">
	
<!--
<option value="date">Date </option>
<option value="file-image">Image Upload </option>
<option value="file-image-ajax">Image Upload Ajax </option>
-->