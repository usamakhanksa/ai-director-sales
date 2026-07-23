
<div class="box box-<?php echo get_skin_class(); ?> ">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo mlx_get_lang(' Login Details'); ?></h3>
	  <div class="box-tools pull-right">
		<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>
	  <div class="box-body">
			<?php if( form_error('user_meta[first_name]')) 	  { 	echo form_error('user_meta[first_name]'); 	  } ?>
			<?php if( form_error('user_meta[last_name]')) 	  { 	echo form_error('user_meta[last_name]'); 	  } ?>
			<?php if( form_error('user_meta[mobile_no]')) 	  { 	echo form_error('user_meta[mobile_no]'); 	  } ?>
			<?php if( form_error('UserName')) 	  { 	echo form_error('UserName'); 	  } ?>
			<?php if( form_error('user_email')) 	  { 	echo form_error('user_email'); 	  } ?>
			<?php if( form_error('Password')) 	  { 	echo form_error('Password'); 	  } ?>
			<?php if( form_error('RepeatPassword')) 	  { 	echo form_error('RepeatPassword'); 	  } ?>	
	  
			  <div class= "col-md-6 form-group">
			  <label for="first_name"><?php echo mlx_get_lang('First Name'); ?> <span class="required">*</span></label>
			  <input type="text" id="first_name" class="form-control" required name="user_meta[first_name]" 
					value="<?php if(isset($_POST['user_meta']['first_name'])) 
									echo $_POST['user_meta']['first_name'];
								 else if(isset($user_id))
									echo get_user_meta($user_id , 'first_name');	
									?>" 
					placeholder ="Enter First name">

			</div>
			<div class="col-md-6 form-group">
			  <label for="last_name"><?php echo mlx_get_lang('Last Name'); ?> <span class="required">*</span></label>
			  <input type="text" id="last_name" class="form-control" name="user_meta[last_name]" 
					value="<?php if(isset($_POST['user_meta']['last_name'])) 
								     echo $_POST['user_meta']['last_name'];
								 else if(isset($user_id))
									echo get_user_meta($user_id , 'last_name');	 
									 ?>"		
					placeholder="Enter Last Name">
			</div>
			<div class="col-md-6 form-group">
			  <label for="UserEmail"><?php echo mlx_get_lang(' Email'); ?> <span class="required">*</span></label>
			  <input type="email" id="UserEmail" class="form-control" required name="UserEmail"
			  value="<?php if(isset($_POST['UserEmail'])) 
								echo $_POST['UserEmail'];
						   else if(isset($user_id))
									echo get_user_meta($user_id , 'user_email');		?>" 
					<?php if(isset($email_edit) && !$email_edit) echo ' readonly ';?>				
								placeholder="Enter Email">
			</div>

			<div class="col-md-6 ">
				<div class=" form-group">
				  <label for="UserName"><?php echo mlx_get_lang('Username '); ?> <span class="required">*</span></label>
				  <input type="text" id="UserName" class="form-control" 
					value="<?php if(isset($_POST['UserName'])) 
								echo $_POST['UserName'];
						   else if(isset($user_id))
									echo get_user_meta($user_id , 'user_name');	 	?>"	
					
					required name="UserName" 
					<?php if(isset($email_edit) && !$email_edit) echo ' readonly ';?>
					placeholder="Enter Username">
				</div>
			</div>


			<div class="col-md-6 form-group">
			  <label for="user_phone"><?php echo mlx_get_lang('Phone Number'); ?> <span class="required">*</span></label>
			  <input type="text" id="user_phone" class="form-control"  name="user_meta[mobile_no]"  placeholder="Enter Phone Number"
					value="<?php if(isset($_POST['user_meta']['mobile_no'])) 
								      echo $_POST['user_meta']['mobile_no'];
								 else if(isset($user_id))
									echo get_user_meta($user_id , 'mobile_no'); 	  
									  ?>"
				>
			</div>
			
			<div class="col-md-6 ">
				<div class=" form-group">
				  <label for="Password"><?php echo mlx_get_lang('Password'); ?> <span class="required">*</span></label>
				  <input type="password" id="Password" class="form-control" required name="Password" required placeholder="Enter Password">
				</div>	
			</div>
			
			<div class="col-md-6 ">
				<div class=" form-group">
				  <label for="RepeatPassword"><?php echo mlx_get_lang('Confirm Password'); ?> <span class="required">*</span></label>
				  <input type="password" id="RepeatPassword" class="form-control" required name="RepeatPassword" required placeholder="Enter Confirm Password">
				</div>
			</div>
			
			
			
		
			
					
   </div>
						
  </div>