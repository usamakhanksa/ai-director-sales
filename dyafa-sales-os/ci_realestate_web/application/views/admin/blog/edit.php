<?php
	$short_desc_limit = 250;
	$user_type = $this->session->userdata('user_type');
	if(isset($blog_meta) && $blog_meta->num_rows() > 0)
	{
		$row = $blog_meta->row();
		
		$b_id = $row->b_id;
		$blog_user_id = $row->created_by;
		$title = $row->title;
		$slug = $row->slug;
		$status = $row->status;
		$short_description = $row->short_description;
		$description = $row->description;
		$seo_meta_keywords = $row->seo_meta_keywords;
		$seo_meta_description = $row->seo_meta_description;
		$image = $row->image;
		$publish_on = $row->publish_on;
		$cat_id = $row->cat_id;
	}
	
?>
<?php 
	$site_language = 			get_option('site_language');
	$enable_multi_language = 	get_option('enable_multi_language');
	$default_language = 		get_option('default_language');
	
	/**
	$user_type != 'admin'
	**/
?>
   
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-edit"></i> <?php echo mlx_get_lang('Edit Blog'); ?> </h1>
          <?php echo validation_errors(); ?>
		  <?php if($this->post_blog_credit <= 0  && ( !(strpos( $user_type, "admin" ) !== false ))){ ?>
		   <div class="alert alert-warning alert-dismissable show_always" style="margin-top:10px; margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				<?php echo mlx_get_lang('You don\'t have sufficent credits for post blog.'); ?> 
			</div>
		  <?php } ?>
        </section>
		<section class="content">
             <?php 
			 
			 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('admin/blog/edit',$attributes); ?>
			<input type="hidden" name="b_id" class="b_id" value="<?php echo EncryptClientID($b_id); ?>">
			
			<div class="row">
			<div class="col-md-8">   
			   
			  <div class="box box-<?php echo get_skin_class(); ?>">
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
						
						$pld_result = $myHelpers->Common_model->commonQuery("select bld_id,title,short_description,description,seo_meta_keywords,seo_meta_description	
						from blog_lang_details
						where blog_id = $b_id and language = '$lang_code' ");
						$p_title = '';
						$p_desc = '';
						$p_short_description = '';
						$m_keyword = '';
						$m_description = '';
						$lang_blog_id = "";
						if($pld_result->num_rows() > 0)
						{
							$pld_row = $pld_result->row();
							$lang_blog_id = $pld_row->bld_id;
							$p_title = $pld_row->title;
							$p_short_description = $pld_row->short_description;
							$p_desc = $pld_row->description;
							$m_keyword = $pld_row->seo_meta_keywords;
							$m_description = $pld_row->seo_meta_description;
						}
						
						?>
					<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>">
					  
					  <div class="checkbox">
							  <label style="padding-left:0px;">
								<input type="checkbox" class="minimal"
								name="multi_lang[<?php echo $lang_code; ?>][blog_delete]" 
								value="<?php echo $lang_blog_id;?>" />&nbsp;<?php echo mlx_get_lang('Delete This Language Version'); ?>
							  </label>
							</div>
						  
						  <input type="hidden" 
							name="multi_lang[<?php echo $lang_code; ?>][bld_id]" 
							value="<?php echo $lang_blog_id;?>" />
							
					  <div class="form-group">
						  <label for="title_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Page Title'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
						  <input type="text" class="form-control " 
						  <?php if($n == 1) {?>required="required"<?php } ?> 
						  name="multi_lang[<?php echo $lang_code; ?>][title]" 
						  id="title_<?php echo $lang_code; ?>" 
						  value="<?php echo $p_title; ?>">
						</div>
						
						<div class="form-group">
						  <label for="short_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Short Description'); ?></label>
						  <textarea class="form-control short-description-element" maxlength="<?php echo $short_desc_limit; ?>" rows="3" 
						  id="short_description_<?php echo $lang_code; ?>" 
						  name="multi_lang[<?php echo $lang_code; ?>][short_description]"><?php echo $p_short_description; ?></textarea>
						  <span class="rchars" id="rchars_<?php echo $lang_code; ?>"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
						</div>
						
						<div class="form-group">
						  <label for="description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Description'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
						  <textarea class="form-control ckeditor-element" rows="3" 
						  data-lang_code="<?php echo $lang_code; ?>" data-lang_dir="<?php echo $v['direction']; ?>"  
						  id="description_<?php echo $lang_code; ?>" <?php if($n == 1) {?>required<?php } ?> 
						  name="multi_lang[<?php echo $lang_code; ?>][description]" ><?php echo $p_desc; ?></textarea>
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
						$default_currency = $this->site_currency;
						$lang_code = $this->default_language;
					?>
						<div class="form-group">
						  <label for="title"><?php echo mlx_get_lang('Page title'); ?> <span class="text-red">*</span></label>
						  <input type="text" class="form-control" required="required" name="title" id="title" 
						  value="<?php if(isset($title) && !empty($title)) echo $title; ?>" >
						</div>
						
						<div class="form-group">
						  <label for="short_description"><?php echo mlx_get_lang('Short Description'); ?></label>
						  <textarea class="form-control short-description-element" rows="3" id="short_description" name="short_description" 
						   maxlength="<?php echo $short_desc_limit; ?>"><?php if(isset($short_description) && !empty($short_description)) echo $short_description; ?></textarea>
						  <span class="rchars" id="rchars"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
						</div>
						
						<div class="form-group">
						  <label for="description"><?php echo mlx_get_lang('Description'); ?> <span class="text-red">*</span></label>
						  <textarea class="form-control ckeditor-element" rows="3" required="required" 
						  data-lang_code="<?php echo $lang_code; ?>"
						  id="description" name="description" ><?php if(isset($description) && !empty($description)) echo $description; ?></textarea>
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
					
					<div class="form-group">
					  <label for="cat_id"><?php echo mlx_get_lang('Category'); ?> <span class="required">*</span></label>
					  
					  <select class="form-control select2_elem" name="cat_id" id="cat_id" required>
						  <option value=""><?php echo mlx_get_lang('Select Any Category'); ?></option>
						  <?php 
						  if(isset($blog_categories) && $blog_categories->num_rows() > 0)
						  {
							  foreach($blog_categories->result() as $b_row)
							  {
								  echo '<option value="'.EncryptClientID($b_row->c_id).'"';
								  if(isset($cat_id) && $cat_id == $b_row->c_id)
									  echo ' selected="selected" ';
								  echo '>'.ucfirst($b_row->title).'</option>';
							  }
						  }
						  ?>
					  </select>
					</div>
					
				 </div>
                
              </div>
		</div>
		  <div class="col-md-4">
			<div class="box box-<?php echo 	get_skin_class(); ?> "><!--sticky_sidebar-->
			  <div class="box-header with-border">
                  <h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					
				  </div>
                </div>
				<div class="box-body">
				
					<label for="publish_on"><?php echo mlx_get_lang('Publish On'); ?> <span class="text-red">*</span></label>
					<input type="text" class="form-control publish_on" required="required" name="publish_on" readonly id="publish_on" 
					data-format="<?php echo get_option('default_date_format'); ?>" 
					value="<?php echo $myHelpers->global_lib->get_date_from_timestamp('',$publish_on);?>">
					<hr>
						<span > <?php echo mlx_get_lang('URL Slug'); ?>: </span> 
						<input type="text" name="slug" value="<?php if( isset($slug)) echo $slug;?>" class="form-control" />
						<input type="hidden" name="old_slug" value="<?php if( isset($slug)) echo $slug;?>"  />
				</div>
				 <div class="box-footer">
					
					<?php if($user_type == 'admin'  || (strpos( $user_type, "_admin" ) !== false ))
						{ 
							if($status == 'pending'){
							?>
							<input type="hidden" name="blog_user_id" value="<?php if( isset($blog_user_id)) echo $blog_user_id;?>"  />
							<input type="hidden" name="current_status" value="<?php if( isset($status)) echo $status;?>"  />
							<?php }?>			
						<button type="submit" name="draft" class="btn btn-draft btn-default" id="save_draft"><?php echo mlx_get_lang('Save as Draft'); ?></button>
						<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
						<?php 
						}
						else if($status == 'publish')
						{
						?>
							<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Update'); ?></button>
						<?php
						}
						else 
						{
							$has_req = get_option('admin_approval_require_for_blog');
							if($has_req == 'N')
							{
							?>
								<button type="submit" name="draft" class="btn btn-draft btn-default" id="save_draft"><?php echo mlx_get_lang('Save as Draft'); ?></button>
								<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
							<?php
							}
							else
							{
							?>
								<button name="pending" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Submit for Approval'); ?></button>
							<?php
							}
						}
						?>
                  </div>
			  </div>
			  
			<div class="box box-<?php echo get_skin_class(); ?>">
			  <div class="box-header with-border">
				  <h3 class="box-title"><?php echo mlx_get_lang('Blog Image'); ?></h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					
				  </div>
				</div>
				<div class="box-body blog-image-container">
					
					<?php 
					$thumb_photo = $myHelpers->global_lib->get_image_type('uploads/blog/',$image,'thumb'); ?>
					<div class="pl_image_container">
						<label class="custom-file-upload" data-element_id="<?php if(isset($b_id) && !empty($b_id)) echo EncryptClientID($b_id); ?>" data-type="blog" id="pl_file_uploader_1" 
							<?php if(isset($thumb_photo) && !empty($thumb_photo)) { echo 'style="display:none;"';}?>>
							<?php echo mlx_get_lang('Drop images here'); ?>
							<br>
							<strong><?php echo mlx_get_lang('OR'); ?></strong>
							<br>
							<?php echo mlx_get_lang('Click here to select images'); ?>
						</label>
						<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
						<?php if(isset($thumb_photo) && !empty($thumb_photo)) { ?>
						
							<a class="pl_file_link" href="<?php echo base_url().'uploads/blog/'.$image; ?>" 
							download="<?php echo $image; ?>" style="">
								<img src="<?php echo base_url().'uploads/blog/'.$thumb_photo; ?>"  style="width:50%;">
							</a>
						
							<a class="pl_file_remove_img" title="Remove Image" href="#"><i class="fa fa-remove"></i></a>
						<?php }else{ ?>
							<a class="pl_file_link" href="" download="" style="display:none;">
								<img src=""  style="width:50%;">
							</a>
							<a class="pl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
						<?php } ?>
						<input type="hidden" name="blog_image" value="<?php if(isset($image) && !empty($image)) { echo $image;}?>" 
						class="pl_file_hidden">
					</div>
				</div>
		  </div>  
		  </div>
		  
		  
		  </div>
			  </form>
        </section>
      </div>