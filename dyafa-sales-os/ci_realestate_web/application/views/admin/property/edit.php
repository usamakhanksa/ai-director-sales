<?php
$user_type = $this->session->userdata('user_type');
$short_desc_limit = 250;


global $property;

if (isset($query) && $query->num_rows() > 0) {

	$property = $row = $query->row();

	

	foreach ($row as $k => $v) {
		${$k} = $v;
	}

	$s_exp = explode('~', $size);
	$size = $s_exp[0];
	$size_measure = (isset($s_exp[1])) ? $s_exp[1] : 'Sq Feet';

	if (!empty($video_urls))
		$video_url_array = explode(',', $video_urls);
	if (!empty($indoor_amenities))
		$indoor_amenities = json_decode($indoor_amenities, true);
	if (!empty($outdoor_amenities))
		$outdoor_amenities = json_decode($outdoor_amenities, true);
	if (!empty($distance_list))
		$saved_distance_list = json_decode($distance_list, true);
	
	$ask_for_price = get_property_meta($p_id,'ask_for_price');
	
}

?>
<style>
	.custom_field_container .form-control,
	.variation_size_container .form-control {
		display: inline;
		width: 48%;
	}

	.custom_field_container .form-control:last-child {
		float: right;
	}

	/*
.add_property_form .nav-tabs-custom ul.nav{
	flex-wrap: nowrap;
	overflow-Y: hidden;
	overflow-x: auto;
	display: flex;
}
.add_property_form .nav-tabs-custom ul.nav li{
	white-space: nowrap;
}
*/
</style>



<?php
$site_language = 			get_option('site_language');
$enable_multi_language = 	get_option('enable_multi_language');
$default_language = 		get_option('default_language');
$locations = 				get_option('locations');
if (!empty($locations)) {
	$loc_list = json_decode($locations, true);
}
?>

<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title">
			
			<?php 
			if(isset($is_clone)) 
				echo '<i class="fa fa-clone"></i> '.mlx_get_lang('Clone Property'); 
			else 
				echo '<i class="fa fa-edit"></i> '.mlx_get_lang('Edit Property'); 
			?>
		<a target="_blank" href="<?php $segments = array('property', $slug . '~' . $p_id);
			echo str_replace("/admin", "", site_url($segments)); ?>" 
			class="btn btn-<?php echo get_skin_class(); ?> content-header-right-link pull-right"><?php echo mlx_get_lang('View'); ?></a></h1>
		<?php if ($this->site_payments == 'Y' &&  $this->post_property_credit <= 0 && $user_type != 'admin') { ?>
			<div class="alert alert-warning alert-dismissable show_always" style="margin-top:10px; margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				<?php echo mlx_get_lang('You don\'t have sufficient credits for post Property.'); ?>
			</div>
		<?php } ?>
	</section>

	<?php do_action('admin_property_before_edit_content');		?>

	<section class="content">
		<?php


		$attributes = array('name' => 'add_form_post', 'class' => 'form add_property_form');
		
		
		echo form_open_multipart(((isset($is_clone))?base_url(array('admin','property','add_new')):''), $attributes); ?>
		<input type="hidden" name="p_id" class="p_id" value="<?php echo EncryptClientID($p_id); ?>">

		<div class="row">
			<div class="col-md-8">

				<div class="box box-<?php echo get_skin_class(); ?>">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Property Details'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

						</div>
					</div>
					<div class="box-body">

						<?php if (isset($enable_multi_language) && $enable_multi_language == 'Y') { ?>

							<?php
							if (isset($site_language) && !empty($site_language)) {
								$site_language_array = json_decode($site_language, true);
								if (!empty($site_language_array)) {

									foreach ($site_language_array as $aak => $aav) {
										if ($aav['language'] == $default_language) {
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
											$n = 0;
											foreach ($site_language_array as $k => $v) {

												if ($v['status'] != 'enable')
													continue;

												$n++;

												$lang_exp = explode('~', $v['language']);
												$lang_code = $lang_exp[1];
												$lang_title = $lang_exp[0];
											?>
												<li <?php if ($n == 1) echo 'class="active"'; ?>>
													<a href="#<?php echo $lang_code; ?>" data-toggle="tab"><?php echo ucfirst($lang_title); ?></a>
												</li>
											<?php } ?>
										</ul>
										<div class="tab-content">
											<?php
											$n = 0;
											foreach ($site_language_array as $k => $v) {

												if ($v['status'] != 'enable')
													continue;

												$n++;
												$lang_exp = explode('~', $v['language']);
												$lang_code = $lang_exp[1];
												$lang_title = $lang_exp[0];

												$lang_title = '';
												$lang_property_id = "";
												$lang_short_description = '';
												$lang_description = '';
												$lang_price = '';

												$m_keyword = '';
												$m_description = '';

												if (isset($title)) $lang_title = $title;
												if (isset($short_description)) $lang_short_description = $short_description;
												if (isset($description)) $lang_description = $description;
												if (isset($price)) $lang_price = $price;
												
												if (isset($seo_meta_keywords)) $m_keyword = $seo_meta_keywords;
												if (isset($seo_meta_description)) $m_description = $seo_meta_description;


												$pld_result = $myHelpers->Common_model->commonQuery("select pld_id , p_id,title,description,price
																			,short_description,seo_meta_keywords,seo_meta_description	
																		from property_lang_details as pl
																		where pl.p_id = $p_id and pl.language = '$lang_code' order by pld_id desc ");

						
												if ($pld_result->num_rows() > 0) {
													$pld_row = $pld_result->row();

													$lang_title = $pld_row->title;
													$lang_property_id = $pld_row->pld_id;
													$lang_short_description = $pld_row->short_description;
													$lang_description = $pld_row->description;
													$lang_price = $pld_row->price;
													$m_keyword = $pld_row->seo_meta_keywords;
													$m_description = $pld_row->seo_meta_description;
												}


											?>

					<div class="<?php if ($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>">

						<div class="checkbox">
							<label style="padding-left:0px;">
								<input type="checkbox" class="minimal" name="multi_lang[<?php echo $lang_code; ?>][property_delete]" 
									value="<?php echo $lang_property_id; ?>" />&nbsp;&nbsp;<?php echo mlx_get_lang('Delete This Language Version'); ?>
							</label>
						</div>

						<input type="hidden" name="multi_lang[<?php echo $lang_code; ?>][pld_id]" value="<?php echo $lang_property_id; ?>" />


						<div class="form-group required-fields">
							<label for="title_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Title'); ?> <?php if ($n == 1) { ?><span class="text-red">*</span><?php } ?></label>
							<input type="text" class="form-control" <?php if ($n == 1) { ?>required="required" <?php } ?> 
								name="multi_lang[<?php echo $lang_code; ?>][title]" id="title_<?php echo $lang_code; ?>" 
								value="<?php echo $lang_title; ?>">
						</div>

						<div class="form-group">
							<label for="short_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Short Description'); ?></label>
							<textarea class="form-control short-description-element" maxlength="<?php echo $short_desc_limit; ?>" rows="3" 
								id="short_description_<?php echo $lang_code; ?>" name="multi_lang[<?php echo $lang_code; ?>][short_description]"><?php echo $lang_short_description; ?></textarea>
							<span class="rchars" id="rchars_<?php echo $lang_code; ?>"><?php echo $short_desc_limit; ?></span> 
							<?php echo mlx_get_lang('Character(s) Remaining'); ?>
						</div>

						<div class="form-group required-fields">
							<label for="description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Description'); ?> 
								<?php if ($n == 1) { ?><span class="text-red">*</span><?php } ?></label>
							<textarea class="form-control ckeditor-element" data-lang_code="<?php echo $lang_code; ?>" 
								data-lang_dir="<?php echo $v['direction']; ?>" rows="3" id="description_<?php echo $lang_code; ?>" <?php if ($n == 1) { ?>required<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][description]"><?php echo $lang_description; ?></textarea>
						</div>


						<div class="form-group required-fields">
							<label for="price_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Price'); ?> <?php if ($n == 1 && 0) { ?><span class="text-red">*</span><?php } ?></label>
							<div class="input-group">
								<span class="input-group-addon">
									<?php echo $myHelpers->global_lib->get_currency_symbol($v['currency']); ?>
								</span>
								<input type="text"  class="form-control" <?php if ($n == 1 && 0) { ?>required<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][price]" id="price_<?php echo $lang_code; ?>" value="<?php echo $lang_price; ?>">
								<span class="input-group-addon property_type_rent_block" 
								<?php if ((isset($property_for) && $property_for != 'Rent') 
											|| !isset($property_for)) {
										echo 'style="display:none;"';
									} 
								?>>
									<?php echo mlx_get_lang('Per Month'); ?>
								</span>
							</div>
						</div>

						<div class="form-group">
							<label for="property_type_status"><?php echo mlx_get_lang('Ask for Price?'); ?></label>

							<div class="radio_toggle_wrapper ">
								<input type="radio" id="ask_for_price_yes" value="Y" name="property_meta[ask_for_price]" class="toggle-radio-button" <?php if((isset($ask_for_price) && $ask_for_price == 'Y')) { echo ' checked="checked" '; } ?>>
								<label for="ask_for_price_yes"><?php echo mlx_get_lang('Yes'); ?></label>

								<input type="radio" id="ask_for_price_no" value="N" name="property_meta[ask_for_price]" class="toggle-radio-button" 
								<?php if((isset($ask_for_price) && $ask_for_price == 'N') || !isset($ask_for_price) || 
								(isset($ask_for_price) && empty($ask_for_price))) { echo ' checked="checked" '; } ?>
								>
								<label for="ask_for_price_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>


						<div class="form-group">
							<label for="meta_keywrod_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Keywords'); ?></label>
							<input type="text" class="form-control" name="multi_lang[<?php echo $lang_code; ?>][seo_meta_keywords]" id="meta_keywrod_<?php echo $lang_code; ?>" value="<?php echo $m_keyword; ?>">
						</div>

						<div class="form-group">
							<label for="meta_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Description'); ?></label>
							<textarea class="form-control" rows="3" id="meta_description_<?php echo $lang_code; ?>" name="multi_lang[<?php echo $lang_code; ?>][seo_meta_description]"><?php echo $m_description; ?></textarea>
						</div>

					</div>
											<?php } ?>
										</div>
									</div>

							<?php }
							} ?>

						<?php } else {

							/*$site_language_array = json_decode($site_language,true);*/

							$default_currency = $this->site_currency;

							/*$lang_code = $this->default_lang_code;*/
							$lang_code = $this->default_language;
						?>


							<div class="form-group required-fields">
								<label for="title"><?php echo mlx_get_lang('Title'); ?> <span class="text-red">*</span></label>
								<input type="text" class="form-control" required="required" name="title" id="title" value="<?php if (isset($title) && !empty($title)) echo $title; ?>">
							</div>

							<div class="form-group">
								<label for="short_description"><?php echo mlx_get_lang('Short Description'); ?></label>
								<textarea class="form-control short-description-element" rows="3" id="short_description" name="short_description" maxlength="<?php echo $short_desc_limit; ?>"><?php if (isset($short_description) && !empty($short_description)) echo $short_description; ?></textarea>
								<span class="rchars" id="rchars"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
							</div>

							<div class="form-group required-fields">
								<label for="description"><?php echo mlx_get_lang('Description'); ?> <span class="text-red">*</span></label>
								<textarea class="form-control ckeditor-element" data-lang_code="<?php echo $lang_code; ?>" rows="3" required="required" id="description" name="description"><?php if (isset($description) && !empty($description)) echo $description; ?></textarea>
							</div>

							<div class="form-group required-fields">
								<label for="price"><?php echo mlx_get_lang('Price'); ?> </label> <!-- <span class="required">*</span> -->
								<div class="input-group">
									<span class="input-group-addon">
										<?php echo $myHelpers->global_lib->get_currency_symbol($default_currency); ?>
										
									</span>
									<input type="text"  class="form-control" name="price" id="price" value="<?php if (isset($price) && !empty($price)) echo $price; ?>"> <!-- required="required" -->
									<span class="input-group-addon property_type_rent_block" <?php if ((isset($property_for) && $property_for != 'Rent') || !isset($property_for)) {
																									echo 'style="display:none;"';
																								} ?>>
										<?php echo mlx_get_lang('Per Month'); ?>
									</span>
								</div>
							</div>

							<div class="form-group">
								<label for="property_type_status"><?php echo mlx_get_lang('Ask for Price?'); ?></label>

								<div class="radio_toggle_wrapper ">
									<input type="radio" id="ask_for_price_yes" value="Y" name="property_meta[ask_for_price]" class="toggle-radio-button" <?php if((isset($ask_for_price) && $ask_for_price == 'Y')) { echo ' checked="checked" '; } ?>>
									<label for="ask_for_price_yes"><?php echo mlx_get_lang('Yes'); ?></label>

									<input type="radio" id="ask_for_price_no" value="N" name="property_meta[ask_for_price]" class="toggle-radio-button" 
									<?php if((isset($ask_for_price) && $ask_for_price == 'N') || !isset($ask_for_price) || 
									(isset($ask_for_price) && empty($ask_for_price))) { echo ' checked="checked" '; } ?>
									>
									<label for="ask_for_price_no"><?php echo mlx_get_lang('No'); ?></label>
								</div>
							</div>

							<div class="form-group">
								<label for="meta_keywrod"><?php echo mlx_get_lang('Meta Keywords'); ?></label>
								<input type="text" class="form-control" name="seo_meta_keywords" id="meta_keywrod" value="<?php if (isset($seo_meta_keywords) && !empty($seo_meta_keywords)) echo $seo_meta_keywords; ?>">
							</div>

							<div class="form-group">
								<label for="meta_description"><?php echo mlx_get_lang('Meta Description'); ?></label>
								<textarea class="form-control" rows="3" id="meta_description" name="seo_meta_description"><?php if (isset($seo_meta_description) && !empty($seo_meta_description)) echo $seo_meta_description; ?></textarea>
							</div>

						<?php } ?>




					</div>

				</div>

				


				<div class="box box-<?php echo get_skin_class(); ?>">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Locations'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
					
						<?php
						do_action('admin_property_location_fields_before' , $property);
						?>

						<div class="form-group hide col-md-12">
							<label for="street_number"><?php echo mlx_get_lang('Street Address'); ?> </label>
							<input type="text" class="form-control" id="street_number" name="street_address" value="<?php if (isset($street_address) && !empty($street_address)) echo $street_address; ?>">
						</div>

						<div class="form-group">
							<label for="address"><?php echo mlx_get_lang('Address'); ?> <span class="text-red">*</span></label>
							<textarea class="form-control" required id="address" name="address"><?php if (isset($address) && !empty($address)) echo $address; ?></textarea>
						</div>

						
						<?php
						$args = array(
							'sel_country' => $country,
							'sel_state' => $state,
							'sel_city' => $city,
							'sel_zip_code' => $zip_code,
							'sel_sub_area' => $sub_area,
							'is_edit' => true
						);
						
						

						do_action('admin_property_location_fields', $args);
						?>

						<?php

						$isOsmAct = $myHelpers->isPluginActive('openstreetmap');
						if ($isOsmAct == true) {

							$meta_id = 0;
							$meta_value = '';
							if (
								isset($property_meta['openstreetmap_embed_code']) && isset($property_meta['openstreetmap_embed_code']['meta_id']) &&
								isset($property_meta['openstreetmap_embed_code']['meta_value'])
							) {
								$meta_id = $property_meta['openstreetmap_embed_code']['meta_id'];
								$meta_value = $property_meta['openstreetmap_embed_code']['meta_value'];
							}
						?>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="openstreetmap"><?php echo mlx_get_lang('Open Street Map'); ?></label>
										<textarea class="form-control no_clean openstreetmap" id="openstreetmap" name="property_meta[openstreetmap_embed_code]
										<?php if(!isset($is_clone)){?>
											[<?php echo $meta_id; ?>]
										<?php } ?>
										" rows="3" col="3"><?php echo $meta_value; ?></textarea>
										<p class="help-block">
											<a href="https://www.openstreetmap.org/" target="_blank"><?php echo mlx_get_lang('Open Street Map'); ?> </a><?php echo mlx_get_lang('Open the link, Copy iframe code and paste above '); ?>
										</p>

									</div>
								</div>
							</div>
						<?php

						}
						?>
					</div>
				</div>



				<?php $this->load->view("$theme/property/property_other_details");?>		
				
				<?php $this->load->view("$theme/property/property_features");?>

				<?php if (isset($amenities_list['indoor_amenities']) && !empty($amenities_list['indoor_amenities'])) { ?>

					<div class="box box-<?php echo get_skin_class(); ?>">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo mlx_get_lang('Indoor Amenities'); ?></h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<div class="box-body">
							<div class="row">
								<?php
								foreach ($amenities_list['indoor_amenities'] as $k => $v) {
									$is_checked = '';
									if (isset($indoor_amenities) && in_array($v, $indoor_amenities))
										$is_checked = ' checked="checked" ';
								?>

									<div class="col-md-6 text-right">
										<label class="pull-left" for="<?php echo $v; ?>"><?php echo mlx_get_lang($v); ?></label>
										<input <?php echo $is_checked; ?> id="<?php echo $v; ?>" class="minimal" type="checkbox" name="indoor_amenities[]" value="<?php echo ucfirst($v); ?>">
									</div>

								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (isset($amenities_list['outdoor_amenities']) && !empty($amenities_list['outdoor_amenities'])) { ?>

					<div class="box box-<?php echo get_skin_class(); ?>">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo mlx_get_lang('Outdoor Amenities'); ?></h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<div class="box-body">
							<div class="row">
								<?php
								foreach ($amenities_list['outdoor_amenities'] as $k => $v) {
									$is_checked = '';
									if (isset($outdoor_amenities) && in_array($v, $outdoor_amenities))
										$is_checked = ' checked="checked" ';
								?>

									<div class="col-md-6 text-right">
										<label class="pull-left" for="<?php echo $v; ?>"><?php echo mlx_get_lang($v); ?></label>
										<input <?php echo $is_checked; ?> id="<?php echo $v; ?>" class="minimal" type="checkbox" name="outdoor_amenities[]" value="<?php echo ucfirst($v); ?>">
									</div>

								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>



				<?php	
				
				?>
				

				<?php $this->load->view("$theme/property/property_distances");?>	

				<?php $this->load->view("$theme/property/property_gallery");?>
				<?php $this->load->view("$theme/property/property_videos");?>
					
					

					<?php
					if (function_exists("cms_property_custom_metaboxes")) {
						cms_property_custom_metaboxes();
					}
					?>

					<?php if (isset($custom_field_list) && !empty($custom_field_list) && 0) { ?>

						<div class="box box-<?php echo get_skin_class(); ?>">
							<div class="box-header with-border">
								<h3 class="box-title"><?php echo mlx_get_lang('Custom Fields'); ?></h3>

							</div>
							<div class="box-body">

								<?php
								$n = 0;
								foreach ($custom_field_list as $cfk => $cfv) {
									$n++;
									$hasChecked = '';
									$curValue = (isset(${$cfv['slug']})) ? ${$cfv['slug']} : '';
								?>
									<div class="form-group">
										<label for="custom_field_<?php echo $n; ?>"><?php echo mlx_get_lang($cfv['title']); ?> <?php if ($n == 1 && 0) { ?><span class="text-red">*</span><?php } ?></label>
										<input type="text" class="form-control" <?php if ($n == 1 && 0) { ?>required="required" <?php } ?> name="custom_fields[<?php echo $cfv['slug']; ?>]" id="custom_field_<?php echo $n; ?>" value="<?php echo $curValue; ?>">
									</div>
								<?php } ?>

							</div>
						</div>
					<?php } ?>


					<?php do_action('load_custom_metaboxes', 'property', array('p_id' => $p_id)); ?>


			</div>

			<div class="col-md-4">
				
				<?php do_action("cms_admin_property_information_box_aside_before" , $property ); ?>	
				<div class="box box-<?php echo get_skin_class(); ?> sticky_sidebar">
					<div class="box-header with-border">
						<h3 class="box-title"> <?php echo mlx_get_lang('Status'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

						</div>
					</div>
					<div class="box-body">
						<div class="form-group">
							<label> <?php echo mlx_get_lang('Current Status'); ?> : </label> <?php
						if (isset($status)) echo ucfirst($status);
						?>
						</div>
						
						
						<div class="form-group">
							<label> <?php echo mlx_get_lang('URL Slug'); ?></label>
							<input type="text" name="slug" value="<?php if (isset($slug)) echo $slug; ?>" class="form-control" />
							<input type="hidden" name="old_slug" value="<?php if (isset($slug)) echo $slug; ?>" />
						</div>
						
						<?php if(!isset($is_clone)) { ?>
						<div class="form-group">
							<label for="property_type_status"><?php echo mlx_get_lang('Return to Edit Page?'); ?></label>

							<div class="radio_toggle_wrapper ">
								<input type="radio" id="return_to_edit_yes" value="Y" name="return_to_edit" class="toggle-radio-button">
								<label for="return_to_edit_yes"><?php echo mlx_get_lang('Yes'); ?></label>

								<input type="radio" id="return_to_edit_no" value="N" name="return_to_edit" class="toggle-radio-button" 
								checked="checked"
								>
								<label for="return_to_edit_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>
						<?php } ?>
						
						<?php do_action("cms_admin_property_edit_information_aside" , $property); ?>
						
						
						
					</div>
					<div class="box-footer">
						<?php if ($user_type == 'admin') {
							if ($status == 'pending') {
						?>
								<input type="hidden" name="prop_user_id" value="<?php if (isset($created_by)) echo $created_by; ?>" />
								<input type="hidden" name="current_status" value="<?php if (isset($status)) echo $status; ?>" />
							<?php } ?>
							<button type="submit" name="draft" class="btn btn-draft btn-default" id="save_draft"><?php echo mlx_get_lang('Save as Draft'); ?></button>
							<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
						<?php
						} else if ($status == 'publish') {
						?>
							<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Update'); ?></button>
							<?php
						} else {
							$has_req = get_option('admin_approval_require_for_property');
							if ($has_req == 'N') {
							?>
								<button type="submit" name="draft" class="btn btn-draft btn-default" id="save_draft"><?php echo mlx_get_lang('Save as Draft'); ?></button>
								<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
							<?php
							} else {
							?>
								<button name="pending" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Submit for Approval'); ?></button>
						<?php
							}
						}
						?>
					</div>
				</div>

				<?php do_action("cms_admin_property_information_box_aside_after"  , $property ); ?>	

			</div>

		</div>
		</form>
	</section>
</div>

<div class="modal direction-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">

		</div>
	</div>
</div>

<?php $this->load->view("$theme/property/property_edit_scripts");?>

<?php

/*do_action("admin_property_edit_scripts"); */

do_action("admin_footer_scripts", "location_updates");
do_action("admin_footer_scripts", "property_update_scripts");

?>