<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('front_url'))
{
    function front_url($args = "")
    { 	
		$furl = site_url();
		
		$furl .= $args; 
		return $furl;	
			
    }   
}

if ( ! function_exists('admin_url'))
{
    function admin_url($args = "")
    { 	
		$furl = site_url("admin/");
		$furl .= $args; 
		return $furl;	
			
    }   
}
