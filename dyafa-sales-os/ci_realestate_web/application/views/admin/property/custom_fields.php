<script>
	$(document).ready(function () { 
		$('.add_new_custom_field').click(function() {
			var li_len = $('.custom_field_container ul li').length;
			var n = (li_len + 1);
			/*
			if(li_len >= 1)
			{
				$('.custom_field_container ul li .remove-cf').css('visibility','visible');
			}
			*/
			
			$('.custom_field_container ul').prepend('<li><div class="input-group">'
			+'<input type="text" name="custom_field_name['+n+']" class="form-control cust_field_name" placeholder="Field Title">'
			+'<input type="text" name="custom_field_slug['+n+']" readonly class="form-control cust_field_slug" placeholder="Field Slug">'
			+'<input type="text" onClick="this.select();" readonly class="form-control cust_field_shortcode" placeholder="Shortcode">'
			+'<span class="input-group-addon remove-cf"><i class="fa fa-remove"></i></span>'
			+'</div></li>');
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
			
			$(this).parents('li').find('.cust_field_shortcode').val('');
			
			$(this).parents('li').find('.cust_field_slug').val(slug);
			$(this).parents('li').removeClass('cur_field');
			if(!has_exists)
			{
				
				$(this).parents('li').find('.cust_field_slug').removeClass('has-error');
				if(slug != '')
					$(this).parents('li').find('.cust_field_shortcode').val("<\?php echo get_custom_field('"+slug+"', $single_property); ?>");
				$('#save_publish').attr('disabled',false).removeClass('disabled');
			}
			else if(title != '' && has_exists)
			{
				$(this).parents('li').find('.cust_field_slug').addClass('has-error');
				$('#save_publish').attr('disabled',true).addClass('disabled');
			}
		});
		
		$(document).delegate('.remove-cf','click',function() 
		{
			
			if(confirm("Do you realy want to delete this field?"))
			{
				if(typeof $(this).attr('data-slug') !== 'undefined')
				{
					window.location.href = "<?php echo site_url().'property/delete_cf/'; ?>"+$(this).attr('data-slug');
				}
				else
				{
					$(this).parents('li').remove();
				}
				/*
				var n = $('.custom_field_container ul.todo-list li').length;
				if(n < 2)
				{
					$('.custom_field_container ul.todo-list li .remove-cf').css('visibility','hidden');
				}*/
				return false;
			}
		});
		/*
		var n = $('.custom_field_container ul li').length;
		if(n < 2)
		{
			$('.custom_field_container ul li .remove-cf').css('visibility','hidden');
		}
		*/
		$('.field_type').change(function() {
			var cur_val = $(this).val();
			$(this).parents('li').find('.cf-field-options').remove();
			if(cur_val == 'select' || cur_val == 'radio' || cur_val == 'checkbox'|| cur_val == 'radio-toggle')
			{
				var cloned_field = $('.select-field-option').clone(true);
				$(this).parents('li').find('.remove-cf').before(cloned_field);
			}
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
.custom_field_container .form-control:first-child {
	/*border-right:  1px solid;*/
	/*width:100%;*/
}
.cust_field_slug.has-error{
	border-color: #dd4b39;
	box-shadow: none;
}
.custom_field_container .input-group .cust_field_shortcode{
	margin-bottom:0px;
}
</style>

      
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
		/*
		if(isset($this->site_user_settings [$user_type]))
		{
			$settings = $this->site_user_settings [$user_type];
			foreach($settings as $setting)
			{
				${$setting['name']} = $myHelpers->global->get_option($setting['name']);
			}
		}
		*/
		?>
        <section class="content">
		  <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('admin/property/custom_fields',$attributes); ?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">

			<div class="row">
				<div class="col-md-8">   
				  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
					  <div class="box-body">
					  
						<div class="form-group">
							<button class="btn btn-draft btn-default btn-flat add_new_custom_field">Add New</button>
						</div>
						
						<div class="custom_field_container">
							<?php if(isset($custom_field_list) && !empty($custom_field_list)) { 
							$n=0;
							echo '<ul class="todo-list">';
							foreach($custom_field_list as $cfk => $cfv)
							{
								$n++;
								$shortcode_string = "<\?php echo get_custom_field('".$cfv['slug']."', \$single_property); ?>";
								$shortcode_string = str_replace('<\?','<?',$shortcode_string);
								
							?>
								<li>
									<div class="input-group">
										<input type="text" name="custom_field_name[<?php echo $n; ?>]" value="<?php echo $cfv['title']; ?>" class="form-control cust_field_name" placeholder="Field Title">
										<input type="text" name="custom_field_slug[<?php echo $n; ?>]" readonly value="<?php echo $cfv['slug']; ?>" class="form-control cust_field_slug" placeholder="Field Slug">
										<input type="text" readonly onClick="this.select();" class="form-control cust_field_shortcode" placeholder="Shortcode" value="<?php echo $shortcode_string; ?>">
										<span data-slug="<?php echo $cfv['slug']; ?>" class="input-group-addon remove-cf"><i class="fa fa-remove"></i></span>
								</li>
							<?php } echo '</ul>'; }else{ ?>
								<ul class="todo-list">
									<li>
										<div class="input-group">
										  
										  <input type="text" name="custom_field_name[1]" class="form-control cust_field_name" placeholder="Field Title">
										  <input type="text" name="custom_field_slug[1]" readonly  class="form-control cust_field_slug" placeholder="Field Slug">
										  <input type="text" readonly onClick="this.select();" class="form-control cust_field_shortcode" placeholder="Shortcode">
										  <span class="input-group-addon remove-cf"><i class="fa fa-remove"></i></span>
										 </div>
									</li>
								</ul>
							<?php } ?>
						</div>
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
	  