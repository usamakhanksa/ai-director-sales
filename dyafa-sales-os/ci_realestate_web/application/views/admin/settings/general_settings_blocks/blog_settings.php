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