<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Appearance extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!$this->isAdminLogin()) {

			redirect('/admin/logins', 'location');
		}/**/
	}

	public function home_page()
	{
		if (!$this->isLogin()) {
			redirect('/logins', 'location');
		}


		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');


		$data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$content_sections = $CI->config->item('content_sections');

		if (isset($_POST['submit']) || isset($_POST['draft'])) {


			$user_id = $this->session->userdata('user_id');

			$content = array();

			foreach ($_POST as $k => $v) {
				if (is_array($v) && $k != 'submit')
					$content[$k] = $v;
			}

			foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}
			extract($_POST, EXTR_OVERWRITE);


			$this->global_lib->update_option('homepage_section', json_encode($content));

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Homepage Section Updated Successfully") . '
			</div>							';
			redirect('/main/home_page', 'location');
		}

		$homepage_section = $this->global_lib->get_option('homepage_section');
		if (isset($homepage_section) && !empty($homepage_section)) {
			$data['meta_content_lists'] = json_decode($homepage_section, true);
		}

		$data['theme'] = $theme;

		$data['content_sections'] = $content_sections;

		$data['content'] = "$theme/home_page";
		$this->load->view("$theme/header", $data);
	}


	public function themes()
	{
		/*if(!$this->isLogin())
		{
			redirect('/logins','location');
		}
		*/

		$CI = &get_instance();

		$data = $CI->data;



		$this->load->config('appearance_config');
		$front_end_themes = $CI->config->item('front_end_themes');



		/*$data = $this->global_lib->uri_check();
		$data['myHelpers']=$this;*/
		$this->load->model('Common_model');
		$this->load->helper('text');


		if (isset($_POST['submit']) || isset($_POST['draft'])) {



			$user_id = $this->session->userdata('user_id');

			$content = array();

			foreach ($_POST as $k => $v) {
				if (is_array($v) && $k != 'submit')
					$content[$k] = $v;
			}

			clean_post();

			/*foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}*/
			extract($_POST, EXTR_OVERWRITE);

			update_option('homepage_section', json_encode($content));

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Homepage Section Updated Successfully") . '</div>							';
			redirect('/main/home_page', 'location');
		}

		$data['front_end_themes'] = $front_end_themes;

		$data['content'] = $CI->theme . "/appearance/themes";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function menus()
	{
		/*if(!$this->isLogin())
		{
			redirect('/logins','location');
		}*/

		$CI = &get_instance();
		$data = $CI->data;

		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit']) || isset($_POST['draft'])) {


			$content = array();

			foreach ($_POST as $k => $v) {
				if (is_array($v) && $k != 'submit')
					$content[$k] = $v;
			}
			
			clean_post();

			/*foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}*/
			extract($_POST, EXTR_OVERWRITE);

			if (isset($options) && !empty($options)) {
				foreach ($options as $k => $v) {
					update_option($k, $v);
				}
			}

			$data['cur_menu'] = $cur_menu;

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Menus Updated Successfully") . '
			</div>';

			redirect('/admin/appearance/menus/'.$cur_menu, 'location');
		}


		if (isset($_POST['menu_location_submit'])) {
			extract($_POST, EXTR_OVERWRITE);
			$menu_list = get_option($menu_locations);
			if (isset($menu_list) && !empty($menu_list)) {
				$data['menu_list'] = $menu_list;
			}
			$data['menu_type'] = ucwords(str_replace('_', ' ', $menu_locations));
			$data['cur_menu'] = $menu_locations;
			/*redirect('/admin/absm_agency/menus/'.$menu_locations, 'location');*/
		} else if (isset($_POST['cur_menu']) && !empty($_POST['cur_menu'])) {
			extract($_POST, EXTR_OVERWRITE);
			$menu_list = get_option($cur_menu);
			if (isset($menu_list) && !empty($menu_list)) {
				$data['menu_list'] = $menu_list;
			}
			$data['menu_type'] = ucwords(str_replace('_', ' ', $cur_menu));
			$data['cur_menu'] = $cur_menu;
			/*redirect('/admin/absm_agency/menus/'.$cur_menu, 'location');*/
		} else {
			$menu_list = get_option('primary_menu');
			if (isset($menu_list) && !empty($menu_list)) {
				$data['menu_list'] = $menu_list;
			}
			$data['menu_type'] = 'Primary Menu';
			$data['cur_menu'] = 'primary_menu';
		}


		$data['page_list'] = $this->Common_model->commonQuery("select page_id,page_title from pages where page_status = 'Y'");
		$data['property_type_list'] = $this->Common_model->commonQuery("select pt_id,title from property_types where status = 'Y'");


		$data['app_site_menus'] = $this->config->item("app_site_menus");
		$data['app_menu_static_pages'] = $this->config->item("app_menu_static_pages");


		$data['content'] = $CI->theme . "/appearance/menus";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function widgets()
	{
		
		/*if (!$this->isLogin()) {
			redirect('/logins', 'location');
		}*/


		$CI = &get_instance();
		//$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');
		$data = $CI->data;

		$this->load->config('appearance_config');
		$front_end_themes = $CI->config->item('front_end_themes');

		//$data = $this->global_lib->uri_check();
		//$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit']) || isset($_POST['draft'])) {



			$user_id = $this->session->userdata('user_id');

			$content = array();

			foreach ($_POST as $k => $v) {
				if (is_array($v) && $k != 'submit')
					$content[$k] = $v;
			}

			foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}

			extract($_POST, EXTR_OVERWRITE);
			update_option('homepage_section', json_encode($content));

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Homepage Section Updated Successfully") . '</div>							';
			redirect('/main/home_page', 'location');
		}



		//$data['theme'] = $theme;

		$data['front_end_themes'] = $front_end_themes;

		$site_widgets_res = $this->Common_model->commonQuery("
				select * from options 	where option_key = 'site_widgets' 		");

		if ($site_widgets_res->num_rows() > 0) {

			$row = $site_widgets_res->row();
			$option_id = $row->option_id;
			$widgets = $row->option_value;
			$widgets = json_decode($widgets, true);
		} else $widgets = array();


		$data['site_widgets'] = $widgets;

		$data['content'] = $CI->theme ."/appearance/widgets";
		$this->load->view($CI->theme ."/header", $data);
	}


	public function customize()
	{
		if (!$this->isLogin()) {
			redirect('/logins', 'location');
		}


		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');

		$this->load->config('appearance_config');
		$customization = $CI->config->item('customization');


		$data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit']) || isset($_POST['draft'])) {



			$user_id = $this->session->userdata('user_id');

			$content = array();

			foreach ($_POST as $k => $v) {
				if (is_array($v) && $k != 'submit')
					$content[$k] = $v;
			}
			clean_post();
			/*foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}*/
			extract($_POST, EXTR_OVERWRITE);
			/*echo "<pre> ";
				print_r($_POST);
				echo "</pre>"; exit;*/

			update_option('custom_styles', json_encode($styles));

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Customize Styles Updated Successfully") . '</div>							';
			redirect('/appearance/customize', 'location');
		}


		$data['theme'] = $theme;

		$data['customization'] = $customization;

		$custom_styles_res = $this->Common_model->commonQuery("
				select * from options 	where option_key = 'custom_styles' 		");

		if ($custom_styles_res->num_rows() > 0) {

			$row = $custom_styles_res->row();
			$option_id = $row->option_id;
			$styles = $row->option_value;
			$styles = json_decode($styles, true);
		} else $styles = array();

		$data['custom_styles'] = $styles;

		$data['content'] = "$theme/appearance/customize";
		$this->load->view("$theme/header", $data);
	}
}
