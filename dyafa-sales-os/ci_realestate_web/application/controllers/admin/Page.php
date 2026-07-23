<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Page extends MY_Controller
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
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['query'] = $this->Common_model->commonQuery("select p1.* from pages p1 order by p1.page_title ASC");


		$data['content'] = $CI->theme . "/page/manage";
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
			if (empty($user_id) || $user_id == 0) {
				$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("Session Expired") . '</p>';
				$_SESSION['logged_in'] = false;
				$this->session->set_userdata('logged_in', false);
				redirect('/admin/logins', 'location');
			}

			if (isset($_POST['submit']))
				$page_status = 'Y';
			else if (isset($_POST['draft']))
				$page_status = 'N';
			else
				$page_status = 'Y';


			if (isset($multi_lang) && !empty($multi_lang)) {
				$n = 0;
				foreach ($multi_lang as $k => $v) {
					$n++;
					if ($n == 1) {
						$page_title = $multi_lang[$k]['page_title'];
						$page_content = $multi_lang[$k]['page_content'];
						$seo_meta_keywords = $multi_lang[$k]['seo_meta_keywords'];
						$seo_meta_description = $multi_lang[$k]['seo_meta_description'];
						break;
					}
				}
			}

			$config = array('field' => 'page_slug', 'title' => 'page_title', 'table' => 'pages', 'id' => 'page_id',);
			$this->load->library('Slug_lib', $config);


			$datap = array('page_title' => $page_title,);
			$page_slug = $this->slug_lib->create_uri($datap);




			$datai = array(
				'page_title' => trim($page_title),
				'page_content' => trim($page_content),
				'created_on' => time(),
				'updated_on' => time(),
				'page_status' => $page_status,
				'created_by' => $user_id,
				'page_slug' => $page_slug,
				'seo_meta_keywords' => $seo_meta_keywords,
				'seo_meta_description' => $seo_meta_description,
				'page_sidebar' => 'no',
			);

			$page_id = $this->Common_model->commonInsert('pages', $datai);

			if (isset($multi_lang) && !empty($multi_lang)) {
				foreach ($multi_lang as $k => $v) {
					if ($v['page_title'] != '' && $v['page_content'] != '') {
						$datai = array(
							'title' => addslashes(trim($v['page_title'])),
							'description' => addslashes(trim($v['page_content'])),
							'seo_meta_keywords' => addslashes(trim($v['seo_meta_keywords'])),
							'seo_meta_description' => addslashes(trim($v['seo_meta_description'])),
							'page_id' => $page_id,
							'language' => $k
						);

						$this->Common_model->commonInsert('page_lang_details', $datai);
					}
				}
			} else {
				$default_language_code = $this->default_language;
				$datai = array(
					'title' => addslashes(trim($page_title)),
					'description' => addslashes(trim($page_content)),
					'seo_meta_keywords' => $seo_meta_keywords,
					'seo_meta_description' => $seo_meta_description,
					'page_id' => $page_id,
					'language' => $default_language_code
				);
				$this->Common_model->commonInsert('page_lang_details', $datai);
			}

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				' . mlx_get_lang("Page Added Successfully") . '
				</div>							';
			redirect('/admin/page/manage', 'location');
		}



		$data['content'] = $CI->theme . "/page/add_new";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function edit($p_id = NULL)
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
			if (isset($_POST['submit']))
				$page_status = 'Y';
			else if (isset($_POST['draft']))
				$page_status = 'N';
			else
				$page_status = 'Y';

			if (isset($multi_lang) && !empty($multi_lang)) {
				$n = 0;
				foreach ($multi_lang as $k => $v) {
					$n++;
					if ($n == 1) {
						$page_title = $multi_lang[$k]['page_title'];
						$page_content = $multi_lang[$k]['page_content'];
						$seo_meta_keywords = $multi_lang[$k]['seo_meta_keywords'];
						$seo_meta_description = $multi_lang[$k]['seo_meta_description'];
						break;
					}
				}
			}

			$decId = DecryptClientID($page_id);


			$datai = array(
				'page_title' => trim($page_title),
				'page_content' => trim($page_content),
				'updated_on' => time(),
				'page_status' => $page_status,
				'page_slug' => $page_slug,
				'seo_meta_keywords' => $seo_meta_keywords,
				'seo_meta_description' => $seo_meta_description,
				'page_sidebar' => 'no',
			);

			if (isset($page_slug) && isset($old_slug) && !empty($page_slug) &&  $page_slug != $old_slug) {
				$config = array('field' => 'page_slug', 'title' => 'page_title', 'table' => 'pages', 'id' => 'page_id',);
				$this->load->library('Slug_lib', $config);

				$datap = array('page_slug' => $page_slug,);
				$page_slug = $this->slug_lib->create_uri($datap);
				$datai['page_slug'] = $page_slug;
			}

			$this->Common_model->commonUpdate('pages', $datai, 'page_id', $decId);

			if (isset($multi_lang) && !empty($multi_lang)) {
				foreach ($multi_lang as $k => $v) {

					if (
						isset($v['page_delete']) && isset($v['pld_id']) &&
						($v['page_delete'] == $v['pld_id'])
					) {
						$this->Common_model->commonQuery("delete from page_lang_details
									where pld_id = " . $v['pld_id']);
						continue;
					}

					if ($v['page_title'] != '' && $v['page_content'] != '') {
						$page_lang_details = $this->Common_model->commonQuery("select * from page_lang_details
									where page_id = $decId and language = '$k' ");
						if ($page_lang_details->num_rows() == 0) {
							$datai = array(
								'title' => addslashes(trim($v['page_title'])),
								'description' => addslashes(trim($v['page_content'])),
								'seo_meta_keywords' => addslashes(trim($v['seo_meta_keywords'])),
								'seo_meta_description' => addslashes(trim($v['seo_meta_description'])),
								'page_id' => $decId,
								'language' => $k
							);

							$this->Common_model->commonInsert('page_lang_details', $datai);
						} else {
							$this->Common_model->commonQuery("update page_lang_details set 
									title = '" . addslashes(trim($v['page_title'])) . "' 
									, description = '" . addslashes(trim($v['page_content'])) . "' ,
									seo_meta_keywords = '" . addslashes(trim($v['seo_meta_keywords'])) . "',
									seo_meta_description =  '" . addslashes(trim($v['seo_meta_description'])) . "'
									where page_id =	$decId and language = '$k'");
						}
					}
				}
			} else {
				$default_language_code = $this->default_language;
				$page_lang_details = $this->Common_model->commonQuery("select * from page_lang_details
							where page_id = $decId and language = '$default_language_code' ");
				if ($page_lang_details->num_rows() == 0) {
					$datai = array(
						'title' => addslashes(trim($page_title)),
						'description' => addslashes(trim($page_content)),
						'seo_meta_keywords' => addslashes(trim($seo_meta_keywords)),
						'seo_meta_description' => addslashes(trim($seo_meta_description)),
						'page_id' => $decId,
						'language' => $default_language_code
					);
					$this->Common_model->commonInsert('page_lang_details', $datai);
				} else {
					$this->Common_model->commonQuery("update page_lang_details set 
						title = '" . addslashes(trim($page_title)) . "' 
						, description = '" . addslashes(trim($page_content)) . "' ,
						seo_meta_keywords = '" . addslashes(trim($seo_meta_keywords)) . "',
						seo_meta_description =  '" . addslashes(trim($seo_meta_description)) . "'
						where page_id = $decId and language = '$default_language_code'");
				}
			}

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable"  style="margin-top:10px;margin-bottom:0px;">	
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>		
				' . mlx_get_lang("Page Updated Successfully") . '							
				</div>';
			redirect('/admin/page/manage', 'location');
		}

		$decId = DecryptClientID($p_id);
		$data['page_meta'] = $page_meta =  $this->Common_model->commonQuery("select *		
		from pages
		where page_id = $decId ");

		if ($page_meta->num_rows() == 0) {
			$_SESSION['msg'] = '<div class="alert alert-danger alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Invalid Page") . '
			</div>							';
			redirect('/admin/page/manage', 'location');
		}


		$data['content'] = $CI->theme . "/page/edit";
		$this->load->view($CI->theme . "/header", $data);
	}


	public function delete($rowid)
	{
		$CI = &get_instance();
		$this->load->library('Global_lib');
		if (!is_array($rowid))
			$rowid	= DecryptClientID($rowid);
		$this->load->model('Common_model');
		$tbl = 'pages';
		$pid = 'page_id';
		$url = '/admin/page/manage/';
		$fld = mlx_get_lang("Page");

		$this->Common_model->commonDelete('page_lang_details', $rowid, $pid);

		$rows = $this->Common_model->commonDelete($tbl, $rowid, $pid);
		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">								
		<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>																
		' . $rows . ' ' . $fld . ' ' . mlx_get_lang("Deleted Successfully") . '							
		</div>							';
		redirect($url, 'location', '301');
	}
}
