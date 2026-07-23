<?php
	
	global $single_field,$meta_content,$content_type;
	$required = ( isset($single_field['is_req']) && $single_field['is_req'] == 'Y') ? true : false;
	
	$field_name = $single_field['name'];
	if(is_array($meta_content) && array_key_exists($field_name,$meta_content))
		$value = $meta_content[$field_name];
	else if(isset($single_field['default']))
		$value = $single_field['default'];
	else
		$value = '';
	
	if(empty($content_type))	
		$content_type = "custom_fields";	
	
	$field_class = '';
	if(isset($single_field['class']))
	{
		$field_class = $single_field['class'];
	}
	
?>
<div class="form-group ">
	<div class="row">
		<div class="col-md-12">
			<label for="<?php echo $single_field['id'];?>"><?php echo $single_field['title'];?> 
				<?php if($required ){ ?>
				<span class="text-red">*</span>
				<?php } ?>
			</label>
		
			<textarea data-lang_code="en" class="form-control ckeditor-element <?php echo $field_class; ?>" 
			<?php if($required ){ ?>
				required="required" 
				<?php } ?>
				name="<?php echo $content_type; ?>[<?php echo $single_field['name'];?>]" 
				id="<?php echo $single_field['id'];?>"
			 ><?php echo $value;?></textarea>
		</div>
	</div>
</div>

