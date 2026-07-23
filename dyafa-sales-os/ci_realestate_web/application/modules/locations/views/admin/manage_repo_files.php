<?php $this->load->view("admin/header-top");?>
      
	  <?php $this->load->view("admin/sidebar-left");?>
<?php 

//$document_file_type = $myHelpers->global_lib->get_option('document_file_type');


/**
"open_all" : true 

**/
echo link_tag("application/views/admin/assets/plugins/jstree/themes/default/style.min.css");
echo script_tag("application/views/admin/assets/plugins/jstree/jstree.min.js");
?>


      
	  <?php 
		
	  ?>
	  
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1> <?php echo mlx_get_lang('Manage Locations'); ?> </h1>
          
        </section>

		
        <!-- Main content -->
        <section class="content">
			
			
			<div class="row">
			<div class="col-md-12">   
			   
			  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Manage Locations'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					<!--<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>-->
				  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
				  
				<?php
						//print_r($repo_files);
					if(isset($repo_files) && count($repo_files) > 0){
						/*echo "<ul class='repo_files' >";
						foreach($repo_files as $file){
							echo "<li data-file='$file'>$file</li>";
						}
						echo "</ul>"; */
						
						echo mlx_get_lang('Download file from'). "<a href='http://demo.mindlogixtech.com/cc_files/locations-json-files.zip'>".mlx_get_lang('Here')."</a> ".mlx_get_lang('and extract to root/locations/json folder.')." ";
					}
				 ?>		
				<?php echo mlx_get_lang('Please wait while downloading repo files ...'); ?>
				</div>
              </div>
			</div>
		  </div><!-- end row 1-->	  

		
        </section>
      </div>