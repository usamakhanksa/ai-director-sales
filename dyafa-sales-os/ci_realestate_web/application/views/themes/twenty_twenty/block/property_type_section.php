<?php 
if(isset($property_type_list) && $property_type_list->num_rows() > 0){ 
global $settings;


?>

<div class="site-section looking-for-property-section">
  <div class="container">
	
	<div class="row justify-content-center mb-5">
	  <div class="col-md-10 text-center">
		<div class="site-section-title">
		  
		  <?php 
			if(isset($settings['heading']) && $settings['heading'] != ''){?>
			<h2> <?php echo mlx_get_lang($settings['heading']); ?></h2>
			<?php } ?>
			<?php if(isset($settings['sub_heading']) && $settings['sub_heading'] != ''){?>
			<p class="subheading"><?php echo mlx_get_lang($settings['sub_heading']); ?></p>
			<?php } ?>
		  
		</div>
	  </div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="property-type-carousel owl-carousel" 
							 data-items = "<?php echo $settings['no_of_item_in_carousel']; ?>" 
							 data-dots="<?php echo $settings['show_nav_dots']; ?>"
							 data-nav="<?php echo $settings['show_nav']; ?>"
							 data-autoplay="<?php echo $settings['auto_start']; ?>"
							 data-interval="<?php echo $settings['carousel_interval']; ?>">
			  <?php 
				$n=0;
				foreach($property_type_list->result() as $prop_row){ $n++; ?>
				 <?php include(__DIR__ . '../../property/template-part/property-type-list.php'); ?>
			  <?php } ?>
			</div>
		</div>
	</div>
  </div>
</div>
<?php } ?>