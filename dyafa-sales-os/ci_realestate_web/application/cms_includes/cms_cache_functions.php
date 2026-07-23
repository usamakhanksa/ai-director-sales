<?php





function cms_get_results_cache($sql = "" , $cache_type = ""){
	
	
	
	$CI = &get_instance();
	
	
	$result = $CI->Common_model->commonQuery($sql );
	
	return $result;
	
}