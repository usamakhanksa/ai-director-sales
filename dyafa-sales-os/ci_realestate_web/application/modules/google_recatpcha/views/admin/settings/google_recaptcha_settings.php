<div class="form-group">
	<label for="recaptcha_site_key"><?php echo mlx_get_lang('Google reCAPTCHA Site Key'); ?></label>
	<input type="text" class="form-control" name="options[recaptcha_site_key]" id="recaptcha_site_key" value="<?php if (isset($recaptcha_site_key)) echo $recaptcha_site_key; ?>">
</div>

<div class="form-group">
	<label for="recaptcha_secret_key"><?php echo mlx_get_lang('Google reCAPTCHA Secret Key'); ?></label>
	<input type="text" class="form-control" name="options[recaptcha_secret_key]" id="recaptcha_secret_key" value="<?php if (isset($recaptcha_secret_key)) echo $recaptcha_secret_key; ?>">
</div>