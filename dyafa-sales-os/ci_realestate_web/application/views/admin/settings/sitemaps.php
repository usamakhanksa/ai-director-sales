<style type="text/css">
	
	.btn-highlight{
		color: #fff;
		background-color: #3c8dbc;
		padding: .5em 1em;
		cursor: pointer;
		border: 1px solid;
	}

</style>
      <div class="content-wrapper">
       <section class="content-header">
          <h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('Sitemaps'); ?> </h1>
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
                  <h3 class="box-title"><?php echo mlx_get_lang('Sitemap Settings'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
                </div>
				  <div class="box-body">
                	
					
					<div class="form-group" >
						<label for="sitemap_attachments"><?php echo mlx_get_lang('Sitemaps Attachments'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" id="sitemap_attachments_save_to_server" value="save_to_server" 
							<?php 
							if( (isset($sitemap_attachments) && $sitemap_attachments == 'save_to_server')|| 
							!isset($sitemap_attachments))  
							{ echo ' checked="checked" '; }
							?> name="sitemap_attachments" 
							class="toggle-radio-button">
							<label for="sitemap_attachments_save_to_server"><?php echo mlx_get_lang('Save to Server'); ?></label>
							
							<input type="radio" id="sitemap_attachments_download" 
							<?php 
							if((isset($sitemap_attachments) && $sitemap_attachments == 'download_as_attachment'))
							{ echo ' checked="checked" '; }
							?>  value="download_as_attachment" name="sitemap_attachments" 
							class="toggle-radio-button">
							<label for="sitemap_attachments_download"><?php echo mlx_get_lang('Download as Attachments'); ?></label>
						</div>
						<p> 
						<?php echo mlx_get_lang('In case you have your site as multi-language, please select Sitemaps attachments as '); ?>
						
						<label for="sitemap_attachments_save_to_server" 
						class="btn-highlight btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?>"><?php echo mlx_get_lang('Save to Server'); ?></label> 
						<?php echo mlx_get_lang('because there will be multiple sitemaps for every language enabled for your website.'); ?>
						 <br>
						<?php echo mlx_get_lang('Remember to link that index in your robots.txt file, like:'); ?>
						
						<code>	<?php echo mlx_get_lang('Sitemap'); ?>: <?php echo front_url("sitemap_index.xml")?>	 </code>
						</p>
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
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      