<?php 

function location_search_fields_callback()
{
        $CI = &get_instance();
	
	ob_start();
	$CI->load->view(LOCATIONS_DIR."/front/includes/location_search_fields"); 
	
    echo $meta_settings = ob_get_clean();
}

add_action('location_search_fields', 'location_search_fields_callback', 10, 0);


function location_search_scripts_callback()
{
        $CI = &get_instance();
	
	ob_start();
	$CI->load->view(LOCATIONS_DIR."/front/includes/location_search_scripts"); 
	
       echo $meta_settings = ob_get_clean();
}

add_action('location_search_scripts', 'location_search_scripts_callback', 10, 0);

function location_search_where_fields_callback($fields = array())
{
    $CI = &get_instance();
	
	/*print_r($fields);*/
	extract($fields);
	
	if(empty($where)) $where = " ";
	
	$find = "city";
	if( isset($city)  &&   preg_match("/".$find ."/", $city))
	{
		$prop_city = str_replace($find . "-","",urldecode($city));
		$where .= " and prop.city='".$prop_city."' ";
	}
	
	$find = "state";
	if(isset($state)  && preg_match("/".$find ."/", $state))
	{
		$prop_state = str_replace($find . "-","",urldecode($state));
		$where .= " and prop.state='".$prop_state."' ";
	}
	
	
	if(isset($_GET["zipcode"])  &&  !empty($_GET["zipcode"])) 
	{
		
		$where .= " and prop.zip_code='".$_GET["zipcode"]."' ";
	}
	
	
	if(isset($_GET["subarea"])  &&  !empty($_GET["subarea"])) 
	{
		
		$where .= " and prop.sub_area='".$_GET["subarea"]."' ";
	}
	
	return $where;
    
}
add_filter('location_search_where_fields', 'location_search_where_fields_callback', 10, 1);


function location_search_get_fields_replace_callback($fields = array())
{
    $CI = &get_instance();
	
	extract($fields);
	
	if(isset($_GET['state']) ){
		if(!empty($_GET['state']))
		{
			$sExp = explode('~',$_GET['state']);
			if(count($sExp) > 1)
				$_GET['state'] = $sExp[0];
			$url_segs [] = "state-".urldecode($_GET['state']);
		}
		unset($_GET['state']);
	}
	if(isset($_GET['city']) ){
		if(!empty($_GET['city']))
		{
			$sExp = explode('~',$_GET['city']);
			
			if(count($sExp) > 1)
				$_GET['city'] = $sExp[0];
			$url_segs [] = "city-".urldecode($_GET['city']);
		}
		unset($_GET['city']);
	}
	
	
	
	return $url_segs;
    
}
add_filter('location_search_get_fields_replace', 'location_search_get_fields_replace_callback', 10, 1);

