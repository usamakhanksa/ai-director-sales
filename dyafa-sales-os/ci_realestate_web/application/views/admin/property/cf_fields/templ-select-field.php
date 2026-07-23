<?php
	
	global $single_field,$meta_content,$content_type;
	$required = ( isset($single_field['is_req']) && $single_field['is_req'] == 'Y') ? true : false;
	
	$field_name = $single_field['name'];
	
	if(is_array($meta_content) && array_key_exists($field_name,$meta_content))
		$value_existing = $meta_content[$field_name];
	else if(isset($single_field['default']))
		$value_existing = $single_field['default'];
	else 	
		$value_existing = '';
	
	if(empty($content_type))	
		$content_type = "custom_fields";
			
	$options = array();
	if(isset($single_field['options']))
		$options = $myHelpers->config->item($single_field['options']) ;
	
	
	
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
			
			<select class="form-control select2_elem <?php echo $field_class; ?>"
			name="<?php echo $content_type; ?>[<?php echo $single_field['name'];?>]" id="<?php echo $single_field['id'];?>" >
			<?php if(count($options) > 0 ){?>
				<?php foreach($options as $k=>$v){?>
				<option value="<?php echo $k; ?>"
				<?php if(!empty($value_existing) && $k ==$value_existing){?>
					selected="selected"
				<?php } ?>	
				>
				<?php echo $v; ?></option>
				<?php } ?>	
			<?php } ?>
			</select>
		</div>
	</div>	
</div>
