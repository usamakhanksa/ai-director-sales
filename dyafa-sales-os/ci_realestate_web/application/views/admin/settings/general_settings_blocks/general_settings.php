<div class="form-group">
	<label for="website_title"><?php echo mlx_get_lang('Website Title'); ?></label>
	<input type="text" class="form-control" 
	name="options[website_title]" id="website_title" value="<?php if(isset($website_title)) echo $website_title; ?>">
</div>

<div class="form-group">
	<label for="website_logo_text"><?php echo mlx_get_lang('Website Logo Text'); ?></label>
	<input type="text" class="form-control" 
	name="options[website_logo_text]" id="website_logo_text" value="<?php if(isset($website_logo_text)) echo $website_logo_text; ?>">
</div>

<div class="row">				
	<div class="col-md-6">
		<div class="form-group">					  
			<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Website Logo'); ?> <small>(400x100)</small></label>						
			
			<?php $thumb_photo = $myHelpers->global_lib->get_image_type('uploads/media/',$website_logo,'thumb'); ?>
			<div class="form-group pl_image_container">
			<label class="custom-file-upload" data-element_id="<?php if(isset($id) && !empty($id)) echo $myHelpers->global_lib->EncryptClientId($id); ?>" data-type="media" id="pl_file_uploader_1" 
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
			<input type="hidden" name="options[website_logo]" value="<?php if(isset($website_logo) && !empty($website_logo)) { echo $website_logo;}?>" 
			class="pl_file_hidden">
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">					  
			<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Fevicon Icon'); ?> <small>(16x16)</small></label>						
			
			<?php $thumb_photo = $myHelpers->global_lib->get_image_type('uploads/media/',$fevicon_icon,'thumb'); ?>
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
					<a class="pl_file_link" href="<?php echo base_url().'uploads/media/'.$fevicon_icon; ?>" 
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
				<input type="hidden" name="options[fevicon_icon]" value="<?php if(isset($fevicon_icon) && !empty($fevicon_icon)) { echo $fevicon_icon;}?>" 
				class="pl_file_hidden">
			</div>
			
			
		</div>
	</div>
</div>

<div class="form-group">
	<label for="company_address"><?php echo mlx_get_lang('Company Address'); ?></label>
	<textarea id="company_address" class="form-control"  name="options[company_address]" 
	rows="3"><?php if(isset($company_address)) echo $company_address; ?></textarea>
</div>

<div class="form-group">
	<label for="company_mob"><?php echo mlx_get_lang('Compony Mobile No.'); ?></label>
	<input type="text" class="form-control" 
	name="options[company_mob]" id="company_mob" placeholder="Enter Mobile No." 
	value="<?php if(isset($company_mob)) echo $company_mob; ?>">
</div>

<div class="form-group">
	<label for="company_tel"><?php echo mlx_get_lang('Company Telephone No.'); ?></label>
	<input type="text" class="form-control" 
	name="options[company_tel]" id="company_tel" 
	value="<?php if(isset($company_tel)) echo $company_tel; ?>">
</div>

<div class="form-group">
	<label for="company_website"><?php echo mlx_get_lang('Company Website'); ?></label>
	<input type="text" class="form-control" 
	name="options[company_website]" id="company_website" 
	value="<?php if(isset($company_website)) echo $company_website; ?>">
</div>

<div class="form-group">
	<label for="company_email"><?php echo mlx_get_lang('Company E-mail'); ?></label>
	<input type="text" class="form-control" required="required" 
	name="options[company_email]" id="company_email" 
	value="<?php if(isset($company_email)) echo $company_email; ?>">
</div>
	
<div class="form-group">
	<label for="company_email"><?php echo mlx_get_lang('Contact E-mail'); ?></label>
	<input type="email" class="form-control" 
	name="options[contact_email]" id="contact_email" 
	value="<?php if(isset($contact_email)) echo $contact_email; ?>">
</div>

<div class="form-group">
	<label for="company_lat_lng"><?php echo mlx_get_lang('Lat/Long'); ?></label><br>
	<input type="text" class="form-control comp_lat_lng" 
	name="options[company_lat]" id="company_lat" placeholder="Enter Latitude " 
	value="<?php if(isset($company_lat)) echo $company_lat; ?>">
	<input type="text" class="form-control comp_lat_lng"  
	name="options[company_lng]" id="company_lng" placeholder="Enter Longitude " 
	value="<?php if(isset($company_lng)) echo $company_lng; ?>">
</div>