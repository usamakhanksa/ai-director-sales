<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Blog extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->library('Language_lib');
		$this->load->library('bloglib');


		$isPlugAct = $this->isPluginActive('blog');
		if ($isPlugAct != true) {
			redirect('/main', 'location');
		}
	}

	function _remap($method = null, $args = null)
	{


		$multi_lang = $this->enable_multi_lang;
		$default_lang = $this->default_language;
		$default_lang_code = $this->default_lang_code;
		$default_lang_code = $this->default_lang_code_small;
		/*print_r($method); print_r($args);exit;/*/
		if ($multi_lang) {
			$curl = current_url();
			$parts = explode("/", $curl);
			$find_lang = false;
			foreach ($parts as $part) {
				/*if($part == $default_lang)
					echo "yes";*/

				if ($part == $default_lang_code) {
					$find_lang = true;
				}
			}

			$is_lang_exists = false;

			if (!$find_lang) {

				$CI = &get_instance();
				$site_language = get_option('site_language');
				$site_language_array = json_decode($site_language, true);

				$url_lang_code = isset($args[0]) ?  $args[0]  : $this->default_lang_code;

				foreach ($site_language_array as $slak => $slav) {
					/*echo "<pre>";print_r($slav);echo "</pre>";	*/
					$lang_exp = explode('~', $slav['language']);
					$lang_code = $lang_exp[1];
					$lang_title = $lang_exp[0];

					$lang_code_combi = $lang_exp[1];
					$lang_code_exp = explode('-', $lang_code_combi);
					
					
					$lang_code_small = strtolower($lang_code);
					

					/*$flag_code = $lang_code = $lang_code_title = $lang_exp[1];*/
					/*if (isset($lang_code_exp[1])) {
						$lang_code = strtolower($lang_code_exp[1]);
					} else
						$lang_code = $lang_code_exp[0];*/

					if ($url_lang_code == $lang_code_small && $slav['status'] == 'enable') {
						$is_lang_exists = true;
						$this->site_direction = $slav['direction'];
						$this->site_currency = $slav['currency'];
						$this->set_default_timezone($slav['timezone']);
						$lang_slug = $CI->global_lib->get_slug($lang_title);
						$this->default_language = $lang_exp[1];
						
						/*echo " 2".$this->default_lang_code = $lang_code;*/
						$this->default_lang_code = $lang_code_small;
						$this->default_language_title = $lang_title;
						$this->lang->load($lang_slug, $lang_slug);
						$_SESSION['default_lang_front'] = $slav['language'];
						break;
					}
				}
			}
			
			
			if (!$find_lang  && !$is_lang_exists) {


				if ($method == null  || $method == 'index') {
					$url = "/blogs/" . $this->default_lang_code_small . "/";
					//$this->index($default_lang_code);
				} else if ($method == 'single') {
					/*$url = "/blog/single/" . $this->default_lang_code;*/
					$url = "/blog/" . $this->default_lang_code_small;
					if (isset($args[0])) $url .= "/" . $args[0];
					//if(isset($args[1])) $url .= "/". $args[1];
					//if(isset($args[2])) $url .= "/". $args[2];
					//if(isset($args[3])) $url .= "/".$args[3];
				}
				if (isset($_GET['view']))
					$url .= '?view=' . $_GET['view'];

				/*echo $url; exit;*/

				redirect($url, 'location');
			}

			if ($method == null  || $method == 'index') {
				$this->index($this->default_lang_code_small);
			} else if ($method == 'single') {
				//echo "here"  ; 
				//print_r($args);
				//exit;
				//$this->single($this->default_lang_code ,$args[0] );
				$this->single($this->default_lang_code_small, $args[1]);
			} else if ($method == 'category') {
				$this->category($this->default_lang_code_small, $args[1]);
			}
		} else {

			if ($method == null  || $method == 'index') {
				$this->index($default_lang);
			} else if ($method == 'single') {
				$this->single($default_lang, $args[0]);
			} else if ($method == 'category') {
				$this->category($default_lang, $args[0]);
			}
		}
	}

	public function index($lang = 'en')
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');
		
		/*echo $lang . $this->default_language;*/

		$data['theme'] = $theme;

		/*/$today_timestamp = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));*/
		$today_timestamp = mktime(date('h', time()), date('i', time()), date('s', time()), date('m', time()), date('d', time()), date('Y', time()));

		/*echo date("m/d/Y h:i" , $today_timestamp);*/

		
		$sql = "select b.image,b.b_id,b.slug,b.publish_on,
		bc.title as cat_title,bc.slug as cat_slug,
		bld.title as title, bld.short_description,
		bld.seo_meta_keywords, bld.seo_meta_description 
		from blogs as b
		inner join blog_lang_details as bld on bld.blog_id = b.b_id and bld.language = '$this->default_language'
		left join blog_categories as bc on bc.c_id = b.cat_id and bc.status = 'Y'
		and bld.title != '' and bld.description != ''
		where b.status = 'publish' and b.publish_on <= $today_timestamp order by b.publish_on DESC";



		$data['blog_list'] = $post = $this->Common_model->commonQuery($sql );

		/*echo $post->num_rows();*/

		$data['meta_keywords'] = '';
		$data['meta_description'] = '';
		$data['page_title'] = $data['banner_title'] = 'Blogs';

		$data['has_banner'] = false;


		$this->load->library('Banner_lib');
		$args = array("assign_type" => "static", "assign_id" => 'blogs');
		$banner_result =  $this->banner_lib->get_banners($args);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}

		$data['meta_keywords'] = $this->global_lib->get_seo_settings("blog", 'meta_keywords');
		$data['meta_description'] = $this->global_lib->get_seo_settings("blog", 'meta_description');

		$data['content'] = "$theme/blog/all";

		$this->load->view("$theme/header", $data);

		$data['sidebar'] = 'sidebar-left';
	}

	public function single($lang = 'en', $slug = NULL)
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');

		/*echo $lang .  $slug ; exit;*/

		$data['theme'] = $theme;

		/*$today_timestamp = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));*/
		$today_timestamp = mktime(date('h', time()), date('i', time()), date('s', time()), date('m', time()), date('d', time()), date('Y', time()));

		if(isset($_GET['az'])){
			echo "select bld.title,bld.description,bld.seo_meta_keywords,
			bld.seo_meta_description,
			b.image,
			b.publish_on,
			bc.title as cat_title,bc.slug as cat_slug
		from blogs as b
			inner join blog_lang_details as bld on bld.blog_id = b.b_id and bld.language = '$this->default_language'
			left join blog_categories as bc on bc.c_id = b.cat_id and bc.status = 'Y'
			and bld.title != '' and bld.description != ''
			where b.status = 'publish' and b.publish_on <= $today_timestamp and b.slug = '$slug'"; exit;
		}


		$sql = "select bld.title,bld.description,bld.seo_meta_keywords,
			bld.seo_meta_description,
			b.image,
			b.publish_on,
			bc.title as cat_title,bc.slug as cat_slug
		from blogs as b
		inner join blog_lang_details as bld on bld.blog_id = b.b_id and bld.language = '$this->default_language'
		left join blog_categories as bc on bc.c_id = b.cat_id and bc.status = 'Y'
		and bld.title != '' and bld.description != ''
		where b.status = 'publish' and b.publish_on <= $today_timestamp and b.slug = '$slug'" ; 


		$sql = "select bld.title,  b.title as main_title,
						bld.description, b.description  as main_description,
						bld.seo_meta_keywords,
						bld.seo_meta_description,
						b.image,
						b.publish_on,
						bc.title as cat_title,bc.slug as cat_slug
		from blogs as b
		left join blog_lang_details as bld on bld.blog_id = b.b_id and bld.language = '$this->default_language'
		left join blog_categories as bc on bc.c_id = b.cat_id and bc.status = 'Y'
		and bld.title != '' and bld.description != ''
		where b.status = 'publish' and b.publish_on <= $today_timestamp and b.slug = '$slug'" ; 


		$post = $this->Common_model->commonQuery($sql);

		if ($post->num_rows() == 0) {
			redirect('/blogs/', 'location');
		}


		$data['blog_row'] = $blog_row = $post->row();
		$data['meta_keywords'] = $blog_row->seo_meta_keywords;
		$data['meta_description'] = $blog_row->seo_meta_description;
		$data['page_title'] = ucwords($blog_row->title);

		$data['has_banner'] = false;

		$this->load->library('Banner_lib');
		$args = array("assign_type" => "static", "assign_id" => 'blog_single');
		$banner_result =  $this->banner_lib->get_banners($args);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}

		$data['blog_categories'] = $this->Common_model->commonQuery("select bc.title,bc.slug as cat_slug,
			COUNT(b.b_id) AS total_blog	
		from blog_categories as bc
		left join blogs as b on b.cat_id = bc.c_id
		inner join blog_lang_details as bld on bld.blog_id = b.b_id and bld.language = '$this->default_language'
		where bc.status = 'Y' 
		
		group by bc.c_id order by total_blog DESC limit 10");

		$data['recent_blogs'] = $this->Common_model->commonQuery("select b.image,b.slug,b.publish_on,
		bld.title as title
		from blogs as b
		inner join blog_lang_details as bld on bld.blog_id = b.b_id and bld.language = '$this->default_language'
		and bld.title != '' and bld.description != ''
		where b.status = 'publish' and b.publish_on <= $today_timestamp order by b.publish_on DESC limit 10");



		$data['content'] = "$theme/blog/single";

		$this->load->view("$theme/header", $data);

		$data['sidebar'] = 'sidebar-left';
	}

	public function category($lang = 'en', $slug = Null)
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['theme'] = $theme;

		$today_timestamp = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));

		$data['blog_list'] = $post = $this->Common_model->commonQuery("select b.image,b.b_id,b.slug,b.publish_on,
		bc.title as cat_title,bc.slug as cat_slug,
		bld.title as title, bld.short_description,
		bld.seo_meta_keywords, bld.seo_meta_description from blogs as b
		inner join blog_lang_details as bld on bld.blog_id = b.b_id and bld.language = '$this->default_language'
		inner join blog_categories as bc on bc.c_id = b.cat_id and bc.status = 'Y'
		and bld.title != '' and bld.description != ''
		where b.status = 'publish' and b.publish_on <= $today_timestamp and bc.slug = '$slug' order by b.publish_on DESC");

		if ($post->num_rows() == 0) {
			redirect('/blogs', 'location');
		}

		$data['blog_row'] = $blog_row = $post->row();
		$data['meta_keywords'] = '';
		$data['meta_description'] = '';
		$data['page_title'] = $data['banner_title'] = ucwords($blog_row->cat_title);

		$data['has_banner'] = false;


		$this->load->library('Banner_lib');
		$args = array("assign_type" => "static", "assign_id" => 'blog_category');
		$banner_result =  $this->banner_lib->get_banners($args);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}

		$data['content'] = "$theme/blog/all";

		$this->load->view("$theme/header", $data);

		$data['sidebar'] = 'sidebar-left';
	}
}
