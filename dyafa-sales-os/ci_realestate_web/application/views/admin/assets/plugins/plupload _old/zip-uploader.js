var i = 0;
var n =1;

function init_pl_zip_uploader()
{
	/*
	plupload.addFileFilter('max_img_resolution', function(maxRes, file, cb) {
	  var self = this, img = new o.Image();
	 
	  function finalize(result) {
		// cleanup
		img.destroy();
		img = null;
	 
		// if rule has been violated in one way or another, trigger an error
		if (!result) {
		  self.trigger('Error', {
			code : plupload.IMAGE_DIMENSIONS_ERROR,
			message : "Resolution exceeds the allowed limit of " + maxRes  + " pixels.",
			file : file
		  });
		   
		}
		cb(result);
	  }
	 
	  img.onload = function() {
			finalize(img.width * img.height < maxRes);
	  };
	 
	  img.onerror = function() {
		finalize(false);
	  };
	 
	  img.load(file.getSource());
	});
	*/
	
	/*
	plupload.addFileFilter('min_width', function(maxwidth, file, cb) {
		var self = this, img = new o.Image();

		function finalize(result) {
			// cleanup
			img.destroy();
			img = null;

		   // if rule has been violated in one way or another, trigger an error
			if (!result) {
				self.trigger('Error', {
					code : plupload.IMAGE_DIMENSIONS_ERROR,
					message : "Image width should be more than " + maxwidth  + " pixels.",
					file : file
				});
		 }
			cb(result);
		}
		img.onload = function() {
			// check if resolution cap is not exceeded
			finalize(img.width >= maxwidth);
		};
		img.onerror = function() {
			finalize(false);
		};
		img.load(file.getSource());
	});
	*/
	
	/*
	plupload.addFileFilter('img_max_file_size', function(maxSize, file, cb) {
	  var undef;
	  if (file.size !== undef && maxSize && file.size > maxSize) {
		this.trigger('Error', {
		  code : plupload.FILE_SIZE_ERROR,
		  message : plupload.translate('File size error.'),
		  file : file
		});
		cb(false);
		alert('File size too big.');
	  } else {
		cb(true);
	  }
	});
	*/
		if($(".pl_zip_container").length)
		{
			$(".pl_zip_container").each(function() {
				
				var button = $(this).find('.custom-file-upload').attr('id');
				
				var $filelist_DIV = $(this);
				var image_type = $filelist_DIV.find('.custom-file-upload').attr('data-type');
				
				var callback = 'upload_zip';
				var uploader = new plupload.Uploader({
					runtimes 			: 'html5,flash,silverlight,html4',
					browse_button 		: button, 
					url 				: base_url+'admin_ajax',
					chunk_size			: '1mb',
					flash_swf_url 		: 'Moxie.swf',
					silverlight_xap_url : 'Moxie.xap',
					drop_element: button,
					multi_selection: false,
					multipart: true,
					multipart_params : {
						image_type : image_type,
						callback : callback
					},
					filters : {
						//min_width: 1000,
						max_img_resolution: 36000000,
						/*img_max_file_size : '10485760',*/ /*10 mb*/
						mime_types: [
							{title : "Zip Files", extensions : "zip"},
						]
					},
					
					init: {
						PostInit: function() {
							$filelist_DIV.find('.pl_file_progress').val('0');
						},

						FilesAdded: function(up, files) {
							var files_added = up.files.length;
							files.reverse();
							uploader.start();
							$('.full_sreeen_overlay').show();
							$filelist_DIV.find('.pl_file_progress').show();
						},
						
						FileUploaded: function(up, file, info){
							
							var obj_resp = $.parseJSON(info.response);
							if(obj_resp.type == 'success')
							{
								var file_thumb = base_url+obj_resp.thumb_img_url;
								$filelist_DIV.find('.pl_file_progress').hide();
								
								
								i = 0;
								
								var output = '<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">'
								+'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'
								+obj_resp.message+'</div>';
								$('.content-header').append(output);
								
							}
							else
							{
								var output = '<div class="alert alert-danger alert-dismissable" style="margin-bottom:0px; margin-top:10px;">'
								+'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'
								+obj_resp.message+'</div>';
								$('.content-header').append(output);
								
							}
						},

						UploadProgress: function(up, file) {
							
							$filelist_DIV.find('.pl_file_progress').val(file.percent);
							
						},
						UploadComplete: function (up, files) {
							$filelist_DIV.find('.pl_file_progress').hide().val('0');
							$('.full_sreeen_overlay').hide();
							i = 0;
						},
						Error: function(up, err) {
							
						}
					}
				});
				
				/*
				uploader.bind('BeforeUpload', function (up, file) {
					if('thumb' in file)
					{
						if (i == 1) {
							up.settings.url = base_url+'admin/ajax_images/upload_zip_callback_func';
						}
					}
					else
					{
						up.settings.url = base_url+'admin/ajax_images/upload_zip_callback_func';
					}
				});
				
				
				uploader.bind('FileUploaded', function(up, file) {
					if(!('thumb' in file)) 
					{
						file.thumb = true;
						file.loaded = 0;
						file.percent = 0;
						file.status = plupload.QUEUED;
						up.trigger("QueueChanged");
						up.refresh();
					}
					else 
					{
						if (i < 1) {
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
				*/
				
				uploader.init();
			});
			
			
		}
	}

$(function () {
	"use strict";
	
	init_pl_zip_uploader(); 
	
});
