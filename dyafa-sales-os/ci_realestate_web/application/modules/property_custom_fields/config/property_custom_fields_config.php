<?php
								
												
		$config['sidebar_left_items'][25]['item'][] = array(
			'class' => 'property',  'method' => 'custom_fields',
			'text' => 'Custom Fields', 'link' => 'property/custom_fields',
			'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
		);										
		
		
	
	$CI =& get_instance();	

	$CI->load->library('global_lib');
	$new_sidebar_left = $CI->global_lib->append_menu_items('sidebar_left' , $config );
	
	
	
	
	/* user access */
	/*$config['user_access'] = array(
		"shop_owner" =>array(
			"menu" => array( 
				"menu_items" => array(
					
					"branches||import_items",
					
					)
				),
			"controller" => array( 
				"all_items"=> array("branches")
			),
			"view" => array( 
				"all_items"=> array(
					"branches" => array("import_items"),
					)
				),
			
			),
		);
	
	
	//$CI->global_lib->add_user_permission($config['user_access']);*/
	
	
	/* tab link*/
	
	
	$general_setting_options = $CI->config->item('general_setting_options');
	if(!is_array($general_setting_options)) $general_setting_options = array();
		
	$tab_fields = array(
		'request_for_shop',
	);
	
	$config ['general_setting_option'] []  = array(	'tab_text'=> 'Branch settings', 
													'tab_link' => 'branch_settings', 
													'tab_fields'=>$tab_fields,
													'tab_view'=>'branches/admin/settings/branches_option_view',
											);
	
	
	
	$general_setting_options = array_merge($general_setting_options , $config['general_setting_option']);
	
//	$CI->config->set_item("general_setting_options" , $general_setting_options);

	