<?php
$enable_compare_property = get_option('enable_compare_property');

$footer_text 			= 		get_option_lang('footer_text', $this->default_language);
$copyright_text 		= 	get_option_lang('copyright_text', $this->default_language);

/*$sql = "select * from property_types where status = 'Y' order by title";
$property_type_list = $myHelpers->Common_model->commonQuery($sql); */
$property_type_list = get_property_type_lists();
?>

<?php echo script_tag("application/views/$theme/assets/js/modernizr-2.6.2.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/jquery-migrate-3.0.1.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/jquery-ui.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/popper.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/bootstrap.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/owl.carousel.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/mediaelement-and-player.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/jquery.stellar.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/jquery.countdown.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/jquery.magnific-popup.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/bootstrap-datepicker.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/icheck.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/price-range.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/validation/jquery.validate.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/plugins/lazy-load/jquery.lazy.min.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/price-range.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/main.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/custom.js"); ?>
<?php echo script_tag("application/views/$theme/assets/js/advance_serach.js"); ?>

<?php do_action("cms_footer"); ?>

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header pt-2 pb-1">
				<h5 class="modal-title" id="exampleModalLongTitle"><?php echo mlx_get_lang('Login'); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p class="text-center"><?php echo mlx_get_lang('You have to login first to proceed for further actions.'); ?></p>
				<h3 class="h5 text-black mb-3 text-center mb-4 social-login-heading"><?php echo mlx_get_lang('Social Login'); ?></h3>
				<div class="social-login-block text-center">
					<a href="<?php echo base_url(); ?>google_login" class="btn btn-dark text-white google"><i class="fa fa-google"></i> <?php echo mlx_get_lang('Gmail'); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

do_action("cms_footer_scripts", "contact_agent_form_scripts");


?>


<footer class="site-footer d-print-none">
	<div class="container">
		<div class="row">
			<?php if (isset($footer_text) && !empty($footer_text)) { ?>
				<div class="col-lg-4">
					<div class="mb-5">

						<h3 class="footer-heading mb-4"><?php echo mlx_get_lang('About'); ?></h3>
						<p><?php echo $footer_text; ?></p>
					</div>
				</div>
			<?php } ?>

			<div class="col-lg-3">
				<div class="row mb-5">
					<div class="col-md-12">
						<h3 class="footer-heading mb-4"><?php echo mlx_get_lang('Navigations'); ?></h3>
					</div>
					<div class="col-md-12 col-lg-12">
						<ul class="list-unstyled">
							<?php $footer_menu = get_option('footer_menu');
							
							$footer_menu = apply_filters("cms_get_details", '','footer_menu');
							
							$app_menu_static_pages = apply_filters("app_menu_static_pages_append_menu_items");

							if (isset($footer_menu) && !empty($footer_menu)) {
								$menu_meta = json_decode($footer_menu, true);
								foreach ($menu_meta as $hmk => $hmv) {
									$p_url = '#';
									$menu_id_exp = explode('~', $hmv['id']);
									$menu_type = $menu_id_exp[0];
									$menu_slug = $menu_id_exp[1];
									$active_class = '';
									
	  
									if($menu_type == 'static')
									  {
										  if(is_array($app_menu_static_pages) &&  array_key_exists($menu_slug, $app_menu_static_pages)){
												$menu_link =  $app_menu_static_pages[$menu_slug]['link']; 
										
												$p_url = $myHelpers->menu_lib->get_link_url($menu_slug , $menu_link);
										  }
										  
									  }
									  else if($menu_type == 'page')
									  {
										  
										  $page_slug = $myHelpers->global_lib->get_page_slug_by_id($menu_slug);
										  $p_url = $myHelpers->menu_lib->get_url('page='.$page_slug); 
									  }
									  else if($menu_type == 'custom_link')
									  {
										  $p_url = $menu_slug; 
									  }
							?>
									<li class="<?php echo $active_class; ?>">
										<a href="<?php echo $p_url; ?>"><?php echo mlx_get_lang($hmv['name']); ?></a>
									</li>
								<?php
								}
							} else {
								?>
								<li><a href="<?php echo $myHelpers->menu_lib->get_url("home"); ?>"><?php echo mlx_get_lang('Home'); ?></a></li>
								<li><a href="<?php echo $myHelpers->menu_lib->get_url('property_for_sale'); ?>"><?php echo mlx_get_lang('Sale'); ?></a></li>
								<li><a href="<?php echo $myHelpers->menu_lib->get_url('property_for_rent'); ?>"><?php echo mlx_get_lang('Rent'); ?></a></li>
								<li><a href="<?php echo $myHelpers->menu_lib->get_url('page=about-us'); ?>"><?php echo mlx_get_lang('About Us'); ?></a></li>
								<li><a href="<?php echo $myHelpers->menu_lib->get_url('contact'); ?>"><?php echo mlx_get_lang('Contact Us'); ?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>

			<?php
			if (isset($property_type_list) && $property_type_list->num_rows() > 0) {

			?>
				<div class="col-lg-2">
					<div class="row mb-5">
						<div class="col-md-12">
							<h3 class="footer-heading mb-4"><?php echo mlx_get_lang('Property Type'); ?></h3>
						</div>
						<div class="col-md-12 col-lg-12">
							<ul class="list-unstyled">
								<?php
								foreach ($property_type_list->result() as $prop_row) {

									/*$type = $myHelpers->menu_lib->get_url('type='.strtolower($prop_row->title));*/
									$prop_type_slug = $prop_row->slug;
									$type = $myHelpers->menu_lib->get_url('type=' . $prop_type_slug);
								?>
									<li><a href="<?php echo $type; ?>"><?php echo mlx_get_lang(ucfirst($prop_row->title)); ?></a></li>
								<?php } ?>

							</ul>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php 
				
				/*$social_media = get_option('social_media');*/
				$social_media = apply_filters("cms_get_details", '','social_media');
				
				if (isset($social_media) && !empty($social_media)) {
					$social_media_array = json_decode($social_media, true);
					/*print_r($social_media_array);
					if(count($social_media_array) > 0){*/
			?>
				<div class="col-lg-3 mb-5 mb-lg-0">
					<h3 class="footer-heading mb-4"><?php echo mlx_get_lang('Follow Us'); ?></h3>

					<div class="footer-social-icon">
						<?php
						foreach ($social_media_array as $k => $v) {
							if (!isset($v['enable']) || (isset($v['enable']) && $v['enable'] != 1))
								continue;
						?>
							<a class="" href="<?php echo $v['url']; ?>" target="_blank"><i class="fa <?php echo $v['icon']; ?>"></i></a>
						<?php } ?>

					</div>

				</div>
			<?php } 
				/*}*/
				?>
		</div>
		<?php if (!empty($copyright_text)) { ?>
			<div class="row text-center">
				<div class="col-md-12">
					<p>
						<?php echo $copyright_text; ?>
					</p>
				</div>

			</div>
		<?php } ?>

	</div>
</footer>

</div>


<?php if (isset($enable_compare_property) && $enable_compare_property == 'Y') { ?>
	<div class="container ">

		<div class="row comparePanle">
			<span class="compare-block-head pull-right">
				<a href="#" class="btn btn-success cmprBtn text-white" target="_blank" disabled><?php echo mlx_get_lang('Compare'); ?></a>
				<i class="fa fa-caret-down show-hide-compare-block close_compare_block"></i>
			</span>
			<div class="header-block" style="display:none;">
				<div class="col-md-12">
					<h5 class="text-left"><?php echo mlx_get_lang('Added for Comparison'); ?>
					</h5>
				</div>
			</div>
			<div class=" titleMargin comparePan text-center" style="display:none;">
				<?php
				if (isset($_SESSION['comparable_properties']) &&  !empty($_SESSION['comparable_properties'])) {
					foreach ($_SESSION['comparable_properties'] as $pk => $pv) {
				?>
						<div id="<?php echo $pk; ?>" data-url="<?php echo $pv['url']; ?>" class="relPos w3-col l3 m3 s3">
							<div class="bg-white w3-ripple titleMargin">
								<a class="selectedItemCloseBtn w3-closebtn cursor">×</a>
								<img src="<?php echo $pv['img']; ?>" alt="<?php echo $pv['title']; ?>">
								<p id="<?php echo $pk; ?>" class="titleMargin1"><?php echo $pv['title']; ?></p>
							</div>
						</div>
				<?php
					}
				}
				?>
			</div>
		</div>
	</div>
<?php }


/*do_action('cms_footer_scripts');*/
?>


<a href="#" class="btn btn-white btn-outline-white d-print-none" id="back-to-top" title="Back to top"><i class="fa fa-chevron-up"></i></a>

</body>

</html>