<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payments extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		if (!$this->isAdminLogin()) {
			redirect('/logins', 'location');
		}

		/*if(!$this->has_method_access())
			{
				redirect('/main/','location');
			}
			$CI =& get_instance();		
			$this->load->library('Global_lib');	
			
			$isPlugAct = $this->isPluginActive('payment');
			if($isPlugAct != true)
			{
				redirect('/main','location');
			}*/
	}

	public function index()
	{
		$this->manage();
	}

	public function manage()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');


		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$payments_methods = $CI->config->item('payment_methods');

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

			$this->global_lib->update_option('payment_methods', json_encode($content));

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				' . mlx_get_lang("Payment Methods Updated Successfully") . '
				</div>							';
			redirect('/payments/manage', 'location');
		}

		$payments_methods_section = $this->global_lib->get_option('payment_methods');

		if (isset($payments_methods_section) && !empty($payments_methods_section)) {
			$data['meta_content_lists'] = json_decode($payments_methods_section, true);
		}



		$data['payment_methods'] = $payments_methods;

		$data['content'] = $CI->theme . "/payments/manage";
		$this->load->view($CI->theme . "/header", $data);
	}
}
