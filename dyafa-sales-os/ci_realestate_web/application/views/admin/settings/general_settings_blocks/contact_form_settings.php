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
					