<?php

	
$config ['sidebar_left_items'] [34]  = array( 	'class' => 'locations',  'method'=> 'manage',
									'text'=> 'Locations', 'link'=> 'locations/manage',		
									'collapse_class'=> '', 'icon_class'=> 'fa fa-map-marker',
									'icon_feather' => 'map-pin'
									);

$CI =& get_instance();	


$sidebar_left = $CI->config->item('sidebar_left');

$new_sidebar_left = ($sidebar_left + $config['sidebar_left_items']);
$CI->config->set_item("sidebar_left" , $new_sidebar_left);		



	

															