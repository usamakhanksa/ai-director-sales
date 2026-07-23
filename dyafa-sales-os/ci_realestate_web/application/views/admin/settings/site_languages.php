
      <div class="content-wrapper">
        <?php 
		$attributes = array('name' => 'add_form_post','class' => 'form site_language_form');		 			
		echo form_open_multipart('',$attributes); 
		?>
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-cog"></i>  <?php echo mlx_get_lang('Site Language'); ?> </h1>
		  <h4 style="margin-bottom:0px;"> <?php echo mlx_get_lang('Enable Multi Language');?> :
		  <small style="vertical-align: middle; display: inline-block;">
			<div class="radio_toggle_wrapper">
				<input type="radio" id="Y" value="Y" 
				<?php 
				if(isset($enable_multi_language) && $enable_multi_language == 'Y') { echo ' checked="checked" '; }
				?> name="options[enable_multi_language]" 
				class="toggle-radio-button">
				<label for="Y"><?php echo mlx_get_lang('Enable'); ?></label>
				
				<input type="radio" id="N" 
				<?php 
				if((isset($enable_multi_language) && $enable_multi_language == 'N') || $enable_multi_language == '')
				{ echo ' checked="checked" '; }
				?> value="N" name="options[enable_multi_language]" 
				class="toggle-radio-button">
				<label for="N"><?php echo mlx_get_lang('Disable'); ?></label>
			</div>
		</small>
		</h4>
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
			
			
			if(isset($site_language) && !empty($site_language)){
				$site_language_array = json_decode($site_language,true);
				/*
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
				*/
			}
			
			$default_language = get_option('default_language');
			
			
			
			if( is_array($site_language_array) &&  isset($site_language_array[1]))
				$first_language = $site_language_array[1];
			else	
			{	
				$first_language = array(
									'language' => "English~en" ,
									'currency' => "USD" ,
									'direction' => "ltr" ,
									'timezone' => "Asia/Kolkata" ,
									'status' => "enable", 	
									'currency_pos' => 'left',
									'thousand_sep' => ',',
									'decimal_sep' => '.',
									'num_decimals' => '2',
									
									);
			}						
			
				
			
			/*if(!isset($first_language['currency_pos'])) $first_language['currency_pos'] = 'left'; */
			if(!array_key_exists( 'currency_pos', $first_language)) $first_language['currency_pos'] = 'left'; 
			if(!array_key_exists( 'thousand_sep', $first_language)) $first_language['thousand_sep'] = ','; 
			if(!array_key_exists( 'decimal_sep', $first_language)) $first_language['decimal_sep'] = '.'; 
			if(!array_key_exists( 'num_decimals', $first_language)) $first_language['num_decimals'] = '2'; 
			
			/*if(!isset($first_language['thousand_sep'])) $first_language['thousand_sep'] = ','; 
			if(!isset($first_language['decimal_sep'])) $first_language['decimal_sep'] = '.'; 
			if(!isset($first_language['num_decimals'])) $first_language['num_decimals'] = '2'; 		*/
			
			?>
             
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
			<div class="row">
			<div class="col-md-12">   
			 
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
					
                  <div class="box-body lang-container">
                    
					<div class="single-lang-block"> 
						<div class="row">
							<div class="col-md-2">
								<div class="row">
									<div class="col-md-12 text-center">
										<label for="default_lang_1" class="">
										  <?php echo mlx_get_lang('Default Language'); ?>
										 </label><br>
										 <input type="radio" class="minimal" id="default_lang_1"  name="options[default_language]" value="<?php echo $first_language['language'];?>" 
						<?php 
							if($default_language == $first_language['language'])
								echo ' checked="checked" '; 
						?>>
									</div>
								</div>
							</div>
							<div class="col-md-10">
								<div class="row">
									
									<div class="col-md-4">
										
										  <label for="language_1"><?php echo mlx_get_lang('Language'); ?> <span class="required">*</span></label>
										  
										  <select name="options[site_language][1][language]"  id="language_1" required class=" select2_elem language_list form-control">
											<?php 
											if(isset($languages) && !empty($languages))
											{
												foreach($languages as $k=>$v)
												{
													
													echo '<option value="'.$v.'~'.$k.'"';
														if($first_language['language'] == $v.'~'.$k )
															echo ' selected="selected" ';
													echo '>'.ucfirst($v).' ('.$k.')</option>';
												}
											}
											?>
										  </select>
									</div>
									
									<div class="col-md-4">
										  <label for="timezone_1"><?php echo mlx_get_lang('Timezone'); ?></label>
										  <?php $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
												$timezone_offsets = array();
												foreach( $timezones as $timezone )
												{
													$tz = new DateTimeZone($timezone);
													$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
												}

												asort($timezone_offsets);
												$timezone_list = array();
												foreach( $timezone_offsets as $timezone => $offset )
												{
													$offset_prefix = $offset < 0 ? '-' : '+';
													$offset_formatted = gmdate( 'H:i', abs($offset) );

													$pretty_offset = "UTC${offset_prefix}${offset_formatted}";

													$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
												}
										  ?>
										<select class="form-control select2_elem" id="timezone_1" name="options[site_language][1][timezone]">
											  <option value="" selected="selected"><?php echo mlx_get_lang('Select Any Timezone'); ?></option>
											  <?php if($timezone_list) { 
													foreach($timezone_list as $timezonekey=>$timezonevalue) {
													 $timezone_selected = "";
													 /*if(isset($site_language_array) && isset($site_language_array[1]['timezone']) && $timezonekey == $site_language_array[1]['timezone'])
														 $timezone_selected = "selected=selected";*/
													if($first_language['timezone'] == $timezonekey  )	
														 $timezone_selected = "selected=selected";
													?>
													<option value="<?php echo $timezonekey; ?>" <?php echo $timezone_selected; ?>><?php echo $timezonevalue; ?></option>
											  <?php } } ?>
										</select>
										  
									</div>
									
									
									
									<div class="col-md-4">
											<label for="direction_1" style="width:100%;"><?php echo mlx_get_lang('Direction'); ?></label>
											 
											 <div class="radio_toggle_wrapper" style="display:inline-block; width:auto;">
												<input type="radio" id="ltr_1" value="ltr" 
												<?php 
												if($first_language['direction'] == 'ltr' )
													echo ' checked="checked" ';
												?> name="options[site_language][1][direction]" 
												class="toggle-radio-button">
												<label for="ltr_1"><?php echo mlx_get_lang('LTR'); ?></label>
												
												<input type="radio" id="rtl_1" 
												<?php 
												if($first_language['direction'] == 'rtl' )
													echo ' checked="checked" ';
												?>
												 value="rtl" name="options[site_language][1][direction]" 
												class="toggle-radio-button">
												<label for="rtl_1"><?php echo mlx_get_lang('RTL'); ?></label>
											</div>
											
											<a class="btn btn-danger pull-right flip remove-lang-block
											<?php 
												if($default_language == $first_language['language'])
													echo ' disabled '; 
											?>"><i class="fa fa-remove"></i></a>
											 
									</div>
									
									<div class="col-md-4">
										  <label for="currency_1"><?php echo mlx_get_lang('Currency'); ?> <span class="required">*</span></label>
										  <select class="form-control select2_elem"  name="options[site_language][1][currency]" id="currency_1" >
											<?php if(isset($currency_symbols) && !empty($currency_symbols)) {
												foreach($currency_symbols as $k=>$v)
												{
													
													echo '<option value="'.$k.'"';
													if($first_language['currency'] == $k )
														echo ' selected="selected" ';
													echo '>'.$k.' - '.$v.'</option>';
												}
											}
											?>
										  </select>
									</div>
									
										<?php
										$currency_positions = array();
										$currency_positions ['left'] = "Left";
										$currency_positions ['left_space'] = "Left with Space";
										$currency_positions ['right'] = "Right";
										$currency_positions ['right_space'] = "Right with Space";
										
										?>

									<div class="col-md-4">
										  <label for="currency_pos_1"><?php echo mlx_get_lang('Currency Position'); ?></label>
										  <select class="form-control select2_elem" name="options[site_language][1][currency_pos]" 
										  id="currency_pos_1" >
											<?php if(isset($currency_positions) && !empty($currency_positions)) {
												foreach($currency_positions as $k=>$v)
												{
													echo '<option value="'.$k.'"';
													if($first_language['currency_pos'] == $k)
														echo ' selected="selected" ';
													echo '>'.mlx_get_lang($v).'</option>';
												}
											}
											?>
										  </select>
										  
									</div>
									
									<div class="col-md-4">
											<label for="status_1" style="width:100%;"><?php echo mlx_get_lang('Status'); ?></label>
											 
											<div class="radio_toggle_wrapper" style="display:inline-block; width:auto;">
												<input type="radio" id="status_enable_1" value="enable" 
												<?php 
												/*if((isset($site_language_array) && isset($site_language_array[1]['status']) && 'enable' == $site_language_array[1]['status'])
													|| !isset($site_language_array[1]['status']))
												{
													echo ' checked="checked" ';
												}*/
												if($first_language['status'] == 'enable' )
													echo ' checked="checked" ';
												?> name="options[site_language][1][status]" 
												class="toggle-radio-button" >
												<label for="status_enable_1" class="status_lbl <?php 
												if($default_language == $first_language['language'])
													echo ' disabled '; 
											?>"><?php echo mlx_get_lang('Enable'); ?></label>
												
												<input type="radio" id="status_disable_1" 
												 value="disable" name="options[site_language][1][status]" 
												 <?php 
												/*if(isset($site_language_array) && isset($site_language_array[1]['status']) && 'disable' == $site_language_array[1]['status'])
												{
													echo ' checked="checked" ';
												}*/
												if($first_language['status'] == 'disable' )
													echo ' checked="checked" ';
												?>
												class="toggle-radio-button  " >
												<label for="status_disable_1" class="status_lbl <?php 
												if($default_language == $first_language['language'])
													echo ' disabled '; 
											?>"><?php echo mlx_get_lang('Disable'); ?></label>
											</div>
											
											 
									</div>
									
									<div class="col-md-4">
									  <label for="thousand_sep_1"><?php echo mlx_get_lang('Thousand Separator for Currecny'); ?></label><br>
									  <input type="text" class="form-control thousand_sep" 
									  name="options[site_language][1][thousand_sep]" id="thousand_sep_1" placeholder="<?php echo mlx_get_lang('Enter Thousand Separator'); ?>" 
									  value="<?php 
									  if(isset($first_language['thousand_sep'])) echo $first_language['thousand_sep']; 
									  else echo ","; ?>">
									  
									</div>
									
									<div class="col-md-4">
									  <label for="decimal_sep_1"><?php echo mlx_get_lang('Decimal separator for Currecny'); ?></label><br>
									  <input type="text" class="form-control decimal_sep" 
									  name="options[site_language][1][decimal_sep]" id="decimal_sep_1" placeholder="<?php echo mlx_get_lang('Enter Decimal Separator'); ?>" 
									  value="<?php 
									  if(isset($first_language['decimal_sep'])) echo $first_language['decimal_sep']; 
									  else echo "."; ?>">
									  
									</div>
									
									<div class="col-md-4">
									  <label for="num_decimals_1"><?php echo mlx_get_lang('Number of decimals for Currecny'); ?></label><br>
									  <input type="number" class="form-control num_decimals" step="1"
									  name="options[site_language][1][num_decimals]" id="num_decimals_1" placeholder="<?php echo mlx_get_lang('Enter Number of Decimals'); ?>" 
									  value="<?php 
									  if(isset($first_language['num_decimals'])) echo $first_language['num_decimals']; 
									  else echo "2"; ?>">
									  
									</div>
									
									
									
								</div>
							</div>
							
						</div>
					</div>
					
					<?php 
					
					if(isset($site_language_array) && is_array($site_language_array) && !empty($site_language_array)){
						$n=0;
						
						
						foreach($site_language_array as $slk=>$slv)
						{
							$n++;
							if($n==1)
								continue;
							if(!isset($slv['language'])) continue;
					?>
					
						<div class="single-lang-block"> 
							<div class="row">
								<div class="col-md-2">
									<div class="row">
										<div class="col-md-12 text-center">
											
											 <label for="default_lang_<?php echo $n; ?>"  class="">
											  <?php echo mlx_get_lang('Default Language'); ?>
											 </label><br>
											 <input type="radio" class="minimal" required id="default_lang_<?php echo $n; ?>" 
											  <?php if(isset($default_language) && $default_language == $slv['language']) echo ' checked="checked" '; ?>
											  name="options[default_language]" value="<?php echo $slv['language']; ?>">
										</div>
									</div>
								</div>
								<div class="col-md-10">
									<div class="row">
										
										<div class="col-md-4">
											
											  <label for="language_<?php echo $n; ?>"><?php echo mlx_get_lang('Language'); ?> <span class="required">*</span></label>
									  
											  <select name="options[site_language][<?php echo $n; ?>][language]"  id="language_<?php echo $n; ?>" required class="select2_elem language_list form-control">
												<option value=""><?php echo mlx_get_lang('Select Any Language'); ?></option>
												<?php 
												if(isset($languages) && !empty($languages))
												{
													foreach($languages as $k=>$v)
													{
														
														echo '<option value="'.$v.'~'.$k.'"';
														if($slv['language'] == $v.'~'.$k)
															echo ' selected="selected" ';
														echo '>'.ucfirst($v).' ('.$k.')</option>';
													}
												}
												?>
											  </select>
										</div>
										
										<div class="col-md-4">
											  <label for="timezone_<?php echo $n; ?>"><?php echo mlx_get_lang('Timezone'); ?></label>
											  <?php $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
													$timezone_offsets = array();
													foreach( $timezones as $timezone )
													{
														$tz = new DateTimeZone($timezone);
														$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
													}

													asort($timezone_offsets);
													$timezone_list = array();
													foreach( $timezone_offsets as $timezone => $offset )
													{
														$offset_prefix = $offset < 0 ? '-' : '+';
														$offset_formatted = gmdate( 'H:i', abs($offset) );

														$pretty_offset = "UTC${offset_prefix}${offset_formatted}";

														$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
													}
											  ?>
											<select class="form-control select2_elem" id="timezone_<?php echo $n; ?>" 
											name="options[site_language][<?php echo $n; ?>][timezone]">
												  <option value="" selected="selected"><?php echo mlx_get_lang('Select Any Timezone'); ?></option>
												  <?php if($timezone_list) { 
														foreach($timezone_list as $timezonekey=>$timezonevalue) {
														 $timezone_selected = "";
														 if(isset($slv['timezone']) && $timezonekey == $slv['timezone'])
															 $timezone_selected = "selected=selected";
														?>
														<option value="<?php echo $timezonekey; ?>" <?php echo $timezone_selected; ?>><?php echo $timezonevalue; ?></option>
												  <?php } } ?>
											</select>
											  
										</div>
										
										
										
										
										
										
										<div class="col-md-4">
												<label for="direction_<?php echo $n; ?>" style="width:100%;"><?php echo mlx_get_lang('Direction'); ?></label>
												 <div class="radio_toggle_wrapper" style="display:inline-block; width:auto;">
													<input type="radio" id="ltr_<?php echo $n; ?>" value="ltr" 
													<?php if($slv['direction'] == 'ltr') echo ' checked="checked" '; ?>
													name="options[site_language][<?php echo $n; ?>][direction]" 
													class="toggle-radio-button">
													<label for="ltr_<?php echo $n; ?>"><?php echo mlx_get_lang('LTR'); ?></label>
													
													<input type="radio" id="rtl_<?php echo $n; ?>" 
													<?php if($slv['direction'] == 'rtl') echo ' checked="checked" '; ?>
													 value="rtl" name="options[site_language][<?php echo $n; ?>][direction]" 
													class="toggle-radio-button">
													<label for="rtl_<?php echo $n; ?>"><?php echo mlx_get_lang('RTL'); ?></label>
												</div>
												
												<a class="btn btn-danger pull-right flip remove-lang-block 
												<?php if($default_language == $slv['language']) echo ' disabled '; ?>
												" 
												<?php if(count($site_language_array) == 1) { echo 'style="display:none;"'; } ?>><i class="fa fa-remove"></i></a>
												 
										</div>
										
										
										<div class="col-md-4">
											  <label for="currency_<?php echo $n; ?>"><?php echo mlx_get_lang('Currency'); ?> <span class="required">*</span></label>
											  <select class="form-control currency_list select2_elem" required name="options[site_language][<?php echo $n; ?>][currency]" 
											  id="currency_<?php echo $n; ?>" >
												<option value=""><?php echo mlx_get_lang('Select Any Currency'); ?></option>
												<?php if(isset($currency_symbols) && !empty($currency_symbols)) {
													foreach($currency_symbols as $k=>$v)
													{
														echo '<option value="'.$k.'"';
														if($slv['currency'] == $k)
															echo ' selected="selected" ';
														echo '>'.$k.' - '.$v.'</option>';
													}
												}
												?>
											  </select>
										</div>
										
										
										<div class="col-md-4">
											  <label for="currency_pos"><?php echo mlx_get_lang('Currency Position'); ?></label>
											  <select class="form-control select2_elem" 
											  name="options[site_language][<?php echo $n; ?>][currency_pos]" 
											  id="currency_<?php echo $n; ?>" >
												<?php if(isset($currency_positions) && !empty($currency_positions)) {
													foreach($currency_positions as $k=>$v)
													{
														echo '<option value="'.$k.'"';
														if(isset($slv['currency_pos']) && $slv['currency_pos'] == $k)
															echo ' selected="selected" ';
														echo '>'.mlx_get_lang($v).'</option>';
													}
												}
												?>
											  </select>
											  
										</div>
										
										<div class="col-md-4">
												<label for="status_<?php echo $n; ?>" style="width:100%;"><?php echo mlx_get_lang('Status'); ?></label>
												 
												 
												<div class="radio_toggle_wrapper" style="display:inline-block; width:auto;">
													<input type="radio" id="status_enable_<?php echo $n; ?>" value="enable" 
													<?php if((isset($slv['status']) && $slv['status'] == 'enable') || !isset($slv['status'])) echo ' checked="checked" '; ?>
													name="options[site_language][<?php echo $n; ?>][status]" 
													class="toggle-radio-button">
													<label for="status_enable_<?php echo $n; ?>" class="status_lbl <?php if($default_language == $slv['language']) echo ' disabled '; ?>"><?php echo mlx_get_lang('Enable'); ?></label>
													
													<input type="radio" id="status_disable_<?php echo $n; ?>" 
													<?php if(isset($slv['status']) && $slv['status'] == 'disable') echo ' checked="checked" '; ?>
													 value="disable" name="options[site_language][<?php echo $n; ?>][status]" 
													class="toggle-radio-button">
													<label for="status_disable_<?php echo $n; ?>" class="status_lbl <?php if($default_language == $slv['language']) echo ' disabled '; ?>"><?php echo mlx_get_lang('Disable'); ?></label>
												</div>
												
												 
										</div>
										
										
										<div class="col-md-4">
										  <label for="thousand_sep"><?php echo mlx_get_lang('Thousand Separator for Currecny'); ?></label><br>
										  <input type="text" class="form-control thousand_sep" 
										  name="options[site_language][<?php echo $n; ?>][thousand_sep]" 
										  id="thousand_sep_<?php echo $n; ?>" placeholder="Enter Thousand Separator" 
										  value="<?php if(isset($slv['thousand_sep'])) echo $slv['thousand_sep']; else echo ","; ?>">
										  
										</div>
										
										<div class="col-md-4">
										  <label for="decimal_sep"><?php echo mlx_get_lang('Decimal separator for Currecny'); ?></label><br>
										  <input type="text" class="form-control decimal_sep" 
										  name="options[site_language][<?php echo $n; ?>][decimal_sep]" 
										  id="decimal_sep_<?php echo $n; ?>" placeholder="Enter Decimal separator" 
										  value="<?php if(isset($slv['decimal_sep'])) echo $slv['decimal_sep']; else echo "."; ?>">
										  
										</div>
										
										<div class="col-md-4">
										  <label for="num_decimals"><?php echo mlx_get_lang('Number of decimals for Currecny'); ?></label><br>
										  <input type="number" class="form-control num_decimals" step="1"
										  name="options[site_language][<?php echo $n; ?>][num_decimals]" 
										  id="num_decimals_<?php echo $n; ?>" placeholder="Enter Number of decimals" 
										  value="<?php if(isset($slv['num_decimals'])) echo $slv['num_decimals']; else echo "2"; ?>">
										  
										</div>
										
										
										
										
										
										
										
										
										
									</div>
								</div>
								
							</div>
						</div>
					
						
					<?php 
						}
					}
					?>
                  </div>
				  
				  <div class="box-footer">
						<a class="btn btn-default add-lang-btn" ><?php echo mlx_get_lang('Add More Language'); ?></a>
						<button name="submit" type="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right flip" id="save_publish"><?php echo mlx_get_lang('Save Changes'); ?></button>
				  </div>
				  
              </div>
			  
			  
		  </div>
		  
		  </div>
		  
			
        </section>
		</form>
      </div>

<div class="single-lang-block hide default-lang-block"> 
	<div class="row">
		<div class="col-md-2">
			<div class="row">
				<div class="col-md-12 text-center">
					<label for="default_lang_0" class="">
					  <?php echo mlx_get_lang('Default Language'); ?>
					 </label><br>
					 <input type="radio" class="minimal" id="default_lang_0"  name="options[default_language]" value="" >
				</div>
			</div>
		</div>
		<div class="col-md-10">
			<div class="row">
				
				<div class="col-md-4">
					
					  <label for="language_0"><?php echo mlx_get_lang('Language'); ?> <span class="required">*</span></label>
					  
					  <select name="options[site_language][0][language]"  id="language_0" required class=" language_list form-control">
						<option value=""><?php echo mlx_get_lang('Select Any Language'); ?></option>
						<?php 
						if(isset($languages) && !empty($languages))
						{
							foreach($languages as $k=>$v)
							{
								echo '<option value="'.$v.'~'.$k.'"';
								echo '>'.ucfirst($v).' ('.$k.')</option>';
							}
						}
						?>
					  </select>
				</div>
				
				<div class="col-md-4">
					  <label for="timezone_1"><?php echo mlx_get_lang('Timezone'); ?></label>
					  <?php $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
							$timezone_offsets = array();
							foreach( $timezones as $timezone )
							{
								$tz = new DateTimeZone($timezone);
								$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
							}

							asort($timezone_offsets);
							$timezone_list = array();
							foreach( $timezone_offsets as $timezone => $offset )
							{
								$offset_prefix = $offset < 0 ? '-' : '+';
								$offset_formatted = gmdate( 'H:i', abs($offset) );

								$pretty_offset = "UTC${offset_prefix}${offset_formatted}";

								$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
							}
					  ?>
					<select class="form-control timezone_list" id="timezone_0" name="options[site_language][0][timezone]">
						  <option value="" selected="selected"><?php echo mlx_get_lang('Select Any Timezone'); ?></option>
						  <?php if($timezone_list) { 
								foreach($timezone_list as $timezonekey=>$timezonevalue) {
								 $timezone_selected = "";
								 ?>
								<option value="<?php echo $timezonekey; ?>" <?php echo $timezone_selected; ?>><?php echo $timezonevalue; ?></option>
						  <?php } } ?>
					</select>
					  
				</div>
				
				
				
				<div class="col-md-4">
					<label for="direction_0" style="width:100%;"><?php echo mlx_get_lang('Direction'); ?></label>
					 
					 <div class="radio_toggle_wrapper" style="display:inline-block; width:auto;">
						<input type="radio" id="ltr_0" value="ltr" checked="checked"
						name="options[site_language][0][direction]" 
						class="toggle-radio-button">
						<label for="ltr_1"><?php echo mlx_get_lang('LTR'); ?></label>
						
						<input type="radio" id="rtl_0" 
						 value="rtl" name="options[site_language][0][direction]" 
						class="toggle-radio-button">
						<label for="rtl_0"><?php echo mlx_get_lang('RTL'); ?></label>
					</div>
					
					<a class="btn btn-danger pull-right flip remove-lang-block"><i class="fa fa-remove"></i></a>
						 
				</div>
				
				
				
				<div class="col-md-4">
					  <label for="currency_0"><?php echo mlx_get_lang('Currency'); ?> <span class="required">*</span></label>
					  <select class="form-control currency_list"  name="options[site_language][0][currency]" id="currency_0" >
						<option value=""><?php echo mlx_get_lang('Select Any Currency'); ?></option>
						<?php if(isset($currency_symbols) && !empty($currency_symbols)) {
							foreach($currency_symbols as $k=>$v)
							{
								echo '<option value="'.$k.'"';
								echo '>'.$k.' - '.$v.'</option>';
							}
						}
						?>
					  </select>
				</div>
				
				
				
				<div class="col-md-4">
					  <label for="currency_pos_0"><?php echo mlx_get_lang('Currency Position'); ?></label>
					  <select class="form-control currency_pos_list" name="options[site_language][0][currency_pos]" id="currency_pos_0" >
						<?php if(isset($currency_positions) && !empty($currency_positions)) {
							foreach($currency_positions as $k=>$v)
							{
								echo '<option value="'.$k.'"';
								//if(isset($currency_pos) && $currency_pos == $k)
									//echo ' selected="selected" ';
								echo '>'.mlx_get_lang($v).'</option>';
							}
						}
						?>
					  </select>
					  
				</div>
				
				
				<div class="col-md-4">
						<label for="status_0" style="width:100%;"><?php echo mlx_get_lang('Status'); ?></label>
						 
						<div class="radio_toggle_wrapper" style="display:inline-block; width:auto;">
							<input type="radio" id="status_enable_0" value="enable" 
							checked="checked" name="options[site_language][0][status]" 
							class="toggle-radio-button">
							<label for="status_enable_0"><?php echo mlx_get_lang('Enable'); ?></label>
							
							<input type="radio" id="status_disable_0" 
							 value="disable" name="options[site_language][0][status]" 
							class="toggle-radio-button">
							<label for="status_disable_0"><?php echo mlx_get_lang('Disable'); ?></label>
						</div>
						
						 
				</div>
				
				<div class="col-md-4">
				  <label for="thousand_sep_0"><?php echo mlx_get_lang('Thousand Separator for Currecny'); ?></label><br>
				  <input type="text" class="form-control thousand_sep inputtext" 
				  name="options[site_language][0][thousand_sep]" id="thousand_sep_0" placeholder="Enter Thousand Separator" 
				  value="<?php echo ","; ?>">
				  
				</div>
				
				<div class="col-md-4">
				  <label for="decimal_sep_0"><?php echo mlx_get_lang('Decimal separator for Currecny'); ?></label><br>
				  <input type="text" class="form-control decimal_sep inputtext" 
				  name="options[site_language][0][decimal_sep]" id="decimal_sep_0" placeholder="Enter Decimal separator" 
				  value="<?php echo "."; ?>">
				  
				</div>
				
				<div class="col-md-4">
				  <label for="num_decimals_0"><?php echo mlx_get_lang('Number of decimals for Currecny'); ?></label><br>
				  <input type="number" class="form-control num_decimals inputtext" step="1"
				  name="options[site_language][0][num_decimals]" id="num_decimals_0" placeholder="Enter Number of decimals" 
				  value="<?php echo "2"; ?>">
				  
				</div>
				
				
				
				
			</div>
		</div>
		
	</div>
</div>