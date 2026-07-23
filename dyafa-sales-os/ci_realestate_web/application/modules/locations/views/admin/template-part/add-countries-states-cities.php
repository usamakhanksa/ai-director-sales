<?php 
	
	$locations = json_decode($locations, true);
	$loc_tax_settings = json_decode($loc_tax_settings,true);
	
	if($loc_tax_settings['state']['enabled'])
		$main_title = 'Add Cities to State';
	else
	{
		$main_title = 'Add Cities';
	
		$countries = file_get_contents(base_url("locations/json/countries.json"));	
		$countries = json_decode($countries, true);
	}	
	
?>	
<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>" id="country-states-cities">
	<div class="box-header with-border">
		<h3 class="box-title"><?php echo mlx_get_lang($main_title); ?></h3>
	</div>
	<div class="box-body">
		<?php 
		$attributes = array('name' => 'add_form_post','class' => 'form');		 			
		echo form_open_multipart('',$attributes); ?>
		<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
		
			<?php if($loc_tax_settings['state']['enabled']) { ?>
			<div class="form-group">
			  <label for="State"><?php echo mlx_get_lang('State'); ?> </label>
			  <input type="text" name="" id="cnt_state_for_city" class="form-control" 
			  readonly required
			  placeholder="Select a state" />
			  
			  <input type="hidden" name="cnt_state_id_for_city" id="cnt_state_id_for_city" class="form-control"    />
			  
			  
			</div>  
			<?php }else{ ?>
			<div class="form-group">
			  <label for="country"><?php echo mlx_get_lang('Country'); ?> </label>
			  <input type="text" name="" id="cnt_country_for_city" class="form-control" required
			  readonly placeholder="<?php echo mlx_get_lang('Select a Country'); ?>" />
			  
			  <input type="hidden" name="cnt_country_code_for_city" id="cnt_country_code_for_city" class="form-control"    />
			  
			  
			</div> 
			<?php } ?>
			
			<!--
			<div class="form-group">
			 <label for="country_city">Select Country <span class="required">*</span></label>
			  <select name="country_city"  class="form-control no_clean select2_elemSSS"   id="country_city" >
			  <option value="">Select Country</option> 
			  <?php
			  /*
			  foreach($countries as $country){
					echo '<option value="'.$country['countryCode'].'~'.$country['countryName'].'~'.$country['geonameId'].'">'.$country['countryName'].'</option>';	
				}
			  */
			  ?>
			  </select>
			</div>
			-->
			
			<div class="form-group">
			  <label for="city"><?php echo mlx_get_lang('Select Cities'); ?> <span class="required">*</span></label>
			  <select name="cnt_state_cities[]" multiple class="form-control no_clean select2_elem" required
			  id="cnt-state-city" >
			  <!--<option value="">Select Cities</option>  -->
			  <?php
			  /*foreach($countries as $country){
					echo '<option value="'.$country['id'].'">'.$country['name'].'</option>';	
				}*/
			  ?>
			  </select>
			  
			  
			</div>
		
			<?php if($loc_tax_settings['state']['enabled']) { ?>
				<button name="add_state_city" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?>" id="add_state_city"><?php echo mlx_get_lang('Add'); ?></button>
			<?php }else{ ?>
				<button name="add_country_city" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?>" id="add_country_city"><?php echo mlx_get_lang('Add'); ?></button>
			<?php } ?>
		</form>
	</div>
	<div class="overlay" style="display:none;">
		<i class="fa fa-spinner fa-spin"></i>
	</div>
</div>
<?php /*} */?>
<script>

	jQuery(document).ready(function($){
		
	var base_url = '<?php echo base_url();?>';	
	
	
	$(".add-state-city").on('click',function() {
		
		var thiss = $(this);
		var state_id = thiss.attr("data-state_id");
		var country_code = thiss.attr("data-country_code");
		var state_title = thiss.attr("data-state_title");
		$("#cnt_state_for_city").val(state_title);
		$("#cnt_state_id_for_city").val(state_id);
		
		var header_height = $('header.main-header').outerHeight() + 15;
		$('html, body').animate({
			scrollTop: $('#country-states-cities').offset().top - header_height 
		}, 1000);
		
		if(state_id){
			$('#country-states-cities').find('.overlay').show();
			$.ajax({						
				url: base_url+'admin/ajax_locations/get_cities_from_states',						
				type: 'POST',						
				success: function (res) 
				{		
					if(res != 'cities not found')
						$("#cnt-state-city").html(res);
					
					$("#cnt_state_for_city").focus();
					$('#country-states-cities').find('.overlay').hide();
				},						
				data: {	state_id : state_id, country_code : country_code},						
				cache: false					
			});	
			
		}
		
		return false;
	});
	
	$(".add-country-city").on('click',function() {
		
		var thiss = $(this);
		var country_code = thiss.attr("data-country_code");
		var country_name = thiss.attr("data-country_name");
		$("#cnt_country_for_city").val(country_name);
		$("#cnt_country_code_for_city").val(country_code);
		
		var header_height = $('header.main-header').outerHeight() + 15;
		$('html, body').animate({
			scrollTop: $('#country-states-cities').offset().top - header_height 
		}, 1000);
		
		if(country_code){
			$('#country-states-cities').find('.overlay').show();
			$.ajax({						
				url: base_url+'admin/ajax_locations/get_cities_from_countries',						
				type: 'POST',						
				success: function (res) 
				{		
					if(res != 'cities not found')
						$("#cnt-state-city").html(res);
					$("#cnt_state_for_city").focus();	
					$('#country-states-cities').find('.overlay').hide();
				},						
				data: {	country_code : country_code},						
				cache: false					
			});	
			
		}
		
		return false;
	});
	
	$('#addCityModal').on('hidden.bs.modal', function (e) {
	  $(this)
		.find("input,textarea,select")
		   .val('')
		   .end()
		.find("input[type=checkbox], input[type=radio]")
		   .prop("checked", "")
		   .end();
		
		$(this).find('.overlay').show();
	})
	
	$('#addCityModal').on('shown.bs.modal', function (e) {
		
		$('#addCityModal .overlay').show();
		
		var $invoker = $(e.relatedTarget);
		var state = $invoker.attr('data-state');
		var state_title = $invoker.attr('data-state_title');
		var state_id = $invoker.attr('data-state_id');
		var state_code = $invoker.attr('data-state');
		var country_code = $invoker.attr('data-country_code');
		var country_title = $invoker.attr('data-country_title');
		
		
		
		$('#addCityModal').find('#cc-country_title').val(country_title);
		$('#addCityModal').find('#cc-country_id').val(country_code);
		
		$('#addCityModal').find('#cc-state_title').val(state_title);
		$('#addCityModal').find('#cc-state_id').val(state_code);
		
		
		
		$.ajax({						
			url: base_url+'admin/ajax_locations/generate_city_id_callback_func',						
			type: 'POST',						
			success: function (res) 
			{		
				$('#addCityModal').find('#cc-city_id').val(res.city_id);
				$('#addCityModal .overlay').hide();
			},						
			data: {	},						
			cache: false					
		});
		
	});
	
	
	$('.add_city_form').submit(function() {
		$('#addCityModal .overlay').show();
		var thiss = $(this);
		$.ajax({						
			url: base_url+'admin/ajax_locations/add_custom_city_callback_func',						
			type: 'POST',						
			success: function (res) 
			{		
				$('#addCityModal').modal('hide');
				window.location.reload();
			},						
			data: thiss.serialize(),						
			cache: false					
		});
		return false;
	});
		
});

</script>
		
		