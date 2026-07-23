<?php
	
	global $single_field,$meta_content,$content_type;
	$required = ( isset($single_field['required']) && $single_field['required'] == 'required') ? true : false;
	
	
	$field_name = $single_field['name'];
	if(is_array($meta_content) && array_key_exists($field_name,$meta_content))
		$value = $meta_content[$field_name];
		
	if(empty($content_type))	
		$content_type = "content";
	
	$field_class = '';
	if(isset($single_field['class']))
	{
		$field_class = $single_field['class'];
	}
	
	$field_attributes = '';
	if(isset($single_field['attributes']) && !empty($single_field['attributes']))
	{
		foreach($single_field['attributes'] as $fak=>$fav)
		{
			$field_attributes .= $fak.'="'.$fav.'" ';
		}
	}
	
	$field_parent_class = '';
	if(isset($single_field['parent_class']))
	{
		$field_parent_class = $single_field['parent_class'];
	}
	
	$image_type = "dyna_content";
	if(isset($single_field['image_type']))
		$image_type = $single_field['image_type'];
	
	if(isset($value) && !empty($value) && !file_exists('../uploads/'.$image_type.'/'.$value)) {
		$value = '';
	}
	
	
	
?>

<div class="form-group">
	<div class="row">
		<div class="col-md-3">
			<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Photo'); ?></label>
		</div>
		<div class="col-md-9">
			<div class="form-group pl_image_container">
				<label class="custom-file-upload" 
				data-type="<?php echo $image_type; ?>" 
				id="<?php echo $single_field['id'];?>" 
				<?php if(isset($value) && !empty($value)) { echo 'style="display:none;"';}?>>
					<?php echo mlx_get_lang('Drop images here'); ?>
					<br>
					<strong><?php echo mlx_get_lang('OR'); ?></strong>
					<br>
					<?php echo mlx_get_lang('Click here to select images'); ?>
				</label>
				<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
				<?php if(isset($value) && !empty($value)) { ?>
					<a class="pl_file_link" href="<?php echo base_url().'../uploads/'.$image_type.'/'.$value; ?>" 
					download="<?php echo $value; ?>" style="">
						<img src="<?php echo base_url().'../uploads/'.$image_type.'/'.$value; ?>" >
					</a>
					<a class="pl_file_remove_img" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
				<?php }else{ ?>
					<a class="pl_file_link" href="" download="" style="display:none;">
						<img src="" >
					</a>
					<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
				<?php } ?>
				<input type="hidden" name="<?php echo $content_type; ?>[<?php echo $single_field['name'];?>]" class="pl_file_hidden"
				value="<?php if(isset($value) && !empty($value)) echo $value; ?>" id="<?php echo $single_field['id'];?>">
			</div>
		</div>
	</div>
</div>
