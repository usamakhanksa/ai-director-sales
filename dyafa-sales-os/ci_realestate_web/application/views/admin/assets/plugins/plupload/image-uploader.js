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
	
		if($(".pl_image_container").length)
		{
			$(".pl_image_container").each(function() {
				
				var button = $(this).find('.custom-file-upload').attr('id');
				var $filelist_DIV = $(this);
				var image_type = $filelist_DIV.find('.custom-file-upload').attr('data-type');
				
				var callback = 'upload_image';
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
						max_img_resolution: 36000000,
						img_max_file_size : '41943040',
						mime_types: [
							{title : "Image files", extensions : "jpg,gif,png,jpeg"},
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
								
								$filelist_DIV.find('.custom-file-upload').hide();
								$filelist_DIV.find('.pl_file_progress').hide();
								$filelist_DIV.find('.pl_file_hidden').val(obj_resp.img_name);
								$filelist_DIV.find('.pl_file_link').attr('href',file_thumb).attr('download',obj_resp.img_name);
								$filelist_DIV.find('.pl_file_link img').attr('src',file_thumb);
								$filelist_DIV.find('.pl_file_link').show();
								$filelist_DIV.find('.pl_file_remove_img').show();
								
								i = 1;
							}
							
						},

						UploadProgress: function(up, file) {
							
							$filelist_DIV.find('.pl_file_progress').val(file.percent);
							
						},
						UploadComplete: function (up, files) {
							$filelist_DIV.find('.pl_file_progress').hide().val('0');
							$('.full_sreeen_overlay').hide();
							i = 1;
						},
						Error: function(up, err) {
						}
					}
				});
				
				
				uploader.bind('BeforeUpload', function (up, file) {
					if(image_type == 'banner' || image_type == 'site_slider' || image_type == 'logo' || image_type == 'fevicon')
					{
						if(image_type == 'banner' || image_type == 'site_slider')
						{
							up.settings.url = base_url+'admin_ajax?callback='+callback,
							up.settings.resize = {width : 1600, height : 800, quality : 85,crop : true};
						}
						else
						{
							up.settings.url = base_url+'admin_ajax?callback='+callback,
							up.settings.resize = {quality : 85};
						}
					}
					else
					{
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
					}
				});
				
				uploader.bind('FileUploaded', function(up, file) {
					if(image_type != 'banner' &&  image_type != 'site_slider' &&  image_type != 'logo' && image_type != 'fevicon')
					{
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
					}
				});
				
				uploader.init();
			});
			
			$(document).delegate('.pl_file_remove_img','click',function() {
				var thiss = $(this);
				var img_name = thiss.parents('.pl_image_container').find('.pl_file_hidden').val();
				var img_type = thiss.parents('.pl_image_container').find('.custom-file-upload').attr('data-type');
				var element_id = thiss.parents('.pl_image_container').find('.custom-file-upload').attr('data-element_id');
				var parentDiv = thiss.parents('.pl_image_container');
				
				var element_column = '';
				var ec_val = thiss.parents('.pl_image_container').find('.custom-file-upload').attr('data-element_column');
				if (typeof ec_val !== typeof undefined && ec_val !== false) {
					element_column = ec_val;
				}
				
				var strconfirm = confirm("Are you sure you want to delete?");
				if (strconfirm == true)
				{
					$('.full_sreeen_overlay').show();
					var callback = 'delete_image';
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
						data: {img_name : img_name,img_type : img_type,element_id:element_id,element_column:element_column, callback : callback},
						cache: false
					});
				}
				return false;
			});
			
		}
});
