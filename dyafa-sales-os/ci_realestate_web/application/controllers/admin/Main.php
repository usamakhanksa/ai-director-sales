<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class Main extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		
		$CI = &get_instance();
		if (!$this->isAdminLogin()) {

			redirect('/admin/logins', 'location');
		}

		$this->data = $this->global_lib->uri_check();
		$this->data['myHelpers'] = $this;
		$this->data['CI'] = $CI;

		$this->theme = $CI->config->item('admin_theme');
		$this->data['theme'] = $this->theme;
	}

	public function index()
	{

		$CI = &get_instance();

		$data = $this->data;


		$this->load->library('Global_lib');


		$this->load->model('Common_model');
		$this->load->helper('text');
		$CI->load->config('dashboard_widgets_config');

		do_action("dashboard_widgets_append");

		$dashboard_widgets = $CI->config->item('dashboard_widgets');

		/*print_r($dashboard_widgets);*/

		$user_type = $this->session->userdata('user_type');

		if (isset($dashboard_widgets[$user_type]))
			$data['user_widgets'] = $dashboard_widgets[$user_type];


		

		$data['content'] = $this->theme . "/dashboard";
		$this->load->view($this->theme . "/header", $data);
	}

	public function home_page()
	{

		/*if(!$this->isLogin())
		{
			redirect('/logins','location');
		}*/


		$CI = &get_instance();

		$this->load->library('Global_lib');



		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');
		
		do_action("homepage_contents_append");
		$content_sections = $CI->config->item('homepage_contents');
		/*echo "<pre>";	print_r($content_sections);  echo "</pre>";*/

		if (isset($_POST['submit']) || isset($_POST['draft'])) {


			/*echo "<pre>"; print_r($_POST); exit;*/




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
			
			/*print_r($content); exit; */
			

			extract($_POST, EXTR_OVERWRITE);
			update_option('homepage_section', json_encode($content));

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Homepage Sections Updated Successfully") . '</div>							';
			redirect('/admin/main/home_page', 'location');
		}

		$homepage_section = get_option('homepage_section');

		if (isset($homepage_section) && !empty($homepage_section)) {
			$data['meta_content_lists'] = json_decode($homepage_section, true);
		}

		/*do_action("homepage_contents_append");*/

		$data['content_sections'] = $content_sections;

		$data['content'] = $this->theme . "/home_page";
		$this->load->view($this->theme . "/header", $data);
	}
}
