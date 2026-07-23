<?php

$enable_compare_property = get_option('enable_compare_property');
$enbale_favourite = get_option('enbale_favourite');

$logged_in = $this->session->userdata('logged_in');

if ($logged_in == true) {
	$user_id = $this->session->userdata('user_id');
	$bookmar_checker = $myHelpers->global_lib->get_bookmarks($user_id);
}
$is_price_set = true;
$property_url = $myHelpers->global_lib->get_property_url($prop_row->p_id, $prop_row);


$def_lang_code = $this->default_language;
if (isset($this->enable_multi_lang) && $this->enable_multi_lang == true) {

	$title = $myHelpers->global_lib->get_property_lang($prop_row->p_id, 'title', $def_lang_code);
	if ($title != '') {
		$prop_row->title = stripslashes($title);
	}

	$ret_data = $myHelpers->global_lib->get_property_price_by_lang($prop_row->p_id, $def_lang_code);

	if (!empty($ret_data)) {
		$prop_row->price = $ret_data['price'];
		/*$currency_symbol = $ret_data['currency'];*/
	} else {
		$is_price_set = false;
	}


	$currency_symbol = $this->site_currency_symbol;
} else {
	$ret_data = $myHelpers->global_lib->get_property_price_by_lang($prop_row->p_id, $def_lang_code);

	if (!empty($ret_data)) {
		$prop_row->price = $ret_data['price'];
		/*$currency_symbol = $ret_data['currency'];*/
	} else {
		$is_price_set = false;
	}
}

?>
<?php
$has_gallery = false;
$gallery_images = array();
$post_image_url =
	$myHelpers->no_property_image_small; //base_url() . 'application/views/' . $theme . '/assets/images/no-property-image.jpg';


if (!empty($prop_row->property_images)) {
	$img_type = 'medium';
	$p_images = $myHelpers->global_lib->get_property_gallery($prop_row->p_id, $img_type);
	if (!empty($p_images) && count($p_images) > 0) {
		$n = 0;
		foreach ($p_images as $k => $v) {
			if (file_exists($v[$img_type])) {
				if ($n > 2)
					break;
				$post_image_url = base_url() . $v[$img_type];
				$has_gallery = true;
				$gallery_images[] = $v[$img_type];
				$n++;
			}
		}

		if (count($gallery_images) == 1 && $has_gallery == true) {
			$has_gallery = false;
		}
	}
}



$prop_attr = '';
if (isset($enable_compare_property) && $enable_compare_property == 'Y') {
	$prop_attr .= ' data-title="' . ucfirst(stripslashes($prop_row->title)) . '" ';
	$prop_attr .= ' data-url="' . $prop_row->slug . '~' . $prop_row->p_id . '"  ';
	$prop_attr .= ' data-id="' . $myHelpers->global_lib->EncryptClientId($prop_row->p_id) . '" ';
}
if (isset($enbale_favourite) && $enbale_favourite == 'Y') {


	$prop_attr .= ' data-title="' . ucfirst(stripslashes($prop_row->title)) . '" ';
	$prop_attr .= ' data-url="' . $prop_row->slug . '~' . $prop_row->p_id . '"  ';
	$prop_attr .= ' data-id="' . $myHelpers->global_lib->EncryptClientId($prop_row->p_id) . '" ';
}



?>

<div class="property-entry h-100" <?php echo $prop_attr; ?>>
	<div class="fixed-property-btn-conainter">
		<?php if (isset($enable_compare_property) && $enable_compare_property == 'Y') { ?>
			<a href="#" class="property-favorite add_to_compare" data-toggle="tooltip" title="Add to Compare"><span class="icon-plus"></span></a>
		<?php } ?>
		<?php
		if (isset($enbale_favourite) && $enbale_favourite == 'Y') {
			$selected = '';
			$fav_id = '';
			if (isset($bookmar_checker) && !empty($bookmar_checker) && array_key_exists($prop_row->p_id, $bookmar_checker)) {
				$selected = 'fa fa-bookmark';
				$fav_id = 'favorite_btn_remove';
				$fev_title = 'Remove from Favorite';
			} else {
				$fav_id = 'favorite_btn';
				$selected = 'fa fa-bookmark-o';
				$fev_title = 'Add to Favorite';
			}

			if ($logged_in == true) {
		?>
				<button class="btn property-favorite gird-favirate_btn <?php if (isset($fav_id)) echo $fav_id; ?> " data-toggle="tooltip" title="<?php echo $fev_title; ?>"><span class="<?php if (isset($selected)) echo $selected; ?> bookmark_icon"></span></button>
			<?php } else { ?>
				<button class="btn not-logged-in property-favorite gird-favirate_btn <?php if (isset($fav_id)) echo $fav_id; ?> " data-toggle="modal" data-target="#loginModal" data-toggle="tooltip" title="<?php echo $fev_title; ?>"><span title="<?php echo $fev_title; ?>" data-toggle="tooltip" class="<?php if (isset($selected)) echo $selected; ?> bookmark_icon"></span></button>
		<?php }
		} ?>

		<?php do_action("cms_property_details_addon_btns" , $prop_row->p_id); ?>
		
	</div>

	<div class="property-thumbnail ">
		<div class="offer-type-wrap">
			<?php if (strtolower($prop_row->property_for) == 'sale') { ?>
				<span class="offer-type bg-danger"><?php echo mlx_get_lang($prop_row->property_for); ?></span>
			<?php } else if (strtolower($prop_row->property_for) == 'rent') { ?>
				<span class="offer-type bg-warning"><?php echo mlx_get_lang($prop_row->property_for); ?></span>
			<?php } ?>
		</div>
		<?php if ($has_gallery && !empty($gallery_images)) { ?>
			<div class="product-gallery-carousel owl-carousel owl-theme" data-nav="yes" data-dots="yes" data-items="1" data-autoplay="yes" data-interval="5000">
				<?php
				foreach ($gallery_images as $gik => $giv) {
					$post_image_url = base_url() . $giv;
				?>
					<a class="" href="<?php echo $property_url; ?>">
						<img src="<?php echo $post_image_url; ?>" class="img-fluid lazy-img-elem" alt="<?php echo ucfirst(stripslashes($prop_row->title)); ?>">
					</a>
				<?php
				}
				?>
			</div>
		<?php } else { ?>
			<div class="img-container ">
				<a class="" href="<?php echo $property_url; ?>">
					<img src="<?php echo $post_image_url; ?>" class="img-fluid lazy-img-elem" alt="<?php echo ucfirst(stripslashes($prop_row->title)); ?>">
				</a>
			</div>
		<?php } ?>
	</div>


	<div class="p-4 property-body">

		<?php do_action("cms_property_details_before_title" , $prop_row); ?>
		<h3 class="property-title text-center">
			<a href="<?php echo $property_url; ?>"><?php echo ucfirst(stripslashes($prop_row->title)); ?></a>
		</h3>
		<?php do_action("cms_property_details_after_title" , $prop_row); ?>
		
		<?php if (!empty($prop_row->address)) { ?>
			<span class="property-location d-block mb-3 text-center"><span class="property-icon icon-room"></span> <?php echo ucfirst($prop_row->address); ?></span>
		<?php } else {
			echo '<span class="d-block mb-3 text-center"></span>';
		} ?>
		<div class="room-info-warp">

			<?php if (
				!empty($prop_row->size) || !empty($prop_row->bedroom) || !empty($prop_row->garage) ||
				!empty($prop_row->bathroom)
			) { ?>
				<div class="room-info">

					<?php if ($prop_row->size) { ?>
						<div class="rf-float">
							<p><i class="fa fa-th-large"></i> <?php echo str_replace('~', ' ', $prop_row->size); ?> </p>
						</div>
					<?php } ?>

					<?php if ($prop_row->bedroom) { ?>
						<div class="rf-float">
							<p><i class="fa fa-bed"></i> <?php echo $prop_row->bedroom; ?> <?php echo mlx_get_lang('Bedrooms'); ?></p>
						</div>
					<?php } ?>

					<?php if ($prop_row->garage) { ?>
						<div class="rf-float">
							<p><i class="fa fa-car"></i> <?php echo $prop_row->garage; ?> <?php echo mlx_get_lang('Garages'); ?></p>
						</div>
					<?php } ?>

					<?php if ($prop_row->bathroom) { ?>
						<div class="rf-float">
							<p><i class="fa fa-bath"></i> <?php echo $prop_row->bathroom; ?> <?php echo mlx_get_lang('Bathrooms'); ?></p>
						</div>
					<?php } ?>

				</div>
			<?php } ?>

			<div class="room-info">
				<div class="rf-float">
					<?php
					$first_name = $this->global_lib->get_user_meta($prop_row->created_by, 'first_name');
					$last_name = $this->global_lib->get_user_meta($prop_row->created_by, 'last_name');
					if (!empty($first_name) || !empty($last_name)) {
					?>
						<p><i class="fa fa-user"></i> <?php echo ucfirst($first_name) . ' ' . ucfirst($last_name); ?></p>
					<?php } ?>
				</div>
				<div class="rf-float">
					<p><i class="fa fa-clock-o"></i> <?php echo ucwords($this->global_lib->relativeTime($prop_row->created_on)); ?></p>
				</div>
			</div>
		</div>
		<a href="<?php echo $property_url; ?>" class="room-price">
			<?php if ($is_price_set) {
				$args = array("currency_symbol" => $currency_symbol);
				echo moneyFormatDollar($prop_row->price, $args);
				if ($prop_row->property_for == 'Rent') echo '/' . mlx_get_lang('Month');
			} else {
				echo mlx_get_lang('Price Not Set');
			}
			?>
		</a>
			
	</div>
</div>