<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * This controller contains the common functions
 * @author Mindlogix Technologies 
 *
 */

class MY_Controller extends MX_Controller
{

	var $theme;
	var $site_users;
	var $site_user_access;
	var $site_user_settings;
	
	
	var $post_id;
	var $cat_id;

	var $canonical_url;
	var $hreflang_url;
	var $hreflang;

	function __construct()
	{
		parent::__construct();

		/*echo " here i am "; */
		
		$CI = &get_instance();

	
		if (!property_exists($CI, "is_loaded")) {
			$CI->is_loaded = true;




			$CI->theme = $CI->config->item('theme');

			$this->load->model('Common_model');
			$sql = "SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));";
			$this->Common_model->commonQuery($sql );

			$this->load->library('global_lib');
			$this->load->library('menu_lib');
			$this->load->library('ajax_images_lib');
			$this->load->library('widget_lib');

			
			$this->site_users = $CI->config->item('site_users');
			$this->site_user_access = $CI->config->item('site_user_access');
			$this->site_user_settings = $CI->config->item('settings');
			$this->set_default_timezone();

			$this->load->helper('mlxlang_helper');
			$this->load->helper('mlxurl_helper');
			include_once(APPPATH."cms_includes/cms_misc_functions.php");
			get_site_options();
			$default_language = get_option('default_language');
			$CI->default_language = 'en';
			$CI->default_lang_code = 'en';
			$CI->default_lang_code_small = 'en';
			$CI->default_language_title = 'English';
			$CI->enable_multi_lang = false;
			$this->lang->load('english', 'english');
			$CI->site_direction = 'ltr';
			$CI->site_currency = 'USD';
			$CI->site_currency_symbol = '$';
			$CI->currency_pos = 'left';
			$CI->thousand_sep = ',';
			$CI->decimal_sep = '.';
			$CI->num_decimals = '2';

			/** admin core changes **/
			$CI->site_payments = 'N';


			$CI->version = '2.12';
			$CI->cms_version = $CI->config->item('cms_version');

			$enable_multi_language = get_option('enable_multi_language');
			if (!empty($enable_multi_language) && $enable_multi_language == 'Y') {
				$CI->enable_multi_lang = true;
			}
			

			$site_language = get_option('site_language');

			

			$CI->current_url =  current_url();
			$pattern = "/admin/i";
			$CI->is_admin =	preg_match($pattern, $CI->current_url);


			$logged_in = $this->session->userdata('logged_in');
			if ($logged_in && $CI->is_admin) {

				$CI->data = $this->global_lib->uri_check();
				$CI->data['myHelpers'] = $this;
				$CI->data['CI'] = $CI;
				
				$CI->user_id = $this->session->userdata('user_id');
				$CI->user_type = $this->session->userdata('user_type');

				$CI->theme = $CI->config->item('admin_theme');
				$CI->data['theme'] = $CI->theme;

				
			} else {
				$CI->data = $this->global_lib->uri_check();
				$CI->data['myHelpers'] = $this;
				$CI->data['CI'] = $CI;

				$CI->theme = $CI->config->item('theme');
				$CI->data['theme'] = $CI->theme;
			}


			$this->set_default_timezone();


			if (
				isset($_SESSION['default_lang_front']) && !empty($_SESSION['default_lang_front']) &&
				!empty($site_language) && $CI->enable_multi_lang  && !$CI->is_admin
			) {
				
				$sesson_def_lang =   $_SESSION['default_lang_front'];
				$lang_exp = explode('~', $_SESSION['default_lang_front']);

				$lang_code_full = $lang_code = $lang_exp[1];
				$lang_title = $lang_exp[0];

				$lang_code_combi = $lang_exp[1];
				$lang_code_exp = explode('-', $lang_code_combi);
				
				
				if (isset($lang_code_exp[1])) {
					$lang_code = strtolower($lang_code_exp[1]);
				} else
					$lang_code = $lang_code_exp[0];/**/

				$lang_code_small = strtolower($lang_code_full);
				

				$site_language_array = json_decode($site_language, true);
				$is_lang_exists = false;
				$def_lang_options = array();

				foreach ($site_language_array as $slak => $slav) {
					if ($slav['language'] == $default_language)
						$def_lang_options = $slav;

					if (($slav['language']) == ($sesson_def_lang)  && $slav['status'] == 'enable') {
						$is_lang_exists = true;
						$CI->site_direction = $slav['direction'];
						
						$CI->site_currency = $slav['currency'];
						$this->set_default_timezone($slav['timezone']);

						if (isset($slav['currency_pos']))
							$CI->currency_pos = $slav['currency_pos'];

						if (isset($slav['thousand_sep']))
							$CI->thousand_sep = $slav['thousand_sep'];
						if (isset($slav['decimal_sep']))
							$CI->decimal_sep = $slav['decimal_sep'];

						if (isset($slav['num_decimals']))
							$CI->num_decimals = $slav['num_decimals'];
						
						if (isset($slav['currency']))	
							$CI->site_currency = $slav['currency'];
						
						/*$CI->site_currency_symbol = '$';	*/

						/*$CI->default_language = $lang_exp[1]; $lang_code; */
						$CI->default_language = $lang_code_full; 
	
						$CI->default_lang_code = $lang_code;
						$CI->default_lang_code_small = $lang_code_small;

						$lang_slug = $CI->global_lib->get_slug($lang_title);


						$CI->default_language_title = $lang_title;
						$this->lang->load($lang_slug, $lang_slug);

						break;
					}
				}
				if (!$is_lang_exists) {


					
					$CI->site_direction = $def_lang_options['direction'];
					
					$CI->site_currency = $def_lang_options['currency'];
					$this->set_default_timezone($def_lang_options['timezone']);

					if (isset($def_lang_options['currency_pos']))
						$CI->currency_pos = $def_lang_options['currency_pos'];
					if (isset($def_lang_options['thousand_sep']))
						$CI->thousand_sep = $def_lang_options['thousand_sep'];
					if (isset($def_lang_options['decimal_sep']))
						$CI->decimal_sep = $def_lang_options['decimal_sep'];
					if (isset($def_lang_options['num_decimals']))
						$CI->num_decimals = $def_lang_options['num_decimals'];
					
					if (isset($def_lang_options['currency']))	
							$CI->site_currency = $def_lang_options['currency'];

					$lang_exp = explode('~', $default_language);


					
					$lang_code_combi = $lang_exp[1];
					$lang_code_exp = explode('-', $lang_code_combi);

					
					if (isset($lang_code_exp[1])) {
						$lang_code = strtolower($lang_code_exp[1]);
					} else
						$lang_code = $lang_code_exp[0];

					$lang_title = $lang_exp[0];
					$lang_slug = $CI->global_lib->get_slug($lang_title);

					$CI->default_language = $lang_exp[1];
					$CI->default_lang_code = $lang_code;

					$CI->default_language_title = $lang_title;
					$this->lang->load($lang_slug, $lang_slug);
				}
			} else if (!empty($default_language) && !empty($site_language)) {

				$lang_exp = explode('~', $default_language);
				
				$lang_code = $lang_exp[1];
				$lang_title = $lang_exp[0];
				$site_language_array = json_decode($site_language, true);
				$is_lang_exists = false;
				
				/*if(isset($_GET['az'])){
					echo "<br/><br/><br/><br/>";
					print_r($site_language_array);
					
				}*/
				
				foreach ($site_language_array as $slak => $slav) {
					if ($slav['language'] == $lang_title . '~' . $lang_code) {
						$this->set_default_timezone($slav['timezone']);
						$is_lang_exists = true;
						$CI->site_direction = $slav['direction'];
						$CI->site_currency = $slav['currency'];

						if (isset($slav['currency_pos']))
							$CI->currency_pos = $slav['currency_pos'];

						if (isset($slav['thousand_sep']))
							$CI->thousand_sep = $slav['thousand_sep'];

						if (isset($slav['decimal_sep']))
							$CI->decimal_sep = $slav['decimal_sep'];

						if (isset($slav['num_decimals']))
							$CI->num_decimals = $slav['num_decimals'];
						
						if (isset($slav['currency']))	
							$CI->site_currency = $slav['currency'];

						$lang_exp = explode('~', $slav['language']);

						
						$lang_code_combi = $lang_exp[1];
						$lang_code_exp = explode('-', $lang_code_combi);

						
						if (isset($lang_code_exp[1])) {
							$lang_code = strtolower($lang_code_exp[1]);
						} else
							$lang_code = $lang_code_exp[0];
						$lang_title = $lang_exp[0];
						$lang_slug = $CI->global_lib->get_slug($lang_title);

						$CI->default_language = $lang_exp[1];
						$CI->default_lang_code = $lang_code;

						

						break;
					}
				}
				if ($is_lang_exists) {

					$lang_slug = $CI->global_lib->get_slug($lang_title);
					if ($CI->default_language == '')
						$CI->default_language = $lang_code;
					$CI->default_language_title = $lang_title;
					if($CI->is_admin)
					{
						
						if(file_exists(APPPATH. "language/".$lang_slug."/admin/".$lang_slug."_lang.php")){
							$this->lang->load("admin/".$lang_slug, $lang_slug);
						}else{
							/*echo " lang file not exists";	*/
						}
						
					}	else{
						if(file_exists(APPPATH. "language/".$lang_slug."/".$lang_slug."_lang.php")){	
							$this->lang->load($lang_slug, $lang_slug);
						}else{
							/*echo " lang file not exists";	*/
						}	
					}
				}
			}


			$currency_symbols = $CI->config->item('currency_symbols');
			if (array_key_exists($CI->site_currency, $currency_symbols))
				$CI->site_currency_symbol =  $currency_symbols[$CI->site_currency];

			


			$CI->site_users 		= $CI->config->item('site_users');
			$CI->site_user_access 	= $CI->config->item('site_user_access');

			$CI->post_id = 0;
			$CI->cat_id = 0;

			$current_url =  current_url();
			$CI->canonical_url = $current_url;


			if ($CI->enable_multi_lang) {
				$CI->hreflang_url = $current_url;
				$CI->hreflang = $CI->default_language;
			} else {
				$CI->hreflang_url = $CI->hreflang = "";
				if (isset($_SESSION['default_lang_front'])) {
					unset($_SESSION['default_lang_front']);
				}
			}
			
			
			if ($logged_in && $CI->is_admin) {
				include_once(APPPATH."cms_includes/cms_admin_actions.php");/**/
			}else{
				include_once(APPPATH."cms_includes/cms_front_actions.php");
				include_once(APPPATH."views/".$CI->theme."/functions.php");
			}	
			
			include_once(APPPATH."cms_includes/cms_send_email_actions.php");
			include_once(APPPATH."cms_includes/cms_common_actions.php");
			
			$this->load_site_options();
			$CI->site_payments = get_option("enable_payment_option");
			get_admin_user_emails();
			
		}
		/**	$CI->is_loaded		**/
	}

	public function load_site_options(){
		
		$CI = &get_instance();

		if (empty($CI->site_options)) {
			$sql  = "select * from options";
			$options_list = $CI->Common_model->commonQuery($sql);

			$options = array();
			if (isset($options_list) && $options_list->num_rows() > 0) {
				foreach ($options_list->result() as $row) {
					$options[$row->option_key] = $row->option_value;
				}
			}

			$CI->site_options = $options;
		}
	}

	public function set_default_timezone($time_zone = null)
	{
		if ($time_zone != null) {
			date_default_timezone_set($time_zone);
		} else {
			date_default_timezone_set('Asia/Kolkata');
		}
	}

	public function get_the_ID()
	{

		$CI = &get_instance();
		$p = $this->get_the_page_context();
		$c = $this->get_the_cat_context();
		if ("default" == $p  && "default" == $c)
			return 0;
		else {
			if ("default" != $p) {
				return $CI->post_id;
			} else if ("default" != $c) {

				return $CI->cat_id;
			}
			return 0;
		}
	}

	public function get_the_page_context()
	{

		$CI = &get_instance();
		$page_contexts = $CI->config->item('page_contexts');
		$current = $this->router->fetch_class();
		$current_context = "default";

		if (in_array($current, $page_contexts))
			$current_context = $current;

		return $current_context;
	}

	public function get_the_cat_context()
	{

		$CI = &get_instance();
		$cat_contexts = $CI->config->item('cat_contexts');
		$current = $this->router->fetch_class();
		$current_context = "default";

		if (in_array($current, $cat_contexts))
			$current_context = $current;

		return $current_context;
	}


	public function has_menu_access($menu_item = "", $user_type = "")
	{
		$CI = &get_instance();

		
		if (array_key_exists($user_type, $CI->site_users)) {

			$menu_access = $CI->site_user_access[$user_type]['menu'];
			
			/*echo $menu_item;
				echo "<pre>"; print_r($menu_access);echo "</pre>"; */
			
			if ($menu_access['has_access'] == 'access_all') {
				return true;
			} else if ($menu_access['has_access'] == 'exclude') {
				$menu_items = $menu_access['menu_items'];
	
				
				
				if (!in_array($menu_item, $menu_items))
					return true;


			} else if ($menu_access['has_access'] == 'limited') {
				$menu_items = $menu_access['menu_items'];

				/*echo $menu_item;
				if($menu_item == 'rcm_rental_collection'){
					print_r($menu_items); exit;
				}*/

				/*echo $menu_item;
				print_r($menu_access);*/

				if (in_array($menu_item, $menu_items))
					return true;
			}
		}
		return false;
	}

	public function has_class_access($class_item = "", $user_type = "")
	{

		if ($class_item == "") {
			$class_item =  $this->router->fetch_class();
		}

		$user_type = $this->session->userdata('user_type');

		if (isset($this->site_user_settings[$user_type]))
			$user_settings = $this->site_user_settings[$user_type];
		else
			$user_settings = array();


		if (in_array($user_type, $this->site_users)) {
			$class_access = $this->site_user_access[$user_type]['controller'];

			if ($class_access['has_access'] == 'access_all') {
				return true;
			} else if ($class_access['has_access'] == 'limited') {

				$class_items = $class_access['all_items'];
				if (in_array($class_item, $class_items))
					return true;
			}
		}

		return false;
	}

	
	public function has_method_access($method_item = "", $user_type = "")
	{
		$CI = &get_instance();	
		
		$class_item =  $this->router->fetch_class();
		$method_item =  $this->router->fetch_method();

		
		$user_type = $this->session->userdata('user_type');

		if (isset($CI->site_user_settings[$user_type]))
			$user_settings = $CI->site_user_settings[$user_type];
		else
			$user_settings = array();

		$method_access = $CI->site_user_access[$user_type]['view'];
		
		/*if(isset($_GET['az1'])){
				echo "<pre>";  print_r($class_item);print_r($method_item);print_r($method_access);  echo "</pre>"; exit;
			}*/
		if (array_key_exists($user_type, $CI->site_users)) {
			$method_access = $CI->site_user_access[$user_type]['view'];
			
			/*print_r($method_access ); exit;*/
			if ($method_access['has_access'] == 'access_all') {
				return true;
			} else if ($method_access['has_access'] == 'exclude') {
				/*
				$method_items = $method_access['all_items'][$class_item];
				*/
				/*if(isset($_GET['az1'])){
		echo "<pre>1";  
		print_r($class_item);
		print_r($method_access['all_items']);
		print_r($method_access['all_items'][$class_item]);
		print_r($method_item);  
		echo "</pre>"; 
					}*/
					
				if (array_key_exists($class_item, $method_access['all_items'])) {
					$method_items = $method_access['all_items'][$class_item];

					if (!in_array($method_item, $method_items))
						return true;
					
				}else{
					if (!in_array($class_item, $method_access['all_items'])) { 
						return true;
					}/*else{
						
						if(!in_array($method_item, $method_access['all_items'][$class_item]))
							return true;
					}*/
				}	
					
					
				/*print_r($method_access);
				echo $class_item; exit;*/
				
			} else if ($method_access['has_access'] == 'limited') {

				if (array_key_exists($class_item, $method_access['all_items'])) {
					$method_items = $method_access['all_items'][$class_item];

					if (in_array($method_item, $method_items))
						return true;
				}
			}
		}

		return false;
	}
	
	public function has_widget_access($widget_item = "", $user_type = "")
	{

		if ($widget_item == "") {
			return false;
		}


		$user_type = $this->session->userdata('user_type');
		if (in_array($user_type, $this->site_users)) {
			if (isset($this->site_user_access[$user_type]['widget']))
				$widget_access = $this->site_user_access[$user_type]['widget'];
			else
				return false;


			if ($widget_access['has_access'] == 'access_all') {
				return true;
			} else if ($widget_access['has_access'] == 'limited') {

				if (in_array($widget_item, $widget_access['all_items'])) {
					return true;
				}
			}
		}

		return false;
	}

	public function has_permission($item = "", $task = "", $user_type = "")
	{
		

		$user_type = $this->session->userdata('user_type');
		if (in_array($user_type, $this->site_users)) {
			$access = $this->site_user_access[$user_type]['content'];

			if ($access['has_access'] == 'access_all') {
				return true;
			} else if ($access['has_access'] == 'limited') {

				$all_items = $access['all_items'];
				if (array_key_exists($item, $all_items)) {
					$current = $all_items[$item];
					if (in_array($task, $current))
						return true;
				}
			}
		}

		return false;
	}

	public function get_default_status($item = "", $user_type = "")
	{


		$user_type = $this->session->userdata('user_type');
		if (in_array($user_type, $this->site_users)) {
			$access = $this->site_user_access[$user_type]['content'];

			if ($access['default_status'] == 'publish_all') {
				return 'publish';
			} else if ($access['default_status'] == 'limited') {

				$all_items = $access['statuses'];
				if (array_key_exists($item, $all_items)) {
					$status = $all_items[$item];
					return $status;
				}
			}
		}

		return "draft";
	}

	public function EncryptClientId($id)
	{
		return substr(md5($id), 0, 8) . dechex($id);
	}

	public function DecryptClientId($id)
	{
		$md5_8 = substr($id, 0, 8);
		$real_id = hexdec(substr($id, 8));
		return ($md5_8 == substr(md5($real_id), 0, 8)) ? $real_id : 0;
	}

	public function user_id_address()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}


	public function isLogin()
	{
		$site_url = site_url();
		if (isset($_SESSION['f_logged_in']) && $_SESSION['f_logged_in'] == TRUE && isset($_SESSION['site_url']) && $_SESSION['site_url'] == $site_url)
			return true;
		else
			return false;
	}

	public function isAdminLogin()
	{
		 $site_url = site_url();
		$logged_in = $this->session->userdata('logged_in');
		 $sess_site_url = $this->session->userdata('site_url');
		
		/*print_r($_SESSION);
		exit;*/
		if (isset($logged_in) && $logged_in == TRUE) {

			return true;
		} else {
			$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("You have to login first to proceed") . '</p>';
			return false;
		}
	}

	public function isPluginActive($plugin_slug = null)
	{
		$site_plugins_json = $this->global_lib->get_option('site_plugins');
		if (!empty($site_plugins_json) && $plugin_slug != null) {
			$site_plugins = json_decode($site_plugins_json, true);
			if (in_array($plugin_slug, $site_plugins)) {
				return true;
			}
		}
		return false;
	}
}
