<?php $CI = &get_instance(); ?>
<!DOCTYPE html>
<html lang="<?php echo $myHelpers->default_language ?>">
<!-- en-US"> -->

<head>

	<?php
	/*$website_title = get_option('website_title');*/
	$website_title = apply_filters("cms_get_details", '','website_title');
	
	/*$company_tel = get_option('company_tel');*/
	$company_tel = apply_filters("cms_get_details", '','company_tel');
	
	/*$company_email = get_option('company_email');*/
	$company_email = apply_filters("cms_get_details", '','company_email');
	
	$site_language = 	get_option('site_language');
	$default_language = get_option('default_language');
	/*
	$fevicon_icon = 	get_option('fevicon_icon');
	$social_media = 	get_option('social_media');
	
	*/
	$fevicon_icon = apply_filters("cms_get_details", '','fevicon_icon');
	$social_media = apply_filters("cms_get_details", '','social_media');
	
	$enbale_front_end_registration = get_option('enbale_front_end_registration');
	$enbale_front_end_login = get_option('enbale_front_end_login');

	?>
	
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<?php 
	
	
	if(isset($og_meta) && !empty($og_meta))
	{
		foreach($og_meta as $ogk=>$ogv)
		{
			echo '<meta property="'.$ogk.'" content="'.$ogv.'" />';
		}
	}
	
	?>

	<?php if (isset($_SESSION['default_lang_front'])) {
		$lang = $_SESSION['default_lang_front'];
		$lang_exp = explode("~", $lang);
		if (isset($lang_exp[1])) {
			$clang = $lang_exp[1];
	?>
			<meta http-equiv="content-language" content="<?php echo $clang; ?>">
	<?php }
	} ?>

	<?php
	if (isset($seometa_for)) {
		//$myHelpers->seometa_lib->get_metadata($seometa_for);
	}
	?>

	<?php
	if (isset($fevicon_icon) && !empty($fevicon_icon) && file_exists('uploads/media/' . $fevicon_icon))
		echo '<link rel="shortcut icon" href="' . base_url() . 'uploads/media/' . $fevicon_icon . '">';
	else
		echo '<link rel="shortcut icon" href="' . base_url() . 'application/views/' . $theme . '/assets/images/fav.png">';
	?>



	<?php if (isset($this->canonical_url)) {	?>
		<link rel="canonical" href="<?php echo $this->canonical_url; ?>" />
	<?php	}	?>

	<?php if (isset($this->enable_multi_lang) && $this->enable_multi_lang == true) {	?>
		<link rel="alternate" hreflang="<?php echo $this->hreflang; ?>" href="<?php echo $this->hreflang_url; ?>" />
	<?php	}	?>



	<title><?php if (isset($page_title) && !empty($page_title)) {
				echo stripslashes($page_title) . ' | ';
			} ?><?php echo $website_title; ?></title>

	
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,700,900|Roboto+Mono:300,400,500">

	<?php echo link_tag("application/views/$theme/assets/css/font-awesome.min.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/fonts/icomoon/style.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/css/flag-icon.min.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/css/bootstrap.min.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/css/magnific-popup.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/css/jquery-ui.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/css/owl.carousel.min.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/css/owl.theme.default.min.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/fonts/flaticon/font/flaticon.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/css/style.css"); ?>
	<?php echo link_tag("application/views/$theme/assets/css/custom-style.css"); ?>


	<?php echo script_tag("application/views/$theme/assets/js/jquery-3.3.1.min.js"); ?>
	<?php echo script_tag("application/views/$theme/assets/dompurify/0.8.4/purify.min.js"); ?>
	
	<?php if ($this->site_direction == 'rtl') { ?>
		<?php echo link_tag("application/views/$theme/assets/css/bootstrap-rtl.min.css"); ?>
		<?php echo link_tag("application/views/$theme/assets/css/style-rtl.css"); ?>
	<?php } ?>

	<script>
		var is_rtl = false;
		<?php if ($this->site_direction == 'rtl') { ?>
			is_rtl = true;
		<?php } ?>

		var def_lang = '';
		<?php if ($this->enable_multi_lang == true) { ?>
			def_lang = '/<?php echo $this->default_language; ?>';
		<?php } ?>
		var base_url = '<?php echo base_url(); ?>';
	</script>

	<style type="text/css">
		.room-info .rf-float:nth-child(odd) {
			float: left;
			clear: left;
		}

		.room-info .rf-float:nth-child(even) {
			float: right;
		}
		
		.form-search .btn[type="submit"]{
			margin-top: 30px;
		} 
		
		
		video {
			width: auto;
			max-width: 100%;
			height: auto;
		}
		
		.mt-n33 {
			margin-top: -33px;
		}

		
		

		
	</style>
	
	
	<?php do_action("cms_header");?>
	
	<?php echo link_tag("application/views/$theme/style.css"); ?>
	
	<style > 
	@media only screen and (max-width: 767px) {
		.header-top_hide_mobile {
			display: none;
		}
	
	}
	</style>
	
</head>

<body>

	<div class="site-loader"></div>

	<?php
	if (isset($homepage_section) && count($homepage_section) > 0) {
		$sections = $homepage_section;
		foreach ($sections as $section_key => $section_settings) {
			if ($section_settings['is_enable'] != 'Y' && $section_key == 'slider_section') {
				$has_banner = false;
				break;
			}
		}
	}
	
	$logged_in = $this->session->userdata('logged_in');
	
	
	/*if (isset($logged_in) && $logged_in == TRUE) 
		$show_header_top_on_mobile = "";
	else
		$show_header_top_on_mobile = " header-top_hide_mobile"; */
		
	$show_header_top_on_mobile = "";	
	?>

	<div class="site-wrap <?php if (!isset($has_banner) || (isset($has_banner) && $has_banner != true)) echo 'no-banner'; ?> d-print-none">

		<header class="header-section bg-trans-dark">
			<div class="header-top <?php echo $show_header_top_on_mobile; ?>">
				<div class="container">
					<div class="row">
						<div class="col-lg-5 header-top-left">
							<?php if (isset($company_tel) && !empty($company_tel)) { ?>
								<div class="top-info">
									<i class="fa fa-phone"></i>
									<?php echo $company_tel; ?>
								</div>
							<?php } ?>
							<?php if (isset($company_email) && !empty($company_email)) { ?>
								<div class="top-info">
									<i class="fa fa-envelope"></i>
									<?php echo $company_email; ?>
								</div>
							<?php } ?>
						</div>
						<div class="col-lg-7 text-lg-right header-top-right">

							<?php if (isset($social_media) && !empty($social_media) && 0) {
								$social_media_array = json_decode($social_media, true);

							?>
								<div class="top-social">
									<?php foreach ($social_media_array as $k => $v) {
										if (!isset($v['enable']) || (isset($v['enable']) && $v['enable'] != 1))
											continue;
									?>
										<a href="<?php echo $v['url']; ?>" target="_blank"><i class="fa <?php echo $v['icon']; ?>"></i></a>
									<?php } ?>

								</div>
							<?php } ?>

							<div class="user-panel">
								<?php
								
								if (isset($logged_in) && $logged_in == TRUE) {
								?>
									<a href="<?php echo base_url('logout');  ?>"><i class="fa fa-sign-out"></i> <?php echo mlx_get_lang('Logout'); ?></a>
									<a href="<?php echo base_url('admin/main');  ?>"><i class="fa fa-dashboard"></i> <?php echo mlx_get_lang('Dashboard'); ?></a>
								<?php } else { ?>
									<?php if (isset($enbale_front_end_login) && $enbale_front_end_login == 'Y') { ?>
										<a href="<?php echo base_url('admin'); ?>"><i class="fa fa-sign-in"></i> <?php echo mlx_get_lang('Login'); ?></a>
									<?php } ?>

									<?php if (isset($enbale_front_end_registration) && $enbale_front_end_registration == 'Y') { ?>
										<a href="<?php echo base_url('register');  ?>"><i class="fa fa-user-plus"></i> <?php echo mlx_get_lang('Register'); ?></a>
									<?php } ?>
								<?php } ?>
							</div>

							<?php

							if (isset($CI->enable_multi_lang) && $CI->enable_multi_lang  == true) { ?>

								<div class="dropdown multi_language">
									<button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?php
										$flag_codes =  array(
											"en" => "us",
											"hi" => "in", "cs" => "cz",
											"he" => "is", "ja" => "jp", "ko" => "sk", "da" => "dk",
										);

											
										if (
											isset($CI->default_language) && !empty($CI->default_language) &&
											isset($CI->default_language_title) && !empty($CI->default_language_title)
										) {
											$lang_title = $CI->default_language_title;
											$flag_code = $lang_code = $lang_code_title =  $CI->default_lang_code;
											if (array_key_exists($flag_code, $flag_codes))
												$flag_code = $flag_codes[$flag_code];
												
											$lang_code_title = $CI->default_lang_code_small	;
										?>

											<span class="flag-icon flag-icon-<?php echo $flag_code; ?>"></span> <?php echo strtoupper($lang_code_title); ?>
										<?php } else if (isset($default_language) && !empty($default_language)) {
											$lang_exp = explode('~', $default_language);
											$lang_title = $lang_exp[0];
											$lang_code = $lang_code_title = $lang_exp[1];


											$lang_code_combi = $lang_exp[1];
											$lang_code_exp = explode('-', $lang_code_combi);
											if (isset($lang_code_exp[1])) {
												$flag_code = strtolower($lang_code_exp[1]);
											} else
												$flag_code = $lang_code_exp[0];

											if (array_key_exists($flag_code, $flag_codes)) {
												$flag_code = $flag_codes[$flag_code];
											}
										?>
											<span class="flag-icon flag-icon-<?php echo $flag_code; ?>"></span> <?php echo strtoupper($lang_code_title); ?>
										<?php } else { ?>
											<span class="flag-icon flag-icon-us"></span> En
										<?php } ?>
									</button>
									<div class="dropdown-menu dropdown-menu-right language">
										<?php if (isset($site_language) && !empty($site_language)) {
											$site_language_array = json_decode($site_language, true);

											foreach ($site_language_array as $aak => $aav) {
												if ($aav['language'] == $default_language) {
													$new_value = $site_language_array[$aak];
													unset($site_language_array[$aak]);
													array_unshift($site_language_array, $new_value);
													break;
												}
											}


											$current_user_lang_code = 'en';



											if (isset($_SESSION['default_lang_front']) && !empty($_SESSION['default_lang_front'])) {
												$sesson_def_lang =   $_SESSION['default_lang_front'];
												$lang_exp = explode('~', $_SESSION['default_lang_front']);

												$lang_code_full = $lang_code = $lang_exp[1];
												$lang_title = $lang_exp[0];

												$lang_code_combi = $lang_exp[1];
												$lang_code_exp = explode('-', $lang_code_combi);
												/*if (isset($lang_code_exp[1])) {
													$current_user_lang_code = strtolower($lang_code_exp[1]);
												} else
													$current_user_lang_code = $lang_code_exp[0];*/
													
												$current_user_lang_code = strtolower($lang_code_combi);		
													
											}


											foreach ($site_language_array as $k => $v) {
												if (!isset($v['status']) || (isset($v['status']) && $v['status'] != 'enable'))
													continue;
												$lang_exp = explode('~', $v['language']);
												$lang_title = $lang_exp[0];

												$lang_code_combi = $lang_exp[1];
												$lang_code_exp = explode('-', $lang_code_combi);

												if (isset($lang_code_exp[1])) {
													$lang_code_title = $lang_code_exp[1];
													$flag_code = $lang_code = strtolower($lang_code_exp[1]);
												} else
													$flag_code = $lang_code = $lang_code_title = $lang_code_exp[0];
													
												$lang_code_small = strtolower($lang_code_combi);	
												$lang_code_title = $lang_code_combi	;

												if (array_key_exists($flag_code, $flag_codes))
													$flag_code = $flag_codes[$flag_code];

												$parts = explode('?', $_SERVER['REQUEST_URI'], 2);

												$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";


												$current_url = $protocol . $_SERVER['HTTP_HOST'] . $parts[0] . (isset($parts[1]) ? '?' . $parts[1] : '');

												if (substr($current_url, -1) != '/')
													$current_url .= "/";

												if (isset($page_title) && $page_title == 'Home') {
													$current_url = str_replace("/home/$current_user_lang_code/", "/", $current_url);
													$current_url .= "home/$lang_code_small/";
												}

												$current_url = str_replace("/$current_user_lang_code/", "/" . $lang_code_small . "/", $current_url);


										?>
												<a class="dropdown-item" href="<?php echo $current_url; ?>" 
														data-lang_code="<?php echo $flag_code; ?>" 
														data-lang_code_or="<?php echo $lang_code_combi; ?>" 
														data-lang_title="<?php echo $lang_title; ?>"><span class="flag-icon flag-icon-<?php echo $flag_code; ?>"></span> <?php echo strtoupper($lang_code_title); ?> </a>
										<?php }
										} ?>
									</div>
								</div>

							<?php } ?>


						</div>
					</div>
				</div>
			</div>
			<div class="site-mobile-menu">
				<div class="site-mobile-menu-header ">
					<div class="site-mobile-menu-close mt-3">
						<span class="icon-close2 js-menu-toggle"></span>
					</div>
				</div>
				<div class="site-mobile-menu-body"></div>
			</div>
			<?php
			/*$website_logo_text = get_option('website_logo_text');
			$website_logo = 	get_option('website_logo');*/
			
			$website_logo_text = apply_filters("cms_get_details", '','website_logo_text');
			$website_logo = apply_filters("cms_get_details", '','website_logo');
			
			?>
			<div class="site-navbar">
				<div class="container py-1">
					<div class="row align-items-center">
						<div class="col-8 col-md-8 col-lg-4 logo-block">
							<h1 class="mb-0"><a href="<?php echo $myHelpers->menu_lib->get_url('home'); ?>" class="text-white h2 mb-0">
									<?php if (isset($website_logo) && !empty($website_logo) && file_exists('uploads/media/' . $website_logo)) { ?>
										<img class="logo-img" src="<?php echo site_url() . 'uploads/media/' . $website_logo; ?>" alt="<?php echo $website_logo_text; ?>">
									<?php } else if (isset($website_logo_text)) {
										echo '<strong">' . $website_logo_text . '</strong>';
									}
									?>
								</a></h1>
						</div>

						<?php

						$this->load->view("$theme/includes/header_menu");
						?>

					</div>
				</div>
			</div>
		</header>


	</div>

	<?php

	$this->load->view($content); ?>

	<?php $this->load->view("$theme/footer"); ?>