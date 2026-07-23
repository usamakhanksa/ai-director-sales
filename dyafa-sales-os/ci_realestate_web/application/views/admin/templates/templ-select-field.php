<?php
	
	global $single_field,$meta_content,$content_type;
	$required = ( isset($single_field['required']) && $single_field['required'] == 'required') ? true : false;
	
	$field_name = $single_field['name'];
	if(is_array($meta_content) && array_key_exists($field_name,$meta_content))
		$value_existing = $meta_content[$field_name];
	else	
		$value_existing = $single_field['default'];
		
	if(empty($content_type))	
		$content_type = "content";


	
			
	$options = array();
	if(isset($single_field['options']))
		$options = $myHelpers->config->item($single_field['options']) ;
	
	
	$elem_class = "";
	if(isset($single_field['elem_class']))
		$elem_class = $single_field['elem_class'];
	
	$section_class = "";
	if(isset($single_field['section_class']))
		$section_class = $single_field['section_class'];
	
	
	
	
	$content_field = (isset($single_field['content_field'])) ? $single_field['content_field'] : "";
	
	if($content_field != ""){
	
		$sliders = $myHelpers->Common_model->commonQuery("select * from contents where content_type = '$content_field' 
							order by updated_at DESC");
		$title_field = (isset($single_field['title_field'])) ? $single_field['title_field'] : "";
		if ($sliders->num_rows() > 0 && $title_field != "")
		{
			foreach ($sliders->result() as $row)
			{
				$key = $row->content_id;
				$meta = json_decode($row->meta_content,true);
				$value = (isset($meta[$title_field]))?$meta[$title_field]: " -- ";
				$options [$key] = $value;
			}
		}
	}
	
	$field_class = '';
	if(isset($single_field['class']))
	{
		$field_class = $single_field['class'];
	}
	
	$field_parent_class = '';
	if(isset($single_field['parent_class']))
	{
		$field_parent_class = $single_field['parent_class'];
	}
	
	$field_attributes = '';
	if(isset($single_field['attributes']) && !empty($single_field['attributes']))
	{
		foreach($single_field['attributes'] as $fak=>$fav)
		{
			$field_attributes .= $fak.'="'.$fav.'" ';
		}
	}
	
?>
<div class="form-group <?php echo $field_parent_class; ?>">
	<div class="row">
		<div class="col-md-3">
			<label for="<?php echo $single_field['id'];?>"><?php echo mlx_get_lang($single_field['title']);?> 
				<?php if($required ){ ?>
				<span class="text-red">*</span>
				<?php } ?>
			</label>
		</div>

		<div class="col-md-9">	
			<div class="form-control <?php echo $section_class; ?>" style="border:none;padding:0;">
				<div class="input-group">
					<select class="form-control select2_elemSSS <?php echo $field_class; ?>" <?php echo $field_attributes; ?>
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
					<span class="input-group-btn">
						
					</span>
				</div>	
			</div>	
		</div>
	</div>	
</div>
