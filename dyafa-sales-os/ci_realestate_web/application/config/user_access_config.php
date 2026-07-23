<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


$config['site_users'] = array(
						"admin" => array("title" => "Admin"),
						"agent" => array("title" => "Agent"),
						"owner" => array("title" => "Owner"),
						"builder" => array("title" => "Builder"),
						"landlord" => array("title" => "Landlord")
						);




$config['site_user_permissions']  =   array(
										"property"  =>  array(
												"title" => "Properties",
												"permissions" => array(
													array(
														"title" => "Properties", "permission" => "property", ),
													array(
														"title" => "Manage Properties", "permission" => "property||manage", ),
													array(
														"title" => "Add New Property", "permission" => "property||add_new", ),
													array(
														"title" => "Edit Property", "permission" => "property||edit", ),
												),
											),
										
										"blog"  =>  array(
												"title" => "Blogs",
												"permissions" => array(
													array(
														"title" => "Blogs", "permission" => "blog", ),
													array(
														"title" => "Manage Blogs", "permission" => "blog||manage", ),
													array(
														"title" => "Add New Blog", "permission" => "blog||add_new", ),
													array(
														"title" => "Edit Blog", "permission" => "blog||edit", ),
												),
											),
										
										);


$config['site_user_access'] = 
array( 
		"admin" => 
		array(	
			"menu" => array( "has_access" => "exclude",
							"menu_items" => array("packages||choose_package","packages||my_credits",)
							),
			"controller" => array( "has_access" => "exclude",
							"all_items" => array()),
			"view" => array( "has_access" => "exclude",
							"all_items" => array()),
			"content" => array( 
						"has_access" => "access_all",
						"default_status" => "publish_all"
						),
			"widget" => array( "has_access" => "access_all"),
			
			),	
		"agent" => 
		array(	
			"menu" => array( 
				"has_access" => "limited",
				"menu_items" => array("home",
					"property",
					"property||add_new",
					"property||edit",
					"property||manage",
					"property||manage_inactive",
					"property||manage_featured",
					"property||layouts",
					"blog",
					"blog||add_new",
					"blog||edit",
					"blog||manage",
					"media",
					"media||manage",
					"settings","settings||change_password","settings||profile","settings||agency_settings",
					"agency_settings","agency_settings||settings",
					"packages",
					"packages||choose_package",
					"packages||my_credits",
					"packages||my_transactions",
					)
				),
			"controller" => array( 
				"has_access" => "limited",
				"all_items"=> array("settings","property","blog","media","packages")
			),
			"view" => array( 
				"has_access" => "limited",
				"all_items"=> array(
					"property"=>array("manage","add_new","edit","manage_inactive","manage_featured","layouts"),
					"blog"=>array("manage","add_new","edit"),
					"media"=>array("manage"),
					"settings"=>array("change_password","profile"),
					"agency_settings" => array('index'),
					"packages"=>array("front_package_page","my_credits","my_transactions"),
					)
				),
			"content" => array( 
				"has_access" => "limited",
				"all_items"=> array(
					"client" => array( "view_all" , "view" ), 
					"loan" => array( "view_all" ),
					),
				"default_status" => "publish_all"	
				),	
			"widget" => array( "has_access" => "access_all"),	
			),
		
		"builder" => 
		array(	
			"menu" => array( 
				"has_access" => "limited",
				"menu_items" => array("home",
					"property",
					"property||add_new",
					"property||edit",
					"property||manage",
					"property||manage_inactive",
					"property||manage_featured",
					"blog",
					"blog||add_new",
					"blog||edit",
					"blog||manage",
					"media",
					"media||manage",
					"settings","settings||change_password","settings||profile",
					"packages",
					"packages||choose_package",
					"packages||my_credits",
					"packages||my_transactions",
					)
				),
			"controller" => array( 
				"has_access" => "limited",
				"all_items"=> array("settings","property","media","blog","packages")
			),
			"view" => array( 
				"has_access" => "limited",
				"all_items"=> array(
					"property"=>array("manage","add_new","edit","manage_inactive","manage_featured"),
					"media"=>array("manage"),
					"blog"=>array("manage","add_new","edit"),
					"settings"=>array("change_password","profile"),
					"packages"=>array("front_package_page","my_credits","my_transactions"),
					)
				),
			"content" => array( 
				"has_access" => "limited",
				"all_items"=> array(
					"client" => array( "view_all" , "view" ), 
					"loan" => array( "view_all" ),
					),
				"default_status" => "publish_all"	
				),	
			"widget" => array( "has_access" => "access_all"),	
			),	
		
		"owner" => 
		array(	
			"menu" => array( 
				"has_access" => "limited",
				"menu_items" => array("home",
					"property",
					"property||add_new",
					"property||edit",
					"property||manage",
					"property||manage_inactive",
					"property||manage_featured",
					"blog",
					"blog||add_new",
					"blog||edit",
					"blog||manage",
					"media",
					"media||manage",
					"settings","settings||change_password","settings||profile",
					"packages",
					"packages||choose_package",
					"packages||my_credits",
					"packages||my_transactions",
					)
				),
			"controller" => array( 
				"has_access" => "limited",
				"all_items"=> array("settings","property","media","blog","packages")
			),
			"view" => array( 
				"has_access" => "limited",
				"all_items"=> array(
					"property"=>array("manage","add_new","edit","manage_inactive","manage_featured"),
					"media"=>array("manage"),
					"blog"=>array("manage","add_new","edit"),
					"settings"=>array("change_password","profile"),
					"packages"=>array("front_package_page","my_credits","my_transactions"),
					)
				),
			"content" => array( 
				"has_access" => "limited",
				"all_items"=> array(
					"client" => array( "view_all" , "view" ), 
					"loan" => array( "view_all" ),
					),
				"default_status" => "publish_all"	
				),	
			"widget" => array( "has_access" => "access_all"),	
			),
		
		"landlord" => 
		array(	
			"menu" => array( 
				"has_access" => "limited",
				"menu_items" => array("home",
					"property",
					"property||add_new",
					"property||edit",
					"property||manage",
					"property||manage_inactive",
					"property||manage_featured",
					"blog",
					"blog||add_new",
					"blog||edit",
					"blog||manage",
					"media",
					"media||manage",
					"settings","settings||change_password","settings||profile",
					"packages",
					"packages||choose_package",
					"packages||my_credits",
					"packages||my_transactions",
					)
				),
			"controller" => array( 
				"has_access" => "limited",
				"all_items"=> array("settings","property","media","blog","packages")
			),
			"view" => array( 
				"has_access" => "limited",
				"all_items"=> array(
					"property"=>array("manage","add_new","edit","manage_inactive","manage_featured"),
					"media"=>array("manage"),
					"blog"=>array("manage","add_new","edit"),
					"settings"=>array("change_password","profile"),
					"packages"=>array("front_package_page","my_credits","my_transactions"),
					)
				),
			"content" => array( 
				"has_access" => "limited",
				"all_items"=> array(
					"client" => array( "view_all" , "view" ), 
					"loan" => array( "view_all" ),
					),
				"default_status" => "publish_all"	
				),	
			"widget" => array( "has_access" => "access_all"),	
			),
	);