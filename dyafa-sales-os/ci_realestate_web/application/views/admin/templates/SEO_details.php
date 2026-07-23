<div class="box box-primary">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo mlx_get_lang('SEO Keywords'); ?></h3>
	  <div class="box-tools pull-right">
		<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		
	  </div>
	</div>
	<div class="box-body">
		<?php 
		$enable_multi_language = $myHelpers->global_lib->get_option('enable_multi_language');
		$site_language = $myHelpers->global_lib->get_option('site_language');
		?>
		<?php 
		if(isset($enable_multi_language) && $enable_multi_language == 'Y' && isset($site_language) && !empty($site_language))
		{
			$site_language_array = json_decode($site_language,true);
		?>
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
				  <?php 
					$n=0;
					foreach($site_language_array as $k=>$v) { $n++; 
					$lang_exp = explode('~',$v['language']);
					$lang_code = $lang_exp[1];
					$lang_title = $lang_exp[0];
					?>
					<li <?php if($n == 1) echo 'class="active"'; ?>>
						<a href="#<?php echo $lang_code; ?>_meta" data-toggle="tab"><?php echo ucfirst($lang_title); ?></a>
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
					
						<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>_meta">
							<div class="form-group">
							  <label for="meta_keywrod_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Keywords'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
							  <input type="text" class="form-control" id="meta_keywrod_<?php echo $lang_code; ?>" 
							  <?php if($n == 1) {?>required<?php } ?> name="meta_multi_lang[<?php echo $lang_code; ?>][meta_keywrod]" value="<?php //echo $myHelpers->global_lib->get_option_lang('meta_keywrod',$lang_code); ?>">
							</div>
							
							<div class="form-group">
							  <label for="meta_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Description'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
							  <textarea class="form-control" rows="3" id="meta_description_<?php echo $lang_code; ?>" 
							  <?php if($n == 1) {?>required<?php } ?> name="meta_multi_lang[<?php echo $lang_code; ?>][meta_description]" ><?php //echo $myHelpers->global_lib->get_option_lang('meta_description',$lang_code); ?></textarea>
							</div>
							
						</div>
					<?php } ?>
				</div>
			  </div>
			
		<?php }else{ ?>
			<div class="form-group">
			  <label for="meta_keywrod"><?php echo mlx_get_lang('Meta Keywords'); ?></label>
			  <input type="text" class="form-control" name="seo_meta_keywords" id="meta_keywrod" 
			  value="<?php if(isset($seo_meta_keywords) && !empty($seo_meta_keywords)) echo $seo_meta_keywords;?>">
			</div>
			
			<div class="form-group">
			  <label for="meta_description"><?php echo mlx_get_lang('Meta Description'); ?></label>
			  <textarea class="form-control" rows="3" id="meta_description" name="seo_meta_description" ><?php if(isset($seo_meta_description) && !empty($seo_meta_description)) echo $seo_meta_description;?></textarea>
			</div>
		<?php } ?>
	</div>
	 
</div>