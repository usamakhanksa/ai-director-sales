
<div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add New Banner'); ?></h1>
          <?php if( form_error('b_title')) 	  { 	echo form_error('b_title'); 	  } ?>
		  <?php if( form_error('b_image')) 	  { 	echo form_error('b_image'); 	  } ?>
        </section>

<?php 
	$site_language = get_option('site_language');
	$enable_multi_language = get_option('enable_multi_language');
	$default_language = get_option('default_language');
?>	
		
        <section class="content">
		     <?php 
			 
			 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('',$attributes); ?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
			
			<div class="row">
			<div class="col-md-8">   
				
			<!-- general form elements -->
              <div class="box box-<?php echo get_skin_class(); ?>">
				
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Banner Details'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					<!--<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>-->
				  </div>
                </div><!-- /.box-header -->
                  <div class="box-body">
                    
					
					<div class="row">
					
						<div class="col-md-12">
							<div class="form-group">
							  <label for="b_title"><?php echo mlx_get_lang('Title'); ?> <span class="text-red">*</span></label>
							  <input type="text" class="form-control"  name="b_title" id="b_title" required
							  value="<?php if(isset($_POST['b_title'])) echo $_POST['b_title'];?>">
							</div>
						</div>
						
						<div class="col-md-12">
							<div class="form-group ">
								<label for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Image'); ?> <span class="text-red">*</span></label>
								
								<div class="pl_image_container">
									<label class="custom-file-upload" data-element_id="" data-type="banner" id="pl_file_uploader_1">
										<?php echo mlx_get_lang('Drop images here'); ?>
										<br />
										<strong><?php echo mlx_get_lang('OR'); ?></strong>
										<br />
										<?php echo mlx_get_lang('Click here to select images'); ?>
										<br>
										<?php echo mlx_get_lang('Best Size: 800px X 600px'); ?>
									</label>
									<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
									<a class="pl_file_link" href="" download="" style="display:none;">
										<img src="" style="width:50%;">
									</a>
									<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
									<input type="hidden" name="b_image" value="" class="pl_file_hidden" required>
								</div>
								
							</div>
						</div>
						
						<!--	<span class="text-red">*</span> 	required	-->
						<div class="col-md-12">
							<div class="form-group">
							  <label for="b_assign_to"><?php echo mlx_get_lang('Assign To'); ?> </label>
							  <select class="form-control select2_elem assign_to_list"  name="b_assign_to[]" id="b_assign_to"  multiple data-placeholder="Select Any Page">
								
								
								<?php if(isset($static_pages) && !empty($static_pages)) { ?>
										<?php foreach($static_pages as $k=>$v) { 
											echo '<option value="static~'.$k.'">'.$v.'</option>';
										} ?>
								<?php } ?>
								<?php if(isset($property_list) && $property_list->num_rows() > 0) { ?>
									<optgroup label="Properties">
										<?php foreach($property_list->result() as $row) { 
											echo '<option value="property~'.$myHelpers->global_lib->EncryptClientId($row->p_id).'">'.ucfirst($row->title).'</option>';
										} ?>
									</optgroup>
								<?php } ?>
								<?php if(isset($page_list) && $page_list->num_rows() > 0) { ?>
									<optgroup label="Pages">
										<?php foreach($page_list->result() as $row) { 
											echo '<option value="page~'.$myHelpers->global_lib->EncryptClientId($row->page_id).'">'.ucfirst($row->page_title).'</option>';
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
					?>
					<div class="col-md-12">
						<div class="form-group">
						  <label for="b_assign_to"><?php echo mlx_get_lang('Banner for Selected Language'); ?> </label>
						  
						<div class="checkbox"> 
						<label for="select-all-lang" style="padding-left:0px;;"> 
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
						<label for="<?php echo $lang_code."_".$k;?>" style="padding-left:0px;"> 
						<input type="checkbox" class="minimal"
								id="<?php echo $lang_code."_".$k;?>" 
								name="banner_for_lang[<?php echo $lang_code;?>]" 
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
                  <h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				  </div>
                </div>
				<div class="box-body">
					<div class="form-group">
						<div class="radio">
							<label style="padding-left:0px;">
							  <input type="radio" class="flat-green" name="b_status" id="b_status_Y" value="Y" checked="checked">
							  &nbsp; &nbsp;<?php echo mlx_get_lang('Active'); ?>
							</label>
						</div>
						<div class="radio">
							<label style="padding-left:0px;">
							  <input type="radio" class="flat-red" name="b_status" id="b_status_N" value="N" >
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