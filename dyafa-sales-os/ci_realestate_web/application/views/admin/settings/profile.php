
<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('Profile'); ?> </h1>
		<?php echo validation_errors(); 
			if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
		?>
	</section>

	<section class="content">
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'form');		 			
		echo form_open_multipart('',$attributes); 
		
		$user_ID = $this->session->userdata('user_id');
		$user_type = $this->session->userdata('user_type');
		?>
		<input type="hidden" name="user_id" class="user_id" value="<?php echo $user_ID; ?>">	
		<div class="row">
		<div class="col-md-8">   
		   
		  <div class="box box-<?php echo get_skin_class(); ?>">
			<div class="box-header with-border">
			  <h3 class="box-title"><?php echo mlx_get_lang('Profile'); ?></h3>
			  <div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			  </div>
			</div>
				
			
			  <div class="box-body">
				<?php 
				$sal_lang = '';
				if(isset($_POST['user_meta["language"]']) && !empty($_POST['user_meta["language"]']))
					$sal_lang = $_POST['user_meta["language"]'];
				else if(get_user_meta($user_ID,'language')) 
					$sal_lang = $myHelpers->global_lib->get_user_meta($user_ID,'language');
				else if(isset($this->default_language_title) && !empty($this->default_language_title))
					$sal_lang = strtolower($this->default_language_title);
					
				$default_language = get_option('default_language');
				?>
				<div class="form-group">
				  <label for="language"><?php echo mlx_get_lang('Language'); ?> <span class="required">*</span></label>
				  <select class="form-control select2_elem" name="user_meta[language]" id="language" required>
					<option value="default"><?php echo mlx_get_lang('Default Language'); ?> 
						<?php echo '('.$this->default_language_title.'('.$this->default_language.'))'; ?></option>
					<?php if(isset($website_languages) && !empty($website_languages)) {
						$language_list = json_decode($website_languages,true);
						foreach($language_list as $aak=>$aav)
						{
							if($aav['language'] == $default_language)
							{
								$new_value = $language_list[$aak];
								unset($language_list[$aak]);
								array_unshift($language_list, $new_value);
								break;
							}
						}
						foreach($language_list as $k=>$v)
						{
							$lang_exp = explode('~',$v['language']);
							echo '<option value="'.strtolower($lang_exp[0]).'"';
							if($sal_lang == strtolower($lang_exp[0]))
								echo ' selected="selected" ';
							echo '>'.$lang_exp[0].' ('.$lang_exp[1].')</option>';
						}
					}
					?>
				  </select>
				</div>
				
				<?php if($user_type != 'admin'){ ?>
				<div class="form-group">
				  <label for="default_timezone"><?php echo mlx_get_lang('Timezone'); ?> <span class="required">*</span></label>
				  <?php $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						$timezone_offsets = array();
						foreach( $timezones as $timezone )
						{
							$tz = new DateTimeZone($timezone);
							$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
						}

						asort($timezone_offsets);
						$timezone_list = array();
						foreach( $timezone_offsets as $timezone => $offset )
						{
							$offset_prefix = $offset < 0 ? '-' : '+';
							$offset_formatted = gmdate( 'H:i', abs($offset) );

							$pretty_offset = "UTC${offset_prefix}${offset_formatted}";

							$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
						}
				  ?>
				<select class="form-control select2_elem" id="default_timezone" name="user_meta[default_timezone]" required>
					  <option value="" selected="selected"><?php echo mlx_get_lang('Select Your Timezone'); ?></option>
					  <?php if($timezone_list) { 
							foreach($timezone_list as $timezonekey=>$timezonevalue) {
							 $timezone_selected = "";
							 if(isset($_POST['user_meta["default_timezone"]']) == $timezonekey) 
								$timezone_selected =  ' selected="selected" ';
							else if(get_user_meta($user_ID,'default_timezone') == $timezonekey)
								$timezone_selected =  ' selected="selected" ';
							?>
							<option value="<?php echo $timezonekey; ?>" <?php echo $timezone_selected; ?>><?php echo $timezonevalue; ?></option>
					  <?php } } ?>
				</select>
				  
				</div>
				
				<div class="form-group">
					<label for="direction"><?php echo mlx_get_lang('Direction'); ?></label>
					 <div class="radio_toggle_wrapper ">
						<input type="radio" id="ltr" value="ltr" 
						<?php 
						if(isset($_POST['user_meta["direction"]']) == 'ltr') 
							echo ' checked="checked" ';
						else if($myHelpers->global_lib->get_user_meta($user_ID,'direction') == 'ltr')
							echo ' checked="checked" ';
						else
							echo ' checked="checked" ';
						?>  name="user_meta[direction]" 
						class="toggle-radio-button">
						<label for="ltr"><?php echo mlx_get_lang('LTR'); ?></label>
						
						<input type="radio" id="rtl" 
						<?php 
						if(isset($_POST['user_meta["direction"]']) == 'rtl') 
							echo ' checked="checked" ';
						else if(get_user_meta($user_ID,'direction') == 'rtl')
							echo ' checked="checked" ';
						?> value="rtl" name="user_meta[direction]" 
						class="toggle-radio-button">
						<label for="rtl"><?php echo mlx_get_lang('RTL'); ?></label>
					</div>
				</div> 
				<?php } ?>
				
				
				<div class="row">
				<?php if($user_type != 'admin' && 0 ){ ?>
				<?php
				$site_users = $myHelpers->config->item("site_users");
				?>		
				<div class="col-md-6">
					<div class="form-group">
					  <label for="user_type"><?php echo mlx_get_lang('User Type'); ?> <span class="required">*</span></label>
					  <select class="form-control select2_elem"  name="user_type" required id="user_type" >
						<option value=""><?php echo mlx_get_lang('Select User Type'); ?></option>
					<?php
						foreach($site_users as $k => $user){
							if($k == 'admin') continue;
					?>				
					<option value="<?php echo $k;?>" 
					<?php if($user_data->user_type == $k) echo 'selected="selected"';?>  ><?php echo $user['title'];?></option>
					<?php	}	?>			
						
					  </select>
					</div>
				</div> 
				<?php } ?>	
				
					<div class="col-md-6">
						<div class="form-group">
						  <label for="FirstName"><?php echo mlx_get_lang('First Name'); ?> <span class="required">*</span></label>
						  <input type="text" class="form-control"  name="user_meta[first_name]" id="FirstName" required
						  value="<?php if(isset($_POST['user_meta["first_name"]'])) 
											echo $_POST['user_meta["first_name"]'];
										else if(get_user_meta($user_ID,'first_name'))
											echo get_user_meta($user_ID,'first_name');
								 ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
						  <label for="LastName"><?php echo mlx_get_lang('Last Name'); ?></label>
						  <input type="text" class="form-control"  name="user_meta[last_name]" id="LastName"
						  value="<?php if(isset($_POST['user_meta[last_name]'])) 
											echo $_POST['user_meta[last_name]'];
										else if(get_user_meta($user_ID,'last_name'))
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
										else if(get_user_meta($user_ID,'mobile_no'))
											echo get_user_meta($user_ID,'mobile_no');
								?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
						  <label for="user_email"><?php echo mlx_get_lang('Email Address'); ?>  <span class="required">*</span></label>
						  <input type="email" class="form-control" id="user_email" name="user_email" required
						  value="<?php if(isset($_POST['user_email'])) 
											echo $_POST['user_email'];
										else if(isset($user_data->user_email) )
											echo $user_data->user_email;
								?>">
						</div>
					</div>
					
					<div class="col-md-12">
						<div class="form-group">
						  <label for="UserAddress"><?php echo mlx_get_lang('Address'); ?></label>
						  <textarea class="form-control" rows="3" id="UserAddress" name="user_meta[address]" 
						  ><?php if(isset($_POST['user_meta[address]'])) echo $_POST['user_meta[address]']; 
								else if($address = get_user_meta($user_ID,'address')) 
									echo $address;?></textarea>
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
								<label class="custom-file-upload" data-element_id="<?php if(isset($b_id) && !empty($b_id)) 
									echo EncryptClientID($b_id); ?>" data-type="user" id="pl_file_uploader_1" 
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
						  <textarea class="form-control ckeditor-element" data-lang_code="en" rows="3" id="description" name="user_meta[description]"
						  ><?php if(isset($_POST['user_meta[description]'])) echo $_POST['user_meta[description]']; 
								else if($description = get_user_meta($user_ID,'description'))
											echo $description;
						  ?></textarea>
						</div>
					</div>
					 <?php 
							$args = ['user_ID' => $user_ID];
							do_action("cms_admin_user_profile_field" , $args);
					  ?>		
					
				
				</div>
			  </div>

		  </div>
		  
		  
		  <?php 
				$args = ['user_ID' => $user_ID];
				do_action("cms_admin_user_profile_box" , $args);
		  ?>
		  
		  <?php if($user_type != 'admin'  && 0){ ?>
			 <div class="box box-<?php echo get_skin_class(); ?>">
				<div class="box-header with-border">
				  <h3 class="box-title"><?php echo mlx_get_lang('Whatsapp Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				  <div class="box-body">
					 
					<div class="form-group">
					  <label for="whatsapp_no"><?php echo mlx_get_lang('Whatsapp No.'); ?></label>
					  <input type="text" class="form-control" 
					  name="user_meta[whatsapp_no]" id="whatsapp_no" value="<?php echo get_user_meta($user_ID,'whatsapp_no'); ?>">
					</div>
				</div>
			</div>
		  <?php } ?>
	  </div>
	  <div class="col-md-4">
			<div class="box box-<?php echo get_skin_class(); ?>">
			<div class="box-header with-border">
			  <h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
			  <div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			  </div>
			</div>
			 <div class="box-body">
			 </div>
			 <div class="box-footer">
				<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" 
						id="save_publish"><?php echo mlx_get_lang('Save Changes'); ?></button>
			  </div>
		  </div>
	  </div>
	  </div>
	  </form>
	</section>
</div>
      