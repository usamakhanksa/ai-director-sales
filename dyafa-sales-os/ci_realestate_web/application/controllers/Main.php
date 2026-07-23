<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$CI = &get_instance();
		$this->load->library('Language_lib');
		
	}

	function _remap($method = null, $args = null)
	{

		$multi_lang = $this->enable_multi_lang;
		$default_lang = $this->default_language;
		$default_lang_code = $this->default_lang_code;
		$default_lang_code = $this->default_lang_code_small;
		/*print_r($method); print_r($args); exit;*/
		if ($multi_lang) {
			/*echo " curl  <br>".*/
			$curl = current_url();
			$parts = explode("/", $curl);
			$find_lang = false;
			foreach ($parts as $part) {
				/*echo $part ." - {". $default_lang_code . "} {". $default_lang . "} ]] <br>" ;*/
				/*if ($part == $default_lang && $default_lang != $default_lang_code) 
				if ($part ==  $default_lang_code) */
				if ($part == $default_lang && $default_lang != $default_lang_code)
				{
					/*echo $part ." - ". $default_lang_code . " ]] " ;*/
					$curl = str_replace($default_lang, $default_lang_code, $curl);
					redirect($curl, 'location');
				}/**/
				/*echo $part ." - ". $default_lang_code . " ]] " ; */
				if ($part == $default_lang_code) {
					/*echo $part .''. $default_lang_code ; */
					$find_lang = true;
					/*echo "find";*/
				}
			}
			
	
			$is_lang_exists = false;
			if (!$find_lang) {

				$CI = &get_instance();
				$site_language = get_option('site_language');
				$site_language_array = json_decode($site_language, true);

				if ($method != 'page')
					$url_lang_code = isset($args[0]) ?  $args[0]  : $this->default_lang_code_small;
				else
					$url_lang_code = isset($args[1]) ?  $args[1]  : $this->default_lang_code_small;


				/*echo " -".$url_lang_code; exit;*/
				foreach ($site_language_array as $slak => $slav) {
					$lang_exp = explode('~', $slav['language']);
					$lang_code = $lang_exp[1];
					$lang_title = $lang_exp[0];

					$lang_code_combi = $lang_exp[1];
					$lang_code_exp = explode('-', $lang_code_combi);

					/*$flag_code = $lang_code = $lang_code_title = $lang_exp[1];*/
					/*if (isset($lang_code_exp[1])) {
						$lang_code = strtolower($lang_code_exp[1]);
					} else
						$lang_code = $lang_code_exp[0];*/
						
					$lang_code_small = strtolower($lang_code);	


					if ($url_lang_code == $lang_code_small && $slav['status'] == 'enable') {
						/*echo ' -'.$url_lang_code .' '. $lang_code ;*/
						$is_lang_exists = true;
						$this->site_direction = $slav['direction'];
						$this->site_currency = $slav['currency'];
						$this->set_default_timezone($slav['timezone']);
						$lang_slug = $CI->global_lib->get_slug($lang_title);
						$this->default_language = $lang_exp[1]; /*$lang_code; */
						
						/*$default_lang_code = $this->default_lang_code = $lang_code;*/
						$default_lang_code = $this->default_lang_code = $lang_code_small;

						$this->default_language_title = $lang_title;
						$this->lang->load($lang_slug, $lang_slug);
						$_SESSION['default_lang_front'] = $slav['language'];

						break;
					}
				}
			} else {
				/** current language is disabled or not in session. **/
			}


			//exit;

			if (!$find_lang) //&& !$is_lang_exists )
			{
				/*echo "here"; exit;*/

				if (!$is_lang_exists) {

					if (!isset($args[0]))
						$args[0] = '';
					if ($method == null  || $method == 'index')
						$url = "home/$default_lang_code/";
					else if ($method == 'search') {
						$url = "$method/$default_lang_code/" . $args[0];
						if (isset($args[1])) $url .= "/" . $args[1];
						if (isset($args[2])) $url .= "/" . $args[2];
						if (isset($args[3])) $url .= "/" . $args[3];
						if (isset($args[4])) $url .= "/" . $args[4];
						
						
					} else if ($method == 'page')
						$url = "$default_lang_code/" . $args[0];
					else if ($method == 'blogs')
						$url = "$default_lang_code/" . $args[0];
					else if ($method == 'contact')
						$url = "$method/$default_lang_code/" . $args[0];
					else if ($method == 'register')
						$url = "$method/$default_lang_code/" . $args[0];
					else if ($method == 'google_login')
						$url = "$method/$default_lang_code/" . $args[0];
					else if ($method == 'logout')
						$url = "$method/$default_lang_code/" . $args[0];

					redirect($url, 'location');
				}


				//echo $url; exit;

			}
			/*else{
				echo "here"; exit;
			}*/
		}/*else{
			echo "here";
		}*/
	

		if ($method == null  || $method == 'index') {
			$this->index();
		} else if ($method == 'search') {

			$p_for = $p_type = $city = $state = "";
			$find_agents = false;
			$agents = "";
			if (!$multi_lang) {
				foreach ($args as $k => $v) {
					$find = "/property-for/";
					if (preg_match($find, $v))
						$p_for = $args[$k];

					$find = "/property-type/";
					if (preg_match($find, $v))
						$p_type = $args[$k];

					$find = "/city/";
					if (preg_match($find, $v))
						$city = $args[$k];

					$find = "/state/";
					if (preg_match($find, $v))
						$state = $args[$k];

					$find = "/agents/";
					if (preg_match($find, $v)) {
						$find_agents = true;
						$agents = $args[$k];
					}
				}
				if (!$find_agents)
					$this->search($default_lang, $p_for, $p_type, $state, $city);
				else
					$this->search_agents($default_lang, $agents);
			} else {
				if (isset($args[0]))
					$lang = $args[0];
				else
					$lang = $default_lang;

			/*print_r($args); exit;*/
				foreach ($args as $k => $v) {
					$find = "/property-for/";
					if (preg_match($find, $v))
						$p_for = $args[$k];

					$find = "/property-type/";
					if (preg_match($find, $v))
						$p_type = $args[$k];

					$find = "/city/";
					if (preg_match($find, $v))
						$city = $args[$k];

					$find = "/state/";
					if (preg_match($find, $v))
						$state = $args[$k];

					$find = "/agents/";
					if (preg_match($find, $v)) {
						$find_agents = true;
						$agents = $args[$k];
					}
				}

				if (!$find_agents)
					$this->search($lang, $p_for, $p_type, $state, $city);
				else
					$this->search_agents($lang, $agents);
			}
		} else if ($method == 'contact') {
			$this->contact($default_lang);
		} else if ($method == 'register') {
			$this->register($default_lang);
		} else if ($method == 'google_login') {
			$this->google_login($default_lang);
		} else if ($method == 'logout') {
			$this->logout($default_lang);
		} else if ($method == 'blogs') {
			$this->blogs($default_lang);
		} else if ($method == 'page') {

			if (isset($args[0])) $page_slug = $args[0];
			else $page_slug = '';
			//echo "find ".$page_slug; exit;
			$this->page($page_slug, $default_lang);
		}
	}

	public function index()
	{

		

		$this->load->model('Common_model');
		$CI = &get_instance();
		
		/*echo "<pre>";
		print_r($CI); exit;
		$this->output->enable_profiler(TRUE); */
		
		$theme = $CI->config->item('theme');
		$this->load->library('Bloglib');
		$this->load->library('Global_lib');
		$this->load->library('Seometa_lib');
		
		$this->load->helper('text');

		$data = $this->global_lib->uri_check();
		/*$this->output->enable_profiler(TRUE); */

		$data['myHelpers'] = $this;

		if (isset($_GET['theme']) && !empty($_GET['theme']) && $_GET['theme'] == 21) {
			$theme = 'themes/mordern';
		} else if (isset($_GET['theme']) && !empty($_GET['theme']) && $_GET['theme'] == 22) {
			$theme = 'themes/twenty_two';
		}

		$data['theme'] = $theme;


		$homepage_section = get_option('homepage_section');
		if (isset($homepage_section) && !empty($homepage_section)) {
			$data['homepage_section'] = json_decode($homepage_section, true);
		}

		do_action("homepage_contents_append");
		$homepage_contents = $this->config->item('homepage_contents');
		$data['homepage_contents'] = $homepage_contents;

		/*$sql = "select * from property_types where status = 'Y' order by title ASC";
		$data['property_type_list'] = $this->Common_model->commonQuery($sql);*/

		$this->load->library('Banner_lib');
		$args = array("assign_type" => "static", "assign_id" => "homepage");
		
		
		
		$data['banner_list'] = $banner_list =  $this->banner_lib->get_banners($args);


		$data['has_banner'] = false;

		if ($banner_list->num_rows() > 0)
			$data['has_banner'] = true;

		$data['currency_symbol'] = $this->global_lib->get_currency_symbol();

		$data['content'] = "$theme/home_page";
		$data['page_title'] = "Home";
		$data['seometa_for'] = "homepage";
		$this->load->view("$theme/header", $data);
	}

	public function search_agents($lang = 'en', $agents = NULL)
	{
		$this->load->model('Common_model');
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');
		$this->load->library('Seometa_lib');
		$this->load->helper('text');
		$data = $this->global_lib->uri_check();
		$data['theme'] = $theme;
		$data['myHelpers'] = $this;
		$data['page_title'] = "Search Agents";

		//echo " here "; 
		$enbale_our_agents = get_option('enbale_our_agents');
		//exit;
		if ($enbale_our_agents != 'Y') {
			redirect('/', 'location');
		}

		$data['banner_title'] = "All Agents";
		
		$sql = "SELECT u . * , count( p.p_id ) AS total_property
				FROM users AS u
				left JOIN properties AS p ON p.created_by = u.user_id AND p.status = 'publish' 
				WHERE u.user_type = 'agent'
				GROUP BY u.user_id";
		$total_agents = $this->Common_model->commonQuery($sql);
		$no_of_agents_to_show = 12;

		$args = array(
			'total_rows' => $total_agents->num_rows(),
			'per_page' => $no_of_agents_to_show
		);
		$this->load->library('Pagination_lib');
		$pagination_args = $this->pagination_lib->get_pagination_links($args);

		$data['pagination_links'] = $pagination_args['pagination_links'];

		$sql .= " limit " . $pagination_args['start'] . "," . $pagination_args['limit'] . "";
		$data['all_agents'] = $this->Common_model->commonQuery($sql);

		$data['has_banner'] = false;

		$this->load->library('Banner_lib');
		$args = array("assign_type" => "static", "assign_id" => "agents",);
		$banner_result =  $this->banner_lib->get_banners($args);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}

		$data['seometa_for'] = "agents";

		$data['content'] = "$theme/agents";
		$this->load->view("$theme/header", $data);


	}

	public function search($lang = 'en', $property_for = NULL, $property_type = NULL, $state = NULL, $city = NULL)
	{

		if (isset($_GET['for']) || isset($_GET['type'])  || isset($_GET['city']) || isset($_GET['state'])) {
			$url_segs = array('search', ':lang');
			$query = $_SERVER['QUERY_STRING'];

			if (isset($_GET['for'])) {
				if (!empty($_GET['for']))
					$url_segs[] = "property-for-" . $_GET['for'];
				unset($_GET['for']);
			}
			if (isset($_GET['type'])) {
				if (!empty($_GET['type']))
					$url_segs[] = "property-type-" . $_GET['type'];
				unset($_GET['type']);
			}


			$fields =  array("url_segs" => $url_segs);

			$url_segs = 	apply_filters("location_search_get_fields_replace", $fields);
			//print_r($url_segs); exit;
			
			
			$querystring = array();


			$adv_search = false;
			if (isset($_GET['adv_search']) && $_GET['adv_search'] == 1) $adv_search = true;
			$adv_search_skip = array('country_list','adv_search', 'price_ranges', 'bath_ranges', 'bed_ranges', 'indoor_amenities', 'outdoor_amenities');
			foreach ($_GET as $k => $v) {


				if (!$adv_search && in_array($k, $adv_search_skip)) continue;

				if (is_array($v)) {
					$grouped = array();
					foreach ($v as $k1 => $v1) {
						$grouped[] = strtolower($k1);
					}
					$querystring[] = $k . '=' . implode(",", $grouped);
				} else
					$querystring[] = $k . '=' . $v;
			}
			/*print_r($querystring); exit;*/

			$redirect_url = site_url($url_segs);
			$redirect_url = $this->menu_lib->remove_lang_from_url($redirect_url);

			if (!empty($querystring))
				$redirect_url .= "?" . implode("&", $querystring);

			
			redirect($redirect_url, 'location');
		}



		$this->load->model('Common_model');
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$CI->load->library('Property_lib');
		$this->load->library('Global_lib');
		$this->load->library('Seometa_lib');

		$this->load->helper('text');

		$data = $this->global_lib->uri_check();

		$data['myHelpers'] = $this;

		$data['theme'] = $theme;

		$where = '';
		$inner_join = '';

		$for = $type = $prop_city = $prop_state = "";


		$banner = 'search';


		$find = "property-for";
		if (preg_match("/" . $find . "/", $property_for)) {
			$for = str_replace($find . "-", "", $property_for);
			$where .= " and property_for='" . $for . "' ";
			$banner = $property_for;

			$data['seometa_for'] = "property-for-$for";
			/*$data['meta_keywords'] = $this->global_lib->get_seo_settings("property_for_$for",'meta_keywords');
			$data['meta_description'] = $this->global_lib->get_seo_settings("property_for_$for",'meta_description');*/
		}

		$find = "property-type";
		if (preg_match("/" . $find . "/", $property_type)) {
			$type = str_replace($find . "-", "", $property_type);
			$inner_join = " inner join property_types as pt on pt.pt_id = prop.property_type and pt.slug='" . $type . "' ";
		}

		
		$fields =  array("where" => $where, "state" => $state, "city" => $city);

		$where_loc = 	apply_filters("location_search_where_fields", $fields);

		if(!is_array($where_loc)) $where = $where_loc;

		$data['has_banner'] = false;

		/*
		$sql = "select b.b_image from banners as b
			inner join banner_assigned_to as bs on bs.banner_id = b.b_id and bs.assign_type = 'static' and bs.assign_id = '$banner'
			where b.b_status = 'Y' order by b.b_id ASC limit 1";
		$banner_result = $this->Common_model->commonQuery($sql );
		*/

		$this->load->library('Banner_lib');
		$args = array("assign_type" => "static", "assign_id" => $banner,);
		$banner_result =  $this->banner_lib->get_banners($args);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		} else if ($banner != 'search') {

			/*$sql = "select b.b_image from banners as b
			inner join banner_assigned_to as bs on bs.banner_id = b.b_id and bs.assign_type = 'static' and bs.assign_id = 'search'
			where b.b_status = 'Y' order by b.b_id ASC limit 1";
			$banner_result = $this->Common_model->commonQuery($sql );
			
			if($banner_result->num_rows() > 0)
			{
				$data['banner_row'] = $banner_result->row();
				$data['has_banner'] = true;
			}	*/

			$this->load->library('Banner_lib');
			$args = array("assign_type" => "static", "assign_id" => 'search',);
			$banner_result =  $this->banner_lib->get_banners($args);
			if ($banner_result->num_rows() > 0) {
				$data['banner_row'] = $banner_result->row();
				$data['has_banner'] = true;
			}
		}


		if (isset($_GET['adv_search']) && $_GET['adv_search'] == 1) {
			$multi_lang = $this->enable_multi_lang;
			if (isset($_GET['price_ranges']) && !empty($_GET['price_ranges'])) {
				$prince_range_exp = explode(",", $_GET['price_ranges']);
				$price_range_min = $prince_range_exp[0];
				$price_range_max = $prince_range_exp[1];
				if ($multi_lang) {
					$inner_join .= " inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$lang' and pld.price between $price_range_min and $price_range_max";
				} else {
					$where .= " and prop.price between $price_range_min and $price_range_max ";
				}
			}
			if (isset($_GET['bath_ranges']) && !empty($_GET['bath_ranges'])) {
				$bath_range_exp = explode(",", $_GET['bath_ranges']);
				$bath_range_min = $bath_range_exp[0];
				$bath_range_max = $bath_range_exp[1];
				$where .= " and prop.bathroom between $bath_range_min and $bath_range_max ";
			}
			if (isset($_GET['bed_ranges']) && !empty($_GET['bed_ranges'])) {
				$bed_range_exp = explode(",", $_GET['bed_ranges']);
				$bed_range_min = $bed_range_exp[0];
				$bed_range_max = $bed_range_exp[1];
				$where .= " and prop.bedroom between $bed_range_min and $bed_range_max ";
			}
			if (isset($_GET['indoor_amenities']) && !empty($_GET['indoor_amenities'])) {
				$iae = explode(',', $_GET['indoor_amenities']);
				if (!empty($iae)) {
					$where .= ' and ( ';

					foreach ($iae as $iak => $iav) {
						$iav = str_replace("/", "%/", $iav);
						/*if($iak == 0)
			$where .= ' (LOWER( prop.indoor_amenities ) LIKE "%'.str_replace('_',' ',$iav).'%") ';
		else
			$where .= ' and (LOWER( prop.indoor_amenities ) LIKE "%'.str_replace('_',' ',$iav).'%") ';*/

						if ($iak == 0)
							$where .= ' (LOWER( prop.indoor_amenities ) LIKE "%' . str_replace('_', ' ', $iav) . '%" ESCAPE "/"' . ') ';
						else
							$where .= ' and (LOWER( prop.indoor_amenities ) LIKE "%' . str_replace('_', ' ', $iav) . '%" ESCAPE "/"' . ') ';
					}
					$where .= ' ) ';
				}
			}

			if (isset($_GET['outdoor_amenities']) && !empty($_GET['outdoor_amenities'])) {
				$iae = explode(',', $_GET['outdoor_amenities']);
				if (!empty($iae)) {
					$where .= ' and ( ';

					foreach ($iae as $iak => $iav) {
						$iav = str_replace("/", "%/", $iav);
						/*if($iak == 0)
			$where .= ' (LOWER( prop.outdoor_amenities ) LIKE "%'.str_replace('_',' ',$iav).'%") ';
		else
			$where .= ' and (LOWER( prop.outdoor_amenities ) LIKE "%'.str_replace('_',' ',$iav).'%") ';*/


						if ($iak == 0)
							$where .= ' (LOWER( prop.outdoor_amenities ) LIKE "%' . str_replace('_', ' ', $iav) . '%" ESCAPE "/"' . ') ';
						else
							$where .= ' and (LOWER( prop.outdoor_amenities ) LIKE "%' . str_replace('_', ' ', $iav) . '%" ESCAPE "/"' . ') ';
					}
					$where .= ' ) ';
				}
			}
		}



		$def_lang_code = $this->default_language;
		
		//print_r($where); 

		$where = apply_filters("cms_search_properties_extend_where", $where);
		
		//print_r($where); exit;

		$sql = "select prop.* from properties as prop 
		inner join property_lang_details as pld1 on pld1.p_id = prop.p_id and pld1.language = '$def_lang_code'
		inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
		$inner_join where prop.status = 'publish' 
		$where and prop.deleted = 'N' group by prop.p_id order by prop.p_id DESC";

		$total_search_properties = $this->Common_model->commonQuery($sql);



		$no_of_property_in_search_page = get_option('no_of_property_in_search_page');
		if (empty($no_of_property_in_search_page))
			$no_of_property_in_search_page = 12;

		$args = array(
			'total_rows' => $total_search_properties->num_rows(),
			'per_page' => $no_of_property_in_search_page
		);
		$this->load->library('Pagination_lib');
		$pagination_args = $this->pagination_lib->get_pagination_links($args);

		$data['pagination_links'] = $pagination_args['pagination_links'];

		if(isset($_GET['view']) && $_GET['view'] == 'map')
		{
			$sql = "select prop.* from properties as prop 
			inner join property_lang_details as pld1 on pld1.p_id = prop.p_id and pld1.language = '$def_lang_code'
			inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
			$inner_join where prop.status = 'publish' 
			$where 
			and prop.deleted = 'N'
			group by prop.p_id 
			order by prop.p_id DESC";
		}
		else
		{
			$sql = "select prop.* from properties as prop 
			inner join property_lang_details as pld1 on pld1.p_id = prop.p_id and pld1.language = '$def_lang_code'
			inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
			$inner_join where prop.status = 'publish' 
			$where 
			and prop.deleted = 'N'
			group by prop.p_id order by prop.p_id DESC limit " . $pagination_args['start'] . "," . $pagination_args['limit'] . "";
		}


		if(isset($_GET['az'])) 
		{
			echo $sql;
			/*$sql = "select prop.* from properties as prop 
					inner join property_lang_details as pld1 on pld1.p_id = prop.p_id and pld1.language = 'es' 
					inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y' 
					inner join property_types as pt on pt.pt_id = prop.property_type and pt.slug='villa' 
					where prop.status = 'publish' and prop.deleted = 'N' 
					group by prop.p_id order by prop.p_id DESC limit 0,12";*/
		}	


		$data['search_properties'] = $this->Common_model->commonQuery($sql);


		$sql = "select * from property_types where status = 'Y' order by title";
		$data['property_type_list'] = $this->Common_model->commonQuery($sql);

		$data['currency_symbol'] = $this->global_lib->get_currency_symbol();

		$data['page_title'] = "Search Property";
		$data['for'] = $for;
		$data['type'] = $type;


		$data['city'] = $city;
		$data['state'] = $state;

		if (get_option('property_amenities')) {
			$data['amenities_list'] = json_decode(get_option('property_amenities'), true);
		}

		$data['content'] = "$theme/search_page";
		$this->load->view("$theme/header", $data);
	}



	public function page($page_slug = NULL, $lang = 'en')
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		//echo " here "; exit;
		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');


		$data['theme'] = $theme;

		$sql = "select pages.page_id,pld.title as page_title, pld.description as page_content,
		pld.seo_meta_keywords, pld.seo_meta_description from pages 
		inner join page_lang_details as pld on pld.page_id = pages.page_id and pld.language = '$this->default_language'
		and pld.title != '' and pld.description != ''
		where pages.page_slug = '$page_slug'";
		$post = $this->Common_model->commonQuery($sql);

		if ($post->num_rows() == 0) {
			/*
			$post = $this->Common_model->commonQuery("select page_id,page_title, page_content, seo_meta_keywords, seo_meta_description from pages 
			where pages.page_slug = '$page_slug'");
			*/
		}
		
		

		if ($post->num_rows() == 0) {
			redirect('/main', 'location');
		}
		$data['page_row'] = $page_row = $post->row();
		$data['meta_keywords'] = $page_row->seo_meta_keywords;
		$data['meta_description'] = $page_row->seo_meta_description;
		$data['page_title'] = $page_row->page_title;

		$data['has_banner'] = false;

		/*$sql = "select b.b_image from banners as b
			inner join banner_assigned_to as bs on bs.banner_id = b.b_id and bs.assign_type = 'page' 
			and bs.assign_id = '$page_row->page_id'
			where b.b_status = 'Y' order by b.b_id ASC limit 1";
		$banner_result = $this->Common_model->commonQuery($sql );*/


		$this->load->library('Banner_lib');
		$args = array("assign_type" => "page", "assign_id" => $page_row->page_id,);
		$banner_result =  $this->banner_lib->get_banners($args);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}

		$data['content'] = "$theme/page/dynamic_page";

		$this->load->view("$theme/header", $data);

		$data['sidebar'] = 'sidebar-left';
	}

	public function contact($lang = 'en')
	{

		$data = $this->security->xss_clean($_POST);

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');
		$this->load->library('Seometa_lib');
		$this->load->helper('security');


		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$this->load->library('Email_lib');


		$data['has_banner'] = false;


		$this->load->library('Banner_lib');
		$args = array("assign_type" => "static", "assign_id" => 'contact',);
		$banner_result =  $this->banner_lib->get_banners($args);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}

		$data['page_title'] = "Contact Us";
		$data['seometa_for'] = "contact";
		$data['theme'] = $theme;

		$data['content'] = "$theme/contact";

		$this->load->view("$theme/header", $data);
	}

	public function google_login($lang = 'en')
	{
		$CI = &get_instance();

		$this->load->library('Global_lib');

		$this->load->model('Common_model');
		$this->load->helper('text');

		$clientId = get_option('google_login_client_id');
		$clientSecret = get_option('google_login_client_secret');

		$multi_lang = $this->enable_multi_lang;
		$default_lang = $this->default_language;

		$redirectURL = base_url() . 'google_login/';
		if ($multi_lang) {
			$redirectURL .= $default_lang;
		}
		//Call Google API
		$gClient = new Google_Client();
		$gClient->setApplicationName('Realstate Login');
		$gClient->setClientId($clientId);
		$gClient->setClientSecret($clientSecret);
		$gClient->setRedirectUri($redirectURL);
		$google_oauthV2 = new Google_Oauth2Service($gClient);

		if (isset($_GET['code'])) {
			$gClient->authenticate($_GET['code']);
			$_SESSION['token'] = $gClient->getAccessToken();
			header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
		}

		if (isset($_SESSION['token'])) {
			$gClient->setAccessToken($_SESSION['token']);
		}

		if ($gClient->getAccessToken()) {
			$userProfile = $google_oauthV2->userinfo->get();

			$cur_time = time();
			$username = strtolower(str_replace(' ', '_', trim($userProfile['name'])));

			$sql = "select * from users where user_name='" . $username . "' and user_email = '" . trim($userProfile['email']) . "' and user_verified = 'Y' and user_status = 'Y' ";
			$detail = $this->Common_model->commonQuery($sql);

			if ($detail->num_rows() > 0) {
				$data = $detail->row();
				$site_url = site_url();
				$newdata = array(
					'first_name' => $this->global_lib->get_user_meta($data->user_id, 'first_name'),
					'last_name' => $this->global_lib->get_user_meta($data->user_id, 'last_name'),
					'username'  => $username,
					'user_name'     => $data->user_name,
					'user_email'     => $data->user_email,
					'user_id'     => $data->user_id,
					'user_type'     => $data->user_type,
					'user_status'     => $data->user_status,
					'logged_in' => TRUE,
					'site_url' => $site_url
				);
				foreach ($newdata as $k => $v) {
					$_SESSION[$k] = $v;
				}
				$this->session->set_userdata($newdata);


				redirect('/', 'location');
			} else {
				$datai = array(
					'user_name' => $username,
					'user_pass' => md5(trim($userProfile['email'])),
					'user_email' => trim($userProfile['email']),
					'user_type' => 'visitor',
					'user_registered_date' => $cur_time,
					'user_update_date' => $cur_time,
					'user_verified' => 'Y',
					'user_status' => 'Y',
				);
				$user_id = $this->Common_model->commonInsert('users', $datai);


				$new_img_name = 'photo-' . time() . '.jpg';

				$url = $userProfile['picture'];

				$img = FCPATH . '/uploads/user/' . $new_img_name;


				file_put_contents($img, file_get_contents($url));


				$user_meta = array(
					'login_via' => 'google',
					'google_acc_id' => $userProfile['id'],
					'first_name' => $userProfile['name'],
					'photo_url' => $new_img_name,
				);
				foreach ($user_meta as $key => $val) {
					$datai = array(
						'meta_key' => trim($key),
						'meta_value' => trim($val),
						'user_id' => $user_id
					);
					$this->Common_model->commonInsert('user_meta', $datai);
				}
			}

			/*
			session_destroy();
			$newdata = array(  
							'first_name',
							'last_name',
							'username', 
							'user_name',
							'user_email', 
							'user_id', 
							'user_type', 
							'user_status', 
							'site_url'
							);
			foreach($newdata as $k=>$v)
			{
				unset($_SESSION[$v]);
			}
			$this->session->unset_userdata($newdata);
			$this->session->set_userdata('logged_in', false);
			$_SESSION['logged_in'] = false;	
			*/
		} else {
			$url = $gClient->createAuthUrl();
			header("Location: $url");
			exit;
		}
	}

	public function register($lang = 'en')
	{

		$data = $this->security->xss_clean($_POST);

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');
		$this->load->library('Seometa_lib');
		$this->load->helper('security');



		$enbale_front_end_registration = get_option('enbale_front_end_registration');

		$enbale_front_fb = 		get_option('enbale_facebook_login');
		$enbale_front_google = get_option('enbale_gmail_login');

		$logged_in = $this->session->userdata('logged_in');
		if ($enbale_front_end_registration != 'Y' || $logged_in == TRUE) {
			redirect('/', 'location');
		}

		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['has_banner'] = false;

		/*$sql = "select b.b_image from banners as b
			inner join banner_assigned_to as bs on bs.banner_id = b.b_id and bs.assign_type = 'property' and bs.assign_id = 'register'
			where b.b_status = 'Y' order by b.b_id ASC limit 1";
		$banner_result = $this->Common_model->commonQuery($sql ); */

		$this->load->library('Banner_lib');
		$args = array("assign_type" => "static", "assign_id" => 'register',);
		$banner_result =  $this->banner_lib->get_banners($args);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}

		if ($enbale_front_fb == 'Y') {
			if ($this->enable_facebook_login) {
				$this->load->library('facebook');
			}
			if ($this->enable_facebook_login) {
				if ($this->facebook->is_authenticated()) {
					$userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,gender,locale,picture');

					$args = array();
					$args['soc_media'] = 'facebook';
					$fb_user_id = isset($userProfile['id']) ? $userProfile['id'] : 0;

					$args['id'] = $fb_user_id;
					$args['user_name'] = "userfb_" . $fb_user_id;

					$user_meta = array();
					$user_meta['first_name'] = isset($userProfile['first_name']) ? $userProfile['first_name'] : '';
					$user_meta['last_name'] = isset($userProfile['last_name']) ? $userProfile['last_name'] : '';
					$user_email = isset($userProfile['email']) ? $userProfile['email'] : '';
					$user_meta['photo_url'] = isset($userProfile['picture']) ? $userProfile['picture'] : '';

					$args['user_email'] = $user_email;
					$args['user_meta'] = $user_meta;

					$this->create_user_from_social_media($args);
					redirect('/', 'location'); /**/
				} else {
					$data['authUrl'] =  $this->facebook->login_url();
				}
			}
		}

		/*if ($enbale_front_google == 'Y') {
			echo 'here';
		}

		*/

		/*$data['meta_keywords'] = $this->global_lib->get_seo_settings("register",'meta_keywords');
		$data['meta_description'] = $this->global_lib->get_seo_settings("register",'meta_description');*/

		$data['seometa_for'] = "register";

		$data['reg_user_type'] = $CI->config->item('reg_user_type');
		$data['page_title'] = "Register";
		$data['theme'] = $theme;
		$data['content'] = "$theme/register";
		$this->load->view("$theme/header", $data);
	}

	public function create_user_from_social_media($args = array())
	{

		$CI = &get_instance();
		//$theme = $CI->config->item('theme') ;
		//$this->load->library('facebook');
		$this->load->library('Global_lib');
		$this->load->model('Common_model');

		extract($args);

		$sql = "select * from users where user_email = '" . $user_email . "' and user_verified = 'Y' and user_status = 'Y' ";
		$detail = $this->Common_model->commonQuery($sql);
		$cur_time  = time();
		if ($detail->num_rows() > 0) {
			/* user email found means, user alreay exists, create the session and auto login. **/
			$data = $detail->row();
			$site_url = site_url();
			$newdata = array(
				'first_name' => $this->global_lib->get_user_meta($data->user_id, 'first_name'),
				'last_name' => $this->global_lib->get_user_meta($data->user_id, 'last_name'),
				'username'  => $username,
				'user_name'     => $data->user_name,
				'user_email'     => $data->user_email,
				'user_id'     => $data->user_id,
				'user_type'     => $data->user_type,
				'user_status'     => $data->user_status,
				'logged_in' => TRUE,
				'site_url' => $site_url
			);
			foreach ($newdata as $k => $v) {
				$_SESSION[$k] = $v;
			}
			$this->session->set_userdata($newdata);
		} else {

			$datai = array(
				'user_name' => $user_name,
				'user_pass' => md5(trim($user_email)),
				'user_email' => trim($user_email),
				'user_type' => 'auth_user',
				'user_registered_date' => $cur_time,
				'user_update_date' => $cur_time,
				'user_verified' => 'Y',
				'user_status' => 'Y',
			);
			$user_id = $this->Common_model->commonInsert('users', $datai);
			extract($user_meta);
			if (isset($photo_url['data']['url'])   && !empty($photo_url['data']['url'])) {
				$new_img_name = 'photo-' . time() . '.jpg';

				$url = $photo_url['data']['url'];

				$img = FCPATH . '/uploads/user/' . $new_img_name;


				file_put_contents($img, file_get_contents($url));
				$user_meta['photo_url'] = $new_img_name;
			}

			foreach ($user_meta as $key => $val) {
				$datai = array(
					'meta_key' => trim($key),
					'meta_value' => trim($val),
					'user_id' => $user_id
				);
				$this->Common_model->commonInsert('user_meta', $datai);
			}


			$site_url = site_url();
			$newdata = array(
				'first_name' => $this->global_lib->get_user_meta($user_id, 'first_name'),
				'last_name' => $this->global_lib->get_user_meta($user_id, 'last_name'),
				'username'  => $username,
				'user_name'     => $data->user_name,
				'user_email'     => $data->user_email,
				'user_id'     => $data->user_id,
				'user_type'     => $data->user_type,
				'user_status'     => $data->user_status,
				'logged_in' => TRUE,
				'site_url' => $site_url
			);
			foreach ($newdata as $k => $v) {
				$_SESSION[$k] = $v;
			}
			$this->session->set_userdata($newdata);
		}
	}

	public function logout($lang = 'en')
	{

		session_destroy();
		$newdata = array(
			'first_name',
			'last_name',
			'username',
			'user_name',
			'user_email',
			'user_id',
			'user_type',
			'user_status',
			'site_url'
		);
		foreach ($newdata as $k => $v) {
			unset($_SESSION[$v]);
		}
		$this->session->unset_userdata($newdata);
		$this->session->set_userdata('logged_in', false);
		$_SESSION['logged_in'] = false;
		$_SESSION['msg'] = '<p class="success_msg">Logged Out Successfully.</p>';
		redirect('/', 'location');
	}
}
