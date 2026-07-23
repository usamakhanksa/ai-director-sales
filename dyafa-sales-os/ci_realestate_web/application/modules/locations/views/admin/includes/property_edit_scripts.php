<script>
		
		
		$(document).ready(function() {

			$('.loc_country_list').change(function() {
				var thiss = $(this);
				var country_code = thiss.find('option:selected').attr('data-country_code');

				
				if(thiss.parents('.location-fields').find('.loc_state_list').length)
				{	
					thiss.parents('.location-fields').find('.loc_state_list').html('<option value="">Select Any State</option>').trigger('change');//.select2("val", "");
				}
				if(thiss.parents('.location-fields').find('.loc_city_list').length)
				{
					thiss.parents('.location-fields').find('.loc_city_list').html('<option value="">Select Any City</option>');//.select2("val", "");
				}
				if(thiss.parents('.location-fields').find('.zipcode_list').length)
					thiss.parents('.location-fields').find('.zipcode_list').html('<option value="">Select Any Zipcode</option>');//.select2("val", "");
				if(thiss.parents('.location-fields').find('.sub_area_list').length)
					thiss.parents('.location-fields').find('.sub_area_list').html('<option value="">Select Any Sub Area</option>');//.select2("val", "");
				
				
				if(country_code != '' && (thiss.parents('.location-fields').find('.loc_state_list').length || 
				thiss.parents('.location-fields').find('.loc_city_list').length))
				{
					var callback = 'loc_get_state_city_name_list';
					$('.full_sreeen_overlay').show();
					$.ajax({						
						
						/*url: base_url+'ajax_locations/get_state_city_name_list_callback_func',						*/
						url: base_url+'admin_ajax',
						
						type: 'POST',						
						success: function (res) 
						{		
							if(thiss.parents('.location-fields').find('.loc_state_list').length)
								thiss.parents('.location-fields').find('.loc_state_list').html(res.state_list);
							if(thiss.parents('.location-fields').find('.loc_city_list').length){
								thiss.parents('.location-fields').find('.loc_city_list').html(res.city_list);
							}	
							$('.full_sreeen_overlay').hide();
						},						
						data: {	country_code : country_code , callback : callback},						
						cache: false					
					});
				}
				return false;
				
			});
			
			$('.loc_state_list').change(function() {
				var thiss = $(this);
				var country_code = thiss.find('option:selected').attr('data-country_code');
				var state_name = thiss.find('option:selected').attr('data-full_value');
				var state_code = thiss.find('option:selected').attr('data-state_code');
				
				if(thiss.parents('.location-fields').find('.loc_city_list').length)
					thiss.parents('.location-fields').find('.loc_city_list').html('<option value="">Select Any City</option>').select2("val", "");
				if(thiss.parents('.location-fields').find('.zipcode_list').length)
					thiss.parents('.location-fields').find('.zipcode_list').html('<option value="">Select Any Zipcode</option>').select2("val", "");
				if(thiss.parents('.location-fields').find('.sub_area_list').length)
					thiss.parents('.location-fields').find('.sub_area_list').html('<option value="">Select Any Sub Area</option>').select2("val", "");
				
				if(country_code != '' && state_code != '' && state_name != '' && thiss.parents('.location-fields').find('.loc_city_list').length)
				{
					var callback = 'loc_get_city_name_list';
					$('.full_sreeen_overlay').show();
					$.ajax({						
						/*url: base_url+'ajax_locations/get_city_name_list_callback_func',	*/
						url: base_url+'admin_ajax',	
						type: 'POST',						
						success: function (res) 
						{		
							thiss.parents('.location-fields').find('.loc_city_list').html(res);
							$('.full_sreeen_overlay').hide();
						},						
						data: {	country_code : country_code, state_code : state_code , callback : callback},						
						cache: false					
					});
				}
				return false;
				
			});
			
			$('.loc_city_list').change(function() {
				var thiss = $(this);
				var country_code = thiss.find('option:selected').attr('data-country_code');
				var state_code = thiss.find('option:selected').attr('data-state_code');
				var city_code = thiss.find('option:selected').attr('data-city_code');
				var city_name = thiss.attr('data-full_value');
				
				
				if(thiss.parents('.location-fields').find('.zipcode_list').length)
					thiss.parents('.location-fields').find('.zipcode_list').html('<option value="">Select Any Zipcode</option>').select2("val", "");
				
				if(thiss.parents('.location-fields').find('.sub_area_list').length)
					thiss.parents('.location-fields').find('.sub_area_list').html('<option value="">Select Any Sub Area</option>').select2("val", "");
				
				if(country_code != '' && state_code != '' && city_code != '' && city_name != '')
				{
					var callback = 'loc_get_zip_sub_area_name_list';
					$('.full_sreeen_overlay').show();
					$.ajax({						
						/*url: base_url+'ajax_locations/get_zip_sub_area_name_list_callback_func',						*/
						url: base_url+'admin_ajax',	
						type: 'POST',						
						success: function (res) 
						{		
							thiss.parents('.location-fields').find('.zipcode_list').html(res.zipcode_list);
							thiss.parents('.location-fields').find('.sub_area_list').html(res.subarea_list);
							$('.full_sreeen_overlay').hide();
						},						
						data: {	country_code : country_code, state_code : state_code, city_code : city_code , callback : callback},						
						cache: false					
					});
				}
				return false;
				
			});

		});
		</script>