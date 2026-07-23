<?php  
	if(isset($meta_contents)) extract($meta_contents);
	
	/*print_r($content_sections);*/
	$dynamic_section = '';
	ob_start();
	$ds_count = 0;
	foreach($content_sections as $content_section_key => $current_section)
	{
		if($current_section['section_type'] != 'dynamic') continue;
		
		$section_fields = $myHelpers->config->item($current_section['section_key']."_fields") ;
		$ds_count++;
		$section_cls = '';///' section_no_'.$ds_count.' ';
								
		if(isset($current_section['section_class']) )
			$section_cls .= $current_section['section_class'];
		
		?>
		<li class="<?php echo $section_cls; ?> dynamic_section <?php echo $current_section['section_key']; ?>" 
			data-section="<?php echo $current_section['section_key']; ?>""
			>
			<input type="hidden" value="dynamic" name="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>[section_type]">
			<input type="hidden" value="<?php echo $current_section['section_key']; ?>" 
								id="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>"
								name="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>[section_key]">
								
			<div class="header-block">
			  <span class="handle ui-sortable-handle">
				<i class="fa fa-ellipsis-v"></i>
				<i class="fa fa-ellipsis-v"></i>
			  </span>
			  <span class="text"><?php echo mlx_get_lang(ucfirst($current_section['section_title'])); ?></span>
			  
			   <div class="radio_toggle_wrapper pull-right">
				<input type="radio" 	checked="checked" 
				id="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>_enable" value="Y" 
				name="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>[is_enable]" class="toggle-radio-button">
				<label for="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>_enable"><?php echo mlx_get_lang('Enable'); ?></label>
				
				<input type="radio" id="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>_disable" value="N" 
				
				name="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>[is_enable]" class="toggle-radio-button">
				<label for="<?php echo $content_section_key; ?>_<?php echo $ds_inner_count; ?>_disable"><?php echo mlx_get_lang('Disable'); ?></label>
			  </div>
		  
			  <?php if(!empty($section_fields)) { ?>
			  <div class="tools">
				<button class="btn btn-box-tool collapsed" ><i class="fa fa-chevron-down"></i></button>
				<button class="btn btn-danger btn-sm remove_ds_btn" title="<?php echo mlx_get_lang('Remove Section'); ?>" data-toggle="tooltip"><i class="fa fa-trash fa-2x"></i></button>
				<button class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> btn-sm clone_section_btn" title="<?php echo mlx_get_lang('Clone Section'); ?>" data-toggle="tooltip"><i class="fa fa-clone fa-2x"></i></button>
			  </div>
			  <?php } ?>
			  
			  
			  
			</div>
		  <?php 
		  
		  
		  
		  if(!empty($section_fields)) { 
		  
			
		  ?>
		  <div class="section_fields hide hidde">
			  <?php 
			   global $single_field,$content_type;
				
				
				
				foreach($section_fields as $k => $single_field){
					$content_type = $content_section_key.'_'.$ds_inner_count;
					
					/*print_r($single_field);
					if(isset($single_field['section_key'])){
						echo $sec_key = $single_field['section_key'];
						$section_fields = $myHelpers->config->item($sec_key."_fields") ;
						
					}else		$section_fields = array();	*/
					
					/*echo $single_field['id'] . " - "; */
					
					if(isset($single_field['element'])  && isset($single_field['options']) && $single_field['options'] == 'callback' ){
							/*echo $single_field['element'];*/
							$single_field['default'] = 'all';
							$single_field['options']  = apply_filters("homepage_sections_get_section_field", $single_field['element'] , $meta_content);
			

						}
						
						
					if(isset($single_field['id']) &&  isset($single_field['options']) && $single_field['options'] == 'callback'  )/*&& $single_field['id'] == 'dynamic_property_type')*/
					{
						$single_field['default'] = 'all';
						$single_field['options']  = apply_filters("homepage_sections_get_section_field", $single_field['id']);
					}
					
					
					if(isset($single_field['name']) && isset(${$single_field['name']}) && $has_val_saved)
					{
						global $meta_content;
						$meta_content[$single_field['name']] = ${$single_field['name']};
						
					}
					else if(!isset($meta_content))
					{
						global $meta_content;
						$meta_content = array();
					}
					
					
					if(isset($single_field['id']))
					{
						$single_field['id'] = $single_field['id'].'_'.$ds_inner_count;
					}
					/*echo "<pre> single_field ";print_r($single_field);  echo "</pre>";*/
					
					
					
					if(isset($single_field['name']) && $single_field['name'] == 'show_as')
						$meta_content[$single_field['name']] = 'grid';
					else if(isset($single_field['name']) && ( $single_field['name'] == 'heading' || 
					$single_field['name'] == 'sub_heading') )
						$meta_content[$single_field['name']] = '';
					else if(isset($single_field['name']) && ( $single_field['name'] == 'no_of_item_in_grid_list' || 
					$single_field['name'] == 'no_of_item_in_carousel'))
						$meta_content[$single_field['name']] = '6';
					else if(isset($single_field['name']) && ( $single_field['name'] == 'show_view_more' || 
					$single_field['name'] == 'show_nav' || $single_field['name'] == 'show_nav_dots'))
						$meta_content[$single_field['name']] = 'yes';
					else if(isset($single_field['name']) && $single_field['name'] == 'auto_start')
						$meta_content[$single_field['name']] = 'no';	
					else if(isset($single_field['name']) && $single_field['name'] == 'carousel_interval')
						$meta_content[$single_field['name']] = '5000';	
					$this->load->view("$theme/templates/templ-".$single_field['type'] ); 
					
					/*print_r($single_field);*/
				}
			  ?>
		  </div>
		  <?php } ?>
		</li>
		<?php  
								
	}	
		$dynamic_section .= ob_get_contents();
	ob_end_clean();
	
	echo $dynamic_section;
?>
