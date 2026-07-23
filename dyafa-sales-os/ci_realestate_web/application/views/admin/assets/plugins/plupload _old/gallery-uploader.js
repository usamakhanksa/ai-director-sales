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
	
	
	
	
	
	var $filelist_DIV = $('#gallery-upload-container');
	var callback = 'upload_gallery_images';
	var uploader = new plupload.Uploader({
		runtimes 			: 'html5,flash,silverlight,html4',
		browse_button 		: 'gallery-drop-target', 
		container			: 'gallery_plupload_container', 
		url 				: base_url+'admin_ajax',
		chunk_size			: '1mb',
		flash_swf_url 		: 'Moxie.swf',
		silverlight_xap_url : 'Moxie.xap',
		drop_element: 'gallery-drop-target',
		
		multi_selection: true,
		multipart: true,
		multipart_params : {
			wedding_id : $(".wedding_id").val(),
			callback:callback
		},
		filters : {
			max_file_size : '41943040',
			mime_types: [
				{title : "Image files", extensions : "jpg,gif,png,jpeg"},
			]
		},
		
		init: {
			
			
			PostInit: function() {
				
				
				var target = $("gallery-drop-target");
          
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
				
				if(obj_resp.enable_watermark == 'Y' && obj_resp.upload_status == 'Y')
				{
					if(obj_resp.watermark_type == 'text')
					{
						var ul = watermark.text.upperLeft;
						watermark([base_url+obj_resp.org_file_path])
						.image(ul(obj_resp.watermark_text,obj_resp.watermark_text_font_size+'px Josefin Slab',obj_resp.watermark_text_color))
						.then(function (url) {
							$.ajax({
								method: 'POST',
								url: base_url+'admin_ajax?callback=saveDataUrlasImage',
								data: {
								dataurl: url,
								org_path :obj_resp.org_file_path
								},
								success : function(){

								}
							});
						});
					}
					else if(obj_resp.watermark_type == 'image')
					{
						
						watermark([base_url+obj_resp.org_file_path, base_url+obj_resp.watermark_path])
						.dataUrl(watermark.image.center())
						.then(function (url) {
							$.ajax({
								method: 'POST',
								url: base_url+'admin_ajax?callback=saveDataUrlasImage',
								data: {
								dataurl: url,
								org_path :obj_resp.org_file_path
								},
								success : function(){

								}
							});
						});
						
						
					}
				}

				if(obj_resp.type == 'success')
				{
					var file_thumb = base_url+obj_resp.thumb_img_url;
					var output = '<img src="'+file_thumb+'" width="100%">';
						output += '<a href="#" class="remove_album_image hide" id="image_'+obj_resp.img_id+'" data-type="photo_gallery" data-name="image_'+obj_resp.img_id+'"><i class="fa fa-remove"></i></a>';
						output += '<span class="select-check hide"><i class="fa fa-check"></i></span>';
						output += '<input type="hidden" name="" id="image_'+obj_resp.img_id+'_hidden" value="'+obj_resp.img_name+'">';
					
					$filelist_DIV.find('#img_' + file.id).find('.media_images_inner').html(output);
					$filelist_DIV.find('#img_' + file.id).find('.media_images_inner').attr('title',obj_resp.img_name);
					i = 1;
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
			'class'	: 'col-md-2 album_images',
			'html'	: inner_html,
			
		}).prependTo($filelist_DIV);
	}
	
});
