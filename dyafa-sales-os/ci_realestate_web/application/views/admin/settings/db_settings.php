
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-database"></i> <?php echo mlx_get_lang('DB Settings'); ?> </h1>
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
			<div class="row">
			<div class="col-md-12">   
			   
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('DB Settings'); ?></h3>
			    
				</div>
                  <div class="box-body">
                    
					
				  <button name="submit" type="submit" id="save" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> ">
					<i aria-hidden="true" class="fa fa-database"></i> 
					<?php echo mlx_get_lang('Create Database Backup'); ?>
				  </button>
					
					
                  </div>

              </div>
			  
			  
		  </div>
		  
		  
		  </div>
		  
			</form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      