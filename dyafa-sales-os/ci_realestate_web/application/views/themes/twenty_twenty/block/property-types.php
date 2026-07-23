<?php 

if(isset($property_type_list) && $property_type_list->num_rows() > 0){ ?>
<style>
.looking-for-property-section .pt-title a{
    color: #252525;
    font-size: 20px;
    line-height: 1.2;
}
</style>
<div class="site-section looking-for-property-section">
  <div class="container">
	
	<div class="row justify-content-center mb-5">
	  <div class="col-md-7 text-center">
		<div class="site-section-title">
		  <h2><?php echo mlx_get_lang('Looking for Property'); ?></h2>
		  <p><?php echo mlx_get_lang('What kind of property are you looking for? We will help you'); ?></p>
		</div>
	  </div>
	</div>
	
	<div class="row property-type-carousel owl-carousel" data-nav="yes">
	  <?php 
		$n=0;
		foreach($property_type_list->result() as $prop_row){ $n++; ?>
		 <?php include(__DIR__ . '../../property/template-part/property-type-list.php'); ?>
	  <?php } ?>
	</div>
	
  </div>
</div>
<?php } ?>