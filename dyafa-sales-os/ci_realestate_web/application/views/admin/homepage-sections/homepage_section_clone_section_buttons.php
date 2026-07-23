<?php  
	if(isset($meta_contents)) extract($meta_contents);
	
	/*print_r($content_sections);*/
	ob_start();
	foreach($content_sections as $k => $current_section)
	{
		if($current_section['section_type'] != 'dynamic') continue;
		
		?>
		<button type="button" class="btn btn-default <?php echo $current_section['section_key']; ?> dyna-click" 
					data-section="<?php echo $current_section['section_key']; ?>" >
		<?php echo mlx_get_lang('Add' .' '. $current_section['section_title']); ?></button>
		
		<?php
		
	}	
	echo 	$dynamic_section = ob_get_clean();
