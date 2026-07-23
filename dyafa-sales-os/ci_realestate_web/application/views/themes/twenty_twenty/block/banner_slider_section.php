<?php if(isset($banner_list) && $banner_list->num_rows() > 0) { 
global $settings;

?> 

<div class="container">
	<div class="row">
		<div class="col-md-12">
		</div>
				</div>
			</div>
<div class="slide-one-item home-slider owl-carousel owl-theme slider-block slider-responsive mt-n33 banner-slider " 
	<?php 
	if(isset($settings['auto_start_slider']) && $settings['auto_start_slider'] == 'yes') 
	{ 
		echo ' data-autoplay="true" ';
		if(isset($settings['slider_interval']) && $settings['slider_interval'] != '') {
			echo ' data-interval="'.$settings['slider_interval'].'" ';
		}
	} 
	?> 
	<?php if(isset($settings['show_nav_dots']) && $settings['show_nav_dots'] == 'yes') { 
		echo ' data-dots="'.$settings['show_nav_dots'].'" ';
	} ?>
	<?php if(isset($settings['show_nav']) && $settings['show_nav'] == 'yes') { 
		echo ' data-nav="'.$settings['show_nav'].'" ';
	} ?>
	>
	<?php foreach($banner_list->result() as $b_row) { 
		if(!file_exists('uploads/banner/'.$b_row->b_image))
		{
			continue;
		}
	?>
	  <div class="item">
		<img src="<?php echo base_url(); ?>uploads/banner/<?php echo $b_row->b_image; ?>" />
	  </div>
	<?php } ?>
	
			
</div>
<?php } ?>