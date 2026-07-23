<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plugins extends MY_Controller {	
	
	function __construct() {
        parent::__construct();
        if(!$this->isAdminLogin())
		{
			
			redirect('/logins','location');
		}
		
		if(!$this->has_method_access())
		{
			redirect('/admin/main/','location');
		}
		/**/
		
		$CI =& get_instance();
		$this->load->library('Language_lib');
		$this->load->library('Package_lib');
		$this->load->library('Global_lib');
		$this->load->model('Common_model');
		$this->load->helper('text');
		
		$this->data = $this->global_lib->uri_check();
		$this->data['myHelpers']=$this;
		$this->data['CI']=$CI;
		
		$this->theme = $CI->config->item('admin_theme') ;
		$this->data['theme']=$this->theme;
    }
	
	public function index()
	{
		$this->manage();
	}
	
	
	public function manage()
	{
		$CI =& get_instance();
		$data = $this->data;
		
		$this->load->library('Plugins_lib');
		
		$data['content'] = "$this->theme/plugins/manage";
		
		$this->load->view("$this->theme/header",$data);
	}
	
	public function add_new()
	{
		$CI = &get_instance();


		$this->load->library('Plugins_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['page_title'] = 'Add New Plugin';

		$data['content'] = $CI->theme . "/plugins/add_new";

		$this->load->view($CI->theme . "/header", $data);
	}

}
