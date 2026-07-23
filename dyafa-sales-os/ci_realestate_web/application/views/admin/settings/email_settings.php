

<?php 
	$email_setting = json_decode($email_setting,true);
?>
      <div class="content-wrapper">
        <section class="content-header">
          <h1  class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('Email Settings'); ?> </h1>
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
			
			?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('SMTP Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
                </div>
				
                  <div class="box-body">
						<div class="form-group">
							<label for="smtp_host"><?php echo mlx_get_lang("SMTP Host"); ?></label>
							<input type="text" class="form-control" name="options[email_setting][smtp_host]" id="smtp_host" value="<?php if(isset($email_setting['smtp_host']) && !empty($email_setting['smtp_host'])){ echo trim($email_setting['smtp_host']);}?>">
							<p>This is the host address for your smtp server, this is only needed if you are using SMTP </p>
						</div>
						<div class="form-group">
							<label for="smtp_port"><?php echo mlx_get_lang("SMTP Port"); ?></label>
							<input type="text" class="form-control" name="options[email_setting][smtp_port]" id="smtp_port" value="<?php if(isset($email_setting['smtp_port']) && !empty($email_setting['smtp_port'])){ echo trim($email_setting['smtp_port']);}?>">
						</div>
						<div class="form-group">
							<label for="smtp_username"><?php echo mlx_get_lang("SMTP Username"); ?></label>
							<input type="text" class="form-control" name="options[email_setting][smtp_username]" id="smtp_username" 
								value="<?php if(isset($email_setting['smtp_username']) && !empty($email_setting['smtp_username'])){ 
									echo trim($email_setting['smtp_username']);}?>">
							<p>This is the username for your smtp server, this is only needed if you are using SMTP</p>
						</div>	
						<div class="form-group">
							<label for="smtp_password"><?php echo mlx_get_lang("SMTP Password"); ?></label>
							<input type="password" class="form-control" name="options[email_setting][smtp_password]" id="smtp_password" 
							value="<?php if(isset($email_setting['smtp_password']) && !empty($email_setting['smtp_password'])){ 
								echo trim($email_setting['smtp_password']);}else{echo '';}?>">
							<p>This is the password for your smtp server, this is only needed if you are using SMTP</p>
						</div>	

						<div class="form-group">
							<label for="smtp_encryption"><?php echo mlx_get_lang("SMTP Encryption"); ?></label>
							<select class="form-control" name="options[email_setting][smtp_encryption]" id="smtp_encryption" >
							<option value="off" 
							<?php if(isset($email_setting['smtp_encryption']) && ($email_setting['smtp_encryption']=='off')) 
									{ echo " selected ";}?> >Off</option>
							<option value="ssl" 
							<?php if(isset($email_setting['smtp_encryption']) && ($email_setting['smtp_encryption']=='ssl')) 
									{ echo " selected ";}?> >SSL</option>
							<option value="tls" 
							<?php if(isset($email_setting['smtp_encryption']) && ($email_setting['smtp_encryption']=='tls')) 
									{ echo " selected ";}?> >TLS</option>
							
							</select>
						</div>
						
						<div class="form-group">
							<label for="smtp_auth"><?php echo mlx_get_lang("SMTP Authentication"); ?></label>
							<select class="form-control" name="options[email_setting][smtp_auth]" id="smtp_auth" >
							<option value="false" 
							<?php if(isset($email_setting['smtp_auth']) && ($email_setting['smtp_auth']=='false')) 
									{ echo " selected ";}?> >Off</option>
							<option value="true" 
							<?php if(isset($email_setting['smtp_auth']) && ($email_setting['smtp_auth']=='true')) 
									{ echo " selected ";}?> >ON</option>
							
							
							</select>
						</div>
						
                  </div>
				</div>			  
			  
				
		  </div>
		  
		  <div class="col-md-4">
		  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
			  <div class="box-header with-border">
                  <h3 class="box-title"> <?php echo mlx_get_lang('Status'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				  </div>
                </div>
				 <div class="box-body">
					<h4 class="box-title"><?php echo mlx_get_lang('Default Email Sender'); ?></h4>
					
					<label ><input class="minimal" type="radio" name="options[default_mailer]" value="php_mail" <?php if((isset($default_mailer) && $default_mailer == 'php_mail') || !isset($default_mailer) || (isset($default_mailer) && empty($default_mailer))){ echo 'checked="checked"'; }?>> <?php echo mlx_get_lang("PHP Mail"); ?></label>
					<br>
					<label ><input class="minimal" type="radio" name="options[default_mailer]" value="smtp" <?php if((isset($default_mailer) && $default_mailer == 'smtp') || !isset($default_mailer)){ echo 'checked="checked"'; }?>> <?php echo mlx_get_lang("SMTP"); ?></label>
					
					
					
					
				 </div>
			  	 <div class="box-footer">
					<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Changes'); ?></button>
                  </div>
			  </div>
		  </div>
		  
		  
		  </div>
		  
			</form>
        </section>
      </div>
      