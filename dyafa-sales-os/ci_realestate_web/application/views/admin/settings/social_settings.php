
<?php 
	$social_media = array();
	if(isset($options_list) && $options_list->num_rows()>0)
	{
		foreach($options_list->result() as $row)
		{
			$social_media = json_decode($row->option_value, true);
		}
	}
	
	
?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
         	 <h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('Social Settings'); ?> </h1>
			  <?php echo validation_errors(); 
					if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
					{
						echo $_SESSION['msg'];
						unset($_SESSION['msg']);
					}
					?>
        </section>

        <!-- Main content -->
        <section class="content">
			<!-- form start -->
               <!-- <form role="form">-->
             <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('',$attributes); 
			
			?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Social Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
                </div><!-- /.box-header -->
				
				
                  <div class="box-body">
                    
					
					
					
					<?php 
					foreach($social_medias as $key => $details){
					$social_media_site = $social_media[$key];
					$url = (isset($social_media[$key]['url']))?$social_media[$key]['url']:'';
					$enable = (isset($social_media[$key]['enable']))?$social_media[$key]['enable']:'';
					 
					?>
					<div class="form-group">
                      <label for="facebook"><?php echo $details['title']; ?></label>
                      <div class="input-group">
					  <span class="input-group-addon">
					  		<input type="hidden" class="form-control "
					  name="options[<?php echo $key; ?>][icon]"  
					  value="<?php echo $details['fa-icon']; ?>">
                          <i class="fa <?php echo $details['fa-icon']; ?>"></i>
                        </span> 
					  <input type="url" class="form-control "
					  name="options[<?php echo $key; ?>][url]" id="<?php echo $key; ?>" placeholder="<?php echo $details['placeholder']; ?>" 
					  value="<?php if(isset($url)) echo $url; ?>">
					  
					  <span class="input-group-addon">
                          <input type="checkbox" class="minimal"
						  <?php if($enable == '1'){?>
						  checked="checked" 
						  <?php } ?>
						  name="options[<?php echo $key; ?>][enable]" value="1">
                        </span> 
					  </div>
					  
                    </div>
					
					<?php
					
					}?>
					
					
					
					
					
                  </div>
					
              </div>
			  
			  
		  </div>
		  
		  <div class="col-md-4">
		  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> sticky_sidebar">
			  <div class="box-header with-border">
                  <h3 class="box-title"> <?php echo mlx_get_lang('Status'); ?></h3>
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
      