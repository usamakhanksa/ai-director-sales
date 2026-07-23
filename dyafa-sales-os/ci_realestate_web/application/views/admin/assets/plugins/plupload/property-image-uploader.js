var i = 1;
var n =1;
$(function () {
	"use strict";
	
	plupload.addFileFilter('img_max_file_size', function(maxSize, file, cb) {
	  var undef;
	 
	  if (file.size !== undef && maxSize && file.size > maxSize) {
		this.trigger('Error', {
		  code : plupload.FILE_SIZE_ERROR,
		  message : plupload.translate('File size error.'),
		  file : file
		});
		cb(false);
	  } else {
		cb(true);
	  }
	});
	
		if($(".property_pl_image_container").length)
		{
			$(".property_pl_image_container").each(function() {
				
				var button = $(this).find('.custom-file-upload').attr('id');
				var $filelist_DIV = $(this);
				var image_type = $filelist_DIV.find('.custom-file-upload').attr('data-type');
				
				var callback = 'upload_property_images';
				var uploader = new plupload.Uploader({
					runtimes 			: 'html5,flash,silverlight,html4',
					browse_button 		: button, 
					url 				: base_url+'admin_ajax',
					chunk_size			: '1mb',
					flash_swf_url 		: 'Moxie.swf',
					silverlight_xap_url : 'Moxie.xap',
					drop_element: button,
					multi_selection: true,
					multipart: true,
					multipart_params : {
						image_type : image_type,
						callback : callback
					},
					filters : {
						max_img_resolution: 36000000,
						img_max_file_size : '41943040',
						mime_types: [
							{title : "Image files", extensions : "jpg,gif,png,jpeg"},
						]
					},
					
					init: {
						PostInit: function() {
							var target = $("document-drop-target");
          
							  target.ondragover = function(event) {
								event.dataTransfer.dropEffect = "copy";
							  };
							  
							  target.ondragenter = function() {
								this.className = "dragover";
							  };
							  
							  target.ondragleave = function() {
								this.className = "";
							  };
							  
							  target.ondrop = function() {
								this.className = "";
							  };
						},

						FilesAdded: function(up, files) {
							var files_added = up.files.length;
							files.reverse();
							plupload.each(files, function (file) {
								add_thumb_box(file, $('.product-gallary-container'), up);
							});
							uploader.start();
						},
						
						FileUploaded: function(up, file, info){
							
							var obj_resp = $.parseJSON(info.response);
							if(obj_resp.type == 'success')
							{
								var file_thumb = obj_resp.thumb_img_url;
								var full_file_url = base_url+obj_resp.thumb_img_url;
								var output = '<span class="remove-product-btn"><i class="fa fa-remove"></i></span><img data-img_id="'+obj_resp.img_id+'" src="'+full_file_url+'" class="lazy-img-elem">';
									
								$('.product-gallary-container').find('#img_' + file.id).find('.media_images_inner').html(output);
								/*$filelist_DIV.find('#img_' + file.id).find('.media_images_inner').addClass('lazy-load-processing');*/
								$('.product-gallary-container').find('#img_' + file.id).find('.media_images_inner').attr('title',obj_resp.img_name);
								i = 1;
								
								var old_img_data = $('input[type="hidden"][name="addedImgFromMediaLibrary"]').val();
								var new_img_data = obj_resp.img_id;
								if(old_img_data != "")
								{
									new_img_data = old_img_data+','+obj_resp.img_id;
								}
								$('input[type="hidden"][name="addedImgFromMediaLibrary"]').val(new_img_data);
								
							}
							
						},

						UploadProgress: function(up, file) {
							
							$('.product-gallary-container').find('#img_' + file.id).find('.progress_bar_runner').html(file.percent + '%');
							$('.product-gallary-container').find('#img_' + file.id).find('.progress_bar_runner').css({'display':'block', 'width':file.percent + '%'});
							
						},
						UploadComplete: function (up, files) {
							jQuery('.srr_plupload_container').removeClass('disable-div');
							i = 1;
						},
						Error: function(up, err) {
							
						}
					}
				});
				
				uploader.bind('Init', function(up, params) {
		
					if (uploader.features.dragdrop) {
					  var target = $("gallery-drop-target");
					  
					  target.ondragover = function(event) {
						event.dataTransfer.dropEffect = "copy";
						/*alert('drag over');*/
					  };
					  
					  target.ondragenter = function() {
						this.className = "dragover";
						/*alert('drag enter');*/
					  };
					  
					  target.ondragleave = function() {
						this.className = "";
						/*alert('drag leave');*/
					  };
					  
					  target.ondrop = function() {
						this.className = "";
						/*alert('on drop');*/
					  };
					}
				  });
				
				uploader.bind('BeforeUpload', function (up, file) {
					
					if('thumb' in file)
					{
						if (i == 1) {
							up.settings.url = base_url+'admin_ajax?diretorio=thumbs&callback='+callback,
							up.settings.resize = {width : 300, height : 300, quality : 85};
						}
						else
						{
							up.settings.url = base_url+'admin_ajax?diretorio=medium&callback='+callback,
							up.settings.resize = {width : 500, height : 300, quality : 85};
						}
					}
					else
					{
						up.settings.url = base_url+'admin_ajax?callback='+callback,
						up.settings.resize = {quality : 50};
					}
					
				});
				
				
				
				uploader.bind('FileUploaded', function(up, file) {
					
					if(!('thumb' in file)) {
						file.thumb = true;
						file.loaded = 0;
						file.percent = 0;
						file.status = plupload.QUEUED;
						up.trigger("QueueChanged");
						up.refresh();
					}
					else 
					{
						if (i < 2) {
							i++;
							file.medium = true;
							file.loaded = 0;
							file.percent = 0;
							file.status = plupload.QUEUED;
							up.trigger("QueueChanged");
							up.refresh();
						}
					}
					
				});
				
				uploader.init();
				
				function add_thumb_box(file, $filelist_DIV) {
					jQuery('.srr_plupload_container').addClass('disable-div');											
					var inner_html 	= '<div class="media_images_inner" data-container="body" data-toggle="tooltip" title="">';
					inner_html		+= '<div class="progress_bar progress progress-striped"><span class="progress_bar_runner progress-bar progress-bar-success"></span></div>';
					inner_html		+= '</div>';
					  
					jQuery( '<div />', {
						'id'	: 'img_'+file.id,
						'class'	: 'media-img-block ui-sortable-handle',
						'html'	: inner_html,
						
					}).prependTo($filelist_DIV);
				}
			});
			
			$(document).delegate('.ppl_file_remove_img','click',function() {
				var thiss = $(this);
				var img_name = thiss.parents('.property_pl_image_container').find('.pl_file_hidden').val();
				var img_type = thiss.parents('.property_pl_image_container').find('.custom-file-upload').attr('data-type');
				var element_id = thiss.parents('.property_pl_image_container').find('.custom-file-upload').attr('data-element_id');
				var parentDiv = thiss.parents('.property_pl_image_container');
				
				var element_column = '';
				var ec_val = thiss.parents('.property_pl_image_container').find('.custom-file-upload').attr('data-element_column');
				if (typeof ec_val !== typeof undefined && ec_val !== false) {
					element_column = ec_val;
				}
				
				var strconfirm = confirm("Are you sure you want to delete?");
				if (strconfirm == true)
				{
					$('.full_sreeen_overlay').show();
					var callback = 'delete_property_image';
					$.ajax({
						url: base_url + 'admin_ajax',
						type: 'POST',
						success: function (res) {
							if(res == 'success')
							{
								parentDiv.find('.pl_file_link').removeAttr('href').removeAttr('download');
								parentDiv.find('.pl_file_link img').removeAttr('src');
								parentDiv.find('.pl_file_link').hide();
								parentDiv.find('.custom-file-upload').show();
								parentDiv.find('.pl_file_hidden').val('');
								thiss.hide();
							}
							$('.full_sreeen_overlay').hide();
						},
						data: {img_name : img_name,img_type : img_type,element_id:element_id,element_column:element_column,callback:callback},
						cache: false
					});
				}
				return false;
			});
			
		}
});
