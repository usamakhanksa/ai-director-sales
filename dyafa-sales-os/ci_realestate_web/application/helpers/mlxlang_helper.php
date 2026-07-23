<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('mlx_get_lang'))
{
    function mlx_get_lang($keyword = "")
    { 	
		$CI =& get_instance();
		$output = $CI->lang->line($keyword); 
		
		apply_filters("cms_update_lang_base" , $output , $keyword);
		
		if(empty($output))
			return $keyword;
		else	
			return stripslashes($output);
    }   
	
}

if ( ! function_exists('mlx_get_lang_with_org'))
{
    function mlx_get_lang_with_org($keyword = "",$org_keyword = "")
    { 	
		$CI =& get_instance();
		
		$output = $CI->lang->line($keyword); 
		if(empty($output))
			return $org_keyword;
		else	
			return stripslashes($output);
    }   
	
}

if ( ! function_exists('mlx_get_norm_string'))
{
    function mlx_get_norm_string($keyword = "")
    { 	
		$CI =& get_instance();
		
		$CI->load->library('language_lib');
		
		$keyword = $CI->language_lib->get_normal_string($keyword);
		
		$keyword=preg_replace("@[^A-Za-z0-9\-_.\/]+@i","-",$keyword);
		$keyword=strtolower($keyword);
		return $keyword;
		
    }   
	
}