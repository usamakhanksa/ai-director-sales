<?php 
	if(isset($options_list) && $options_list->num_rows()>0)
	{
		
		foreach($options_list->result() as $row)
		{
			
			${$row->option_key} = $row->option_value;
		}
	}
	
?>
<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('Media Settings'); ?> </h1>
  
</section>

<section class="content">
	  <?php 
	
	$attributes = array('name' => 'add_form_post','class' => 'form');		 			
	echo form_open_multipart('admin/media/settings',$attributes); ?>
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-8">   
	   
	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Media Settings'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		  </div>
		</div>
		<div class="box-body">
			<div class="form-group" >
				<label for="enbale_watermark_on_media"><?php echo mlx_get_lang('Enable Watermark'); ?></label>
				 <div class="radio_toggle_wrapper ">
					<input type="radio" id="enbale_watermark_on_media_yes" value="Y" 
					data-target="enbale_watermark_on_media_yes" data-elem="enbale_watermark_on_media_elem"
					<?php 
					if(isset($enbale_watermark_on_media) && $enbale_watermark_on_media == 'Y')  
					{ echo ' checked="checked" '; }
					?> name="options[enbale_watermark_on_media]" 
					class="toggle-radio-button show_hide_setting_elem">
					<label for="enbale_watermark_on_media_yes"><?php echo mlx_get_lang('Yes'); ?></label>
					
					<input type="radio" id="enbale_watermark_on_media_no" 
					data-target="enbale_watermark_on_media_no" data-elem="enbale_watermark_on_media_elem"
					<?php 
					if((isset($enbale_watermark_on_media) && $enbale_watermark_on_media == 'N')|| 
					!isset($enbale_watermark_on_media))
					{ echo ' checked="checked" '; }
					?> value="N" name="options[enbale_watermark_on_media]" 
					class="toggle-radio-button show_hide_setting_elem">
					<label for="enbale_watermark_on_media_no"><?php echo mlx_get_lang('No'); ?></label>
				</div>
			</div> 
			
			<div class="form-group enbale_watermark_on_media_elem enbale_watermark_on_media_yes" >
				<label for="water_mark_type_text"><?php echo mlx_get_lang('Watermark Type'); ?></label>
				 <div class="radio_toggle_wrapper ">
					<input type="radio" id="water_mark_type_text" value="text" 
					data-target="watermark_type_text" data-elem="watermark_type_elem"
					<?php 
					if((isset($watermark_type) && $watermark_type == 'text') || !isset($watermark_type)) 
					{ echo ' checked="checked" '; }
					?> name="options[watermark_type]" 
					class="toggle-radio-button show_hide_setting_elem">
					<label for="water_mark_type_text"><?php echo mlx_get_lang('Text'); ?></label>
					
					<input type="radio" id="water_mark_type_image" 
					data-target="watermark_type_image" data-elem="watermark_type_elem"
					<?php 
					if((isset($watermark_type) && $watermark_type == 'image'))
					{ echo ' checked="checked" '; }
					?> value="image" name="options[watermark_type]" 
					class="toggle-radio-button show_hide_setting_elem">
					<label for="water_mark_type_image"><?php echo mlx_get_lang('Image'); ?></label>
				</div>
			</div>
			
			<div class="form-group enbale_watermark_on_media_elem enbale_watermark_on_media_yes watermark_type_text watermark_type_elem">
			  <label for="watermark_text"><?php echo mlx_get_lang('Text'); ?></label><br>
			  <input type="text" class="form-control " maxlength="15"
			  name="options[watermark_text]" id="watermark_text"
			  value="<?php if(isset($watermark_text)) echo $watermark_text; ?>">
			</div>
			
			<div class="form-group enbale_watermark_on_media_elem enbale_watermark_on_media_yes watermark_type_text watermark_type_elem">
			  <label for="watermark_text_color"><?php echo mlx_get_lang('Color'); ?></label><br>
			  <input type="text" class="form-control jscolor " 
			  name="options[watermark_text_color]" id="watermark_text_color" readonly
			  value="<?php if(isset($watermark_text_color)) echo $watermark_text_color; else echo "555555"; ?>">
			</div>
			
			<?php $font_size_list = array('10','12','14','16','18','20','22','24','28','30'); ?>
			<div class="form-group enbale_watermark_on_media_elem enbale_watermark_on_media_yes watermark_type_text watermark_type_elem">
			  <label for="watermark_text_font_size"><?php echo mlx_get_lang('Font Size'); ?></label>
			  <select class="form-control select2_elem" name="options[watermark_text_font_size]" id="watermark_text_font_size" >
				<?php foreach($font_size_list as $k=>$v) { ?>
					<option value="<?php echo $v; ?>" 
					<?php if((isset($watermark_text_font_size) && $v == $watermark_text_font_size) || (!isset($watermark_text_font_size) && $v == '16')) echo "selected=selected";?>><?php echo $v; ?></option>
				<?php } ?>
			  </select>
			</div>
			
			<div class="form-group enbale_watermark_on_media_elem enbale_watermark_on_media_yes watermark_type_image watermark_type_elem">					  
				<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Watermark Image'); ?></label>						
				<label class="custom-file-upload" <?php if(isset($watermark_image) && !empty($watermark_image) && file_exists('../uploads/media/'.$watermark_image)) echo 'style="display:none;"'; ?>>	
					<input type="file" accept="image/*" class="att_photo" id="watermark_image" name="attachments" data-user-type="media">							
					<i class="fa fa-cloud-upload"></i> <?php echo mlx_get_lang('Upload Image'); ?>						
				</label>						
				<progress id="watermark_image_progress" value="0" max="100" style="display:none;"></progress>						
				<a id="watermark_image_link" href="<?php if(isset($watermark_image) && !empty($watermark_image) && file_exists('../uploads/media/'.$watermark_image)) 
					echo base_url().'../uploads/media/'.$watermark_image; ?>" 						
				download="<?php if(isset($watermark_image) && !empty($watermark_image) && file_exists('../uploads/media/'.$watermark_image)) 
					echo base_url().'../uploads/media/'.$watermark_image; ?>" 
				<?php if(!isset($watermark_image)|| empty($watermark_image) || !file_exists('../uploads/media/'.$watermark_image)) echo 'style="display:none;"'; ?>>							
					<img src="<?php if(isset($watermark_image) && !empty($watermark_image) && file_exists('../uploads/media/'.$watermark_image)) 
						echo base_url().'../uploads/media/'.$watermark_image; ?>" style="max-width:150px;">						
				</a>						
				<a class="remove_img" id="watermark_image_remove_img" data-name="watermark_image" title="Remove Image" 
				href="#" <?php if(!isset($watermark_image) || empty($watermark_image) || !file_exists('../uploads/media/'.$watermark_image)) echo 'style="display:none;"'; ?>>
				<i class="fa fa-remove"></i></a>						
				<input type="hidden" name="options[watermark_image]" 
				value="<?php if(isset($watermark_image) && !empty($watermark_image) && file_exists('../uploads/media/'.$watermark_image)) echo $watermark_image; ?>" id="watermark_image_hidden">											
			</div>
			
			<div class="enbale_watermark_on_media_elem enbale_watermark_on_media_yes">
			  <label for="watermark_text_position"><?php echo mlx_get_lang('Position'); ?></label><br>
			  
			  <div class="row">
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[watermark_text_position]" id="optionsRadios1" value="top-left" 
					  <?php if(isset($watermark_text_position) && 'top-left' == $watermark_text_position) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Top Left'); ?>
					</label>
				</div>
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[watermark_text_position]" id="optionsRadios1" value="top-center" 
					  <?php if(isset($watermark_text_position) && 'top-center' == $watermark_text_position) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Top Center'); ?>
					</label>
				</div>
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[watermark_text_position]" id="optionsRadios1" value="top-right" 
					  <?php if(isset($watermark_text_position) && 'top-right' == $watermark_text_position) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Top Right'); ?>
					</label>
				</div>
				
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[watermark_text_position]" id="optionsRadios1" value="center-left" 
					  <?php if(isset($watermark_text_position) && 'center-left' == $watermark_text_position) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Center Left'); ?>
					</label>
				</div>
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[watermark_text_position]" id="optionsRadios1" value="center-center" 
					  <?php if((isset($watermark_text_position) && 'center-center' == $watermark_text_position) || (!isset($watermark_text_position))) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Center Center'); ?>
					</label>
				</div>
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[watermark_text_position]" id="optionsRadios1" value="center-right"
					  <?php if(isset($watermark_text_position) && 'center-right' == $watermark_text_position) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Center Right'); ?>
					</label>
				</div>
				
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[watermark_text_position]" id="optionsRadios1" value="bottom-left" 
					  <?php if(isset($watermark_text_position) && 'bottom-left' == $watermark_text_position) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Bottom Left'); ?>
					</label>
				</div>
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[watermark_text_position]" id="optionsRadios1" value="bottom-center" 
					  <?php if(isset($watermark_text_position) && 'bottom-center' == $watermark_text_position) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Bottom Center'); ?>
					</label>
				</div>
				<div class="col-md-4">
					<label>
					  <input type="radio" class="minimal" name="options[options[watermark_text_position]]" id="optionsRadios1" value="bottom-right" 
					  <?php if(isset($watermark_text_position) && 'bottom-right' == $watermark_text_position) echo "checked='checked'";?>>
					  &nbsp;<?php echo mlx_get_lang('Bootom Right'); ?>
					</label>
				</div>
				
			  </div>
			</div>
			
			
			
			
				
		</div>
	  </div>
	</div>
	
	<div class="col-md-4">
		<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> sticky_sidebar">
		  <div class="box-header with-border">
			  <h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
			  <div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			  </div>
			</div>
			 
			 <div class="box-footer">
				<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Changes'); ?></button>
			  </div>
		  </div>
	</div>
	
  </div>
	  </form>
</section>
</div>