<?php
	
	
if (!empty($single_property->property_images)) {
	$p_images = $myHelpers->global_lib->get_property_gallery($single_property->p_id);

	if (!empty($p_images)) {


?>
		<div class="row mt-5 d-print-none">
			<div class="col-12">
				<h2 class="h4 text-black mb-3 text-left"><?php echo mlx_get_lang('Gallery'); ?></h2>
			</div>
			<div class="card-columns col-md-12">
				<?php
				foreach ($p_images as $k => $v) {
					$post_image_url = base_url() . $v['original'];
				?>
					<div class="card">
						<!--col-sm-6 col-md-4 col-lg-3 mb-4-->
						<a href="<?php echo $post_image_url; ?>" class="image-popup gal-item img-container">
							<img src="<?php echo $post_image_url; ?>" alt="Image" class="img-fluid img-responsive">
						</a>
					</div>
				<?php } ?>
			</div>
		</div>
<?php }
}
?>