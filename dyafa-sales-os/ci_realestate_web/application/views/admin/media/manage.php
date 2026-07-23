

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-image"></i> <?php echo mlx_get_lang('Manage Media'); ?> </h1>
  
</section>

<section class="content">
	  <?php 
	
	$attributes = array('name' => 'add_form_post','class' => 'form');		 			
	echo form_open_multipart('admin/media/manage',$attributes); ?>
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-12">   
	   
	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> gallery-section" >
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Media Library'); ?></h3>
		  <div class="btn-group pull-right" style="margin-left:5px;">
				<button class="btn btn-default btn-xs select-all-album-btn" data-container="body" data-toggle="tooltip" title="<?php echo mlx_get_lang('Select All'); ?>"><i class="fa fa-check"></i></button>
				<button class="btn btn-default btn-xs unselect-all-album-btn" data-container="body" data-toggle="tooltip" title="<?php echo mlx_get_lang('Select None'); ?>"><i class="fa fa-square-o"></i></button>
			</div>
			
			<button class="btn btn-danger btn-xs remove-album-images pull-right disabled" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></button>
			<span class="pull-right select-msg-text-block" style="margin-right:10px; margin-top:5px;">
				(<?php echo mlx_get_lang('Click on image to select multiple'); ?>)
			</span>
		</div>
		  <div class="box-body">
			
			<div id="gallery_plupload_container" class="gallery_plupload_container">
				<div id ="gallery-drop-target" class="gallery-drop-target">
					<span class="gallery-drop-target-inner">
						<?php echo mlx_get_lang("Drop images or folders here"); ?>
						<br>
						<strong><?php echo mlx_get_lang("OR"); ?></strong>
						<br>
						<?php echo mlx_get_lang("Click here to select multiple images"); ?>
						<br><br>
						<small>(<?php echo mlx_get_lang("Allowed file size upto 40MB and 6000x6000 in dimention."); ?>)</small>
					</span>
					
				</div>
			</div>
			
			
			<div class="media_container media-upload-container row" id="gallery-upload-container">
				
				<?php if(isset($media_list) && $media_list->num_rows() > 0)
				{
					foreach($media_list->result() as $row)
					{
						if(!file_exists($row->image_path.$row->image_name))
						{
							$myHelpers->Common_model->commonDelete('post_images',$row->parent_image_id,'parent_image_id' );
							$myHelpers->Common_model->commonDelete('post_images',$row->parent_image_id,'image_id' );
							continue;
						}
				?>
					<div class="col-md-2 album_images">
						<div class="media_images_inner lazy-load-processing"  data-container="body" data-toggle="tooltip" title="" data-original-title="<?php echo $row->image_alt;?>">
							<img data-src="<?php echo base_url().$row->image_path.$row->image_name; ?>" width="100%" class="lazy-img-elem">
							<a href="#" class=" remove_album_image hide" id="<?php echo 'image_'.$row->image_id;?>" data-type="photo_gallery" data-name="<?php echo 'image_'.$row->image_id;?>"><i class="fa fa-remove"></i></a>
							<span class="select-check hide"><i class="fa fa-check"></i></span>
							<input type="hidden" name="" id="<?php echo 'image_'.$row->image_id.'_hidden';?>" value="<?php echo $row->image_alt;?>">
						</div>
					</div>
					
				<?php
					}
				}
				?>
			</div>
			
			
		 </div>
		
	  </div>
	  
	  
</div>
  </div>
	  </form>
</section>
</div>