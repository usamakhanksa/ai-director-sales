<?php if (isset($banner_list) && $banner_list->num_rows() > 0) { ?>
	<div class="slide-one-item home-slider owl-carousel owl-theme" data-nav="yes">
		<?php foreach ($banner_list->result() as $b_row) {
			if (!file_exists('uploads/banner/' . $b_row->b_image)) {
				continue;
			}
		?>
			<div class="site-blocks-cover overlay" style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $b_row->b_image; ?>);" data-aos="fade" data-stellar-background-ratio="0.5">

			</div>
		<?php } ?>

	</div>
<?php } ?>


<?php include('block/properties-recent.php'); ?>

<?php include('block/property-types.php'); ?>

<?php include('block/properties-featured.php'); ?>