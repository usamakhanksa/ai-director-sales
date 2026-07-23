
<?php $this->load->view("default/header-top");?>
<?php $this->load->view("default/sidebar-left");?>

<?php 
if(isset($query) && $query->num_rows() > 0)
{
	$row = $query->row();
	
	$b_title = $row->title;
	$desc = $row->short_description;
	$b_image = '';
	if(!empty($row->image) && file_exists('../uploads/badge/'.$row->image))
	{
		$b_image  = $row->image ;
	}
	$b_status = $row->status;
}
else
{
	$b_title = "";
	$b_image  = '';
	$b_status = 'N';
}
?>	  

<?php 
	$site_language = $myHelpers->global_lib->get_option('site_language');
	$enable_multi_language = $myHelpers->global_lib->get_option('enable_multi_language');
	$default_language = $myHelpers->global_lib->get_option('default_language');
?>	
	  <div class="content-wrapper">
        <section class="content-header">
          <h1  class="page-title"><i class="fa fa-edit"></i> <?php echo mlx_get_lang('Edit Badge'); ?> </h1>
          <?php if( form_error('b_title')) 	  { 	echo form_error('b_title'); 	  } ?>
		  <?php if( form_error('b_image')) 	  { 	echo form_error('b_image'); 	  } ?>
        </section>

        <section class="content">
		     <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('badge/edit',$attributes); ?>
			   <input type="hidden" name="b_id" value="<?php if(isset($b_id) && !empty($b_id)) echo $b_id; ?>">
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
				
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('badge Details'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				  </div>
                </div>
                  <div class="box-body">
                    
					
					<div class="row">
					
						
						<div class="col-md-12">
							<div class="form-group">
							  <label for="b_title"><?php echo mlx_get_lang('Title'); ?></label>
							  <input type="text" class="form-control" id="b_title" name="b_title" 
							  value="<?php if(isset($_POST['b_title'])) 
												echo $_POST['b_title'];
											else if(isset($b_title) && !empty($b_title))
												echo $b_title;
									?>">
							</div>
						</div>
						
						
						
						<div class="col-md-6">
							<div class="form-group">
							  <label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Image'); ?></label>
								<label class="custom-file-upload" <?php if(isset($b_image) && !empty($b_image)) { echo 'style="display:none;"';}?>>
									<input type="file" accept="image/*" id="att_photo" name="attachments" data-type="photo" data-user-type="badge"/>
									<i class="fa fa-cloud-upload"></i> <?php echo mlx_get_lang('Upload Image'); ?>
								</label>
								<progress id="att_photo_progress" value="0" max="100" style="display:none;"></progress>
								<?php if(isset($b_image) && !empty($b_image)) { ?>
									<a id="att_photo_link" href="<?php echo base_url().'../uploads/badge/'.$b_image; ?>" download="<?php echo $b_image; ?>" style="">
										<img src="<?php echo base_url().'../uploads/badge/'.$b_image; ?>" >
									</a>
									<a class="remove_img" id="att_photo_remove_img" data-name="att_photo" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
								<?php }else{ ?>
									<a id="att_photo_link" href="" download="" style="display:none;">
										<img src="" >
									</a>
									<a class="remove_img" id="att_photo_remove_img" data-name="att_photo" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
								<?php } ?>
								<input type="hidden" name="b_image" value="<?php if(isset($b_image) && !empty($b_image)) echo $b_image; ?>" id="att_photo_hidden">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
							  <label for="short_description"><?php echo mlx_get_lang('Short Description'); ?> </label>
							 	<textarea class="form-control" name="short_description" id="short_description" cols="30" rows="10"><?php echo $desc; ?></textarea>
							</div>
						</div>
						
						<!--	<span class="text-red">*</span> required	-->
						<!-- <div class="col-md-12">
							<div class="form-group">
							  <label for="b_assign_to"><?php echo mlx_get_lang('Assign To'); ?> </label>
							  <select class="form-control select2_elem assign_to_list"  name="b_assign_to[]" id="b_assign_to"  multiple data-placeholder="Select Any Page">
								
								
								<?php if(isset($static_pages) && !empty($static_pages)) { ?>
										<?php foreach($static_pages as $k=>$v) { 
											echo '<option value="static~'.$k.'" ';
											$result = $myHelpers->Common_model->commonQuery("select banner_id from banner_assigned_to 
																							where assign_type = 'static' and assign_id = '$k' and banner_id = '".$myHelpers->global_lib->DecryptClientId($b_id)."'");
											if($result->num_rows() > 0)
											{
												echo ' selected="selected" ';
											}
											echo '>'.$v.'</option>';
										} ?>
								<?php } ?>
								<?php if(isset($property_list) && $property_list->num_rows() > 0) { ?>
									<optgroup label="Properties">
										<?php foreach($property_list->result() as $row) { 
											echo '<option value="property~'.$myHelpers->global_lib->EncryptClientId($row->p_id).'" ';
											$result = $myHelpers->Common_model->commonQuery("select banner_id from banner_assigned_to 
																							where assign_type = 'property' and assign_id = '$row->p_id' and banner_id = '".$myHelpers->global_lib->DecryptClientId($b_id)."'");
											if($result->num_rows() > 0)
											{
												echo ' selected="selected" ';
											}
											echo '>'.ucfirst($row->title).'</option>';
										} ?>
									</optgroup>
								<?php } ?>
								<?php if(isset($page_list) && $page_list->num_rows() > 0) { ?>
									<optgroup label="Pages">
										<?php foreach($page_list->result() as $row) { 
											echo '<option value="page~'.$myHelpers->global_lib->EncryptClientId($row->page_id).'" ';
											$result = $myHelpers->Common_model->commonQuery("select banner_id from banner_assigned_to 
																							where assign_type = 'page' and assign_id = '$row->page_id' and banner_id = '".$myHelpers->global_lib->DecryptClientId($b_id)."'");
											if($result->num_rows() > 0)
											{
												echo ' selected="selected" ';
											}
											echo '>'.ucfirst($row->page_title).'</option>';
										} ?>
									</optgroup>
								<?php } ?>
							  </select>
							</div>
						</div> -->
						
						
						<?php if(isset($enable_multi_language) && $enable_multi_language == 'Y'){ ?>
					
					<?php if(isset($site_language) && !empty($site_language)) { 
						$site_language_array = json_decode($site_language,true);
						if(!empty($site_language_array)) { 
						
						/*echo "<pre>";print_r($site_language_array); echo "</pre>"; */
						$banner_for_langs = array();
						
						if($banner_for_lang_res->num_rows() > 0)
						{
							foreach ($banner_for_lang_res->result() as $row)
							{ 
								$banner_for_langs [] = 	$row->for_lang;
							}	
						}	
						
					?>
					
					<?php 
						}
					}
						}
					?>
						
						
						
						
						
						
					</div>	
					
                    
                  </div>
                
              </div>
			  
			 
		  </div>
		  <div class="col-md-4">
		  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
			  <div class="box-header with-border">
                  <h3 class="box-title"> <?php echo mlx_get_lang('Status'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				  </div>
                </div>
				<div class="box-body">
					<div class="form-group">
						<div class="radio">
							<label style="padding-left:0px;">
							  <input type="radio" class="flat-green" name="b_status" id="b_status_Y" value="Y" 
							  <?php if($b_status == 'Y') echo 'checked="checked"'; ?>>
							  &nbsp; &nbsp;<?php echo mlx_get_lang('Active'); ?>
							</label>
						</div>
						<div class="radio">
							<label style="padding-left:0px;">
							  <input type="radio" class="flat-red" name="b_status" id="b_status_N" value="N" 
							  <?php if($b_status == 'N') echo 'checked="checked"'; ?>>
							  &nbsp; &nbsp;<?php echo mlx_get_lang('In-Active'); ?>
							</label>
						 </div>
					</div>
				</div>
				 <div class="box-footer">
					<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
                  </div>
			  </div>  
		  </div>
		  </div>
			  </form>
        </section>
      </div>
      