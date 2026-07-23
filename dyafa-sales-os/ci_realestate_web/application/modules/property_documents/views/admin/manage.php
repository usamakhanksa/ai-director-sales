<?php 
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
?>
<script>
var id;
 $(document).ready(function () { 
		
		
		$(document).delegate('a.remove_document_btn','click',function() {
			var id = $(this).attr('data-name');
			var thiss = $(this);
			var img_name = $('#'+id+'_hidden').val();
			var user_type =  thiss.attr('data-type');
			var att_id =  thiss.attr('data-att_id');
			var file_type =  thiss.attr('data-file_type');
			var strconfirm = confirm("Are you sure you want to delete?");
			if (strconfirm == true)
			{
					$('.full_sreeen_overlay').show();
					var callback = 'delete_documents';
					$.ajax({
						url: base_url+'admin_ajax',
						type: 'POST',
						success: function (res) {
							if(res == 'success')
							{
								thiss.parent().parent().fadeOut().remove();
								
							}
							$('.full_sreeen_overlay').hide();
						},
						data: {img_name : img_name,user_type : user_type,att_id : att_id,file_type : file_type, callback : callback},
						cache: false
					});
				
			}
			return false;
		});
		
		
		
    }); 
   
    function progress(e){
        
		if(e.lengthComputable){
           $('#'+id+'_progress').show();
            //$('progress').attr({value:e.loaded,max:e.total});
			var percentComplete = (e.loaded / e.total) * 100;
			$('progress').attr({value:percentComplete});
        }
		
		
    }
	
</script>

<style>
.document_uploader{
    display: none !important;
}
.custom-file-upload {
    border: 1px solid #ccc;
    display: inline-block;
    padding: 6px 12px;
    cursor: pointer;
	font-weight: 500;
}
a.remove_document_btn {
	background-color: #f2f2f2;
    border: 1px solid #ddd;
    color: #999;
    padding: 0 3px;
    position: relative;
    
	top: -8px;
	left: -12px;
	border-radius: 10px;
	vertical-align: top;
	
	-webkit-transition:  0.4s ease-out;
    -moz-transition: 0.4s ease-out ;
    -o-transition: 0.4s ease-out ;
    transition: 0.4s ease-out;
	
	z-index:999;
}
a.remove_document_btn:hover {
    background-color: #ddd;
}
.form-group a img {
    border: 1px solid #f2f2f2;
    max-width: 150px;
    min-width: auto;
}

.or {
    background: #ddd none repeat scroll 0 0;
    border-radius: 40px;
    color: #ffffff;
    display: inline-block;
    font-family: "Roboto",sans-serif;
    font-size: 12px;
    height: 40px;
    line-height: 40px;
    text-align: center;
    width: 40px;
}
.add_from_document_btn,.add_from_document_btn:hover,.add_from_document_btn:focus{
	color:#333;
}
.product-gallary-container img{
	margin:10px;
	border:1px solid #ccc;
	 margin: 1%;
    width: 14%;
}
.document_images {
    margin-bottom: 30px;
	
}
/*
.document_images_inner {
	position:relative;
}
*/

a.select-check{
	background-color: #f2f2f2;
    border: 1px solid #ddd;
    color: #999;
    padding: 0 4px;
    position: absolute;
    
	top: 5px;
	right: 5px;
	bottom: inherit;
	border-radius: 10px;
	vertical-align: top;
	left: inherit;
	-webkit-transition:  0.4s ease-out;
    -moz-transition: 0.4s ease-out ;
    -o-transition: 0.4s ease-out ;
    transition: 0.4s ease-out;
}

a.select-check:hover {
    background-color: #ddd;
}

/*tool tip*/
.document_images_inner
{
	background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: inline-block;
    line-height: 1.42857;
    padding: 4px;
    transition: border 0.2s ease-in-out 0s;
	overflow: hidden;
	min-height: 150px;
	max-height: 150px;
	height:150px;
	text-align: center;
	width:100%;
}

.document_images_inner .mask {
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 4px;
    bottom: 1px;
    left: 1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    right: 1px;
    top: 1px;
    transition: all 0.4s ease-in-out 0s;
}
.document_images_inner:hover .mask{
	opacity:1;
}
.document_images_inner .mask .tools {
    color: #fff;
    font-size: 17px;
    margin: 0;
    opacity: 1;
    padding: 3px;
    position: relative;
    text-align: center;
    top: 50%;
    transform: translateY(-50%);
    transition: all 0.2s ease-in-out 0s;
}
.document_images_inner .mask .tools a {
    color: #fff;
}
.document_images_inner img {
    max-width: 100%;
    height: auto;
    max-height: 100%;
    display: inline-block;
	width:auto;
}
</style>
     
	  <?php $this->load->view("admin/sidebar-left");?>
      
	  <?php 
		$file_accept_string = implode(', ',$file_accept_array);
	  ?>
	  
      <div class="content-wrapper">
        <section class="content-header">
          <h1> <?php echo mlx_get_lang('Manage Documents'); ?> </h1>
        </section>

        <section class="content">
			 <?php 
			 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('documents/manage',$attributes); ?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
			
			<div class="row">
			<div class="col-md-12">   
			   
			<!-- general form elements -->
              <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> document-section">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Document Library'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					
				  </div>
                </div><!-- /.box-header -->
                  <div class="box-body">
                    
					<!--
					<div class="form-group">
						<label class="custom-file-upload">
							<input type="file" class="document_uploader" accept="<?php //echo $file_accept_string; ?>" id="document_uploader" multiple name="attachments[]" data-type="documents">
							<i class="fa fa-cloud-upload"></i> Upload Files
						</label>
						<progress id="document_uploader_progress" value="0" max="100" style="display:none;"></progress>
						
					</div>
					-->
					<div id="document_plupload_container" class="document_plupload_container">
						<div id ="document-drop-target" class="document-drop-target">
							<span class="document-drop-target-inner">
								<?php echo mlx_get_lang("Drop files or folders here"); ?>
								<br>
								<strong><?php echo mlx_get_lang("OR"); ?></strong>
								<br>
								<?php echo mlx_get_lang("Click here to select multiple files"); ?>
								<br><br>
								<small>(<?php echo mlx_get_lang("Allowed file size upto 40MB."); ?>)</small>
							</span>
							
						</div>
					</div>
			
					<div class="document_container media-upload-container row" id="document-upload-container">
						<?php 
						
						if(isset($document_list) && $document_list->num_rows() > 0)
						{
							
							foreach($document_list->result() as $row)
							{
								$explode = explode('.',$row->att_name);
								$extension = $explode[count($explode)-1];
								$actual_name = substr($row->att_name, 0, strrpos($row->att_name, "."));
								$light_box_string = '';
								$open_doc_iframe_class = '';
								if($row->file_type == 'image')
								{
									
									$thumb_image_url = base_url().'../'.$row->att_path.$actual_name.'-thumbnail.'.$extension;
									if(file_exists('../'.$row->att_path.$actual_name.'-thumbnail.'.$extension))
									{
										$thumb_image_url = base_url().'../'.$row->att_path.$actual_name.'-thumbnail.'.$extension;
									}
									else if(file_exists('../'.$row->att_path.$row->att_name))
									{
										$thumb_image_url = base_url().'../'.$row->att_path.$row->att_name;
									}
									else
									{
										continue;
									}
									$origional_dowload_image_url = $origional_image_url = base_url().'../'.$row->att_path.$row->att_name;
									
									$light_box_string = 'data-gallery="example-gallery" 
										data-title="Document Library" data-footer="'.$row->att_alt.'"
										data-toggle="lightbox"';
								}
								/*
								else if(($row->file_type == 'file') && ($extension == 'doc' || $extension == 'docx' || $extension == 'xls' || $extension == 'xlsx') && 
								file_exists('../'.$row->att_path.$row->att_name))
								{
									
									if(file_exists('../themes/default/images/file_icons/'.$extension.'_file.png'))
									{
										$thumb_image_url = base_url().'../themes/default/images/file_icons/'.$extension.'_file.png';
									}
									else
									{
										$thumb_image_url = base_url().'../themes/default/images/file_icons/default_file.jpg';
									}
									$url_final = base_url().'../'.$row->att_path.$row->att_name;
									
									/*$url_final = str_replace("\\75", "=", $url_final);
									$url_final = str_replace("\/", "%2F", $url_final);
									$url_final = str_replace("\\46", "&", $url_final);
									*/
									//$origional_image_url = 'https://docs.google.com/gview?url='.$url_final.'&embedded=true';
								/*	
									$origional_image_url = $url_final;
									$origional_dowload_image_url = base_url().'../'.$row->att_path.$row->att_name;
									$open_doc_iframe_class = 'open_doc_iframe_class';
								}
								*/
								else if(($row->file_type == 'file') && file_exists('../'.$row->att_path.$row->att_name))
								{
									if(file_exists('../themes/default/images/file_icons/'.$extension.'_file.png'))
									{
										$thumb_image_url = base_url().'../themes/default/images/file_icons/'.$extension.'_file.png';
									}
									else
									{
										$thumb_image_url = base_url().'../themes/default/images/file_icons/default_file.jpg';
									}
									$origional_dowload_image_url = $origional_image_url = base_url().'../'.$row->att_path.$row->att_name;
								}
								else
								{
									continue;
								}
						?>
							<div class="col-md-2 document_images">
								<div class="document_images_inner lazy-load-processing"  data-toggle="tooltip" title="" data-original-title="<?php echo $row->att_alt;?>">
										<img data-src="<?php echo $thumb_image_url; ?>" width="100%" class="lazy-img-elem">
									<a href="#" class="select-check remove_document_btn" id="<?php echo 'image_'.$myHelpers->EncryptClientId($row->att_id);?>" 
									data-type="documents" data-att_id="<?php echo $row->att_id;?>" 
									data-file_type="<?php echo $row->file_type;?>"
									data-name="<?php echo 'image_'.$row->att_id;?>"><i class="fa fa-remove"></i></a>
									<input type="hidden" name="" id="<?php echo 'image_'.$row->att_id.'_hidden';?>" value="<?php echo $row->att_alt;?>">
									<div class="mask">
									  <div class="tools tools-bottom">
										<a href="<?php echo $origional_dowload_image_url; ?>" download="<?php echo $row->att_alt;?>" class="btn btn-success"><i class="fa fa-download"></i></a>
										<!--<a href="<?php //echo $origional_image_url; ?>" target="_blank" <?php //echo $light_box_string; ?> class="btn btn-warning <?php //echo $open_doc_iframe_class;?>"><i class="fa fa-search-plus"></i></a>-->
									  </div>
									</div>
								</div>
							</div>
							
						<?php
							}
						}
						?>
					</div>
					
					
				 </div>
                
              </div>
			  
			  
		</div><!-- end col-md-8-->
		  
		  
		  
		  
		  </div><!-- end row 1-->	  
		  
		  
			  
			  </form>
        </section>
      </div>