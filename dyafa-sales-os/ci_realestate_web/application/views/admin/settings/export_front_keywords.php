<?php $default_language = $myHelpers->global_lib->get_option('default_language'); ?>
<script>
$(document).ready(function() {
	$('.impot_lang_form').on('submit',function() {
		var thiss = $(this);
		
		if(thiss.is(':valid'))
		{
			$('.full_sreeen_overlay').show();
			var callback = 'export_lang_keyword';
			$.ajax({
				url: base_url +'admin_ajax',
				type: 'POST',
				success: function (res) {
					if($('input:radio[name="export_type"]:checked').val() == 'save')
					{
						$('.page-title').after('<div class="alert alert-success alert-dismissable" style="margin-top:10px; margin-bottom:0px;">'+
							'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
							'Keyword Export Successfully'+
						'</div>');
					}
					else
					{
						var data = new Blob([res.file_content], {type: 'text/plain'});

						var textFile;
						if (textFile !== null) {
						window.URL.revokeObjectURL(textFile);
						}

						textFile = window.URL.createObjectURL(data);
						
						var link=document.createElement('a');
						link.href=textFile;
						link.setAttribute('download',res.file_name);
						document.body.appendChild(link);
						link.click();
						link.remove();
					}
					thiss.find('.select2_elem').val('').trigger('change');
					thiss.find('input:radio[value="download"]').attr('checked',true).trigger('change');
					thiss[0].reset();
					
					$('.alert').delay(5000).fadeOut('slow');
					$('.full_sreeen_overlay').hide();
				},
				data: thiss.serialize()+'&callback='+callback,
				cache: false
			});
			return false;
		}
		
	});
});
</script>

      <div class="content-wrapper">
        <section class="content-header">
          <h1 class="page-title"><i class="fa fa-upload"></i> <?php echo mlx_get_lang('Export Front Keywords'); ?> 
		  
		  <a href="<?php echo base_url("admin/settings/front_keyword_settings");?>" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right content-header-right-link"><?php echo mlx_get_lang('Keyword Settings'); ?></a>
		  
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
			$attributes = array('name' => 'add_form_post','class' => 'form impot_lang_form');		 			
			echo form_open_multipart('',$attributes); 
			
			?>
			<input type="hidden" name="lang_for" value="front">
			<div class="row">
			<div class="col-md-12">   
			   
			<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
                
				<div class="box-body">
					<div class="row">
						<div class="col-md-4">
							
							<label for="lang"><?php echo mlx_get_lang('Language List'); ?> </label>
							<select class="form-control select2_elem" name="lang" id="lang" required>
								<option value=""><?php echo mlx_get_lang('Select Any Language'); ?> </option>
								<?php 
									if(isset($site_language) && !empty($site_language)) 
									{ 
										$site_language_array = json_decode($site_language,true);
										if(!empty($site_language_array)) 
										{ 
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
											foreach($site_language_array as $k=>$v) 
											{ 
												$lang_exp = explode('~',$v['language']);
												$lang_code = $lang_exp[1];
												$lang_title = $lang_exp[0];
												$lang_slug = $myHelpers->global_lib->get_slug($lang_title,'_');
												echo '<option value="'.$lang_slug.'">'.ucfirst($lang_title).'</option>';
											}
										}
									}
								  ?>
							</select>
							
						</div>
					</div>
					
					<p class="lead"><?php echo mlx_get_lang('Export Settings'); ?> </p>
					
					<div class="row">
						<div class="col-md-4">
							<div class="form-group" >
								<label for="enable_homepage_section"><?php echo mlx_get_lang('Export Type'); ?></label>
								 <div class="radio_toggle_wrapper ">
									<input type="radio" id="export_type_download" value="download" 
									name="export_type" checked="checked" 
									class="toggle-radio-button show_hide_setting_elem">
									<label for="export_type_download"><?php echo mlx_get_lang('Download'); ?></label>
									
									<input type="radio" id="export_type_save" 
									value="save" name="export_type" 
									class="toggle-radio-button show_hide_setting_elem">
									<label for="export_type_save"><?php echo mlx_get_lang('Save to Import Directory'); ?></label>
								</div>
							</div> 
						</div>
					</div>
					
				</div>
				<div class="box-footer">
					<button name="submit" type="submit" id="save" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> ">
						<i aria-hidden="true" class="fa fa-upload"></i> 
						<?php echo mlx_get_lang('Export'); ?>
					</button>
				</div>
              </div>
			  
			  
		  </div>
		  
		  
		  </div>
		  
			</form>
        </section>
      </div>
      