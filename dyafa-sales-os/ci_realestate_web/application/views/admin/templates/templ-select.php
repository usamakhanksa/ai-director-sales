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
		$options = $single_field['options'];
	else if($myHelpers->config->item($single_field['options']))
		$options = $myHelpers->config->item($single_field['options']);
	
	
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
<div class="form-group <?php echo $field_parent_class; ?>" 
	<?php /*if(isset($single_field['is_hidden']) && $single_field['is_hidden'] == 'Y') echo 'style="display:none;"';*/ ?>>
	<div class="row">
		<div class="col-md-3">
			<label for="<?php echo $single_field['id'];?>"><?php echo mlx_get_lang($single_field['title']); ?> 
				<?php if($required ){ ?>
				<span class="text-red">*</span>
				<?php } ?>
			</label>
		</div>

		<div class="col-md-9">	
			<select class="form-control select2_elem <?php echo $field_class; ?>" <?php echo $field_attributes; ?> 
			fieldname_<?php echo $single_field['name'];?>
			name="<?php echo $content_type; ?>[<?php echo $single_field['name'];?>]" id="<?php echo $single_field['id'];?>" >
			<?php if(count($options) > 0 ){?>
				<?php foreach($options as $k=>$v){

					if(isset($v['attributes']) && !empty($v['attributes']))
					{
				?>
				<option value="<?php echo $k; ?>"
				
				<?php 
				foreach($v['attributes'] as $ak=>$av)
				{
					echo ' data-'.$ak.'="'.$av.'" ';
				}
				?>
				
				<?php if(!empty($value_existing) && $k ==$value_existing){?>
					selected="selected"
				<?php } ?>	
				>
				<?php echo $v['title']; ?></option>
				
				<?php 
				}
				else
				{
				?>
					<option value="<?php echo $k; ?>"
				
					<?php if(!empty($value_existing) && $k ==$value_existing){?>
						selected="selected"
					<?php } ?>	
					>
					<?php echo $v; ?></option>
					
				<?php	}
					}
				?>	
			<?php } ?>
			</select>
		</div>
	</div>	
</div>
