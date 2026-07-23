<script>
	

	jQuery("document").ready(function($){


		$(document).delegate('.dynamic_property_for_lang_country','change',function() {
			var thiss = $(this);
			var lang_code = thiss.parents('.section_fields').find('.dynamic_property_for_lang_opt:checked').val();
			
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_state').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_state').html('<option value="all">All States</option>').trigger('change');
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').html('<option value="all">All Cities</option>').trigger('change');
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').html('<option value="all">All Zipcodes</option>').trigger('change');
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').html('<option value="all">All Subareas</option>').trigger('change');
			
			
			if(thiss.val() != 'all' && (thiss.parents('.section_fields').find('.dynamic_property_for_lang_state').length || 
			thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').length))
			{
				
				$('.full_sreeen_overlay').show();
				var country_code = thiss.select2().find(":selected").data("country_code");
				
				var callback = 'get_states_or_cities_list_homepage_sections';
				$.ajax({						
					
					url: base_url+ 'admin_ajax',
					type: 'POST',						
					success: function (res) 
					{		
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_state').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_state').html(res.state_list).val('all').trigger('change');;
						
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').html(res.city_list).val('all').trigger('change');;
						
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').html(res.zipcode_list).val('all').trigger('change');;
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').html(res.subarea_list).val('all').trigger('change');;
						
						$('.full_sreeen_overlay').hide();
					},						
					data: {	lang_code: lang_code, country_code : country_code, callback : callback },						
					cache: false					
				});
				
			}
			
			return false;
		});
		
		$('.dynamic_property_for_lang_state').on('change',function() {
			var thiss = $(this);
			var lang_code = thiss.parents('.section_fields').find('.dynamic_property_for_lang_opt:checked').val();
			
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').html('<option value="all">All Cities</option>');
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').html('<option value="all">All Zipcodes</option>').trigger('change');
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').html('<option value="all">All Subareas</option>').trigger('change');
			
			if(thiss.val() != 'all' && thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').length)
			{
				$('.full_sreeen_overlay').show();
				var country_code = thiss.select2().find(":selected").data("country_code");
				var state_code = thiss.select2().find(":selected").data("state_code");
				var callback = 'get_cities_from_state_homepage_sections';
				$.ajax({						
					url: base_url+ 'admin_ajax',
					type: 'POST',						
					success: function (res) 
					{		
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_city').html(res.city_list).val('all').trigger('change');
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').html(res.zipcode_list).val('all').trigger('change');
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').html(res.subarea_list).val('all').trigger('change');
						$('.full_sreeen_overlay').hide();
					},						
					data: {	lang_code: lang_code, country_code : country_code, state_code : state_code, callback : callback },						
					cache: false					
				});
				
			}
		});
		
		$('.dynamic_property_for_lang_city').on('change',function() {
			var thiss = $(this);
			var lang_code = thiss.parents('.section_fields').find('.dynamic_property_for_lang_opt:checked').val();
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').html('<option value="all">All Zipcodes</option>').trigger('change');
			if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').length)
				thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').html('<option value="all">All Subareas</option>').trigger('change');
			
			if(thiss.val() != 'all' && (
			thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').length || 
			thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').length))
			{
				$('.full_sreeen_overlay').show();
				var country_code = thiss.select2().find(":selected").data("country_code");
				var state_code = thiss.select2().find(":selected").data("state_code");
				var city_code = thiss.select2().find(":selected").data("city_code");
				var callback = 'get_zip_subareas_from_city_homepage_sections';
				$.ajax({						
					/*url: base_url+'ajax/get_zip_subarea_list_from_city_by_lang_callback_func',						*/
					url: base_url+ 'admin_ajax',
					type: 'POST',						
					success: function (res) 
					{		
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_zipcode').html(res.zipcode_list).val('all').trigger('change');
						if(thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').length)
							thiss.parents('.section_fields').find('.dynamic_property_for_lang_sub_area').html(res.subarea_list).val('all').trigger('change');
						$('.full_sreeen_overlay').hide();
					},						
					data: {	lang_code: lang_code, country_code : country_code, state_code : state_code , city_code : city_code, callback : callback},						
					cache: false					
				});
				
			}
		});

	});		
		
</script>