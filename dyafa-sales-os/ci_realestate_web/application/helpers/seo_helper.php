<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('seo_fields'))
{
    /*function test_method($var = '')
    {
        return $var;
    }*/   
	
	function seo_fields(){
		$CI =& get_instance();
		
		$p = $CI->get_the_page_context() ;
		$c = $CI->get_the_cat_context() ;
		
		if("default" != $p  )//&& "default" == $c)	
		{
			
			$id = $CI->get_the_ID();
			$seo_meta_description = $CI->global->get_post_meta($id,"seo_meta_description");
		}
		?>
	<!-- Description, Keywords and Author -->
		<meta name="description" content="<?php if(isset($seo_meta_description) && !empty($seo_meta_description)) echo $seo_meta_description.' | '; ?>">
		<meta name="keywords" content="<?php if(isset($seo_meta_keywords) && !empty($seo_meta_keywords)) echo $seo_meta_keywords.' | '; ?>">
		<meta name="author" content="">	
		<?php
		
		
	}
	
	
}