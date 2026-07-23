<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index($id)
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['content'] = "$theme/payment/payment_option";
		$this->load->view("$theme/header", $data);
	}
}
