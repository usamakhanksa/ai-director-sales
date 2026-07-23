
<?php 
if(isset($query) && $query->num_rows() > 0)
{
	$row = $query->row();
	$dec_b_id = $row->b_id;
	$b_title = $row->b_title;
	$b_image = '';
	
	if(!empty($row->b_image) && file_exists('uploads/banner/'.$row->b_image))
	{
		$b_image  = $row->b_image ;
	}
	$b_status = $row->b_status;
}
else
{
	$b_title = "";
	$b_image  = '';
	$b_status = 'N';
}
?>	  

<?php 
	$site_language = get_option('site_language');
	$enable_multi_language = get_option('enable_multi_language');
	$default_language = get_option('default_language');
?>	
	  <div class="content-wrapper">
        <section class="content-header">
          <h1  class="page-title"><i class="fa fa-edit"></i> <?php echo mlx_get_lang('Edit Banner'); ?> </h1>
          <?php if( form_error('b_title')) 	  { 	echo form_error('b_title'); 	  } ?>
		  <?php if( form_error('b_image')) 	  { 	echo form_error('b_image'); 	  } ?>
        </section>

        <section class="content">
		     <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('',$attributes); ?>
			   <input type="hidden" name="b_id" value="<?php if(isset($b_id) && !empty($b_id)) echo $b_id; ?>">
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-<?php echo get_skin_class(); ?>">
				
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Banner Details'); ?></h3>
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
						
						
						
						<div class="col-md-12">
							<div class="form-group">
								<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Image'); ?></label>
								
								<?php 
								
								$thumb_photo = $myHelpers->global_lib->get_image_type('uploads/banner/',$b_image,'thumb'); 
								if(empty($thumb_photo))$thumb_photo = $b_image;
								?>
								
								<div class="pl_image_container">
									<label class="custom-file-upload" data-element_id="<?php if(isset($dec_b_id) && !empty($dec_b_id)) echo $myHelpers->EncryptClientId($dec_b_id); ?>" data-type="banner" id="pl_file_uploader_1" 
										<?php if(isset($thumb_photo) && !empty($thumb_photo)) { echo 'style="display:none;"';}?>>
										<?php echo mlx_get_lang('Drop images here'); ?>
										<br>
										<strong><?php echo mlx_get_lang('OR'); ?></strong>
										<br>
										<?php echo mlx_get_lang('Click here to select images'); ?>
										<br>
										<?php echo mlx_get_lang('Best Size: 800px X 600px'); ?>
									</label>
									<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
									<?php if(isset($thumb_photo) && !empty($thumb_photo)) { ?>
									
										<a class="pl_file_link" href="<?php echo base_url().'uploads/banner/'.$b_image; ?>" 
										download="<?php echo $b_image; ?>" style="">
											<img src="<?php echo base_url().'uploads/banner/'.$thumb_photo; ?>"  style="width:50%;">
										</a>
									
										<a class="pl_file_remove_img" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
									<?php }else{ ?>
										<a class="pl_file_link" href="" download="" style="display:none;">
											<img src=""  style="width:50%;">
										</a>
										<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
									<?php } ?>
									<input type="hidden" name="b_image" value="<?php if(isset($b_image) && !empty($b_image)) { echo $b_image;}?>" 
									class="pl_file_hidden">
								</div>
								
								
							</div>
						</div>
						
						<!--	<span class="text-red">*</span> required	-->
						<div class="col-md-12">
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
						</div>
						
						
						<?php if(isset($enable_multi_language) && $enable_multi_language == 'Y'){ ?>
					
					<?php if(isset($site_language) && !empty($site_language)) { 
						$site_language_array = json_decode($site_language,true);
						if(!empty($site_language_array)) { 
						
						foreach($site_language_array as $aak=>$aav)
						{
							if($aav['language'] == $default_language)
							{
								$new_value = $site_language_array[$aak];
								unset($site_language_array[$aak]);
								array_unshift($site_language_array, $new_value);
								break;
							}
						}
						
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
					<div class="col-md-12">
						<div class="form-group">
						  <label for="b_assign_to"><?php echo mlx_get_lang('Banner for Selected Language'); ?> </label>
						  
						<div class="checkbox"> 
						<label for="select-all-lang" style="padding-left:0px;"> 
							<input class="minimal" type="checkbox" id="select-all-lang" />&nbsp;&nbsp;<?php echo mlx_get_lang('Select All'); ?> </label>
						</div>
						<?php 
						$n=0;
						foreach($site_language_array as $k=>$v) { 
						if($v['status'] != 'enable')
							continue;
						$n++; 
						$lang_exp = explode('~',$v['language']);
						$lang_code = $lang_exp[1];
						$lang_title = $lang_exp[0];
						
						?>
						
						<div class="checkbox banner-lang-select"> 
						<label for="<?php echo $lang_code."_".$k;?>"  style="padding-left:0px;"> 
						<input type="checkbox" class="minimal"
								name="banner_for_lang[<?php echo $lang_code;?>]" 
								id="<?php echo $lang_code."_".$k;?>" 
								<?php if(in_array($lang_code , $banner_for_langs)) { ?>
								checked
								<?php } ?>
								value="<?php echo $lang_code;?>" />&nbsp;&nbsp;<?php echo $lang_title; ?> </label>
						</div>	
						<?php  } ?>
						</div>
					</div>	
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
		  <div class="box box-<?php echo get_skin_class(); ?>">
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
					<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" 
					id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
                  </div>
			  </div>  
		  </div>
		  </div>
			  </form>
        </section>
      </div>
   <script>
	$(document).ready(function() {
		$('#select-all-lang').on('ifChanged', function(event){
			 if($('#select-all-lang').is(':checked'))
			 {
				 $('.banner-lang-select input[type="checkbox"]').iCheck('check');
			 }
			 else
			 {
				 $('.banner-lang-select input[type="checkbox"]').iCheck('uncheck');
			 }
		});
	});
</script>   