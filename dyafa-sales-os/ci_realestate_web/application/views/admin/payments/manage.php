<?php $this->load->view("default/header-top"); ?>
<?php $this->load->view("default/sidebar-left"); ?>

<div class="content-wrapper">
	<section class="content-header">
		<h1><?php echo mlx_get_lang('Payments'); ?></h1>
		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>
	</section>

	<section class="content">
		<?php
		$attributes = array('name' => 'add_form_post', 'class' => 'homepage_section_form');
		echo form_open_multipart('payments/manage', $attributes); ?>
		<div class="row">
			<div class="col-md-12">

				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> homepage_section_container">
					<div class="box-body">
						<?php

						if (isset($payment_methods) && !empty($payment_methods)) {

							if (isset($methods) && !empty($methods)) {
								$old_payment_methods = $payment_methods[0];
								$payment_methods = array();
								foreach ($methods as $k => $v) {
									if (isset($old_payment_methods[$k])) {
										$payment_methods[$k] = $old_payment_methods[$k];
										unset($old_payment_methods[$k]);
									}
								}

								if (!empty($old_payment_methods)) {
									foreach ($old_payment_methods as $k => $v) {
										$payment_methods[$k] = $old_payment_methods[$k];
										unset($old_payment_methods[$k]);
									}
								}
							}
						?>
							<ul class="todo-list ui-sortable">
								<?php

								$manage_contents = array();

								foreach ($payment_methods as $payment_methods_key => $payment_methods_value) {

									$section_fields = $myHelpers->config->item($payment_methods_key . "_fields");

									$sec_key = str_replace('_section', '', $payment_methods_key);

									$has_val_saved = false;
									if (isset($meta_content_lists) && isset($meta_content_lists[$payment_methods_key])) {
										$has_val_saved = true;
										foreach ($meta_content_lists[$payment_methods_key] as $csk => $csv) {
											${$csk} = $csv;
										}
									}

								?>
									<li>
										<div class="header-block">
											<span class="handle ui-sortable-handle">
												<i class="fa fa-ellipsis-v"></i>
												<i class="fa fa-ellipsis-v"></i>
											</span>
											<span class="text"><?php echo mlx_get_lang(ucfirst($payment_methods_value['method_text'])); ?></span>

											<div class="radio_toggle_wrapper pull-right">
												<input type="radio" <?php if ((isset($is_enable) && $is_enable == 'Y') || !isset($is_enable)) { ?> checked="checked" <?php } ?> id="<?php echo $payment_methods_key; ?>_enable" value="Y" name="<?php echo $payment_methods_key; ?>[is_enable]" class="toggle-radio-button">
												<label for="<?php echo $payment_methods_key; ?>_enable">Enable</label>

												<input type="radio" id="<?php echo $payment_methods_key; ?>_disable" value="N" <?php if (isset($is_enable) && $is_enable == 'N') { ?> checked="checked" <?php } ?> name="<?php echo $payment_methods_key; ?>[is_enable]" class="toggle-radio-button">
												<label for="<?php echo $payment_methods_key; ?>_disable">Disable</label>
											</div>

											<?php if (!empty($section_fields)) { ?>
												<div class="tools">
													<button class="btn btn-box-tool collapsed"><i class="fa fa-chevron-down"></i></button>
												</div>
											<?php } ?>
										</div>
										<?php if (!empty($section_fields)) {

										?>
											<div class="section_fields hide">
												<?php
												global $single_field, $content_type;
												$content_type = $payment_methods_key;

												foreach ($section_fields as $k => $single_field) {

													if (isset($single_field['name']) && isset(${$single_field['name']}) && $has_val_saved) {
														global $meta_content;
														$meta_content[$single_field['name']] = ${$single_field['name']};
													} else {
														global $meta_content;
														$meta_content = array();
													}
													print_r($single_field['type']);
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
						<button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right submit-section-btn">Save</button>
					</div>
				</div>
			</div>

		</div>
		</form>
	</section>
</div>