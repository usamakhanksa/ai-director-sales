<?php $CI = &get_instance(); ?>
<script>
var id;
 $(document).ready(function () { 
		
		$('.document_uploader').on('change',function(){
			$('.full_sreeen_overlay').show();
			id = $(this).attr('id');
			var thiss = $(this);
			var image_type = $(this).attr('data-type');
			var data = new FormData();
			var unsupported_file = 0;
			var invalid_file = 0;
			var valid_file = 0;
			var files = $('#'+id).prop('files');
			
			for (var i = 0; i < files.length; i++) {
				var file = files[i];
				var ext = file.name.split('.').pop().toLowerCase();
				
				var file_size = file.size;
				
				console.log(<?php echo $CI->dfes; ?>);
				console.log(<?php echo $CI->DOCUMENTS_FILE_SIZE_LIMIT; ?>);
				
				<?php 
					if(!empty($CI->DOCUMENTS_FILE_EXT_STRING)) { ?>
					if(jQuery.inArray(ext, [<?php echo $CI->DOCUMENTS_FILE_EXT_STRING; ?>]) == -1) 
					{
						unsupported_file++;
					}
					else if(file_size > <?php echo $CI->DOCUMENTS_FILE_SIZE_LIMIT; ?> )
					{
						invalid_file++;
					}
					else
					{
						valid_file++;
						data.append('mFile[]', file, file.name);
					}
				<?php }else{ ?>
					if(file_size > <?php echo $CI->DOCUMENTS_FILE_SIZE_LIMIT; ?> )
					{
						invalid_file++;
					}
					else
					{
						valid_file++;
						data.append('mFile[]', file, file.name);
					}
				<?php } ?>
			}
			if(valid_file > 0)
			{
				$('#'+id+'_progress').show();
				data.append('user_type',image_type);
				$.ajax({
					url: '<?php echo site_url();?>/documents/upload_documents_callback_func',
					type: 'POST',
					data: data,
					cache: false,
					enctype: 'multipart/form-data',
					contentType: false,
					processData: false,
					xhr: function() {
						var myXhr = $.ajaxSettings.xhr();
						if(myXhr.upload){
							myXhr.upload.addEventListener('progress',progress, false);
						}
						return myXhr;
						
						
					},
					success: function (res) {
						$('.content-header .alert').remove();
						$('#'+id+'_progress').hide();
						res.invalid_file += invalid_file;
						if(res.invalid_file > 0)
						{
							var msg = '<div class="alert alert-danger alert-dismissable">'+
								'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
								res.invalid_file+' File Failed to Upload Due File Size Exceed to Uploaded Size Limit.'+
							'</div>';
							thiss.parents('.document-block').find('.box-body').prepend(msg);
						}
						if(res.upload_failed_file > 0)
						{
							var msg = '<div class="alert alert-danger alert-dismissable">'+
								'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
								res.upload_failed_file+' File Failed to Upload Due to an Error Occured While Uploading.'+
							'</div>';
							thiss.parents('.document-block').find('.box-body').prepend(msg);
						}
						res.unsupported_file += unsupported_file;
						if(res.unsupported_file > 0)
						{
							var msg = '<div class="alert alert-warning alert-dismissable">'+
								'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
								res.unsupported_file+' File Failed to Upload Due to Unsupported File Formats.'+
							'</div>';
							thiss.parents('.document-block').find('.box-body').prepend(msg);
						}
						if(res.valid_file > 0)
						{
							$.each(res.uploaded_image_array, function(k, v) {
								var output_string = '<div class="col-md-3 document_images">'+
								'<div class="document_images_inner" data-toggle="tooltip" title="" data-original-title="'+v.img_name+'">'+
									'<img src="'+v.thumb_url+'" width="100%" data-img_id="'+v.enc_att_id+'">'+
									'<a href="#" class="select-check remove_document_from_list" id="image_'+v.att_id+'" data-type="documents"'+ 
									'data-file_type="'+v.type+'"'+
									'data-att_id="'+v.att_id+'" data-name="image_'+v.att_id+'"><i class="fa fa-remove"></i></a>'+
									'<input type="hidden" name="" id="image_'+v.att_id+'_hidden" value="'+v.img_name+'">'+
								'</div>'+
								'</div>';
								thiss.parents('.document-block').find('.product-document-container').prepend(output_string);
								
								var old_img_data = $('#'+id+'_hidden').val();
								var new_img_data = v.enc_att_id;
								if(old_img_data != "")
								{
									new_img_data = old_img_data+','+v.enc_att_id;
								}
								$('#'+id+'_hidden').val(new_img_data);
							});
							var msg = '<div class="alert alert-success alert-dismissable">'+
								'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
								res.valid_file+' File Uploaded Successfully.'+
							'</div>';
							thiss.parents('.document-block').find('.box-body').prepend(msg);
						}
						
						$('.alert').delay(10000).fadeOut('slow');
						$('.full_sreeen_overlay').hide();
					},
					error: function(data){
						console.log("error");
						console.log(data);
					},
					
				});
			}
			else if(invalid_file > 0 || unsupported_file > 0)
			{
				if(invalid_file > 0)
				{
					var msg = '<div class="alert alert-danger alert-dismissable">'+
						'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
						invalid_file+' File Failed to Upload Due File Size Exceed to Uploaded Size Limit.'+
					'</div>';
					thiss.parents('.document-block').find('.box-body').prepend(msg);
				}
				if(unsupported_file > 0)
				{
					var msg = '<div class="alert alert-warning alert-dismissable">'+
						'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
						unsupported_file+' File Failed to Upload Due to Unsupported File Formats.'+
					'</div>';
					thiss.parents('.document-block').find('.box-body').prepend(msg);
				}
				
			}
			$('.alert').delay(10000).fadeOut('slow');
			$('.full_sreeen_overlay').hide();
		 });
		
		$('.add_property_form').submit(function() {
			$('.alert').hide();
			var has_error = false;
			var jump_to = '';
			if($("input[name^='document_meta']").length)
			{
				$("input[name^='document_meta']").each(function() {
					if($(this).attr('required') && $(this).val() == '')
					{
						has_error = true;
						var dm_id = $(this).parents('.box').attr('id')
						if(jump_to == '')
							jump_to = dm_id;
						$(this).parents('.box').find('.alert').show();
					}
				});
			}
			if(has_error)
			{
				$('html, body').animate({
					scrollTop: $("#"+jump_to).offset().top - 60
				}, 500);
				$('.alert').delay(5000).fadeOut('slow');
				return false;
			}
		});
	}); 
   
	function progress(e){
		
		if(e.lengthComputable){
		   $('#'+id+'_progress').show();
			var percentComplete = (e.loaded / e.total) * 100;
			$('#'+id+'_progress').attr({value:percentComplete});
		}
		
		
	}
	
</script>