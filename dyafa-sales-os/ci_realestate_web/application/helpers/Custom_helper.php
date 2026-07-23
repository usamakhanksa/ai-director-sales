<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Global_helper {

	public function Index(){}
	
	public function uri_check()
	{
		
		$str=uri_string();
		$strs=explode("/",$str);
		$data['class']='main';
		if( isset($strs[1]))   
		{
			$data['func']=$strs[1]; 
			switch ($strs[1])
			{
				case 'home': 
				$data['class']='home';break;
				
				case 'classifieds': 
				$data['class']='classifieds';break;
				
				case 'classified': 
				$data['class']='classifieds';break;
				
				case 'user_classified': 
				$data['class']='classifieds';break;
				
				case 'articles': 
				$data['class']='articles';break;
				
				case 'view_article': 
				$data['class']='articles';break;
				
				case 'search_classified': 
				$data['class']='classifieds';break;
				
				case 'category' : 
					if(isset($strs[2]))
					{
						switch($strs[2])
						{
							case 'classified' :
							$data['class']='classifieds';break;
							
							case 'article' :
							$data['class']='articles';break;
						}
					
					}
				case 'user_data' : 
					if(isset($strs[2]))
					{
						switch($strs[2])
						{
							case 'classified' :
							$data['class']='classifieds';break;
							
							case 'article' :
							$data['class']='articles';break;
						}
					
					}	
			}	
		}
		else {
			$data['func']='home';
			$data['class']='home';
		}	
			
		
		
		//$data1=$this->getSEOfields();
		//$data = array_merge($data,$data1);
		
		return $data;
	}	

	

}

/* End of file Myhelpers.php */