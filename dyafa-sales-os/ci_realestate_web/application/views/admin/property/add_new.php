<?php
$user_type = $this->session->userdata('user_type');
$short_desc_limit = 250;


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
}*/
</style>

<script>
	function RemoveRougeChar(convertString) {
		/*var n = convertString.toString();
		//var newvalue = n.replace(/,/g, ''); 
		//var valuewithcomma = newvalue.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		//return valuewithcomma;*/
		return convertString;
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
	});
	
</script>

<?php
$site_language = get_option('site_language');
$enable_multi_language = get_option('enable_multi_language');
$default_language = get_option('default_language');
$locations = get_option('locations');
if (!empty($locations)) {
	$loc_list = json_decode($locations, true);
}
?>

<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-plus"></i> <?php echo mlx_get_lang('Add New Property'); ?> </h1>
		<?php

		if ($this->site_payments == 'Y' &&  $this->post_property_credit <= 0  && $user_type != 'admin') { ?>
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
		echo form_open_multipart('admin/property/add_new', $attributes); ?>

		<div class="row">
			<div class="col-md-8">

				<div class="box box-<?php echo get_skin_class(); ?> ">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Property Details'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">

						<?php

						if (isset($enable_multi_language) && $enable_multi_language == 'Y') {
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
											?>

												<div class="<?php if ($n == 1) echo 'active'; ?> tab-pane" id="<?php echo $lang_code; ?>">
													<div class="form-group required-fields">
														<label for="title_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Title'); ?> <?php if ($n == 1) { ?><span class="text-red">*</span><?php } ?></label>
														<input type="text" class="form-control" <?php if ($n == 1) { ?>required="required" <?php } ?> name="multi_lang[<?php echo $lang_code; ?>][title]" id="title_<?php echo $lang_code; ?>">
													</div>

													<div class="form-group">
														<label for="short_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Short Description'); ?></label>
														<textarea class="form-control short-description-element" maxlength="<?php echo $short_desc_limit; ?>" rows="3" id="short_description_<?php echo $lang_code; ?>" name="multi_lang[<?php echo $lang_code; ?>][short_description]"></textarea>
														<span class="rchars" id="rchars_<?php echo $lang_code; ?>"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
													</div>

													<div class="form-group required-fields">
														<label for="description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Description'); ?> <?php if ($n == 1) { ?><span class="text-red">*</span><?php } ?></label>
														<textarea class="form-control ckeditor-element" data-lang_code="<?php echo $lang_code; ?>" data-lang_dir="<?php echo $v['direction']; ?>" rows="3" id="description_<?php echo $lang_code; ?>" <?php if ($n == 1) { ?>required<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][description]"></textarea>
													</div>

													<div class="form-group required-fields">
														<label for="price_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Price'); ?> <?php if ($n == 1 && 0) { ?><span class="text-red">*</span><?php } ?></label>
														<div class="input-group">
															<span class="input-group-addon">
																<?php echo $myHelpers->global_lib->get_currency_symbol($v['currency']); ?>
															</span>
															<input type="text"  class="form-control" <?php if ($n == 1 && 0) { ?>required<?php } ?> name="multi_lang[<?php echo $lang_code; ?>][price]" id="price_<?php echo $lang_code; ?>">
															<span class="input-group-addon property_type_rent_block" style="display:none;">
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
														<input type="text" class="form-control" name="multi_lang[<?php echo $lang_code; ?>][seo_meta_keywords]" id="meta_keywrod_<?php echo $lang_code; ?>" value="">
													</div>

													<div class="form-group">
														<label for="meta_description_<?php echo $lang_code; ?>"><?php echo mlx_get_lang('Meta Description'); ?></label>
														<textarea class="form-control" rows="3" id="meta_description_<?php echo $lang_code; ?>" name="multi_lang[<?php echo $lang_code; ?>][seo_meta_description]"></textarea>
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
								<label for="title"><?php echo mlx_get_lang('Title'); ?> <span class="required">*</span></label>
								<input type="text" class="form-control" required="required" name="title" id="title">
							</div>

							<div class="form-group">
								<label for="short_description"><?php echo mlx_get_lang('Short Description'); ?></label>
								<textarea class="form-control short-description-element" rows="3" id="short_description" name="short_description" maxlength="<?php echo $short_desc_limit; ?>"></textarea>
								<span class="rchars" id="rchars"><?php echo $short_desc_limit; ?></span> <?php echo mlx_get_lang('Character(s) Remaining'); ?>
							</div>

							<div class="form-group required-fields">
								<label for="description"><?php echo mlx_get_lang('Description'); ?> <span class="required">*</span></label>
								<textarea class="form-control ckeditor-element" required data-lang_code="<?php echo $lang_code; ?>" rows="2" id="description" name="description"></textarea>
							</div>

							<div class="form-group required-fields">
								<label for="price"><?php echo mlx_get_lang('Price'); ?> </label> <!-- <span class="required">*</span> -->
								<div class="input-group">
									<span class="input-group-addon">
										<?php echo $myHelpers->global_lib->get_currency_symbol($default_currency); ?>
										<!--<i class="fa fa-usd"></i> -->
									</span>
									<input type="text"  class="form-control"  name="price" id="price"> <!-- required="required" -->
									<span class="input-group-addon property_type_rent_block" style="display:none;">
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
								<input type="text" class="form-control" name="seo_meta_keywords" id="meta_keywrod" value="">
							</div>

							<div class="form-group">
								<label for="meta_description"><?php echo mlx_get_lang('Meta Description'); ?></label>
								<textarea class="form-control" rows="3" id="meta_description" name="seo_meta_description"></textarea>
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
						do_action('admin_property_location_fields_before');
						?>

						
						<div class="form-group hide col-md-12">
							<label for="street_number"><?php echo mlx_get_lang('Street Address'); ?> </label>
							<input type="text" class="form-control" id="street_number" name="street_address">
						</div>

						<div class="form-group">
							<label for="address"><?php echo mlx_get_lang('Address'); ?> <span class="text-red">*</span></label>
							<textarea class="form-control" required id="address" name="address"></textarea>
						</div>

						<?php
						do_action('admin_property_location_fields');
						?>

						<?php
						$isOsmAct = $myHelpers->isPluginActive('openstreetmap');
						if ($isOsmAct == true) {
						?>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="openstreetmap"><?php echo mlx_get_lang('Open Street Map'); ?></label>
										<textarea class="form-control no_clean openstreetmap" id="openstreetmap" name="property_meta[openstreetmap_embed_code]" rows="3" col="3"> </textarea>
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
								?>

									<div class="col-md-6 text-right">
										<label class="pull-left" for="<?php echo $v; ?>"><?php echo mlx_get_lang($v); ?></label>
										<input id="<?php echo $v; ?>" class="minimal" type="checkbox" name="indoor_amenities[]" value="<?php echo ucfirst($v); ?>">
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
								?>

									<div class="col-md-6 text-right">
										<label class="pull-left" for="<?php echo $v; ?>"><?php echo mlx_get_lang($v); ?></label>
										<input id="<?php echo $v; ?>" class="minimal" type="checkbox" name="outdoor_amenities[]" value="<?php echo ucfirst($v); ?>">
									</div>

								<?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>

				
					
				<?php $this->load->view("$theme/property/property_distances");?>	

				<?php $this->load->view("$theme/property/property_gallery");?>
				<?php $this->load->view("$theme/property/property_videos");?>
					
					
					

					<?php
					if (function_exists("cms_property_custom_metaboxes")) {
						cms_property_custom_metaboxes();
					}
					?>

					<?php
					if (isset($custom_field_list) && !empty($custom_field_list) && 0) {

					?>

						<div class="box box-<?php echo get_skin_class(); ?>">
							<div class="box-header with-border">
								<h3 class="box-title"><?php echo mlx_get_lang('Custom Fields'); ?></h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div>
							</div>
							<div class="box-body">

								<?php
								$n = 0;
								foreach ($custom_field_list as $cfk => $cfv) {
									$n++;
									$hasChecked = '';
									$curValue = '';
								?>
									<div class="form-group">
										<label for="custom_field_<?php echo $n; ?>"><?php echo mlx_get_lang($cfv['title']); ?> <?php if ($n == 1 && 0) { ?><span class="text-red">*</span><?php } ?></label>
										<input type="text" class="form-control" <?php if ($n == 1 && 0) { ?>required="required" <?php } ?> name="custom_fields[<?php echo $cfv['slug']; ?>]" id="custom_field_<?php echo $n; ?>">
									</div>

								<?php } ?>

							</div>
						</div>
					<?php } ?>

					<?php
					/*
			if(function_exists("cms_property_doc_type_metaboxes"))
			{
				cms_property_doc_type_metaboxes();
			}
			*/
					?>

					<?php do_action('load_custom_metaboxes', 'property'); ?>

					
			</div>

			<div class="col-md-4">


				<?php do_action("cms_admin_property_information_box_aside_before" ); ?>	

				<div class="box box-<?php echo get_skin_class(); ?> sticky_sidebar">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Status'); ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

						</div>
					</div>
					
					<div class="box-body" >
						<div class="form-group">
							<label for="property_type_status"><?php echo mlx_get_lang('Move to Edit Page?'); ?></label>

							<div class="radio_toggle_wrapper ">
								<input type="radio" id="return_to_edit_yes" value="Y" name="return_to_edit" class="toggle-radio-button">
								<label for="return_to_edit_yes"><?php echo mlx_get_lang('Yes'); ?></label>

								<input type="radio" id="return_to_edit_no" value="N" name="return_to_edit" class="toggle-radio-button" 
								checked="checked"
								>
								<label for="return_to_edit_no"><?php echo mlx_get_lang('No'); ?></label>
							</div>
						</div>
					<?php do_action("cms_admin_property_edit_information_aside" ); ?>
					</div>
					
					<div class="box-footer">
						<?php if ($user_type == 'admin') {
						?>
							<button type="submit" name="draft" class="btn btn-draft btn-default" id="save_draft"><?php echo mlx_get_lang('Save as Draft'); ?></button>
							<button name="submit" type="submit" class="btn btn-<?php echo get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Save Publish'); ?></button>
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
				
				<?php do_action("cms_admin_property_information_box_aside_after" ); ?>	


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


<?php 
		$this->load->view("$theme/property/property_edit_scripts");

		do_action("admin_footer_scripts", "location_updates");
		do_action("admin_footer_scripts", "property_update_scripts");

?>