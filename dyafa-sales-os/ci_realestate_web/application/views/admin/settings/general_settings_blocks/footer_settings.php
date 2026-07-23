<?php 
					if(isset($enable_multi_language) && $enable_multi_language == 'Y' && isset($site_language) && !empty($site_language))
					{
						$site_language_array = json_decode($site_language,true);
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
									<a href="#<?php echo 'footer_tab_'.$lang_code; ?>" data-toggle="tab"><?php echo ucfirst($lang_title); ?></a>
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
								
									<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo 'footer_tab_'.	$lang_code; ?>">
									    <div class="form-group">
										  <label for="footer_text_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Footer Text'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
										  <textarea class="form-control wysihtml_editor_elem" rows="3" id="footer_text_<?php echo $lang_code; ?>" 
										  <?php if($n == 1) {?> required <?php } ?> name="multi_lang[<?php echo $lang_code; ?>][footer_text]"  ><?php echo $myHelpers->global_lib->get_option_lang('footer_text',$lang_code); ?></textarea>
										</div>
										
										<div class="form-group">
										  <label for="copyright_text_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Copyright Text'); ?> <?php if($n == 1) {?><span class="text-red">*</span><?php } ?></label>
										  <textarea class="form-control wysihtml_editor_elem" rows="3" id="copyright_text_<?php echo $lang_code; ?>" 
										  <?php if($n == 1) {?> required <?php } ?> name="multi_lang[<?php echo $lang_code; ?>][copyright_text]" ><?php echo $myHelpers->global_lib->get_option_lang('copyright_text',$lang_code); ?></textarea>
										</div>
										
									</div>
								<?php } ?>
							</div>
						  </div>
						
					<?php }else{ ?>
						<div class="form-group">
						  <label for="footer_text"><?php echo mlx_get_lang('Footer Text'); ?></label>
						  <textarea id="footer_text" class="form-control wysihtml_editor_elem" name="options[footer_text]" 
						  rows="3"><?php if(isset($footer_text)) echo $footer_text; ?></textarea>
						</div>
						
						<div class="form-group">
						  <label for="copyright_text"><?php echo mlx_get_lang('Copyright Text'); ?></label>
						  <textarea id="copyright_text" class="form-control wysihtml_editor_elem" name="options[copyright_text]" 
						  rows="3"><?php if(isset($copyright_text)) echo $copyright_text; ?></textarea>
						</div>
					<?php } ?>