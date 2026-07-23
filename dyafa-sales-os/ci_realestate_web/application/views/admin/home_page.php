<?php 


?>
<style >

.section_fields .form-group label:not(.col-md-2) {
    /*width: 16.66666667%;*/
}
.section_fields .form-group > .form-control, .section_fields .form-group > .cke {
    display: inline-block;
    width: calc((100% - 16.66666667%) - 15px);
}


</style>
<div class="content-wrapper">
	<section class="content-header">
	  <h1 class="page-title"><i class="fa fa-home"></i> <?php echo mlx_get_lang('Homepage Sections'); ?></h1>
	  <?php 	do_action("cms_notifications");		?>
		<?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
		?> 
	</section>

	<section class="content">
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'homepage_section_form');		 			
		echo form_open_multipart('',$attributes); ?>
		<div class="row">
			<div class="col-md-12">
			
				<div class="box box-<?php echo get_skin_class(); ?> homepage_section_container" >
					<div class="box-body">
					<?php 
					
					$old_content_sections = array();
					$content_sections2 = array();
					/*echo "<pre> meta_content_lists ";print_r($meta_content_lists);  echo "</pre>";
					echo "<pre> content_sections ";print_r($content_sections);  echo "</pre>";*/
					if(isset($content_sections) && !empty($content_sections)) 
					{ 
						
						/*echo "<pre> content_sections before ";print_r($content_sections);  echo "</pre>";*/
						$old_content_sections = $content_sections; 
						/*foreach($content_sections as $k => $v)
						{
							if($v['section_type'] == 'dynamic'){
								unset($content_sections[$k]);
							}
						}*/	
						
						if(isset($meta_content_lists) && !empty($meta_content_lists))
						{
							 
							
							foreach($meta_content_lists as $k => $meta_content)
							{
								if(  isset($meta_content['section_type'])){
									/*echo $k . "</br>";*/
									if($meta_content['section_type'] == 'fixed' && array_key_exists($k , $old_content_sections)){
										$content_sections2[$k] = array_merge($content_sections[$k], $meta_content);
										unset($content_sections[$k]);/**/	
									}else 	if($meta_content['section_type'] == 'dynamic'  && 
												array_key_exists($meta_content['section_key'] , $old_content_sections)){
										
										$section_key = $meta_content['section_key'];
										$content_sections2[$k] = array_merge($old_content_sections[$section_key], $meta_content);
										unset($content_sections[$section_key]);/**/
									}	
									
									
								}
								
							}
							
						}
						/*echo "<pre> content_sections ";print_r($content_sections2);  echo "</pre>";*/
						/*$content_sections = $content_sections2;*/
						$content_sections = array_merge($content_sections2 , $content_sections);
						
						
					  /*echo "<pre>";print_r($content_sections);echo "</pre>";*/
					  $dynamic_section = '';
					  $dynamic_video_section = '';
					  ?>
					  <ul class="todo-list ui-sortable">
							<?php 
							
							$manage_contents = array();
							$ds_count = 0;
							$ds_heading = '';
							
							foreach($content_sections  as $content_section_key => $content_section_value)
							{ 
							
								//print_r($content_section_value);
								
								$ds_count++;
								$sec_key = $content_section_value['section_key'];
								$section_type = $content_section_value['section_type'];
								$section_fields = $myHelpers->config->item($sec_key."_fields") ;
								
								global $meta_content;
								$meta_content = array();
								
								
								$has_val_saved = false;
								if(isset($meta_content_lists) && isset($meta_content_lists[$content_section_key]))
								{
									$has_val_saved = true;
									foreach($meta_content_lists[$content_section_key] as $csk=>$csv)
									{
										${$csk} = $csv;
									}
								}else{
										if($section_type == 'dynamic')
											continue;
								}		

								
								$section_cls = ' section_no_'.$ds_count.' ';
								
								if(isset($content_section_value['section_class']) )
									$section_cls .= $content_section_value['section_class'];
								
								
							$ds_inner_count = 0;						
							$hp_args =  array();						
							$hp_args['content_section_key'] = $content_section_key;
							$hp_args['content_section_value'] = $content_section_value;
							$hp_args['section_cls'] = $section_cls;
							$hp_args['ds_inner_count'] = $ds_inner_count;
							$hp_args['ds_count'] = $ds_count;
							$hp_args['section_fields'] = $section_fields;
							if(isset($meta_content_lists[$content_section_key]))
								$hp_args['meta_contents'] = $meta_content_lists[$content_section_key];
							$hp_args['has_val_saved'] = $has_val_saved;
							
							do_action("homepage_section_header_edit" , $hp_args);	 
			
						} ?>
					  </ul>
			<?php } ?>
					  
					  
					
		
					</div>
					<div class="box-body" style="border-top:1px solid #f4f4f4;">
						<?php 
						
						$hp_args = array();
						$hp_args['content_sections'] = $old_content_sections;
						do_action("homepage_section_clone_section_buttons" , $hp_args);
						?>
						
					  </div>
				 
				  <div class="box-footer">
				  	<button type="submit" name="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right submit-section-btn"><?php echo mlx_get_lang('Save'); ?></button>
				  </div>
				  </div>
			</div>
		
		</div>
		</form>
		
		<ul id="dynamic-sections" style="display:none;">	
			<?php 
			$hp_args = array();
			$hp_args['content_sections'] = $old_content_sections;
			$hp_args['meta_contents'] = array();/**/
			do_action("homepage_section_clone_section" , $hp_args);
			?>
		</ul>
		
			
	</section>
	
<style>
.clone_section_btn, .remove_ds_btn {
    margin-left: 10px;
}
</style>	

<?php		do_action("admin_footer_scripts","homepage_updates"); ?>	
	
	
</div>