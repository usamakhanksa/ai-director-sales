<?php 

$locations = json_decode($locations, true);
$loc_tax_settings = json_decode($loc_tax_settings,true); 

?>
<div class="box box-<?php echo get_skin_class(); ?>" id="city_zip_sub_area">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo mlx_get_lang('Add Zipcode/Sub-area'); ?></h3>
	</div>
	<div class="box-body">
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'form');		 			
		echo form_open_multipart('',$attributes); ?>
		<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
		
			<div class="form-group">
			  <label for="city"><?php echo mlx_get_lang('City'); ?> </label>
			  <input type="text" name="" id="cnt_city_zip_sub_area" class="form-control" required
			  readonly placeholder="<?php echo mlx_get_lang('Select a city from left'); ?>" />
			  
			<input type="hidden" name="cnt_city_id_for_zip_sub_area" id="cnt_city_id_for_zip_sub_area" class="form-control"   />
			  
			  
			</div>
			
			<?php
			
			
			if($loc_tax_settings['zipcode']['enabled'] ){
			?>
			
			<div class="form-group">
			  <label for="zipcode"><?php echo mlx_get_lang('Zipcode'); ?> </label>
			  <input type="text" name="cnt_city_zipcode" id="cnt_city_zipcode" class="form-control" 
			  placeholder="<?php echo mlx_get_lang('Enter zipcode comma(,) separated'); ?>" />
			  
			</div>
			<?php } ?>
			
			<?php
			if( $loc_tax_settings['sub-area']['enabled']){
			?>
			<div class="form-group">
			  <label for="sub-area"><?php echo mlx_get_lang('Sub-area'); ?> </label>
			  <input type="text" name="cnt_city_sub_area" id="cnt_city_sub_area" 
			  class="form-control"  auto-complete="off"
			  placeholder="<?php echo mlx_get_lang('Enter Sub-area comma(,) separated'); ?>" />
			  
			</div>
			<?php } ?>
			<button name="add_city_zip_sub_area" type="submit" class="btn btn-<?php echo get_skin_class(); ?>" id="add_city_zip_sub_area"><?php echo mlx_get_lang('Add'); ?></button>
		</form>
	</div>
</div>
			
<script>

jQuery(document).ready(function($)
{
	
	var base_url = '<?php echo base_url();?>';	

	$(".add-city-zip-sub-area").on('click',function() {
	
	var thiss = $(this);
	var city_id = thiss.attr("data-city_id");
	
	var city_title = thiss.attr("data-city_title");
	$("#cnt_city_zip_sub_area").val(city_title);
	
	$("#cnt_city_id_for_zip_sub_area").val(city_id);
	
	var header_height = $('header.main-header').outerHeight() + 15;
		$('html, body').animate({
			scrollTop: $('#city_zip_sub_area').offset().top - header_height 
		}, 1000);
	
	if($('#cnt_city_zipcode').length)
	{
		$('#cnt_city_zipcode').focus();
	}
	else if($('#cnt_city_sub_area').length)
	{
		$('#cnt_city_sub_area').focus();
	}
		
	
	
	return false;
});



});

</script>			