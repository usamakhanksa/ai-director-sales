<?php $this->load->view("default/header-top");?>
<?php $this->load->view("default/sidebar-left");?>

<?php 
	if(isset($options_list) && $options_list->num_rows()>0)
	{
		
		foreach($options_list->result() as $row)
		{
			${$row->option_key} = $row->option_value;
			
			if($row->option_key == 'site_plugins')
			{
				${$row->option_key} = json_decode($row->option_value,true);
			}
			
		}
	}
	
?>
<script>
$(window).on('load', function () {
	var hash = window.location.hash;
	setTimeout(function(){
		if (hash) {
			hash = hash.replace('#',''); 
			$('#'+hash).find('.box-tools .btn-box-tool').trigger('click');
		}
	},100);
});
</script>
      <div class="content-wrapper">
       <section class="content-header">
          <h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('General Settings'); ?> </h1>
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
			echo form_open_multipart('settings/general_settings',$attributes); 
			
			?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('General Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
                </div>
				  <div class="box-body">
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
								
								<?php $thumb_photo = $myHelpers->global_lib->get_image_type('../uploads/media/',$website_logo,'thumb'); ?>
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
									<a class="pl_file_link" href="<?php echo base_url().'../uploads/media/'.$website_logo; ?>" 
									download="<?php echo $website_logo; ?>" style="">
										<img src="<?php echo base_url().'../uploads/media/'.$thumb_photo; ?>" >
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
								
								<?php $thumb_photo = $myHelpers->global_lib->get_image_type('../uploads/media/',$fevicon_icon,'thumb'); ?>
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
											<img src="<?php echo base_url().'../uploads/media/'.$thumb_photo; ?>" >
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
					
					
				</div>
					
              </div>
			
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
				  <div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Visual Section Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
				  </div>
				  
				  <div class="box-body" >
					
					<div class="form-group" >
						<label for="enable_homepage_section"><?php echo mlx_get_lang('Enable Homepage Section'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="homepage_section_yes" value="Y" 
							<?php 
							if(isset($enable_homepage_section) && $enable_homepage_section == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_homepage_section]" 
							class="toggle-radio-button">
							<label for="homepage_section_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="homepage_section_no" 
							<?php 
							if((isset($enable_homepage_section) && $enable_homepage_section == 'N')|| 
							!isset($enable_homepage_section))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_homepage_section]" 
							class="toggle-radio-button">
							<label for="homepage_section_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group" >
						<label for="enbale_front_end_registration"><?php echo mlx_get_lang('Enable Front End Registration'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_front_end_registration_yes" value="Y" 
							data-target="front_end_reg_yes" data-elem="front_end_reg_elem"
							<?php 
							if(isset($enbale_front_end_registration) && $enbale_front_end_registration == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_front_end_registration]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enbale_front_end_registration_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_front_end_registration_no" 
							data-target="front_end_reg_no" data-elem="front_end_reg_elem"
							<?php 
							if((isset($enbale_front_end_registration) && $enbale_front_end_registration == 'N')|| 
							!isset($enbale_front_end_registration))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_front_end_registration]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enbale_front_end_registration_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group front_end_reg_elem front_end_reg_yes child-form-group" >
						<label for="default_user_status_after_reg_yes"><?php echo mlx_get_lang('Default User Status After Register'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="default_user_status_after_reg_yes" value="Y" 
							<?php 
							if(isset($default_user_status_after_reg) && $default_user_status_after_reg == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[default_user_status_after_reg]" 
							class="toggle-radio-button">
							<label for="default_user_status_after_reg_yes"><?php echo mlx_get_lang('Active'); ?></label>
							
							<input type="radio" id="default_user_status_after_reg_no" 
							<?php 
							if((isset($default_user_status_after_reg) && $default_user_status_after_reg == 'N')|| 
							!isset($default_user_status_after_reg))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[default_user_status_after_reg]" 
							class="toggle-radio-button">
							<label for="default_user_status_after_reg_no"><?php echo mlx_get_lang('InActive'); ?></label>
						</div>
					</div>
					
					<div class="form-group front_end_reg_elem front_end_reg_yes child-form-group" >
						<label for="enbale_reg_auto_login_yes"><?php echo mlx_get_lang('Enable Auto Login After Register'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_reg_auto_login_yes" value="Y" 
							<?php 
							if(isset($enbale_reg_auto_login) && $enbale_reg_auto_login == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_reg_auto_login]" 
							class="toggle-radio-button">
							<label for="enbale_reg_auto_login_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_reg_auto_login_no" 
							<?php 
							if((isset($enbale_reg_auto_login) && $enbale_reg_auto_login == 'N')|| 
							!isset($enbale_reg_auto_login))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_reg_auto_login]" 
							class="toggle-radio-button">
							<label for="enbale_reg_auto_login_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group front_end_reg_elem front_end_reg_yes child-form-group" >
						<label for="enbale_reg_img_upload_yes"><?php echo mlx_get_lang('Enable Profile Picture Upload'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_reg_img_upload_yes" value="Y" 
							<?php 
							if(isset($enbale_reg_img_upload) && $enbale_reg_img_upload == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_reg_img_upload]" 
							class="toggle-radio-button">
							<label for="enbale_reg_img_upload_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_reg_img_upload_no" 
							<?php 
							if((isset($enbale_reg_img_upload) && $enbale_reg_img_upload == 'N')|| 
							!isset($enbale_reg_img_upload))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_reg_img_upload]" 
							class="toggle-radio-button">
							<label for="enbale_reg_img_upload_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group front_end_reg_elem front_end_reg_yes child-form-group" >
						<label for="register_message"><?php echo mlx_get_lang('Register Message'); ?></label>
						<textarea class="form-control" 
						name="options[register_message]" id="register_message" 
						><?php if(isset($register_message)) echo $register_message; ?></textarea>
					</div>
					
					<div class="form-group front_end_reg_elem front_end_reg_yes child-form-group" >
						<label for="disclaimer_message"><?php echo mlx_get_lang('Disclaimer Message'); ?></label>
						<textarea class="form-control" 
						name="options[disclaimer_message]" id="disclaimer_message" 
						><?php if(isset($disclaimer_message)) echo $disclaimer_message; ?></textarea>
					</div>
					
					
					
					<div class="form-group" >
						<label for="enbale_our_agents"><?php echo mlx_get_lang('Enable Our Agents'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_our_agents_yes" value="Y" 
							<?php 
							if(isset($enbale_our_agents) && $enbale_our_agents == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_our_agents]" 
							class="toggle-radio-button">
							<label for="enbale_our_agents_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_our_agents_no" 
							<?php 
							if((isset($enbale_our_agents) && $enbale_our_agents == 'N')|| 
							!isset($enbale_our_agents))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_our_agents]" 
							class="toggle-radio-button">
							<label for="enbale_our_agents_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group">
                      <label for="advance_search_min_price"><?php echo mlx_get_lang('Frontend Advance Search Min Price'); ?></label>
                      <input type="number" min="0" step="1" class="form-control" 
					  name="options[advance_search_min_price]" id="advance_search_min_price" 
					  value="<?php if(isset($advance_search_min_price)) echo $advance_search_min_price; ?>">
                    </div>
					
					<div class="form-group">
                      <label for="advance_search_max_price"><?php echo mlx_get_lang('Frontend Advance Search Max Price'); ?></label>
                      <input type="number" min="0" step="1" class="form-control" 
					  name="options[advance_search_max_price]" id="advance_search_max_price" 
					  value="<?php if(isset($advance_search_max_price)) echo $advance_search_max_price; ?>">
                    </div>
					
				</div>
			</div>
			
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
				  <div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Payment Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
				  </div>
				  
				  <div class="box-body" >
					
					<div class="form-group" >
						<label for="enable_payment_option_yes"><?php echo mlx_get_lang('Enable Payment Option'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_payment_option_yes" value="Y" 
							data-target="payment_option_yes" data-elem="enable_payment_option_elem"
							<?php 
							if(isset($enable_payment_option) && $enable_payment_option == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_payment_option]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enable_payment_option_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_payment_option_no" 
							data-target="payment_option_no" data-elem="enable_payment_option_elem"
							<?php 
							if((isset($enable_payment_option) && $enable_payment_option == 'N')|| 
							!isset($enable_payment_option))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_payment_option]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enable_payment_option_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group enable_payment_option_elem payment_option_yes" >
						<label for="enable_subscription"><?php echo mlx_get_lang('Enable Subscriptions'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_subscription_yes" value="Y" 
							<?php 
							if(isset($enable_subscription) && $enable_subscription == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_subscription]" 
							class="toggle-radio-button">
							<label for="enable_subscription_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_subscription_no" 
							<?php 
							if((isset($enable_subscription) && $enable_subscription == 'N')|| 
							!isset($enable_subscription))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_subscription]" 
							class="toggle-radio-button">
							<label for="enable_subscription_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group enable_payment_option_elem payment_option_yes" >
						<label for="enable_property_posting"><?php echo mlx_get_lang('Enable Credits for Property Posting'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_property_posting_yes" value="Y" 
							<?php 
							if(isset($enable_property_posting) && $enable_property_posting == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_property_posting]" 
							class="toggle-radio-button">
							<label for="enable_property_posting_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_property_posting_no" 
							<?php 
							if((isset($enable_property_posting) && $enable_property_posting == 'N')|| 
							!isset($enable_property_posting))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_property_posting]" 
							class="toggle-radio-button">
							<label for="enable_property_posting_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group enable_payment_option_elem payment_option_yes" >
						<label for="enable_featured_property_posting"><?php echo mlx_get_lang('Enable Credits for Featured Property Postings'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_featured_property_posting_yes" value="Y" 
							<?php 
							if(isset($enable_featured_property_posting) && $enable_featured_property_posting == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_featured_property_posting]" 
							class="toggle-radio-button">
							<label for="enable_featured_property_posting_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_featured_property_posting_no" 
							<?php 
							if((isset($enable_featured_property_posting) && $enable_featured_property_posting == 'N')|| 
							!isset($enable_featured_property_posting))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_featured_property_posting]" 
							class="toggle-radio-button">
							<label for="enable_featured_property_posting_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="enable_payment_option_elem payment_option_yes" >
						<label for="enable_blog_posting"><?php echo mlx_get_lang('Enable Credits for Blog Postings'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_blog_posting_yes" value="Y" 
							<?php 
							if(isset($enable_blog_posting) && $enable_blog_posting == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_blog_posting]" 
							class="toggle-radio-button">
							<label for="enable_blog_posting_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_blog_posting_no" 
							<?php 
							if((isset($enable_blog_posting) && $enable_blog_posting == 'N')|| 
							!isset($enable_blog_posting))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_blog_posting]" 
							class="toggle-radio-button">
							<label for="enable_blog_posting_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
				</div>
			</div>
			
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Whatsapp Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
                </div>
				  <div class="box-body">
				     
                	<div class="form-group">
                      <label for="site_whatsapp_no"><?php echo mlx_get_lang('Whatsapp No.'); ?></label>
                      <input type="text" class="form-control" 
					  name="options[site_whatsapp_no]" id="site_whatsapp_no" value="<?php if(isset($site_whatsapp_no)) echo $site_whatsapp_no; ?>">
					  <p class="help-block">Enter whatsapp no. without country code symbol i.e. +(plus) or anything else.</p>
                    </div>
					
					<div class="form-group">
                      <label for="site_whatsapp_group_link"><?php echo mlx_get_lang('Whatsapp Group Link'); ?></label>
                      <input type="url" class="form-control" 
					  name="options[site_whatsapp_group_link]" id="site_whatsapp_group_link" value="<?php if(isset($site_whatsapp_group_link)) echo $site_whatsapp_group_link; ?>">
                    </div>
					
				</div>
					
            </div>
			
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
				  <div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Login Page Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
				  </div>
				  
				  <div class="box-body" >
					
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
						<label class="custom-file-upload" <?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('../uploads/media/'.$login_bg_image)) echo 'style="display:none;"'; ?>>	
							<input type="file" accept="image/*" class="att_photo" id="login_bg_image" name="attachments" data-user-type="media">							
							<i class="fa fa-cloud-upload"></i> <?php echo mlx_get_lang('Upload Image'); ?>						
						</label>						
						<progress id="login_bg_image_progress" value="0" max="100" style="display:none;"></progress>						
						<a id="login_bg_image_link" href="<?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('../uploads/media/'.$login_bg_image)) 
							echo base_url().'../uploads/media/'.$login_bg_image; ?>" 						
						download="<?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('../uploads/media/'.$login_bg_image)) 
							echo base_url().'../uploads/media/'.$login_bg_image; ?>" 
						<?php if(!isset($login_bg_image)|| empty($login_bg_image) || !file_exists('../uploads/media/'.$login_bg_image)) echo 'style="display:none;"'; ?>>							
							<img src="<?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('../uploads/media/'.$login_bg_image)) 
								echo base_url().'../uploads/media/'.$login_bg_image; ?>" style="max-width:150px;">						
						</a>						
						<a class="remove_img" id="login_bg_image_remove_img" data-name="login_bg_image" title="Remove Image" 
						href="#" <?php if(!isset($login_bg_image) || empty($login_bg_image) || !file_exists('../uploads/media/'.$login_bg_image)) echo 'style="display:none;"'; ?>>
						<i class="fa fa-remove"></i></a>						
						<input type="hidden" name="options[login_bg_image]" 
						value="<?php if(isset($login_bg_image) && !empty($login_bg_image) && file_exists('../uploads/media/'.$login_bg_image)) echo $login_bg_image; ?>" id="login_bg_image_hidden">											
						-->
						
						
						<?php $thumb_photo = $myHelpers->global_lib->get_image_type('../uploads/media/',$login_bg_image,'thumb'); ?>
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
							<a class="pl_file_link" href="<?php echo base_url().'../uploads/media/'.$login_bg_image; ?>" 
							download="<?php echo $login_bg_image; ?>" style="">
								<img src="<?php echo base_url().'../uploads/media/'.$thumb_photo; ?>" >
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
				</div>
			</div>
			
			  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
				  <div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Admin Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
				  </div>
				  <div class="box-body">
					
					<div class="form-group">
                      <label for="default_date_format"><?php echo mlx_get_lang('Date Format'); ?></label>
                      <select class="form-control select2_elem" name="options[default_date_format]" id="default_date_format" >
						<option value="mm/dd/yyyy" 
						<?php if(isset($default_date_format) && 'mm/dd/yyyy' == $default_date_format) echo "selected=selected";?>>MM/DD/YYYY</option>
						<option value="dd/mm/yyyy" 
						<?php if(isset($default_date_format) && 'dd/mm/yyyy' == $default_date_format) echo "selected=selected";?>>DD/MM/YYYY</option>
					  </select>
					</div>
					
					<div class="form-group">
                      <label for="skins"><?php echo mlx_get_lang('Admin Skins'); ?></label>
					  <input type="hidden" name="options[skin]" class="option_skin" value="<?php if(isset($skin)) echo $skin; ?>">
                      <div class="skin-container row">
						 <ul class="list-unstyled clearfix ">
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-blue" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" 
								class="clearfix <?php if((!isset($skin)) || (isset($skin) && $skin == 'skin-blue')) echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span>
										<span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Blue'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-black" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-black') echo ''; else echo 'full-opacity-hover'; ?>">
									<div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix">
										<span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span>
										<span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Black'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-purple" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-purple') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span>
										<span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Purple'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-green" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-green') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span>
										<span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Green'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-red" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-red') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span>
										<span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Red'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-yellow" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-yellow') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span>
										<span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
									    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Yellow'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-blue-light" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-blue-light') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span>
										<span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center no-margin" ><?php echo mlx_get_lang('Blue Light'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-black-light" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-black-light') echo ''; else echo 'full-opacity-hover'; ?>">
									<div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix">
										<span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span>
										<span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								 </a>
								 <p class="text-center no-margin" ><?php echo mlx_get_lang('Black Light'); ?></p>
								</li>
								<li class="col-md-2">
									<a href="javascript:void(0);" data-skin="skin-purple-light" 
									style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-purple-light') echo ''; else echo 'full-opacity-hover'; ?>">
										<div>
											<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span>
											<span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span>
										</div>
										<div>
											<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
											<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
										</div>
									 </a>
									 <p class="text-center no-margin" ><?php echo mlx_get_lang('Purple Light'); ?></p>
								</li>
								<li class="col-md-2">
									<a href="javascript:void(0);" data-skin="skin-green-light" 
									style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-green-light') echo ''; else echo 'full-opacity-hover'; ?>">
										<div>
											<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span>
											<span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span>
										</div>
										<div>
											<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
											<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
										</div>
									</a>
									<p class="text-center no-margin" ><?php echo mlx_get_lang('Green Light'); ?></p>
								</li>
								<li class="col-md-2">
									<a href="javascript:void(0);" data-skin="skin-red-light" 
									style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-red-light') echo ''; else echo 'full-opacity-hover'; ?>">
										<div>
											<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span>
											<span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span>
										</div>
										<div>
											<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
											<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
										</div>
									</a>
									<p class="text-center no-margin" ><?php echo mlx_get_lang('Red Light'); ?></p>
								</li>
								<li class="col-md-2">
									<a href="javascript:void(0);" data-skin="skin-yellow-light" 
									style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-yellow-light') echo ''; else echo 'full-opacity-hover'; ?>">
										<div>
											<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span>
											<span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span>
										</div>
										<div>
											<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
											<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
										</div>
									</a>
									<p class="text-center no-margin" ><?php echo mlx_get_lang('Yellow Light'); ?></p>
								</li>
							</ul>
					  </div>
					</div>
				  </div>
			</div>
			
			
			<?php if(isset($site_plugins) && in_array('google_recaptcha',$site_plugins)){ ?>	
				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box" id="google_recaptcha_settings">
					<div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Google reCAPTCHA Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
					</div>
					
					  <div class="box-body">
						
						<div class="form-group">
						  <label for="recaptcha_site_key"><?php echo mlx_get_lang('Google reCAPTCHA Site Key'); ?></label>
						  <input type="text" class="form-control" 
						  name="options[recaptcha_site_key]" id="recaptcha_site_key" 
						  value="<?php if(isset($recaptcha_site_key)) echo $recaptcha_site_key; ?>">
						</div>
						
						<div class="form-group">
						  <label for="recaptcha_secret_key"><?php echo mlx_get_lang('Google reCAPTCHA Secret Key'); ?></label>
						  <input type="text" class="form-control" 
						  name="options[recaptcha_secret_key]" id="recaptcha_secret_key" 
						  value="<?php if(isset($recaptcha_secret_key)) echo $recaptcha_secret_key; ?>">
						</div>
						
						
					  </div>
						
				</div>
			<?php } ?>
			
			<?php if(isset($site_plugins) && in_array('google_map',$site_plugins)){ ?>
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box" id="google_map_settings">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Google Map Javascript API Settings'); ?></h3>
				  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
                </div>
				
                  <div class="box-body">
                    
					<div class="form-group" >
						<label for="enable_google_map_js_api"><?php echo mlx_get_lang('Enable Google Map API'); ?></label>
						<br>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_google_map_js_api_yes" value="Y" 
							data-target="google_map_js_api_yes" data-elem="google_map_js_api_elem"
							<?php 
							if(isset($enable_google_map_js_api) && $enable_google_map_js_api == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_google_map_js_api]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enable_google_map_js_api_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_google_map_js_api_no" 
							data-target="google_map_js_api_no" data-elem="google_map_js_api_elem"
							<?php 
							if((isset($enable_google_map_js_api) && $enable_google_map_js_api == 'N')|| 
							!isset($enable_google_map_js_api))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_google_map_js_api]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enable_google_map_js_api_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group google_map_js_api_elem google_map_js_api_yes child-form-group">
                      <label for="google_map_js_api_key"><?php echo mlx_get_lang('API Key'); ?></label>
                      <input type="text" class="form-control" 
					  name="options[google_map_js_api_key]" id="google_map_js_api_key" 
					  value="<?php if(isset($google_map_js_api_key)) echo $google_map_js_api_key; ?>">
                    </div>
					
					<div class="form-group google_map_js_api_elem google_map_js_api_yes child-form-group">
						<div class="row">
							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon"><?php echo mlx_get_lang('Latitude'); ?></span>
									<input id="google_map_center_latitude" type="text" class="form-control" name="options[google_map_center_latitude]"
									value="<?php if(isset($google_map_center_latitude)) echo $google_map_center_latitude; ?>">
								</div>
							</div>
							<div class="col-md-5">
								<div class="input-group">
									<span class="input-group-addon"><?php echo mlx_get_lang('Longitude'); ?></span>
									<input type="text" id="google_map_center_longitude" class="form-control" name="options[google_map_center_longitude]" 
									value="<?php if(isset($google_map_center_longitude)) echo $google_map_center_longitude; ?>">
								</div>
							</div>
							<div class="col-md-2 text-center">
								<a  href="#popme" class="btn btn-block btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> popup-player" data-toggle="tooltip" title="<?php echo mlx_get_lang('Fetch From Map'); ?>"><i class="fa fa-map-marker"></i></a>
								<div class="white-popup mfp-hide" id="popme">
									<div id="map" style="width: 100%; min-height: 500px"></div>
								</div>
							</div>
						</div>
						
                    </div>
					
                  </div>
					
              </div>
			<?php } ?>
			
			<?php if(isset($site_plugins) && in_array('google_analytics',$site_plugins)){ ?>
				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box" id="google_analytics_settings">
					<div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Google Analytics Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
					</div>
					
					  <div class="box-body">
						
						<div class="form-group">
						  <label for="google_analytics_tracking_id"><?php echo mlx_get_lang('Google Analytics Tracking ID'); ?></label>
						  
						  <input type="text" class="form-control" 
						  name="options[google_analytics_tracking_id]" id="google_analytics_tracking_id" 
						  value="<?php if(isset($google_analytics_tracking_id)) echo $google_analytics_tracking_id; ?>">
						  <p class="help-block">i.e. UA-XXXXXXXX-Y</p>
						</div>
						
						
					  </div>
						
				</div>
			<?php } ?>
			
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Front End Login Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
                </div>
				
                  <div class="box-body">
                    
					<div class="form-group" >
						<label for="enbale_front_end_login"><?php echo mlx_get_lang('Enable Front End Login'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_front_end_login_yes" value="Y" 
							<?php 
							if(isset($enbale_front_end_login) && $enbale_front_end_login == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_front_end_login]"
							data-target="front_end_login_yes" data-elem="front_end_login_elem" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enbale_front_end_login_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_front_end_login_no" 
							<?php 
							if((isset($enbale_front_end_login) && $enbale_front_end_login == 'N')|| 
							!isset($enbale_front_end_login))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_front_end_login]" 
							data-target="front_end_login_no" data-elem="front_end_login_elem"
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enbale_front_end_login_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
						<div class="form-group front_end_login_elem front_end_login_yes child-form-group" >
							<label for="enbale_gmail_login"><?php echo mlx_get_lang('Enable Gmail Login'); ?></label>
							 <div class="radio_toggle_wrapper ">
								<input type="radio" id="enbale_gmail_login_yes" value="Y" 
								<?php 
								if(isset($enbale_gmail_login) && $enbale_gmail_login == 'Y')  
								{ echo ' checked="checked" '; }
								?> name="options[enbale_gmail_login]"
								data-target="gmail_login_yes" data-elem="gmail_login_elem" 
								class="toggle-radio-button show_hide_setting_elem">
								<label for="enbale_gmail_login_yes"><?php echo mlx_get_lang('Yes'); ?></label>
								
								<input type="radio" id="enbale_gmail_login_no" 
								<?php 
								if((isset($enbale_gmail_login) && $enbale_gmail_login == 'N')|| 
								!isset($enbale_gmail_login))
								{ echo ' checked="checked" '; }
								?> value="N" name="options[enbale_gmail_login]" 
								data-target="gmail_login_no" data-elem="gmail_login_elem"
								class="toggle-radio-button show_hide_setting_elem">
								<label for="enbale_gmail_login_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>
						
						<div class="form-group front_end_login_elem front_end_login_yes gmail_login_elem gmail_login_yes child-form-group">
						  <label for="google_login_client_id"><?php echo mlx_get_lang('Google Client ID'); ?></label>
						  <input type="text" class="form-control" 
						  name="options[google_login_client_id]" id="google_login_client_id" 
						  value="<?php if(isset($google_login_client_id)) echo $google_login_client_id; ?>">
						</div>
						
						<div class="form-group front_end_login_elem front_end_login_yes gmail_login_elem gmail_login_yes child-form-group">
						  <label for="google_login_client_secret"><?php echo mlx_get_lang('Google Client Secret'); ?></label>
						  <input type="text" class="form-control" 
						  name="options[google_login_client_secret]" id="google_login_client_secret" 
						  value="<?php if(isset($google_login_client_secret)) echo $google_login_client_secret; ?>">
						</div>
						
						
						<div class="form-group front_end_login_elem front_end_login_yes child-form-group" >
							<label for="enbale_facebook_login"><?php echo mlx_get_lang('Enable Facebook Login'); ?></label>
							 <div class="radio_toggle_wrapper ">
								<input type="radio" id="enbale_facebook_login_yes" value="Y" 
								<?php 
								if(isset($enbale_facebook_login) && $enbale_facebook_login == 'Y')  
								{ echo ' checked="checked" '; }
								?> name="options[enbale_facebook_login]"
								data-target="facebook_login_yes" data-elem="facebook_login_elem" 
								class="toggle-radio-button show_hide_setting_elem">
								<label for="enbale_facebook_login_yes"><?php echo mlx_get_lang('Yes'); ?></label>
								
								<input type="radio" id="enbale_facebook_login_no" 
								<?php 
								if((isset($enbale_facebook_login) && $enbale_facebook_login == 'N')|| 
								!isset($enbale_facebook_login))
								{ echo ' checked="checked" '; }
								?> value="N" name="options[enbale_facebook_login]" 
								data-target="facebook_login_no" data-elem="facebook_login_elem"
								class="toggle-radio-button show_hide_setting_elem">
								<label for="enbale_facebook_login_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>
						
						<div class="form-group front_end_login_elem front_end_login_yes facebook_login_elem facebook_login_yes child-form-group">
						  <label for="facebook_login_app_id"><?php echo mlx_get_lang('App ID'); ?></label>
						  <input type="text" class="form-control" 
						  name="options[facebook_login_app_id]" id="facebook_login_app_id" 
						  value="<?php if(isset($facebook_login_app_id)) echo $facebook_login_app_id; ?>">
						</div>
						
						<div class="form-group front_end_login_elem front_end_login_yes facebook_login_elem facebook_login_yes child-form-group">
						  <label for="facebook_login_app_secret"><?php echo mlx_get_lang('App Secret'); ?></label>
						  <input type="text" class="form-control" 
						  name="options[facebook_login_app_secret]" id="facebook_login_app_secret" 
						  value="<?php if(isset($facebook_login_app_secret)) echo $facebook_login_app_secret; ?>">
						</div>
						
                  </div>
					
              </div>
			
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
				  <div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Property Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
				  </div>
				  
				  <div class="box-body" >
					
					<div class="form-group" >
						<label for="enable_property_soft_delete"><?php echo mlx_get_lang('Enable Soft Delete'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_property_soft_delete_yes" value="Y" 
							<?php 
							if((isset($enable_property_soft_delete) && $enable_property_soft_delete == 'Y')) { echo ' checked="checked" '; }
							?> name="options[enable_property_soft_delete]" 
							class="toggle-radio-button">
							<label for="enable_property_soft_delete_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_property_soft_delete_no" 
							<?php 
							if((isset($enable_property_soft_delete) && $enable_property_soft_delete == 'N')  || 
							!isset($enable_property_soft_delete))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_property_soft_delete]" 
							class="toggle-radio-button">
							<label for="enable_property_soft_delete_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group" >
						<label for="enable_compare_property"><?php echo mlx_get_lang('Enable Property Compare on Front End'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="compare_property_yes" value="Y" 
							<?php 
							if((isset($enable_compare_property) && $enable_compare_property == 'Y') || 
							!isset($enable_compare_property)) { echo ' checked="checked" '; }
							?> name="options[enable_compare_property]" 
							class="toggle-radio-button">
							<label for="compare_property_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="compare_property_no" 
							<?php 
							if(isset($enable_compare_property) && $enable_compare_property == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_compare_property]" 
							class="toggle-radio-button">
							<label for="compare_property_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
				  
					<div class="form-group">
						<label for="admin_approval_require_for_property"><?php echo mlx_get_lang('Require Admin Approval for Publish Property'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="admin_approval_yes" value="Y" 
							<?php 
							if((isset($admin_approval_require_for_property) && $admin_approval_require_for_property == 'Y') || 
							!isset($admin_approval_require_for_property)) { echo ' checked="checked" '; }
							?> name="options[admin_approval_require_for_property]" 
							class="toggle-radio-button">
							<label for="admin_approval_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="admin_approval_no" 
							<?php 
							if(isset($admin_approval_require_for_property) && $admin_approval_require_for_property == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[admin_approval_require_for_property]" 
							class="toggle-radio-button">
							<label for="admin_approval_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group">
						<label for="enable_property_for_states"><?php echo mlx_get_lang('Enable Property for States'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_property_for_states_yes" value="Y" 
							<?php 
							if((isset($enable_property_for_states) && $enable_property_for_states == 'Y') || 
							!isset($enable_property_for_states)) { echo ' checked="checked" '; }
							?> name="options[enable_property_for_states]" 
							class="toggle-radio-button show_hide_property_for_states">
							<label for="enable_property_for_states_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_property_for_states_no" 
							<?php 
							if(isset($enable_property_for_states) && $enable_property_for_states == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_property_for_states]" 
							class="toggle-radio-button show_hide_property_for_states">
							<label for="enable_property_for_states_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group show_hide_property_for_states_block child-form-group" 
						<?php 
							if(isset($enable_property_for_states) && $enable_property_for_states == 'N') { echo ' style="display:none;" '; }
							?>>
							
						
						 <label for="property_for_states"><?php echo mlx_get_lang('Property for States'); ?></label>
						 <select name="options[property_for_states][]" multiple="multiple" class="property_for_states form-control">
							<?php 
							
							if(isset($property_for_states) && !empty($property_for_states))
							{
								$property_for_states_array = json_decode($property_for_states,true);
								foreach($property_for_states_array as $k=>$v)
								{
									echo '<option selected="selected" value="'.$v.'">'.$v.'</option>';
								}
							}
							
							?>
						</select>
						
					</div> 
					
					<div class="form-group">
						<label for="enable_property_for_cities"><?php echo mlx_get_lang('Enable Property for Cities'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_property_for_cities_yes" value="Y" 
							<?php 
							if((isset($enable_property_for_cities) && $enable_property_for_cities == 'Y') || 
							!isset($enable_property_for_cities)) { echo ' checked="checked" '; }
							?> name="options[enable_property_for_cities]" 
							class="toggle-radio-button show_hide_property_for_cities">
							<label for="enable_property_for_cities_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_property_for_cities_no" 
							<?php 
							if(isset($enable_property_for_cities) && $enable_property_for_cities == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_property_for_cities]" 
							class="toggle-radio-button show_hide_property_for_cities">
							<label for="enable_property_for_cities_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group show_hide_property_for_cities_block child-form-group" 
						<?php 
							if(isset($enable_property_for_cities) && $enable_property_for_cities == 'N') { echo ' style="display:none;" '; }
							?>>
							
						
						 <label for="property_for_cities"><?php echo mlx_get_lang('Property for Cities'); ?></label>
						 <select name="options[property_for_cities][]" multiple="multiple" class="property_for_cities form-control">
							<?php 
							
							if(isset($property_for_cities) && !empty($property_for_cities))
							{
								$property_for_cities_array = json_decode($property_for_cities,true);
								foreach($property_for_cities_array as $k=>$v)
								{
									echo '<option selected="selected" value="'.$v.'">'.$v.'</option>';
								}
							}
							
							?>
						</select>
						
					</div> 
					
					
					
					
					<div class="form-group" >
						<label for="enbale_map_embed_js"><?php echo mlx_get_lang('Enable Map Embed Javascript'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_map_embed_js_yes" value="Y" 
							<?php 
							if(isset($enbale_map_embed_js) && $enbale_map_embed_js == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_map_embed_js]" 
							class="toggle-radio-button">
							<label for="enbale_map_embed_js_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_map_embed_js_no" 
							<?php 
							if((isset($enbale_map_embed_js) && $enbale_map_embed_js == 'N')|| 
							!isset($enbale_map_embed_js))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_map_embed_js]" 
							class="toggle-radio-button">
							<label for="enbale_map_embed_js_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group" >
						<label for="enbale_favourite"><?php echo mlx_get_lang('Enable Favourite Bookmarking'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_favourite_yes" value="Y" 
							<?php 
							if(isset($enbale_favourite) && $enbale_favourite == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_favourite]" 
							class="toggle-radio-button">
							<label for="enbale_favourite_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_favourite_no" 
							<?php 
							if((isset($enbale_favourite) && $enbale_favourite == 'N')|| 
							!isset($enbale_favourite))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_favourite]" 
							class="toggle-radio-button">
							<label for="enbale_favourite_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group" >
						<label for="enbale_print_priview"><?php echo mlx_get_lang('Enable Print preview'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_print_priview_yes" value="Y" 
							<?php 
							if(isset($enbale_print_priview) && $enbale_print_priview == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_print_priview]" 
							class="toggle-radio-button">
							<label for="enbale_print_priview_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_print_priview_no" 
							<?php 
							if((isset($enbale_print_priview) && $enbale_print_priview == 'N')|| 
							!isset($enbale_print_priview))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_print_priview]" 
							class="toggle-radio-button">
							<label for="enbale_print_priview_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group" >
						<label for="enbale_pdf_export"><?php echo mlx_get_lang('Enable Pdf Export'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_pdf_export_yes" value="Y" 
							<?php 
							if(isset($enbale_pdf_export) && $enbale_pdf_export == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_pdf_export]" 
							class="toggle-radio-button">
							<label for="enbale_pdf_export_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_pdf_export_no" 
							<?php 
							if((isset($enbale_pdf_export) && $enbale_pdf_export == 'N')|| 
							!isset($enbale_pdf_export))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_pdf_export]" 
							class="toggle-radio-button">
							<label for="enbale_pdf_export_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group" >
						<label for="enbale_mortgage_calculator"><?php echo mlx_get_lang('Enable Mortgage Calculator on Single Property Page'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_mortgage_calculator_yes" value="Y" 
							<?php 
							if(isset($enbale_mortgage_calculator) && $enbale_mortgage_calculator == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_mortgage_calculator]" 
							class="toggle-radio-button">
							<label for="enbale_mortgage_calculator_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_mortgage_calculator_no" 
							<?php 
							if((isset($enbale_mortgage_calculator) && $enbale_mortgage_calculator == 'N')|| 
							!isset($enbale_mortgage_calculator))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_mortgage_calculator]" 
							class="toggle-radio-button">
							<label for="enbale_mortgage_calculator_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group" >
						<label for="enbale_agent_contact_form"><?php echo mlx_get_lang('Enable Agent Contact Form on Single Property Page'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_agent_contact_form_yes" value="Y" 
							<?php 
							if(isset($enbale_agent_contact_form) && $enbale_agent_contact_form == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_agent_contact_form]" 
							class="toggle-radio-button">
							<label for="enbale_agent_contact_form_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_agent_contact_form_no" 
							<?php 
							if((isset($enbale_agent_contact_form) && $enbale_agent_contact_form == 'N')|| 
							!isset($enbale_agent_contact_form))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_agent_contact_form]" 
							class="toggle-radio-button">
							<label for="enbale_agent_contact_form_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group" >
						<label for="enbale_social_share"><?php echo mlx_get_lang('Enable Social Share on Single Property Page'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enbale_social_share_yes" value="Y" 
							<?php 
							if(isset($enbale_social_share) && $enbale_social_share == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enbale_social_share]" 
							class="toggle-radio-button">
							<label for="enbale_social_share_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enbale_social_share_no" 
							<?php 
							if((isset($enbale_social_share) && $enbale_social_share == 'N')|| 
							!isset($enbale_social_share))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enbale_social_share]" 
							class="toggle-radio-button">
							<label for="enbale_social_share_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					
					
				</div>
			</div>

			
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
				  <div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Search Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
				  </div>
				  
				  <div class="box-body" >
					<div class="form-group">
                      <label for="no_of_property_in_search_page"><?php echo mlx_get_lang('No. of Property in Search Page'); ?></label>
                      <input type="number" class="form-control" 
					  name="options[no_of_property_in_search_page]" id="no_of_property_in_search_page" 
					  value="<?php if(isset($no_of_property_in_search_page)) echo $no_of_property_in_search_page; else echo '12'; ?>" min="1" step="1">
                    </div>
					
					<div class="form-group" >
						<label for="enable_advance_search"><?php echo mlx_get_lang('Enable Advance Search?'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="enable_advance_search_yes" value="Y" 
							data-target="advance_search_yes" data-elem="advance_search_elem"
							<?php 
							if(isset($enable_advance_search) && $enable_advance_search == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[enable_advance_search]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enable_advance_search_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="enable_advance_search_no" 
							data-target="advance_search_no" data-elem="advance_search_elem"
							<?php 
							if((isset($enable_advance_search) && $enable_advance_search == 'N')|| 
							!isset($enable_advance_search))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[enable_advance_search]" 
							class="toggle-radio-button show_hide_setting_elem">
							<label for="enable_advance_search_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_price_range_yes"><?php echo mlx_get_lang('Price Range'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_price_range_yes" value="Y" 
							<?php 
							if(isset($advance_search_price_range) && $advance_search_price_range == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_price_range]" 
							class="toggle-radio-button">
							<label for="advance_search_price_range_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_price_range_no" 
							<?php 
							if((isset($advance_search_price_range) && $advance_search_price_range == 'N')|| 
							!isset($advance_search_price_range))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_price_range]" 
							class="toggle-radio-button">
							<label for="advance_search_price_range_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_bath_yes"><?php echo mlx_get_lang('Bath'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_bath_yes" value="Y" 
							<?php 
							if(isset($advance_search_bath) && $advance_search_bath == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_bath]" 
							class="toggle-radio-button">
							<label for="advance_search_bath_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_bath_no" 
							<?php 
							if((isset($advance_search_bath) && $advance_search_bath == 'N')|| 
							!isset($advance_search_bath))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_bath]" 
							class="toggle-radio-button">
							<label for="advance_search_bath_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_bed_yes"><?php echo mlx_get_lang('Bed'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_bed_yes" value="Y" 
							<?php 
							if(isset($advance_search_bed) && $advance_search_bed == 'Y')  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_bed]" 
							class="toggle-radio-button">
							<label for="advance_search_bed_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_bed_no" 
							<?php 
							if((isset($advance_search_bed) && $advance_search_bed == 'N')|| 
							!isset($advance_search_bed))
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_bed]" 
							class="toggle-radio-button">
							<label for="advance_search_bed_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_indoor_amenities_yes"><?php echo mlx_get_lang('Indoor Amenities'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_indoor_amenities_yes" value="Y" 
							<?php 
							if((isset($advance_search_indoor_amenities) && $advance_search_indoor_amenities == 'Y') || 
							!isset($advance_search_indoor_amenities))  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_indoor_amenities]" 
							class="toggle-radio-button">
							<label for="advance_search_indoor_amenities_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_indoor_amenities_no" 
							<?php 
							if(isset($advance_search_indoor_amenities) && $advance_search_indoor_amenities == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_indoor_amenities]" 
							class="toggle-radio-button">
							<label for="advance_search_indoor_amenities_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
					<div class="form-group advance_search_elem advance_search_yes child-form-group" >
						<label for="advance_search_outdoor_amenities_yes"><?php echo mlx_get_lang('Outdoor Amenities'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="advance_search_outdoor_amenities_yes" value="Y" 
							<?php 
							if((isset($advance_search_outdoor_amenities) && $advance_search_outdoor_amenities == 'Y') || 
							!isset($advance_search_outdoor_amenities))  
							{ echo ' checked="checked" '; }
							?> name="options[advance_search_outdoor_amenities]" 
							class="toggle-radio-button">
							<label for="advance_search_outdoor_amenities_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="advance_search_outdoor_amenities_no" 
							<?php 
							if(isset($advance_search_outdoor_amenities) && $advance_search_outdoor_amenities == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[advance_search_outdoor_amenities]" 
							class="toggle-radio-button">
							<label for="advance_search_outdoor_amenities_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div>
					
				</div>
				  
				  
				  
			</div>
			
			<?php if(isset($site_plugins) && in_array('blog',$site_plugins)){ ?>
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box" id="blog_settings">
				  <div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Blog Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
				  </div>
				  
				  <div class="box-body" >
					
					
				  
					<div class="form-group">
						<label for="admin_approval_require_for_blog"><?php echo mlx_get_lang('Require Admin Approval for Publish Blog'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="admin_approval_blog_yes" value="Y" 
							<?php 
							if((isset($admin_approval_require_for_blog) && $admin_approval_require_for_blog == 'Y') || 
							!isset($admin_approval_require_for_blog)) { echo ' checked="checked" '; }
							?> name="options[admin_approval_require_for_blog]" 
							class="toggle-radio-button">
							<label for="admin_approval_blog_yes"><?php echo mlx_get_lang('Yes'); ?></label>
							
							<input type="radio" id="admin_approval_blog_no" 
							<?php 
							if(isset($admin_approval_require_for_blog) && $admin_approval_require_for_blog == 'N')
							{ echo ' checked="checked" '; }
							?> value="N" name="options[admin_approval_require_for_blog]" 
							class="toggle-radio-button">
							<label for="admin_approval_blog_no"><?php echo mlx_get_lang('No'); ?></label>
						</div>
					</div> 
					
					
					
				</div>
			</div>
			<?php } ?>
			
			  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Contact Form Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
                </div>
				
				
                  <div class="box-body">
                    
					
					<div class="form-group">
                      <label for="site_domain"><?php echo mlx_get_lang('Site Domain'); ?></label>
                      <input type="text" class="form-control" 
					  name="options[site_domain]" id="site_domain" 
					  value="<?php if(isset($site_domain)) echo $site_domain; ?>">
                    </div>
					
					<div class="form-group">
                      <label for="site_domain_email"><?php echo mlx_get_lang('Site Domain E-mail'); ?></label>
                      <input type="text" class="form-control" 
					  name="options[site_domain_email]" id="site_domain_email" 
					  value="<?php if(isset($site_domain_email)) echo $site_domain_email; ?>">
                    </div>
					
					<div class="form-group">
                      <label for="contact_form_email"><?php echo mlx_get_lang('Contact Form E-mail'); ?></label>
                      <input type="text" class="form-control" 
					  name="options[contact_form_email]" id="contact_form_email" 
					  value="<?php if(isset($contact_form_email)) echo $contact_form_email; ?>">
                    </div>
					
					
                  </div>
					
              </div>
			  
			  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Footer Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
                </div>
				
                  <div class="box-body">
                    
					<?php 
					if(isset($enable_multi_language) && $enable_multi_language == 'Y' && isset($site_language) && !empty($site_language))
					{
						$site_language_array = json_decode($site_language,true);
						foreach($site_language_array as $aak=>$aav)
						{
							if($aav['language'] == $default_language)
							{
								$new_value = $site_language_array[$aak];
								unset($site_language_array[$aak]);
								array_unshift($site_language_array, $new_value);
								break;
							}
						}
					?>
						<div class="nav-tabs-custom">
							<ul class="nav nav-tabs">
							  <?php 
								$n=0;
								foreach($site_language_array as $k=>$v) { 
								if($v['status'] != 'enable')
									continue;
								$n++; 
								$lang_exp = explode('~',$v['language']);
								$lang_code = $lang_exp[1];
								$lang_title = $lang_exp[0];
								?>
								<li <?php if($n == 1) echo 'class="active"'; ?>>
									<a href="#<?php echo 'footer_tab_'.$lang_code; ?>" data-toggle="tab"><?php echo ucfirst($lang_title); ?></a>
								</li>
							  <?php } ?>
							</ul>
							<div class="tab-content">
							  <?php 
								$n=0;
								foreach($site_language_array as $k=>$v) { 
								
								if($v['status'] != 'enable')
									continue;
								
								$n++; 
								$lang_exp = explode('~',$v['language']);
								$lang_code = $lang_exp[1];
								$lang_title = $lang_exp[0];
								
								
								?>
								
									<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo 'footer_tab_'.	$lang_code; ?>">
									    <div class="form-group">
										  <label for="footer_text_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Footer Text'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
										  <textarea class="form-control wysihtml_editor_elem" rows="3" id="footer_text_<?php echo $lang_code; ?>" 
										  <?php if($n == 1) {?> required <?php } ?> name="multi_lang[<?php echo $lang_code; ?>][footer_text]"  ><?php echo $myHelpers->global_lib->get_option_lang('footer_text',$lang_code); ?></textarea>
										</div>
										
										<div class="form-group">
										  <label for="copyright_text_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Copyright Text'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
										  <textarea class="form-control wysihtml_editor_elem" rows="3" id="copyright_text_<?php echo $lang_code; ?>" 
										  <?php if($n == 1) {?> required <?php } ?> name="multi_lang[<?php echo $lang_code; ?>][copyright_text]" ><?php echo $myHelpers->global_lib->get_option_lang('copyright_text',$lang_code); ?></textarea>
										</div>
										
									</div>
								<?php } ?>
							</div>
						  </div>
						
					<?php }else{ ?>
						<div class="form-group">
						  <label for="footer_text"><?php echo mlx_get_lang('Footer Text'); ?></label>
						  <textarea id="footer_text" class="form-control wysihtml_editor_elem" name="options[footer_text]" 
						  rows="3"><?php if(isset($footer_text)) echo $footer_text; ?></textarea>
						</div>
						
						<div class="form-group">
						  <label for="copyright_text"><?php echo mlx_get_lang('Copyright Text'); ?></label>
						  <textarea id="copyright_text" class="form-control wysihtml_editor_elem" name="options[copyright_text]" 
						  rows="3"><?php if(isset($copyright_text)) echo $copyright_text; ?></textarea>
						</div>
					<?php } ?>
					
                  </div>
					
              </div>
			
			
			
			<?php if(isset($site_plugins) && in_array('cookie_consent',$site_plugins)){ ?>
				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> collapsed-box" id="cookie_settings">
					<div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Cookie Settings'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
						</div>
					</div>
					
					  <div class="box-body">
						
						<div class="form-group" >
							<label for="enable_cookie"><?php echo mlx_get_lang('Enable Cookie'); ?></label>
							 <div class="radio_toggle_wrapper ">
								<input type="radio" id="enbale_cookie_yes" value="Y" 
								data-target="front_end_cookie_yes" data-elem="front_end_cookie_elem"
								<?php 
								if(isset($enable_cookie) && $enable_cookie == 'Y')  
								{ echo ' checked="checked" '; }
								?> name="options[enable_cookie]" 
								class="toggle-radio-button show_hide_setting_elem">
								<label for="enbale_cookie_yes"><?php echo mlx_get_lang('Yes'); ?></label>
								
								<input type="radio" id="enbale_cookie_no" 
								data-target="front_end_cookie_no" data-elem="front_end_cookie_elem"
								<?php 
								if((isset($enable_cookie) && $enable_cookie == 'N')|| 
								!isset($enable_cookie))
								{ echo ' checked="checked" '; }
								?> value="N" name="options[enable_cookie]" 
								class="toggle-radio-button show_hide_setting_elem">
								<label for="enbale_cookie_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>
						
						<?php 
						if(isset($enable_multi_language) && $enable_multi_language == 'Y' && isset($site_language) && !empty($site_language))
						{
							$site_language_array = json_decode($site_language,true);
							foreach($site_language_array as $aak=>$aav)
							{
								if($aav['language'] == $default_language)
								{
									$new_value = $site_language_array[$aak];
									unset($site_language_array[$aak]);
									array_unshift($site_language_array, $new_value);
									break;
								}
							}
						?>
							<div class="nav-tabs-custom front_end_cookie_elem front_end_cookie_yes">
								<label><?php echo mlx_get_lang('Cookie Text'); ?></label>
								<ul class="nav nav-tabs">
								  <?php 
									$n=0;
									foreach($site_language_array as $k=>$v) { 
									
									if($v['status'] != 'enable')
										continue;
									$n++; 
									$lang_exp = explode('~',$v['language']);
									$lang_code = $lang_exp[1];
									$lang_title = $lang_exp[0];
									?>
									<li <?php if($n == 1) echo 'class="active"'; ?>>
										<a href="#<?php echo 'cookie_tab_'.$lang_code; ?>" data-toggle="tab"><?php echo ucfirst($lang_title); ?></a>
									</li>
								  <?php } ?>
								</ul>
								<div class="tab-content">
								  <?php 
									$n=0;
									foreach($site_language_array as $k=>$v) { 
									
									if($v['status'] != 'enable')
										continue;
									$n++; 
									$lang_exp = explode('~',$v['language']);
									$lang_code = $lang_exp[1];
									$lang_title = $lang_exp[0];
									
									
									?>
									
										<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo 'cookie_tab_'.$lang_code; ?>">
											<div class="form-group">
											  <label for="cookie_text_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Cookie Text'); ?> </label>
											  <textarea class="form-control wysihtml_editor_elem" rows="3" id="cookie_text_<?php echo $lang_code; ?>" 
											   name="multi_lang[<?php echo $lang_code; ?>][cookie_text]" ><?php echo $myHelpers->global_lib->get_option_lang('cookie_text',$lang_code); ?></textarea>
											</div>
											
										</div>
									<?php } ?>
								</div>
							  </div>
							
						<?php }else{ ?>
							<div class="form-group front_end_cookie_elem front_end_cookie_yes">
							  <label><?php echo mlx_get_lang('Cookie Text'); ?></label>
							  <textarea id="cookie_text" class="form-control wysihtml_editor_elem" name="options[cookie_text]" 
							  rows="3"><?php if(isset($cookie_text)) echo $cookie_text; ?></textarea>
							</div>
							
						<?php } ?>
						
					  </div>
						
				  </div>
			<?php } ?>
		  </div>
		  
			  <div class="col-md-4">
				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> sticky_sidebar">
				  <div class="box-header with-border">
					  <h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					  </div>
					</div>
					 
					 <div class="box-footer">
						<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Changes'); ?></button>
					  </div>
				  </div>
			  </div>
		  
		  
		  </div>
		  
			</form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
<?php

	echo link_tag("themes/$theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css");
	echo script_tag("themes/$theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js");

?>
<?php
/*
	echo link_tag("themes/$theme/plugins/summernote-editor/summernote.min.css");
	echo script_tag("themes/$theme/plugins/summernote-editor/summernote.min.js");
*/
?>
<script>
	$(document).ready(function(){
		
		$('.wysihtml_editor_elem').each(function() {
			$(this).wysihtml5();
		});
		
		/*
		$('.wysihtml_editor_elem').each(function() {
			$(this).summernote({
				
			});
		});
		*/
	});
</script>