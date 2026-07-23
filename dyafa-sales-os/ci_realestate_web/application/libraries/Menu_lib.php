<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_lib {

	var $menu_items = array(); 

	

	public function __construct(){
	
		$this->menu_items ['home'] =  base_url(array('home',':lang',''));
		$this->menu_items ['property_for_sale'] =  site_url(array('search',':lang','property-for-sale')); 
		$this->menu_items ['property_for_rent'] =  site_url(array('search',':lang','property-for-rent')); 
		$this->menu_items ['property'] =  site_url(array('property',':lang',''));
		$this->menu_items ['agents'] =  site_url(array('search',':lang','agents'));
		$this->menu_items ['contact'] =  site_url(array('contact',':lang',''));
		$this->menu_items ['search'] =  site_url(array('search',':lang',''));
		$this->menu_items ['projects'] =  site_url(array('projects',':lang',''));
		$this->menu_items ['register'] =  site_url(array('register',':lang',''));
		$this->menu_items ['blogs'] =  site_url(array('blogs',':lang',''));
		$this->menu_items ['blog'] =  site_url(array('blog',':lang',''));
	}
	
	public function get_link_url($menu_item = '' , $menu_link = ''){
		
		$CI =& get_instance();
		
		$menu_item = str_replace("& ","",$menu_item);
		$menu_item = str_replace(" ","-",$menu_item);
		
		
		
		/*if(array_key_exists($menu_item,$this->menu_items))
		{	$return_menu_item =  $this->menu_items[$menu_item];
			
			
			if( isset($CI->enable_multi_lang ) && $CI->enable_multi_lang  != true  && $menu_item = 'home')
			{	
				$return_menu_item = str_replace("home/","",$return_menu_item);
				  	
				
			}
			
			
		}else*/ 
		$return_menu_item =  site_url($menu_link);
			
			
		if( isset($CI->enable_multi_lang ) && $CI->enable_multi_lang  != true  && $menu_item = 'home')
		{	
			$return_menu_item = str_replace("home/","",$return_menu_item);
		}
		$return_menu_item  =  $this->remove_lang_from_url($return_menu_item);
		return $return_menu_item ;
		
		
		/*if(preg_match("/type=/", $menu_item))
		{
			$return_menu_item = str_replace("type=","",$menu_item);
			$return_menu_item = site_url(array('search',':lang','property-type-'.$return_menu_item));
			
		}
		else if(preg_match("/page=/", $menu_item))
		{
			$return_menu_item = str_replace("page=","",$menu_item);
			$return_menu_item = site_url(array(':lang',$return_menu_item));
			
		}	
		else	
			$return_menu_item  = base_url();
		
		
		
		$return_menu_item  =  $this->remove_lang_from_url($return_menu_item);
		
		return $return_menu_item ;*/
		
	}	
	
	public function get_url($menu_item = ''){
		
		$CI =& get_instance();
		
		$menu_item = str_replace("& ","",$menu_item);
		$menu_item = str_replace(" ","-",$menu_item);
		
		
		
		if(array_key_exists($menu_item,$this->menu_items))
		{	$return_menu_item =  $this->menu_items[$menu_item];
			
			
			if( isset($CI->enable_multi_lang ) && $CI->enable_multi_lang  != true  && $menu_item = 'home')
			{	
				$return_menu_item = str_replace("home/","",$return_menu_item);
				  	
				
			}
			
			
		}else if(preg_match("/type=/", $menu_item))
		{
			$return_menu_item = str_replace("type=","",$menu_item);
			$return_menu_item = site_url(array('search',':lang','property-type-'.$return_menu_item));
			
		}
		else if(preg_match("/page=/", $menu_item))
		{
			$return_menu_item = str_replace("page=","",$menu_item);
			$return_menu_item = site_url(array(':lang',$return_menu_item));
			
		}	
		else	
			$return_menu_item  = base_url();
		
		
		
		$return_menu_item  =  $this->remove_lang_from_url($return_menu_item);
		
		return $return_menu_item ;
		
	}	
	
	public function remove_lang_from_url($menu_item){
	
		$CI =& get_instance();
		$return_menu_item  =  "";
		
		if(isset($CI->enable_multi_lang ) && $CI->enable_multi_lang  == true )
		{	$lang = '';
			
			/*$lang = $CI->default_language;	*/
			$lang = $CI->default_lang_code_small;	
			$return_menu_item  =  str_replace(":lang",$lang,$menu_item  );	
		
		}else{
			$return_menu_item  =  str_replace("/:lang/","",$menu_item  );
			 $return_menu_item  =  str_replace("/:lang","",$menu_item  );
		}
		
		
		
		return $return_menu_item ;
	
	}
	
	public function get_menus($menu_location = NULL)
	{
		$CI =& get_instance();
		$CI->load->library('Global_lib');
		$data = $CI->global_lib->uri_check_front();
		extract($data);
		$sql = "select * from property_types where status = 'Y' order by title";
		$property_type_list = $CI->Common_model->commonQuery($sql );

		if($menu_location != NULL && get_option($menu_location))
		{
			$menu_meta = get_option($menu_location);
		}

		$menu_list = array();
		if(isset($menu_meta) && !empty($menu_meta)) 
		{
			$menu_meta = json_decode($menu_meta,true);
			
			foreach($menu_meta as $hmk=>$hmv)
			{
				$p_url = '#';
				$menu_id_exp = explode('~',$hmv['id']);
				$menu_type = $menu_id_exp[0];
				$menu_slug = $menu_id_exp[1];
				$active_class = '';
				if($menu_type == 'static')
				{
					if($menu_slug == 'homepage')
					{
						$menu_slug = 'home';
						if($class == 'home' && $func == 'home')
						{
							$active_class = 'active'; 
						}
					}
					else if($menu_slug == 'property-for-sale')
					{
						if($class == 'main' && $func == $menu_slug)
						{
							$active_class = 'active'; 
						}
						$menu_slug = 'property_for_sale';
						
					}
					else if($menu_slug == 'property-for-rent')
					{
						if($class == 'main' && $func == $menu_slug)
						{
							$active_class = 'active'; 
						}
						$menu_slug = 'property_for_rent';
					}
					else if($menu_slug == 'blog')
					{
						$isBlogAct = $CI->isPluginActive('blog');
						if($isBlogAct != true)
						{
								continue;
						}
						if($class == 'main' && $func == $menu_slug)
						{
							$active_class = 'active'; 
						}
						$menu_slug = 'blogs';
					}
					
					$p_url = $CI->menu_lib->get_url($menu_slug);
					
					
					if($menu_slug == 'all_properties')
					{
						$menu_slug = 'property';
						/*$p_url = base_url($menu_slug);*/
						$p_url = $CI->menu_lib->get_url($menu_slug);
						if($class == 'main' && $func == $menu_slug)
							{
								$active_class = 'active'; 
							}
					}
					
				}
				else if($menu_type == 'page')
				{
					
					$page_slug = $CI->global_lib->get_page_slug_by_id($menu_slug);
					$p_url = $CI->menu_lib->get_url('page='.$page_slug); 
				}
				else if($menu_type == 'custom_link')
				{
					$p_url = $menu_slug; 
				}
				
				$menu_list[] = array(
					'class' => $active_class,
					'link_url' => $p_url,
					'title' => mlx_get_lang($hmv['name'])
				);
			
			}
		
		}
		else
		{
		
		
		
		} 
		return $menu_list;
	}
}
