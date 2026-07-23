<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plugins_lib {
	
    public function __construct($params = array())
    {
        $this->_ci =& get_instance();
    }
    
	public function get_plugin_headers()
    {
		
		$modules_path = APPPATH.'modules/';     
		$modules = scandir($modules_path);

		$plugin_list = array();
		
		
		foreach($modules as $module)
		{
			
			if($module === '.' || $module === '..') continue;
			
			if(is_dir($modules_path) . '/' . $module)
			{
				$plugin_slug = $module;
				
				$header_file_path = $modules_path . $module . '/'.$plugin_slug.'.php';
				
				if(file_exists($header_file_path))
				{
					$plugin_data = read_file($header_file_path);
				
					
					preg_match ('|Plugin Name:(.*)$|mi', $plugin_data, $name);
					preg_match ('|Plugin URI:(.*)$|mi', $plugin_data, $uri);
					preg_match ('|Version:(.*)|i', $plugin_data, $version);
					preg_match ('|Description:(.*)$|mi', $plugin_data, $description);
					preg_match ('|Author:(.*)$|mi', $plugin_data, $author_name);
					preg_match ('|Author URI:(.*)$|mi', $plugin_data, $author_uri);
						
					if (isset($name[1]))
					{
						$plugin_list[$plugin_slug]['plugin_name'] = trim($name[1]);
					}
					
					if (isset($uri[1]))
					{

						$plugin_list[$plugin_slug]['plugin_uri'] = trim($uri[1]);
					}
					
					if (isset($version[1]))
					{
						$plugin_list[$plugin_slug]['plugin_version'] = trim($version[1]);
					}
					
					if (isset($description[1]))
					{
						$plugin_list[$plugin_slug]['plugin_description'] = trim($description[1]);
					}
					
					if (isset($author_name[1]))
					{
						$plugin_list[$plugin_slug]['plugin_author'] = trim($author_name[1]);
					}
					
					if (isset($author_uri[1]))
					{
						$plugin_list[$plugin_slug]['plugin_author_uri'] = trim($author_uri[1]);
					}
				}
			}
		}
		
		return $plugin_list;
		
    }
	
	public function get_plugin_header($plugin)
    {
		
		$modules_path = APPPATH.'modules/';     
		
		$plugin_list = array();
		
		$module = $plugin;
		
		if(is_dir($modules_path) . '/' . $module)
		{
			$plugin_slug = $module;
			
			$header_file_path = $modules_path . $module . '/'.$plugin_slug.'.php';
			
			if(file_exists($header_file_path))
			{
				$plugin_data = read_file($header_file_path);
			
				
				preg_match ('|Plugin Name:(.*)$|mi', $plugin_data, $name);
				preg_match ('|Plugin URI:(.*)$|mi', $plugin_data, $uri);
				preg_match ('|Version:(.*)|i', $plugin_data, $version);
				preg_match ('|Description:(.*)$|mi', $plugin_data, $description);
				preg_match ('|Author:(.*)$|mi', $plugin_data, $author_name);
				preg_match ('|Author URI:(.*)$|mi', $plugin_data, $author_uri);
					
				if (isset($name[1]))
				{
					$plugin_list['plugin_name'] = trim($name[1]);
				}
				
				if (isset($uri[1]))
				{

					$plugin_list['plugin_uri'] = trim($uri[1]);
				}
				
				if (isset($version[1]))
				{
					$plugin_list['plugin_version'] = trim($version[1]);
				}
				
				if (isset($description[1]))
				{
					$plugin_list['plugin_description'] = trim($description[1]);
				}
				
				if (isset($author_name[1]))
				{
					$plugin_list['plugin_author'] = trim($author_name[1]);
				}
				
				if (isset($author_uri[1]))
				{
					$plugin_list['plugin_author_uri'] = trim($author_uri[1]);
				}
			}
		}
		
		return $plugin_list;
		
    }

	public function get_plugin_header_from_json()
	{
		$plugin_meta = array();
		
		$modules_path = APPPATH.'modules/';
		if (file_exists($modules_path."/modules.json"))
		{
			$content = file_get_contents($modules_path."/modules.json");
			$plugin_meta = json_decode($content, true);
		}
		else
		{
			$CI =& get_instance();
			$CI->load->library('Global_lib');
			$site_modules = get_option('site_modules');
			if(!empty($site_modules))
				$plugin_meta = json_decode($site_modules, true);
			else
			{	$plugin_meta = $this->get_plugin_headers();	}
		}
		
		
			
		return $plugin_meta;
	}
	
}

