<script>
$(document).ready(function() {
	
		
		$('.loc_state_list').change(function() {
			var thiss = $(this);
			var country_code = thiss.find('option:selected').attr('data-country_code');
			var state_name = thiss.val();
			var state_code = thiss.find('option:selected').attr('data-state_code');
			thiss.parents('.form-search').find('.loc_city_list').html('<option value="">Select Any City</option>');
			thiss.parents('.form-search').find('.zipcode_list').html('<option value="">Select Any Zipcode</option>');
			thiss.parents('.form-search').find('.sub_area_list').html('<option value="">Select Any Sub Area</option>');
			if(country_code != '' && state_code != '' && state_name != '')
			{
				$('.full_sreeen_overlay').show();
				
				thiss.parents('.form-search').find('.loc_city_list').html('<option value="">Loading...</option>');
				thiss.parents('.form-search').find('.zipcode_list').html('<option value="">Loading...</option>');
				thiss.parents('.form-search').find('.sub_area_list').html('<option value="">Loading...</option>');
				
				var callback = 'loc_get_city_name_list';
				
				$.ajax({						
					/*url: base_url+'ajax_locations/get_city_name_list_callback_func',						*/
					url: base_url+'admin_ajax',
					type: 'POST',						
					success: function (res) 
					{	
						if(res == '')
							thiss.parents('.form-search').find('.loc_city_list').html('<option value="">Select Any City</option>');
						else
							thiss.parents('.form-search').find('.loc_city_list').html(res);
						$('.full_sreeen_overlay').hide();
					},						
					data: {	country_code : country_code, state_code : state_code, callback : callback},						
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
			var city_name = thiss.attr('data-full_value')
			thiss.parents('.form-search').find('.zipcode_list').html('<option value="">Select Any Zipcode</option>');
			thiss.parents('.form-search').find('.sub_area_list').html('<option value="">Select Any Sub Area</option>');
			if(country_code != '' && state_code != '' && city_code != '' && city_name != '')
			{
				$('.full_sreeen_overlay').show();
				
				thiss.parents('.form-search').find('.zipcode_list').html('<option value="">Loading...</option>');
				thiss.parents('.form-search').find('.sub_area_list').html('<option value="">Loading...</option>');
				
				var callback = 'loc_get_zip_sub_area_name_list';
				$.ajax({						
					/*url: base_url+'ajax_locations/get_zip_sub_area_name_list_callback_func',						*/
					url: base_url+'admin_ajax',
					type: 'POST',						
					success: function (res) 
					{		
						thiss.parents('.form-search').find('.zipcode_list').html(res.zipcode_list);
						thiss.parents('.form-search').find('.sub_area_list').html(res.subarea_list);
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