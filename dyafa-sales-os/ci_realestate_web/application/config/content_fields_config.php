<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI = &get_instance();


$CI->admin_ajax_items["check_username_existence"] = array(
	"callback_id" => "check_username_existence", "callback_path" => "ajax_validation_lib/check_username_existence_callback"
);

$CI->admin_ajax_items["check_user_email_existence"] = array(
	"callback_id" => "check_user_email_existence", "callback_path" => "ajax_validation_lib/check_user_email_existence_callback"
);



$CI->admin_ajax_items["cms_register_user"] = array(
	"callback_id" => "cms_register_user", "callback_path" => "ajax_mailer_lib/register_user_form_callback_func"
);

$CI->admin_ajax_items["get_order_details"] = array(
	"callback_id" => "get_order_details", "callback_path" => "orders_lib/get_order_details_callback"
);

$CI->admin_ajax_items["upload_image"] = array(
	"callback_id" => "upload_image", "callback_path" => "ajax_images_lib/upload_image_callback_func"
);

/*
$CI->admin_ajax_items["upload_multi_images"] = array(
	"callback_id" => "upload_multi_images", "callback_path" => "ajax_images_lib/upload_multi_image_callback_func"
);
*/

$CI->admin_ajax_items["delete_image"] = array(
	"callback_id" => "delete_image", "callback_path" => "ajax_images_lib/delete_image_callback_func"
);
$CI->admin_ajax_items["upload_zip"] = array(
	"callback_id" => "upload_zip", "callback_path" => "ajax_images_lib/upload_zip_callback_func"
);


$CI->admin_ajax_items["site_contact_form_submit"] = array(
	"callback_id" => "site_contact_form_submit", "callback_path" => "ajax_mailer_lib/submit_contact_form_callback_func"
);


$CI->admin_ajax_items["site_contact_agent_form_submit"] = array(
	"callback_id" => "site_contact_agent_form_submit", "callback_path" => "ajax_mailer_lib/submit_site_contact_agent_form_callback"
);


$CI->admin_ajax_items["saveDataUrlasImage"] = array(
	"callback_id" => "saveDataUrlasImage", "callback_path" => "ajax_images_lib/saveDataUrlasImage_callback_func"
);

$CI->admin_ajax_items["upload_property_images"] = array(
	"callback_id" => "upload_property_images", "callback_path" => "ajax_images_lib/upload_property_images_callback_func"
);
$CI->admin_ajax_items["delete_property_image"] = array(
	"callback_id" => "delete_property_image", "callback_path" => "ajax_images_lib/delete_image_callback_func"
);


$CI->admin_ajax_items["upload_gallery_images"] = array(
	"callback_id" => "upload_gallery_images", "callback_path" => "ajax_images_lib/upload_gallery_images_callback_func"
);

$CI->admin_ajax_items["delete_gallery_images"] = array(
	"callback_id" => "delete_gallery_images", "callback_path" => "ajax_images_lib/delete_gallery_images_callback_func"
);


$CI->admin_ajax_items["add_image_from_media_ajax"] = array(
	"callback_id" => "add_image_from_media_ajax", "callback_path" => "ajax_images_lib/add_image_from_media_ajax_callback_func"
);

$CI->admin_ajax_items["export_lang_keyword"] = array(
	"callback_id" => "export_lang_keyword", "callback_path" => "settings_lib/export_lang_keyword_callback_func"
);

$CI->admin_ajax_items["import_lang_keyword"] = array(
	"callback_id" => "import_lang_keyword", "callback_path" => "settings_lib/import_lang_keyword_callback_func"
);


$CI->admin_ajax_items["update_site_lang_keywords"] = array(
	"callback_id" => "update_site_lang_keywords", "callback_path" => "settings_lib/update_keywords_callback_func"
);



$CI->admin_ajax_items["manage_direction"] = array(
	"callback_id" => "manage_direction", "callback_path" => "property_lib/manage_direction_callback_func"
);


$CI->admin_ajax_items["add_direction"] = array(
	"callback_id" => "add_direction", "callback_path" => "property_lib/add_direction_callback_func"
);

$CI->admin_ajax_items["toggle_featured_property_callback_func"] = array(
	"callback_id" => "toggle_featured_property_callback_func", "callback_path" => "property_lib/toggle_featured_property_callback_func"
);


$CI->admin_ajax_items["dashboard_widget_admin_total_properties"] = array(
	"callback_id" => "dashboard_widget_admin_total_properties",
	"callback_path" => "widget_lib/admin_total_properties_callback"
);
$CI->admin_ajax_items["dashboard_widget_total_enquiries"] = array(
	"callback_id" => "dashboard_widget_total_enquiries",
	"callback_path" => "widget_lib/admin_total_enquiries_callback"
);
$CI->admin_ajax_items["agent_widget_total_enquiries"] = array(
	"callback_id" => "agent_widget_total_enquiries",
	"callback_path" => "widget_lib/admin_total_enquiries_callback"
);

$CI->admin_ajax_items["dashboard_widget_user_total_properties"] = array(
	"callback_id" => "dashboard_widget_user_total_properties",
	"callback_path" => "widget_lib/user_total_properties_callback"
);

$CI->admin_ajax_items["dashboard_widget_total_agents"] = array(
	"callback_id" => "dashboard_widget_total_agents",
	"callback_path" => "widget_lib/total_agents_callback"
);

$CI->admin_ajax_items["dashboard_widget_total_owners"] = array(
	"callback_id" => "dashboard_widget_total_owners",
	"callback_path" => "widget_lib/total_owners_callback"
);

$CI->admin_ajax_items["dashboard_widget_total_builders"] = array(
	"callback_id" => "dashboard_widget_total_builders",
	"callback_path" => "widget_lib/total_builders_callback"
);

$CI->admin_ajax_items["dashboard_widget_total_landlords"] = array(
	"callback_id" => "dashboard_widget_total_landlords",
	"callback_path" => "widget_lib/total_landlords_callback"
);

$CI->admin_ajax_items["dashboard_widget_total_blogs"] = array(
	"callback_id" => "dashboard_widget_total_blogs",
	"callback_path" => "widget_lib/total_blogs_callback"
);

$CI->admin_ajax_items["save_widget_to_sidebar"] = array(
	"callback_id" => "save_widget_to_sidebar",
	"callback_path" => "widget_lib/save_widget_to_sidebar_callback_func"
);





$config['yes_no_options'] =  array();
$config['yes_no_options']['yes'] = "Yes";
$config['yes_no_options']['no'] = "No";

$config['active_inactive']['active'] = "Active";
$config['active_inactive']['inactive'] = "inactive";



/*********	Package Features	************************/


		$config['package_features'] = array();
		$config['package_features']['subscription'] = array('title' => 'Subscription', 'details' => '',);
		$config['package_features']['post_property'] = array('title' => 'Property Posting', 'details' => '',);
		$config['package_features']['featured_property'] = array('title' => 'Featured Property Posting', 'details' => '',);
		$config['package_features']['post_blog'] = array('title' => 'Blog Posting', 'details' => '',);




/* Payment Methos */








	/* Cash On Delievery*/
	$config['payment_methods']["payment_method_cod_section"] = array('title' => 'COD Mode');

	$config['payment_method_cod_section_fields'] = array();


	$config['payment_method_cod_section_fields'][]  = array(
		'id' => 'method_cod', 'name' => 'method_cod', 'title' => 'Method Name',

		'type' => 'text-field',	'required' => 'required', 'default' => 'Use: Cash on delivery (COD)',
	);

	$config['payment_method_cod_section_fields'][]  = array(
		'id' => 'method_cod_guide', 'name' => 'cod_payment_guide', 'title' => 'Payment guide',

		'type' => 'text-field',	'required' => 'required', 'default' => 'Displayed on the notice of successful purchase and payment page',
	);

	/* Bank Transfer */
	$config['payment_methods']["payment_method_bank_section"] = array('title' => 'Bank Transfer Mode');

	$config['payment_method_bank_section_fields'] = array();


	$config['payment_method_bank_section_fields'][]  = array(
		'id' => 'method_bank', 'name' => 'method_bank_transfer', 'title' => 'Method Name',

		'type' => 'text-field',	'required' => 'required', 'default' => 'Use: Bank Transfer',
	);

	$config['payment_method_bank_section_fields'][]  = array(
		'id' => 'method_bank_guide', 'name' => 'bank_transfer_guide', 'title' => 'Payment guide',

		'type' => 'text-field',	'required' => 'required', 'default' => 'Displayed on the notice of successful purchase and payment page',
	);



	$config['seo_static_pages']  = array();
	$config['seo_static_pages']["homepage"] = array('title' => 'SEO for Home Page');

	$config['seo_static_pages']["register"] = array('title' => 'SEO for Register Page');
	$config['seo_static_pages']["contact"] = array('title' => 'SEO for Contact Page');


	$config['seo_static_pages']["search"] = array('title' => 'SEO for Search Property Page');
	$config['seo_static_pages']["property-for-sale"] = array('title' => 'SEO for Search Property For Sale Page');
	$config['seo_static_pages']["property-for-rent"] = array('title' => 'SEO for Search Property For Rent Page');
	$config['seo_static_pages']["all_properties"] = array('title' => 'SEO for All Properties Page');
	$config['seo_static_pages']["agents"] = array('title' => 'SEO for Agents Page');
	$config['seo_static_pages']["blogs"] = array('title' => 'SEO for Blogs Page');
	$config['seo_static_pages']["blog_categories"] = array('title' => 'SEO for Blog Categories Page');

	/*$config ['seo_static_pages'] ["search"] = array('title' => 'SEO for Search Page');
		$config ['seo_static_pages'] ["search"] = array('title' => 'SEO for Search Page');
		$config ['seo_static_pages'] ["search"] = array('title' => 'SEO for Search Page');*/


	$config['seo_detail_fieds']  = array();
	$config['seo_detail_fieds'][]  = array(
		'id' => 'meta_keywords',
		'name' => 'meta_keywords',
		'title' => 'Meta Keywords',
		'type' => 'text-field',
		'required' => '',
		'default' => '',
	);

	$config['seo_detail_fieds'][]  = array(
		'id' => 'meta_description',
		'name' => 'meta_description',
		'title' => 'Meta Description',
		'type' => 'textarea',
		'required' => '',
		'default' => '',
	);




	$config['property_type_features']  = array();
	$config['property_type_features']["apartment"] = array("size","width","height","length","bedroom","bathroom","hall","kitchen");
	$config['property_type_features']["flat"] = array("size","width","height","length","bedroom","bathroom","hall","kitchen");
	$config['property_type_features']["land"] = array("size","width","length");
	$config['property_type_features']["farm-house"] = array("size","width","length"); 
	$config['property_type_features']["villa"] = array("size","width","height","length","bedroom","bathroom","garage");

	$config['property_type_features']["bungalow"] = array("size","width","height","length","bedroom","bathroom","kitchen","garage","hall");

	
	$config['property_type_features']["residential-plot"] = array("size","width","length");
	$config['property_type_features']["commercial-plot"] = array("size","width","length");
	
	
	
	
	$config['app_site_menus']  = array();
	$config['app_site_menus']['primary_menu']  = array(		'title' => 'Primary Menu',	);
	$config['app_site_menus']['footer_menu']  = array(		'title' => 'Footer Menu',	);




	$config['app_menu_static_pages']  = array();
	$config['app_menu_static_pages']["homepage"] = array(
		'title' => 'Home',
		'keyword' => 'home',
		'link' => array('home', ':lang', ''),
	);
	$config['app_menu_static_pages']["property-for-sale"] = array(
		'title' => 'Sale',
		'keyword' => 'property-for-sale',
		'link' => array('search', ':lang', 'property-for-sale')
	);
	$config['app_menu_static_pages']["property-for-rent"] = array(
		'title' => 'Rent',
		'keyword' => 'property-for-rent',
		'link' => array('search', ':lang', 'property-for-rent'),
	);
	$config['app_menu_static_pages']["all_properties"] = array(
		'title' => 'All Properties',
		'keyword' => 'all_properties',
		'link' => array('property', ':lang', ''),
	);
	$config['app_menu_static_pages']["blog"] = array(
		'title' => 'Blogs',
		'keyword' => 'blog',
		'link' => array('blogs', ':lang', ''),
	);
	$config['app_menu_static_pages']["agents"] = array(
		'title' => 'Agents',
		'keyword' => 'agents',
		'link' => array('search', ':lang', 'agents'),
	);
	$config['app_menu_static_pages']["contact"] = array(
		'title' => 'Contact Us',
		'keyword' => 'contact',
		'link' => array('contact', ':lang', ''),
	);
