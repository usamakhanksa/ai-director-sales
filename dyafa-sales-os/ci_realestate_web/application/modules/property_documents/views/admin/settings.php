
      <?php $this->load->view("default/header-top");?>
      
	  <?php $this->load->view("default/sidebar-left");?>
      
<script>
 $(function() {
    $('.alert').delay(5000).fadeOut('slow');
	
	$('a.remove_img').click(function() 
	{			
		var id = $(this).attr('data-name');			
		var thiss = $(this);			
		var img_name = $('#'+id+'_hidden').val();			
		var image_type =  $('#'+id).attr('data-type');			
		var strconfirm = confirm("Are you sure you want to delete?");			
		if (strconfirm == true)			
		{					
			$('.full_sreeen_overlay').show();					
			$.ajax({						
				url: '<?php echo site_url();?>/ajax/delete_logo_images_callback_func',						
				type: 'POST',						
				success: function (res) 
				{							
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
				data: {img_name : img_name,image_type : image_type},						
				cache: false					
			});							
		}			
		return false;		
	});	
	
	$('.add_new_image_type').click(function() 
	{			
		var thiss = $(this);			
		$('.table tbody').append('<tr>'+
					'<td>'+
						'<input type="text" class="form-control title_text" name="" value="">'+
					'</td>'+
					'<td>'+
						'<input type="text" class="form-control width_text"  name="" value="">'+
					'</td>'+
					'<td>'+
						'<input type="text" class="form-control height_text" name="" value="">'+
					'</td>'+
					'<td>'+
						'<a href="#" class="btn btn-info edit_image_type hide"><i class="fa fa-pencil"></i></a>&nbsp;'+
						'<a href="#" class="btn btn-danger delete_image_type hide"><i class="fa fa-remove"></i></a>'+
						'<a href="#" class="btn btn-success save_image_type"><i class="fa fa-check"></i></a>'+
					'</td>'+
				'</tr>');		
		return false;		
	});	
	
	$(document).delegate('.save_image_type','click',function() {
		var thiss = $(this);
		var has_valid = true;
		
		if($(thiss).parents('tr').find('.title_text').val() == '')
		{
			has_valid = false;
			$(thiss).parents('tr').find('.title_text').parent().addClass('has-error');
			
			$(thiss).parents('tr').find('.title_text').data("title", 'Field is Required').addClass("error").tooltip({
				trigger: 'manual'
			  }).tooltip('show');
			$(thiss).parents('tr').find('.title_text').focus();
		}
		else
		{
			$(thiss).parents('tr').find('.title_text').parent().removeClass('has-error');
			$(thiss).parents('tr').find('.title_text').tooltip("destroy");
		}
		
		if($(thiss).parents('tr').find('.width_text').val() == '')
		{
			has_valid = false;
			$(thiss).parents('tr').find('.width_text').parent().addClass('has-error');
			
			$(thiss).parents('tr').find('.width_text').data("title", 'Field is Required').addClass("error").tooltip({
				trigger: 'manual'
			  }).tooltip('show');
			$(thiss).parents('tr').find('.width_text').focus();
		}
		else if(!$.isNumeric($(thiss).parents('tr').find('.width_text').val()))
		{
			has_valid = false;
			$(thiss).parents('tr').find('.width_text').val('');
			$(thiss).parents('tr').find('.width_text').data("title", 'This Field Contain Only Numeric Value').addClass("error").tooltip({
				trigger: 'manual'
			  }).tooltip('show');
			$(thiss).parents('tr').find('.width_text').focus();
		}
		else
		{
			$(thiss).parents('tr').find('.width_text').parent().removeClass('has-error');
			$(thiss).parents('tr').find('.width_text').tooltip("destroy");
		}
		
		if($(thiss).parents('tr').find('.height_text').val() == '')
		{
			has_valid = false;
			$(thiss).parents('tr').find('.height_text').parent().addClass('has-error');
			
			$(thiss).parents('tr').find('.height_text').data("title", 'Field is Required').addClass("error").tooltip({
				trigger: 'manual'
			  }).tooltip('show');
			$(thiss).parents('tr').find('.height_text').focus();
		}
		else if(!$.isNumeric($(thiss).parents('tr').find('.height_text').val()))
		{
			has_valid = false;
			$(thiss).parents('tr').find('.height_text').val('');
			$(thiss).parents('tr').find('.height_text').tooltip("destroy").data("title", 'This Field Contain Only Numeric Value').addClass("error").tooltip({
				trigger: 'manual'
			  }).tooltip('show');
			$(thiss).parents('tr').find('.width_text').focus();
		}
		else
		{
			$(thiss).parents('tr').find('.height_text').parent().removeClass('has-error');
			$(thiss).parents('tr').find('.height_text').tooltip("destroy");
		}
		
		if(has_valid)
		{
			$(thiss).parents('tr').find('.title_text').attr({'name':'options[document_image_types][title][]','readonly':'readonly'});
			$(thiss).parents('tr').find('.width_text').attr({'name':'options[document_image_types][width][]','readonly':'readonly'});
			$(thiss).parents('tr').find('.height_text').attr({'name':'options[document_image_types][height][]','readonly':'readonly'});
			thiss.addClass('hide');
			$(thiss).parents('tr').find('.edit_image_type,.delete_image_type').removeClass('hide');
		}
		return false;
	});
	
	$(document).delegate('.edit_image_type','click',function() {
		var thiss = $(this);
		$(thiss).parents('tr').find('.title_text').attr({'name':'','readonly':false});
		$(thiss).parents('tr').find('.width_text').attr({'name':'','readonly':false});
		$(thiss).parents('tr').find('.height_text').attr({'name':'','readonly':false});
		$(thiss).parents('tr').find('.edit_image_type,.delete_image_type').addClass('hide');
		$(thiss).parents('tr').find('.save_image_type').removeClass('hide');
		return false;
	});
	
	$(document).delegate('.delete_image_type','click',function() {
		var thiss = $(this);
		thiss.parents('tr').remove();
		return false;
	});
	
	$('.file_type_checkbox').change(function() {
		if($(this).val() == 'limited')
		{
			$('.file_type_hidden_block').removeClass('hide');
		}
		else
		{
			$('.file_type_hidden_block').addClass('hide');
		}
		return false;
	});
	
	$('.sel-desel-btn').click(function() {
		var action = $(this).attr('data-action');
		var target = $(this).parent().attr('data-target');
		if(action  == 'select')
		{
			$('.file_type_hidden_block ').find('.'+target+'_checkbox input[type="checkbox"]').prop('checked',true);
		}
		else if(action  == 'deselect')
		{
			$('.file_type_hidden_block ').find('.'+target+'_checkbox input[type="checkbox"]').prop('checked',false);
		}
		return false;
	});
	
});

function progress(e)
{        
	if(e.lengthComputable){           
		$('#'+id+'_progress').show();            
		$('progress').attr({value:e.loaded,max:e.total});        
	}    
}
</script> 
<style>
.att_photo,#att_photo,#att_id,#guarantor_1_photo, #guarantor_1_id, #guarantor_2_photo, #guarantor_2_id ,#media_att_photo{
    display: none !important;}
.custom-file-upload {    
	border: 1px solid #ccc;   
	 display: inline-block;   
	 padding: 6px 12px;    
	 cursor: pointer;	
	 font-weight: 500;
 }
 
a.remove_img {	
	 background-color: #f2f2f2;   
	 border: 1px solid #ddd;    
	 color: #999;   
	 padding: 0 3px;   
	 position: relative;    	
	 top: -8px;	left: -12px;	
	 border-radius: 10px;	
	 vertical-align: top;		
	 -webkit-transition:  0.4s ease-out;    
	 -moz-transition: 0.4s ease-out ;    
	 -o-transition: 0.4s ease-out ;    
	 transition: 0.4s ease-out;
 }
 
a.remove_img:hover {    
	background-color: #ddd;
}
.file_type_hidden_block {
    background-color: rgba(238, 238, 238, 0.5);
    border: 1px solid #d2d6de;
    margin-bottom: 10px;
    padding: 5px 15px 0;
}
</style>
<?php 
	if(isset($options_list) && $options_list->num_rows()>0)
	{
		
		foreach($options_list->result() as $row)
		{
			${$row->option_key} = $row->option_value;
		}
	}
?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1> Document Settings </h1>
          <?php 
			if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
			
			
			
			?>
        </section>

        <!-- Main content -->
        <section class="content">
			<!-- form start -->
               <!-- <form role="form">-->
             <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('documents/settings',$attributes); 
			
			?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Document Settings</h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
                </div><!-- /.box-header -->
				
				
                  <div class="box-body">
                    
					
					
					<label for="document_file_size">File Size</label>
					<div class="input-group">
                      
                      <input type="number" min="1" step="1" class="form-control" required="required" 
					  name="options[document_file_size]" id="document_file_size" placeholder="" value="<?php if(isset($document_file_size)) echo $document_file_size; ?>">
					  <span class="input-group-addon" style="background-color: #eee;"><strong>MB</strong></span>
					  
                    </div>
					<p class="help-block">File Size in MB i.e. 2 MB. Default File Size is <strong>2 MB</strong>.</p>
					
					<div class="form-group" style="margin-bottom:0px;">
                      <label for="document_file_types">File Types</label><br>
					  
					  <div class="radio" style="display: inline-block; margin-right: 15px; margin-top:0px;">
                        <label>
                          <input name="options[document_file_type_options]" class="file_type_checkbox" id="file_type_all" value="all" 
						  <?php if((isset($document_file_type_options) && $document_file_type_options == 'all') || !isset($document_file_type_options)) echo 'checked="checked"'; ?> type="radio">
                          All supported file formats.
                        </label>
                      </div>
					  <div class="radio" style="display: inline-block; margin-right: 15px; margin-top:0px;">
                        <label>
                          <input name="options[document_file_type_options]" class="file_type_checkbox" id="file_type_limited" value="limited" type="radio" 
                          <?php if((isset($document_file_type_options) && $document_file_type_options == 'limited')) echo 'checked="checked"'; ?>>
						  Limited file formates.
                        </label>
                      </div>
					  <?php 
					  $valid_extensions = array(); 
					  $valid_extensions['Image'] = array('jpeg', 'jpg', 'png', 'gif','bmp');
					  $valid_extensions['Word Document'] = array('doc' , 'docx','xls','xlsx','ppt','pptx');
					  $valid_extensions['Other'] = array('pdf' ,'txt',);
					  ?>
					  <div class="file_type_hidden_block <?php if((isset($document_file_type_options) && $document_file_type_options == 'all') || !isset($document_file_type_options)) echo 'hide'; ?>">
						<?php 
						foreach($valid_extensions as $kk=>$vv) 
						{ 
							asort($vv);
							$target = strtolower(str_replace(' ','_',$kk));
							echo '<div class="img_type_header" data-target="'.$target.'"><label for="document_file_types">'.ucfirst($kk).'</label>
								 <a class="pull-right sel-desel-btn" data-action="deselect" href="#"><i class="fa fa-remove"></i> Deselect All</a>
								 <a class="pull-right sel-desel-btn" data-action="select" href="#" style="margin-right:10px;"><i class="fa fa-check"></i> Select All</a>
							</div>';
							foreach($vv as $k=>$v)
							{
								$checked_string = '';
								if(isset($document_file_type) && !empty($document_file_type))
								{
									$document_file_type_array = json_decode($document_file_type,true);
									if(in_array($v,$document_file_type_array))
										$checked_string = ' checked="checked" ';
								}
								else
								{
									$checked_string = ' checked="checked" ';
								}
								?>
								<div class="checkbox <?php echo $target.'_checkbox'; ?>" style="display: inline-block; margin-right: 15px;margin-top:0px;">
									<label>
									  <input type="checkbox" name="options[document_file_type][]" value="<?php echo $v;?>" <?php echo $checked_string; ?>>
									  <?php 
									  $exp_name = explode('~',$v);
									  echo strtoupper($exp_name[0]);?>
									</label>
								</div>
						<?php
							}
						} ?>
					  </div>
					  
					</div>
					
					<div class="form-group">
                      <label for="document_file_size">Image Types</label>
                      <div class="file_type_container table-responsive">
							<table class="table table-bordered">
								<thead>
									<th width="25%">Title</th>
									<th width="25%">Width</th>
									<th width="25%">Height</th>
									<th width="25%">Action</th>
								</thead>
								<tbody>
									<?php 
									if(isset($document_image_types) && !empty($document_image_types)) { 
										$document_image_types_array = json_decode($document_image_types,true);
									}
									if(isset($document_image_types_array) && !empty($document_image_types_array)) { 
										foreach($document_image_types_array as $k=>$v)
										{
											$disabled = '';
											if($v['disable'] == true)
											{
												$disabled = 'disabled';
											}
										?>
											<tr>
												<td>
													<input type="text" class="form-control title_text" name="options[document_image_types][title][]" 
													readonly value="<?php echo $v['title']; ?>">
												</td>
												<td>
													<input type="text" class="form-control width_text"  name="options[document_image_types][width][]" 
													readonly value="<?php echo $v['width']; ?>">
												</td>
												<td>
													<input type="text" class="form-control height_text" name="options[document_image_types][height][]" 
													readonly value="<?php echo $v['height']; ?>">
												</td>
												<td>
													<a href="#" class="btn btn-info edit_image_type <?php echo $disabled; ?>"><i class="fa fa-pencil"></i></a>
													<a href="#" class="btn btn-danger delete_image_type <?php echo $disabled; ?>"><i class="fa fa-remove"></i></a>
													<a href="#" class="btn btn-success save_image_type hide"><i class="fa fa-check"></i></a>
												</td>
											</tr>
										<?php
										}
									?>
									<?php }else { ?>
										<tr>
											<td>
												<input type="text" class="form-control" name="options[document_image_types][title][]" readonly value="Thumbnail">
											</td>
											<td>
												<input type="text" class="form-control"  name="options[document_image_types][width][]" readonly value="150">
											</td>
											<td>
												<input type="text" class="form-control" name="options[document_image_types][height][]" readonly value="150">
											</td>
											<td>
												<a href="#" class="btn btn-info edit_image_type disabled"><i class="fa fa-pencil"></i></a>
												<a href="#" class="btn btn-danger delete_image_type disabled"><i class="fa fa-remove"></i></a>
												
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
							<a href="#" class="btn btn-default pull-right add_new_image_type">Add New</a>
					  </div>
                    </div>
					
                  </div>

              </div>
			  
			  
		  </div>
		  
		  <div class="col-md-4">
		  <div class="box box-primary">
			  <div class="box-header with-border">
                  <h3 class="box-title"> Status</h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				  </div>
                </div>
				 
			  	 <div class="box-footer">
					<button name="submit" type="submit" class="btn btn-primary pull-right" id="save_publish">Save Changes</button>
                  </div>
			  </div>
		  </div>
		  
		  
		  </div>
		  
			</form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      