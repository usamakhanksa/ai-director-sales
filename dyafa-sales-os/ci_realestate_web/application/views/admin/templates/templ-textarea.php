<?php
	
	global $single_field,$meta_content,$content_type;
	$required = ( isset($single_field['required']) && $single_field['required'] == 'required') ? true : false;
	
	$field_name = $single_field['name'];
	if(is_array($meta_content) && array_key_exists($field_name,$meta_content))
		$value = $meta_content[$field_name];
	else	
		$value = $single_field['default'];
		
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
	
?>
<div class="form-group <?php echo $field_parent_class; ?>">
	<div class="row">
		<div class="col-md-3">
			<label for="<?php echo $single_field['id'];?>"><?php echo mlx_get_lang($single_field['title']); ?> 
				<?php if($required ){ ?>
				<span class="text-red">*</span>
				<?php } ?>
			</label>
		</div>
		<div class="col-md-9">
			<textarea class="form-control <?php echo $field_class; ?>" rows="3"  <?php echo $field_attributes; ?>
			<?php if($required ){ ?>
				required="required" 
				<?php } ?>
				name="<?php 
					if(isset($single_field['field_id']))
						echo $content_type.'['.$single_field['field_id'].']['.$single_field['name'].']';
					else
						echo $content_type.'['.$single_field['name'].']';
				?>" 
				id="<?php echo $single_field['id'];?>"
			 ><?php echo $value;?></textarea>
		</div>
	</div>
</div>

