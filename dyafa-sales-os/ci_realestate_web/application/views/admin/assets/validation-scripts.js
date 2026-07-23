$(function () {

	"use strict";

	if($('.add_package_form').length && 0)
	{
		var validator = $( ".add_package_form" ).validate({
			ignore: "",
			normalizer: function( value ) {
				return $.trim( value );
			},
			rules: 
			{
				package: 
					{
						required: true
					},
				packages_price: 
					{
						required: true,
						number: true
					},
				
				currency_code: 
					{
						required: true
					},
				'user_types[]':
					{
						required: true,
						minlength: 1 
					},
				'package_features[]':
					{
						required: true,
						minlength: 1 
					},
			},
			messages:{
			  /*
			  username:{
				remote:'Username not available. Please try another username.'
			  },
			  email:{
				remote:'Email already registered. Please try another email.'
			  },
			  */
			  
			   'user_types[]': {
					required: "You must select at least 1 user",
					minlength: "You must select at least 1 user",
				},
				'package_features[]': {
					required: "You must select at least 1 feature",
					minlength: "You must select at least 1 feature",
				}
			},
			 tooltip_options: {

				/*example4: {trigger:'focus'},*/
				'user_types[]': {placement:'top'},
				'package_features[]': {placement:'right'}

			}, 
		  submitHandler: function(form) {
			
			$(form).submit();
			
		  }
			
		});
		/*
		$(".package_features").rules("add", {
			required: true,
			minlength: 1,
			messages: {
				required:  "You must select at least 1 feature",
			}
		});
		*/
		/*
		$('.add_package_form').on('submit',function() {
			alert('here');
			if ($('.add_package_form').valid()) {
				return true;
			}
			else
			{
				return  false;
			}
		});
		*/
	}
	
	$('.select2_elem').on('change', function() { 
		$(this).valid(); 
	});
	
	$('.minimal:not(.no-validate)').on('ifChecked', function() { 
		$(this).valid(); 
	});
	
	if($('form.form').length)
	{
		$('form.form').each(function() 
		{
		
			var validator = $(this).validate({
				errorClass: "invalid",
				ignore: false,
				invalidHandler: function(e,validator) {
					for (var i=0;i<validator.errorList.length;i++){
						$(validator.errorList[i].element).closest('.collapsed-box').find('.btn-box-tool').click();
					}
				},
				submitHandler: function(form) {
					if ($(form).valid()) 
					   return true;
				},
				highlight: function(element) {
					$(element).closest('.form-group').addClass('has-error');
				},
				unhighlight: function(element) {
					$(element).closest('.form-group').removeClass('has-error');
				},
				errorPlacement: function(error, element) {
					if(element.hasClass('select2_elem') && element.next('.select2-container').length) {
						error.insertAfter(element.next('.select2-container'));
					}
					else if(element.hasClass('minimal') && element.parents('.form-group').length)
					{
						error.appendTo(element.parents('.form-group')).css('display','block');
					}
					else if (element.parent('.custom-file-upload').length && element.parents('.form-group').length) {
						error.appendTo(element.parents('.form-group')).css('display','block');
					}
					else if(element.hasClass('wysihtml_editor_elem') && element.parents('.form-group').length)
					{
						error.appendTo(element.parents('.form-group')).css('display','block');
					}
					else if (element.parent('.input-group').length) {
						error.insertAfter(element.parent());
					}
					else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
						error.insertAfter(element.parent().parent());
					}
					else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
						error.appendTo(element.parent().parent());
					}
					else {
						error.insertAfter(element);
					}
				}
			});
		});
	}
	
	
	if($('form.homepage_section_form').length)
	{
		var validator = $( ".homepage_section_form" ).validate({
			errorClass: "invalid",
			ignore: false,
			invalidHandler: function(e,validator) {
				for (var i=0;i<validator.errorList.length;i++){
					$(validator.errorList[i].element).parents('li').find('.header-block .btn-box-tool').click(); 
				}
			},
			submitHandler: function(form) {
				if ($(form).valid()) 
				   return true;
			},
			highlight: function(element) {
				$(element).closest('.form-group').addClass('has-error');
			},
			unhighlight: function(element) {
				$(element).closest('.form-group').removeClass('has-error');
			},
			errorPlacement: function(error, element) {
				if(element.hasClass('select2_elem') && element.next('.select2-container').length) {
					error.insertAfter(element.next('.select2-container'));
				}
				else if (element.parent('.input-group').length) {
					error.insertAfter(element.parent());
				}
				else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
					error.insertAfter(element.parent().parent());
				}
				else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
					error.appendTo(element.parent().parent());
				}
				else {
					error.insertAfter(element);
				}
			}
		});
		
	}
	
});
