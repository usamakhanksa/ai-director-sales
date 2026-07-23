<?php 
	$site_language = $myHelpers->global_lib->get_option('site_language');
	$enable_multi_language = $myHelpers->global_lib->get_option('enable_multi_language');
	$default_language = $myHelpers->global_lib->get_option('default_language');
?>


<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add New Page'); ?> </h1>
  <?php echo validation_errors(); ?>
</section>

<section class="content">
	<?php 
	$attributes = array('name' => 'add_form_post','class' => 'form');		 			
	echo form_open_multipart('admin/page/add_new',$attributes); ?>
	<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
	
	<div class="row">
	<div class="col-md-8">   
	   
	  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo mlx_get_lang('Page Details'); ?></h3>
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
						foreach($site_language_array as $k=>$v) { $n++; 
						$lang_exp = explode('~',$v['language']);
						$lang_code = $lang_exp[1];
						$lang_title = $lang_exp[0];
						?>
						
							<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>">
							  <div class="form-group">
								  <label for="page_title_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Page Title'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
								  <input type="text" class="form-control" <?php if($n == 1) {?>required="required"<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][page_title]" id="page_title_<?php echo $lang_code; ?>">
								</div>
								
								<div class="form-group">
								  <label for="page_content_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Page Content'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
								  <textarea class="form-control ckeditor-element" 
								  data-lang_code="<?php echo $lang_code; ?>" data-lang_dir="<?php echo $v['direction']; ?>"   
								  rows="3" id="page_content_<?php echo $lang_code; ?>" <?php if($n == 1) {?>required<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][page_content]" ></textarea>
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
				  <label for="page_title"><?php echo mlx_get_lang('Page Title'); ?> <span class="text-red">*</span></label>
				  <input type="text" class="form-control" required="required" name="page_title" id="page_title">
				</div>
				
				<div class="form-group">
				  <label for="page_content"><?php echo mlx_get_lang('Page Content'); ?> <span class="text-red">*</span></label>
				  <textarea class="form-control ckeditor-element" data-lang_code="<?php echo $lang_code; ?>" rows="3" id="page_content" required name="page_content" ></textarea>
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
		 <div class="box-footer">
			<button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right submit-form-btn" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
		  </div>
	  </div>

	
  </div>
  
  </div>
	  </form>
</section>
</div>