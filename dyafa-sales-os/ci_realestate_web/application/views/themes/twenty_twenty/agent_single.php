<?php
$user_id = $single_user->user_id;
$first_name = $myHelpers->global_lib->get_user_meta($user_id, 'first_name');
$last_name = $myHelpers->global_lib->get_user_meta($user_id, 'last_name');
$mobile_no = $myHelpers->global_lib->get_user_meta($user_id, 'mobile_no');
$address = $myHelpers->global_lib->get_user_meta($user_id, 'address');
$photo_url = $myHelpers->global_lib->get_user_meta($user_id, 'photo_url');
$description = $myHelpers->global_lib->get_user_meta($user_id, 'description');
$social_media = $myHelpers->global_lib->get_user_meta($user_id, 'social_media');
?>
<!--
<section class="page-top-section set-bg" data-setbg="<?php echo site_url() . 'themes/' . $theme . '/'; ?>images/search_banner.jpg" style="background-image: url(<?php echo site_url() . 'themes/' . $theme . '/'; ?>images/search_banner.jpg);">
	<div class="container text-white">
		<h1><?php echo ucfirst($first_name) . ' ' . ucfirst($last_name); ?></h1>
	</div>
</section>
-->

<style>
.profile-agent-content {
    padding: 30px 50px 30px 30px;
    border: 1px solid #e1e1e1;
}
.profile-agent-content .profile-agent-info {
    position: relative;
}
.profile-agent-content .profile-agent-info .pi-pic {
    width: 120px;
    height: 120px;
    position: relative;
    float: left;
    margin-right: 30px;
}
.profile-agent-content .profile-agent-info .pi-pic img {
    border-radius: 50%;
	width:100%;
}
.profile-agent-content .profile-agent-info .pi-text {
    overflow: hidden;
    padding-top: 20px;
}
.profile-agent-content .profile-agent-info .pi-text h5 {
    color: #111111;
    font-weight: 700;
    margin-bottom: 4px;
}
.profile-agent-content .profile-agent-info .pi-text span {
    font-size: 16px;
    color: #2897bb;
    font-weight: 500;
}
.profile-agent-content .profile-agent-info .pi-text p {
    margin-bottom: 0;
    color: #111111;
    margin-top: 8px;
}
.profile-agent-content .profile-agent-newslatter {
    padding-top: 10px;
}

.profile-agent-content .col-lg-4:not(:last-child):after {
    position: absolute;
    right: 0;
    top: 0;
    width: 1px;
    height: 120px;
    background: #ebebeb;
    content: "";
}

.profile-agent-content .profile-agent-widget {
    padding-right: 0px;
    position: relative;
}
.profile-agent-content .profile-agent-widget ul {
    padding-top: 5px;
	padding-left:0px;
	margin-bottom:0px;
}
.profile-agent-content .profile-agent-widget ul li {
    list-style: none;
    font-size: 16px;
    color: #111111;
    font-weight: 500;
    line-height: 36px;
    overflow: hidden;
    -webkit-transition: all 22s;
    transition: all 0.2s;
}
.profile-agent-content .profile-agent-widget ul li span {
    font-weight: 600;
    float: right;
	font-size:14px;
}

.profile-agent-content .profile-agent-widget ul li .icon-social {
    width: 25px;
    height: 25px;
    line-height: 25px;
    border-radius: 50%;
    font-size: 12px;
    text-align: center;
    color: #fff;
    display: inline-block;
    margin-left: 0px;
}
</style>

<div class="site-section">
	<div class="container">

		<div class="profile-agent-content rounded mb-5">
			<div class="row">
				<div class="col-lg-4">
					<div class="profile-agent-info">
						<div class="pi-pic">
							<?php
							if (isset($photo_url) && !empty($photo_url)) {
								if (file_exists('uploads/user/' . $photo_url)) {
									echo '<img src="' . base_url() . 'uploads/user/' . $photo_url . '" class="agent_img img-responsive img-thumbnail">';
								} else {
									echo '<img src="' . $myHelpers->property_agent_image . '" class="agent_img img-responsive img-thumbnail">';
								}
							} else {
								echo '<img src="' . $myHelpers->property_agent_image . '" class="agent_img img-responsive img-thumbnail">';
							}
							?>
						</div>
						<div class="pi-text">
							<h5><?php echo ucfirst($first_name) . ' ' . ucfirst($last_name); ?></h5>
							<?php if (!empty($address)) { ?>
								<span><i class="fa fa-map-marker"></i> <?php echo $address; ?></span>
							<?php } ?>
							<p><?php echo mlx_get_lang('Real Estate') . ' ' . mlx_get_lang(ucfirst($single_user->user_type)); ?></p>
						</div>
					</div>
				</div>
				
				<?php if (isset($description) && !empty($description)) { ?>
					<div class="col-lg-4">
						<div class="profile-agent-newslatter">
							<h5 class="text-dark">
								<strong><?php echo mlx_get_lang('About'); ?> <?php echo ucfirst($first_name); ?></strong>
							</h5>
							<div class="agent-description mb-5"><?php echo $description; ?></div>
						</div>
					</div>
				<?php } ?>

				<div class="col-lg-4">
					<div class="profile-agent-widget">
						<ul>
							<li>
								<?php echo mlx_get_lang('Active Properties'); ?> 
								<span><?php echo $agents_propeties->num_rows(); ?></span>
							</li>
							<?php if (!empty($mobile_no) && 0) { ?>
								<li>
									<?php echo mlx_get_lang('Phone'); ?> 
									<span><?php echo $mobile_no; ?></span>
								</li>
							<?php } ?>
							<?php if (!empty($single_user->user_email) && 0) { ?>
								<li>
									<?php echo mlx_get_lang('Email'); ?> 
									<span><?php echo $single_user->user_email; ?></span>
								</li>
							<?php } ?>

							<?php

							if (isset($social_media) && !empty($social_media)) {
								$social_media_array = json_decode($social_media, true);
							?>
								<li>
									<?php echo mlx_get_lang('Social Connections'); ?>
									<span class="social-icons">
										<?php foreach ($social_media_array as $k => $v) {
											if (empty($v['url']))
												continue;
										?>
										<a title="<?php echo $v['title']; ?>" href="<?php echo $v['url']; ?>" class="icon-social <?php echo $v['icon']; ?>"></a>
									<?php } ?>
									</span>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				
			</div>
		</div>

		
		<div class="bg-white widget border rounded text-left">
			<div class="row">
				<?php
				$recaptcha_site_key = $myHelpers->global_lib->get_option('recaptcha_site_key');
				$recaptcha_secret_key = $myHelpers->global_lib->get_option('recaptcha_secret_key');
				?>
				<?php if (!empty($recaptcha_site_key) && !empty($recaptcha_secret_key)) { ?>
					<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=<?php echo $this->default_language; ?>" async defer>
					</script>
					<script type="text/javascript">
						var onloadCallback = function() {
							grecaptcha.render('recaptcha_element', {
								'sitekey': '<?php echo $recaptcha_site_key; ?>'
							});
						};
					</script>


				<?php } ?>
				
				<div class="col-md-12">
					<h3 class="h4 text-black widget-title mb-3"><?php echo mlx_get_lang('Contact for any Inquiry'); ?></h3>
				</div>

				<div class="col-md-12">
					<?php
					$args = array('class' => 'form-contact-agent', 'id' => 'contact_agent_form');
					echo form_open_multipart('', $args); ?>
					<input type="hidden" name="agent_email" value="<?php if (isset($single_user->user_email) && !empty($single_user->user_email)) echo $single_user->user_email; ?>">
						<div class="row">
							<div class="col-md-3">
								<input type="text" id="name" name="name" class="form-control" required placeholder="<?php echo mlx_get_lang('Name'); ?> *">
							</div>
							<div class="col-md-3">
								<input type="email" id="email" name="email" class="form-control" required placeholder="<?php echo mlx_get_lang('Email'); ?> *">
							</div>
							<div class="col-md-3">
								<textarea id="message" name="message" class="form-control" required style="height:auto;" placeholder="<?php echo mlx_get_lang('Message'); ?> *"></textarea>
							</div>
							
							<?php if (!empty($recaptcha_site_key) && !empty($recaptcha_secret_key)) { ?>
								<div class="col-md-3">
									<div id="recaptcha_element"></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<div class="col-md-3">
								<button type="submit" name="submit" class="btn custom-btn text-white submit-contact-agent-form-btn"><?php echo mlx_get_lang('Send Message'); ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>									
		
		<div class="row mb-3">
			<div class="col-md-12">
				<h2><?php echo ucfirst($first_name) . ' ' . ucfirst($last_name); ?><?php echo mlx_get_lang('\'s Properties'); ?>
					<small class="pull-right h6">(<?php echo $agents_propeties->num_rows(); ?> <?php echo mlx_get_lang('Properties'); ?>)</small>
				</h2>
				<hr class="mt-0 mb-5">
				<?php
				if (isset($agents_propeties) && $agents_propeties->num_rows() > 0) {
					echo '<div class="row">';
					foreach ($agents_propeties->result() as $prop_row) {
						echo '<div class=" col-lg-4 col-md-4 mb-4">';
						include('property/template-part/single-property-grid.php');
						echo '</div>';
					}
					echo '</div>';
				}
				?>



				</h4>
			</div>
			
		</div>

	</div>
</div>