<?php 
	$site_language = 			get_option('site_language');
	$enable_multi_language = 	get_option('enable_multi_language');
	$default_language = 		get_option('default_language');
	$short_desc_limit = 250;
	$user_type = $this->session->userdata('user_type');
?>


<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add New Blog'); ?> </h1>
  <?php echo validation_errors(); ?>
  <?php 
	/**		$user_type != 'admin' ||			*/
  if($this->site_payments == 'Y' && $this->post_blog_credit <= 0  && ( !(strpos( $user_type, "admin" ) !== false ))  ){ ?>
   <div class="alert alert-warning alert-dismissable show_always" style="margin-top:10px; margin-bottom:0px;">
		<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
		<?php echo mlx_get_lang('You don\'t have sufficent credits for post blog.'); ?> 
	</div>
  <?php } ?>
</section>

<section class="content">
	<?php 
	$attributes = array('name' => 'add_form_post','class' => 'form');		 			
	echo form_open_multipart('admin/blog/add_new',$attributes); ?>
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-8">   
	   
	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Blog Details'); ?></h3>
		  <div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		  </div>
		</div>
		  <div class="box-body">
			
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
			 
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
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
						<li <?php if($n == 1) echo 'class="active"'; ?>>
							<a href="#<?php echo $lang_code; ?>" data-toggle="tab"><?php echo ucfirst($lang_title); ?></a>
						</li>
					  <?php } ?>
					</ul>
					<div class="tab-content">
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
						
							<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>">
							  <div class="form-group">
								  <label for="title_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Title'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
								  <input type="text" class="form-control" <?php if($n == 1) {?>required="required"<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][title]" id="title_<?php echo $lang_code; ?>">
								</div>
								
								<div class="form-group">
								  <label for="short_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Short Description'); ?></label>
								  <textarea class="form-control short-description-element" maxlength="<?php echo $short_desc_limit; ?>" rows="3" 
								  id="short_description_<?php echo $lang_code; ?>" name="multi_lang[<?php echo $lang_code; ?>][short_description]"></textarea>
								  <span class="rchars" id="rchars_<?php echo $lang_code; ?>"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
								</div>
								
								<div class="form-group">
								  <label for="description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Description'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
								  <textarea class="form-control ckeditor-element" 
								  data-lang_code="<?php echo $lang_code; ?>" data-lang_dir="<?php echo $v['direction']; ?>"   
								  rows="3" id="description_<?php echo $lang_code; ?>" <?php if($n == 1) {?>required<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][description]" ></textarea>
								</div>
								
								<div class="form-group">
								  <label for="meta_keywrod_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Keywords'); ?></label>
								  <input type="text" class="form-control" name="multi_lang[<?php echo $lang_code; ?>][seo_meta_keywords]" id="meta_keywrod_<?php echo $lang_code; ?>" 
								  value="">
								</div>
								
								<div class="form-group">
								  <label for="meta_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Description'); ?></label>
								  <textarea class="form-control" rows="3" id="meta_description_<?php echo $lang_code; ?>" name="multi_lang[<?php echo $lang_code; ?>][seo_meta_description]" ></textarea>
								</div>
							</div>
						<?php } ?>
					</div>
				  </div>
				 
			  <?php }} ?>
			
			<?php }else{ 
				$lang_code = $this->default_language;
			?>
				
				<div class="form-group">
				  <label for="title"><?php echo mlx_get_lang('Title'); ?> <span class="text-red">*</span></label>
				  <input type="text" class="form-control" required="required" name="title" id="title">
				</div>
				
				<div class="form-group">
				  <label for="short_description"><?php echo mlx_get_lang('Short Description'); ?></label>
				  <textarea class="form-control short-description-element" rows="3" id="short_description" name="short_description" 
				   maxlength="<?php echo $short_desc_limit; ?>"></textarea>
				  <span class="rchars" id="rchars"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
				</div>
				
				<div class="form-group">
				  <label for="description"><?php echo mlx_get_lang('Description'); ?> <span class="text-red">*</span></label>
				  <textarea class="form-control ckeditor-element" data-lang_code="<?php echo $lang_code; ?>" rows="3" id="description" required name="description" ></textarea>
				</div>
				
				<div class="form-group">
				  <label for="meta_keywrod"><?php echo mlx_get_lang('Meta Keywords'); ?></label>
				  <input type="text" class="form-control" name="seo_meta_keywords" id="meta_keywrod" 
				  value="">
				</div>
				
				<div class="form-group">
				  <label for="meta_description"><?php echo mlx_get_lang('Meta Description'); ?></label>
				  <textarea class="form-control" rows="3" id="meta_description" name="seo_meta_description" ></textarea>
				</div>
			<?php } ?>
			
			<div class="form-group">
			  <label for="cat_id"><?php echo mlx_get_lang('Category'); ?> <span class="required">*</span></label>
			  
			  <select class="form-control select2_elem" name="cat_id" id="cat_id" required>
				  <option value=""><?php echo mlx_get_lang('Select Any Category'); ?></option>
				  <?php 
				  if(isset($blog_categories) && $blog_categories->num_rows() > 0)
				  {
					  foreach($blog_categories->result() as $b_row)
					  {
						  echo '<option value="'.$myHelpers->global_lib->EncryptClientId($b_row->c_id).'">'.ucfirst($b_row->title).'</option>';
					  }
				  }
				  ?>
			  </select>
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
			<label for="publish_on"><?php echo mlx_get_lang('Publish On'); ?> <span class="text-red">*</span></label>
			<input type="text" class="form-control publish_on" required="required" name="publish_on" readonly id="publish_on" 
			data-format="<?php echo $myHelpers->global_lib->get_option('default_date_format'); ?>"
			value="<?php echo $myHelpers->global_lib->get_date_from_timestamp();?>">
		</div>
		<div class="box-footer">
			<!--
			<button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right submit-form-btn" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
			-->
			<?php if($user_type == 'admin')
			{ 
			?>
			<button type="submit" name="draft" class="btn btn-draft btn-default" id="save_draft"><?php echo mlx_get_lang('Save as Draft'); ?></button>
			<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
			<?php 
			}
			else 
			{
				$has_req = $myHelpers->global_lib->get_option('admin_approval_require_for_blog');
				if($has_req == 'N')
				{
				?>
					<button type="submit" name="draft" class="btn btn-draft btn-default" id="save_draft"><?php echo mlx_get_lang('Save as Draft'); ?></button>
					<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
				<?php
				}
				else
				{
				?>
					<button name="pending" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Submit for Approval'); ?></button>
				<?php
				}
			}
			?>
		</div>
	  </div>

	
	<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		  <div class="box-header with-border">
			  <h3 class="box-title"><?php echo mlx_get_lang('Blog Image'); ?></h3>
			  <div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				
			  </div>
			</div>
		<div class="box-body blog-image-container">
			<div class="pl_image_container">
				<label class="custom-file-upload" data-element_id="" data-type="blog" id="pl_file_uploader_1">
					<?php echo mlx_get_lang('Drop images here'); ?>
					<br />
					<strong><?php echo mlx_get_lang('OR'); ?></strong>
					<br />
					<?php echo mlx_get_lang('Click here to select images'); ?>
				</label>
				<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
				<a class="pl_file_link" href="" download="" style="display:none;">
					<img src="" style="width:50%;">
				</a>
				<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
				<input type="hidden" name="blog_image" value="" class="pl_file_hidden">
			</div>
		</div>
	  </div>
	
  </div>
  
  </div>
	  </form>
</section>
</div>