<?php $default_language = $myHelpers->global_lib->get_option('default_language'); ?>
      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-cog"></i> <?php echo mlx_get_lang('SEO Settings'); ?> 
		  
		  </h1>
          <?php echo validation_errors(); 
			if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
			?>
        </section>
	
		
        <section class="content">
			
			<?php 
				$attributes = array('name' => 'add_form_post','class' => 'form-horizontal front_keyword_settings_form form');		 			
				echo form_open_multipart('',$attributes); 
				
				
			?>
             
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
			<div class="row">
			<div class="col-md-12">   
			 
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
					
				if(!empty($seo_static_page_details))
					$seo_static_page_details = json_decode($seo_static_page_details,true);
				
				//echo "<pre>";print_r($seo_static_page_details);echo "</pre>";	
			  ?>
			  
			    <div class="nav-tabs-custom ">
					
					<ul class="nav nav-tabs">
					  <?php 
						$n=0;
						foreach($site_language_array as $k=>$v) { $n++; 
						$lang_exp = explode('~',$v['language']);
						$lang_code = $lang_exp[1];
						$lang_title = $lang_exp[0];
						
						if($v['status'] != 'enable')
							continue;
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
							//$language = str_replace("~","_TLD_",$v['language']);
							
							$lang_exp = explode('~',$v['language']);
							$lang_code = $lang_exp[1];
							$lang_title = $lang_exp[0];
							
							if($v['status'] != 'enable')
								continue;	
							
							$lang_slug = $myHelpers->global_lib->get_slug($lang_title,'_');
							$language = $lang_slug."_TLD_".$lang_code;
						?>
							  <div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>">
								 
								 <input type="hidden" name="lang_slug" value="<?php echo $lang_slug; ?>">
								 <input type="hidden" name="lang_code" class="lang_code" value="<?php echo $lang_code; ?>">
								
								
						<div class="col-md-12 ">		
						<?php  
						 if(isset($seo_static_pages) && !empty($seo_static_pages)){
							global $content_type,$meta_content;
							$content_type = "seo_static_pages[$language]";
							
							//print_r($seo_static_pages);
							foreach($seo_static_pages as $ksp => $static_page){
								
						?>
							<label for="<?php //echo $row->keyword; ?>" style="width:100%" 
										class=""><?php echo $static_page['title']; ?></label>
							<div class="col-md-12 ">
								 <?php  
								 if(isset($seo_detail_fieds) && !empty($seo_detail_fieds)){
									global $single_field;
									if(is_array($seo_static_page_details)  && 
										array_key_exists($language,$seo_static_page_details)
												&& array_key_exists($ksp,$seo_static_page_details[$language])
											)
									$meta_content = $seo_static_page_details[$language][$ksp];
									foreach($seo_detail_fieds as $kdf => $single_field){
										$single_field['field_id'] = $ksp;
										$value = "";
										//print_r($seo_static_page_details[$language][$ksp]);
										$field_key = $single_field['name'];
										if(is_array($seo_static_page_details)  && array_key_exists($language,$seo_static_page_details))
										{
											if(array_key_exists($ksp,$seo_static_page_details[$language])
												&& array_key_exists($field_key,$seo_static_page_details[$language][$ksp])
											)
											$value = $seo_static_page_details[$language][$ksp][$field_key];
										}
										$this->load->view("$theme/templates/templ-".$single_field['type'] ); 
									}
								 }	
									?>
							 </div>
								 
								 
				<?php			}
							}	
						/*echo "<pre>";
								print_r($seo_static_pages);
								print_r($seo_detail_fieds);
								echo "</pre>";*/	
				?>		 
						</div>		 
								 
								 					  
								  
								  <div class="form-group" >
									<div class="col-sm-offset-3 col-sm-9">
									  
									</div>
								  </div>
								   
								
							  </div>
						<?php } ?>
					</div>
				  </div>
                 
			  <?php }} ?>
		  <button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?>"><?php echo mlx_get_lang('Save'); ?></button>
		  </div>
		  
		  </div>
		  </form>
			
        </section>
      </div>
 