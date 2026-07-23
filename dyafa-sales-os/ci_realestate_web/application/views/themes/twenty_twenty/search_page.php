<?php




$enable_advance_search = get_option('enable_advance_search');

$advance_search_price_range = get_option('advance_search_price_range');
$advance_search_bath = get_option('advance_search_bath');
$advance_search_bed = get_option('advance_search_bed');
$advance_search_indoor_amenities = get_option('advance_search_indoor_amenities');
$advance_search_outdoor_amenities = get_option('advance_search_outdoor_amenities');
?>


<?php if (isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/' . $banner_row->b_image)) {
	$search_banner = true;
?>
	<section class="page-top-section set-bg" data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
		<div class="container text-white">
			<h1><?php echo mlx_get_lang('Search Property'); ?></h1>
		</div>
	</section>
<?php } else {
	$search_banner = false;
} ?>

<style type="text/css">
	@media (min-width: 768px) {

		.col-md-3.cols-5 {
			-webkit-box-flex: 0;
			-ms-flex: 0 0 20% !important;
			flex: 0 0 20% !important;
			max-width: 20% !important;
		}

	}

	.view-options {
		padding: 0.5rem 0px;
		border-bottom: 1px solid rgb(210, 213, 218);
		border-top: 1px solid rgb(210, 213, 218);
		margin-top: 30px;
	}

	.view-options .icon-view {
		font-size: 1rem;
		text-decoration: none;
		position: relative;
		color: #555;
		display: inline-block;
	}

	.view-options .icon-view:hover {
		color: #669c19;
	}

	.view-options .icon-view.active {
		color: #669c19;
	}

	.view-options .icon-view.active::after {
		content: "";
		display: block;
		position: absolute;
		left: 0px;
		bottom: -17px;
		width: 100%;
		height: 3px;
		background: #669c19 none repeat scroll 0% 0%;
	}

	.view-options .icon-view span {
		font-size: 1.5rem;
		vertical-align: middle;
	}
	
	
	
</style>



<div class="site-section site-section-sm pb-0">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				<?php

				if ($search_banner)
					$margin_top = 100;
				else
					$margin_top = 64;
				$attributes = array(
					'name' => 'add_form_post',
					'class' => 'form-search col-md-12',
					'style' => 'margin-top: -' . $margin_top . 'px;',
					'method' => 'get'
				);
				$search = $myHelpers->menu_lib->get_url('search');
				echo form_open_multipart($search, $attributes);

				$view_type_hidden = 'grid';
				if (isset($_GET['view']) && !empty($_GET['view']))
					$view_type_hidden = $_GET['view'];

				if ($view_type_hidden != 'grid') {
				?>
					<input type="hidden" name="view" class="view_type_hidden" value="<?php echo $view_type_hidden; ?>" />
				<?php } ?>

				<div class="row  align-items-end">

					<?php
					global $search_col_class, $states, $cities;

					$search_col_class = "col-md-3";
					$states = $cities = false;

					if ($states && $cities)
						$search_col_class = "col-md-3 cols-5";
					else if (!$states && !$cities)
						$search_col_class = "col-md-4 ";
					else if ($states || $cities)
						$search_col_class = "col-md-3 ";



					?>

					<div class="<?php echo $search_col_class; ?> search-filter-block">
						<label for="list-types"><?php echo mlx_get_lang('Listing Types'); ?></label>
						<div class="select-wrap">
							<span class="icon icon-arrow_drop_down"></span>
							<select name="type" id="list-types" class="form-control d-block rounded-0">
								<option value=""><?php echo mlx_get_lang('Select Property Type'); ?></option>
								<?php
								$property_type_list = get_property_type_lists();
								if (isset($property_type_list) && $property_type_list->num_rows() > 0) {
									foreach ($property_type_list->result() as $prop_row) {

										$prop_type_slug = $prop_row->slug;
								?>
										<option <?php
												if (isset($type) && $type == $prop_type_slug) echo ' selected="selected" ';
												?> value="<?php echo $prop_type_slug; ?>"><?php echo mlx_get_lang(ucfirst($prop_row->title)); ?></option>
								<?php }
								} ?>
							</select>
						</div>
					</div>

					<div class="<?php echo $search_col_class; ?> search-filter-block">
						<label for="offer-types"><?php echo mlx_get_lang('Offer Type'); ?></label>
						<div class="select-wrap">
							<span class="icon icon-arrow_drop_down"></span>

							<select name="for" id="offer-types" class="form-control d-block rounded-0">
								<option value=""><?php echo mlx_get_lang('Select Property For'); ?></option>
								<option value="sale" <?php if (isset($for) && $for == 'sale') echo ' selected="selected" '; ?>><?php echo mlx_get_lang('For Sale'); ?></option>
								<option value="rent" <?php if (isset($for) && $for == 'rent') echo ' selected="selected" '; ?>><?php echo mlx_get_lang('For Rent'); ?></option>

							</select>
						</div>
					</div>

					<?php do_action("location_search_fields"); ?>
					<?php do_action("location_search_scripts"); ?>


					<div class="<?php echo $search_col_class; ?>">
						<button type="submit" class="btn btn-success text-white btn-block rounded-0"><?php echo mlx_get_lang('Search'); ?></button>
					</div>
				</div>

				<?php

				$advance_search_price_range = $myHelpers->global_lib->get_option('advance_search_price_range');
				$advance_search_bath = $myHelpers->global_lib->get_option('advance_search_bath');
				$advance_search_bed = $myHelpers->global_lib->get_option('advance_search_bed');
				$advance_search_indoor_amenities = $myHelpers->global_lib->get_option('advance_search_indoor_amenities');
				$advance_search_outdoor_amenities = $myHelpers->global_lib->get_option('advance_search_outdoor_amenities');

				if ($enable_advance_search == 'Y' && ($advance_search_price_range == 'Y' || $advance_search_bath == 'Y' ||
					$advance_search_bed == 'Y' || $advance_search_indoor_amenities == 'Y' ||
					$advance_search_outdoor_amenities == 'Y')) {
					include('properties-advance-search.php');
				}
				?>

				</form>
			</div>
		</div>
		<?php

		if (isset($search_properties) && $search_properties->num_rows() > 0) { ?>
			<div class="row">
				<div class="col-md-12">
					<div class="view-options bg-white py-3 px-3 d-md-flex align-items-center">

						<div class="mr-auto">
							<?php
							$search_link = 'search/';
							if (isset($_SERVER['REDIRECT_URL']))
								$search_link = "http://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REDIRECT_URL'];

							$search_attr = array();
							foreach ($_GET as $k => $v) {
								if ($k != 'view')
									$search_attr[] = $k . '=' . $v;
							}
							$view_grid_link = $search_link;
							$view_list_link = $search_link;
							$view_map_link = $search_link;
							if (empty($search_attr)) {
								$view_grid_link .= "?" . implode("&", array('view=grid'));
								$view_list_link .= "?" . implode("&", array('view=list'));
								$view_map_link .= "?" . implode("&", array('view=map'));
							} else {
								$view_grid_link .= "?" . implode("&", array_merge($search_attr, array('view=grid')));
								$view_list_link .= "?" . implode("&", array_merge($search_attr, array('view=list')));
								$view_map_link .= "?" . implode("&", array_merge($search_attr, array('view=map')));
							}

							?>
							<a href="<?php echo ($view_grid_link); ?>" class="icon-view view-module <?php if ($view_type_hidden == 'grid') echo 'active'; ?>"><span class="icon-view_module"></span> <?php echo mlx_get_lang('Grid'); ?></a>
							&nbsp;&nbsp;
							<a href="<?php echo ($view_list_link); ?>" class="icon-view view-list <?php if ($view_type_hidden == 'list') echo 'active'; ?>"><span class="icon-view_list"></span> <?php echo mlx_get_lang('List'); ?></a>
							<?php
								$isGmapPluginAct = $myHelpers->isPluginActive('google_map');
								if ($isGmapPluginAct == true) {
									$enable_google_map_js_api = $myHelpers->global_lib->get_option('enable_google_map_js_api');
									$google_map_js_api_key = $myHelpers->global_lib->get_option('google_map_js_api_key');
									if ($enable_google_map_js_api == 'Y' && !empty($google_map_js_api_key)) {
							?>
									&nbsp;&nbsp;
									<a href="<?php echo ($view_map_link); ?>" class="icon-view view-list <?php if ($view_type_hidden == 'map') echo 'active'; ?>"><span class="icon-map-marker"></span> <?php echo mlx_get_lang('Map'); ?></a>
							<?php }} ?>

						</div>

						<div class="ml-auto d-flexS align-items-center d-none">
							<div>
								<a href="<?php echo $myHelpers->menu_lib->get_url('search'); ?>" class="view-list px-3 border-right active"><?php echo mlx_get_lang('All'); ?></a>
								<a href="<?php echo $myHelpers->menu_lib->get_url('property_for_rent');  ?>" class="view-list px-3 border-right"><?php echo mlx_get_lang('Rent'); ?></a>
								<a href="<?php echo $myHelpers->menu_lib->get_url('property_for_sale');  ?>" class="view-list px-3"><?php echo mlx_get_lang('Sale'); ?></a>
							</div>

						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>




<?php include('properties-search-result.php'); ?>