<div class="form-group" >
							<label for="enable_cookie"><?php echo mlx_get_lang('Enable Cookie'); ?></label>
							 <div class="radio_toggle_wrapper ">
								<input type="radio" id="enbale_cookie_yes" value="Y" 
								data-target="front_end_cookie_yes" data-elem="front_end_cookie_elem"
								<?php 
								if(isset($enable_cookie) && $enable_cookie == 'Y')  
								{ echo ' checked="checked" '; }
								?> name="options[enable_cookie]" 
								class="toggle-radio-button show_hide_setting_elem">
								<label for="enbale_cookie_yes"><?php echo mlx_get_lang('Yes'); ?></label>
								
								<input type="radio" id="enbale_cookie_no" 
								data-target="front_end_cookie_no" data-elem="front_end_cookie_elem"
								<?php 
								if((isset($enable_cookie) && $enable_cookie == 'N')|| 
								!isset($enable_cookie))
								{ echo ' checked="checked" '; }
								?> value="N" name="options[enable_cookie]" 
								class="toggle-radio-button show_hide_setting_elem">
								<label for="enbale_cookie_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>
						
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
							<div class="nav-tabs-custom front_end_cookie_elem front_end_cookie_yes">
								<label><?php echo mlx_get_lang('Cookie Text'); ?></label>
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
										<a href="#<?php echo 'cookie_tab_'.$lang_code; ?>" data-toggle="tab"><?php echo ucfirst($lang_title); ?></a>
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
									
										<div class="<?php if($n == 1) echo 'active'; ?> tab-pane" id="<?php echo 'cookie_tab_'.$lang_code; ?>">
											<div class="form-group">
											  <label for="cookie_text_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Cookie Text'); ?> </label>
											  <textarea class="form-control wysihtml_editor_elem" rows="3" id="cookie_text_<?php echo $lang_code; ?>" 
											   name="multi_lang[<?php echo $lang_code; ?>][cookie_text]" ><?php echo $myHelpers->global_lib->get_option_lang('cookie_text',$lang_code); ?></textarea>
											</div>
											
										</div>
									<?php } ?>
								</div>
							  </div>
							
						<?php }else{ ?>
							<div class="form-group front_end_cookie_elem front_end_cookie_yes">
							  <label><?php echo mlx_get_lang('Cookie Text'); ?></label>
							  <textarea id="cookie_text" class="form-control wysihtml_editor_elem" name="options[cookie_text]" 
							  rows="3"><?php if(isset($cookie_text)) echo $cookie_text; ?></textarea>
							</div>
							
						<?php } ?>