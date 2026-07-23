<?php
$enable_property_for_cities = $myHelpers->global_lib->get_option('enable_property_for_cities');
$property_for_cities = $myHelpers->global_lib->get_option('property_for_cities');

$enable_property_for_states = $myHelpers->global_lib->get_option('enable_property_for_states');
$property_for_states = $myHelpers->global_lib->get_option('property_for_states');
?>




<div class="site-section site-section-sm pb-0" style="padding:0 0;">
	<div class="container">
		<div class="row">
			<?php
			$attributes = array(
				'name' => 'add_form_post',
				'class' => 'form-search col-md-12',
				'style' => 'margin: 80px 0;',
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
				<div class="col-md-3 search-filter-block">
					<label for="list-types"><?php echo mlx_get_lang('Listing Types'); ?></label>
					<div class="select-wrap">
						<span class="icon icon-arrow_drop_down"></span>
						<select name="type" id="list-types" class="form-control d-block rounded-0">
							<option value=""><?php echo mlx_get_lang('Select Property Type'); ?></option>
							<?php
							if (isset($property_type_list) && $property_type_list->num_rows() > 0) {
								foreach ($property_type_list->result() as $prop_row) {

									$prop_type_slug = $prop_row->slug;
							?>
									<option <?php
											if (isset($type) && $type == $prop_type_slug) echo ' selected="selected" ';
											?> value="<?php echo $prop_type_slug; ?>"><?php echo ucfirst($prop_row->title); ?></option>
							<?php }
							} ?>
						</select>
					</div>
				</div>

				<div class="col-md-3 search-filter-block">
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

				<?php do_action("property_search_fields"); ?>


				<div class="col-md-3">
					<button type="submit" class="btn btn-success text-white btn-block rounded-0"><?php echo mlx_get_lang('Search'); ?></button>
				</div>
			</div>

			<?php //include('properties-advance-search.php'); 
			?>

			</form>
		</div>

		<div class="row" style="display:none;">
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
						if (empty($search_attr)) {
							$view_grid_link .= "?" . implode("&", array('view=grid'));
							$view_list_link .= "?" . implode("&", array('view=list'));
						} else {
							$view_grid_link .= "?" . implode("&", array_merge($search_attr, array('view=grid')));
							$view_list_link .= "?" . implode("&", array_merge($search_attr, array('view=list')));
						}

						?>
						<a href="<?php echo ($view_grid_link); ?>" class="icon-view view-module active"><span class="icon-view_module"></span></a>
						<a href="<?php echo ($view_list_link); ?>" class="icon-view view-list"><span class="icon-view_list"></span></a>

					</div>

					<div class="ml-auto d-flex align-items-center">
						<div>
							<a href="<?php echo $myHelpers->menu_lib->get_url('search'); ?>" class="view-list px-3 border-right active"><?php echo mlx_get_lang('All'); ?></a>
							<a href="<?php echo $myHelpers->menu_lib->get_url('property_for_rent');  ?>" class="view-list px-3 border-right"><?php echo mlx_get_lang('Rent'); ?></a>
							<a href="<?php echo $myHelpers->menu_lib->get_url('property_for_sale');  ?>" class="view-list px-3"><?php echo mlx_get_lang('Sale'); ?></a>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>



<?php //include('properties-search-result.php'); 
?>