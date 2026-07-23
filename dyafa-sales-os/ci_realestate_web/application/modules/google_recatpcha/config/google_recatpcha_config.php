<?php
$CI = &get_instance();


$config["google_recaptcha_settings"]	= array(
	'title' => 'Google Recaptcha Settings',
	'module_template' => GOOGLE_RECATPCHA_DIR . '/admin/settings/google_recaptcha_settings',
);

$general_settings_sections = $CI->config->item('general_settings_sections');
$general_settings_sections = array_merge($general_settings_sections, $config);
$CI->config->set_item("general_settings_sections", $general_settings_sections);
