
<?php 
	if(isset($query) && $query->num_rows() > 0)
	{
		$row = $query->row();
		
		$UserEmail = $row->user_email;
		$user_ID = $row->user_id;
		$status = $row->user_status;
	}
	else
	{
		$UserEmail = "";
		$user_ID = '';
		$status = 'N';
	}
?>	  
	  
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-edit"></i> <?php echo mlx_get_lang('Edit User'); ?> </h1>
          
        </section>

        <section class="content">
			 <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('admin/user/edit',$attributes); ?>
			   <input type="hidden" name="user_id" value="<?php if(isset($user_id) && !empty($user_id)) echo $user_id; ?>">
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-<?php echo get_skin_class(); ?>">
				
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('User Details'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				 </div>
                </div>
                  <div class="box-body">
                    
					<?php if( form_error('user_meta[first_name]')) 	  { 	echo form_error('user_meta[first_name]'); 	  } ?>
					<?php if( form_error('user_meta[last_name]')) 	  { 	echo form_error('user_meta[last_name]'); 	  } ?>
					<?php if( form_error('user_meta[mobile_no]')) 	  { 	echo form_error('user_meta[mobile_no]'); 	  } ?>
					<div class="row">
					
						<div class="col-md-6">
							<div class="form-group">
							  <label for="FirstName"><?php echo mlx_get_lang('First Name'); ?> <span class="required">*</span></label>
							  <input type="text" class="form-control"  name="user_meta[first_name]" id="FirstName" required
							  value="<?php if(isset($_POST['user_meta["first_name"]'])) 
												echo $_POST['user_meta["first_name"]'];
											else 
												echo get_user_meta($user_ID,'first_name');
									 ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="LastName"><?php echo mlx_get_lang('Last Name'); ?> <span class="required">*</span></label>
							  <input type="text" class="form-control"  name="user_meta[last_name]" id="LastName" required
							  value="<?php if(isset($_POST['user_meta[last_name]'])) 
												echo $_POST['user_meta[last_name]'];
											else 
												echo get_user_meta($user_ID,'last_name');
									?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserMobile"><?php echo mlx_get_lang('Mobile No.'); ?></label>
							  <input type="text" class="form-control" name="user_meta[mobile_no]" id="UserMobile"
							  value="<?php if(isset($_POST['user_meta[mobile_no]'])) 
												echo $_POST['user_meta[mobile_no]'];
											else 
												echo get_user_meta($user_ID,'mobile_no');
									?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserEmail"><?php echo mlx_get_lang('Email Address'); ?>  <span class="required">*</span></label>
							  <input type="email" class="form-control" id="UserEmail" name="UserEmail" required
							  value="<?php if(isset($_POST['UserEmail'])) 
												echo $_POST['UserEmail'];
											else if(isset($UserEmail) && !empty($UserEmail))
												echo $UserEmail;
									?>">
							</div>
						</div>
						
						<div class="col-md-12">
							<div class="form-group">
							  <label for="UserAddress"><?php echo mlx_get_lang('Address'); ?></label>
							  <textarea class="form-control" rows="3" id="UserAddress" name="user_meta[address]" 
							  ><?php if(isset($_POST['user_meta[address]'])) echo $_POST['user_meta[address]']; 
										else if($address = get_user_meta($user_ID,'address')) echo $address;?></textarea>
							</div>
						</div>
						
						<?php 
							$image = '';
							if(get_user_meta($user_ID,'photo_url'))
							{
								$image = get_user_meta($user_ID,'photo_url');
							}
						?>
						
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Photo'); ?></label>
								
								
								<?php 
								$thumb_photo = $myHelpers->global_lib->get_image_type('../uploads/user/',$image,'thumb'); ?>
								<div class="pl_image_container">
									<label class="custom-file-upload" data-element_id="<?php if(isset($user_ID) && !empty($user_ID)) 
											echo $myHelpers->EncryptClientId($user_ID); ?>" data-type="user" id="pl_file_uploader_1" 
										<?php if(isset($thumb_photo) && !empty($thumb_photo)) { echo 'style="display:none;"';}?>>
										<?php echo mlx_get_lang('Drop images here'); ?>
										<br>
										<strong><?php echo mlx_get_lang('OR'); ?></strong>
										<br>
										<?php echo mlx_get_lang('Click here to select images'); ?>
									</label>
									<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
									<?php if(isset($thumb_photo) && !empty($thumb_photo)) { ?>
									
										<a class="pl_file_link" href="<?php echo base_url().'../uploads/user/'.$image; ?>" 
										download="<?php echo $image; ?>" style="">
											<img src="<?php echo base_url().'../uploads/user/'.$thumb_photo; ?>"  style="width:50%;">
										</a>
									
										<a class="pl_file_remove_img" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
									<?php }else{ ?>
										<a class="pl_file_link" href="" download="" style="display:none;">
											<img src=""  style="width:50%;">
										</a>
										<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
									<?php } ?>
									<input type="hidden" name="user_meta[photo_url]" value="<?php if(isset($image) && !empty($image)) { echo $image;}?>" 
									class="pl_file_hidden">
								</div>
							</div>
						</div>
						
						<div class="col-md-12">
							<div class="form-group">
							  <label for="description"><?php echo mlx_get_lang('Description');?> </label>
							  <textarea class="form-control ckeditor-element" data-lang_code="en" rows="3" id="description" 
									name="user_meta[description]"
							  ><?php if(isset($_POST['user_meta[description]'])) echo $_POST['user_meta[description]']; 
									else if($description = get_user_meta($user_ID,'description'))
												echo $description;
							  ?></textarea>
							</div>
						</div>
						
						<div class="col-md-12">
							<div class="box-header with-border">
							  <h3 class="box-title"><?php echo mlx_get_lang('Login Details'); ?></h3>
							</div><br>
						</div>
				<?php
						$site_users = $myHelpers->config->item("site_users");
						
				?>		
						<div class="col-md-6">
							<div class="form-group">
							  <label for="user_type"><?php echo mlx_get_lang('User Type'); ?></label>
							  <select class="form-control"  name="user_type" required id="user_type" >
								<option value=""><?php echo mlx_get_lang('Select User Type'); ?></option>
					<?php
						foreach($site_users as $k => $user){
							///if($k == 'admin') continue;
					?>				
							<option value="<?php echo $k;?>" 
							<?php if($row->user_type == $k) echo 'selected="selected"';?>  ><?php echo $user['title'];?></option>
					<?php	}	?>			
								
							  </select>
							</div>
						</div> 
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="UserName"><?php echo mlx_get_lang('User Name'); ?></label>
							  <input type="text" class="form-control" required name="UserName" readonly id="UserName" 
							  value="<?php if(isset($_POST['UserName'])) echo $_POST['UserName']; 
													else if(isset($row->user_name) && !empty($row->user_name)) echo $row->user_name;?>">
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="Password"><?php echo mlx_get_lang('Reset Password'); ?></label>
							  <input type="password" class="form-control" name="Password" id="Password" autocomplete="new-password">
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="RepeatPassword"><?php echo mlx_get_lang('Repeat Password'); ?></label>
							  <input type="password" class="form-control" name="RepeatPassword" id="RepeatPassword" autocomplete="new-repeat-password">
							</div>
						</div>
						
						
						
					</div>	
					
                    
                  </div>
                
              </div>
			  
				 <?php 
						$args = ['user_ID' => $user_ID];
						do_action("cms_admin_user_profile_box" , $args);
				  ?>
			 
			 
		  </div><!-- end col-md-8-->
		  
		  <div class="col-md-4">
		  <div class="box box-<?php echo get_skin_class(); ?>">
			  <div class="box-header with-border">
                  <h3 class="box-title"> <?php echo mlx_get_lang('Status'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					<!--<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>-->
				  </div>
                </div><!-- /.box-header -->
				<div class="box-body">
					<div class="form-group">
						<div class="radio">
							<label style="padding-left:0px;">
							  <input type="radio" class="flat-green" name="status" id="" value="Y" 
							  <?php if($status == 'Y') echo 'checked="checked"'; ?>>
							  &nbsp; &nbsp;<?php echo mlx_get_lang('Active'); ?>
							</label>
						</div>
						<div class="radio">
							<label style="padding-left:0px;">
							  <input type="radio" class="flat-red" name="status" id="" value="N" 
							  <?php if($status == 'N') echo 'checked="checked"'; ?>>
							  &nbsp; &nbsp;<?php echo mlx_get_lang('In-Active'); ?>
							</label>
						 </div>
					</div>
				</div>
				 <div class="box-footer">
					<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
                  </div>
			  </div><!-- /.box -->	  
		  </div><!-- end col-md-4-->
		  
		  
		  </div><!-- end row-->	  
			  
			  
			  
			  
			  </form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      