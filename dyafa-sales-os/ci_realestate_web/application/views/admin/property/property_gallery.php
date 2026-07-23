<?php 

	$enable_property_gallery_for_all  = get_option("enable_property_gallery_for_all");
	$enable_property_gallery_for_admin  = get_option("enable_property_gallery_for_admin");
	
	$CI = &get_instance();
	
	if($enable_property_gallery_for_all == 'Y' || ( $CI->user_type == 'admin' && $enable_property_gallery_for_admin == 'Y')  ){
		
		
		if (isset($query) && $query->num_rows() > 0) {

			$property = $row = $query->row();

			foreach ($row as $k => $v) {
				${$k} = $v;
			}
			
			if (!empty($video_urls))
				$video_url_array = explode(',', $video_urls);
		}	
?>


<div class="box box-<?php echo get_skin_class(); ?>">
		<div class="box-header with-border">
			<h3 class="box-title"><?php echo mlx_get_lang('Property Gallery'); ?></h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="form-group" align="center">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="property_pl_image_container">
							<label class="custom-file-upload" data-element_id="" data-type="media" id="pl_file_uploader_1">
								<?php echo mlx_get_lang('Drop images here'); ?>
								<br />
								<strong><?php echo mlx_get_lang('OR'); ?></strong>
								<br />
								<?php echo mlx_get_lang('Click here to select images'); ?>
							</label>
							<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
							<a class="pl_file_link" href="" download="" style="display:none;">
								<img src="" style="width:50%;">
							</a>
							<a class="ppl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
							<input type="hidden" name="blog_image" value="" class="pl_file_hidden">
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<br>
						<span class="or"><?php echo mlx_get_lang('OR'); ?></span>
						<br>
						<br>
					</div>
					<?php
					
					
					?>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<a onclick="lazy_load_on_media_img()" href="#" class="custom-file-upload add_from_media_btn "><i class="fa fa-camera"></i>&nbsp;<?php echo mlx_get_lang('Add From Media'); ?></a>
						<input type="hidden" name="addedImgFromMediaLibrary" 
						value="<?php if (isset($property_images) && !empty($property_images)) 
						{
							$data_exp = explode(',', $property_images);
							$data_imp = array();
							foreach ($data_exp as $k => $v) {
								$data_imp[] = EncryptClientID($v);
							}
							echo implode(',', $data_imp);
						} ?>">
					</div>
				</div>


			</div>

			<div class="form-group" style="margin-bottom:0px;">
				<div class="product-gallary-container">

					<?php
					if (isset($property_images) && !empty($property_images)) {
						$p_g_i = explode(',', $property_images);
						if (count($p_g_i) > 0) {
							foreach ($p_g_i as $key => $val) {
								$img_id = $val;

								$query = "SELECT pi.* FROM `post_images` 
						inner join post_images pi on post_images.image_id = pi.parent_image_id
						and pi.image_type = 'thumbnail'
						WHERE post_images.image_id = $img_id";
								$result = $myHelpers->Common_model->commonQuery($query);
								if ($result->num_rows() > 0) {
									$img_row = $result->row();
									if (!file_exists($img_row->image_path . $img_row->image_name))
										continue;
									echo '<div class="media-img-block ui-sortable-handle">
										<div class="media_images_inner lazy-load-processing" data-container="body" data-toggle="tooltip" title="' . $img_row->image_alt . '">
										<span class="remove-product-btn"><i class="fa fa-remove"></i></span>
										<img class="lazy-img-elem" data-img_id="' . $myHelpers->global_lib->EncryptClientId($img_id) . '" data-src="' . base_url() . $img_row->image_path . $img_row->image_name . '">
									</div>
									</div>';
								}
							}
						}
					}
					?>

				</div>
			</div>



		</div>

	</div>

<?php 	}	?>					