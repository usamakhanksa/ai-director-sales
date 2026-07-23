<?php

/*$currency_symbol = $myHelpers->global_lib->get_currency_symbol();*/
$currency_symbol = $this->site_currency_symbol;
$enable_compare_property = get_option('enable_compare_property');
$enbale_favourite = get_option('enbale_favourite');
$enbale_print_priview = get_option('enbale_print_priview');
$enbale_pdf_export = get_option('enbale_pdf_export');



$enbale_social_share = get_option('enbale_social_share');

$logged_in = $this->session->userdata('logged_in');

if ($logged_in == true) {
	$user_id = $this->session->userdata('user_id');
	$bookmar_checker = $myHelpers->global_lib->get_bookmarks($user_id);
}
global $prop_row;

$prop_row = $single_property;
?>

<?php if (isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/' . $banner_row->b_image)) { ?>
	<section class="page-top-section set-bg d-print-none" data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
		<div class="container text-white">
			<h1><?php echo ucfirst(stripslashes($single_property->title)); ?></h1>
		</div>
	</section>
<?php } ?>

<?php
$prop_attr = '';
if (isset($enable_compare_property) && $enable_compare_property == 'Y') {
	$prop_attr .= ' data-title="' . ucfirst(stripslashes($single_property->title)) . '" ';
	$prop_attr .= ' data-url="' . $single_property->slug . '~' . $single_property->p_id . '"  ';
	$prop_attr .= ' data-id="' . $myHelpers->global_lib->EncryptClientId($single_property->p_id) . '" ';
}
if (isset($enbale_favourite) && $enbale_favourite == 'Y') {


	$prop_attr .= ' data-title="' . ucfirst(stripslashes($single_property->title)) . '" ';
	$prop_attr .= ' data-url="' . $single_property->slug . '~' . $single_property->p_id . '"  ';
	$prop_attr .= ' data-id="' . $myHelpers->global_lib->EncryptClientId($single_property->p_id) . '" ';
}


?>

<style>
	/*
.owl-carousel.slide-one-item.home-slider .owl-item{
    height:500px;
    width:100%;
}
.owl-carousel.sl-thumb-slider .owl-item{
    height:120px;
    width:100%;
}
.owl-carousel.sl-thumb-slider .owl-item .sl-thumb {
    width: 100%;
    height: 100%;
}
.owl-carousel.sl-thumb-slider .owl-item img{
    height:100%;
    width:100%;
}
.img-container {
    padding: 0.25rem;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    width: 100%;
    height: 150px;
    display: inline-block;
    overflow: hidden;
	position:relative;
}
.img-container img{
	position: relative;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: auto;
    max-width: 100%;
    height: auto;
    max-height: 100%;
}
*/
</style>

<div class="site-section site-section-sm single-property-section">
	<input type="hidden" class="prop_id" value="<?php echo EncryptClientID($single_property->p_id); ?>">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 ">

				<div class="property_slider property-entry " <?php echo $prop_attr; ?>>
					<div class="fixed-property-btn-conainter">
						<?php if (isset($enable_compare_property) && $enable_compare_property == 'Y') { ?>
							<a href="#" class="single-prop-add-compare-btn add_to_compare d-print-none" data-toggle="tooltip" title="Add to Compare"><span class="icon-plus"></span></a>
						<?php } ?>
						<?php

						if (isset($enbale_favourite) && $enbale_favourite == 'Y') {
							$selected = '';
							$fav_id = '';
							if (isset($bookmar_checker) && !empty($bookmar_checker) && array_key_exists($single_property->p_id, $bookmar_checker)) {
								$selected = 'fa fa-bookmark';
								$fav_id = 'favorite_btn_remove';
							} else {
								$fav_id = 'favorite_btn';
								$selected = 'fa fa-bookmark-o';
							}

						?>
							<?php
							if ($logged_in == true) {
							?>
								<button class="btn property-favorite single_page_favirate_btn d-print-none <?php if (isset($fav_id)) echo $fav_id; ?>" id="<?php if (isset($fav_id)) echo $fav_id; ?>" data-toggle="tooltip" title="Add to Favorite"><span class="bookmark_icon <?php if (isset($selected)) echo $selected; ?>"></span></button>
							<?php } else { ?>
								<button data-toggle="modal" data-target="#loginModal" class="btn property-favorite not-logged-in single_page_favirate_btn d-print-none <?php if (isset($fav_id)) echo $fav_id; ?>" id="<?php if (isset($fav_id)) echo $fav_id; ?>" data-toggle="tooltip" title="Add to Favorite"><span class="bookmark_icon <?php if (isset($selected)) echo $selected; ?>"></span></button>
							<?php } ?>

							
						<?php
						}

						?>
						
						<?php do_action("cms_property_details_addon_btns" , $single_property->p_id); ?>
						
					</div>
					<?php

					if (!empty($single_property->property_images)) {
						$p_images = $myHelpers->global_lib->get_property_gallery($single_property->p_id, 'original');
						if (!empty($p_images)) {
							foreach ($p_images as $k => $v) {

								$post_image_url = base_url() . $v['original'];
								echo '<div class="media-print"><img src="' . $post_image_url . '" class="img-fluid" style="width:100%;"></div>';
								break;
							}
						}
					} ?>

					<?php


					if (!empty($single_property->property_images)) {
						
						
						$p_images = $myHelpers->global_lib->get_property_gallery($single_property->p_id);
						
						

						if (!empty($p_images)) {
							if (count($p_images) > 1) {
								echo '<div class="slide-one-item home-slider owl-carousel d-print-none" id="sl-slider">';
								$i = 0;
								foreach ($p_images as $k => $v) {
									$i++;
									$post_image_url = base_url() . $v['original'];
									echo '<div class="sl-item set-bg';
									if ($i > 1) {
										echo ' d-print-none ';
									} else {
										echo ' d-print-none ';
									}
									echo '"><img src="' . $post_image_url . '" class="img-responsive" id="media_image"></div>';
								}
								echo '</div>';
							} else {
								foreach ($p_images as $k => $v) {
									$post_image_url = base_url() . $v['original'];
									echo '<div class="sl-item set-bg  d-print-none "><img src="' . $post_image_url . '" class="img-fluid"></div>';
								}
							}
						} else {
							//$post_image_url = base_url() . 'themes/' . $theme . '/assets/images/single-no-property-image.jpg';
							$post_image_url = $myHelpers->no_property_image;
							echo '<div ><img src="' . $post_image_url . '" class="img-fluid"></div> ';
						}
					} else {
						$post_image_url = $myHelpers->no_property_image;
						echo '<div><img src="' . $post_image_url . '" class="img-fluid"></div>';
					}


					?>


					<?php
					if (!empty($single_property->property_images)) {
						$p_images = $myHelpers->global_lib->get_property_gallery($single_property->p_id, 'thumbnail');

						

						if (!empty($p_images) && count($p_images) > 1) {
							echo '<div class="owl-carousel sl-thumb-slider d-print-none" id="sl-slider-thumb">';
							foreach ($p_images as $k => $v) {
								$post_image_url = base_url() . $v['thumbnail'];
								echo '<div class="sl-thumb set-bg" style="background-image:url(' . $post_image_url . ')"></div>';
								/*
							echo '<div class="sl-thumb set-bg"><img src="'.$post_image_url.'" class="img-fluid"></div>';
							*/
							}
							echo '</div>';
						}
					}
					?>
				</div>
				<div class="bg-white property-body border-bottom border-left border-right">


					<?php

					$is_price_set = true;
					$is_desc_set = true;
					$is_address_set = true;
					if (isset($this->enable_multi_lang) && $this->enable_multi_lang == true) {
						$def_lang_code = $this->default_language;

						$ret_data = $myHelpers->global_lib->get_property_price_by_lang($single_property->p_id, $def_lang_code);
						if (!empty($ret_data)) {
							$single_property->price = $ret_data['price'];
							/*$currency_symbol = $ret_data['currency'];*/
						} else {
							$is_price_set = false;
						}

						$dec_data = $myHelpers->global_lib->get_property_description_by_lang($single_property->p_id, $def_lang_code);
						if (!empty($dec_data)) {
							$single_property->description = $dec_data;
						} else {
							$is_desc_set = false;
						}

						$add_data = $myHelpers->global_lib->get_property_address_by_lang($single_property->p_id, $def_lang_code);
						if (!empty($add_data)) {
							$single_property->address = $add_data;
						} else {
							$is_address_set = false;
						}
					}

					?>



					<div class="row mb-2">


						<?php do_action("cms_property_information_before_title" , $single_property);?>

						<div class="col-md-8 text-left">
							<h4 class="text-black"><?php echo ucfirst(stripslashes($single_property->title)); ?></h4>
							<p class="lead">
								<?php if ($is_address_set) { ?>
									<i class="fa fa-map-marker"></i> <?php echo $single_property->address; ?>
								<?php } else {
									echo mlx_get_lang('Address Not Set');
								} ?>
							</p>
						</div>
						<?php do_action("cms_property_information_after_title" , $single_property);?>
						
						<div class="col-md-4 text-right">
							<strong class=" price-block-btn btn-block btn-lg text-black h1 mb-3 text-center">
								<?php
								if ($is_price_set) {
									$args = array("currency_symbol" => $currency_symbol);
									echo $myHelpers->global_lib->moneyFormatDollar($single_property->price, $args);

									if ($single_property->property_for == 'Rent') {
										echo '/' . mlx_get_lang('Month');
									}
								} else {
									echo mlx_get_lang('Price Not Set');
								}
								?></strong>
						</div>

					</div>
					<div class="row border-bottom border-top">

						<?php if ($single_property->prop_type_title) { ?>
							<div class="col-md-6 col-lg-3 text-center py-3">
								<span class="d-inline-block text-black mb-0 caption-text"><?php echo mlx_get_lang('Property Type'); ?></span>
								<strong class="d-block"><?php echo mlx_get_lang(ucfirst($single_property->prop_type_title)); ?></strong>
							</div>
						<?php } ?>

						<?php if ($single_property->size) { ?>
							<div class="col-md-6 col-lg-3 text-center py-3">
								<span class="d-inline-block text-black mb-0 caption-text"><?php echo mlx_get_lang('Size'); ?></span>
								<strong class="d-block"><?php echo str_replace('~', ' ', $single_property->size); ?></strong>
							</div>
						<?php } ?>

						<?php if ($single_property->bedroom) { ?>
							<div class="col-md-6 col-lg-3 text-center py-3">
								<span class="d-inline-block text-black mb-0 caption-text"><?php echo mlx_get_lang('Beds'); ?></span>
								<strong class="d-block"><?php echo $single_property->bedroom; ?></strong>
							</div>
						<?php } ?>

						<?php if ($single_property->bathroom) { ?>
							<div class="col-md-6 col-lg-3 text-center py-3">
								<span class="d-inline-block text-black mb-0 caption-text"><?php echo mlx_get_lang('Baths'); ?></span>
								<strong class="d-block"><?php echo $single_property->bathroom; ?></strong>
							</div>
						<?php } ?>


					</div>

					<?php

					$isBlogAct = $myHelpers->isPluginActive('google_map');
					if ($isBlogAct == true  && !empty($single_property->lat) && !empty($single_property->long)) {
						$enable_google_map_js_api = get_option('enable_google_map_js_api');
						$google_map_js_api_key = get_option('google_map_js_api_key');

						if ($enable_google_map_js_api == 'Y' && !empty($google_map_js_api_key)) {
							$gmap_url = "https://maps.google.com/maps?key=$google_map_js_api_key&q=" . $single_property->lat . ", " . $single_property->long . "&z=15&output=embed";
					?>
							<div class="row mt-5">
								<div class="col-md-12">
									<h2 class="h4 text-black text-left"><?php echo mlx_get_lang('Google Map'); ?></h2>
								</div>
								<div class="col-md-12 embed_iframe_container">
									<div id="gMap" style="width:100%;height:350px;"></div>

									<script>
										function myMap() {

											var map = new google.maps.Map(document.getElementById("gMap"), mapOptions);
											var myCenter = new google.maps.LatLng(<?php echo $single_property->lat; ?>, <?php echo $single_property->long; ?>);
											var mapCanvas = document.getElementById("gMap");
											var mapOptions = {
												center: myCenter,
												zoom: 15
											};
											var map = new google.maps.Map(mapCanvas, mapOptions);
											var marker = new google.maps.Marker({
												position: myCenter
											});
											marker.setMap(map);
										}
									</script>

									<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_map_js_api_key; ?>&callback=myMap"></script>
								</div>
							</div>
					<?php
						}
					}
					?>




					<?php
					$isBlogAct = $myHelpers->isPluginActive('openstreetmap');
					if ($isBlogAct == true) {

						if (isset($meta_result['openstreetmap_embed_code']) && !empty($meta_result['openstreetmap_embed_code'])) {
							$meta_value = html_entity_decode(trim($meta_result['openstreetmap_embed_code']));
							if (!empty($meta_value)) {

					?>

								<div class="row mt-5">
									<div class="col-md-12">
										<h2 class="h4 text-black text-left"><?php echo mlx_get_lang('Open Street Map'); ?></h2>
									</div>
									<div class="col-md-12 embed_iframe_container">
										<?php echo $meta_value;  ?>
									</div>
								</div>
					<?php  }
						}
					}
					?>

					<?php if (isset($single_property->description) && !empty($single_property->description)) { ?>

						<h2 class="h4 text-black text-left mt-5"><?php echo mlx_get_lang('Description'); ?></h2>
						<div class="prop_desc text-left">
							<?php
							if ($is_desc_set) {
								echo $single_property->description;
							} else {
								echo mlx_get_lang('Property description not avaiable in current language.');
							}
							?>
						</div>
					<?php } ?>

					<?php
					$this->load->view($theme . "/property/template-part/property-amenities-part");
					$this->load->view($theme . "/property/template-part/property-distances-part");
					$this->load->view($theme . "/property/template-part/property-videos-part");
					$this->load->view($theme . "/property/template-part/property-images-part");
					?>

					<?php
					
					do_action('property_after_main_content');

					?>



				</div>

			</div>
			<div class="col-lg-4 d-print-none p_single_sidebar">
				
				<?php do_action('property_before_sidebar'); ?>
				<?php do_action('property_after_sidebar_widgets'); ?>
				<?php do_action('property_after_sidebar'); ?>

			</div>

		</div>

	</div>
</div>

<style>
	.right-side-fixed-menu {
		position: fixed;
		right: 15px;
		top: 50%;
		z-index: 999;
		transform: translateY(-50%);
	}

	.right-side-fixed-menu ul {
		margin: 0;
		padding: 0;
		list-style-type: none;
	}

	.right-side-fixed-menu ul li a {
		background-color: #667792;
		color: #fff;
		width: 40px;
		height: 40px;
		display: inline-block;
		margin-bottom: 10px;
		text-align: center;
		border-radius: 50%;
		line-height: 40px;

		-webkit-transition: .3s all ease;
		-o-transition: .3s all ease;
		transition: .3s all ease;
	}

	.right-side-fixed-menu ul li a:hover {
		background-color: rgba(0, 0, 0, .5);
	}

	.right-side-fixed-menu ul li:last-child a {
		margin-bottom: 0px;
	}

	.right-side-fixed-menu {
		display: none;
	}

	@media (max-width: 512px) {
		.right-side-fixed-menu {
			display: block;
		}
	}
</style>
<script>
	$(document).ready(function() {
		$(".right-side-fixed-menu li a").click(function() {
			var thiss = $(this);
			var href_text = thiss.attr('href');
			var header_height = $('header .site-navbar').outerHeight() + 15;
			$('html, body').animate({
				scrollTop: $(href_text).offset().top - header_height
			}, 1000);
			return false;
		});
	});
</script>
<div class="right-side-fixed-menu">
	<ul>
		<li>
			<a href="#agent_detail"><i class="fa fa-phone"></i></a>
		</li>

		<?php
		if (isset($enbale_social_share) && $enbale_social_share == 'Y') { ?>
			<li>
				<a href="#whatsapp_link"><i class="fa fa-whatsapp"></i></a>
			</li>
		<?php } ?>

		<?php
		if (isset($enbale_social_share) && $enbale_social_share == 'Y') { ?>
			<li>
				<a href="#social_share"><i class="fa fa-share"></i></a>
			</li>
		<?php } ?>

		<?php
		if (!empty($recentlyViewed) && count($recentlyViewed)) {
		?>
			<li>
				<a href="#recent_viewed"><i class="fa fa-history"></i></a>
			</li>
		<?php } ?>

		<?php if (isset($enbale_agent_contact_form) && $enbale_agent_contact_form == 'Y') { ?>
			<li>
				<a href="#agent_contact_form"><i class="fa fa-info"></i></a>
			</li>
		<?php } ?>

		<?php if (isset($enbale_mortgage_calculator) && $enbale_mortgage_calculator == 'Y') { ?>
			<li>
				<a href="#mortgage_calculator"><i class="fa fa-calculator"></i></a>
			</li>
		<?php } ?>
	</ul>
</div>

<?php if (isset($related_properties) && $related_properties->num_rows() > 0) { ?>
	<div class="site-section site-section-sm bg-light d-print-none">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="site-section-title mb-5">
						<h2 class="text-left"><?php echo mlx_get_lang('Related Properties'); ?></h2>
					</div>
				</div>
			</div>
			<div class="row mb-5">
				<div class="related-property owl-carousel col-md-12">
					<?php foreach ($related_properties->result() as $prop_row) { ?>

						<?php include('template-part/single-property-grid.php'); ?>

					<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
<script>
	$(document).ready(function() {
		$("#print_pre").on('click', function() {
			window.print();
		});
	});
</script>