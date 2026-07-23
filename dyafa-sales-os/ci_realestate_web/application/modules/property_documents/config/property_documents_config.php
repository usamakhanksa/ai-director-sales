<?php
										
	
	$config ['sidebar_left_items'] [35]  = array( 	'class' => 'document',  'method'=> '',
										'text'=> 'Document', 'link'=> '#',		
										'collapse_class'=> 'fa fa-angle-left pull-right', 'icon_class'=> 'fa fa-folder'	);
		
		$config ['sidebar_left_items'] [35] ['item']  [] = array( 'class' => 'document',  'method'=> 'manage',
											'text'=> 'Manage', 'link'=> 'document/manage',		
											'collapse_class'=> '','icon_class'=> 'fa fa-circle-o',	);
		
		/*
		$config ['sidebar_left_items'] [35] ['item']  [] = array( 'class' => 'document',  'method'=> 'settings',
											'text'=> 'Settings', 'link'=> 'document/settings',		
											'collapse_class'=> '','icon_class'=> 'fa fa-circle-o',	);
		*/
	
		$config['sidebar_left_items'][35]['item'][] = array(
													'class' => 'document',  'method' => 'type',
													'text' => 'Types', 'link' => 'document/type',
													'collapse_class' => '', 'icon_class' => 'fa fa-circle-o',
												);										
		
		
	
	$CI =& get_instance();	

	$CI->load->library('global_lib');
	$new_sidebar_left = $CI->global_lib->append_menu_items('sidebar_left' , $config );
	
	