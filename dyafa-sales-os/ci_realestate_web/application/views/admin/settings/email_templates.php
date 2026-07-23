<?php
$site_language = 			get_option('site_language');
$enable_multi_language = 	get_option('enable_multi_language');
$default_language = 		get_option('default_language');
?>

<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-list"></i> <?php echo mlx_get_lang('Email Templates'); ?> </h1>


		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>
	</section>
	<style>
		.homepage_section_form .row [class*=col-] {
			width: 100%;
		}

		.input-group {
			display: inline-block;
		}
	</style>
	<section class="content">
		<div class="row">
			<div class="col-md-8">
				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> homepage_section_container">
					<?php
					$attributes = array('name' => 'add_form_post', 'class' => 'homepage_section_form');
					echo form_open_multipart('', $attributes);


					?>
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Email Templates'); ?></h3>
					</div>
					<div class="box-body content-box">

				<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">

				<?php


				if (isset($email_templates_sections) && !empty($email_templates_sections)) {
					
					/*echo "<pre>"; print_r($email_templates_sections); echo "</pre>";*/
					if (isset($meta_content_lists) && !empty($meta_content_lists)) {
						$old_email_templates_sections = $email_templates_sections;
						$email_templates_sections = array();
						foreach ($meta_content_lists as $k => $v) {
							if (isset($old_email_templates_sections[$k])) {
								$email_templates_sections[$k] = $old_email_templates_sections[$k];
								unset($old_email_templates_sections[$k]);
							} else {
								$email_templates_sections[$k] = array('section_type' => 'dynamic', 'title' => 'Properties Section');
							}
						}
						if (!empty($old_email_templates_sections)) {
							foreach ($old_email_templates_sections as $k => $v) {
								$email_templates_sections[$k] = $old_email_templates_sections[$k];
								unset($old_email_templates_sections[$k]);
							}
						}
					}

					
					$dynamic_section = '';
				?>
							<ul class="todo-list">
								<?php

								$manage_contents = array();

								$ds_count = 0;

								$ds_heading = '';



								foreach ($email_templates_sections  as $content_section_key => $content_section_value) {

									$section_fields = $myHelpers->config->item($content_section_key . "_fields");



									$sec_key = str_replace('_section', '', $content_section_key);

									$has_val_saved = false;
									if (isset($meta_content_lists) && isset($meta_content_lists[$content_section_key])) {
										$has_val_saved = true;
										//print_r($meta_content_lists[$content_section_key]);
										foreach ($meta_content_lists[$content_section_key] as $csk => $csv) {
											${$csk} = $csv;
										}
									}

								?>


									<li class="
					<?php
									if (isset($content_section_value['section_type']) && $content_section_value['section_type'] == 'dynamic' && $content_section_key != 'properties_section') {
										$ds_count++;
										echo 'dynamic_section de_' . $ds_count;
									}
					?> ">

										<div class="header-block">

											<span class="text"><?php echo (!empty($ds_heading) ? $ds_heading . ' - ' : ''); ?><?php echo mlx_get_lang(ucfirst($content_section_value['title'])); ?></span>


											<?php if (!empty($section_fields)) { ?>
												<div class="tools">
													<button class="btn btn-box-tool collapsed"><i class="fa fa-chevron-down"></i></button>
													<?php if (isset($content_section_value['section_type']) && $content_section_value['section_type'] == 'dynamic' && $content_section_key != 'properties_section') {
													?>
														<button class="btn btn-danger btn-sm remove_ds_btn" title="<?php echo mlx_get_lang('Remove Section'); ?>" data-toggle="tooltip"><i class="fa fa-trash fa-2x"></i></button>
													<?php } ?>
												</div>
											<?php } ?>
										</div>

										<?php if (!empty($section_fields)) { ?>
											<div class="section_fields hide">
												<?php
												global $single_field, $content_type;
												$content_type = $content_section_key;

												foreach ($section_fields as $k => $single_field) {


													if (isset($single_field['name']) && isset(${$single_field['name']}) && $has_val_saved) {
														global $meta_content;
														$meta_content[$single_field['name']] = ${$single_field['name']};
													} else {
														global $meta_content;
														$meta_content = array();
													}



													if (isset($single_field['name']) && $single_field['name'] == 'email_lang') {
														continue;
														$property_lang_option = array();

														if (isset($site_language) && !empty($site_language)) {
															$site_language_array = json_decode($site_language, true);
															if (!empty($site_language_array)) {
																$property_lang_option['all'] = 'All Languages';
																foreach ($site_language_array as $k => $v) {
																	if ($v['status'] != 'enable')
																		continue;
																	$lang_exp = explode('~', $v['language']);
																	$lang_code = $lang_exp[1];
																	$lang_title = $lang_exp[0];

																	$property_lang_option[$lang_code] = $lang_title;
																}
															}
														}
														$single_field['default'] = 'all';
														$single_field['options'] = $property_lang_option;
													} else {

														if (isset($single_field['template']) && !empty($single_field['template'])) {

															$template = $single_field['template'];
															
															
															
															
															$template_content = "" ;
															if(file_exists(APPPATH . "views/" . $theme . '/' . $template . ".tpl"))
																$template_content = file_get_contents(APPPATH . "views/" . $theme . '/' . $template . ".tpl");
															else if(file_exists( $template . ".tpl")){
																$template_content = file_get_contents( $template . ".tpl");
															}
															
															
															/*$single_field['default'] = "<pre>".htmlentities($template_content)."</pre>";*/

															$single_field['default'] = nl2br($template_content);
														}
													}

													$this->load->view("$theme/templates/templ-" . $single_field['type']);
												}
												?>
											</div>
										<?php } ?>
									</li>
								<?php } ?>
							</ul>
						<?php } ?>

					</div>
					<div class="box-footer">
						<a href="#" id="test_email_notifications_action" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-rights" data-toggle='modal' data-target='#test_email_notifications_Modal'> Test Email Notifications </a>
						<button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right" id="save_publish"><?php echo mlx_get_lang('Submit'); ?></button>
					</div>
					</form>
				</div>
			</div>
			<div class="col-md-4">
				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> homepage_section_container">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo mlx_get_lang('Shortcodes'); ?></h3>
					</div>
					<div class="box-body content-box">
						<?php
						if (isset($email_template_shortcodes) && !empty($email_template_shortcodes)) {
							$n = 0;
							foreach ($email_template_shortcodes as $shortcode_list) {
								foreach ($shortcode_list as $k => $v) {
									$n++;
						?>
									<div class="form-group">
										<label for="shortcode_<?php echo $n; ?>"><?php echo $v; ?></label>
										<input type="text" id="shortcode_<?php echo $n; ?>" readonly class="form-control" onClick="this.select();" value="<?php echo $k; ?>">
									</div>
						<?php
								}
							}
						} else {
							echo '<h4 class="text-center">' . mlx_get_lang("No Shortchode Available.") . '</h4>';
						}
						?>
					</div>
				</div>
			</div>

		</div>

	</section>
</div>


<div id="test_email_notifications_Modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content box">
			<form method="POST" class="test_email_notifications_form">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo mlx_get_lang('Test Email Notifications'); ?> <button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button></h4>
				</div>
				<div class="modal-body">

					<div class="form-group">
						<label for="to_email"><?php echo mlx_get_lang('To Email'); ?></label>
						<input type="email" required class="form-control" name="to_email" id="to_email">
					</div>

					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" class="btn btn-default dropdown-toggle">
								Email Notification Templates (<span class="sele_notif_count">0</span>)
								<span class="fa fa-caret-down"></span></button>
							<ul class="dropdown-menu email_notif_list">
								<li><a>Select At-least One</a></li>
								<li class="divider"></li>

								<?php
								foreach ($email_templates_sections  as $content_section_key => $content_section_value) {
								?>
									<li><a class="checkbox">
											<div>
												<label>
													<input type="checkbox" class="email_templates_available" value="<?php echo $content_section_key; ?>" id="<?php echo $content_section_key; ?>" name="<?php echo $content_section_key; ?>">
													<?php echo mlx_get_lang(ucfirst($content_section_value['title'])); ?>
												</label>
											</div>
										</a></li>

								<?php  } ?>
							</ul>
						</div>

					</div>



				</div>
				<div class="modal-footer">
					<button type="submit" id="test_email_notifications" data-href="admin/ajax_mailer/test_email_notifications" class="test_email_notifications btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?>"><?php echo mlx_get_lang('Send'); ?></button>
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo mlx_get_lang('Close'); ?></button>
				</div>
			</form>

			<div class="overlay">
				<i class="fa fa-refresh fa-spin"></i>
			</div>

		</div>
	</div>
</div>


<script>
	function update_selected_template() {
		var selc_templ = $('.email_notif_list .email_templates_available:checked').length;
		$('.sele_notif_count').html(selc_templ);
	}

	$(document).ready(function() {

		$('.dropdown-toggle').on('click', function(event) {
			$(this).parent().toggleClass('open');
		});

		$('body').on('click', function(e) {
			if (!$('.input-group-btn').is(e.target) &&
				$('.input-group-btn').has(e.target).length === 0 &&
				$('.open').has(e.target).length === 0
			) {
				$('.input-group-btn').removeClass('open');
			}
		});

		$('.modal .overlay').hide();

		$('.test_email_notifications_form').on('submit', function() {

			var href = $('#test_email_notifications').attr("data-href");
			var to_email = $('#to_email').val();
			var email_templates = '';

			if ($(".email_templates_available:checked").length == 0) {
				$('#test_email_notifications_Modal .modal-body').prepend('<div class="alert alert-danger alert-dismissable" style="margin-top:0px; margin-bottom:10px;">' +
					'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
					'Please select atleast one email templates' +
					'</div>');
				$('.alert:not(".show_always")').delay(5000).fadeOut('slow');
			} else {
				$('#test_email_notifications_Modal .overlay').show();


				$(".email_templates_available:checked").each(function() {
					var email_template = $(this).val();
					$.ajax({
						url: base_url + href,
						type: 'POST',
						success: function(res) {
							$('#test_email_notifications_Modal .modal-body').prepend('<div class="alert alert-success alert-dismissable" style="margin-top:0px; margin-bottom:10px;">' +
								'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
								res + '</div>');
							$('.alert:not(".show_always")').delay(5000).fadeOut('slow');
							$('#test_email_notifications_Modal .overlay').hide();
						},
						data: {
							to_email: to_email,
							email_template: email_template
						},
						cache: false
					});

				});
			}
			return false;
		});

		$('.email_notif_list .email_templates_available').on('change', function() {
			update_selected_template();
			return false;
		});

	});
</script>