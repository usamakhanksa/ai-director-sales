<?php
extract($payment_method);
// echo "<pre>";
// var_dump($meta_content['site_payment_methods'][$payment_method['method_check_name']]['label_txt']);
?>
<div class="box-body">

	<div class="form-group">
		<label for="<?php echo $payment_method['method_check_name']; ?>"><?php echo mlx_get_lang(ucfirst($payment_method['method_text'])); ?></label>

		<input type="text" class="form-control" name="site_payment_methods[<?php echo $payment_method['method_check_name'] ?>][label_txt]" id="<?php echo $payment_method['method_check_name']; ?>" value="<?php
																																																			if (isset($meta_content['site_payment_methods'][$payment_method['method_check_name']]['label_txt']) && !empty($meta_content['site_payment_methods'][$payment_method['method_check_name']]['label_txt'])) {
																																																				echo $meta_content['site_payment_methods'][$payment_method['method_check_name']]['label_txt'];
																																																			} else {

																																																				echo $payment_method['method_text'];
																																																			}
																																																			?>">


	</div>
</div>