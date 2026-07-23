 function checkForZero(field) {
	if (field.value == 0 || field.value.length == 0) {
		alert ('<?php echo mlx_get_lang("This field can not be 0!"); ?>');
		field.focus(); }
	else
	calculatePayment(field.form);
}

function cmdCalc_Click(form) {
	if (form.price.value == 0 || form.price.value.length == 0) {
		form.price.focus(); }
	else if (form.ir.value == 0 || form.ir.value.length == 0) {
		form.ir.focus(); }
	else if (form.term.value == 0 || form.term.value.length == 0) {
		form.term.focus(); }
	else
		calculatePayment(form);
}

function calculatePayment(form) {
	princ = form.price.value - form.dp.value;
	intRate = (form.ir.value/100) / 12;
	months = form.term.value * 12;
	form.pmt.value = Math.floor((princ*intRate)/(1-Math.pow(1+intRate,(-1*months)))*100)/100;
	form.principle.value = princ;
	form.payments.value = months;
}

function escapeHtml(text) {
	'use strict';
	var filter_data = DOMPurify.sanitize(text, {SAFE_FOR_TEMPLATES: true});
	return jQuery.trim(filter_data);
}

function validateEmail(email) {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}

var is_valid_reg_form = false;

var id;
function progress(e){
	if(e.lengthComputable){
	   $('#'+id+'_progress').show();
		$('progress').attr({value:e.loaded,max:e.total});
	}
}

$(function () {

  "use strict";
   
$('.lazy-img-elem').lazy({
	effect: "fadeIn",
	effectTime: 500,
	threshold: 0,
	afterLoad: function(element) {
		element.parent().removeClass('lazy-load-processing');
	},
});
   
  $('[data-toggle="tooltip"]').tooltip() 
   
   $(document).on('click', '.favorite_btn:not(".not-logged-in")', function () {
		
		var productID = $(this).parents('.property-entry').attr('data-id');
		var productURL = $(this).parents('.property-entry').attr('data-url');
		var displayTitle = $(this).parents('.property-entry').attr('data-title');
		var thiss = $(this);
		thiss.find('.bookmark_icon').addClass('fa-bookmark').removeClass('fa-bookmark-o'); 
		thiss.attr('data-original-title', 'Remove from Favorite');
		thiss.tooltip("hide");
		$.ajax({
				url: base_url+'ajax/favirate_callback_func',
				type: 'POST',
				success: function (res) {
					thiss.removeClass('favorite_btn').addClass('favorite_btn_remove');
				},
				error: function(res){
					console.log(res);
				},
				data: {p_id : productID,'action':'add',productURL:productURL, productTitle : displayTitle},
				cache: false
			});
		
    });
		
		
	$(document).on('click', '.favorite_btn_remove', function () {
		var productID = $(this).parents('.property-entry').attr('data-id');		
		
		var thiss = $(this);
		thiss.find('.bookmark_icon').addClass('fa-bookmark-o').removeClass('fa-bookmark'); 
		thiss.attr('data-original-title', 'Add to Favorite');
		thiss.tooltip("hide");
		$.ajax({
				url: base_url+'ajax/favirate_callback_func',
				type: 'POST',
				success: function (res) {
					thiss.removeClass('favorite_btn_remove').addClass('favorite_btn');
				},
				error: function(res){
					console.log(res);
				},
				data: {p_id : productID,'action':'remove'},
				cache: false
			});
			
	});
   
  if($('.form-search').length)
  {
		$("#adv_search").on('click',function(){
			var adv_search = $(".adv_search_hidden").val();
			if(adv_search == '0') $(".adv_search_hidden").val('1'); else $(".adv_search_hidden").val('0');
			$(".adv-serach-row").toggle();
			return false;
		});

		var adv_search = $(".adv_search_hidden").val();
		if(adv_search == '0') $(".adv-serach-row").hide(); else $(".adv-serach-row").show();
  }
  
  if ($('#back-to-top').length) {
		var scrollTrigger = 100, 
			backToTop = function () {
				var scrollTop = $(window).scrollTop();
				if (scrollTop > scrollTrigger) {
					$('#back-to-top').addClass('show');
				} else {
					$('#back-to-top').removeClass('show');
				}
			};
		backToTop();
		$(window).on('scroll', function () {
			backToTop();
		});
		$('#back-to-top').on('click', function (e) {
			e.preventDefault();
			$('html,body').animate({
				scrollTop: 0
			}, 700);
		});
	}

	$('.multi_language .dropdown-item').on('click',function() {
		var output = $(this).html();
		$(".multi_language .dropdown-toggle").trigger('click');
		$(".multi_language .dropdown-toggle").html(output);
		var lang_code = $(this).attr('data-lang_code_or');
		var lang_title = $(this).attr('data-lang_title');
		var default_lang = lang_title+'~'+lang_code;
		var redirect_url = $(this).attr('href');
		$.ajax({
			url: base_url+'ajax/set_default_language_frontend',
			type: 'POST',
			success: function (res) {
				window.location.href = redirect_url;
			},
			error: function(res){
				console.log(res);
			},
			data: {default_lang : default_lang},
			cache: false
		});
		return false;
	});
	
	$('.contact_form').on('submit',function() {
		  $('.alert').remove();
		  
		  var thiss = $(this);
		  
		  if(thiss.find('#contact_name').val() == '' || thiss.find('#contact_email').val() == '' || thiss.find('#contact_message').val() == '')
		  {
			$('.contact_form').prepend('<div class="alert alert-danger alert-dismissable" style="margin-top:0px;">'
											+'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'
											+'Please fill all required fields'
										+'</div>');
			$("html, body").animate({ scrollTop: 0 }, "slow");
			$('.alert').delay(5000).fadeOut('slow');  
		  }
		  else
		  {
			  thiss.find('.submit-contact-form-btn').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Sending');
			  var callback  = "site_contact_form_submit";	
			   var formData = $(this).find(":input")
				.filter(function(index, element) {
					var updated_string = escapeHtml($(element).val());
					$(element).val(updated_string);
					return updated_string;
				})
				.serialize()+"&callback="+callback;

				
			  $.ajax({
					/*url: base_url+'ajax/submit_contact_form_callback_func',*/
					url: base_url+'admin_ajax',
					type: 'POST',
					success: function (res) {
						thiss.find('.submit-contact-form-btn').html('Send Message');
						$('.contact_form').prepend(res.output);
						if(res.return_type == 'success')
						{
							thiss[0].reset();
						}
						grecaptcha.reset();
						$("html, body").animate({ scrollTop: 0 }, "slow");
						$('.alert').delay(5000).fadeOut('slow');
					},
					error:function(args1,args2,args3){
						if(args3 == 'Not Acceptable')
						{
							$('.contact_form').prepend('<div class="alert alert-danger alert-dismissable" style="margin-top:0px;">'
											+'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'
											+'You have malicious code in submitted form data'
										+'</div>');
							thiss[0].reset();
							$("html, body").animate({ scrollTop: 0 }, "slow");
							$('.alert').delay(5000).fadeOut('slow');
						}
					},
					data: formData,
					cache: false,
					dataType: 'json',
				});
			}
		  return false;
	  });
	  
	$('.form-contact-agent').on('submit',function() {
	  $('.alert').remove();
	  var thiss = $(this);
	  
	   if(thiss.find('#name').val() == '' || thiss.find('#email').val() == '' || thiss.find('#message').val() == '')
	  {
			$('.form-contact-agent').prepend('<div class="alert alert-danger alert-dismissable" style="margin-top:0px;">'
											+'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'
											+'Please fill all required fields.'
											+'</div>');
			$("html, body").animate({ scrollTop: 0 }, "slow");
			$('.alert').delay(5000).fadeOut('slow');  
	  }
	  else
	  {
	  
		  thiss.find('.submit-contact-agent-form-btn').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Sending');
		  var callback  = "site_contact_agent_form_submit";	
		  var formData = $(this).find(":input")
			.filter(function(index, element) {
				var updated_string = escapeHtml($(element).val());
				$(element).val(updated_string);
				return updated_string;
			})
			.serialize()+"&callback="+callback;
			
		  $.ajax({
				/*url: base_url+'ajax/submit_contact_agent_form_callback_func',*/
				url: base_url+'admin_ajax',
				type: 'POST',
				success: function (res) {
					thiss.find('.submit-contact-agent-form-btn').html("Send Message");
					$('.form-contact-agent').prepend(res.output);
					if(res.return_type == 'success')
					{
						thiss[0].reset();
					}
					grecaptcha.reset();
					$('.alert').delay(5000).fadeOut('slow');
				},
				error:function(args1,args2,args3){
					if(args3 == 'Not Acceptable')
					{
						$('.form-contact-agent').prepend('<div class="alert alert-danger alert-dismissable" style="margin-top:0px;">'
										+'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'
										+'You have malicious code in submitted form data.'
									+'</div>');
						thiss[0].reset();
						
						$('.alert').delay(5000).fadeOut('slow');
					}
				},
				data: formData,
				cache: false,
				dataType: 'json',
			});
	  }
	  return false;
  });
	
	if($('.register_form').length)
	{
		function removePhpCookie(cookieName)
		{
			var cookieValue = "";
			var cookieLifetime = -1;
			var date = new Date();
			date.setTime(date.getTime()+(cookieLifetime*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
			document.cookie = cookieName+"="+JSON.stringify(cookieValue)+expires+"; path=/";
		}

		$.validator.addMethod(
			"regex",
			function(value, element, regexp) {
				var check = false;
				return this.optional(element) || regexp.test(value);
			},
			"Please provide a valid username."
		);
		
		/*
		if($('.register_form').find('.photo_url_field').length)
		{
			$.validator.addClassRules('photo_url_field', {
				required : true,
				messages : { required : 'field is required.' }
			});
		}
		*/
		
		jQuery.validator.addMethod("noSpace", function(value, element) { 
		  return value.indexOf(" ") < 0 && value != ""; 
		}, "Username didn't accept space between characters.");
		
		var validator = $( "#register_form" ).validate({
			/*ignore: "input[type='file']#att_photo",*/
			normalizer: function( value ) {
				return $.trim( value );
			},
			rules: 
			{
				first_name: 
					{
						required: true
					},
				last_name: 
					{
						required: true
					},
				username: {
					required: true,
					minlength: 5,
					noSpace: true,
					regex: /^[a-z0-9_]+$/,
					/*alphanumeric: true,*/
					"remote":
                    {
                      url: base_url+'ajax/user_field_validation_callback_func',
                      type: "post",
                      data:
                      {
						  field_type:'user_name',
                          field_value: function()
                          {
                              return $('#register_form :input[name="username"]').val();
                          }
                      },
					  beforeSend: function (response) {
						  $('#register_form :input[name="username"]').siblings('.fa').show();
					  },
					  complete: function () {
							
					  },
					  dataFilter: function (responseString) {
							var response = jQuery.parseJSON(responseString);
							if(response)
							{
								$('#register_form :input[name="username"]').siblings('.fa').attr('class','fa fa-check text-success');
								return 'true';
							}
							else
							{
								$('#register_form :input[name="username"]').siblings('.fa').attr('class','fa fa-close text-danger');
								return 'false';
							}
							 
						},
                    }
				  },
				email: {
					required: true,
					email: true,
					"remote":
                    {
                      url: base_url+'ajax/user_field_validation_callback_func',
                      type: "post",
                      data:
                      {
						  field_type:'user_email',
                          field_value: function()
                          {
                              return $('#register_form :input[name="email"]').val();
                          }
                      },
					  beforeSend: function (response) {
						  $('#register_form :input[name="email"]').siblings('.fa').show();
					  },
					  complete: function () {
							
					  },
					  dataFilter: function (responseString) {
							var response = jQuery.parseJSON(responseString);
							if(response)
							{
								$('#register_form :input[name="email"]').siblings('.fa').attr('class','fa fa-check text-success');
							}
							else
							{
								$('#register_form :input[name="email"]').siblings('.fa').attr('class','fa fa-close text-danger');
							}
						},
                    }
				  },
				password:
				{
					required: true,
					minlength: 8
				},
				repeat_password:
				{
					required: true,
					equalTo: password,
					minlength: 8
				},
			},
			messages:{
			  username:{
				remote:'Username not available. Please try another username.'
			  },
			  email:{
				remote:'Email already registered. Please try another email.'
			  },
			},
			
		  submitHandler: function(form) {
			
		  }
			
		});
		
		if($('.register_form').find('.photo_url_field').length)
		{
			$("#register_form").validate();
			$("input#att_photo_hidden").rules("add", "required");
		}
		
		$('#register_form').on('submit',function() {
			if ($('#register_form').valid()) {
				$('.submit-contact-form-btn').html('Registering...');
			
			removePhpCookie("PHPSESSID");
			var callback = 'cms_register_user';
            $.ajax({
                type: 'POST',
                url: base_url+'admin_ajax',
                dataType: "json",
                data: $(this).serialize()+'&callback='+callback,
                success: function(result) {
					$('.submit-contact-form-btn').html('Register');
					$('#register_form h4').after(result.output);
					$('#register_form .fa').hide();
					$('#register_form')[0].reset();
					
					if($('#register_form').find('.custom-file-upload').length)
					{
						var profile_img_parent = $('#register_form');
						var pi_id = 'att_photo';
						profile_img_parent.find('#'+pi_id+'_link').removeAttr('href').removeAttr('download');
						profile_img_parent.find('#'+pi_id+'_link img').removeAttr('src');
						profile_img_parent.find('#'+pi_id+'_link').hide();
						profile_img_parent.find('#'+pi_id+'_remove_img').hide();
						profile_img_parent.find('#'+pi_id+'_remove_img .fa').show();
						profile_img_parent.find('#'+pi_id).parent().show();
						profile_img_parent.find('#'+pi_id+'_hidden').val('');
					}
					
					if(result.auto_redirect == 'Y')
					{
						window.location.href = base_url+'admin/main';
					}
				},
            });
			
			}
		});
		
	}
	
	if($('.add_to_compare').length)
	{
	 var list = [];
	 
	 $(document).on('click', '.add_to_compare', function () {
        $(".comparePanle").show();
		var thiss = $(this);
		var productID = $(this).parents('.property-entry').attr('data-id');
		
		$(".property-entry[data-id="+productID+"]").toggleClass("selected");
		$(".property-entry[data-id="+productID+"]").find('.add_to_compare').toggleClass("rotateBtn").toggleClass('active');
		
		var productURL = $(this).parents('.property-entry').attr('data-url');
		//var productIMG = $(this).parents('.property-entry').attr('data-img');
		var inArray = $.inArray(productID, list);
		thiss.tooltip("hide");
		if($('.show-hide-compare-block').hasClass('close_compare_block'))
		{
			$('.show-hide-compare-block').trigger('click');
		}
		
		if (inArray < 0) 
		{
            if (list.length > 3) {
                alert('Maximum of Four Property are allowed for comparision.');
                
				$(".property-entry[data-id="+productID+"]").toggleClass("selected");
				$(".property-entry[data-id="+productID+"]").find('.add_to_compare').toggleClass("rotateBtn").toggleClass('active');
				
                return false;
            }

            if (list.length < 4) {
                list.push(productID);

                var displayTitle = $(this).parents('.property-entry').attr('data-title');
				var image_url = $(this).parents('.property-entry').find(".img-fluid").attr('src');
				
                $(".comparePan").append('<div id="' + productID + '" data-url="'+productURL+'" class="relPos w3-col l3 m3 s3"><div class="bg-white w3-ripple titleMargin"><a class="selectedItemCloseBtn w3-closebtn cursor">&times</a><img src="' + image_url + '" alt="'+displayTitle+'" /><p id="' + productID + '" class="titleMargin1">' + displayTitle + '</p></div></div>');
				update_compare_btn_url();
				$.ajax({
					url: base_url+'ajax/update_compare_settion_callback_func',
					type: 'POST',
					success: function (res) {
						thiss.attr('data-original-title', 'Remove from Compare');
					},
					error: function(res){
						console.log(res);
					},
					data: {'p_id' : productID,'action':'add','productURL':productURL, 'productIMG' : image_url, 'productTitle' : displayTitle},
					cache: false
				});
			}
        } 
		else 
		{
            list.splice($.inArray(productID, list), 1);
            var prod = productID.replace(" ", "");
            $('#' + prod).remove();
            hideComparePanel();
			update_compare_btn_url();
			$.ajax({
				url: base_url+'ajax/update_compare_settion_callback_func',
				type: 'POST',
				success: function (res) {
					thiss.attr('data-original-title', 'Add to Compare');
				},
				error: function(res){
					console.log(res);
				},
				data: {p_id : prod,'action':'remove'},
				cache: false
			});
        }
        if (list.length > 1) {

            $(".cmprBtn").addClass("active");
            $(".cmprBtn").removeAttr('disabled');
			$(".cmprBtn").removeClass("disabled_btn");
        } else {
			$(".cmprBtn").addClass('disabled_btn');
            $(".cmprBtn").removeClass("active");
            $(".cmprBtn").attr('disabled', '');
        }
		
		return false;
    });
	
	$(document).on('click', '.selectedItemCloseBtn', function () {
		var test = $(this).siblings("p").attr('id');
		if($('[data-id=' + test + ']').length)
		{
			$('[data-id=' + test + ']').eq(0).find(".add_to_compare").click();
			hideComparePanel();
			update_compare_btn_url();
		}
		else
		{
			list.splice($.inArray(test, list), 1);
            var prod = test.replace(" ", "");
            $('#' + prod).remove();
            hideComparePanel();
			update_compare_btn_url();
			$.ajax({
				url: base_url+'ajax/update_compare_settion_callback_func',
				type: 'POST',
				success: function (res) {
					
				},
				error: function(res){
					console.log(res);
				},
				data: {p_id : prod,'action':'remove'},
				cache: false
			});
			if (list.length > 1) {

				$(".cmprBtn").addClass("active");
				$(".cmprBtn").removeAttr('disabled');
				$(".cmprBtn").removeClass("disabled_btn");
			} else {
				$(".cmprBtn").addClass('disabled_btn');
				$(".cmprBtn").removeClass("active");
				$(".cmprBtn").attr('disabled', '');
			}
		}
		
        
    });
	
	init_compare_func();
	
	$('.show-hide-compare-block').on('click',function() {
		$(this).toggleClass('close_compare_block');
		$('.comparePanle').find('.header-block, .comparePan').slideToggle('slow');
	});
	
	function init_compare_func()
	{
		if($('.comparePan .relPos').length)
		{
			var url = base_url+'compare'+def_lang;
			$('.comparePan .relPos').each(function() {
				var prop_url = $(this).attr('data-url');
				url += '/'+prop_url;
				
				var prop_id = $(this).attr('id');
				$('[data-id=' + prop_id + ']').find('.add_to_compare').addClass("rotateBtn active");
				$('[data-id=' + prop_id + ']').addClass("selected");
				
				list.push(prop_id);
			});
			if (list.length > 1) {

				$(".cmprBtn").addClass("active");
				$(".cmprBtn").removeAttr('disabled');
				$(".cmprBtn").removeClass("disabled_btn");
			} else {
				$(".cmprBtn").addClass('disabled_btn');
				$(".cmprBtn").removeClass("active");
				$(".cmprBtn").attr('disabled', '');
			}
		
			$(".cmprBtn").attr('href', url);
			$(".comparePanle").show();
		}
	}
	
	function update_compare_btn_url()
	{
		if($('.comparePan .relPos').length)
		{
			var url = base_url+'compare'+def_lang;
			$('.comparePan .relPos').each(function() {
				var prop_url = $(this).attr('data-url');
				url += '/'+prop_url;
			});
			$(".cmprBtn").attr('href', url);
		}
		else
		{
			$(".cmprBtn").attr('href', '#');
		}
	}
	
	function hideComparePanel() {
        if (!list.length) {
            $(".comparePan").empty();
            $(".comparePanle").hide();
        }
    }
	
  }
  
    $('.att_photo,#att_photo,#att_id').on('change',function()
	{ 
		$('.full_sreeen_overlay').show();
		id = $(this).attr('id');
		var thiss = $(this);
		var user_type = $(this).attr('data-user-type');
		$('#'+id+'_progress').show();
		var data = new FormData();
		data.append('img', $('#'+id).prop('files')[0]);
		data.append('user_type',user_type);
		$.ajax({
			url: base_url + 'ajax_images/upload_image_callback_func',
			type: 'POST',
			xhr: function() {
				var myXhr = $.ajaxSettings.xhr();
				if(myXhr.upload){
					myXhr.upload.addEventListener('progress',progress, false);
				}
				return myXhr;
			},
			success: function (res) {
				$('#'+id).parent().hide();
				$('#'+id+'_progress').hide();
				$('#'+id+'_hidden').val(res.img_name).change();	
				if($('.register_form').length)
				{
					$('#'+id+'_hidden').valid();	
				}
				$('a#'+id+'_link').attr('href',res.img_url).attr('download',res.img_name);
				$('a#'+id+'_link img').attr('src',res.img_url);
				$('a#'+id+'_link').show();
				$('a#'+id+'_remove_img').show();
				$('.full_sreeen_overlay').hide();
			},
			data: data,
			cache: false,
			contentType: false,
			processData: false,
		});
	});
	
		
	$('a.remove_img').on('click',function() {
		var id = $(this).attr('data-name');
		var thiss = $(this);
		var img_name = $('#'+id+'_hidden').val();
		var user_type =  $('#'+id).attr('data-user-type');
		
		var strconfirm = confirm("Are you sure you want to delete?");
		if (strconfirm == true)
		{
				$('.full_sreeen_overlay').show();
				$.ajax({
					url: base_url + 'ajax_images/delete_image_callback_func',
					type: 'POST',
					success: function (res) {
						if(res == 'success')
						{
							$('a#'+id+'_link').removeAttr('href').removeAttr('download');
							$('a#'+id+'_link img').removeAttr('src');
							$('#'+id+'_link').hide();
							$('#'+id).parent().show();
							thiss.hide();
							$('#'+id+'_hidden').val('');
						}
						$('.full_sreeen_overlay').hide();
					},
					data: {img_name : img_name,user_type : user_type},
					cache: false
				});
			
		}
		return false;
	});
});