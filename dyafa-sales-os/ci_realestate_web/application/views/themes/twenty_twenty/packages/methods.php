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
						<input type="hidden" name="package_price" id="package_price" value="<?php echo html_escape($price); ?>" />	
						
			<input type="hidden" name="customer_id" id="customer_id" 	value="<?php if (isset($customer_id) ) {	echo $customer_id;	} ?>" />

			<input type="hidden" name="customer_email" id="customer_email" 	value="<?php if (isset($customer_email)) {	echo $customer_email;	} ?>" />
			
			<input type="hidden" name="post_url" id="post_url" 	value="<?php if (isset($post_url) ) {	echo $post_url;		} ?>" />	
						
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
								
								$payment_config = $myHelpers->config->item($key . "_pmt_config");

						?>
								<div class="box-header with-border">

									<input type="radio" name="payment_method" required 
												id="<?= 'radio_' . $key; ?>" 
												value="<?= $key; ?>" 
												<?php if (is_array($payment_config) && array_key_exists("form_action", $payment_config)) { ?> 
													data-form_action="<?php echo base_url($payment_config['form_action']); ?>" 
												<?php } ?>
												data-inner_elem="<?= $key; ?>"
												class="minimal payment_method <?= 'payment_method_' . $key; ?>">
											
										<label 	for="<?= 'radio_' . $key; ?>" 
												id="<?php echo 'btn_' . $key; ?>" 		>
												<?php
													if(isset($value['label_txt']))
														echo $value['label_txt'];
													else
														echo ucfirst(str_replace('_', ' ', $key));		?>
										</label>
											
					<?php 	if (is_array($payment_config) && array_key_exists("front_label_inner_text", $payment_config)) {	 ?>
						<div id="<?php echo $key . "_inner";	?>" 
								class="panel-collapse collapse payment_method_inner" aria-expanded="false" 
								style="heights: 0px;">
								<?php			$this->load->view( $payment_config['front_label_inner_text']); ?>
						</div>
					<?php } ?>
				
				
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
    var SITEURL = "<?php echo base_url(); ?>";

    $(document).ready(function() {

        $(".payment_method").on('click', function() {
            var thiss = $(this);

            if (thiss.attr('data-form_action') != undefined) {
                var form_action = thiss.attr('data-form_action');
                $("#checkout_form").attr("action", form_action);
            } else {
                $("#checkout_form").removeAttr("action");
            }
			
			var inner_elem = thiss.attr('data-inner_elem');
			
			$('.payment_method_inner').removeClass("expand").addClass("collapse");
			$('#'+inner_elem+'_inner').removeClass("collapse").addClass("expand");
			
        });
    });
</script>

<?php	do_action('cms_checkout_footer_scripts',$args);	?>


