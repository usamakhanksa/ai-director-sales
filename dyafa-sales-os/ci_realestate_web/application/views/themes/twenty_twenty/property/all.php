<?php if (isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/' . $banner_row->b_image)) { ?>
	<section class="page-top-section set-bg d-print-none" data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
		<div class="container text-white">
			<h1><?php echo ucfirst(stripslashes($page_header_title)); ?></h1>
		</div>
	</section>
<?php } ?>

<div class="site-section site-section-sm pb-0">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="view-options bg-white py-3 px-3 d-md-flex align-items-center">

					<div class="mr-auto">
						<?php $search_link = 'property/';
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
						<a href="<?php echo site_url($view_grid_link); ?>" class="icon-view view-module active"><span class="icon-view_module"></span></a>
						<a href="<?php echo site_url($view_list_link); ?>" class="icon-view view-list"><span class="icon-view_list"></span></a>

					</div>

					<div class="ml-auto d-flex align-items-center">

					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<?php

if (isset($property_list) && $property_list->num_rows() > 0) { ?>
	<div class="site-section site-section-sm bg-light">
		<div class="container">

			<?php
			$view = 'grid';
			if (isset($_GET['view']) && $_GET['view'] == 'list')
				$view = 'list';

			if ($view == 'grid') {
			?>
				<div class="row mb-5">
				<?php } ?>

				<?php
				
				
				
				global $prop_row;

				foreach ($property_list->result() as $prop_row) { ?>

					<?php
					
					if (isset($_GET['view']) && $_GET['view'] == 'list') {
					?>
						<div class="row mb-4">
							<div class="col-md-12">
								<?php
								include('template-part/single-property-list.php');
								?>
							</div>
						</div>
					<?php
					} else {
					?>
						<div class="col-md-6 col-lg-4 mb-4">
							<?php
							include('template-part/single-property-grid.php');
							?>
						</div>

					<?php }
				}
				if ($view == 'grid') {
					?>
				</div>
			<?php } ?>


			<?php if (isset($pagination_links)) { ?>
				<div class="row">
					<div class="col-md-12 text-center">
						<?php echo $pagination_links; ?>
					</div>
				</div>
			<?php } ?>

			<div class="row" style="display:none;">
				<div class="col-md-12 text-center">
					<a href="<?php echo site_url('property'); ?>" class="btn btn-success text-white rounded-0"><?php echo mlx_get_lang('View More'); ?></a>
				</div>
			</div>

		</div>
	</div>
<?php } ?>