<?php




function get_user_metadata($user_id = NULL)
{
	$CI = &get_instance();
	if(!is_numeric($user_id)) $user_id = DecryptClientID($user_id);

	$query = $CI->Common_model->commonQuery("select * from user_meta where user_id = '$user_id'");

	if ($query->num_rows() > 0) {
		$metadata = array();
		foreach ($query->result() as $row) {
			$metadata[$row->meta_key] = $row->meta_value;
		}
		$CI->site_users[$user_id] = 		$metadata;
	}
	
	$query = $CI->Common_model->commonQuery("select * from users where user_id = '$user_id'");

	if ($query->num_rows() > 0) {
		$row = $query->row();
		$CI->site_users[$user_id]['user_email'] = $row->user_email;
		$CI->site_users[$user_id]['user_name'] = $row->user_name;
		$CI->site_users[$user_id]['user_type'] = $row->user_type;
		$CI->site_users[$user_id]['user_verified'] = $row->user_verified;
		$CI->site_users[$user_id]['user_status'] = $row->user_status;
		$CI->site_users[$user_id]['user_code'] = $row->user_code;
		$CI->site_users[$user_id]['user_link_id'] = $row->user_link_id;
	}	
}


function get_user_meta($user_id = NULL, $meta_key = NULL)
{
	$CI = &get_instance();
	if(!is_numeric($user_id)) $user_id = DecryptClientID($user_id);
	$user_meta = "";
	if (!isset($CI->site_users[$user_id])) {
		get_user_metadata($user_id);
	}
	if (isset($CI->site_users[$user_id][$meta_key])) {
		$user_meta = $CI->site_users[$user_id][$meta_key];
	}
	return $user_meta;
}

function update_user_meta($user_id, $key, $val)
{
	$CI = &get_instance();
	if (get_user_meta($user_id, $key)) {
		
		
		$CI->Common_model->commonQuery("update user_meta set meta_value ='" . $val . "' where user_id=" . $user_id . " and meta_key='" . $key . "'");
	} else {
		$datai = array('meta_key' => $key,	'meta_value' => $val, 'user_id' => $user_id);
		$mid =  $CI->Common_model->commonInsert('user_meta', $datai);
		$CI->site_users[$user_id][$key] = $val;
	}
}


function getVisitorIP()
{
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];

	if (filter_var($client, FILTER_VALIDATE_IP)) {
		$ip = $client;
	} elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
		$ip = $forward;
	} else {
		$ip = $remote;
	}

	return $ip;
}



function get_site_options()
{

	$CI = &get_instance();
	$site_options = array();
	$query = $CI->Common_model->commonQuery("select *  from options ");
	if ($query->num_rows() > 0) {
		foreach ($query->result() as $row) {
			$site_options[$row->option_key] = $row->option_value;
		}
	}
	$CI->site_options =  $site_options;

}


function get_option($option = "")
{

	$CI = &get_instance();
	if (isset($CI->site_options[$option])) 
		return $CI->site_options[$option];

	return false;
}

function update_option($key, $val)
{
	$CI = &get_instance();
	if (get_option($key)) {
		$datai = array('option_value' => $val);
		$CI->Common_model->commonUpdate('options', $datai, 'option_key', $key);
	} else {
		$datai = array('option_key' => $key,	'option_value' => $val);
		$CI->Common_model->commonInsert('options', $datai);
		
	}
	$CI->site_options[$key] = $val;
}

function get_option_id($key){
	
	
	$CI = &get_instance();
	$query = $CI->Common_model->commonQuery("select *from options where option_key = '$key'");
	 $query->num_rows();
	
	if ($query->num_rows() > 0) {
		$row = $query->row();
		return $row->option_id;
	}
	return false;	
}



function get_option_lang($key, $lang = 'en')
	{
		$CI = &get_instance();

		/*if ($CI->enable_multi_lang) {
			$query = $CI->Common_model->commonQuery("select lang_text from options as opt
			inner join options_lang_details as old on old.opt_id = opt.option_id
			where opt.option_key = '$key' and old.language = '$lang' and old.lang_text != ''");
			if ($query->num_rows() > 0) {
				$row = $query->row();
				return $row->lang_text;
			} else {
				return $this->get_option($key);
			}
		} else {
			return $this->get_option($key);
		}*/
		
		
		if (!isset($CI->site_option_langs) && $CI->enable_multi_lang){
			
			$site_option_langs = array();
			$sql = "select * from options as opt
					inner join options_lang_details as old on old.opt_id = opt.option_id
					order by option_key asc					";
			$query = $CI->Common_model->commonQuery($sql);
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$site_option_langs[$row->language][$row->option_key] = $row->lang_text;
				}
			}
			$CI->site_option_langs =  $site_option_langs;
			
		}
		/*print_r($CI->site_option_langs);*/
		if(	array_key_exists($lang , $CI->site_option_langs)  && 
		
			array_key_exists($key , $CI->site_option_langs[$lang])
			){
				return $CI->site_option_langs[$lang][$key];
			}else{
				return get_option($key);
			}
		
		
		
		
	}


function get_property_type_lists(){
	
	
	$CI = &get_instance();
	
	if (!isset($CI->property_type_list)){
		
		$sql = "select * from property_types where status = 'Y' order by title ASC";
		$CI->property_type_list = $CI->Common_model->commonQuery($sql);
	}
	
	return $CI->property_type_list;
}












function get_admin_user_emails()
{
	$CI = &get_instance();
	$user_emails = array();
	$query = $CI->Common_model->commonQuery("select user_email from users where user_type = 'admin'");
	if ($query->num_rows() > 0) {
		foreach ($query->result() as $row) {
			$user_emails[] = $row->user_email;
		}
	}
	$CI->admin_emails =  $user_emails;
}





