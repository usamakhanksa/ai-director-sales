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
			
	<?php do_action("general_settings_payment_settings_append"); ?>
			