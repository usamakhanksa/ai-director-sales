<?php
	
	if(isset($page_meta) && $page_meta->num_rows() > 0)
	{
		$row = $page_meta->row();
		
		$page_id = $row->page_id;
		$page_title = $row->page_title;
		$page_content = $row->page_content;
		$seo_meta_keywords = $row->seo_meta_keywords;
		$seo_meta_description = $row->seo_meta_description;
		$page_sidebar = $row->page_sidebar;
		$page_slug = $row->page_slug;
	}
	
?>
<?php 
	$site_language = $myHelpers->global_lib->get_option('site_language');
	$enable_multi_language = $myHelpers->global_lib->get_option('enable_multi_language');
	$default_language = $myHelpers->global_lib->get_option('default_language');
?>
   
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-edit"></i> <?php echo mlx_get_lang('Edit Page'); ?> </h1>
          <?php echo validation_errors(); ?>
        </section>
		<section class="content">
             <?php 
			 
			 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('admin/page/edit',$attributes); ?>
			<input type="hidden" name="page_id" class="page_id" value="<?php echo $myHelpers->EncryptClientId($page_id); ?>">
			
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
						
						$pld_result = $myHelpers->Common_model->commonQuery("select pld_id,title,description,seo_meta_keywords,seo_meta_description	
						from page_lang_details
						where page_id = $page_id and language = '$lang_code' ");
						$p_title = '';
						$p_desc = '';
						$m_keyword = '';
						$m_description = '';
						$lang_page_id = "";
						if($pld_result->num_rows() > 0)
						{
							$pld_row = $pld_result->row();
							$lang_page_id = $pld_row->pld_id;
							$p_title = $pld_row->title;
							$p_desc = $pld_row->description;
							$m_keyword = $pld_row->seo_meta_keywords;
							$m_description = $pld_row->seo_meta_description;
						}
						
						?>
						<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>">
						  <div class="checkbox">
							  <label style="padding-left:0px;">
								<input type="checkbox" class="minimal"
							name="multi_lang[<?php echo $lang_code; ?>][page_delete]" 
							value="<?php echo $lang_page_id;?>" />&nbsp; <?php echo mlx_get_lang('Delete This Language Version'); ?>
							  </label>
							</div>
						  
						  <input type="hidden" 
							name="multi_lang[<?php echo $lang_code; ?>][pld_id]" 
							value="<?php echo $lang_page_id;?>" />
							
						  <div class="form-group">
							  <label for="page_title_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Page Title'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
							  <input type="text" class="form-control " 
							  <?php if($n == 1) {?>required="required"<?php } ?> 
							  name="multi_lang[<?php echo $lang_code; ?>][page_title]" 
							  id="page_title_<?php echo $lang_code; ?>" 
							  value="<?php echo $p_title; ?>">
							</div>
							
							<div class="form-group">
							  <label for="page_content_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Page Content'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
							  <textarea class="form-control ckeditor-element" rows="3" 
							  data-lang_code="<?php echo $lang_code; ?>" data-lang_dir="<?php echo $v['direction']; ?>"  
							  id="page_content_<?php echo $lang_code; ?>" <?php if($n == 1) {?>required<?php } ?> 
							  name="multi_lang[<?php echo $lang_code; ?>][page_content]" ><?php echo $p_desc; ?></textarea>
							</div>
							
							<div class="form-group">
							  <label for="meta_keywrod_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Keywords'); ?></label>
							  <input type="text" class="form-control" name="multi_lang[<?php echo $lang_code; ?>][seo_meta_keywords]" id="meta_keywrod_<?php echo $lang_code; ?>" 
							  value="<?php echo $m_keyword; ?>">
							</div>
							
							<div class="form-group">
							  <label for="meta_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Description'); ?></label>
							  <textarea class="form-control" rows="3" id="meta_description_<?php echo $lang_code; ?>" 
							  name="multi_lang[<?php echo $lang_code; ?>][seo_meta_description]" ><?php echo $m_description; ?></textarea>
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
						  <label for="page_title"><?php echo mlx_get_lang('Page title'); ?> <span class="text-red">*</span></label>
						  <input type="text" class="form-control" required="required" name="page_title" id="page_title" 
						  value="<?php if(isset($page_title) && !empty($page_title)) echo $page_title; ?>" >
						</div>
						
						<div class="form-group">
						  <label for="page_content"><?php echo mlx_get_lang('Page Content'); ?> <span class="text-red">*</span></label>
						  <textarea class="form-control ckeditor-element" rows="3" required="required" 
						  data-lang_code="<?php echo $lang_code; ?>"
						  id="page_content" name="page_content" ><?php if(isset($page_content) && !empty($page_content)) echo $page_content; ?></textarea>
						</div>
						
						<div class="form-group">
						  <label for="meta_keywrod"><?php echo mlx_get_lang('Meta Keywords'); ?></label>
						  <input type="text" class="form-control" name="seo_meta_keywords" id="meta_keywrod" 
						  value="<?php if(isset($seo_meta_keywords) && !empty($seo_meta_keywords)) echo $seo_meta_keywords; ?>">
						</div>
						
						<div class="form-group">
						  <label for="meta_description"><?php echo mlx_get_lang('Meta Description'); ?></label>
						  <textarea class="form-control" rows="3" id="meta_description" name="seo_meta_description" ><?php if(isset($seo_meta_description) && !empty($seo_meta_description)) echo $seo_meta_description; ?></textarea>
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
				  <div> 
				  <hr>
						<span > <?php echo mlx_get_lang('URL Slug'); ?>: </span> 
						<input type="text" name="page_slug" value="<?php if( isset($page_slug)) echo $page_slug;?>" class="form-control" />
						<input type="hidden" name="old_slug" value="<?php if( isset($page_slug)) echo $page_slug;?>"  />
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