<?php  
	if(isset($meta_contents)) extract($meta_contents);
	
	
?>
<li class="<?php echo $section_cls;  
			if(isset($content_section_value['section_type']) && $content_section_value['section_type'] == 'dynamic' && $content_section_key != 'properties_section')
			{
				$ds_count++;
				echo ' dynamic_section de_'.$ds_count;
			} 
			?>">
			<?php 
			if(isset($content_section_value['section_type']) )
			{
			?>
		<input type="hidden" value="<?php echo $content_section_value['section_type']; ?>" name="<?php echo $content_section_key; ?>[section_type]">
			<?php } ?>
			
			<?php 
			if(isset($content_section_value['section_key']) )
			{
			?>
		<input type="hidden" value="<?php echo $content_section_value['section_key']; ?>" name="<?php echo $content_section_key; ?>[section_key]">
			<?php } ?>
			
			
			
			
			<div class="header-block">
			  <span class="handle ui-sortable-handle">
				<i class="fa fa-ellipsis-v"></i>
				<i class="fa fa-ellipsis-v"></i>
			  </span>
			  <?php /*if(isset($content_section_value['title'])){ */ ?>
			  <span class="text"><?php echo (!empty($ds_heading) ? $ds_heading.' - ' : ''); ?><?php echo mlx_get_lang(ucfirst($content_section_value['title'])); ?></span>
			  <?php /*}*/ ?>
			   <div class="radio_toggle_wrapper pull-right"> 
				<input type="radio" 
				<?php if((isset($is_enable) && $is_enable == 'Y') || !isset($is_enable)) { ?>
				checked="checked" 
				<?php } ?>
				id="<?php echo $content_section_key; ?>_<?php echo $ds_count; ?>_enable" value="Y" 
				name="<?php echo $content_section_key; ?>[is_enable]" class="toggle-radio-button">
				<label for="<?php echo $content_section_key; ?>_<?php echo $ds_count; ?>_enable"><?php echo mlx_get_lang('Enable'); ?></label>
				
				<input type="radio" id="<?php echo $content_section_key; ?>_<?php echo $ds_count; ?>_disable" value="N" 
				<?php if(isset($is_enable) && $is_enable == 'N') { ?>
				checked="checked" 
				<?php } ?>
				name="<?php echo $content_section_key; ?>[is_enable]" class="toggle-radio-button">
				<label for="<?php echo $content_section_key; ?>_<?php echo $ds_count; ?>_disable"><?php echo mlx_get_lang('Disable'); ?></label>
			  </div>
		  
	<?php  
			  if(!empty($section_fields)) { ?>
			  <div class="tools">
				<button class="btn btn-box-tool collapsed" ><i class="fa fa-chevron-down"></i></button>
				
				<?php if(isset($content_section_value['section_type']) && 
								$content_section_value['section_type'] == 'dynamic' )
				{
				?>
					<button class="btn btn-danger btn-sm remove_ds_btn" title="<?php echo mlx_get_lang('Remove Section'); ?>" data-toggle="tooltip"><i class="fa fa-trash fa-2x"></i></button>
					<button class="btn btn-<?php echo get_skin_class(); ?> btn-sm clone_section_btn" title="<?php echo mlx_get_lang('Clone Section'); ?>" data-toggle="tooltip"><i class="fa fa-clone fa-2x"></i></button>
				<?php } ?>
				
				
				
				
			  </div>
			  <?php } ?>
			</div>
			
		<?php if(!empty($section_fields)) { ?>
		  <div class="section_fields hide hidded23">
			  <?php 
			   global $single_field,$content_type;
				$content_type = $content_section_key;
				
				foreach($section_fields as $k => $single_field){
					
					
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
					
					
					if(isset($content_section_value['section_type']) && $content_section_value['section_type'] == 'dynamic' )
					{
						
						if(isset($single_field['element'])  && isset($single_field['options']) && $single_field['options'] == 'callback' ){
							
							$single_field['default'] = 'all';
							$single_field['options']  = apply_filters("homepage_sections_get_section_field", $single_field['element'] , $meta_content);
			

						}
						if(isset($single_field['id'])  && isset($single_field['options']) && $single_field['options'] == 'callback' ){
						
							$single_field['default'] = 'all';
							
							$single_field['options']  = apply_filters("homepage_sections_get_section_field", $single_field['id'] , $meta_content);
						}
						
						
					}
					
					
					if(isset($single_field['id']))
						$single_field['id'] = $single_field['id'].'_'.$ds_count;
					
					$this->load->view("$theme/templates/templ-".$single_field['type'] ); 
				}
			  ?>
		  </div>
		  <?php } ?>
		</li>