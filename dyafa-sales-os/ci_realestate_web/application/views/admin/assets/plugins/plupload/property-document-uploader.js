var i = 1;
var n =1;
$(function () {
	"use strict";
	
	plupload.addFileFilter('max_file_size', function(maxSize, file, cb) {
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
	
	
	
	if($(".property_document_pl_image_container").length)
	{
		
		$(".property_document_pl_image_container").each(function() {
			
			var button = $(this).find('.custom-file-upload').attr('id');
			/*var $filelist_DIV = $(this);*/
			var $filelist_DIV = $(this).parents('.document-block').find('.product-document-container');
			var callback = 'upload_document';
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
					callback : callback
				},
				filters : {
					max_file_size : '41943040',
					mime_types: [
						{title : "Image files", extensions : "jpg,gif,png,jpeg"},
						{title : "application/pdf", extensions : "pdf"},
						{title : "application/msword", extensions : "doc"},
						{title : "application/vnd.openxmlformats-officedocument.wordprocessingml.document", extensions : "docx"},
						{title : "text/plain", extensions : "txt"},
						{title : "application/vnd.ms-excel", extensions : "xls"},
						{title : "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", extensions : "xlsx"},
						{title : "application/vnd.ms-powerpoint", extensions : "ppt"},
						{title : "application/vnd.openxmlformats-officedocument.presentationml.presentation", extensions : "pptx"},
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
							add_thumb_box(file, $filelist_DIV, up);
						});
						uploader.start();
					},
					
					FileUploaded: function(up, file, info){
						
						var obj_resp = $.parseJSON(info.response);
						if(obj_resp.type == 'success')
						{
							
							var file_thumb = obj_resp.thumb_img_url;
							var full_file_url = base_url+obj_resp.img_name;
							var output = '<img src="'+file_thumb+'" class="lazy-img-elem" data-img_id="'+obj_resp.end_img_id+'">';
								output += '<a href="#" class="select-check remove_document_from_list" id="image_'+obj_resp.img_id+'" data-type="documents" data-name="image_'+obj_resp.img_id+'" data-file_type="'+obj_resp.file_type+'" data-att_id="'+obj_resp.end_img_id+'"><i class="fa fa-remove"></i></a>';
								
								
							$filelist_DIV.find('#img_' + file.id).find('.document_images_inner').html(output);
							/*$filelist_DIV.find('#img_' + file.id).find('.document_images_inner').addClass('lazy-load-processing');*/
							$filelist_DIV.find('#img_' + file.id).find('.document_images_inner').attr('title',obj_resp.img_name);
							i = 1;
							
							var ele_id = $filelist_DIV.parents('.document-block').attr('id');
							var old_img_data = $filelist_DIV.parents('.document-block').find('#'+ele_id+'_field_hidden').val();
							var new_img_data = obj_resp.end_img_id;
							if(old_img_data != "")
							{
								new_img_data = old_img_data+','+obj_resp.end_img_id;
							}
							$filelist_DIV.parents('.document-block').find('#'+ele_id+'_field_hidden').val(new_img_data);
							
						}
						
					},

					UploadProgress: function(up, file) {
						
						$filelist_DIV.find('#img_' + file.id).find('.progress_bar_runner').html(file.percent + '%');
						$filelist_DIV.find('#img_' + file.id).find('.progress_bar_runner').css({'display':'block', 'width':file.percent + '%'});
						
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
				if(file.type.indexOf('image/') == -1)
				{
					up.settings.url = base_url+'admin_ajax?diretorio=doc&callback='+callback,
					up.settings.resize = {quality : 100};
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
				if(file.type.indexOf('image/') == -1)
				{
					
				}
				else
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
			
			function add_thumb_box(file, $filelist_DIV) {
				
				var inner_html 	= '<div class="document_images_inner" data-container="body" data-toggle="tooltip" title="">';
				inner_html		+= '<div class="progress_bar progress progress-striped"><span class="progress_bar_runner progress-bar progress-bar-success"></span></div>';
				inner_html		+= '</div>';
				  
				jQuery( '<div />', {
					'id'	: 'img_'+file.id,
					'class'	: 'col-md-3 document_images',
					'html'	: inner_html,
					
				}).prependTo($filelist_DIV);
			}
		});
	}
	
});
