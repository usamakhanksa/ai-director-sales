<?php

	$CI = &get_instance();
	
	
	$user_id = apply_filters("get_user_account_id");
	$short_desc_limit = 250;
	
	
	if(isset($agency_meta_data) && !empty($agency_meta_data))
	{
		$agency_meta = json_decode($agency_meta_data,true);	

		foreach($agency_meta as $k=>$v)
		{
			${$k} = $v;
		}
	}
	
?>
<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-building"></i> 
		<?php 	echo mlx_get_lang('My Agency'); ?>
			
		</h1>
		<?php 	do_action("cms_notifications");		?>
		<?php
		if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>


	</section>

	<section class="content">

             <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('',$attributes); 
			
			?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo  apply_filters("get_user_account_id"); ?>">	
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-<?php echo get_skin_class(); ?>">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Agency Details'); ?> </h3>
				 
                </div>
				
				
                  <div class="box-body">
                     <?php if(isset($agency_details)){?>
					 <input type="hidden" name="agency_id" value="<?php echo EncryptClientID($agency_details->agency_id); ?>" />
					 <?php } ?>
					<div class="form-group">
						<label for="website_title"><?php echo mlx_get_lang('Agency Title'); ?></label>
						<input type="text" class="form-control" 
						name="agency_meta[website_title]" id="website_title" 
							placeholder="Enter Agency Title" 
						value="<?php if(isset($website_title)) echo $website_title; ?>">
					</div>
					
					
					
					
					
					
					<div class="form-group">
						<label for="website_logo_text"><?php echo mlx_get_lang('Website Logo Text'); ?></label>
						<input type="text" class="form-control" 
						name="agency_meta[website_logo_text]" id="website_logo_text" 
						value="<?php if(isset($website_logo_text)) echo $website_logo_text; ?>">
					</div>
					
					
					
					<div class="row">				
						<div class="col-md-6">
							<div class="form-group">					  
								<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Website Logo'); ?> <small>(400x100)</small></label>						
								
								<?php $thumb_photo = get_image_type('uploads/media/',$website_logo,'thumb'); ?>
								<div class="form-group pl_image_container">
								<label class="custom-file-upload" data-element_id="<?php if(isset($id) && !empty($id)) echo EncryptClientID($id); ?>" 
										data-type="media" id="pl_file_uploader_1" 
								<?php if(isset($thumb_photo) && !empty($thumb_photo)) { echo 'style="display:none;"';}?>>
									<?php echo mlx_get_lang('Drop images here'); ?>
									<br>
									<strong><?php echo mlx_get_lang('OR'); ?></strong>
									<br>
									<?php echo mlx_get_lang('Click here to select images'); ?>
								</label>
								<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
								<?php if(isset($thumb_photo) && !empty($thumb_photo)) { ?>
									<a class="pl_file_link" href="<?php echo base_url().'uploads/media/'.$website_logo; ?>" 
									download="<?php echo $website_logo; ?>" style="">
										<img src="<?php echo base_url().'uploads/media/'.$thumb_photo; ?>" >
									</a>
									<a class="pl_file_remove_img" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
								<?php }else{ ?>
									<a class="pl_file_link" href="" download="" style="display:none;">
										<img src="" >
									</a>
									<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
								<?php } ?>
								<input type="hidden" name="agency_meta[website_logo]" value="<?php if(isset($website_logo) && !empty($website_logo)) { echo $website_logo;}?>" 
								class="pl_file_hidden">
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">					  
								<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Fevicon Icon'); ?> <small>(16x16)</small></label>						
								
								
								
								<?php $thumb_photo = get_image_type('uploads/media/',$fevicon_icon,'thumb'); ?>
								<div class="form-group pl_image_container">
									<label class="custom-file-upload" data-element_id="<?php if(isset($id) && !empty($id)) echo $myHelpers->global_lib->EncryptClientId($id); ?>" data-type="media" id="pl_file_uploader_2" 
									<?php if(isset($thumb_photo) && !empty($thumb_photo)) { echo 'style="display:none;"';}?>>
										<?php echo mlx_get_lang('Drop images here'); ?>
										<br>
										<strong><?php echo mlx_get_lang('OR'); ?></strong>
										<br>
										<?php echo mlx_get_lang('Click here to select images'); ?>
									</label>
									<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
									<?php if(isset($thumb_photo) && !empty($thumb_photo)) { ?>
										<a class="pl_file_link" href="<?php echo base_url().'../uploads/media/'.$fevicon_icon; ?>" 
										download="<?php echo $fevicon_icon; ?>" style="">
											<img src="<?php echo base_url().'uploads/media/'.$thumb_photo; ?>" >
										</a>
										<a class="pl_file_remove_img" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
									<?php }else{ ?>
										<a class="pl_file_link" href="" download="" style="display:none;">
											<img src="" >
										</a>
										<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
									<?php } ?>
									<input type="hidden" name="agency_meta[fevicon_icon]" value="<?php if(isset($fevicon_icon) && !empty($fevicon_icon)) { echo $fevicon_icon;}?>" 
									class="pl_file_hidden">
								</div>
								
								
							</div>
						</div>
					</div>
					
					
					
					<div class="form-group">
						<label for="short_description"><?php echo mlx_get_lang('Short Description'); ?></label>
						<textarea class="form-control short-description-element" rows="3" 
						id="agency_short_description" name="agency_meta[short_description]" 
						maxlength="<?php echo $short_desc_limit; ?>"><?php 
						if(isset($short_description)){ 
							echo $short_description; }?></textarea>
						<span class="rchars" id="rchars"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
					</div>

					<div class="form-group">
						<label for="description"><?php echo mlx_get_lang('Description'); ?> <span class="required">*</span></label>
						<textarea class="form-control ckeditor-element" required  rows="2" 
						id="agency_description" name="user_meta[agency_meta_description]"><?= get_user_meta($user_id , "agency_meta_description"); ?></textarea>
					</div>
					
					<?php
					
						global $country;
						$country = "india";
						
						
						$args = array();
						$args['is_edit'] = true;

						if(isset($country)){
							$args['sel_country'] = $country;
						}
						if(isset($state)){ 
							$args['sel_state'] = $state;
						}
						if(isset($city)){ 
							$args['sel_city'] = $city;
						}
						if(isset($zipcode)){ 
							$args['sel_zip_code'] = $zipcode;
						}
						if(isset($sub_area)){ 
							$args['sel_sub_area'] = $sub_area;
						}
						
						do_action('admin_property_location_fields',$args);
						?>

					<div class="form-group">
						<label for="company_address"><?php echo mlx_get_lang('Company Address'); ?></label>
						<textarea name="agency_meta[company_address]" id="company_address"  
								class="form-control" ><?php if(isset($company_address)) echo $company_address; ?></textarea>
						
					</div>
					
					
					<div class="form-group">
						<label for="company_tel"><?php echo mlx_get_lang('Company Telephone'); ?></label>
						<input type="text" class="form-control" 
						name="agency_meta[company_tel]" id="company_tel" 
							placeholder="Enter Company Telephone" 
						value="<?php if(isset($company_tel)) echo $company_tel; ?>">
					</div>
					
					<div class="form-group">
						<label for="company_email"><?php echo mlx_get_lang('Company Email'); ?></label>
						<input type="text" class="form-control" 
						name="agency_meta[company_email]" id="company_email" 
							placeholder="Enter Company Email" 
						value="<?php if(isset($company_email)) echo $company_email; ?>">
					</div>
					
					<div class="form-group">
						<label for="contact_email"><?php echo mlx_get_lang('Contact Email'); ?></label>
						<input type="text" class="form-control" 
						name="agency_meta[contact_email]" id="contact_email" 
							placeholder="Enter Contact Email" 
						value="<?php if(isset($contact_email)) echo $contact_email; ?>">
					</div>
					
					
                  </div>
					
              </div>
			  
			  <div class="box box-<?php echo get_skin_class(); ?>">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Social Media Settings'); ?> </h3>
				 
                </div>
				
				
                  <div class="box-body">

				  <?php 
				  		/*
						$social_media = array();
						if(isset($options_list) && $options_list->num_rows()>0)
						{
							foreach($options_list->result() as $row)
							{
								$social_media = json_decode($row->option_value, true);
							}
						}
						//social_media_list
						$social_media = json_decode($social_media_list, true);
						*/
					?>

						<?php 
					foreach($social_medias as $key => $details){
					$social_media_site = $social_media[$key];
					$url = (isset($social_media[$key]['url']))?$social_media[$key]['url']:'';
					$enable = (isset($social_media[$key]['enable']))?$social_media[$key]['enable']:'';
					 
					?>
					<div class="form-group">
                      <label for="facebook"><?php echo $details['title']; ?></label>
                      <div class="input-group">
					  <span class="input-group-addon">
					  		<input type="hidden" class="form-control "
					  name="agency_meta[social_media][<?php echo $key; ?>][icon]"  
					  value="<?php echo $details['fa-icon']; ?>">
                          <i class="fa <?php echo $details['fa-icon']; ?>"></i>
                        </span> 
					  <input type="url" class="form-control "
					  name="agency_meta[social_media][<?php echo $key; ?>][url]" id="<?php echo $key; ?>" placeholder="<?php echo $details['placeholder']; ?>" 
					  value="<?php if(isset($url)) echo $url; ?>">
					  
					  <span class="input-group-addon">
                          <input type="checkbox" class="minimal"
						  <?php if($enable == '1'){?>
						  checked="checked" 
						  <?php } ?>
						  name="agency_meta[social_media][<?php echo $key; ?>][enable]" value="1">
                        </span> 
					  </div>
					  
                    </div>
					
					<?php
					
					}?>
						
				  
				  </div>
		    </div>
			  
		  </div>
		  
		  <div class="col-md-4">
		  <div class="box box-<?php echo get_skin_class(); ?>">
			  <div class="box-header with-border">
                  <h3 class="box-title"> <?php echo mlx_get_lang('Status'); ?></h3>
				
                </div>
				 
			  	 <div class="box-body">
					<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Changes'); ?></button>
                  </div>
			  </div>
		  </div>
		  
		  
		  </div>
		  
			</form>
					
</section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php

do_action("admin_footer_scripts", "location_updates");


?>
