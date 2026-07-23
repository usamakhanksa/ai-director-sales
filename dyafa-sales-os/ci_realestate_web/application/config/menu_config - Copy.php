<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


$config['sidebar_left'] = array();

$config['sidebar_left'][10]  = array(
	'class' => 'home', 'method' => '',
	'text' => 'Dashboard', 'link' => 'main',
	'collapse_class' => '', 'icon_class' => 'fa fa-dashboard',
);

$config['sidebar_left'][11]  = array(
	'class' => 'main', 'method' => 'home_page',
	'text' => 'Homepage', 'link' => 'main/home_page',
	'collapse_class' => '', 'icon_class' => 'fa fa-home',
);


$config['sidebar_left'][25]  = array(
	'class' => 'property',  'method' => '',
	'text' => 'Property', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-building'
);

$config['sidebar_left'][25]['item'][] = array(
	'class' => 'property',  'method' => 'manage',
	'text' => 'Manage', 'link' => 'property/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);


$config['sidebar_left'][25]['item'][] = array(
	'class' => 'property',  'method' => 'add_new',
	'text' => 'Add New ', 'link' => 'property/add_new',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][25]['item'][] = array(
	'class' => 'property',  'method' => 'prop_type',
	'text' => 'Manage Types ', 'link' => 'property/prop_type',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][25]['item'][] = array(
	'class' => 'property',  'method' => 'amenities',
	'text' => 'Amenities', 'link' => 'property/amenities',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][25]['item'][] = array(
	'class' => 'property',  'method' => 'distances',
	'text' => 'Distances', 'link' => 'property/distances',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

/** $config['sidebar_left'][25]['item'][] = array('class' => 'property',  'method' => 'settings','text' => 'Settings', 'link' => 'property/settings','collapse_class' => '', 'icon_class' => 'fa fa-circle-o',);*/



/*
		$config ['sidebar_left'] [25] ['item'] [] = array( 	'class' => 'property',  'method'=> 'doc_type',
												'text'=> 'Document Types', 'link'=> 'property/doc_type',		
												'collapse_class'=> '','icon_class'=> 'fa fa-circle-o',	);
		*/



$config['sidebar_left'][21]  = array(
	'class' => 'blog',  'method' => '',
	'text' => 'Blog', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-newspaper-o'
);

$config['sidebar_left'][21]['item'][] = array(
	'class' => 'blog',  'method' => 'manage',
	'text' => 'Manage', 'link' => 'blog/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][21]['item'][] = array(
	'class' => 'blog',  'method' => 'add_new',
	'text' => 'Add New', 'link' => 'blog/add_new',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][21]['item'][] = array(
	'class' => 'blog',  'method' => 'category',
	'text' => 'Category', 'link' => 'blog/category',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][30]  = array(
	'class' => 'media',  'method' => '',
	'text' => 'Media', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-image'
);

$config['sidebar_left'][30]['item'][] = array(
	'class' => 'media',  'method' => 'manage',
	'text' => 'Manage', 'link' => 'media/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);




$config['sidebar_left'][40]  = array(
	'class' => 'user',  'method' => '',
	'text' => 'User', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-users'
);

$config['sidebar_left'][40]['item'][] = array(
	'class' => 'user',  'method' => 'manage',
	'text' => 'Manage', 'link' => 'user/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);


$config['sidebar_left'][40]['item'][] = array(
	'class' => 'user',  'method' => 'add_new',
	'text' => 'Add New', 'link' => 'user/add_new',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][40]['item'][] = array(
	'class' => 'user',  'method' => 'settings',
	'text' => 'Settings', 'link' => 'user/settings',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);






/*$config['sidebar_left'][130]  = array(
	'class' => 'enquiries',  'method' => '',
	'text' => 'Enquiries', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-question'
);

$config['sidebar_left'][130]['item'][] = array(
	'class' => 'enquiries',  'method' => 'manage',
	'text' => 'Manage', 'link' => 'enquiries/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);*/

$config['sidebar_left'][41]  = array(
	'class' => 'banner',  'method' => '',
	'text' => 'Banner', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-server'
);

$config['sidebar_left'][41]['item'][] = array(
	'class' => 'banner',  'method' => 'manage',
	'text' => 'Manage', 'link' => 'banner/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);


$config['sidebar_left'][41]['item'][] = array(
	'class' => 'banner',  'method' => 'add_new',
	'text' => 'Add New', 'link' => 'banner/add_new',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);


$config['sidebar_left'][50]  = array(
	'class' => 'page',  'method' => '',
	'text' => 'Page', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-file'
);

$config['sidebar_left'][50]['item'][] = array(
	'class' => 'page',  'method' => 'manage',
	'text' => 'Manage', 'link' => 'page/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][50]['item'][] = array(
	'class' => 'page',  'method' => 'add_new',
	'text' => 'Add New', 'link' => 'page/add_new',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);


$config['sidebar_left'][80]  = array(
	'class' => 'appearance',  'method' => '',
	'text' => 'Appearance', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-star'
);

$config['sidebar_left'][80]['item'][] = array(
	'class' => 'appearance',  'method' => 'themes',
	'text' => 'Themes', 'link' => 'appearance/themes',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][80]['item'][] = array(
	'class' => 'appearance',  'method' => 'menus',
	'text' => 'Menus', 'link' => 'appearance/menus',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);
	
/*$config ['sidebar_left'] [80] ['item']  [] = array( 	'class' => 'appearance',  'method'=> 'widgets',
	'text'=> 'Widgets', 'link'=> 'appearance/widgets',		
	'collapse_class'=> '','icon_class'=> 'fa fa-circle-o',	);


		*/


$config['sidebar_left'][90]  = array(
	'class' => 'plugins',  'method' => '',
	'text' => 'Plugins', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-plug'
);

$config['sidebar_left'][90]['item'][] = array(
	'class' => 'plugins',  'method' => 'manage',
	'text' => 'Manage', 'link' => 'plugins/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][90]['item'][] = array(
	'class' => 'plugins',  'method' => 'add_new',
	'text' => 'Add New', 'link' => 'plugins/add_new',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]  = array(
	'class' => 'settings',  'method' => '',
	'text' => 'Settings', 'link' => '#',
	'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-cog'
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'general_settings',
	'text' => 'General Settings', 'link' => 'settings/general_settings',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'seo_settings',
	'text' => 'SEO Settings', 'link' => 'settings/seo_settings',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'social_settings',
	'text' => 'Social Settings', 'link' => 'settings/social_settings',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'site_languages',
	'text' => 'Site Languages', 'link' => 'settings/site_languages',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'front_keyword_settings',
	'text' => 'Front Keywords Settings', 'link' => 'settings/front_keyword_settings',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'admin_keyword_settings',
	'text' => 'Admin Keywords Settings', 'link' => 'settings/admin_keyword_settings',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'sitemaps',
	'text' => 'Sitemaps', 'link' => 'settings/sitemaps',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'email_setting',
	'text' => 'Email Settings', 'link' => 'settings/email_setting',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'email_templates',
	'text' => 'Email Templates', 'link' => 'settings/email_templates',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'profile',
	'text' => 'Profile', 'link' => 'settings/profile',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'db_settings',
	'text' => 'Database Backup', 'link' => 'settings/db_settings',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][120]['item'][] = array(
	'class' => 'settings',  'method' => 'change_password',
	'text' => 'Change Password', 'link' => 'settings/change_password',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);


/* $config ['sidebar_left'] [121]  = array( 	'class' => 'packages',  'method'=> 'packages/manage',
		 'text'=> 'Packages', 'link'=> 'packages/manage',		
		 'collapse_class'=> 'fa fa-angle-left pull-right', 'icon_class'=> 'fa fa-cog');*/


$config['sidebar_left'][122]  = array(
	'class' => 'packages',  'method' => '',
	'text' => 'Payments', 'link' => '#', 'collapse_class' => 'fa fa-angle-left pull-right', 'icon_class' => 'fa fa-credit-card'
);

$config['sidebar_left'][122]['item'][] = array(
	'class' => 'packages',  'method' => 'manage',
	'text' => 'Packages', 'link' => 'packages/manage',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][122]['item'][] = array(
	'class' => 'packages',  'method' => 'transaction',
	'text' => 'Transactions', 'link' => 'packages/transaction',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);



$config['sidebar_left'][122]['item'][] = array(
	'class' => 'packages',  'method' => 'payment_methods',
	'text' => 'Payment Methods', 'link' => 'packages/payment_methods',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][122]['item'][] = array(
	'class' => 'packages',  'method' => 'choose_package',
	'text' => 'Choose Package', 'link' => 'packages/choose_package',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][122]['item'][] = array(
	'class' => 'packages',  'method' => 'my_credits',
	'text' => 'My Credits', 'link' => 'packages/my_credits',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);

$config['sidebar_left'][122]['item'][] = array(
	'class' => 'packages',  'method' => 'my_transactions',
	'text' => 'My Transactions', 'link' => 'packages/my_transactions',
	'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
);
		/*
		$config ['sidebar_left'] [123]  = array( 	'class'=> 'packages', 'method' => 'choose_package', 
											'text' => 'Choose Package', 'link' => 'packages/choose_package',		
											'collapse_class' => '','icon_class' => 'fa fa-home', 				);

		$config ['sidebar_left'] [124]  = array( 'class'=> 'packages', 'method' => 'my_credits', 
		'text' => 'My Credits', 'link' => 'packages/my_credits',		
		'collapse_class' => '','icon_class' => 'fa fa-home', 				);
		*/



		/* Badge menu start */
		/*
		$config ['sidebar_left'] [125]  = array( 	'class' => 'badge',  'method'=> '',
											'text'=> 'Badge', 'link'=> '#',		
											'collapse_class'=> 'fa fa-angle-left pull-right', 'icon_class'=> 'fa fa-server'	);
	
		$config ['sidebar_left'] [125] ['item']  [] = array( 	'class' => 'badge',  'method'=> 'manage',
												'text'=> 'Manage', 'link'=> 'badge/manage',		
												'collapse_class'=> '','icon_class'=> 'fa fa-circle-o',	);
		
		
		$config ['sidebar_left'] [125] ['item'] [] = array( 	'class' => 'badge',  'method'=> 'add_new',
												'text'=> 'Add New', 'link'=> 'badge/add_new',		
												'collapse_class'=> '','icon_class'=> 'fa fa-circle-o',	);
		*/
