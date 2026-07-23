<?php if (isset($banner_row) && isset($banner_row->b_image) && !empty($banner_row->b_image) && file_exists('uploads/banner/' . $banner_row->b_image)) { ?>
	<section class="page-top-section set-bg" data-setbg="<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>" style="background-image: url(<?php echo base_url(); ?>uploads/banner/<?php echo $banner_row->b_image; ?>);">
		<div class="container text-white">
			<h1><?php echo mlx_get_lang('Register'); ?></h1>
		</div>
	</section>
<?php } ?>

<?php
$enbale_reg_img_upload = get_option('enbale_reg_img_upload');
$register_message = 	get_option('register_message');
$disclaimer_message = get_option('disclaimer_message');

//$enbale_gmail_login = 	get_option('enbale_gmail_login');
//$enbale_facebook_login = get_option('enbale_facebook_login');
?>

<div class="site-section">
	<div class="container">

		<div class="row">

			<?php if (form_error('contact_name')) {
				echo form_error('contact_name');
			} ?>
			<?php if (form_error('contact_email')) {
				echo form_error('contact_email');
			} ?>
			<?php if (form_error('contact_subject')) {
				echo form_error('contact_subject');
			} ?>
			<?php if (form_error('contact_message')) {
				echo form_error('contact_message');
			} ?>



			<div class="col-md-6 col-lg-6 mb-5 offset-lg-3 offset-md-3">


				<?php
				$args = array(
					'class' => 'register_form  text-left', 'id' => 'register_form', 'autocomplete' => "off",
					'enctype' => 'application/x-www-form-urlencoded'
				);
				echo form_open('', $args); ?>

				<?php if (isset($_SESSION['register_msg']) && !empty($_SESSION['register_msg'])) {
					echo $_SESSION['register_msg'];
					unset($_SESSION['register_msg']);
				}

				?>

				<h4 class="text-center mb-4"><?php echo mlx_get_lang('Create an Account'); ?></h4>

				<?php   do_action("cms_register_form_extra_fields_before");?>
				<div class="row form-group">
					<div class="col-md-6 mb-3 mb-md-0">
						<label class="font-weight-bold" for="first_name"><?php echo mlx_get_lang('First Name'); ?> <span class="required text-danger">*</span></label>
						<input type="text" id="first_name" name="first_name" required class="form-control">
					</div>

					<div class="col-md-6 mb-3 mb-md-0">
						<label class="font-weight-bold" for="last_name"><?php echo mlx_get_lang('Last Name'); ?> <span class="required text-danger">*</span></label>
						<input type="text" id="last_name" name="last_name" required class="form-control">
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-12 mb-3 mb-md-0 validation-field">
						<label class="font-weight-bold" for="username"><?php echo mlx_get_lang('Username'); ?> <span class="required text-danger">*</span></label>
						<input type="text" id="username" name="username" autocomplete="off" required class="form-control ">
						<i class="fa fa-spinner fa-spin"></i>
						<p class="help-block" style="margin:0px; font-style:italic;">Use lower case without space and special characters are allowed.</p>
					</div>
				</div>

				<?php
				if (isset($reg_user_type) && !empty($reg_user_type)) {
					if (count($reg_user_type) > 1) {
				?>
						<div class="row form-group">
							<div class="col-md-12 mb-3 mb-md-0">
								<label class="font-weight-bold" for="first_name"><?php echo mlx_get_lang('Register as'); ?> <span class="required text-danger">*</span></label>
								<select name="user_type" required class="form-control select2_elem">
									<option value="">Select User Type</option>
									<?php
									foreach ($reg_user_type as $k => $v) {
										echo '<option value="' . $k . '">' . ucfirst($v) . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<?php
					} else {
						foreach ($reg_user_type as $k => $v) {
						?>
							<input type="hidden" name="user_type" value="<?php echo $k; ?>">
				<?php
						}
					}
				} ?>

				<div class="row form-group">
					<div class="col-md-12 mb-3 mb-md-0 validation-field">
						<label class="font-weight-bold" for="email"><?php echo mlx_get_lang('Email'); ?> <span class="required text-danger">*</span></label>
						<input type="email" id="email" name="email" autocomplete="off" required class="form-control ">
						<i class="fa fa-spinner fa-spin"></i>
					</div>
				</div>
				
				<?php   do_action("cms_register_form_extra_fields");?>

				<div class="row form-group">
					<div class="col-md-6">
						<label class="font-weight-bold" for="password"><?php echo mlx_get_lang('Password'); ?> <span class="required text-danger">*</span></label>
						<input type="password" id="password" name="password" required class="form-control" autocomplete="new-password">
					</div>

					<div class="col-md-6">
						<label class="font-weight-bold" for="repeat_password"><?php echo mlx_get_lang('Repeat Password'); ?> <span class="required text-danger">*</span></label>
						<input type="password" id="repeat_password" name="repeat_password" required class="form-control">
					</div>
				</div>

				<?php if ($enbale_reg_img_upload == 'Y') { ?>
					<div class="form-group row">
						<div class="col-md-12">
							<label class="font-weight-bold" for="exampleInputFile" style="display: block;"><?php echo mlx_get_lang('Profile Image'); ?> <span class="required text-danger">*</span></label>
							<label class="custom-file-upload rounded-2">
								<input type="file" accept="image/*" id="att_photo" name="attachments" data-type="photo" data-user-type="user" />
								<i class="fa fa-cloud-upload"></i> <?php echo mlx_get_lang('Upload Image'); ?>
							</label>
							<progress id="att_photo_progress" value="0" max="100" style="display:none;"></progress>
							<a id="att_photo_link" href="" download="" style="display:none;">
								<img src="">
							</a>
							<a class="remove_img" id="att_photo_remove_img" data-name="att_photo" title="Remove Image" href="#" style="display:none;"><i class="fa fa-remove"></i></a>
							<input type="text" name="att_photo_hidden" class="photo_url_field" id="att_photo_hidden" style="visibility:hidden">
						</div>
					</div>
				<?php } ?>
				
				
				<?php   do_action("cms_register_form_extra_fields_after");?>

				<?php if ($disclaimer_message != '' && $register_message != '') { ?>
					<div class="form-group row">
						<div class="col-md-12">
							<label id="disclaimer_checkbox-error" class="error" for="disclaimer_checkbox" style="display:none;">This field is required.</label>
						</div>
						<div class="col-md-12">
							<label style="margin:0px;">
								<input type="checkbox" required class="" name="disclaimer_checkbox" value="Y" id="disclaimer_checkbox"> &nbsp; <?php echo $register_message; ?></label>
						</div>
						<div class="col-md-12">
							<br>
							<?php echo $disclaimer_message; ?>
						</div>
					</div>
				<?php } ?>

				<div class="row form-group mt-4">
					<div class="col-md-12">
						<button type="submit" name="register" class="btn submit-contact-form-btn py-2 px-4 rounded-2 text-white btn-block"><?php echo mlx_get_lang('Register'); ?></button>
					</div>
				</div>
				</form>

				<?php if ((isset($enbale_gmail_login) && $enbale_gmail_login == 'Y') ||
					(isset($enbale_facebook_login) && $enbale_facebook_login == 'Y') && 0
				) { ?>
					<h3 class="h5 text-black mb-3 text-center mt-5 mb-4 social-login-heading"><?php echo mlx_get_lang("Social Login"); ?></h3>
					<div class="social-login-block text-center">

						<?php if (isset($enbale_facebook_login) && $enbale_facebook_login == 'Y') { ?>
							<a href="<?php echo $authUrl; ?>" class="btn btn-dark text-white facebook"><i class="fa fa-facebook"></i> Facebook</a>
						<?php } ?>

						<?php if (isset($enbale_gmail_login) && $enbale_gmail_login == 'Y') { ?>
							<a href="<?php echo base_url(); ?>google_login" class="btn btn-dark text-white google"><i class="fa fa-google"></i> Gmail</a>
						<?php } ?>

						<!--
					<a class="btn btn-dark text-white twitter" ><i class="fa fa-twitter"></i> Twitter</a>
					<a class="btn btn-dark text-white instagram" ><i class="fa fa-instagram"></i> Instagram</a>
					<a class="btn btn-dark text-white linkedin" ><i class="fa fa-linkedin"></i> Linkedin</a>
					-->
					</div>
				<?php } ?>
			</div>

		</div>
	</div>
</div>