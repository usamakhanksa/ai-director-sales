<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bloglib {

	var $url = "";

	

	public function Index(){}
	
	
	
	public function get_url($blog)	{
		$CI =& get_instance();
			
		$return_menu_item = base_url('blog/:lang/'.$blog->slug);		
		
		if(isset($CI->enable_multi_lang ) && $CI->enable_multi_lang  == true)
		{
			$lang = $CI->default_lang_code_small;	
			$return_menu_item  =  str_replace(":lang",$lang,$return_menu_item  );	
		
		}else{
			$return_menu_item  =  str_replace("/:lang","",$return_menu_item  );
		}
		
		
		
		return $return_menu_item ;
		
				
	}
	
	public function get_cat_url($blog)	{
		$CI =& get_instance();
			
		$return_menu_item = base_url('blog/category/:lang/'.$blog->cat_slug);		
		
		if(isset($CI->enable_multi_lang ) && $CI->enable_multi_lang  == true)
		{
			$lang = $CI->default_lang_code_small;	 
			$return_menu_item  =  str_replace(":lang",$lang,$return_menu_item  );	
		
		}else{
			$return_menu_item  =  str_replace("/:lang","",$return_menu_item  );
		}
		
		
		
		return $return_menu_item ;
		
				
	}
}
