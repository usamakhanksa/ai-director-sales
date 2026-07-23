<?php


function cms_user_post_property_use_credit_callback($args =  array()){
	
	$CI =  &get_instance();
	extract($args);
	$user_type = $CI->user_type;
	$user_id = $CI->user_id;
	
	if(isset($prop_user_id)) $user_id = $prop_user_id  ;	
	
	if ($CI->site_payments == 'Y') {
		
		
		
		if (($user_type != 'admin'  && $status == 'publish') || isset($update_user_credit)) {
			$CI->credit_id = $CI->package_lib->get_credit_id_by_user_id($user_id, 'property', 'post_property');


			$credit_used = $CI->package_lib->check_credit_used('post_property', $p_id, 'property');
			if (!$credit_used && $CI->credit_id) {

				$CI->package_lib->add_credit_uses('post_property', $p_id, 'property', $user_id);
				$CI->package_lib->update_credits_by_user_id($user_id, 'post_property_credit', 'minus_credit', 1);
				$CI->package_lib->update_credits_updated_credit_for_user($CI->credit_id);
			}
		}
	}

}
add_action('user_post_property_use_credit', 'cms_user_post_property_use_credit_callback', 10, 0);



/*add_action('user_update_credits', 'cms_user_update_credits_callback', 10, 0);*/















function package_additional_features_callback($args = array())
{
        $CI = &get_instance();
	
        ob_start(); 
        $CI->load->view($CI->theme."/packages/package_additional_features",$args); 
	
       echo $meta_settings = ob_get_clean();
}
/*add_action('package_additional_features', 'package_additional_features_callback', 10, 0);*/




add_filter('cms_show_credit_title', 'cms_show_credit_title_callback');
function cms_show_credit_title_callback($args = array()){
	
	extract($args);
	
	$CI = &get_instance();
	$package_features = $CI->config->item('package_features');
	
	if(isset($credit)){
		$credit = str_replace("_credit","",$credit);
		if(array_key_exists($credit,$package_features)){
			if(array_key_exists('title',$package_features[$credit])){
				echo mlx_get_lang("Credits for ".$package_features[$credit]['title']);
				return true;
			}
		}
	}
	return false;
}


add_filter('cms_show_credit_value', 'cms_show_credit_value_callback');
function cms_show_credit_value_callback($args = array()){
	
	extract($args);
	
	$CI = &get_instance();
	$package_features = $CI->config->item('package_features');
	
	if(isset($credit)){
		$credit = str_replace("_credit","",$credit);
		if(array_key_exists($credit,$package_features)){
			
			$pattern = '/subscription/';
			preg_match($pattern, $credit,$matches);
			
			
			if(count($matches) > 0)
			{
				echo mlx_get_lang('Expires On')." ".date("d/m/Y",$credit_value); 
			}else{
				echo $credit_value;
			}
				return true;
		}
	}
	return false;
}

