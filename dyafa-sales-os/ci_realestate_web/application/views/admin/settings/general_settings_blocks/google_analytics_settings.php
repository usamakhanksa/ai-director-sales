<div class="form-group">
	<label for="google_analytics_tracking_id"><?php echo mlx_get_lang('Google Analytics Tracking ID'); ?></label>

	<input type="text" class="form-control" name="options[google_analytics_tracking_id]" id="google_analytics_tracking_id" value="<?php if (isset($google_analytics_tracking_id)) echo $google_analytics_tracking_id; ?>">
	<p class="help-block">i.e. UA-XXXXXXXX-Y</p>
</div>