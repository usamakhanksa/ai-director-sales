<?php



include_once(APPPATH."cms_includes/includes/cms_property_functions.php");
include_once(APPPATH."cms_includes/includes/cms_user_functions.php");
include_once(APPPATH."cms_includes/includes/cms_module_functions.php");
include_once(APPPATH."cms_includes/includes/cms_pricing_functions.php");

include_once(APPPATH."cms_includes/cms_cache_functions.php");

function cms_file_get_contents($url){
	
	$arrContextOptions=array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	);  

	$response = file_get_contents($url, false, stream_context_create($arrContextOptions));

	return $response;
}


if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}



function cms_file_exists($file_path = '')
{

	

	if ($file_path != '') {
		if (file_exists((APPPATH . "views" . "/" . $file_path . ".php"))) {
			return true;
		}
		
		
		if (file_exists((APPPATH . "modules" . "/" . $file_path . ".php"))) {
			return true;
		}
		if (file_exists(($file_path . ".php"))) {
			return true;
		}
	}
	return false;
}



function EncryptClientID($id)
{
	return substr(md5($id), 0, 8) . dechex($id);
}

function DecryptClientID($id)
{
	$md5_8 = substr($id, 0, 8);
	$real_id = hexdec(substr($id, 8));
	return ($md5_8 == substr(md5($real_id), 0, 8)) ? $real_id : 0;
}

global $ik;
$ik =0;

function clean_post_array($post_var )
{
	global $ik;
	$ik++;
	
/*	echo "<pre> middle  $ik " ; print_r($post_var); echo "</pre>"; */
	$return = array();
	foreach($post_var as $k=>$v)
	{
		if(is_array($post_var[$k])){
			$return[$k] = clean_post_array($post_var[$k] );	
		}else{
			$return [$k] =  str_replace('[removed]', '', $post_var[$k]);
		}
		
	}
	return $return;
}

function clean_post()
{
	
	$CI = &get_instance();
	
	foreach ($_POST as $k => $v) 
	{
		/*$_POST[$k] = $CI->security->xss_clean($v);*/
		/*$_POST[$k] = html_escape($_POST[$k]);
		$_POST[$k] = strip_tags($_POST[$k]);*/
		
		/*$_POST[$k] = preg_replace('/<[^<|>]+?>/', '', htmlspecialchars_decode($_POST[$k]));
		$_POST[$k] = htmlentities($_POST[$k], ENT_QUOTES, "UTF-8");*/
		
		

		if(!is_array($_POST[$k]))
		{
		 	$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
		}
		else
		{
			$_POST[$k] = clean_post_array($_POST[$k] );
		}
	}
}


function get_skin_class()
	{
		$CI =& get_instance();
		
		
		if(property_exists($CI,"skin_class"))
			return $CI->skin_class;
		
		$skin_default = 'skin-blue';
		$skin_class = 'primary';
		$skin = get_option('skin');
		if(!empty($skin))
			$skin_default = $skin;
		
		if($skin_default == 'skin-blue' || $skin_default == 'skin-blue-light')
		{
			$skin_class = 'primary';
		}
		else if($skin_default == 'skin-black' || $skin_default == 'skin-black-light')
		{
			$skin_class = 'default';
		}
		else if($skin_default == 'skin-purple' || $skin_default == 'skin-purple-light')
		{
			$skin_class = 'purple';
		}
		else if($skin_default == 'skin-green' || $skin_default == 'skin-green-light')
		{
			$skin_class = 'success';
		}
		else if($skin_default == 'skin-red' || $skin_default == 'skin-red-light')
		{
			$skin_class = 'danger';
		}
		else if($skin_default == 'skin-yellow' || $skin_default == 'skin-yellow-light')
		{
			$skin_class = 'warning';
		}
		$CI->skin_class = $skin_class;
		return $CI->skin_class;
	}

	function getToken($length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $max = strlen($codeAlphabet);
    
        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[rand(0, $max-1)];
        }
    
        return $token;
	}
	
	
	function base64url_encode($bin) {
		return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($bin));
	}

	function base64url_decode($str) {
		return base64_decode(str_replace(['-', '_'], ['+', '/'], $str));
	}

	function get_include_contents($filename)
	{
		
		if (is_file($filename)) {
			ob_start(); 
			include $filename;
			return ob_get_clean();
		}
		return false;
	}
	function strtoarray($a, $t = '')
	{

		$arr = array();
		$a = str_replace('$lang[\'', '', $a);

		$tempArr = explode(";", $a);
		foreach ($tempArr as $k => $v) {
			$tempArr2 = explode("'] = ", $v);
			if (count($tempArr2) > 1)
				$arr[trim($tempArr2[0])] = trim(str_replace("'", "", $tempArr2[1]));
		}
		
		return $arr;
	}

	

	function get_date_from_timestamp($retun_type = null, $date = null)
	{
		$CI =& get_instance();
		$default_date_format = get_option('default_date_format');
		if(empty($default_date_format) || $default_date_format == '')
		{
			$default_date_format = 'mm/dd/yyyy';
		}
		
		$default_date = '';
		if($default_date_format == 'mm/dd/yyyy')
		{
			if($retun_type != null && $retun_type == 'start_date')
			{
				if($date != null)
				{
					$default_date = date('m/04/Y',$date); 
				}
				else
				{
					$default_date = date('m/04/Y',time()); 
				}
			}
			else if($retun_type != null && $retun_type == 'end_date')
			{
				if($date != null)
				{
					$default_date = date('m/03/Y',$date); 
				}
				else
				{
					$default_date = date('m/03/Y',time()); 
				}
			}
			else
			{
				if($date == null)
				{
					$default_date = date('m/d/Y',time());
				}
				else
				{
					$default_date = date('m/d/Y',$date);
				}
			}
		}
		else if($default_date_format == 'dd/mm/yyyy')
		{
			
			if($retun_type != null && $retun_type == 'start_date')
			{
				
				if($date != null)
				{
					$default_date = date('04/m/Y',$date); 
				}
				else
				{
					$default_date = date('04/m/Y',time()); 
				}
			}
			else if($retun_type != null && $retun_type == 'end_date')
			{
				
				if($date != null)
				{
					$default_date = date('03/m/Y',$date); 
				}
				else
				{
					$default_date = date('03/m/Y',time()); 
				}
			}
			else
			{
				if($date == null)
				{
					$default_date = date('d/m/Y',time());
				}
				else
				{
					$default_date = date('d/m/Y',$date);
				}
			}
		}
		return $default_date;
	}

	function get_timestamp_from_date($date,$type = 'start')
	{
		$CI =& get_instance();
		$default_date_format = get_option('default_date_format');
		if(empty($default_date_format) || $default_date_format == '')
		{
			$default_date_format = 'mm/dd/yyyy';
		}
		$date_timestamp = '';
		if($default_date_format == 'mm/dd/yyyy')
		{
			$date_explode = explode('/',$date);
			if($type == 'end')
				$date_timestamp = mktime(23,59,59,$date_explode[0],$date_explode[1],$date_explode[2]);
			else
				$date_timestamp = mktime(0,0,0,$date_explode[0],$date_explode[1],$date_explode[2]);
			
		}
		else if($default_date_format == 'dd/mm/yyyy')
		{
			$date_explode = explode('/',$date);
			if($type == 'end')
				$date_timestamp = mktime(23,59,59,$date_explode[1],$date_explode[0],$date_explode[2]);
			else
				$date_timestamp = mktime(0,0,0,$date_explode[1],$date_explode[0],$date_explode[2]);
		}
		return $date_timestamp;
	}
	
	
	
	function get_default_date($retun_type = null, $date = null)
	{
		$CI =& get_instance();
		$default_date_format = get_option('default_date_format');
		if(empty($default_date_format) || $default_date_format == '')
		{
			$default_date_format = 'mm/dd/yyyy';
		}
		
		$default_date = '';
		if($default_date_format == 'mm/dd/yyyy')
		{
			if($retun_type != null && $retun_type == 'start_date')
			{
				if($date != null)
				{
					$default_date = date('m/d/Y',$date); 
				}
				else
				{
					$default_date = date('m/d/Y',time()); 
				}
			}
			else if($retun_type != null && $retun_type == 'end_date')
			{
				if($date != null)
				{
					$default_date = date('m/d/Y',$date); 
				}
				else
				{
					$default_date = date('m/d/Y',time()); 
				}
			}
			else
			{
				if($date == null)
				{
					$default_date = date('m/d/Y',time());
				}
				else
				{
					$default_date = date('m/d/Y',$date);
				}
			}
		}
		else if($default_date_format == 'dd/mm/yyyy')
		{
			
			if($retun_type != null && $retun_type == 'start_date')
			{
				
				if($date != null)
				{
					$default_date = date('d/m/Y',$date); 
				}
				else
				{
					$default_date = date('d/m/Y',time()); 
				}
			}
			else if($retun_type != null && $retun_type == 'end_date')
			{
				
				if($date != null)
				{
					$default_date = date('d/m/Y',$date); 
				}
				else
				{
					$default_date = date('d/m/Y',time()); 
				}
			}
			else
			{
				if($date == null)
				{
					$default_date = date('d/m/Y',time());
				}
				else
				{
					$default_date = date('d/m/Y',$date);
				}
			}
		}
		return $default_date;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_page_slug_by_id($page_id)	
	{				
		$CI =& get_instance();		
		$enc_id = DecryptClientID($page_id);
		$query = $CI->Common_model->commonQuery("select page_slug from pages where page_id = '$enc_id' ");							
		if($query->num_rows() > 0)		
		{			
			$row=$query->row();			
			$page_slug=$row->page_slug;			
			return $page_slug;		
		}				
		else		
		{			
			return false;		
		}					
	}
	
	function truncate_string($string, $length, $stopanywhere=false) {
		
		$words = explode(" ",$string);
		if(count($words) > $length)
		{
			return implode(" ", array_splice($words, 0, $length)).'...';
		}
		return $string;
	}
	
	
	
	function relativeTime($time, $short = false){
		$SECOND = 1;
		$MINUTE = 60 * $SECOND;
		$HOUR = 60 * $MINUTE;
		$DAY = 24 * $HOUR;
		$MONTH = 30 * $DAY;
		$before = time() - $time;

		if ($before < 0)
		{
			return mlx_get_lang("Not Yet");
		}

		if ($short){
			if ($before < 1 * $MINUTE)
			{
				return ($before <5) ? mlx_get_lang("Just Now") : $before . mlx_get_lang(" Ago");
			}

			if ($before < 2 * $MINUTE)
			{
				return mlx_get_lang("1 Min Ago");
			}

			if ($before < 45 * $MINUTE)
			{
				return floor($before / 60) . " ".mlx_get_lang("Min Ago");
			}

			if ($before < 90 * $MINUTE)
			{
				return mlx_get_lang("1 Hour Ago");
			}

			if ($before < 24 * $HOUR)
			{

				return floor($before / 60 / 60). " ".mlx_get_lang("Hour Ago");
			}

			if ($before < 48 * $HOUR)
			{
				return mlx_get_lang("1 Day Ago");
			}

			if ($before < 30 * $DAY)
			{
				return floor($before / 60 / 60 / 24) . " ".mlx_get_lang('Day Ago');
			}


			if ($before < 12 * $MONTH)
			{
				$months = floor($before / 60 / 60 / 24 / 30);
				return $months <= 1 ? mlx_get_lang("1 Month Ago") : $months . " ".mlx_get_lang("Month Ago");
			}
			else
			{
				$years = floor  ($before / 60 / 60 / 24 / 30 / 12);
				return $years <= 1 ? mlx_get_lang("1 Year Ago") : $years." ".mlx_get_lang("Year Ago");
			}
		}

		if ($before < 1 * $MINUTE)
		{
			return ($before <= 1) ? mlx_get_lang("Just Now") : $before . " ".mlx_get_lang("Seconds Ago");
		}

		if ($before < 2 * $MINUTE)
		{
			return mlx_get_lang("A Minute Ago");
		}

		if ($before < 45 * $MINUTE)
		{
		    return floor($before / 60) . " ".mlx_get_lang("Minutes Ago");
		}

		if ($before < 90 * $MINUTE)
		{
			return mlx_get_lang("An Hour Ago");
		}

		if ($before < 24 * $HOUR)
		{

			return (floor($before / 60 / 60) == 1 ? mlx_get_lang('About an Hour') : floor($before / 60 / 60).' '.mlx_get_lang('Hours')). " ".mlx_get_lang("Ago");
		}

		if ($before < 48 * $HOUR)
		{
			return mlx_get_lang("Yesterday");
		}

		if ($before < 30 * $DAY)
		{
			return floor($before / 60 / 60 / 24) . " ".mlx_get_lang("Days Ago");
		}

		if ($before < 12 * $MONTH)
		{

			$months = floor($before / 60 / 60 / 24 / 30);
			return $months <= 1 ? mlx_get_lang("One Month Ago") : $months . " ".mlx_get_lang("Months Ago");
		}
		else
		{
			$years = floor  ($before / 60 / 60 / 24 / 30 / 12);
			return $years <= 1 ? mlx_get_lang("One Year Ago") : $years." ".mlx_get_lang("Years Ago");
		}

		return "$time";
	}
	
	
	function generate_random_string($length = 20)
	{
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));

		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}

		return $key;
	}

	function sortArrayByArray($array,$orderArray) {
		$ordered = array();
		foreach($orderArray as $key => $value) {
			if(array_key_exists($key,$array)) {
					$ordered[$key] = $array[$key];
					unset($array[$key]);
			}
		}
		return $ordered + $array;
	}