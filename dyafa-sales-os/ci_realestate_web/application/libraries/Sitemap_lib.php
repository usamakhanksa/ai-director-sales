<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sitemap_lib {


	public function save_sitemaps($args)	{
		extract($args);
		
		$CI =& get_instance();
		$site_language = $CI->global_lib->get_option('site_language');
		$site_language_array = json_decode($site_language,true);
		
		$filename_woext = str_replace(".xml","",$filename);
		/*echo "<pre>"; print_r($site_language_array); exit;	*/
		
		/**
		Array
        (
            [language] => English~en
            [currency] => INR
            [direction] => ltr
            [timezone] => Asia/Kolkata
            [status] => enable
        )

    	Array
        (
            [language] => Arabic~ar
            [currency] => AED
            [direction] => rtl
            [timezone] => Pacific/Apia
            [status] => enable
        )

    	Array
        (
            [language] => German~de
            [currency] => BRL
            [direction] => ltr
            [timezone] => America/Yakutat
            [status] => enable
        )
		*/		
		$xmlString = '';

		$xmlString .= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 
		
		$xmlString .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		foreach($site_language_array as $k => $v)
		{
			$lang_exp = explode('~',$v['language']);
			$lang_name = $lang_exp[0];
			/*$lang_code = $lang_exp[1];	*/
			
			
			$lang_code_combi = $lang_exp[1];
			$lang_code_exp = explode('-',$lang_code_combi);
			if(isset($lang_code_exp[1]))
			{
				$lang_code = strtolower( $lang_code_exp[1]);
				
			}else
				$lang_code = strtolower($lang_code_exp[0]);	
			
			$lastmod = date("Y-m-d");
			/*$new_filename = $filename_woext."_".$lang_code.".xml";*/
			$new_filename = $filename_woext."_".$lang_code_combi.".xml";
			$xmlString .= "<sitemap>" . PHP_EOL;
			$sitemap_url = front_url().$new_filename;
			$xmlString .= "<loc>$sitemap_url </loc>" . PHP_EOL;	
			$xmlString .= "<lastmod>$lastmod</lastmod>" . PHP_EOL;	
			$xmlString .= "</sitemap>" . PHP_EOL;
			
			
			$sitemap_filename = "../".$new_filename;	
			$args = array("front_url" => $front_url , 
							"filename" => $sitemap_filename , 
							"multi_lang" => 'Y' ,
							"lang_code" => $lang_code,
							"language" => $lang_code_combi
							);
							
							
			$this->save_sitemap($args);
			
		}
		
		//print_r($args); exit;
			/*<sitemap>
				<loc>http://website.net/sitemap_fr.xml</loc>
				<lastmod>2004-10-01</lastmod>
			</sitemap>
			<sitemap>
				<loc>http://website.net/sitemap_en.xml</loc>
				<lastmod>2005-01-01</lastmod>
			</sitemap>
			<sitemap>
				<loc>http://website.net/sitemap_es.xml</loc>
				<lastmod>2005-01-01</lastmod>
			</sitemap>*/
		$xmlString .= '</sitemapindex>';
		
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($xmlString);
		
		$download_filename = "../". $filename_woext."_index.xml";
		$dom->save($download_filename);
	}


	public function save_sitemap($args)	{
	
		$CI =& get_instance();
		extract($args);
		
		$xmlString = '';

		$xmlString .= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 
		
		$xmlString .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;
		
		
		if(isset($multi_lang) && $multi_lang == 'Y')
			$home_url = $front_url . "home/".$lang_code;
		else
			$home_url = $front_url;	
		
		
		
		$xmlString .= "<url>" . PHP_EOL;
		$xmlString .= "<loc>$home_url</loc>" . PHP_EOL;
		$xmlString .= "<changefreq>daily</changefreq>" . PHP_EOL;
		$xmlString .= "</url>" . PHP_EOL;
		
		
		$other_urls = array("search/property-for-sale","search/property-for-rent",);
		
		foreach($other_urls as $other_url)
		{
			
			if(isset($multi_lang) && $multi_lang == 'Y')
			{
				$url = explode("/",$other_url);
				$new_url = $front_url . $url[0]. "/".$lang_code."/".$url[1]."/";
			}else
				$new_url = $front_url . $other_url."/";	
			
			
			$xmlString .= "<url>" . PHP_EOL;
			$xmlString .= "<loc>$new_url</loc>" . PHP_EOL;
			$xmlString .= "<changefreq>daily</changefreq>" . PHP_EOL;
			$xmlString .= "</url>" . PHP_EOL;
			
		
		}
		
		/**
		"
						SELECT * FROM `properties` as prop 
						where prop.status = 'Y' and  prop.cur_status = 'publish'
						order by prop.p_id DESC"
		*/
		
		if(!isset($language))
			$language = $this->default_language;
		
				
		
		$sql = "select prop.* from properties as prop 
		inner join property_lang_details as pld1 on pld1.p_id = prop.p_id and pld1.language = '$language'
		inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
		where prop.status = 'publish' group by prop.p_id order by prop.p_id DESC";
		
		$properties = $CI->Common_model->commonQuery($sql);	
						
		if ($properties->num_rows() > 0)
		{				
			foreach ($properties->result() as $row)
			{ 				
				
				if(isset($multi_lang) && $multi_lang == 'Y')
					$segments = array('property',$lang_code,$row->slug."~".$row->p_id); 
				else
					$segments = array('property',$row->slug."~".$row->p_id); 
						
				$prop_permalink =  str_replace("/admin","",site_url($segments));
			
				$xmlString .= "<url>" . PHP_EOL;
				$xmlString .= "<loc>$prop_permalink/</loc>" . PHP_EOL;
				$xmlString .= "<changefreq>daily</changefreq>" . PHP_EOL;
				$xmlString .= "</url>" . PHP_EOL;
			}
		}	
		
		$xmlString .= "</urlset>" . PHP_EOL;
		
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($xmlString);
		
		$dom->save($filename);
	}

	
	public function download_sitemaps($args){
	
		extract($args);
		$xmlString = "";	
		
		$CI =& get_instance();
		$CI->load->library('zip');
		
		
		//$CI->zip->download('sitemaps.zip');
		
		
		if(isset($multi_lang) && $multi_lang == 'Y')
		{
		
			$site_language = $CI->global_lib->get_option('site_language');
			$site_language_array = json_decode($site_language,true);
			
			$filename_woext = str_replace(".xml","",$filename);
			$xmlString = '';
	
			$xmlString .= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 
			
			$xmlString .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			
			foreach($site_language_array as $k => $v)
			{
				$lang_exp = explode('~',$v['language']);
				$lang_name = $lang_exp[0];
				/*$lang_code = $lang_exp[1];	*/
				
				
				$lang_code_combi = $lang_exp[1];
				$lang_code_exp = explode('-',$lang_code_combi);
				if(isset($lang_code_exp[1]))
				{
					$lang_code = strtolower( $lang_code_exp[1]);
					
				}else
					$lang_code = strtolower($lang_code_exp[0]);	
				
				$lastmod = date("Y-m-d");
				/*$new_filename = $filename_woext."_".$lang_code.".xml";*/
				$new_filename = $filename_woext."_".$lang_code_combi.".xml";
				$xmlString .= "<sitemap>" . PHP_EOL;
				$sitemap_url = front_url().$new_filename;
				$xmlString .= "<loc>$sitemap_url </loc>" . PHP_EOL;	
				$xmlString .= "<lastmod>$lastmod</lastmod>" . PHP_EOL;	
				$xmlString .= "</sitemap>" . PHP_EOL;
				
				
				$sitemap_filename = $new_filename;	
				$args = array("front_url" => $front_url , 
								"filename" => $sitemap_filename , 
								"multi_lang" => 'Y' ,
								"lang_code" => $lang_code,
								"CI" => $CI,
								"language" => $lang_code_combi
								);
								
								
				$this->download_sitemap($args);
				
			}			
			$xmlString .= '</sitemapindex>';
			
			$name = $filename; //'mydata1.txt';
		
			$CI->zip->add_data($name, $xmlString);
			
			
		}else{
			/** not multi lang **/
			$args = array("front_url" => $front_url , 
								"filename" => $filename , 
								"lang_code" => $lang_code,
								"CI" => $CI,
								"language" => $lang_code_combi
								);
								
								
				$this->download_sitemap($args);
				
		
		}	
		
		$CI->zip->download('sitemaps.zip');
	
	}
	
	public function download_sitemap($args){
	
		extract($args);
		$xmlString = "";	
		
		
		
		
		$xmlString .= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 
		
		$xmlString .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;
		
		
		/*$xmlString .= "<url>" . PHP_EOL;
		$xmlString .= "<loc>$front_url</loc>" . PHP_EOL;
		$xmlString .= "<changefreq>daily</changefreq>" . PHP_EOL;
		$xmlString .= "</url>" . PHP_EOL;*/
		
		
		if(isset($multi_lang) && $multi_lang == 'Y')
			$home_url = $front_url . "home/".$lang_code;
		else
			$home_url = $front_url;	
		
		
		
		$xmlString .= "<url>" . PHP_EOL;
		$xmlString .= "<loc>$home_url</loc>" . PHP_EOL;
		$xmlString .= "<changefreq>daily</changefreq>" . PHP_EOL;
		$xmlString .= "</url>" . PHP_EOL;
		
		
		$other_urls = array("search/property-for-sale","search/property-for-rent",);
		
		foreach($other_urls as $other_url)
		{
			
			if(isset($multi_lang) && $multi_lang == 'Y')
			{
				$url = explode("/",$other_url);
				$new_url = $front_url . $url[0]. "/".$lang_code."/".$url[1]."/";
			}else
				$new_url = $front_url . $other_url."/";	
			
			
			$xmlString .= "<url>" . PHP_EOL;
			$xmlString .= "<loc>$new_url</loc>" . PHP_EOL;
			$xmlString .= "<changefreq>daily</changefreq>" . PHP_EOL;
			$xmlString .= "</url>" . PHP_EOL;
			
		
		}
		
		if(!isset($language))
			$language = $this->default_language;
		
		$def_lang_code = $language ; //$this->default_language;
		
		$sql = "select prop.* from properties as prop 
		inner join property_lang_details as pld1 on pld1.p_id = prop.p_id and pld1.language = '$def_lang_code'
		inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
		 where prop.status = 'publish' group by prop.p_id order by prop.p_id DESC";
		
		$properties = $CI->Common_model->commonQuery($sql);	
						
		if ($properties->num_rows() > 0)
		{				
			foreach ($properties->result() as $row)
			{ 				
		
				
				
				if(isset($multi_lang) && $multi_lang == 'Y')
				{
					$segments = array('property',$lang_code,$row->slug."~".$row->p_id); 
				}else{
					$segments = array('property',$row->slug."~".$row->p_id); 	
				}	
				
				$prop_permalink =  str_replace("/admin","",site_url($segments));
			
				$xmlString .= "<url>" . PHP_EOL;
				$xmlString .= "<loc>$prop_permalink/</loc>" . PHP_EOL;
				$xmlString .= "<changefreq>daily</changefreq>" . PHP_EOL;
				$xmlString .= "</url>" . PHP_EOL;
			}
		}	
		
		$xmlString .= "</urlset>" . PHP_EOL;
		
		
		
		$name = $filename; //'mydata1.txt';
		
		$CI->zip->add_data($name, $xmlString);
		
		return $CI;
		
		
		
	}

	
	
}



/* End of file Myhelpers.php */