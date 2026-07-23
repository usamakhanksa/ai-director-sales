
<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Settings'); ?></h1>
  
</section>

<section class="content">
	 <?php 
	
	$attributes = array('name' => 'add_form_post','class' => 'form');		 			
	echo form_open_multipart('',$attributes); ?>
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-8">   
		
	  <div class="box box-<?php echo get_skin_class(); ?>">
		
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Active Usertypes on Register'); ?></h3>
		  
		</div>
		  <div class="box-body">
			
			<?php if( form_error('user_meta[first_name]')) 	  { 	echo form_error('user_meta[first_name]'); 	  } ?>
			<?php if( form_error('user_meta[last_name]')) 	  { 	echo form_error('user_meta[last_name]'); 	  } ?>
			<?php if( form_error('UserType')) 	  { 	echo form_error('UserType'); 	  } ?>
			<?php if( form_error('UserName')) 	  { 	echo form_error('UserName'); 	  } ?>
			<?php if( form_error('Password')) 	  { 	echo form_error('Password'); 	  } ?>
			<?php if( form_error('RepeatPassword')) 	  { 	echo form_error('RepeatPassword'); 	  } ?>
			<div class="row">
			
				
				<div class="col-md-12">
					<div class="form-group">
					  <label for="UserAddress"><?php echo mlx_get_lang('Address'); ?></label>
					  <textarea class="form-control" rows="3" id="UserAddress" 
					  name="user_meta[address]" ><?php if(isset($_POST['user_meta[address]'])) echo $_POST['user_meta[address]'];?></textarea>
					</div>
				</div>
				
				
				<?php
				$site_users = $myHelpers->config->item("site_users");
				?>
				
				<div class="col-md-6">
					<div class="form-group">
					  <label for="UserType"><?php echo mlx_get_lang('User Type'); ?>  <span class="required">*</span></label>
					  <select class="form-control user_types"  name="UserType[]" multiple="multiple" required id="UserType">
						<option value=""><?php echo mlx_get_lang('Select User Type'); ?></option>
						<?php foreach($site_users as $k => $user){ if($k == 'admin') continue; ?>				
								<option value="<?php echo $k;?>"><?php echo $user['title'];?></option>
						<?php } ?>			
					  </select>
					</div>
				</div>
				
				
				
				
			</div>	
			
			
			
				
			
			
		  </div>
		
	  </div>
</div><!-- end col-md-8-->
  
  <div class="col-md-4">
  <div class="box box-<?php echo get_skin_class(); ?>">
	  <div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			<!--<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>-->
		  </div>
		</div><!-- /.box-header -->
		<div class="box-body">
			
			
		</div>
		 <div class="box-footer">
			<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
		  </div>
	  </div><!-- /.box -->	  
  </div><!-- end col-md-4-->
  
  
  </div><!-- end row 1-->	  
  
  
  
	  
	  </form>
</section>
</div>