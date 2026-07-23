<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Banner extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		if (!$this->isAdminLogin()) {

			redirect('/admin/logins', 'location');
		}

		if(!$this->has_method_access())
		{
			redirect('/admin/main/','location');
		}
	}

	public function index()
	{
		$this->manage();
	}


	public function manage()
	{


		$CI = &get_instance();
		
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');


		$data['query'] = $this->Common_model->commonQuery("
				select * from banners 
				order by b_id DESC
				");

		$data['static_pages'] = $CI->config->item('static_pages');
		$data['property_list'] = $this->Common_model->commonQuery("
				select prop.title,prop.p_id from properties as prop
				where prop.status = 'publish' and prop.deleted = 'N'
				order by prop.p_id DESC
				");
		$data['page_list'] = $this->Common_model->commonQuery("select p1.page_title,p1.page_id from pages p1 order by p1.page_title ASC");


		$data['content'] = $CI->theme . "/banner/manage";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function add_new()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');


		if (isset($_POST['submit']) || isset($_POST['draft'])) {
			
			
			clean_post();
			extract($_POST, EXTR_OVERWRITE);


			$cur_time = time();
			$datai = array(
				'b_title' => $b_title,
				'b_image' => $b_image,
				'created_by' => $user_id,
				'created_on' => $cur_time,
				'b_status' => $b_status,
			);
			$banner_id = $this->Common_model->commonInsert('banners', $datai);

			if (isset($b_assign_to) && !empty($b_assign_to)) {
				foreach ($b_assign_to as $k => $v) {
					$exp_string = explode('~', $v);
					$datai = array(
						'banner_id' => $banner_id,
						'assign_type' => $exp_string[0]
					);

					if ($exp_string[0] == 'static') {
						$datai['assign_id'] = $exp_string[1];
					} else {
						$datai['assign_id'] = DecryptClientID($exp_string[1]);
					}

					if (!isset($_POST['banner_for_lang'])) {
						$datai['for_lang'] = "";
						$this->Common_model->commonInsert('banner_assigned_to', $datai);
					} else {
						if (count($banner_for_lang) > 0) {
							foreach ($banner_for_lang as $for_lang) {
								$datai['for_lang'] = $for_lang;
								$this->Common_model->commonInsert('banner_assigned_to', $datai);
							}
						}
					}
				}
			}

			$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Banner Added Successfully") . '
							</div>
							';
			redirect('/admin/banner/manage', 'location');
		}

		$data['static_pages'] = $CI->config->item('static_pages');
		$data['property_list'] = $this->Common_model->commonQuery("
				select prop.title,prop.p_id from properties as prop
				where prop.status = 'publish' and prop.deleted = 'N'
				order by prop.p_id DESC
				");
		$data['page_list'] = $this->Common_model->commonQuery("select p1.page_title,p1.page_id from pages p1 order by p1.page_title ASC");



		$data['content'] = $CI->theme . "/banner/add_new";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function edit($c_id = NULL)
	{


		$CI = &get_instance();
		
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');


		if (isset($_POST['submit']) || isset($_POST['draft'])) {
			
			extract($_POST, EXTR_OVERWRITE);

			$cId = DecryptClientID($b_id);

			$datai = array(
				'b_title' => $b_title,
				'b_image' => $b_image,
				'b_status' => $b_status,
			);
			$this->Common_model->commonUpdate('banners', $datai, 'b_id', $cId);

			$this->Common_model->commonQuery("delete from banner_assigned_to where banner_id = '$cId'");

			if (isset($b_assign_to) && !empty($b_assign_to)) {
				foreach ($b_assign_to as $k => $v) {
					$exp_string = explode('~', $v);
					$datai = array(
						'banner_id' => $cId,
						'assign_type' => $exp_string[0]
					);

					if ($exp_string[0] == 'static') {
						$datai['assign_id'] = $exp_string[1];
					} else {
						$datai['assign_id'] = DecryptClientID($exp_string[1]);
					}

					if (!isset($_POST['banner_for_lang'])) {
						$datai['for_lang'] = "";
						$this->Common_model->commonInsert('banner_assigned_to', $datai);
					} else {
						if (count($banner_for_lang) > 0) {
							foreach ($banner_for_lang as $for_lang) {
								$datai['for_lang'] = $for_lang;
								$this->Common_model->commonInsert('banner_assigned_to', $datai);
							}
						}
					}
				}
			}

			$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Banner Updated Successfully") . '
						  </div>
							';
			redirect('/admin/banner/manage', 'location');
		}

		$data['b_id'] = $c_id;
		$decId = DecryptClientID($c_id);
		$data['query'] = $results = $this->Common_model->commonQuery("select * from banners where b_id = '$decId'");
		
		if($results->num_rows() == 0){
			$_SESSION['msg'] = '<div class="alert alert-danger alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Invalid Banner") . '
			</div>';	
			redirect('/admin/banner/manage', 'location');
		}

		$data['static_pages'] = $CI->config->item('static_pages');
		$data['property_list'] = $this->Common_model->commonQuery("
				select prop.title,prop.p_id from properties as prop
				where prop.status = 'publish' and prop.deleted = 'N'
				order by prop.p_id DESC
				");
		$data['page_list'] = $this->Common_model->commonQuery("select p1.page_title,p1.page_id from pages p1 order by p1.page_title ASC");



		$data['banner_for_lang_res'] = $this->Common_model->commonQuery("select * from banner_assigned_to where banner_id = $decId group by for_lang");

		$data['content'] = $CI->theme . "/banner/edit";

		$this->load->view($CI->theme . "/header", $data);
	}



	public function delete($rowid)
	{

		$CI = &get_instance();
		
		if (!is_array($rowid))
			$rowid	= DecryptClientID($rowid);
		$this->load->model('Common_model');

		$tbl = 'banners';
		$pid = 'b_id';
		$url = '/admin/banner/manage/';
		$fld = mlx_get_lang("Banner");

		$result = $this->Common_model->commonQuery("select b_image from banners where b_id = $rowid and b_image != ''");
		if ($result->num_rows() > 0) {
			$img_row = $result->row();
			$photo_name = $img_row->b_image;
			if (isset($photo_name) && !empty($photo_name) && file_exists('uploads/banner/' . $photo_name))
				unlink('uploads/banner/' . $photo_name);
		}
		$this->Common_model->commonDelete('banner_assigned_to', $rowid, 'banner_id');

		$rows = $this->Common_model->commonDelete($tbl, $rowid, $pid);
		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . $rows . ' ' . $fld . ' ' . mlx_get_lang("Deleted Successfully") . '
							</div>
							';
		redirect($url, 'location', '301');
	}
}
