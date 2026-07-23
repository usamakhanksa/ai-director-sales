<?php 
/*
Plugin Name: Documents for Properties
Plugin URI:http://www.mindlogixtech.com
Version: 1.0
Description: Extend Properties with Documents
Author: Mindlogixtech
Author URI: http://www.mindlogixtech.com
*/

define("DOCUMENTS_DIR", "property_documents");
define("DOCUMENTS_ASSETS_PATH", "application/modules/".DOCUMENTS_DIR."/assets/");
define("DOCUMENTS_PLUGIN_NAME", "Property_documents");
define("DOCUMENTS_FILE_EXT_STRING", ""); 
define("DOCUMENTS_FILE_SIZE_LIMIT", "");

add_action('cms_init', 'document_init');

function document_init()
{

    $CI = &get_instance();
    $CI->load->config(DOCUMENTS_DIR . "/property_documents_config");

    
	$CI->admin_ajax_items["upload_document"] = array(
        "callback_id" => "upload_document",
        "callback_path" => DOCUMENTS_DIR . "/property_documents_lib/upload_document_callback_func"   ); 
	
	/* upload_documents */

	$CI->admin_ajax_items["delete_documents"] = array(
        "callback_id" => "delete_documents",
        "callback_path" => DOCUMENTS_DIR . "/property_documents_lib/delete_documents_callback_func"   ); 
	
	
	$CI->admin_ajax_items["add_doc_from_document_library_ajax"] = array(
        "callback_id" => "add_doc_from_document_library_ajax",
        "callback_path" => DOCUMENTS_DIR . "/property_documents_lib/add_doc_from_document_library_ajax_callback_func"   ); 

	//$CI->load->library(DOCUMENTS_DIR . '/Property_documents_lib');
    //$CI->property_documents_lib->load_property_edit_scripts();
}





function property_documents_before_edit_content()
{

	$CI = &get_instance();
	
	ob_start();
	/*$CI->load->view("table_booking/admin/includes/table_booking_custom_shop_settings", $data);  /*, $data);*/
	
	$document_file_type = $CI->global_lib->get_option('document_file_type');
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
	$CI->DOCUMENTS_FILE_EXT_STRING  = $document_file_ext_string = implode(',',$document_file_ext_array);
	$document_file_size = $CI->global_lib->get_option('document_file_size');
	if(empty($document_file_size) || !isset($document_file_size))
	{
		$document_file_size = 2;
	}
	$CI->DOCUMENTS_FILE_SIZE_LIMIT  = $file_size_limit = $document_file_size*pow(1024,2);
	$file_accept_string = implode(', ',$file_accept_array);
	
	?>
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
				var callback = 'upload_document';
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
	}); 
   
	function progress(e){
		
		if(e.lengthComputable){
		   $('#'+id+'_progress').show();
			var percentComplete = (e.loaded / e.total) * 100;
			$('#'+id+'_progress').attr({value:percentComplete});
		}
		
		
	}
	
</script>
	
	<?php
	
	
	echo $meta_settings = ob_get_clean();
}
add_action('admin_property_before_edit_content', 'property_documents_before_edit_content', 10, 0);


function load_custom_metaboxes_callback($cpt_type = null,$args = array())
{
	$CI = &get_instance();
	if($cpt_type == 'property')
	{
		extract($args);
		$data = array("CI" =>$CI);
		$data['document_types'] = $CI->Common_model->commonQuery("SELECT * FROM `property_doc_types` as prop order by prop.pdt_order ASC, prop.pdt_id DESC");
		$data['myHelpers'] = $CI;
		if(!isset($p_id))
			$p_id = 0;
		$data['p_id'] = $p_id;
		ob_start();
		$CI->load->view("property_documents/admin/property_documents_metabox" , $data);
		$doc_type_metabox = ob_get_contents();
		ob_end_clean();
		echo $doc_type_metabox;
	}
}
add_action('load_custom_metaboxes', 'load_custom_metaboxes_callback', 10, 0);

function admin_save_property_document_meta_callback($post_args)
{
    $CI =  &get_instance();

    extract($post_args);
    $CI->load->library('Property_lib');
	if (isset($document_meta) && !empty($document_meta)) {
		if (!isset($property_meta))
			$property_meta = array();

		foreach ($document_meta as $dmk => $dmv) {
			$data_exp = explode(',', $dmv);
			$data_exp_array = array();
			foreach ($data_exp as $k => $v) {
				$data_exp_array[] = $CI->global_lib->DecryptClientID($v);
			}
			$meta_val = implode(',', $data_exp_array);
			$CI->property_lib->update_property_meta($p_id,$dmk,$meta_val);
		}
	}


}
add_action('admin_save_property_document_meta', 'admin_save_property_document_meta_callback', 10, 0);