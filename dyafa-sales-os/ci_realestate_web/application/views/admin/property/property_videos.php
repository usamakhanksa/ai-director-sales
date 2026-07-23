<?php 

	$enable_property_videos_for_all  = get_option("enable_property_videos_for_all");
	$enable_property_videos_for_admin  = get_option("enable_property_videos_for_admin");
	
	$CI = &get_instance();
	
	if($enable_property_videos_for_all == 'Y' || ( $CI->user_type == 'admin' && $enable_property_videos_for_admin == 'Y')  ){
		
		
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
		<h3 class="box-title"><?php echo mlx_get_lang('Property Video'); ?></h3>
		<div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>

	<div class="box-body vdo_url_container">
		<?php if (isset($video_url_array) && !empty($video_url_array)) {
			foreach ($video_url_array as $k => $v) {
				$ele_count = $k + 1;
		?>
				<div class="form-group">
					<label for="video_url_<?php echo $ele_count; ?>"><?php echo mlx_get_lang('Video URL'); ?> </label>
					<div class="input-group">
						<input type="url" class="form-control video_url" value="<?php echo $v; ?>" id="video_url_<?php echo $ele_count; ?>" name="video_url[]">

						<span class="input-group-addon text-red remove-video-link">
							<!--<a class="popup-player" href="<?php //echo $v; 
																?>"><i class="fa fa-play"></a></i>-->
							<i class="fa fa-remove"></i>
						</span>

					</div>
				</div>

			<?php }
		} else { ?>
			<div class="form-group">
				<label for="video_url_1"><?php echo mlx_get_lang('Video URL'); ?> </label>
				<div class="input-group">
					<input type="url" class="form-control video_url" id="video_url_1" name="video_url[]">

					<span class="input-group-addon remove-video-link">
						<!--<a class="popup-player" disabled><i class="fa fa-play"></a></i>-->
						<i class="fa fa-remove"></i>
					</span>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="box-footer">
		<button type="button" class="btn btn-default pull-right add_more_vdo_btn"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add Video'); ?></button>
	</div>
</div>

	<?php 	}	?>