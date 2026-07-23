<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Seometa_lib {

	var $site_contents ;

	

	public function Index(){}
	
	
	public function get_metadata($page = "")	{
		
		$CI =& get_instance();
		$key = 'seo_static_page_details';
		$seo_static_page_details = $CI->global_lib->get_option($key);
		
		
		
		if(isset($_SESSION['default_lang_front']) && !empty($seo_static_page_details))
		{
			$current_lang =   $_SESSION['default_lang_front'];
			
			$lang_exp = explode('~',$current_lang);
			$lang_code = $lang_exp[1];
			$lang_title = $lang_exp[0];
			
			$lang_slug = $CI->global_lib->get_slug($lang_title,'_');
			$language = $lang_slug."_TLD_".$lang_code;
			
			$seo_static_page_details = json_decode($seo_static_page_details,true);
			//print_r($seo_static_page_details);
			if(is_array($seo_static_page_details) && array_key_exists($language , $seo_static_page_details))
			{
				/*print_r($seo_static_page_details[$language]);*/
				$seo_metadata = $seo_static_page_details[$language];
				
				if(	!empty($page)  && 
					is_array($seo_metadata) && 
					array_key_exists($page , $seo_metadata))
				{
					$metadata = $seo_metadata[$page];
					/*print_r($metadata);*/
					$keywords = $description = "";
					if(is_array($metadata) && isset($metadata['meta_keywords'])  && !empty($metadata['meta_keywords']))
					{
						$keywords = $metadata['meta_keywords'];
						echo 	'<meta name="keywords" content="'.$keywords.'">';
					}else{
						echo 	'<meta name="keywords" content="">';
					}
					
					if(is_array($metadata) && isset($metadata['meta_description']) && !empty($metadata['meta_description']))
					{
						$description = $metadata['meta_description'];
						echo 	'<meta name="description" content="'.$description.'">';			
					}else{
						echo 	'<meta name="description" content="">';			
					}
					
								
					
					
				}else{
					echo 	'<meta name="keywords" content="">';
					echo 	'<meta name="description" content="">';			
				}		
			}else{
					echo 	'<meta name="keywords" content="">';
					echo 	'<meta name="description" content="">';			
				}
		}else{
					echo 	'<meta name="keywords" content="">';
					echo 	'<meta name="description" content="">';			
				}
		
		
		
	}	
	
	public function get_all_contents($content_type = "")	{
		
		$CI =& get_instance();
		
		if(empty($this->site_contents))
		{
			$sql  = "select * from contents";
			$contents_list = $CI->Common_model->commonQuery($sql);			
			
			$contents = array();
			if(isset($contents_list) && $contents_list->num_rows()>0)
			{
			  foreach($contents_list->result() as $row)
			  {
				//print_r($row);
				$row_arr  = array();
				$row_arr['content_id'] = $row->content_id ; 
				$row_arr['content_type'] = $row->content_type ; 
				$row_arr['meta_content'] = $row->meta_content ; 
				$row_arr['content_status'] = $row->content_status ; 
				
				$contents[$row->content_type] [$row->content_id] = $row_arr;
					
			  }
			}
			$this->site_contents = $contents;
		}
		
		/*print_r($this->site_contents );*/
		
		return 		$this->site_contents ;
		
	}
	
	// Update Get Option
	public function get_content($content_type = "")	{
		
		$return_content = "";
		/*if(!empty($content_type))
		{
			$options = $this->get_options();
			if(array_key_exists($option,$options))
				$result = $options[$option];
		}*/
		if(!is_array($this->site_contents))
		{
			$this->get_all_contents();
		}
		
		if(array_key_exists($content_type,$this->site_contents))
			$return_content = $this->site_contents[$content_type];
		
		return 	$return_content ;
	}
	
	public function get_sub_contents($content_type = "" , $sub_content_type = "", $content_for = "")	{
	
		$return_content = array();
		$content_data = $sub_content_data = "";
		
		if(!is_array($this->site_contents))
		{
			$this->get_all_contents();
		}
		
		if(array_key_exists($content_type,$this->site_contents))
			$content_data = $this->site_contents[$content_type];
			
		if(array_key_exists($sub_content_type,$this->site_contents))
			$sub_content_data = $this->site_contents[$sub_content_type];	
		
		
		
		$main_content_id = "";
		
		foreach($content_data as $k => $dv){
			$data = json_decode($dv['meta_content'],true);
			if(array_key_exists($content_type."_for",$data) && $data[$content_type."_for"] == $content_for)
			{
				$main_content_id = $k;	break;
			}
		}
		
		
		foreach($sub_content_data as $k => $dv){
			$data = json_decode($dv['meta_content'],true);
			if(array_key_exists("select_".$content_type ,$data) && $data["select_slider"] == $main_content_id)
			{
				$return_content [] = $dv;	//break;
			}
		}	
		
		/*echo "<pre>"; 
		echo $main_content_id;
		print_r($content_data); 
		print_r($sub_content_data); 
		print_r($return_content); 
		echo "</pre>"; */
		
		return $return_content;
		
	}
	
}

/* End of file Myhelpers.php */