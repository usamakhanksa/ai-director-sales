<div class="content-wrapper">
	<section class="content-header">
		<h1 class="page-title"><i class="fa fa-money"></i> <?php echo mlx_get_lang('Payment Methods'); ?></h1>
		<?php if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
			echo $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		?>
	</section>

	<section class="content">
		<?php
		$attributes = array('name' => 'add_form_post', 'class' => 'homepage_section_form');
		echo form_open_multipart('admin/packages/payment_methods', $attributes); ?>
		<div class="row">
			<div class="col-md-12">

				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?> homepage_section_container">
					<div class="box-body">
						<?php
						$CI = &get_instance();
						$method_settings = $CI->config->item('site_payment_methods');

						if (isset($method_settings) && !empty($method_settings)) {

							foreach ($method_settings as $ms) {

								$is_enable = $is_required = false;

								if (
									isset($meta_site_payment_methods[$ms['method_check_name']]['is_enable']) &&
									$meta_site_payment_methods[$ms['method_check_name']]['is_enable'] == 'Y'
								) {
									$is_enable = $is_required = true;
								}
						?>
								<ul class="todo-list ui-sortable">

									<li>
										<div class="header-block">
											<span class="handle ui-sortable-handle">
												<i class="fa fa-ellipsis-v"></i>
												<i class="fa fa-ellipsis-v"></i>
											</span>
											<span class="text"><?php echo mlx_get_lang(ucfirst($ms['method_text'])); ?></span>
											<?php //print_r($meta_site_payment_methods['site_payment_methods'][$ms['method_check_name']]['is_enable']);
											?>
											<div class="radio_toggle_wrapper pull-right">
												<input type="radio" <?php if ((isset($meta_site_payment_methods['site_payment_methods'][$ms['method_check_name']]['is_enable']) && $meta_site_payment_methods['site_payment_methods'][$ms['method_check_name']]['is_enable'] == 'Y') || !isset($meta_site_payment_methods['site_payment_methods'][$ms['method_check_name']]['is_enable'])) { ?> checked="checked" <?php } ?> id="<?php echo $ms['method_check_name']; ?>_enable" value="Y" name="site_payment_methods[<?php echo $ms['method_check_name'] ?>][is_enable]" class="toggle-radio-button">
												<label for="<?php echo $ms['method_check_name']; ?>_enable"><?php echo mlx_get_lang('Enable'); ?></label>

												<input type="radio" id="<?php echo $ms['method_check_name']; ?>_disable" value="N" <?php if (isset($meta_site_payment_methods['site_payment_methods'][$ms['method_check_name']]['is_enable']) && $meta_site_payment_methods['site_payment_methods'][$ms['method_check_name']]['is_enable'] == 'N') { ?> checked="checked" <?php } ?> name="site_payment_methods[<?php echo $ms['method_check_name'] ?>][is_enable]" class="toggle-radio-button">
												<label for="<?php echo $ms['method_check_name']; ?>_disable"><?php echo mlx_get_lang('Disable'); ?></label>
											</div>

											<?php if (!empty($ms['method_fields'])) { ?>
												<div class="tools">
													<button class="btn btn-box-tool collapsed"><i class="fa fa-chevron-down"></i></button>
												</div>
											<?php } ?>
										</div>
										<?php if (!empty($ms['method_fields'])) { ?>
											<div class="section_fields hide">
												<?php
												$data['is_required'] = $is_required;
												$data['payment_method'] = $ms;
												$data['meta_content'] = $meta_site_payment_methods;
												if (isset($ms['method_check_name']) && $ms['method_check_name'] == 'cash_card_window') {
													$this->load->view("$theme/" . $ms['method_view'], $data);
												} else {
													$this->load->view($ms['method_view'], $data);
												}
												?>
											</div>
											<?php /*}*/ ?>
									</li>
								<?php }
								?>
								</ul>
						<?php }
						} ?>
					</div>
					<div class="box-footer">
						<button type="submit" name="submit" class="btn btn-<?php echo $myHelpers->global_lib->get_skin_class(); ?> pull-right submit-section-btn"><?php echo mlx_get_lang('Save'); ?></button>
					</div>
				</div>
			</div>

		</div>
		</form>
	</section>
</div>