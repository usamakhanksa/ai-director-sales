<?php $this->load->view("default/header-top");?>

<?php $this->load->view("default/sidebar-left");?>
      

      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add New Badge'); ?></h1>
          <?php if( form_error('b_title')) 	  { 	echo form_error('b_title'); 	  } ?>
		  <?php if( form_error('b_image')) 	  { 	echo form_error('b_image'); 	  } ?>
        </section>

		<?php 
	$site_language = $myHelpers->global_lib->get_option('site_language');
	$enable_multi_language = $myHelpers->global_lib->get_option('enable_multi_language');
	$default_language = $myHelpers->global_lib->get_option('default_language');
?>	
		
        <section class="content">
		     <?php 
			 
			 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('badge/add_new',$attributes); ?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
			
			<div class="row">
			<div class="col-md-8">   
				
			<!-- general form elements -->
              <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
				
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Badge Details'); ?></h3>
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
								<label class="custom-file-upload">
									<input type="file" accept="image/*" required id="att_photo" name="attachments" data-type="photo" data-user-type="badge"/>
									<i class="fa fa-cloud-upload"></i> <?php echo mlx_get_lang('Upload Image'); ?>
								</label>
								<progress id="att_photo_progress" value="0" max="100" style="display:none;"></progress>
								<a id="att_photo_link" href="" download="" style="display:none;">
									<img src="">
								</a>
								<a class="remove_img" id="att_photo_remove_img" data-name="att_photo" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
								<input type="hidden" name="b_image" value="" id="att_photo_hidden">
								<!--<p class="help-block" style="margin-bottom:0px;font-style: oblique;"><?php echo mlx_get_lang('Image file must be greater then 1900x1300'); ?></p>-->
							</div>
						</div>
						
						<!--	<span class="text-red">*</span> 	required	-->
						<div class="col-md-12">
							<div class="form-group">
							  <label for="short_description"><?php echo mlx_get_lang('Short Description'); ?> </label>
							 	<textarea class="form-control" name="short_description" id="short_description" cols="30" rows="10"></textarea>
							</div>
						</div>
						
						
					<?php if(isset($enable_multi_language) && $enable_multi_language == 'Y'){ ?>
					
					<?php if(isset($site_language) && !empty($site_language)) { 
						$site_language_array = json_decode($site_language,true);
						if(!empty($site_language_array)) { 
						
						/*echo "<pre>";print_r($site_language_array); echo "</pre>"; */
					?>
					<!-- <div class="col-md-12">
						<div class="form-group">
						  <label for="b_assign_to"><?php echo mlx_get_lang('Banner for Selected Language'); ?> </label>
						  
						<div class="checkbox"> 
						<label for="select-all-lang"> <input type="checkbox" id="select-all-lang" /> Select All </label>
						</div>
						<?php 
						$n=0;
						foreach($site_language_array as $k=>$v) { $n++; 
						$lang_exp = explode('~',$v['language']);
						$lang_code = $lang_exp[1];
						$lang_title = $lang_exp[0];
						//print_r($lang_exp);
						?>
						
						<div class="checkbox"> 
						<label for="<?php echo $lang_code."_".$k;?>"> 
						<input type="checkbox" 
								id="<?php echo $lang_code."_".$k;?>" 
								name="banner_for_lang[<?php echo $lang_code;?>]" 
								value="<?php echo $lang_code;?>" /> <?php echo $lang_title; ?> </label>
						</div>	
						<?php  } ?>
						</div>
					</div>	 -->
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
					<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
                  </div>
			  </div>
		  </div>
		  
		  </div>
			  
			  </form>
        </section>
      </div>