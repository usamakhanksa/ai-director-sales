
<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add New User'); ?></h1>
  
</section>

<section class="content">
	 <?php 
	
	$attributes = array('name' => 'add_form_post','class' => 'form');		 			
	echo form_open_multipart('admin/user/add_new',$attributes); ?>
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-8">   
		
	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('User Details'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		  </div>
		</div>
		  <div class="box-body">
			
			<?php if( form_error('user_meta[first_name]')) 	  { 	echo form_error('user_meta[first_name]'); 	  } ?>
			<?php if( form_error('user_meta[last_name]')) 	  { 	echo form_error('user_meta[last_name]'); 	  } ?>
			<?php if( form_error('UserType')) 	  { 	echo form_error('UserType'); 	  } ?>
			<?php if( form_error('UserName')) 	  { 	echo form_error('UserName'); 	  } ?>
			<?php if( form_error('Password')) 	  { 	echo form_error('Password'); 	  } ?>
			<?php if( form_error('RepeatPassword')) 	  { 	echo form_error('RepeatPassword'); 	  } ?>
			<div class="row">
			
				<div class="col-md-6">
					<div class="form-group">
					  <label for="FirstName"><?php echo mlx_get_lang('First Name'); ?> <span class="required">*</span></label>
					  <input type="text" class="form-control"  name="user_meta[first_name]" id="FirstName" required
					  value="<?php if(isset($_POST['user_meta["first_name"]'])) echo $_POST['user_meta["first_name"]'];?>">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="LastName"><?php echo mlx_get_lang('Last Name'); ?> <span class="required">*</span></label>
					  <input type="text" class="form-control"  name="user_meta[last_name]" id="LastName" required
					  value="<?php if(isset($_POST['user_meta[last_name]'])) echo $_POST['user_meta[last_name]'];?>">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="UserMobile"><?php echo mlx_get_lang('Mobile No.'); ?> </label>
					  <input type="text" class="form-control" name="user_meta[mobile_no]" id="UserMobile"
					  value="<?php if(isset($_POST['user_meta[mobile_no]'])) echo $_POST['user_meta[mobile_no]'];?>">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="UserEmail"><?php echo mlx_get_lang('Email Address'); ?> <span class="required">*</span></label>
					  <input type="email" class="form-control" id="UserEmail" name="UserEmail" required
					  value="<?php if(isset($_POST['UserEmail'])) echo $_POST['UserEmail'];?>">
					</div>
				</div>
				
				<div class="col-md-12">
					<div class="form-group">
					  <label for="UserAddress"><?php echo mlx_get_lang('Address'); ?></label>
					  <textarea class="form-control" rows="3" id="UserAddress" 
					  name="user_meta[address]" ><?php if(isset($_POST['user_meta[address]'])) echo $_POST['user_meta[address]'];?></textarea>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
					  <label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Photo'); ?></label>
						<!--
						<label class="custom-file-upload">
							<input type="file" accept="image/*" id="att_photo" name="attachments" data-type="photo" data-user-type="user"/>
							<i class="fa fa-cloud-upload"></i> <?php echo mlx_get_lang('Upload Image'); ?>
						</label>
						<progress id="att_photo_progress" value="0" max="100" style="display:none;"></progress>
						<a id="att_photo_link" href="" download="" style="display:none;">
							<img src="">
						</a>
						<a class="remove_img" id="att_photo_remove_img" data-name="att_photo" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
						<input type="hidden" name="user_meta[photo_url]" value="" id="att_photo_hidden">
						-->
						
						<div class="pl_image_container">
							<label class="custom-file-upload" data-element_id="" data-type="user" id="pl_file_uploader_1">
								<?php echo mlx_get_lang('Drop images here'); ?>
								<br />
								<strong><?php echo mlx_get_lang('OR'); ?></strong>
								<br />
								<?php echo mlx_get_lang('Click here to select images'); ?>
							</label>
							<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
							<a class="pl_file_link" href="" download="" style="display:none;">
								<img src="" style="width:50%;">
							</a>
							<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
							<input type="hidden" name="user_meta[photo_url]" value="" class="pl_file_hidden">
						</div>
					</div>
				</div>
				
				<div class="col-md-12">
					<div class="form-group">
					  <label for="description"><?php echo mlx_get_lang('Description');?> </label>
					  <textarea class="form-control ckeditor-element" data-lang_code="en" rows="3" id="description" name="user_meta[description]"
					  ><?php if(isset($_POST['user_meta[description]'])) echo $_POST['user_meta[description]'];?></textarea>
					</div>
				</div>
				
				<div class="col-md-12">
					<div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Login Details'); ?></h3>
					</div><br>
				</div>

				<?php
				$site_users = $myHelpers->config->item("site_users");
				/*print_r($site_users);
				echo " user type ".$user_type;*/
				
				$user_type_exists = false;
				if(isset($user_type) && !empty($user_type) && array_key_exists($user_type , $site_users)){
					
					
					$user_type_exists = true;
					echo " yes ";	
				}
				?>
				
				<div class="col-md-6">
					<div class="form-group">
					  <label for="UserType"><?php echo mlx_get_lang('User Type'); ?>  <span class="required">*</span></label>
					  <select class="form-control user_types"  name="UserType" required id="UserType">
						<option value=""
						<?php if($user_type_exists ){ echo " disabled "; } ?>
						><?php echo mlx_get_lang('Select User Type'); ?></option>
						<?php foreach($site_users as $k => $user){ if($k == 'admin') continue; ?>				
								<option value="<?php echo $k;?>" 
							<?php if(isset($user_type) && $user_type_exists && $user_type != $k){ echo " disabled='disabled' ";	}?>	
								><?php echo $user['title'];?></option>
						<?php } ?>			
					  </select>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
					  <label for="UserName"><?php echo mlx_get_lang('User Name'); ?>  <span class="required">*</span></label>
					  <input type="text" class="form-control" required name="UserName" id="UserName" 
					   value="<?php if(isset($_POST['UserName'])) echo $_POST['UserName'];?>" autocomplete="new-user">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
					  <label for="Password"><?php echo mlx_get_lang('Password'); ?>  <span class="required">*</span></label>
					  <input type="password" class="form-control" required name="Password" id="Password" autocomplete="new-password">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
					  <label for="RepeatPassword"><?php echo mlx_get_lang('Repeat Password'); ?>  <span class="required">*</span></label>
					  <input type="password" class="form-control" required name="RepeatPassword" id="RepeatPassword" autocomplete="new-repeat-password">
					</div>
				</div>
				
				
				<div class="col-md-12">
					<div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Social Media Details'); ?></h3>
					</div><br>
				</div>
				<?php 
				foreach($social_medias as $key => $details){
				?>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="facebook"><?php echo $details['title']; ?></label>
					  <div class="input-group">
					  <span class="input-group-addon">
							<input type="hidden" class="form-control "
					  name="options[<?php echo $key; ?>][icon]"  
					  value="<?php echo $details['fa-icon']; ?>">
						  <i class="fa <?php echo $details['fa-icon']; ?>"></i>
						</span> 
					  <input type="url" class="form-control "
					  name="user_meta[social_media][<?php echo $key; ?>][url]" id="<?php echo $key; ?>" >
					  <input type="hidden" name="user_meta[social_media][<?php echo $key; ?>][title]" 
					  value="<?php echo $details['title']; ?>">
					  <input type="hidden" name="user_meta[social_media][<?php echo $key; ?>][icon]" 
					  value="<?php echo $details['fa-icon']; ?>">
					  </div>
					</div>
				</div>
				<?php
				}
				?>
				
			</div>	
			
			
			
				
			
			
		  </div>
		
	  </div>
</div><!-- end col-md-8-->
  
  <div class="col-md-4">
  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
	  <div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			<!--<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>-->
		  </div>
		</div><!-- /.box-header -->
		<div class="box-body">
			<div class="form-group">
				<div class="radio">
					<label style="padding-left:0px;">
					  <input type="radio" class="flat-green" name="status" id="" value="Y" checked="checked">
					  &nbsp; &nbsp;<?php echo mlx_get_lang('Active'); ?>
					</label>
				</div>
				<div class="radio">
					<label style="padding-left:0px;">
					  <input type="radio" class="flat-red" name="status" id="" value="N" >
					  &nbsp; &nbsp;<?php echo mlx_get_lang('In-Active'); ?>
					</label>
				 </div>
			</div>
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