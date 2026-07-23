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
					
		<hr>

	<div class="form-group" >
		<label for="enable_property_videos_for_all"><?php echo mlx_get_lang('Enable Property Videos for All Users'); ?></label>
		 <div class="radio_toggle_wrapper ">
			<input type="radio" id="enable_property_videos_for_all_yes" value="Y" 
			<?php 
			if((isset($enable_property_videos_for_all) && $enable_property_videos_for_all == 'Y')) { echo ' checked="checked" '; }
			?> name="options[enable_property_videos_for_all]" 
			class="toggle-radio-button">
			<label for="enable_property_videos_for_all_yes"><?php echo mlx_get_lang('Yes'); ?></label>
			
			<input type="radio" id="enable_property_videos_for_all_no" 
			<?php 
			if((isset($enable_property_videos_for_all) && $enable_property_videos_for_all == 'N')  || 
			!isset($enable_property_videos_for_all))
			{ echo ' checked="checked" '; }
			?> value="N" name="options[enable_property_videos_for_all]" 
			class="toggle-radio-button">
			<label for="enable_property_videos_for_all_no"><?php echo mlx_get_lang('No'); ?></label>
		</div>
	</div> 
	
	
	<div class="form-group" >
		<label for="enable_property_videos_for_admin"><?php echo mlx_get_lang('Enable Property Videos for Admin Only'); ?></label>
		 <div class="radio_toggle_wrapper ">
			<input type="radio" id="enable_property_videos_for_admin_yes" value="Y" 
			<?php 
			if((isset($enable_property_videos_for_admin) && $enable_property_videos_for_admin == 'Y')) { echo ' checked="checked" '; }
			?> name="options[enable_property_videos_for_admin]" 
			class="toggle-radio-button">
			<label for="enable_property_videos_for_admin_yes"><?php echo mlx_get_lang('Yes'); ?></label>
			
			<input type="radio" id="enable_property_videos_for_admin_no" 
			<?php 
			if((isset($enable_property_videos_for_admin) && $enable_property_videos_for_admin == 'N')  || 
			!isset($enable_property_videos_for_admin))
			{ echo ' checked="checked" '; }
			?> value="N" name="options[enable_property_videos_for_admin]" 
			class="toggle-radio-button">
			<label for="enable_property_videos_for_admin_no"><?php echo mlx_get_lang('No'); ?></label>
		</div>
	</div> 
	
	
	
	
	<div class="form-group" >
		<label for="enable_property_distances_for_all"><?php echo mlx_get_lang('Enable Property Distances for All Users'); ?></label>
		 <div class="radio_toggle_wrapper ">
			<input type="radio" id="enable_property_distances_for_all_yes" value="Y" 
			<?php 
			if((isset($enable_property_distances_for_all) && $enable_property_distances_for_all == 'Y')) { echo ' checked="checked" '; }
			?> name="options[enable_property_distances_for_all]" 
			class="toggle-radio-button">
			<label for="enable_property_distances_for_all_yes"><?php echo mlx_get_lang('Yes'); ?></label>
			
			<input type="radio" id="enable_property_distances_for_all_no" 
			<?php 
			if((isset($enable_property_distances_for_all) && $enable_property_distances_for_all == 'N')  || 
			!isset($enable_property_distances_for_all))
			{ echo ' checked="checked" '; }
			?> value="N" name="options[enable_property_distances_for_all]" 
			class="toggle-radio-button">
			<label for="enable_property_distances_for_all_no"><?php echo mlx_get_lang('No'); ?></label>
		</div>
	</div> 
	
	
	<div class="form-group" >
		<label for="enable_property_distances_for_admin"><?php echo mlx_get_lang('Enable Property Distances for Admin Only'); ?></label>
		 <div class="radio_toggle_wrapper ">
			<input type="radio" id="enable_property_distances_for_admin_yes" value="Y" 
			<?php 
			if((isset($enable_property_distances_for_admin) && $enable_property_distances_for_admin == 'Y')) { echo ' checked="checked" '; }
			?> name="options[enable_property_distances_for_admin]" 
			class="toggle-radio-button">
			<label for="enable_property_distances_for_admin_yes"><?php echo mlx_get_lang('Yes'); ?></label>
			
			<input type="radio" id="enable_property_distances_for_admin_no" 
			<?php 
			if((isset($enable_property_distances_for_admin) && $enable_property_distances_for_admin == 'N')  || 
			!isset($enable_property_distances_for_admin))
			{ echo ' checked="checked" '; }
			?> value="N" name="options[enable_property_distances_for_admin]" 
			class="toggle-radio-button">
			<label for="enable_property_distances_for_admin_no"><?php echo mlx_get_lang('No'); ?></label>
		</div>
	</div> 
	
	
	
	
	
	<div class="form-group" >
		<label for="enable_property_gallery_for_all"><?php echo mlx_get_lang('Enable Property Galleries for All Users'); ?></label>
		 <div class="radio_toggle_wrapper ">
			<input type="radio" id="enable_property_gallery_for_all_yes" value="Y" 
			<?php 
			if((isset($enable_property_gallery_for_all) && $enable_property_gallery_for_all == 'Y')) { echo ' checked="checked" '; }
			?> name="options[enable_property_gallery_for_all]" 
			class="toggle-radio-button">
			<label for="enable_property_gallery_for_all_yes"><?php echo mlx_get_lang('Yes'); ?></label>
			
			<input type="radio" id="enable_property_gallery_for_all_no" 
			<?php 
			if((isset($enable_property_gallery_for_all) && $enable_property_gallery_for_all == 'N')  || 
			!isset($enable_property_gallery_for_all))
			{ echo ' checked="checked" '; }
			?> value="N" name="options[enable_property_gallery_for_all]" 
			class="toggle-radio-button">
			<label for="enable_property_gallery_for_all_no"><?php echo mlx_get_lang('No'); ?></label>
		</div>
	</div> 
	
	
	<div class="form-group" >
		<label for="enable_property_gallery_for_admin"><?php echo mlx_get_lang('Enable Property Galleries for Admin Only'); ?></label>
		 <div class="radio_toggle_wrapper ">
			<input type="radio" id="enable_property_gallery_for_admin_yes" value="Y" 
			<?php 
			if((isset($enable_property_gallery_for_admin) && $enable_property_gallery_for_admin == 'Y')) { echo ' checked="checked" '; }
			?> name="options[enable_property_gallery_for_admin]" 
			class="toggle-radio-button">
			<label for="enable_property_gallery_for_admin_yes"><?php echo mlx_get_lang('Yes'); ?></label>
			
			<input type="radio" id="enable_property_gallery_for_admin_no" 
			<?php 
			if((isset($enable_property_gallery_for_admin) && $enable_property_gallery_for_admin == 'N')  || 
			!isset($enable_property_gallery_for_admin))
			{ echo ' checked="checked" '; }
			?> value="N" name="options[enable_property_gallery_for_admin]" 
			class="toggle-radio-button">
			<label for="enable_property_gallery_for_admin_no"><?php echo mlx_get_lang('No'); ?></label>
		</div>
	</div> 
	
	
	
	
	
	
	
	
	
	
	
	
	
		