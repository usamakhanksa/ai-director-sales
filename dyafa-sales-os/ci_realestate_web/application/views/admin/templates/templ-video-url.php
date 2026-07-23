<?php
	
	global $single_field,$meta_content,$content_type;
	$required = ( isset($single_field['required']) && $single_field['required'] == 'required') ? true : false;
	
	$field_name = $single_field['name'];
	if(is_array($meta_content) && array_key_exists($field_name,$meta_content) && !empty($meta_content[$field_name]))
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
	
	if(!empty($value))
		$value = array_filter($value);
	
	
if(!empty($value) && count(array_filter($value)) == count($value)) {
	
	foreach($value as $vk=>$vv)
	{
?>
	<div class="form-group video-url-container <?php echo $field_parent_class; ?>">
		<div class="row">
			<div class="col-md-3">
				<label for="<?php echo $single_field['id'];?>_<?php echo $vk;?>"><?php echo mlx_get_lang($single_field['title']); ?> 
					<?php if($required ){ ?>
					<span class="text-red">*</span>
					<?php } ?>
				</label>
			</div>
			<div class="col-md-9">
				<div class="input-group">
					<input type="url" class="form-control <?php echo $field_class; ?>"  <?php echo $field_attributes; ?>
					<?php if($required ){ ?>
					required="required" 
					<?php } ?>
					name="<?php 
						if(isset($single_field['field_id']))
							echo $content_type.'['.$single_field['field_id'].']['.$single_field['name'].'][]';
						else
							echo $content_type.'['.$single_field['name'].'][]';
					?>" 
					id="<?php echo $single_field['id'];?>_<?php echo $vk;?>"
					value="<?php echo $vv;?>" 
					>
					<span class="input-group-addon btn btn-danger rmv-vdo-url-btn"><i class="fa fa-trash"></i></span>
				</div>
			</div>
		</div>
	</div>
<?php
	}
} 
else {
?>
<div class="form-group video-url-container <?php echo $field_parent_class; ?>">
	<div class="row">
		<div class="col-md-3">
			<label for="<?php echo $single_field['id'];?>_0"><?php echo mlx_get_lang($single_field['title']); ?> 
				<?php if($required ){ ?>
				<span class="text-red">*</span>
				<?php } ?>
			</label>
		</div>
		<div class="col-md-9">
				<div class="input-group">
				<input type="url" class="form-control <?php echo $field_class; ?>"  <?php echo $field_attributes; ?>
				<?php if($required ){ ?>
				required="required" 
				<?php } ?>
				name="<?php 
					if(isset($single_field['field_id']))
						echo $content_type.'['.$single_field['field_id'].']['.$single_field['name'].'][]';
					else
						echo $content_type.'['.$single_field['name'].'][]';
				?>" 
				id="<?php echo $single_field['id'];?>_0"
				value="" 
				>
				<span class="input-group-addon btn btn-danger rmv-vdo-url-btn"><i class="fa fa-trash"></i></span>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<div class="form-group text-right">
	<button type="button" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> add-video-url-btn"><?php echo mlx_get_lang('Add More URL'); ?></button>
</div>