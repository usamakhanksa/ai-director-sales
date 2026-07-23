<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_custom_field'))
{
    function get_custom_field($keyword = "",$property = "")
    { 	
		$CI =& get_instance();
		
		if( isset($property->$keyword))
		{
			return $property->$keyword;
		}
    }   
}