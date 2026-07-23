<?php 

$locations = json_decode($locations, true);
$loc_tax_settings = json_decode($loc_tax_settings,true); 


$enable_zipcode_on_search = get_option('enable_zipcode_on_search');
$enable_subarea_on_search = get_option('enable_subarea_on_search');

?>
<div class="box box-<?php echo get_skin_class(); ?>" id="city_zip_sub_area">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo mlx_get_lang('Show Zipcode/Sub-area on Search'); ?></h3>
	</div>
	<div class="box-body">
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'form');		 			
		echo form_open_multipart('',$attributes); ?>
		<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
		
			
			
			<?php
			
			
			if($loc_tax_settings['zipcode']['enabled'] ){
			?>
			
			<div class="form-group" >
				<label for="enable_zipcode_on_search"><?php echo mlx_get_lang('Show Zipcode on Search'); ?></label>
				 <div class="radio_toggle_wrapper ">
					<input type="radio" id="enable_zipcode_on_search_yes" value="Y" 
					<?php 
					if((isset($enable_zipcode_on_search) && $enable_zipcode_on_search == 'Y')) { echo ' checked="checked" '; }
					?> name="options[enable_zipcode_on_search]" 
					class="toggle-radio-button">
					<label for="enable_zipcode_on_search_yes"><?php echo mlx_get_lang('Yes'); ?></label>
					
					<input type="radio" id="enable_zipcode_on_search_no" 
					<?php 
					if((isset($enable_zipcode_on_search) && $enable_zipcode_on_search == 'N')  || 
					!isset($enable_zipcode_on_search))
					{ echo ' checked="checked" '; }
					?> value="N" name="options[enable_zipcode_on_search]" 
					class="toggle-radio-button">
					<label for="enable_zipcode_on_search_no"><?php echo mlx_get_lang('No'); ?></label>
				</div>
			</div>
			
			<?php } ?>
			
			<?php
			if( $loc_tax_settings['sub-area']['enabled']){
			?>
			
			<div class="form-group" >
				<label for="enable_subarea_on_search"><?php echo mlx_get_lang('Show Sub-area on Search'); ?></label>
				 <div class="radio_toggle_wrapper ">
					<input type="radio" id="enable_subarea_on_search_yes" value="Y" 
					<?php 
					if((isset($enable_subarea_on_search) && $enable_subarea_on_search == 'Y')) { echo ' checked="checked" '; }
					?> name="options[enable_subarea_on_search]" 
					class="toggle-radio-button">
					<label for="enable_subarea_on_search_yes"><?php echo mlx_get_lang('Yes'); ?></label>
					
					<input type="radio" id="enable_subarea_on_search_no" 
					<?php 
					if((isset($enable_subarea_on_search) && $enable_subarea_on_search == 'N')  || 
					!isset($enable_subarea_on_search))
					{ echo ' checked="checked" '; }
					?> value="N" name="options[enable_subarea_on_search]" 
					class="toggle-radio-button">
					<label for="enable_subarea_on_search_no"><?php echo mlx_get_lang('No'); ?></label>
				</div>
			</div>
			
			
			
			<?php } ?>
			<button name="show_city_zip_sub_area" type="submit" class="btn btn-<?php echo get_skin_class(); ?>" 
					id="show_city_zip_sub_area"><?php echo mlx_get_lang('Update'); ?></button>
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