<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Media extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!$this->isAdminLogin()) {

			redirect('/admin/logins', 'location');
		}

		if (!$this->has_method_access()) {
			redirect('/admin/main/', 'location');
		}/**/
	}

	public function index()
	{
		$this->manage();
	}

	public function manage()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('admin_theme');

		$this->load->library('Global_lib');


		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$user_id = $this->session->userdata('user_id');
		$user_type = $this->session->userdata('user_type');
		if ($user_type == 'admin' || (strpos( $user_type, "_admin" ) !== false )) {
			$qry = "select pi1.* from post_images pi
			
			inner join post_images pi1 on pi1.parent_image_id = pi.image_id
			and pi1.image_type = 'thumbnail'
			
			order by pi.image_id DESC";
		} else {
			$qry = "select pi1.* from post_images pi
			
			inner join post_images pi1 on pi1.parent_image_id = pi.image_id
			and pi1.image_type = 'thumbnail'
			where pi.user_id = $user_id
			order by pi.image_id DESC";
		}
		$data['media_list'] = $this->Common_model->commonQuery($qry);


		$data['content'] = $theme . "/media/manage";

		$this->load->view($theme . "/header", $data);
	}
}
