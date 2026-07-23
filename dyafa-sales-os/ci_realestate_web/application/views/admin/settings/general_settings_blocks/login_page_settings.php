<div class="form-group" >
						<label for="login_page_bg_type"><?php echo mlx_get_lang('Background Type'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="login_page_bg_type_color" value="color" 
							data-target="login_page_bg_type_color" data-elem="login_bg_elem"
							<?php 
							if((isset($login_page_bg_type) && $login_page_bg_type == 'color')|| 
							!isset($login_page_bg_type))  
							{ echo ' checked="checked" '; }
							?> name="options[login_page_bg_type]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="login_page_bg_type_color"><?php echo mlx_get_lang('Color'); ?></label>
							
							<input type="radio" id="login_page_bg_type_image" 
							<?php 
							if((isset($login_page_bg_type) && $login_page_bg_type == 'image'))
							{ echo ' checked="checked" '; }
							?> value="image" name="options[login_page_bg_type]" 
							data-target="login_page_bg_type_image" data-elem="login_bg_elem"
							class="toggle-radio-button show_hide_setting_elem">
							<label for="login_page_bg_type_image"><?php echo mlx_get_lang('Image'); ?></label>
						</div>
					</div> 
					
					<div class="login_bg_elem login_page_bg_type_image ">					  
						<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Background Image'); ?></label>						
						<!--
						<label class="custom-file-upload" <?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('uploads/media/'.$login_bg_image)) echo 'style="display:none;"'; ?>>	
							<input type="file" accept="image/*" class="att_photo" id="login_bg_image" name="attachments" data-user-type="media">							
							<i class="fa fa-cloud-upload"></i> <?php echo mlx_get_lang('Upload Image'); ?>						
						</label>						
						<progress id="login_bg_image_progress" value="0" max="100" style="display:none;"></progress>						
						<a id="login_bg_image_link" href="<?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('uploads/media/'.$login_bg_image)) 
							echo base_url().'uploads/media/'.$login_bg_image; ?>" 						
						download="<?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('uploads/media/'.$login_bg_image)) 
							echo base_url().'uploads/media/'.$login_bg_image; ?>" 
						<?php if(!isset($login_bg_image)|| empty($login_bg_image) || !file_exists('uploads/media/'.$login_bg_image)) echo 'style="display:none;"'; ?>>							
							<img src="<?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('uploads/media/'.$login_bg_image)) 
								echo base_url().'uploads/media/'.$login_bg_image; ?>" style="max-width:150px;">						
						</a>						
						<a class="remove_img" id="login_bg_image_remove_img" data-name="login_bg_image" title="Remove Image" 
						href="#" <?php if(!isset($login_bg_image) || empty($login_bg_image) || !file_exists('uploads/media/'.$login_bg_image)) echo 'style="display:none;"'; ?>>
						<i class="fa fa-remove"></i></a>						
						<input type="hidden" name="options[login_bg_image]" 
						value="<?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('uploads/media/'.$login_bg_image)) echo $login_bg_image; ?>" id="login_bg_image_hidden">											
						-->
						
						
						<?php $thumb_photo = $myHelpers->global_lib->get_image_type('uploads/media/',$login_bg_image,'thumb'); ?>
						<div class="form-group pl_image_container">
						<label class="custom-file-upload" data-element_id="<?php if(isset($id) && !empty($id)) echo $myHelpers->global_lib->EncryptClientId($id); ?>" data-type="media" id="pl_file_uploader_3" 
						<?php if(isset($thumb_photo) && !empty($thumb_photo)) { echo 'style="display:none;"';}?>>
							<?php echo mlx_get_lang('Drop images here'); ?>
							<br>
							<strong><?php echo mlx_get_lang('OR'); ?></strong>
							<br>
							<?php echo mlx_get_lang('Click here to select images'); ?>
						</label>
						<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
						<?php if(isset($thumb_photo) && !empty($thumb_photo)) { ?>
							<a class="pl_file_link" href="<?php echo base_url().'uploads/media/'.$login_bg_image; ?>" 
							download="<?php echo $login_bg_image; ?>" style="">
								<img src="<?php echo base_url().'uploads/media/'.$thumb_photo; ?>" >
							</a>
							<a class="pl_file_remove_img" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
						<?php }else{ ?>
							<a class="pl_file_link" href="" download="" style="display:none;">
								<img src="" >
							</a>
							<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
						<?php } ?>
						<input type="hidden" name="options[login_bg_image]" value="<?php if(isset($login_bg_image) && !empty($login_bg_image)) { echo $login_bg_image;}?>" 
						class="pl_file_hidden">
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-3">
							<div class="login_page_bg_type_color login_bg_elem">
							  <label for="login_bg_color"><?php echo mlx_get_lang('Background Color'); ?></label><br>
							  <input type="text" class="form-control jscolor " 
							  name="options[login_bg_color]" id="login_bg_color" readonly
							  value="<?php if(isset($login_bg_color)) echo $login_bg_color; else echo "555555"; ?>">
							</div>
						</div>
					</div>