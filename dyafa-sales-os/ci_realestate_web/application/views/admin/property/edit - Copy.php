<?php
$user_type = $this->session->userdata('user_type');
$short_desc_limit = 250;



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
		<h1 class="page-title"><i class="fa fa-edit"></i> <?php echo mlx_get_lang('Edit Property'); ?> <a target="_blank" href="<?php $segments = array('property', $slug . '~' . $p_id);
																																echo str_replace("/admin", "", site_url($segments)); ?>" class="btn btn-<?php echo get_skin_class(); ?> content-header-right-link pull-right"><?php echo mlx_get_lang('View'); ?></a></h1>
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
		echo form_open_multipart('', $attributes); ?>
		<input type="hidden" name="p_id" class="p_id" value="<?php echo $myHelpers->EncryptClientId($p_id); ?>">

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
															<input type="checkbox" class="minimal" name="multi_lang[<?php echo $lang_code; ?>][property_delete]" value="<?php echo $lang_property_id; ?>" />&nbsp;&nbsp;<?php echo mlx_get_lang('Delete This Language Version'); ?>
														</label>
													</div>

													<input type="hidden" name="multi_lang[<?php echo $lang_code; ?>][pld_id]" value="<?php echo $lang_property_id; ?>" />


													<div class="form-group required-fields">
														<label for="title_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Title'); ?> <?php if ($n == 1) { ?><span class="text-red">*</span><?php } ?></label>
														<input type="text" class="form-control" <?php if ($n == 1) { ?>required="required" <?php } ?> name="multi_lang[<?php echo $lang_code; ?>][title]" id="title_<?php echo $lang_code; ?>" value="<?php echo $lang_title; ?>">
													</div>

													<div class="form-group">
														<label for="short_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Short Description'); ?></label>
														<textarea class="form-control short-description-element" maxlength="<?php echo $short_desc_limit; ?>" rows="3" id="short_description_<?php echo $lang_code; ?>" name="multi_lang[<?php echo $lang_code; ?>][short_description]"><?php echo $lang_short_description; ?></textarea>
														<span class="rchars" id="rchars_<?php echo $lang_code; ?>"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
													</div>

													<div class="form-group required-fields">
														<label for="description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Description'); ?> <?php if ($n == 1) { ?><span class="text-red">*</span><?php } ?></label>
														<textarea class="form-control ckeditor-element" data-lang_code="<?php echo $lang_code; ?>" data-lang_dir="<?php echo $v['direction']; ?>" rows="3" id="description_<?php echo $lang_code; ?>" <?php if ($n == 1) { ?>required<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][description]"><?php echo $lang_description; ?></textarea>
													</div>


													<div class="form-group required-fields">
														<label for="price_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Price'); ?> <?php if ($n == 1) { ?><span class="text-red">*</span><?php } ?></label>
														<div class="input-group">
															<span class="input-group-addon">
																<?php echo $myHelpers->global_lib->get_currency_symbol($v['currency']); ?>
															</span>
															<input type="text"  class="form-control" <?php if ($n == 1) { ?>required<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][price]" id="price_<?php echo $lang_code; ?>" value="<?php echo $lang_price; ?>">
															<span class="input-group-addon property_type_rent_block" <?php if ((isset($property_for) && $property_for != 'Rent') || !isset($property_for)) {
																															echo 'style="display:none;"';
																														} ?>>
																<?php echo mlx_get_lang('Per Month'); ?>
															</span>
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
								<label for="price"><?php echo mlx_get_lang('Price'); ?> <span class="required">*</span></label>
								<div class="input-group">
									<span class="input-group-addon">
										<?php echo $myHelpers->global_lib->get_currency_symbol($default_currency); ?>
										<i class="fa fa-usd"></i>
									</span>
									<input type="text"  class="form-control" required="required" name="price" id="price" value="<?php if (isset($price) && !empty($price)) echo $price; ?>">
									<span class="input-group-addon property_type_rent_block" <?php if ((isset($property_for) && $property_for != 'Rent') || !isset($property_for)) {
																									echo 'style="display:none;"';
																								} ?>>
										<?php echo mlx_get_lang('Per Month'); ?>
									</span>
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
						<h3 class="box-title"><?php echo mlx_get_lang('Other Details'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="form-group">
							<label class="control-label"><?php echo mlx_get_lang('Property Type'); ?> <span class="required">*</span></label>

							<?php
							if (isset($property_types) && $property_types->num_rows() > 0) { ?>
								<div class="radio_toggle_wrapper ">
									<?php
									foreach ($property_types->result() as $row) {
									?>
										<input type="radio" <?php
															if (isset($property_type) && $property_type == $row->pt_id)
																echo ' checked="checked" ';
															?> id="property_type_<?php echo $myHelpers->EncryptClientId($row->pt_id); ?>" value="<?php echo $myHelpers->EncryptClientId($row->pt_id); ?>" name="property_type" class="toggle-radio-button">
										<label for="property_type_<?php echo $myHelpers->EncryptClientId($row->pt_id); ?>"><?php echo ucfirst($row->title); ?></label>
									<?php } ?>
								</div>
							<?php } else { ?>
								<p class="no-margin"><?php echo mlx_get_lang('Property Type Not Available Now'); ?></p>
							<?php } ?>
						</div>

						<div class="form-group">
							<label for="property_type_status"><?php echo mlx_get_lang('Property For'); ?> <span class="required">*</span></label>

							<div class="radio_toggle_wrapper ">
								<input type="radio" id="property_for_sale" value="sale" name="property_for" <?php
																											if ((isset($property_for) && strtolower($property_for) == 'sale') || !isset($property_for) || (isset($property_for) && $property_for == ''))
																												echo ' checked="checked" ';
																											?> class="toggle-radio-button">
								<label for="property_for_sale"><?php echo mlx_get_lang('Sale'); ?></label>

								<input type="radio" id="property_for_rent" value="rent" name="property_for" class="toggle-radio-button" <?php
																																		if (isset($property_for) && strtolower($property_for) == 'rent')
																																			echo ' checked="checked" ';
																																		?>>
								<label for="property_for_rent"><?php echo mlx_get_lang('Rent'); ?></label>
							</div>
						</div>

						<!--
					<div class="form-group">
						<label for="property_type_status"><?php echo mlx_get_lang('Status'); ?></label>
						 <div class="radio_toggle_wrapper ">
							<input type="radio" checked="checked" id="status_y" value="Y" 
							name="status" class="toggle-radio-button" 
							<?php
							if ((isset($status) && $status == 'Y') || !isset($status))
								echo ' checked="checked" ';
							?>>
							<label for="status_y"><?php echo mlx_get_lang('Active'); ?></label>
							
							<input type="radio" id="status_n" value="N" name="status" 
							class="toggle-radio-button" 
							<?php
							if (isset($status) && $status == 'N')
								echo ' checked="checked" ';
							?>>
							<label for="status_n"><?php echo mlx_get_lang('In-Active'); ?></label>
						</div>
					</div>
					-->

						<?php

						$user_type = $this->session->userdata('user_type');
						if ($user_type == 'admin') {
						?>
							<div class="row">
								<div class="form-group col-md-6">
									<label for="user_id"><?php echo mlx_get_lang('Property Added By'); ?> <span class="required">*</span></label>

									<select class="form-control select2_elem" name="user_id" id="user_id" required>
										<option value=""><?php echo mlx_get_lang('Select Any User'); ?></option>
										<?php
										if (isset($user_list) && $user_list->num_rows() > 0) {
											foreach ($user_list->result() as $u_row) {
												$first_name = get_user_meta($u_row->user_id, 'first_name');
												$last_name = get_user_meta($u_row->user_id, 'last_name');

												if (!empty($last_name))
													$first_name .= ' ' . $last_name;
												echo '<option value="' . EncryptClientID($u_row->user_id) . '"';
												if (isset($created_by) && $created_by == $u_row->user_id)
													echo ' selected="selected" ';
												echo '>' . ucfirst($first_name) . ' (' . ucfirst($u_row->user_type) . ')</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
						<?php
						} else {

							echo '<input type="hidden" name="user_id" class="user_id" value="' . EncryptClientID($this->session->userdata("user_id")) . '">';
						}
						?>
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
						$isOsmAct = $myHelpers->isPluginActive('google_map');
						if ($isOsmAct == true) {
							$enable_google_map_js_api = get_option('enable_google_map_js_api');
							$google_map_js_api_key = 	get_option('google_map_js_api_key');
							$google_map_center_latitude = get_option('google_map_center_latitude');
							$google_map_center_longitude = get_option('google_map_center_longitude');

							if ($enable_google_map_js_api == 'Y' && !empty($google_map_js_api_key)) {
						?>

								<div class="form-group ">
									<label for="google_map_locations"><?php echo mlx_get_lang('Google Map Locations'); ?></label>
									<div class="row">
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon"><?php echo mlx_get_lang('Latitude'); ?></span>
												<input id="property_latitude" type="text" class="form-control" name="lat" value="<?php if (isset($lat) && !empty($lat)) echo $lat; ?>">
											</div>
										</div>
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon"><?php echo mlx_get_lang('Longitude'); ?></span>
												<input type="text" id="property_longitude" class="form-control" name="long" value="<?php if (isset($long) && !empty($long)) echo $long; ?>">
											</div>
										</div>
										<div class="col-md-2 text-center">
											<a href="#popme" data-map_lat="<?php echo $google_map_center_latitude; ?>" data-map_lng="<?php echo $google_map_center_longitude; ?>" data-api_key="<?php echo $google_map_js_api_key; ?>" class="btn btn-block btn-<?php echo get_skin_class(); ?> popup-property" data-toggle="tooltip" title="<?php echo mlx_get_lang('Fetch From Map'); ?>"><i class="fa fa-map-marker"></i></a>
											<div class="white-popup mfp-hide" id="popme">
												<div id="map" style="width: 100%; min-height: 500px"></div>
											</div>
										</div>
									</div>

								</div>

						<?php
							}
						}
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
										<textarea class="form-control no_clean openstreetmap" id="openstreetmap" name="property_meta[openstreetmap_embed_code][<?php echo $meta_id; ?>]" rows="3" col="3"><?php echo $meta_value; ?></textarea>
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


				<div class="box box-<?php echo get_skin_class(); ?>">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Features'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<!--
				<span class="required">*</span>
				required="required"
				-->
					<div class="box-body">
						<div class="form-group">
							<label for="size"><?php echo mlx_get_lang('Size'); ?> </label>

							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-expand"></i>
								</span>
								<input type="text" class="form-control" name="size" id="size" value="<?php if (isset($size) && !empty($size)) echo $size; ?>">

								<input type="hidden" class="form-control" value="<?php if (isset($size_measure) && !empty($size_measure)) echo $size_measure;
																					else echo mlx_get_lang('Sq Feet'); ?>" name="size_measure" id="size_measure">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle size_measure" data-toggle="dropdown" aria-expanded="false">


										<?php if (isset($size_measure) && !empty($size_measure)) echo $size_measure;
										else echo mlx_get_lang('Sq Feet'); ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
									<ul class="dropdown-menu size_measure_menus">
										<li><a data-val="<?php echo mlx_get_lang('Sq Feet'); ?>"><?php echo mlx_get_lang('Sq Feet'); ?></a></li>
										<li><a data-val="<?php echo mlx_get_lang('Sq Meter'); ?>"><?php echo mlx_get_lang('Sq Meter'); ?></a></li>
										<li><a data-val="<?php echo mlx_get_lang('Sq Yard'); ?>"><?php echo mlx_get_lang('Sq Yard'); ?></a></li>
										<?php
										if (isset($size_units) && !empty($size_units)) {
											foreach ($size_units as $suv) {
										?>
												<li><a data-val="<?php echo mlx_get_lang($suv); ?>"><?php echo mlx_get_lang($suv); ?></a></li>
										<?php
											}
										}
										?>
									</ul>

								</div>

							</div>
						</div>

						<div class="form-group">
							<label for="bedroom"><?php echo mlx_get_lang('Bedroom'); ?> </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-bed"></i>
								</span>
								<input type="text" value="<?php if (isset($bedroom) && !empty($bedroom)) echo $bedroom;
															else echo '0'; ?>" class="form-control" name="bedroom" id="bedroom">
							</div>
						</div>

						<div class="form-group">
							<label for="bathroom"><?php echo mlx_get_lang('Bathroom'); ?> </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-bathtub"></i>
								</span>
								<input type="text" value="<?php if (isset($bathroom) && !empty($bathroom)) echo $bathroom;
															else echo '0'; ?>" class="form-control" name="bathroom" id="bathroom">
							</div>
						</div>


						<div class="form-group">
							<label for="garage"><?php echo mlx_get_lang('Garages'); ?> </label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-car"></i>
								</span>
								<input type="text" value="<?php if (isset($garage) && !empty($garage)) echo $garage;
															else echo '0'; ?>" class="form-control" name="garage" id="garage">
							</div>
						</div>
						
						<?php do_action("cms_admin_property_edit_additional_features" , $property); ?>
						
						
						
						
					</div>
				</div>

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


				<?php if (isset($distances_list) && !empty($distances_list)) { ?>
					<?php

					$any_direction_list = array(
						'East' => 'East',
						'West' => 'West',
						'North' => 'North',
						'South' => 'South',
						'North-East' => 'North-East',
						'South-East' => 'South-East',
						'South-West' => 'South-West',
						'North-West' => 'North-West',
					);
					?>
					<style>
						.distance_block .direction-block {
							border: 1px solid #f4f4f4;
							display: flex;
							flex-direction: column;
							justify-content: center;
							align-items: center;
							padding-left: 15px;
							padding-right: 15px;
						}

						.distance_block .direction-block:nth-child(even) {
							border-left: 0px;
							border-right: 0px;
						}

						.distance_block .row:nth-child(even) .direction-block {
							border-top: 0px;
							border-bottom: 0px;
						}

						.distance_block .row.no-gutters {
							display: flex;
							align-items: stretch;
							flex-direction: row;
						}

						.distance_block .direction-block .btn {
							margin-bottom: 10px;
						}

						.distance_block .direction-block .direction-listing {
							width: 100%;
						}

						.distance_block .direction-block .direction-listing .list-group {
							margin-bottom: 10px;
							text-align: left;
						}

						.distance_block .direction-block .direction-listing .list-group span.badge {
							background-color: #dd4b39;
							cursor: pointer;
						}

						.distance_block .row:nth-child(odd) .direction-block:nth-child(even) {
							background-color: #f8f9fa;
						}

						.distance_block .row:nth-child(even) .direction-block:nth-child(odd) {
							background-color: #f8f9fa;
						}
						.distance_block .direction-block.center-block{
							position:relative;
						}
						.arrow {
							border: solid black;
							border-width: 0 3px 3px 0;
							display: inline-block;
							padding: 3px;
							position:absolute;
						}

						.top-left {
							transform: rotate(-180deg);
							-webkit-transform: rotate(-180deg);
							top:30px;
							left:30px;
						}
						.top-center {
							transform: rotate(-135deg);
							-webkit-transform: rotate(-135deg);
							top:30px;
						}
						.top-right {
							transform: rotate(-90deg);
							-webkit-transform: rotate(-90deg);
							top:30px;
							right:30px;
						}

						.center-right {
							transform: rotate(-45deg);
							-webkit-transform: rotate(-45deg);
							right:30px;
						}

						.center-left {
							transform: rotate(135deg);
							-webkit-transform: rotate(135deg);
							left:30px;
						}

						
						.bottom-left {
							transform: rotate(90deg);
							-webkit-transform: rotate(90deg);
							bottom:30px;
							left:30px;
						}
						.bottom-center {
							transform: rotate(45deg);
							-webkit-transform: rotate(45deg);
							bottom:30px;
						}
						.bottom-right {
							transform: rotate(0deg);
							-webkit-transform: rotate(0deg);
							bottom:30px;
							right:30px;
						}
					</style>
					<div class="box box-<?php echo get_skin_class(); ?>">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo mlx_get_lang('Distances'); ?></h3>
							<div class="box-tools pull-right">


								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<div class="box-body distance_block">

							<?php
							// echo "<pre>";
							// print_r($saved_distance_list);
							/* foreach ($distances_list as $k => $v) { */
							?>


							<div class="direction_code">

								<div class="row no-gutters">
									<div class="col-md-4 text-center direction-block North-West-block">
										<h4><?php echo mlx_get_lang('North-West'); ?></h4>
										<div class="direction-listing">
											<ul class="list-group">
												<?php if (isset($saved_distance_list) && isset($saved_distance_list['North-West']) && !empty($saved_distance_list['North-West'])) {
													foreach ($saved_distance_list['North-West'] as $dk => $dv) {
												?>
														<li class="list-group-item">
															<span class="badge badge-danger">X</span>
															<?php if (isset($dv['title']) && !empty($dv['title']))
																echo ucfirst($dv['title']) . '<br />';
															?>
															<?php if (isset($dv['entity']) && !empty($dv['entity']))
																echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
															?>
															<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
															?>
															<input type="hidden" name="distance[North-West][title][]" value="<?php echo $dv['title'] ?>">
															<input type="hidden" name="distance[North-West][entity][]" value="<?php echo $dv['entity'] ?>">
															<input type="hidden" name="distance[North-West][measurement][]" value="<?php echo $dv['measurement'] ?>">
															<input type="hidden" name="distance[North-West][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
														</li>
												<?php }
												} ?>
											</ul>
										</div>
										<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="North-West"><i class="fa fa-plus"></i></button>
									</div>
									<div class="col-md-4 text-center direction-block North-block">
										<h4><?php echo mlx_get_lang('North'); ?></h4>
										<div class="direction-listing">
											<ul class="list-group">
												<?php if (isset($saved_distance_list) && isset($saved_distance_list['North']) && !empty($saved_distance_list['North'])) {
													foreach ($saved_distance_list['North'] as $dk => $dv) {
												?>
														<li class="list-group-item">
															<span class="badge badge-danger">X</span>
															<?php if (isset($dv['title']) && !empty($dv['title']))
																echo ucfirst($dv['title']) . '<br />';
															?>
															<?php if (isset($dv['entity']) && !empty($dv['entity']))
																echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
															?>
															<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
															?>
															<input type="hidden" name="distance[North][title][]" value="<?php echo $dv['title'] ?>">
															<input type="hidden" name="distance[North][entity][]" value="<?php echo $dv['entity'] ?>">
															<input type="hidden" name="distance[North][measurement][]" value="<?php echo $dv['measurement'] ?>">
															<input type="hidden" name="distance[North][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
														</li>
												<?php }
												} ?>
											</ul>
										</div>
										<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="North"><i class="fa fa-plus"></i></button>
									</div>
									<div class="col-md-4 text-center direction-block North-East-block">
										<h4><?php echo mlx_get_lang('North-East'); ?></h4>
										<div class="direction-listing">
											<ul class="list-group">
												<?php if (isset($saved_distance_list) && isset($saved_distance_list['North-East']) && !empty($saved_distance_list['North-East'])) {
													foreach ($saved_distance_list['North-East'] as $dk => $dv) {
												?>
														<li class="list-group-item">
															<span class="badge badge-danger">X</span>
															<?php if (isset($dv['title']) && !empty($dv['title']))
																echo ucfirst($dv['title']) . '<br />';
															?>
															<?php if (isset($dv['entity']) && !empty($dv['entity']))
																echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
															?>
															<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
															?>
															<input type="hidden" name="distance[North-East][title][]" value="<?php echo $dv['title'] ?>">
															<input type="hidden" name="distance[North-East][entity][]" value="<?php echo $dv['entity'] ?>">
															<input type="hidden" name="distance[North-East][measurement][]" value="<?php echo $dv['measurement'] ?>">
															<input type="hidden" name="distance[North-East][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
														</li>
												<?php }
												} ?>
											</ul>
										</div>
										<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="North-East"><i class="fa fa-plus"></i></button>
									</div>
								</div>


								<div class="row no-gutters">
									<div class="col-md-4 text-center direction-block West-block">
										<h4><?php echo mlx_get_lang('West'); ?></h4>
										<div class="direction-listing">
											<ul class="list-group">
												<?php if (isset($saved_distance_list) && isset($saved_distance_list['West']) && !empty($saved_distance_list['West'])) {
													foreach ($saved_distance_list['West'] as $dk => $dv) {
												?>
														<li class="list-group-item">
															<span class="badge badge-danger">X</span>
															<?php if (isset($dv['title']) && !empty($dv['title']))
																echo ucfirst($dv['title']) . '<br />';
															?>
															<?php if (isset($dv['entity']) && !empty($dv['entity']))
																echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
															?>
															<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
															?>
															<input type="hidden" name="distance[West][title][]" value="<?php echo $dv['title'] ?>">
															<input type="hidden" name="distance[West][entity][]" value="<?php echo $dv['entity'] ?>">
															<input type="hidden" name="distance[West][measurement][]" value="<?php echo $dv['measurement'] ?>">
															<input type="hidden" name="distance[West][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
														</li>
												<?php }
												} ?>
											</ul>
										</div>
										<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="West"><i class="fa fa-plus"></i></button>
									</div>
									<div class="col-md-4 text-center direction-block center-block">
										<i class="fa fa-building fa-3x"></i>
										<i class="arrow top-left"></i>
										<i class="arrow top-center"></i>
										<i class="arrow top-right"></i>
										
										<i class="arrow center-left"></i>
										<i class="arrow center-right"></i>

										<i class="arrow bottom-left"></i>
										<i class="arrow bottom-center"></i>
										<i class="arrow bottom-right"></i>
									</div>
									<div class="col-md-4 text-center direction-block East-block">
										<h4><?php echo mlx_get_lang('East'); ?></h4>
										<div class="direction-listing">
											<ul class="list-group">
												<?php if (isset($saved_distance_list) && isset($saved_distance_list['East']) && !empty($saved_distance_list['East'])) {
													foreach ($saved_distance_list['East'] as $dk => $dv) {
												?>
														<li class="list-group-item">
															<span class="badge badge-danger">X</span>
															<?php if (isset($dv['title']) && !empty($dv['title']))
																echo ucfirst($dv['title']) . '<br />';
															?>
															<?php if (isset($dv['entity']) && !empty($dv['entity']))
																echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
															?>
															<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
															?>
															<input type="hidden" name="distance[East][title][]" value="<?php echo $dv['title'] ?>">
															<input type="hidden" name="distance[East][entity][]" value="<?php echo $dv['entity'] ?>">
															<input type="hidden" name="distance[East][measurement][]" value="<?php echo $dv['measurement'] ?>">
															<input type="hidden" name="distance[East][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
														</li>
												<?php }
												} ?>
											</ul>
										</div>
										<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="East"><i class="fa fa-plus"></i></button>
									</div>
								</div>

								<div class="row no-gutters">
									<div class="col-md-4 text-center direction-block South-West-block">
										<h4><?php echo mlx_get_lang('South-West'); ?></h4>

										<div class="direction-listing">
											<ul class="list-group">
												<?php if (isset($saved_distance_list) && isset($saved_distance_list['South-West']) && !empty($saved_distance_list['South-West'])) {
													foreach ($saved_distance_list['South-West'] as $dk => $dv) {
												?>
														<li class="list-group-item">
															<span class="badge badge-danger">X</span>
															<?php if (isset($dv['title']) && !empty($dv['title']))
																echo ucfirst($dv['title']) . '<br />';
															?>
															<?php if (isset($dv['entity']) && !empty($dv['entity']))
																echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
															?>
															<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
															?>
															<input type="hidden" name="distance[South-West][title][]" value="<?php echo $dv['title'] ?>">
															<input type="hidden" name="distance[South-West][entity][]" value="<?php echo $dv['entity'] ?>">
															<input type="hidden" name="distance[South-West][measurement][]" value="<?php echo $dv['measurement'] ?>">
															<input type="hidden" name="distance[South-West][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
														</li>
												<?php }
												} ?>
											</ul>
										</div>

										<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="South-West"><i class="fa fa-plus"></i></button>
									</div>

									<div class="col-md-4 text-center direction-block South-block">
										<h4><?php echo mlx_get_lang('South'); ?></h4>
										<div class="direction-listing">
											<ul class="list-group">
												<?php if (isset($saved_distance_list) && isset($saved_distance_list['South']) && !empty($saved_distance_list['South'])) {
													foreach ($saved_distance_list['South'] as $dk => $dv) {
												?>
														<li class="list-group-item">
															<span class="badge badge-danger">X</span>
															<?php if (isset($dv['title']) && !empty($dv['title']))
																echo ucfirst($dv['title']) . '<br />';
															?>
															<?php if (isset($dv['entity']) && !empty($dv['entity']))
																echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
															?>
															<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
															?>
															<input type="hidden" name="distance[South][title][]" value="<?php echo $dv['title'] ?>">
															<input type="hidden" name="distance[South][entity][]" value="<?php echo $dv['entity'] ?>">
															<input type="hidden" name="distance[South][measurement][]" value="<?php echo $dv['measurement'] ?>">
															<input type="hidden" name="distance[South][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
														</li>
												<?php }
												} ?>
											</ul>
										</div>
										<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="South"><i class="fa fa-plus"></i></button>
									</div>
									<div class="col-md-4 text-center direction-block South-East-block">
										<h4><?php echo mlx_get_lang('South-East'); ?></h4>
										<div class="direction-listing">
											<ul class="list-group">
												<?php if (isset($saved_distance_list) && isset($saved_distance_list['South-East']) && !empty($saved_distance_list['South-East'])) {
													foreach ($saved_distance_list['South-East'] as $dk => $dv) {
												?>
														<li class="list-group-item">
															<span class="badge badge-danger">X</span>
															<?php if (isset($dv['title']) && !empty($dv['title']))
																echo ucfirst($dv['title']) . '<br />';
															?>
															<?php if (isset($dv['entity']) && !empty($dv['entity']))
																echo '<strong>' . ucfirst($dv['entity']) . '</strong> - ';
															?>
															<?php echo $dv['measurement'] . ' ' . ucfirst($dv['measurement_type']);
															?>
															<input type="hidden" name="distance[South-East][title][]" value="<?php echo $dv['title'] ?>">
															<input type="hidden" name="distance[South-East][entity][]" value="<?php echo $dv['entity'] ?>">
															<input type="hidden" name="distance[South-East][measurement][]" value="<?php echo $dv['measurement'] ?>">
															<input type="hidden" name="distance[South-East][measurement_type][]" value="<?php echo $dv['measurement_type'] ?>">
														</li>
												<?php }
												} ?>
											</ul>
										</div>
										<button type="button" class="btn btn-<?php echo get_skin_class(); ?> btn-sm " title="Add Distance" data-toggle="modal" data-target=".direction-modal" data-direction="South-East"><i class="fa fa-plus"></i></button>
									</div>
								</div>

							</div>

							<script>
								$(document).ready(function() {
									$('.direction-modal').on('show.bs.modal', function(event) {
										var button = $(event.relatedTarget);
										var modal = $(this);
										var direction = button.attr('data-direction');

										var callback = 'manage_direction';
										$.ajax({
											url: base_url + 'admin_ajax',
											type: 'POST',
											success: function(res) {
												modal.find('.modal-content').append(res.modal_content);
												modal.find('.modal-overlay').hide();
											},
											data: {
												direction: direction,
												callback: callback
											},
											cache: false
										});

									});

									$('.direction-modal').on('hidden.bs.modal', function(event) {
										var modal = $(this);
										modal.find('.modal-content').html('');
										modal.find('.modal-overlay').show();
									});

									$(document).delegate('.direction-modal-form', 'submit', function() {

										var thiss = $(this);
										var callback = 'add_direction';
										var direction = thiss.find('input[type="hidden"][name="direction"]').val();
										$.ajax({
											url: base_url + 'admin_ajax',
											type: 'POST',
											success: function(res) {
												$('.' + direction + '-block').find('.list-group').append(res.output);
												$('.direction-modal').modal('hide');
											},
											data: thiss.serialize() + '&callback=' + callback,
											cache: false
										});
										return false;
									});

									$('.direction-listing .list-group').sortable();

									$(document).delegate('.direction-listing .list-group-item span.badge', 'click', function() {
										if (confirm('Do you really want to delete?')) {
											var thiss = $(this);
											thiss.parents('.list-group-item').remove();
										}
									});
								});
							</script>


						<?php } ?>
						</div>
						<!-- <div class="box-footer">
							<button class="btn btn-success btn-sm pull-right" data-toggle="tooltip" data-placement="left" title="Add Field" id="add_fields">ADD</button>
						</div> -->
					</div>
					<?php //} 
					?>

					<?php if (isset($distances_list) && !empty($distances_list) && 0) { ?>
						<?php
						$direction_list = array(
							'East' => 'East',
							'West' => 'West',
							'North' => 'North',
							'South' => 'South',
							'North-East' => 'North-East',
							'South-East' => 'South-East',
							'South-West' => 'South-West',
							'North-West' => 'North-West',
						);
						?>
						<div class="box box-<?php echo get_skin_class(); ?>">
							<div class="box-header with-border">
								<h3 class="box-title"><?php echo mlx_get_lang('Distances'); ?></h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div>
							</div>
							<div class="box-body">

								<?php
								foreach ($distances_list as $k => $v) {
								?>
									<div class="row form-group">
										<div class="col-md-4">
											<label for="<?php echo $v; ?>"><?php echo mlx_get_lang($v); ?></label>
										</div>
										<div class="col-md-4">
											<select name="distance_list[<?php echo str_replace(' ', '_', $v); ?>][direction]" id="<?php echo str_replace(' ', '_', $v); ?>" class="direction">
												<option value=""><?php echo mlx_get_lang('Select Any Direction'); ?></option>
												<?php if (isset($direction_list) && !empty($direction_list)) {
													foreach ($direction_list as $dk => $dv) {
												?>
														<option value="<?php echo $dk; ?>" <?php if (isset($saved_distance_list) && array_key_exists($v, $saved_distance_list)) {
																								if ($saved_distance_list[$v]['direction'] == $dk) {
																									echo ' selected="selected" ';
																								}
																							} ?>><?php echo mlx_get_lang($dv); ?></option>
												<?php }
												} ?>
											</select>
										</div>
										<div class="col-md-4">
											<div class="input-group">
												<input type="number" min="0" step=".1" value="<?php if (isset($saved_distance_list) && array_key_exists($v, $saved_distance_list)) {
																									if (isset($saved_distance_list[$v]['distance'])) {
																										echo $saved_distance_list[$v]['distance'];
																									} else
																										echo '0';
																								} else
																									echo '0'; ?>" name="distance_list[<?php echo str_replace(' ', '_', $v); ?>][distance]" class="form-control">
												<div class="input-group-btn">
													<input type="hidden" name="distance_list[<?php echo str_replace(' ', '_', $v); ?>][distance_text]" value="<?php if (isset($saved_distance_list) && array_key_exists($v, $saved_distance_list)) {
																																									if (isset($saved_distance_list[$v]['distance_text'])) {
																																										echo $saved_distance_list[$v]['distance_text'];
																																									} else
																																										echo 'Meter';
																																								} else
																																									echo 'Meter'; ?>">
													<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php if (isset($saved_distance_list) && array_key_exists($v, $saved_distance_list)) {
																																									if (isset($saved_distance_list[$v]['distance_text'])) {
																																										echo $saved_distance_list[$v]['distance_text'];
																																									} else
																																										echo 'Meter';
																																								} else
																																									echo 'Meter'; ?>&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
													<ul class="dropdown-menu custom-dropdown-menu">
														<li><a href="#"><?php echo mlx_get_lang('Meter'); ?></a></li>
														<li><a href="#"><?php echo mlx_get_lang('KM'); ?></a></li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>

							</div>
						</div>
					<?php } ?>

					<div class="box box-<?php echo get_skin_class(); ?>">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo mlx_get_lang('Property Gallery'); ?></h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<div class="box-body">
							<div class="form-group" align="center">
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="property_pl_image_container">
											<label class="custom-file-upload" data-element_id="" data-type="media" id="pl_file_uploader_1">
												<?php echo mlx_get_lang('Drop images here'); ?>
												<br />
												<strong><?php echo mlx_get_lang('OR'); ?></strong>
												<br />
												<?php echo mlx_get_lang('Click here to select images'); ?>
											</label>
											<progress class="pl_file_progress" value="0" max="100" style="display:none;"></progress>
											<a class="pl_file_link" href="" download="" style="display:none;">
												<img src="" style="width:50%;">
											</a>
											<a class="ppl_file_remove_img" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
											<input type="hidden" name="blog_image" value="" class="pl_file_hidden">
										</div>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<br>
										<span class="or"><?php echo mlx_get_lang('OR'); ?></span>
										<br>
										<br>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<a onclick="lazy_load_on_media_img()" href="#" class="custom-file-upload add_from_media_btn "><i class="fa fa-camera"></i>&nbsp;<?php echo mlx_get_lang('Add From Media'); ?></a>
										<input type="hidden" name="addedImgFromMediaLibrary" value="<?php if (isset($property_images) && !empty($property_images)) {
																										$data_exp = explode(',', $property_images);
																										$data_imp = array();
																										foreach ($data_exp as $k => $v) {
																											$data_imp[] = $myHelpers->EncryptClientId($v);
																										}
																										echo implode(',', $data_imp);
																									} ?>">
									</div>
								</div>


							</div>

							<div class="form-group" style="margin-bottom:0px;">
								<div class="product-gallary-container">

									<?php
									if (isset($property_images) && !empty($property_images)) {
										$p_g_i = explode(',', $property_images);
										if (count($p_g_i) > 0) {
											foreach ($p_g_i as $key => $val) {
												$img_id = $val;

												$query = "SELECT pi.* FROM `post_images` 
										inner join post_images pi on post_images.image_id = pi.parent_image_id
										and pi.image_type = 'thumbnail'
										WHERE post_images.image_id = $img_id";
												$result = $myHelpers->Common_model->commonQuery($query);
												if ($result->num_rows() > 0) {
													$img_row = $result->row();
													if (!file_exists($img_row->image_path . $img_row->image_name))
														continue;
													echo '<div class="media-img-block ui-sortable-handle">
													 <div class="media_images_inner lazy-load-processing" data-container="body" data-toggle="tooltip" title="' . $img_row->image_alt . '">
														<span class="remove-product-btn"><i class="fa fa-remove"></i></span>
														<img class="lazy-img-elem" data-img_id="' . $myHelpers->global_lib->EncryptClientId($img_id) . '" data-src="' . base_url() . $img_row->image_path . $img_row->image_name . '">
													</div>
												  </div>';
												}
											}
										}
									}
									?>

								</div>
							</div>



						</div>

					</div>

					<div class="box box-<?php echo get_skin_class(); ?>">
						<div class="box-header with-border">
							<h3 class="box-title"><?php echo mlx_get_lang('Property Video'); ?></h3>
							<div class="box-tools pull-right">
								<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>

						<div class="box-body vdo_url_container">
							<?php if (isset($video_url_array) && !empty($video_url_array)) {
								foreach ($video_url_array as $k => $v) {
									$ele_count = $k + 1;
							?>
									<div class="form-group">
										<label for="video_url_<?php echo $ele_count; ?>"><?php echo mlx_get_lang('Video URL'); ?> </label>
										<div class="input-group">
											<input type="url" class="form-control video_url" value="<?php echo $v; ?>" id="video_url_<?php echo $ele_count; ?>" name="video_url[]">

											<span class="input-group-addon text-red remove-video-link">
												<!--<a class="popup-player" href="<?php //echo $v; 
																					?>"><i class="fa fa-play"></a></i>-->
												<i class="fa fa-remove"></i>
											</span>

										</div>
									</div>

								<?php }
							} else { ?>
								<div class="form-group">
									<label for="video_url_1"><?php echo mlx_get_lang('Video URL'); ?> </label>
									<div class="input-group">
										<input type="url" class="form-control video_url" id="video_url_1" name="video_url[]">

										<span class="input-group-addon remove-video-link">
											<!--<a class="popup-player" disabled><i class="fa fa-play"></a></i>-->
											<i class="fa fa-remove"></i>
										</span>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="box-footer">
							<button type="button" class="btn btn-default pull-right add_more_vdo_btn"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add Video'); ?></button>
						</div>
					</div>

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
				<div class="box box-<?php echo get_skin_class(); ?> sticky_sidebar">
					<div class="box-header with-border">
						<h3 class="box-title"> <?php echo mlx_get_lang('Status'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

						</div>
					</div>
					<div class="box-body">
						<span> <?php echo mlx_get_lang('Current Status'); ?> : </span>
						<?php
						if (isset($status)) echo ucfirst($status);
						?>
						<hr>
						<label> <?php echo mlx_get_lang('URL Slug'); ?></label>
						<input type="text" name="slug" value="<?php if (isset($slug)) echo $slug; ?>" class="form-control" />
						<input type="hidden" name="old_slug" value="<?php if (isset($slug)) echo $slug; ?>" />
						
						
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
							$has_req = $myHelpers->global_lib->get_option('admin_approval_require_for_property');
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


<script>
	function lazy_load_on_media_img() {
		setTimeout(function() {
			$('.media_img_block li').each(function() {
				$(this).find('.lazy-img-elem').lazy({
					effect: "fadeIn",
					effectTime: 500,
					threshold: 0,
					afterLoad: function(element) {
						element.parent().removeClass('lazy-load-processing');
					},
				}).trigger("appear");;
				$(this).find('.lazy-img-elem').parent().removeClass('lazy-load-processing');
				var src = $(this).find('.lazy-img-elem').attr('data-src');
				$(this).find('.lazy-img-elem').attr('src', src);
			});
		}, 1000);
	}

	$(document).ready(function() {
		
		$('.nav-tabs-custom ul li').click(function() {
			var thiss = $(this);

			$('.tab-content .tab-pane').each(function(e) {
				var thiss = $(this);
				thiss.find('.required-fields input[type="text"]').attr('required',false);
				thiss.find('.required-fields input[type="text"]').parent().find('label span').remove();

				thiss.find('.required-fields textarea').attr('required',false);
				thiss.find('.required-fields textarea').parent().find('label span').remove();

				thiss.find('.required-fields input[type="number"]').attr('required',false);
				thiss.find('.required-fields input[type="number"]').parent().parent().find('label span').remove();
			});
			

			var target_id = thiss.find('a').attr('href').replace( "#", "" );

			$('.tab-content #'+target_id).find('.required-fields input[type="text"]').attr('required',true);
			$('.tab-content #'+target_id).find('.required-fields input[type="text"]').parent().find('label').append('<span class="text-red">*</span>');

			$('.tab-content #'+target_id).find('.required-fields textarea').attr('required',true);
			$('.tab-content #'+target_id).find('.required-fields textarea').parent().find('label').append('<span class="text-red">*</span>');

			$('.tab-content #'+target_id).find('.required-fields input[type="number"]').attr('required',true);
			$('.tab-content #'+target_id).find('.required-fields input[type="number"]').parent().parent().find('label').append('<span class="text-red">*</span>');
		});

		$(document).delegate(".measurement-group .dropdown-menu li", "click", function () {
	
				var data_val = $(this).find('a').attr('data-val');
				
				$(this).parents('.input-group-btn').find('.dropdown-toggle').html(data_val + '&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
				$(this).parents('.input-group-btn').removeClass('open');
				$(this).parents('.input-group').find('.measurement_type').val(data_val);
				return false;
		});
	
	
		$('.dropdown-menu.size_measure_menus li').click(function() {
			var data_val = $(this).find('a').attr('data-val');

			$(this).parents('.input-group-btn').find('.dropdown-toggle').html(data_val + '&nbsp;&nbsp;<span class="fa fa-caret-down"></span>');
			$(this).parents('.input-group-btn').removeClass('open');
			$(this).parents('.input-group').find('#size_measure').val(data_val);
			return false;
		});
	});
</script>

<?php

/*do_action("admin_property_edit_scripts"); */

do_action("admin_footer_scripts", "location_updates");
do_action("admin_footer_scripts", "property_update_scripts");

?>