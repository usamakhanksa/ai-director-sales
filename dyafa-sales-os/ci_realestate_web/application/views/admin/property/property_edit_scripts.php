<?php 

?>



<script>
	function lazy_load_on_media_img() {
		setTimeout(function() {
			$('.media_img_block li').each(function() {
				$(this).find('.lazy-img-elem').lazy({
					effect: "fadeIn",
					effectTime: 500,
					threshold: 0,
					afterLoad: function(element) {
						element.parent().removeClass('lazy-load-processing');
					},
				}).trigger("appear");;
				$(this).find('.lazy-img-elem').parent().removeClass('lazy-load-processing');
				var src = $(this).find('.lazy-img-elem').attr('data-src');
				$(this).find('.lazy-img-elem').attr('src', src);
			});
		}, 1000);
	}

	
	$(document).ready(function() {
		
		$('.nav-tabs-custom ul li').click(function() {
			var thiss = $(this);

			$('.tab-content .tab-pane').each(function(e) {
				var thiss = $(this);
				thiss.find('.required-fields input[type="text"]').attr('required',false);
				thiss.find('.required-fields input[type="text"]').parent().find('label span').remove();

				thiss.find('.required-fields textarea').attr('required',false);
				thiss.find('.required-fields textarea').parent().find('label span').remove();

				thiss.find('.required-fields input[type="number"]').attr('required',false);
				thiss.find('.required-fields input[type="number"]').parent().parent().find('label span').remove();
			});
			

			var target_id = thiss.find('a').attr('href').replace( "#", "" );

			$('.tab-content #'+target_id).find('.required-fields input[type="text"]').attr('required',true);
			$('.tab-content #'+target_id).find('.required-fields input[type="text"]').parent().find('label').append('<span class="text-red">*</span>');

			$('.tab-content #'+target_id).find('.required-fields textarea').attr('required',true);
			$('.tab-content #'+target_id).find('.required-fields textarea').parent().find('label').append('<span class="text-red">*</span>');

			$('.tab-content #'+target_id).find('.required-fields input[type="number"]').attr('required',true);
			$('.tab-content #'+target_id).find('.required-fields input[type="number"]').parent().parent().find('label').append('<span class="text-red">*</span>');
		});

		$(document).delegate(".measurement-group .dropdown-menu li", "click", function () {
	
				var data_val = $(this).find('a').attr('data-val');
				
				$(this).parents('.input-group-btn').find('.dropdown-toggle').html(data_val + '&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
				$(this).parents('.input-group-btn').removeClass('open');
				$(this).parents('.input-group').find('.measurement_type').val(data_val);
				return false;
		});
	
	
		$('.dropdown-menu.size_measure_menus li').click(function() {
			var data_val = $(this).find('a').attr('data-val');

			$(this).parents('.input-group-btn').find('.dropdown-toggle').html(data_val + '&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
			$(this).parents('.input-group-btn').removeClass('open');
			$(this).parents('.input-group').find('#size_measure').val(data_val);
			return false;
		});
		
		$('.dropdown-menu.width_measure_menus li').click(function() {
			var data_val = $(this).find('a').attr('data-val');

			$(this).parents('.input-group-btn').find('.dropdown-toggle').html(data_val + '&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
			$(this).parents('.input-group-btn').removeClass('open');
			$(this).parents('.input-group').find('#width_measure').val(data_val);
			return false;
		});
		
		$('.dropdown-menu.height_measure_menus li').click(function() {
			var data_val = $(this).find('a').attr('data-val');

			$(this).parents('.input-group-btn').find('.dropdown-toggle').html(data_val + '&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
			$(this).parents('.input-group-btn').removeClass('open');
			$(this).parents('.input-group').find('#height_measure').val(data_val);
			return false;
		});
		
		$('.dropdown-menu.length_measure_menus li').click(function() {
			var data_val = $(this).find('a').attr('data-val');

			$(this).parents('.input-group-btn').find('.dropdown-toggle').html(data_val + '&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
			$(this).parents('.input-group-btn').removeClass('open');
			$(this).parents('.input-group').find('#length_measure').val(data_val);
			return false;
		});
		
		var property_type = $('input[name="property_type"]:checked').attr('data-slug');
		
		
		/*alert(property_type);*/
		
		if(property_type == undefined) {
			 $(".property_feature").hide();
		}else{
			
			console.log(property_type);
			$(".property_feature").hide();
			
			$(".property_type_"+property_type).show();
		}
		
		$('input[name="property_type"]').on('click',function(){
			var thiss = $(this);
			var property_type_selected = thiss.attr('data-slug');
			
			$(".property_feature").hide();
			
			$(".property_type_"+property_type_selected).show();
			
		});
		
		
		
		
		
	});
</script>