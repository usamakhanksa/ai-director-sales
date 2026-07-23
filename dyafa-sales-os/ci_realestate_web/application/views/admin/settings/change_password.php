
<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('Change Password'); ?> </h1>
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
		  <h3 class="box-title"><?php echo mlx_get_lang('Change Password'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		  </div>
		</div>
			
		
		  <div class="box-body">
			
			<div class="form-group">
			  <label for="password"><?php echo mlx_get_lang('Password'); ?></label>
			  <input type="password" class="form-control" required="required" 
			  name="password" id="password" autocomplete="new-password">
			</div>
			
			<div class="form-group">
			  <label for="repeat_password"><?php echo mlx_get_lang('Repeat Password'); ?></label>
			  <input type="password" class="form-control" required="required" 
			  name="repeat_password" id="repeat_password" autocomplete="new-repeat-password">
			</div>
			
		  </div>

	  </div>
  </div>
  <div class="col-md-4">
		<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
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
</section>
</div>
      