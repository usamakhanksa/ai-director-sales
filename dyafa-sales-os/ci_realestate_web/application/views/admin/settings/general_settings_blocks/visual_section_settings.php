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
					
					