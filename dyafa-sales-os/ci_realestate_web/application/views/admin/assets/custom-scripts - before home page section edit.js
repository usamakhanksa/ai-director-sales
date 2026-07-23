var id, img_data;

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
	
	
	
  	/*
	$(".sidebar-property-details").sortable({
		placeholder: "sort-highlight",
		connectWith: ".sidebar-property-details .widget",
		handle: ".box-header, .nav-tabs",
		forcePlaceholderSize: true,
		zIndex: 999999
	});
  
	$(".sidebars .widget .box-header, .sidebars .widget .nav-tabs-custom").css("cursor", "move");*/
		/*$(".widget").sortable({
		placeholder: "sort-highlight",
		connectWith: ".widget",
		handle: ".box-header, .nav-tabs",
		forcePlaceholderSize: true,
		zIndex: 999999
	});
  
	$(".sidebars .widget .box-header, .sidebars .widget .nav-tabs-custom").css("cursor", "move");*/
	
	/***** this is from add-new forms, ******/
	
	var id;
	
	function escapeHtml(text) {
		var filter_data = DOMPurify.sanitize(text, {SAFE_FOR_TEMPLATES: true});
		return jQuery.trim(filter_data);
	}
	
	function progress(e){
		if(e.lengthComputable){
		   $('#'+id+'_progress').show();
			$('progress').attr({value:e.loaded,max:e.total});
		}
	}
	
	function cal_deleted_img()
	{
		var del_leng = $('.media_container .album_images.selected').length;
		if(del_leng > 0)
		{
			if(del_leng == 1)
				$('.select-msg-text-block').html(del_leng+' image selected');
			else
				$('.select-msg-text-block').html(del_leng+' images selected');
			$('.remove-album-images').removeClass('disabled');
		}
		else
		{
			$('.select-msg-text-block').html('(Click on image to select multiple)');
			$('.remove-album-images').addClass('disabled');
		}
	}
	
	function update_video_links()
	{
		if($('.vdo_url_container').find('.form-group').length <= 1)
		{
			$('.vdo_url_container').find('.form-group .remove-video-link').addClass('disabled');
		}
		else
		{
			$('.vdo_url_container').find('.form-group .remove-video-link').removeClass('disabled');
		}
	}
	
	function extendMagnificIframe(){

		var $start = 0;
		var $iframe = {
			markup: '<div class="mfp-iframe-scaler">' +
					'<div class="mfp-close"></div>' +
					'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
					'</div>' +
					'<div class="mfp-bottom-bar">' +
					'<div class="mfp-title"></div>' +
					'</div>',
			patterns: {
				youtube: {
					index: 'youtu', 
					id: function(url) {   

						var m = url.match( /^.*(?:youtu.be\/|v\/|e\/|u\/\w+\/|embed\/|v=)([^#\&\?]*).*/ );
						if ( !m || !m[1] ) return null;

							if(url.indexOf('t=') != - 1){

								var $split = url.split('t=');
								var hms = $split[1].replace('h',':').replace('m',':').replace('s','');
								var a = hms.split(':');

								if (a.length == 1){

									$start = a[0]; 

								} else if (a.length == 2){

									$start = (+a[0]) * 60 + (+a[1]); 

								} else if (a.length == 3){

									$start = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]); 

								}
							}                                   

							var suffix = '?autoplay=1';

							if( $start > 0 ){

								suffix = '?start=' + $start + '&autoplay=1';
							}

						return m[1] + suffix;
					},
					src: '//www.youtube.com/embed/%id%'
				},
				vimeo: {
					index: 'vimeo.com/', 
					id: function(url) {        
						var m = url.match(/(https?:\/\/)?(www.)?(player.)?vimeo.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/);
						if ( !m || !m[5] ) return null;
						return m[5];
					},
					src: '//player.vimeo.com/video/%id%?autoplay=1'
				}
			}
		};

		return $iframe;     

	}
	
	function RemoveRougeChar(convertString){
		/*var n = convertString.toString();
		//var newvalue = n.replace(/,/g, ''); 
		//var valuewithcomma = newvalue.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		//return valuewithcomma;*/ 
		return convertString;
	}
	
	function update_site_language_name_id_func()
	{
		$('.select2_elem').select2('destroy');
		$('.minimal').iCheck('destroy');
		$('.lang-container .single-lang-block').each(function(i){
			var row_count = i+1; 
			$(this).find('.col-md-4').each(function() {
				if($(this).find('select').length)
				{
					$(this).find('select').attr("id", $(this).find('select').attr("id").replace(/\d+/, row_count));
					$(this).find('select').attr("name", $(this).find('select').attr("name").replace(/\d+/, row_count));
				}
				if($(this).find('input[type="radio"]').length)
				{
					$(this).find('.radio_toggle_wrapper input[type="radio"]').each(function() {
						$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
						$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
					});
					
				}
				if($(this).find('label').length)
				{
					if($(this).find('label').length == 1)
						$(this).find('label').attr("for", $(this).find('label').attr("for").replace(/\d+/, row_count));
					else
					{
						$(this).find('.radio_toggle_wrapper label').each(function() {
							$(this).attr("for", $(this).attr("for").replace(/\d+/, row_count));
						});
						
					}
				}
				
				$(this).find('.inputtext').each(function(e) {
					$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
					$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
					
				});
				
			});
			$(this).find('.radio_toggle_wrapper input[type="radio"]:checked').next('label').trigger('click');
			
			$(this).find('.language_list').select2({
				width : '100%'
			});
			$(this).find('.currency_list').select2({
				width : '100%'
			});
			$(this).find('.timezone_list').select2({
				width : '100%'
			});
			$(this).find('.currency_pos_list').select2({
				width : '100%'
			});
			
			$(this).find('.minimal').iCheck({
			  checkboxClass: 'icheckbox_minimal-blue',
			  radioClass: 'iradio_minimal-blue'
			});
			
		});
		
		if($('.lang-container .single-lang-block').length <= 1)
		{
			$('.lang-container .single-lang-block .remove-lang-block').hide();
		}
		else
		{
			$('.lang-container .single-lang-block .remove-lang-block').show();
		}
	}
	
	if($('.homepage_section_form').length)
    {
	  if($('.show_hide_block_btn').length)
		{
			$('.show_hide_block_btn').each(function(e) { /*:not(".child_element_block")*/
				var thiss = $(this);
				var li_parents = thiss.parents('li');
				var elem_name = $(this).attr('name');
				var elem_val = li_parents.find("input[name='"+elem_name+"']:checked").val();
				var data_target = li_parents.find("input[name='"+elem_name+"']:checked").attr('data-target');
				li_parents.find('.'+data_target).hide();
				li_parents.find('.'+data_target+'.'+elem_val+'_block').addClass('hidden-elem').show();
				
				/*
				var unchecked_elem_val = $("input[name='"+elem_name+"']:not(:checked)").val(); 
				$('.'+data_target+'.'+unchecked_elem_val+'_block').find('.show_hide_block_btn').addClass('child_element_block');
				
				if($('.'+data_target+'.'+unchecked_elem_val+'_block').find('.show_hide_block_btn').length)
				{
					var elem_name = $('.'+data_target+'.'+unchecked_elem_val+'_block').find('.show_hide_block_btn').attr('name');
					var elem_val = $("input[name='"+elem_name+"']:checked").val();
					
					var data_target = $("input[name='"+elem_name+"']:checked").attr('data-target');
					$('.'+data_target).hide();
					$('.'+data_target+'.'+elem_val+'_block').addClass('hidden-elem').show();
				}
				*/
			});
			
			
			$('.show_hide_block_btn').each(function(e) {
				
				if(!$(this).parents('.form-group').hasClass('carousel_block') && 
					!$(this).parents('.form-group').hasClass('grid_block'))
				{
					var thiss = $(this);
					var li_parents = thiss.parents('li');
					var elem_name = $(this).attr('name');
					var elem_val = li_parents.find("input[name='"+elem_name+"']:checked").val();
					var data_target = li_parents.find("input[name='"+elem_name+"']:checked").attr('data-target');
					if(!li_parents.find('.'+data_target+'.'+elem_val+'_block').hasClass('hidden-elem'))
					{
						li_parents.find('.'+data_target).hide();
						li_parents.find('.'+data_target+'.'+elem_val+'_block').show();
					}
				}
			});
			
		}
		
		$('.show_hide_block_btn:not(".child_element_block")').change(function() {
			var elem_name = $(this).attr('name');
			var elem_val = $("input[name='"+elem_name+"']:checked").val();
			var data_target = $("input[name='"+elem_name+"']:checked").attr('data-target');
			$('.'+data_target).hide();
			$('.'+data_target+'.'+elem_val+'_block').show();
			return false;
		});
		
		$(document).delegate('.collapsed','click',function() {
			$(this).find('.fa').removeClass('fa-chevron-down').addClass('fa-chevron-up');
			$(this).parents('li').find('.section_fields').removeClass('hide');
			$(this).removeClass('collapsed').addClass('expended');
			
			return false;
		});
		
		$(document).delegate('.expended','click',function() {
			$(this).find('.fa').removeClass('fa-chevron-up').addClass('fa-chevron-down');
			$(this).parents('li').find('.section_fields').addClass('hide');
			$(this).removeClass('expended').addClass('collapsed');
			return false;
		});
		
		$(".todo-list").sortable({
			placeholder: "sort-highlight",
			handle: ".handle",
			forcePlaceholderSize: true,
			zIndex: 999999,
			cancel : '.fixed-section',
			items: "li:not(.fixed-section)",
		  });
		
		function escapeHtml(text) {
			'use strict';
			var filter_data = DOMPurify.sanitize(text, {SAFE_FOR_TEMPLATES: true});
			return jQuery.trim(filter_data);
		}

		$('.submit-section-btn').on('click',function() {
			var values = $('.form').find(":input,textarea")
			.filter(function(index, element) {
				var updated_string = escapeHtml($(element).val());
				$(element).val(updated_string);
			})
			if (values) {
				$('.form').submit();
			}
			else
			{
				return false;
			}
		});
		
		$('.add-video-url-btn').on('click',function() {
			var thiss = $(this);
			var cloned_elem = thiss.parents('li').find('.video-url-container').eq(0).clone(true);
			cloned_elem.find('input[type="url"]').val('');
			thiss.parents('.form-group').before(cloned_elem);
			return false;
		});
		
  }
	
	if($('.add_property_form').length)
	{
		update_video_links();
		$(document).delegate('.remove-video-link','click',function() {
			if(!$(this).hasClass('disabled'))
			{
				$(this).parents('.form-group').remove();
				update_video_links();
			}
			return false;
		});
		
		$('.add_more_vdo_btn').on('click',function() {
			var cloned_elem = $('.vdo_url_container').find('.form-group').eq(0).clone(true);
			cloned_elem.find('.form-control').val('');
			cloned_elem.find('.popup-player').removeAttr('href').attr('disabled',true);
			$('.vdo_url_container').append(cloned_elem);
			
			$('.vdo_url_container .form-group').each(function(i){
				var row_count = i+1; 
				$(this).find('.video_url').attr("id", $(this).find('.video_url').attr("id").replace(/\d+/, row_count));
				$(this).find('label').attr("for", $(this).find('label').attr("for").replace(/\d+/, row_count));
			});
			update_video_links();
			return false;
		});
		
		$('.popup-player').magnificPopup({
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,
			fixedContentPos: false,
			iframe: extendMagnificIframe(),
			closeOnBgClick:false,
			fixedContentPos : true,
			overflowY:'hidden',
			callbacks: {
			  
			  elementParse: function(item) {
				  var cur_elem = item.el;
				 item.src = cur_elem.parents('.input-group').find('.video_url').val();
			  },
			},
		});
		
		$('input[type="radio"][name="property_for"]').on('change',function() {
			$('.property_type_rent_block').hide();
			if($(this).val() == 'Rent')
			{
				$('.property_type_rent_block').show();
			}
		});
		
		$('.custom-dropdown-menu li a').on('click',function() {
			var f_value = $(this).html();
			$(this).parents('.input-group-btn').find('input[type="hidden"]').val(f_value);
			$(this).parents('.input-group-btn').removeClass('open');
			$(this).parents('.input-group-btn').find('button').attr('aria-expanded',false).html(f_value+'&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
			return false;
		});
		
		$('body').delegate('.tools i.fa-trash-o','click', function() {
			$(this).parents('li').remove('');
			return false;
		});
		
		$(".todo-list").sortable({
			placeholder: "sort-highlight",
			handle: ".handle",
			forcePlaceholderSize: true,
			zIndex: 999999
		  });
		
		/*
		$("#price").on("change",function(){
			var $this = $(this);
			var price = $(this).val().replace(/,/g,"");
			$this.val(price);
		}).on("focus",function(e){
			var $this = $(this);
			var num = $this.val().replace(/,/g,"");
			$this.val(num);
		}).on("blur", function(e){
			var $this = $(this);
			var num = $this.val();	
			var num2 = RemoveRougeChar(num);
			$this.val(num2);
		});
		*/
		
		$('.direction').select2({
			width:'100%'
		});
		
		$('.custom-dropdown-menu li a').on('click',function() {
			var f_value = $(this).html();
			$(this).parents('.input-group-btn').find('input[type="hidden"]').val(f_value);
			$(this).parents('.input-group-btn').removeClass('open');
			$(this).parents('.input-group-btn').find('button').attr('aria-expanded',false).html(f_value+'&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
			return false;
		});
		
		$('#country, #state, #city').select2({
			width : '100%'
		});
		
		$(document).delegate('.remove-product-btn','click',function() {
			var thiss = $(this);
			$(this).parents('.media_images_inner').tooltip('destroy');
			var img_id = $(this).parent().find('img').attr('data-img_id');
			var img_ids = $('input[type="hidden"][name="addedImgFromMediaLibrary"]').val();
			if(img_ids != '')
			{
				$(this).parents('.media-img-block').remove();
				var ids_array = img_ids.split(',');
				ids_array.splice( $.inArray(img_id, ids_array), 1 );
				$('input[type="hidden"][name="addedImgFromMediaLibrary"]').val(ids_array.join(","));
			}
			return false;
		});
		
	/*
	$('#media_att_photo').on('change',function(){
		$(this).valid(); 
		$('.full_sreeen_overlay').show();
		id = $(this).attr('id');
		var image_type = $(this).attr('data-type');
		$('#'+id+'_progress').show();
		var data = new FormData();
		
		var files = $('#'+id).prop('files');
		for (var i = 0; i < files.length; i++) {
			var file = files[i];
			data.append('img[]', file, file.name);
		}
		
		data.append('image_type',image_type);
		
		$.ajax({
			url: base_url+'ajax_images/multi_image_upload_callback_func',
			type: 'POST',
			xhr: function() {
				var myXhr = $.ajaxSettings.xhr();
				if(myXhr.upload){
					myXhr.upload.addEventListener('progress',progress, false);
				}
				return myXhr;
			},
			success: function (res) {
				$('#'+id+'_progress').hide();
				$('#'+id).parent().show();
				$.each(res.uploaded_img_list, function(k, v) {
					var img = "<div class='media-img-block'><span class='remove-product-btn'><i class='fa fa-remove'></i></span><img data-img_id='"+v.img_id+"' src=";
					img += v.img_url;
					img += "></div>";
					$('.product-gallary-container').append(img);
					
					var old_img_data = $('input[type="hidden"][name="addedImgFromMediaLibrary"]').val();
					var new_img_data = v.img_id;
					if(old_img_data != "")
					{
						new_img_data = old_img_data+','+v.img_id;
					}
					$('input[type="hidden"][name="addedImgFromMediaLibrary"]').val(new_img_data);
				});
				$('.full_sreeen_overlay').hide();
			},
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			enctype: 'multipart/form-data',
		});
			
	 });
	*/
	
	$('.add_from_media_btn').on('click',function() {
			img_data = $('input[type="hidden"][name="addedImgFromMediaLibrary"]').val();
	});
	
	
	$('.add_from_media_btn').magnificPopup({
	  type: 'ajax',
	  closeOnContentClick : false,
	  closeOnBgClick : false,
	  overflowY : 'scroll',
	  ajax: {
		 settings: {
				url: base_url+'ajax_images/add_image_from_media_ajax_callback_func',
				type: 'POST'
			},

		  cursor: 'mfp-ajax-cur', 
		  tError: '<a href="%url%">The content</a> could not be loaded.' 
		},
		callbacks: {
			
			elementParse: function() {
				this.st.ajax.settings.data = {
					img_data     : img_data
				}
			},
			 parseAjax: function(mfpResponse) {
			  },
			  ajaxContentAdded: function() {
			  }
		},
	});
	
	$(document).delegate('ul.media_img_block li a ','click',function() {
		if($(this).find('img').hasClass('selected'))
		{
			$(this).find('img').removeClass('selected');
			$(this).find('span.select-check').hide();
		}
		else
		{
			$(this).find('img').addClass('selected');
			$(this).find('span.select-check').show();
		}
		return false;
	});
	
		$(document).delegate('.insert_in_product','click',function() {
			var img_id = [];
			var data = '';
			$('ul.media_img_block li').each(function() {
				if($(this).find('a img').hasClass('selected'))
				{	
					img_id.push($(this).find('a').attr('data-image-id'));
					var img_title = $(this).find("a").attr("data-title");
					var img_src = $(this).find("a img").attr("src");
					var img = "<div class='media-img-block'><div class='media_images_inner' data-container='body' data-toggle='tooltip' title='"+img_title+"'><span class='remove-product-btn'><i class='fa fa-remove'></i></span><img data-img_id='"+$(this).find('a').attr('data-image-id')+"' src=";
					img += img_src;
					img += "></div></div>";
					data += img;
				}
			});
			$('input[type="hidden"][name="addedImgFromMediaLibrary"]').val(img_id);
			$('.product-gallary-container').html(data);
			$.magnificPopup.close();
			$( ".product-gallary-container" ).sortable({
			  stop: function( event, ui ) {
				  var imgEditArray = [];
				  $('.product-gallary-container .media-img-block').each(function() {
					  var img_id = $(this).find('img').attr('data-img_id');
					  if(img_id != '')
						imgEditArray.push(img_id);
				  });
				  $('input[type="hidden"][name="addedImgFromMediaLibrary"]').val(imgEditArray.join(","));
			  }
			});
			return false;
		});
		
		if($('.product-gallary-container .media-img-block').length)
		{
			
			$( ".product-gallary-container" ).sortable({
			  stop: function( event, ui ) {
				  var imgEditArray = [];
				  $('.product-gallary-container .media-img-block').each(function() {
					  imgEditArray.push($(this).find('img').attr('data-img_id'));
				  });
				  $('input[type="hidden"][name="addedImgFromMediaLibrary"]').val(imgEditArray.join(","));
			  }
			});
		}
		
		/* Document Related Script*/
		
		
		var document_data = '';
		$('.add_from_document_btn').on('click',function() {
			$('.add_from_document_btn').removeClass('current');
			var thiss = $(this);
			thiss.addClass('current')
			id = thiss.parents('.document-block').find('.document_uploader').attr('id');
			document_data = thiss.parents('.document-block').find('#'+id+'_hidden').val();
		});
		
		
		$('.add_from_document_btn').magnificPopup({
		  type: 'ajax',
		  closeOnContentClick : false,
		  closeOnBgClick : false,
		  overflowY : 'scroll',
		  ajax: {
			 settings: {
					url: base_url+'documents/add_doc_from_document_library_ajax_callback_func',
					type: 'POST'
				},

			  cursor: 'mfp-ajax-cur', 
			  tError: '<a href="%url%">The content</a> could not be loaded.' 
			},
			callbacks: {
				
				elementParse: function() {
					this.st.ajax.settings.data = {
						img_data     : document_data
					}
				},
				 parseAjax: function(mfpResponse) {
				  },
				  ajaxContentAdded: function() {
				  },
				close: function(){
					 $('.add_from_document_btn').removeClass('current')
				  }
			},
		});
		
		
		$(document).delegate('.insert_doc_in_property','click',function() {
			var img_id = [];
			var data = '';
			$('ul.media_img_block li').each(function() {
				if($(this).find('a img').hasClass('selected'))
				{	
					img_id.push($(this).find('a').attr('data-image-id'));
					var img_src = $(this).find("a img").attr("src");
					var img = "<div class='col-md-3 document_images'><div class='document_images_inner' data-toggle='tooltip' data-original-title='"+$(this).find('a').attr('data-title')+"'><img data-img_id='"+$(this).find('a').attr('data-image-id')+"' src=";
					img += img_src;
					img += '><a href="#" class="select-check remove_document_from_list" data-att_id="'+$(this).find('a').attr('data-image-id')+'"'+ 
							'><i class="fa fa-remove"></i></a>';
					img += "</div></div>";
					data += img;
				}
			});
			
			var thiss = $(this);
			
			var curr_btn = $('.add_from_document_btn.current');
			
			id = curr_btn.parents('.document-block').attr('id');
			
			var old_img_data = $('#'+id+'_field_hidden').val();
			var new_img_data = img_id.join(",");
			if(old_img_data != "")
			{
				new_img_data = old_img_data+','+new_img_data;
			}
			$('#'+id+'_field_hidden').val(new_img_data);
			
			curr_btn.parents('.document-block').find('.product-document-container').prepend(data);
			
			$.magnificPopup.close();
			
			return false;
		});
		
		
		$(document).delegate('.remove_document_from_list','click',function() {
			var curr_btn = $(this);
			$(this).parents('.document_images_inner').tooltip('destroy');
			var img_id = $(this).parent().find('img').attr('data-img_id');
			id = curr_btn.parents('.document-block').attr('id');
			var img_ids = String($('#'+id+'_field_hidden').val());
			if(img_ids != '')
			{
				$(this).parents('.document_images').remove();
				var ids_array = img_ids.split(',');
				ids_array.splice( $.inArray(img_id, ids_array), 1 );
				$('#'+id+'_field_hidden').val(ids_array.join(","));
				
			}
			return false;
		});
		
		
		/* End of Document Related Script*/
	}
	
	$('.keywords').on('change',function() {
		var thiss = $(this);
		thiss.parents('.form-group').addClass('processing');
		var lang_id =  thiss.attr('data-lang_id');
		var lang_slug =  thiss.attr('data-lang_slug');		
		var value =  thiss.val();
		$.ajax({						
			url: base_url+'ajax/update_keywords_callback_func',						
			type: 'POST',						
			success: function (res) 
			{							
				thiss.parents('.form-group').removeClass('processing');
				thiss.parent().append('<span class="label label-success" >Updated</span>');
				
				thiss.parent().find('.label').delay(3000).hide("slow",function(){
					$(this).remove();
				});
				
			},						
			data: {lang_id : lang_id, lang_slug : lang_slug, value : value},						
			cache: false					
		});	
		return false;
	});
	
	$('.delete-property').on('click',function() {
		if (!confirm("Do you really want to delete this property?")){
		  return false;
		}
	});
	
	$('.skin-container li a').on('click',function() {
		var skin = $(this).attr('data-skin');
		$('.option_skin').val(skin);
		$('.skin-container li a').addClass('full-opacity-hover');
		$(this).removeClass('full-opacity-hover');
		return false;
	});
	
	$(".property_for_cities").select2({
	  tags: true,
	  allowClear: true,
	  width:'100%',
	  multiple:true,
	 
	});
	
	$('.show_hide_property_for_cities').on('change',function() {
		if($(this).val() == 'Y')
		{
			$('.show_hide_property_for_cities_block').show();
		}
		else
		{
			$('.show_hide_property_for_cities_block').hide();
		}
	});
	
	$(".property_for_states").select2({
		tags: true,
		allowClear: true,
		width:'100%',
		multiple:true,
	});
	
	$('.show_hide_property_for_states').on('change',function() {
		if($(this).val() == 'Y')
		{
			$('.show_hide_property_for_states_block').show();
		}
		else
		{
			$('.show_hide_property_for_states_block').hide();
		}
	});
	
	$('.featured-prod-checkbox').on('ifChanged', function (event) { $(event.target).trigger('change'); });
	
	$('.featured-prod-checkbox').on('change',function() {
		$('.full_sreeen_overlay').show();
		var thiss = $(this);
		var p_id = thiss.attr('data-p_id');
		var is_feat = 'N';
		if(thiss.is(':checked'))
			is_feat = 'Y';
		
		$.ajax({
			url: base_url+'ajax/toggle_featured_property_callback_func',
			type: 'POST',
			success: function (res) {
				if(res != 'success')
				{
					$('.page-title').after(res);
					thiss.iCheck('uncheck');
				}
				$('.full_sreeen_overlay').hide();
			},
			data: {is_feat : is_feat,p_id : p_id},
			cache: false
		});
	
		
		return false;
	});
	
	$(".distances_list").select2({
	  tags: true,
	  allowClear: true
	});
	
	$(".amenities_list").select2({
	  tags: true,
	  allowClear: true
	});
	
	$('.user_types').select2();
	
	$('#UserName').on('keyup',function() {
		var user_name = $(this).val();
		var thiss = $(this);
		if(user_name != '')
		{
			$.ajax({
				url: base_url+'ajax/check_username_existence',
				type: 'POST',
				success: function (res) {
					thiss.parents('.form-group').removeClass('has-success');
					thiss.parents('.form-group').removeClass('has-error');
					if(res == 'success')
					{
						thiss.parents('.form-group').addClass('has-success');
						$('#save_publish').removeClass('disabled');
					}
					else
					{
						thiss.parents('.form-group').addClass('has-error');
						$('#save_publish').addClass('disabled');
					}	
					
				},
				data: {user_name : user_name},
				cache: false
			});
		}
		return false;
	});

	$('#RepeatPassword').on('keyup',function() {
		var password = $('#Password').val();
		var thiss = $(this);
		thiss.parents('.form-group').removeClass('has-success');
		thiss.parents('.form-group').removeClass('has-error');
		if($(this).val() != '')
		{
			if(password == $(this).val())
			{
				thiss.parents('.form-group').addClass('has-success');
				$('#save_publish').removeClass('disabled');
			}
			else
			{
				thiss.parents('.form-group').addClass('has-error');
				$('#save_publish').addClass('disabled');
			}	
		}
	});
	
	/*
	$('.multi_image_uploader').on('change',function(){
		$(this).valid(); 
		$('.full_sreeen_overlay').show();
		id = $(this).attr('data-att_id');
		var image_type = $(this).attr('data-type');
		$('#'+id+'_progress').show();
		var data = new FormData();
		var files = $(this).prop('files');
		for (var i = 0; i < files.length; i++) {
			var file = files[i];
			data.append('img[]', file, file.name);
		}
		data.append('image_type',image_type);
		$.ajax({
			url: base_url+'ajax_images/multi_image_upload_callback_func',
			type: 'POST',
			xhr: function() {
				var myXhr = $.ajaxSettings.xhr();
				if(myXhr.upload){
					myXhr.upload.addEventListener('progress',progress, false);
				}
				return myXhr;
			},
			success: function (res) {
				if(res.result == 'success')
				{
					$('#'+id+'_progress').hide();
					var output = '';
					res.uploaded_img_list.reverse();
					
					$.each(res.uploaded_img_list, function(k, v) {
						output += '<div class="col-md-2 media_images">'+
							'<div class="media_images_inner" data-container="body" data-toggle="tooltip" title="" data-original-title="'+v.img_name+'">'+
								'<img src="'+v.img_url+'" width="100%">'+
								'<a href="#" class="remove_multi_img hide" id="image_'+v.img_id+'" data-type="media" data-name="image_'+v.img_id+'"><i class="fa fa-remove"></i></a>'+
								'<span class="select-check hide"><i class="fa fa-check"></i></span>'+
								'<input type="hidden" name="" id="image_'+v.img_id+'_hidden" value="'+v.img_name+'">'+
							'</div>'+
							'</div>';
					});
					$('.media_container').prepend(output);
				}
				else
				{
					$('.page-title').after(res.error_msg);
				}
				$('.full_sreeen_overlay').hide();
			},
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			enctype: 'multipart/form-data',
		});
			
	 });
	*/
	
	$(document).delegate('.album_images','click',function() {
		if($(this).hasClass('selected'))
		{
			$(this).find('.select-check').addClass('hide');
			$(this).removeClass('selected');
		}
		else
		{
			$(this).find('.select-check').removeClass('hide');
			$(this).addClass('selected');
		}
		cal_deleted_img();
		return false;
	});
		
	$('.remove-album-images').on('click',function() {
		if(!$(this).hasClass('disabled'))
		{
			
			var strconfirm = confirm('Are you sure you want to delete?');
			if (strconfirm == true)
			{
				$('.full_sreeen_overlay').show();
				$('.media_container .album_images.selected').each(function() {
					$(this).find('.remove_album_image').trigger('click');
				});
				
				$('.select-msg-text-block').html('(Click on image to select multiple)');
				$('.remove-album-images').addClass('disabled');
			}
			$('.full_sreeen_overlay').hide();
		}
		
		return false;
	});
		
	$(document).delegate('a.remove_album_image','click',function() {
		var id = $(this).attr('data-name');
		var thiss = $(this);
		var img_name = $('#'+id+'_hidden').val();
		var image_type =  $('#'+id).attr('data-type');
		/*$('.full_sreeen_overlay').show();*/
		$.ajax({
			url: base_url+'ajax_images/delete_gallery_images_callback_func',
			type: 'POST',
			success: function (res) {
				if(res == 'success')
				{
					if(image_type == 'photo_gallery')
					{
						thiss.parent().parent().fadeOut().remove();
					}
					else
					{
						$('a#'+id+'_link').removeAttr('href').removeAttr('download');
						$('a#'+id+'_link img').removeAttr('src');
						$('#'+id+'_link').hide();
						$('#'+id).parent().show();
						thiss.hide();
						$('#'+id+'_hidden').val('');
					}
				}
				/*$('.full_sreeen_overlay').hide();*/
			},
			data: {img_name : img_name,image_type : image_type},
			cache: false
		});
		return false;
	});
		
	$('.select-all-album-btn').on('click',function() {
		$('.media_container .album_images').addClass('selected');
		$('.media_container .album_images').find('.select-check').removeClass('hide');
		cal_deleted_img();
		return false;
	});
	
	$('.unselect-all-album-btn').on('click',function() {
		$('.media_container .album_images').removeClass('selected');
		$('.media_container .album_images').find('.select-check').addClass('hide');
		cal_deleted_img();
		return false;
	});
	
	if($('.login-page').length)
	{
		$('p.error_msg,p.success_msg').delay(5000).fadeOut('slow');
	}
	
	if($('.site_language_form').length)
	{
		$('.add-lang-btn').on('click',function() {
			var cloned_elem = $('.default-lang-block').clone(true);
			cloned_elem.removeClass('hide default-lang-block');
			$('.lang-container').append(cloned_elem);
			update_site_language_name_id_func();
			return false;
		});
		
		$(document).delegate('.remove-lang-block','click',function() {
			
			if($(this).parents('.single-lang-block').find('input[type="radio"][name="options[default_language]"]').is(':checked'))
			{
				$(this).parents('.lang-container').find('.single-lang-block').eq(0).find('input[type="radio"][name="options[default_language]"]').iCheck('check');
			}
			$(this).parents('.single-lang-block').remove();
			update_site_language_name_id_func();
			return false;
		});
		
		$(document).delegate('.language_list','change',function() {
			var cur_val = $(this).val();
			$(this).parents('.single-lang-block').find('.minimal').val(cur_val);
		});
		
		
		$('input[type="radio"][name="options[default_language]"]').on('ifChanged',function(){
			$(".status_lbl").removeClass('disabled');
			$(".remove-lang-block").removeClass('disabled');
			var thiss = $(this);
			thiss.parents('.single-lang-block').find('.status_lbl').addClass('disabled');
			thiss.parents('.single-lang-block').find('.remove-lang-block').addClass('disabled');
		});
		
		$('.site_language_form').on('submit',function() {
			$('input[type="radio"][name="options[default_language]"]:checked').parents('.single-lang-block').find('input[type="radio"][value="enable"]').trigger('click').trigger('change');
		});
	}
	
	$('.form').on('submit',function(e) {						   
		$('.form').find(":input,select,textarea").filter(function(index, element) 
		{	
			if(!$(element).hasClass('distances_list') && !$(element).hasClass('amenities_list') 
				&& !$(element).hasClass('property_for_cities') && !$(element).hasClass('property_for_states') 
				&& !$(element).hasClass('no_clean') && !$(element).hasClass('assign_to_list'))
			{
				var updated_string = escapeHtml($(element).val());
				$(element).val(updated_string);
			}
		});
	});
	
	/*
	$('.att_photo,#att_photo,#att_id').on('change',function()
	{ 
		//$(this).valid(); 
		$('.full_sreeen_overlay').show();
		id = $(this).attr('id');
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
				$('#'+id+'_hidden').val(res.img_name);
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
	*/
	/*	
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
	*/	
		
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
	
	$(".from_date").datepicker({
	  autoclose: true,
	}).on('changeDate', function (selected) {
		var startDate = new Date(selected.date.valueOf());
		$('.to_date').datepicker('setStartDate', startDate);
	}).on('clearDate', function (selected) {
		$('.to_date').datepicker('setStartDate', null);
	});
	
	$(".to_date").datepicker({
	   autoclose: true,
	}).on('changeDate', function (selected) {
	   var endDate = new Date(selected.date.valueOf());
	   $('.from_date').datepicker('setEndDate', endDate);
	}).on('clearDate', function (selected) {
	   $('.from_date').datepicker('setEndDate', null);
	});
	
	$(".datepicker").datepicker({
		autoclose:true
	});
	
	if($(".publish_on").length)
	{
		$(".publish_on").each(function() {
			var format = $(this).attr('data-format');
			var date = new Date();
			date.setDate(date.getDate());
			$(this).datepicker({
				autoclose:true,
				format : format,
				startDate:date
			});
			/*$(this).datepicker('setDate', 'now');*/
		});
	}
	
	
	
	$('#reservation').daterangepicker();
	
	$('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
	
	$('#daterange-btn').daterangepicker(
		{
		  ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		  },
		  startDate: moment().subtract(29, 'days'),
		  endDate: moment()
		},
	function (start, end) {
	  $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	}
	);

	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
	  checkboxClass: 'icheckbox_minimal-blue',
	  radioClass: 'iradio_minimal-blue'
	});
	
	$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
	  checkboxClass: 'icheckbox_minimal-red',
	  radioClass: 'iradio_minimal-red'
	});
	
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
	  checkboxClass: 'icheckbox_flat-red',
	  radioClass: 'iradio_flat-red'
	});

	$('input[type="checkbox"].minimal-green, input[type="radio"].minimal-green').iCheck({
	  checkboxClass: 'icheckbox_minimal-green',
	  radioClass: 'iradio_minimal-green'
	});
	
	$('input[type="checkbox"].flat-green, input[type="radio"].flat-green').iCheck({
	  checkboxClass: 'icheckbox_flat-green',
	  radioClass: 'iradio_flat-green'
	});
	
	$(".sidebar").slimscroll({
		height: ($(window).height() - $(".main-header").height()) + "px",
		color: "rgba(0,0,0,0.2)",
		size: "3px"
	  });
	
	$(".control-sidebar .tab-pane .scrollable_tab").slimscroll({
		height: (($(window).height() - ($(".main-header").height() + $('.control-sidebar ul.nav-tabs').height() + ($('.control-sidebar .tab-content h3.control-sidebar-heading').height()*4)) )+5) + "px",
		color: "rgba(0,0,0,0.2)",
		size: "3px"
	  });
	
	$('.hide_right_sidebar').on('click',function() {
		if($(".control-sidebar").hasClass('control-sidebar-open'))
		{
			$('.notifications-menu .dropdown-menu .footer a').trigger('click');
		}
		else
		{
			$(".control-sidebar").removeClass('control-sidebar-open');
		}
	});
	
	$('.remove_property_notif').on('click',function() {
		if (confirm("Do you really want to delete this notification?")){
			var notif_id = $(this).attr('data-notif_id');
			var thiss = $(this);
			thiss.removeClass('fa-remove').addClass('fa-spin fa-spinner');
			$.ajax({
				url: base_url +'ajax/remove_notifications_callback_func',
				type: 'POST',
				success: function (res) {
					if(res == 'success')
					{
						thiss.parents('li').fadeOut(300, function(){ thiss.parent().remove();});
					}
				},
				data: {notif_id : notif_id},
				cache: false
			});
		}
		return false;
	});
	
	
	$('.select2_elem').select2({
		width:'100%'
	});
	
	if($('.ckeditor-element').length)
	{
		var path = base_url.replace("/admin","");
		$('.ckeditor-element').each(function(e) {
			var cur_elem_id = $(this).attr('id');
			var lang_code = $(this).attr('data-lang_code');
			var lang_dir = $(this).attr('data-lang_dir');
			var editor = CKEDITOR.replace( cur_elem_id,{
				language: lang_code,
				contentsLangDirection : lang_dir,
				filebrowserBrowseUrl: path+'ckfinder/ckfinder.html',
				filebrowserImageBrowseUrl: path+'ckfinder/ckfinder.html?type=Images',
				filebrowserUploadUrl: path+'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
			});
			editor.config.removeButtons = 'Language';
			var extra =["codesnippet","youtube"];
			editor.config.extraPlugins=extra;
			editor.config.basicEntities = false;
		});
	}
	
	if($('.short-description-element').length)
	{
		var maxLength = 250;
		$('.short-description-element').keyup(function() {
		  var textlen = maxLength - $(this).val().length;
		  $(this).parents('.form-group').find('.rchars').text(textlen);
		});
		
		$('.short-description-element').each(function(e) {
			var textlen = maxLength - $(this).val().length;
			$(this).parents('.form-group').find('.rchars').text(textlen);
		});
	}
	
	$('.datatable-element').DataTable({
	  "paging": true,
	  "lengthChange": false,
	  "searching": true,
	  "ordering": false,
	  "info": true,
	  "autoWidth": false
	});
	
	$('.datatable-element-scrollx').DataTable({
	  "paging": true,
	  "lengthChange": false,
	  "searching": true,
	  "ordering": false,
	  "info": true,
	  "autoWidth": false,
	  "scrollX": true,
	});
	
	$('.alert:not(".show_always")').delay(5000).fadeOut('slow');
	
	$('.gallery_images').magnificPopup({
	  delegate: 'a', 
	  type: 'image',
	  gallery:{
		enabled:true
	  }
	});
	
	if($('.show_hide_setting_elem').length)
	{
		$('.show_hide_setting_elem').each(function() {
			var thiss = $(this);
			var elem_name = thiss.attr('name');
			var show_hide_elem = thiss.attr('data-elem');
			$('.'+show_hide_elem).hide();
			var target = $("input[name='"+elem_name+"']:checked").attr('data-target');
			$('.'+target).show();
		});
		
		$('.show_hide_setting_elem').on('change',function() {
			var thiss = $(this);
			var elem_name = thiss.attr('name');
			var show_hide_elem = thiss.attr('data-elem');
			$('.'+show_hide_elem).hide();
			var target = $("input[name='"+elem_name+"']:checked").attr('data-target');
			$('.'+target).show();
		});
	}
	
	$('.sticky_sidebar').stickySidebar({

	  headerSelector: 'header',
	  navSelector : '.content-header',
	  contentSelector: '.content', 
	  footerSelector: 'footer',
	  //sidebarTopMargin: 20, 
	  footerThreshold: 40 
	});
	
	$('#openstreetmap').on('input', function(e) {
	  $(this).val(function(i, v) {
		return v.replace(/&amp;/g, '&');
	  });
	});
	
	
	let map;
	function initialize(def_lat , def_lng) { 
		map = new google.maps.Map(document.getElementById("map"), { 
          center: { lat: def_lat, lng: def_lng },
          zoom: 8,
        });
		
		var marker = new google.maps.Marker({
			position: { lat: def_lat, lng: def_lng },
			/*title:"Hello World!",*/
			draggable: true,
		});
		
		google.maps.event.addListener(marker, 'dragend', function (event) {
			map.setCenter(marker.position);
			$('#google_map_center_latitude').val(this.getPosition().lat());
			$('#google_map_center_longitude').val(this.getPosition().lng());
		});
		
		google.maps.event.addListener(map, 'click', function(event) {
		   marker.setPosition(event.latLng);
		});
		
		// To add the marker to the map, call setMap();
		marker.setMap(map);
	}
	
	var google_map_api_key = '';
	var google_map_center_latitude = -34.397;
	var google_map_center_longitude = 150.644;
	
	$('.popup-player').magnificPopup({
		
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: true,
		fixedContentPos: true,
		midClick: true,
		closeBtnInside: true,
		closeOnBgClick:true,
		overflowY:'hidden',
		callbacks: {
			 elementParse: function(item) {
				google_map_api_key = $('#google_map_js_api_key').val();
				if($('#google_map_center_latitude').val() != '')
					google_map_center_latitude = $('#google_map_center_latitude').val();
				if($('#google_map_center_longitude').val() != '')
					google_map_center_longitude = $('#google_map_center_longitude').val();
				
			  },
			open: function () 
			{
				
				$('.popup-player').tooltip('hide');
				if(google_map_api_key != '')
				{
					$.getScript('https://maps.googleapis.com/maps/api/js?key='+google_map_api_key, function() {
						initialize(parseFloat(google_map_center_latitude),parseFloat(google_map_center_longitude));
					});
				}
				else
				{
					$.magnificPopup.close();
					alert('Please enter API Key first');
				}
			},
			close: function () {
				$('.popup-player').tooltip();
				$('#map').html('');
			}
		}
	});
	
	
	function initialize_property(def_lat , def_lng) { 
		map = new google.maps.Map(document.getElementById("map"), { 
          center: { lat: def_lat, lng: def_lng },
          zoom: 8,
        });
		
		var marker = new google.maps.Marker({
			position: { lat: def_lat, lng: def_lng },
			/*title:"Hello World!",*/
			draggable: true,
		});
		
		google.maps.event.addListener(marker, 'dragend', function (event) {
			map.setCenter(marker.position);
			$('#property_latitude').val(this.getPosition().lat());
			$('#property_longitude').val(this.getPosition().lng());
		});
		
		google.maps.event.addListener(map, 'click', function(event) {
		   marker.setPosition(event.latLng);
		});
		
		// To add the marker to the map, call setMap();
		marker.setMap(map);
	}
	
	$('.popup-property').magnificPopup({
		
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: true,
		fixedContentPos: true,
		midClick: true,
		closeBtnInside: true,
		closeOnBgClick:true,
		overflowY:'hidden',
		callbacks: {
			 elementParse: function(item) {
				
			  },
			open: function () 
			{
				 var mp = $.magnificPopup.instance,
				 t = $(mp.currItem.el[0]);
				 var api_key = t.attr('data-api_key');
				 var map_lat = t.attr('data-map_lat');
				 var map_lng = t.attr('data-map_lng');
				$('.popup-property').tooltip('hide');
				
				if($('#property_latitude').val() != '')
					map_lat = $('#property_latitude').val();
				if($('#property_longitude').val() != '')
					map_lng = $('#property_longitude').val();	
				
				if(map_lat == '')
					map_lat = -34.397;
				if(map_lng == '')
					map_lng = 150.644;
				
				$.getScript('https://maps.googleapis.com/maps/api/js?key='+api_key, function() {
					initialize_property(parseFloat(map_lat),parseFloat(map_lng));
				});
				
			},
			close: function () {
				$('.popup-property').tooltip();
				$('#map').html('');
			}
		}
	});
	
});
