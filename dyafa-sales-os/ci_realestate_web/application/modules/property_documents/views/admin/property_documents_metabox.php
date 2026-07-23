<?php
if(isset($document_types) && $document_types->num_rows() > 0)
{
	$document_file_type = $myHelpers->global_lib->get_option('document_file_type');
	$file_accept_array = array();
	$document_file_ext_array = array();

	$file_accept_types = array('jpeg' => 'image/jpeg',
							   'jpg' => 'image/jpeg',
							   'png' => 'image/png',
							   'gif' => 'image/gif',
							   'pdf' => 'application/pdf',
							   'doc' => 'application/msword',
							   'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
							   'txt' => 'text/plain',
							   'xls' => 'application/vnd.ms-excel',
							   'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
							   'ppt' => 'application/vnd.ms-powerpoint',
							   'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
							);

	if(isset($document_file_type) && !empty($document_file_type))
	{
		$document_file_type_array = json_decode($document_file_type,true);
		
		if(count($document_file_type_array) > 0)
		{
			foreach($document_file_type_array as $k=>$v)
			{
				if(array_key_exists($v,$file_accept_types))
				{
					$file_accept_array[] = $file_accept_types[$v];
				}
				$f_exp = explode('~',$v);
				$document_file_ext_array[] = "'".$f_exp[0]."'";
			}
		}
	}
	$document_file_ext_string = implode(',',$document_file_ext_array);
	$document_file_size = $this->global_lib->get_option('document_file_size');
	if(empty($document_file_size) || !isset($document_file_size))
	{
		$document_file_size = 2;
	}
	$file_size_limit = $document_file_size*pow(1024,2);
	$file_accept_string = implode(', ',$file_accept_array);
	?>
	<script>
	var id;
	function progress(e){
		
		if(e.lengthComputable){
		   $('#'+id+'_progress').show();
			var percentComplete = (e.loaded / e.total) * 100;
			$('#'+id+'_progress').attr({value:percentComplete});
		}
		
		
	}
	
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
					<?php 
						if(!empty($document_file_ext_string)) { ?>
						if(jQuery.inArray(ext, [<?php echo $document_file_ext_string; ?>]) == -1) 
						{
							unsupported_file++;
						}
						else if(file_size > <?php echo $file_size_limit; ?> )
						{
							invalid_file++;
						}
						else
						{
							valid_file++;
							data.append('mFile[]', file, file.name);
						}
					<?php }else{ ?>
						if(file_size > <?php echo $file_size_limit; ?> )
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
					var callback = 'upload_documents';
					data.append('callback',callback);
					$.ajax({
						url: '<?php echo site_url();?>admin_ajax',
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
						url: base_url+'admin_ajax?callback=add_doc_from_document_library_ajax',
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
		}); 
	   
	
		
	</script>
	<?php
	$this->load->library('property_lib');
	foreach($document_types->result() as $doc_type_row)
	{
		$field_id = $doc_type_row->slug.'_field';

		$property_meta = $this->property_lib->get_property_meta($p_id,$doc_type_row->slug.'-ids');
		
?>
  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> document-block" id="<?php echo $doc_type_row->slug; ?>">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo ucfirst($doc_type_row->title).' - '.mlx_get_lang('Document Type'); ?> <?php if($doc_type_row->is_required == 'Y') { echo '<span class="required">*</span>'; } ?></h3>
	  <div class="box-tools pull-right">
		<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	  </div>
	</div>
	<div class="box-body">
		<div class="alert alert-danger alert-dismissable"  style="margin-bottom:10px; margin-top:0px; display:none;">
			<?php echo (!empty($doc_type_row->error_message)?$doc_type_row->error_message:'This field is required.'); ?>
		</div>
		<div class="form-group" align="center">
			
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				   <div class="property_document_pl_image_container">
						<label class="custom-file-upload" data-element_id="" data-type="documents" id="pl_file_uploader_<?php echo $field_id; ?>">
							<?php echo mlx_get_lang('Drop documents here'); ?>
							<br />
							<strong><?php echo mlx_get_lang('OR'); ?></strong>
							<br />
							<?php echo mlx_get_lang('Click here to select documents'); ?>
						</label>
						<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
						<a class="pl_file_link" href="" download="" style="display:none;">
							<img src="" style="width:50%;">
						</a>
						<a class="ppl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
						<input type="hidden" name="blog_image" value="" class="pl_file_hidden">
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<br>
					<span class="or"><?php echo mlx_get_lang('OR'); ?></span>
					<br>
					<br>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<a onclick="lazy_load_on_media_img()"href="#" class="custom-file-upload add_from_document_btn"><i class="fa fa-folder"></i>&nbsp;<?php echo mlx_get_lang('Add From Documents'); ?></a>
					<input type="hidden" id="<?php echo $field_id; ?>_hidden" name="document_meta[<?php echo $doc_type_row->slug; ?>-ids]" 
					value="<?php if(isset($property_meta) && !empty($property_meta) && $property_meta != 0)
					{
						$data_exp = explode(',',$property_meta);
						$data_imp = array();
						foreach($data_exp as $k=>$v)
						{
							if(!empty($v) && $v != '0')
								$data_imp[] = $myHelpers->global_lib->EncryptClientID($v);
						}
						echo implode(',',$data_imp);
					}
					?>" <?php if($doc_type_row->is_required == 'Y') { echo 'required'; } ?>>
				</div>
			</div>
			
		</div>
		
		<div class="product-document-container row">
		<?php 
			if(isset($property_meta) && !empty($property_meta) && $property_meta != 0)
			{
				$p_g_i = explode(',',$property_meta);
				
				if(count($p_g_i) > 0)
				{
					foreach($p_g_i as $key=>$val)
					{
						$img_id = $val;
						
						$query = "SELECT att.* FROM `attachments` as att
						WHERE att.att_type = 'document' and att_id = $img_id";
						$result = $myHelpers->Common_model->commonQuery($query);
						if($result->num_rows() > 0)
						{
							$img_row = $result->row();
							
							$explode = explode('.',$img_row->att_name);
							$extension = $explode[count($explode)-1];
							$actual_name = substr($img_row->att_name, 0, strrpos($img_row->att_name, "."));

							if($img_row->file_type == 'image')
							{
								$thumb_image_url = base_url().'../'.$img_row->att_path.$actual_name.'-thumbnail.'.$extension;
								if(file_exists('../'.$img_row->att_path.$actual_name.'-thumbnail.'.$extension))
								{
									$thumb_image_url = base_url().'../'.$img_row->att_path.$actual_name.'-thumbnail.'.$extension;
								}
								else
								{
									$thumb_image_url = base_url().'../'.$img_row->att_path.$img_row->att_name;
								}
								$origional_dowload_image_url = $origional_image_url = base_url().'../'.$img_row->att_path.$img_row->att_name;
								
							}
							else if($extension == 'doc' || $extension == 'docx' || $extension == 'xls' || $extension == 'xlsx')
							{
								if(file_exists('../themes/default/images/file_icons/'.$extension.'_file.png'))
								{
									$thumb_image_url = base_url().'../themes/default/images/file_icons/'.$extension.'_file.png';
								}
								else
								{
									$thumb_image_url = base_url().'../themes/default/images/file_icons/default_file.jpg';
								}
								$url_final = base_url().'../'.$img_row->att_path.$img_row->att_name;
								$origional_image_url = $url_final;
								$origional_dowload_image_url = base_url().'../'.$img_row->att_path.$img_row->att_name;
							}
							else
							{
								if(file_exists('../themes/default/images/file_icons/'.$extension.'_file.png'))
								{
									$thumb_image_url = base_url().'../themes/default/images/file_icons/'.$extension.'_file.png';
								}
								else
								{
									$thumb_image_url = base_url().'../themes/default/images/file_icons/default_file.jpg';
								}
								$origional_dowload_image_url = $origional_image_url = base_url().'../'.$img_row->att_path.$img_row->att_name;
							}
							
							
							echo "<div class='col-md-3 document_images '><div class='document_images_inner lazy-load-processing' data-toggle='tooltip' 
								data-original-title='".$img_row->att_name."'>
									<img class='lazy-img-elem' data-img_id='".$myHelpers->global_lib->EncryptClientId($img_id)."' 
									data-src='".$thumb_image_url."'>
									<a href='#' class='select-check remove_document_from_list' 
									data-att_id='".$myHelpers->global_lib->EncryptClientId($img_id)."' 
									><i class='fa fa-remove'></i></a></div></div>";
							
							
						}
					}
				}
			}
			?>		
		</div>
		
	</div>
	<?php if(isset($doc_type_row->description) && !empty($doc_type_row->description)){ ?>
		<div class="box-footer">
			<p class="help-block" style="margin:0px;"><strong><?php echo mlx_get_lang('Note'); ?></strong> : <?php echo $doc_type_row->description; ?></p>
		</div>
	<?php } ?>
  </div>
<?php }
} 
?>