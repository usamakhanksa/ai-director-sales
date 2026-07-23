<div class="container">
	<div class="row">
		<div class="col md-12">
			<div class="content-header">
				<h2 class="page-title text-center">
					<!-- <i class="fa fa-list"></i> -->
					<?php echo mlx_get_lang('Payment Methods'); ?>
				</h2>
			</div>

			<div class="bg-white widget border rounded  text-left">
				<div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
					<?php
					$attributes = array('name' => 'checkout', 'class' => 'form', 'id' => 'payment_form');
					echo form_open_multipart('packages/confirmation/', $attributes);
					?>

					<div class="box-body content-box">

						<input type="hidden" name="user_id" id="user_id" value="<?php echo EncryptClientID($this->session->userdata('user_id')); ?>">
						<input type="hidden" name="package_currency" id="package_currency" value="<?php echo $package_currency; ?>">
						<input type="hidden" name="package_id" id="package_id" value="<?php echo $package_id; ?>">

						<?php

						if (isset($methods) && !empty($methods)) {
							$c = 0;

							$payment_methods_dec = json_decode($methods, true);
							foreach ($payment_methods_dec['site_payment_methods'] as  $key => $value) {
								$payment_method = $key;
								if (
									isset($payment_method_currency_supports)
									&& array_key_exists($payment_method, $payment_method_currency_supports)
									&& !in_array($package_currency, $payment_method_currency_supports[$key])
								) continue;

						?>
								<div class="box-header with-border">

									<input type="radio" name="payment_method" required id="<?= 'radio_' . $key; ?>" value="<?= $key; ?>" class="minimal ">
									<label for="<?= 'radio_' . $key; ?>" id="<?php echo 'btn_' . $key; ?>" href="#<?= $c; ?>">
										<?php
										if ($key == 'cash_card_window') {
											echo ucfirst(str_replace('_', ' ', $key));
										} else {
											echo ucfirst($key);
										}
										?>
									</label>
								</div>
								<div id="<?php echo $c;
											?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="box-body">
										<?php
										//echo $method[2];

										if ($key === 'stripe') {
											echo $value->method_stripe;
										} elseif ($key === 'bank') { ?>
											<h3><?php echo $value->method_bank_transfer; ?></h3>
											<h4><?php echo $value->bank_transfer_guide; ?></h4>
										<?php } elseif ($key === 'paypal') {
										?>
											Pay With Paypal
										<?php } elseif ($key == 'cod') { ?>
											<h3><?php echo $value->method_cod; ?></h3>
											<h4><?php echo $value->cod_payment_guide; ?></h4>
										<?php } ?>
									</div>
								</div>
						<?php }
						}
						?>

					</div>
					<div class="box-footer">
						<button type="submit" name="submit" class="btn btn-success text-white  rounded-1 submit-form-btn" 
								id="payBtn"><?php echo mlx_get_lang('CheckOut'); ?></button>
					</div>
					</form>
				</div>
				<!-- <div class="content-wrapper">
			</div> -->
			</div>
		</div>

	</div>
</div>
<script>
	$(document).ready(function() {
		$('input:radio[name="payment_method"]').click(function(e) {
			var ele = $(this).attr('id');
			var action = ele.replace("radio_", "");
			var url;
			if (action == 'cash_card_window') {
				url = base_url + 'packages/confirmation/';
			} else if (action == 'paypal') {
				url = base_url + action + '/payment';
				$("#payment_form").attr('action', url);
			} else if (action == 'iyzico') {
				url = base_url + action;
				$("#payment_form").attr('action', url);
			} else if (action == 'razorpay') {
				var callback = 'get_price_by_package_id';
				var SITEURL = "<?php echo base_url(); ?>";
				var id = $("#package_id").val();
				$.ajax({
					url: SITEURL + 'admin_ajax',
					type: 'POST',
					data: {
						package_id: id,
						callback: callback
					},
					success: function(res) {
						if (res != undefined || res != null) {
							if (res.price) {
								$(".content-box").append(`<input type='hidden' id='package_price' value='${res.price}'/>`);
							}
						}
					},
					error: function(err) {
						console.log(err);
					}
				});
				$("#payment_form").removeAttr('action');
			}
		});
	})
</script>

<?php	do_action('cms_checkout_footer_scripts',$args);	?>


