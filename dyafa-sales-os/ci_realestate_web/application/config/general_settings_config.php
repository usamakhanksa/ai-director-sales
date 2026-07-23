<?php if (!defined('BASEPATH')) exit('No direct script access allowed');




$config['general_settings_sections'] = array();

$config['general_settings_sections']["general_settings"]
	= 	array(
		'title' => 'General Settings',
		'template' => 'general_settings_blocks/general_settings'
	);

$config['general_settings_sections']["visual_section_settings"]
	= array(
		'title' => 'Visual Section Settings',
		'template' => 'general_settings_blocks/visual_section_settings'
	);



$config['general_settings_sections']["payment_settings"]
	= array(
		'title' => 'Payment Settings',
		'template' => 'general_settings_blocks/payment_settings'
	);


$config['general_settings_sections']["login_page_settings"]
	= array(
		'title' => 'Login Page Settings',
		'template' => 'general_settings_blocks/login_page_settings'
	);

$config['general_settings_sections']["admin_settings"]
	= array(
		'title' => 'Admin Settings',
		'template' => 'general_settings_blocks/admin_settings'
	);

/*$config['general_settings_sections']["google_recaptcha_settings"]
	= array(
		'title' => 'Google reCAPTCHA  Settings',
		'template' => 'general_settings_blocks/google_recaptcha_settings'
	);*/




$config['general_settings_sections']["property_settings"]
	= array(
		'title' => 'Property Settings',
		'template' => 'general_settings_blocks/property_settings'
	);


$config['general_settings_sections']["search_settings"]
	= array(
		'title' => 'Search Settings',
		'template' => 'general_settings_blocks/search_settings'
	);

$config['general_settings_sections']["blog_settings"]
	= array(
		'title' => 'Blog Settings',
		'template' => 'general_settings_blocks/blog_settings'
	);


$config['general_settings_sections']["contact_form_settings"]
	= array(
		'title' => 'Contact Form Settings',
		'template' => 'general_settings_blocks/contact_form_settings'
	);


$config['general_settings_sections']["footer_settings"]
	= array(
		'title' => 'Footer Settings',
		'template' => 'general_settings_blocks/footer_settings'
	);


$config['general_settings_sections']["cookie_settings"]
	= array(
		'title' => 'Cookie Settings',
		'template' => 'general_settings_blocks/cookie_settings'
	);
