<div class="bg-white widget border rounded agent_details" id="agent_detail">


	<?php
	$user_id = $single_property->created_by;
	$first_name = 	get_user_meta($user_id, 'first_name');
	$last_name = 	get_user_meta($user_id, 'last_name');
	$mobile_no = 	get_user_meta($user_id, 'mobile_no');
	$address = 		get_user_meta($user_id, 'address');
	$photo_url = 	get_user_meta($user_id, 'photo_url');

	$full_name = strtolower($first_name) . ' ' . strtolower($last_name);
	$agent_url = site_url(array('user', 'agent', $this->default_language, str_replace(' ', '-', strtolower($full_name)) . '~' . $this->global_lib->EncryptClientID($user_id)));

	?> 
	<form action="" class="">
		<div class="form-group">
			<div class="row">
				<div class="col-md-4 text-center">
					<?php
					if (isset($photo_url) && !empty($photo_url)) {
						if (file_exists('uploads/user/' . $photo_url)) {
							echo '<img src="' . base_url() . 'uploads/user/' . $photo_url . '" class="agent_img">';
						} else {
							echo '<img src="' .
								$myHelpers->property_agent_image . '" class="agent_img">';
						}
					} else {
						echo '<img src="' .
							$myHelpers->property_agent_image . '" class="agent_img">';
					}
					?>
					<hr>
				</div>
				<div class="col-md-8 text-left">
					<h5><a><?php echo ucfirst($first_name) . ' ' . ucfirst($last_name); ?></a></h5>
					<!--href="<?php //echo $agent_url; 
								?>"-->
					<p><?php echo mlx_get_lang(ucfirst($single_property->user_type)); ?></p>
				</div>
			</div>
		</div>
		<?php if (!empty($mobile_no)) { ?>
			<div class="form-group text-left">
				<i class="fa fa-phone"></i> <a href="tel:<?php echo $mobile_no; ?>"><?php echo $mobile_no; ?></a>
			</div>
		<?php } ?>

		<?php if (!empty($single_property->user_email)) { ?>
			<div class="form-group text-left">
				<i class="fa fa-envelope"></i> <a href="mailto:<?php echo $single_property->user_email; ?>"><?php echo $single_property->user_email; ?></a>
			</div>
		<?php } ?>

		<?php if (!empty($address)) { ?>
			<div class=" text-left">
				<i class="fa fa-map-marker"></i> <?php echo $address; ?>
			</div>
		<?php } ?>

	</form>
</div>
<?php
