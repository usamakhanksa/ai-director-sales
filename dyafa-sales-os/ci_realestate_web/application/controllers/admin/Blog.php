<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Blog extends MY_Controller
{

	var $post_blog_credit;
	function __construct()
	{

		parent::__construct();
		if (!$this->isAdminLogin()) {
			redirect('/admin/logins', 'location');
		}

		if(!$this->has_method_access())
		{
			redirect('/admin/main/','location');
		}/**/


		$this->load->library('Package_lib');
		$user_id = $this->session->userdata('user_id');
		$this->post_blog_credit = $this->package_lib->get_credits_by_user_id($user_id, 'post_blog_credit');
	}

	public function index()
	{
		$this->manage();
	}

	public function manage($slug = '')
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$user_type = $this->session->userdata('user_type');
		
		/*$subject = "abcdef";
		$pattern = '/^admin/';
		preg_match($pattern, $user_type, $matches, PREG_OFFSET_CAPTURE, 3);
		print_r($matches);
		
		echo strpos( $user_type, "_admin" );*/
		
		if ($user_type == 'admin'  || (strpos( $user_type, "_admin" ) !== false )) {
			if ($slug == 'all') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b order by b.b_id DESC");
			} else if ($slug == 'active' || $slug == '') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'publish' and b.publish_on <= b.created_on order by b.b_id DESC");
			} else if ($slug == 'inactive') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'draft' order by b.b_id DESC");
			} else if ($slug == 'pending') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'pending' order by b.b_id DESC");
			} else if ($slug == 'future_publish') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.publish_on > b.created_on order by b.b_id DESC");
			} else if ($slug == 'rejected') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'reject' order by b.b_id DESC");
			}

			$data['all_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b order by b.b_id DESC");
			$data['active_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'publish' and b.publish_on <= b.created_on order by b.b_id DESC");
			$data['inactive_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'draft' order by b.b_id DESC");
			$data['pending_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'pending' order by b.b_id DESC");
			$data['future_publish_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.publish_on > b.created_on order by b.b_id DESC");
			$data['rejected_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'reject' order by b.b_id DESC");
		} else {
			$user_id = $this->session->userdata('user_id');

			if ($slug == 'all') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.created_by = $user_id order by b.b_id DESC");
			} else if ($slug == 'active' || $slug == '') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'publish' and b.publish_on <= b.created_on and b.created_by = $user_id  order by b.b_id DESC");
			} else if ($slug == 'inactive') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'draft' and b.created_by = $user_id order by b.b_id DESC");
			} else if ($slug == 'pending') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'pending' and b.created_by = $user_id order by b.b_id DESC");
			} else if ($slug == 'future_publish') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.publish_on > b.created_on and b.created_by = $user_id order by b.b_id DESC");
			} else if ($slug == 'rejected') {
				$data['query'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'reject' and b.created_by = $user_id order by b.b_id DESC");
			}

			$data['all_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.created_by = $user_id order by b.b_id DESC");
			$data['active_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'publish' and b.publish_on <= b.created_on and b.created_by = $user_id order by b.b_id DESC");
			$data['inactive_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'draft' and b.created_by = $user_id order by b.b_id DESC");
			$data['pending_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'pending' and b.created_by = $user_id order by b.b_id DESC");
			$data['future_publish_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.publish_on > b.created_on and b.created_by = $user_id order by b.b_id DESC");
			$data['rejected_blogs'] = $this->Common_model->commonQuery("select b.* from blogs b where b.status = 'reject' and b.created_by = $user_id order by b.b_id DESC");
		}
		$data['cur_active_tab'] = $slug;


		$data['content'] = $CI->theme . "/blog/manage";
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

		if (isset($_POST['submit']) || isset($_POST['draft']) || isset($_POST['pending'])) {
			extract($_POST);
			$user_type = $this->session->userdata('user_type');

			if ($this->site_payments == 'Y') {
				if ($this->enable_blog_posting && $this->post_blog_credit <= 0 && $user_type != 'admin') {
					$_SESSION['msg'] = '
							<div class="alert alert-warning alert-dismissable" style="margin-top:10px; margin-bottom:10px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("You don't have sufficent credits for post blog.") . ' 
							</div>
							';
					redirect('/admin/blog/manage', 'location');
				} else if (!$this->is_subscription_expires && $user_type != 'admin') {
					$_SESSION['msg'] = '
					<div class="alert alert-warning alert-dismissable" style="margin-top:10px; margin-bottom:10px;">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						' . mlx_get_lang("You don't have sufficent subscription credits.") . ' 
					</div>
					';
					redirect('/admin/blog/manage', 'location');
				}
			}


			/*foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}

			if (isset($_POST['multi_lang']) && !empty($_POST['multi_lang'])) {
				foreach ($_POST['multi_lang'] as $mk => $mv) {
					foreach ($mv as $mvk => $mvv) {
						$_POST['multi_lang'][$mk][$mvk] = str_replace('[removed]', '', $mvv);
					}
				}
			}*/
			
			clean_post();
			

			extract($_POST, EXTR_OVERWRITE);
			if (empty($user_id) || $user_id == 0) {
				$_SESSION['msg'] = '<p class="error_msg">Session Expired.</p>';
				$_SESSION['logged_in'] = false;
				$this->session->set_userdata('logged_in', false);
				redirect('/admin/logins', 'location');
			}

			$status = 'draft';
			$sess_msg = '';
			if (isset($_POST['submit'])) {
				$status = 'publish';
				$sess_msg = mlx_get_lang('Blog Publish Successfully');
			} else if (isset($_POST['draft'])) {
				$status = 'draft';
				$sess_msg = mlx_get_lang('Blog Saved as Draft Successfully');
			} else if (isset($_POST['pending'])) {
				$status = 'pending';
				$sess_msg = mlx_get_lang('Blog Submitted for Approval Successfully');
			}


			if (isset($multi_lang) && !empty($multi_lang)) {
				$n = 0;
				foreach ($multi_lang as $k => $v) {
					$n++;
					if ($n == 1) {
						$title = $multi_lang[$k]['title'];
						$description = $multi_lang[$k]['description'];
						$short_description = $multi_lang[$k]['short_description'];
						$seo_meta_keywords = $multi_lang[$k]['seo_meta_keywords'];
						$seo_meta_description = $multi_lang[$k]['seo_meta_description'];
						break;
					}
				}
			}

			/*$blog_slug = $this->global_lib->get_slug(trim($title));
			$qry = "select slug from  blogs where slug = '".$blog_slug."' order by b_id DESC limit 1";
			$result = $this->Common_model->commonQuery($qry);
			if($result->num_rows() > 0)
			{
				$row = $result->row();
				$old_slug = $row->slug;
				$slug_explode = explode('-',$old_slug);
				if(is_numeric($slug_explode[(count($slug_explode)-1)]))
				{
					$slug_count = $slug_explode[(count($slug_explode)-1)];
					$slug_count++;
				}
				else
					$slug_count = 2;
				$blog_slug = $blog_slug.'-'.$slug_count;
			}*/

			$config = array('field' => 'slug', 'title' => 'title', 'table' => 'blogs', 'id' => 'b_id',);
			$this->load->library('Slug_lib', $config);

			$datap = array('title' => $title,);
			$blog_slug = $this->slug_lib->create_uri($datap);


			$datai = array(
				'title' => trim($title),
				'description' => trim($description),
				'short_description' => trim($short_description),
				'created_on' => time(),
				'updated_on' => time(),
				'publish_on' => $this->global_lib->get_timestamp_from_date($publish_on ),
				'status' => $status,
				'created_by' => $user_id,
				'slug' => $blog_slug,
				'cat_id' => DecryptClientID($cat_id),
				'image' => $blog_image,
				'seo_meta_keywords' => $seo_meta_keywords,
				'seo_meta_description' => $seo_meta_description,
			);

			$blog_id = $this->Common_model->commonInsert('blogs', $datai);

			if (isset($multi_lang) && !empty($multi_lang)) {
				foreach ($multi_lang as $k => $v) {
					if ($v['title'] != '' && $v['description'] != '') {
						$datai = array(
							'title' => addslashes(trim($v['title'])),
							'description' => addslashes(trim($v['description'])),
							'short_description' => addslashes(trim($v['short_description'])),
							'seo_meta_keywords' => addslashes(trim($v['seo_meta_keywords'])),
							'seo_meta_description' => addslashes(trim($v['seo_meta_description'])),
							'blog_id' => $blog_id,
							'language' => $k
						);

						$this->Common_model->commonInsert('blog_lang_details', $datai);
					}
				}
			} else {
				$default_language_code = $this->default_language;
				$datai = array(
					'title' => addslashes(trim($title)),
					'description' => addslashes(trim($description)),
					'short_description' => addslashes(trim($short_description)),
					'seo_meta_keywords' => $seo_meta_keywords,
					'seo_meta_description' => $seo_meta_description,
					'blog_id' => $blog_id,
					'language' => $default_language_code
				);
				$this->Common_model->commonInsert('blog_lang_details', $datai);
			}

			$user_type = $this->session->userdata('user_type');

			if ($this->site_payments == 'Y') {
				if ($user_type != 'admin'  && $status == 'publish') {
					$this->credit_id = $this->package_lib->get_credit_id_by_user_id($user_id, 'blog', 'post_blog');
					$credit_used = $this->package_lib->check_credit_used('post_blog', $blog_id, 'blog');
					if (!$credit_used && $this->credit_id) {
						$this->package_lib->add_credit_uses('post_blog', $blog_id, 'blog', $user_id);
						$this->package_lib->update_credits_by_user_id($user_id, 'post_blog_credit', 'minus_credit', 1);
						$this->package_lib->update_credits_updated_credit_for_user($this->credit_id);
					}
				}
			}

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . $sess_msg . '</div>';
			redirect('/admin/blog/manage', 'location');
		}

		$data['blog_categories'] = $page_meta =  $this->Common_model->commonQuery("select title,c_id	
		from blog_categories
		where status = 'Y' order by title ASC");


		$data['content'] = $CI->theme . "/blog/add_new";
		$this->load->view($CI->theme . "/header", $data);
	}


	public function edit($b_id = NULL)
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		if (isset($_POST['submit']) || isset($_POST['draft']) || isset($_POST['pending'])) {


			$user_type = $this->session->userdata('user_type');
			if ($this->site_payments == 'Y') {
				if (!$this->is_subscription_expires && $user_type != 'admin') {
					$_SESSION['msg'] = '
					<div class="alert alert-warning alert-dismissable" style="margin-top:10px; margin-bottom:10px;">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						' . mlx_get_lang("You don't have sufficent subscription credits") . '
					</div>
					';
					redirect('/admin/blog/manage', 'location');
				}
			}

			/*foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}*/
			
			clean_post();
			

			extract($_POST);
			if (isset($_POST['multi_lang']) && !empty($_POST['multi_lang'])) {
				foreach ($_POST['multi_lang'] as $mk => $mv) {
					foreach ($mv as $mvk => $mvv) {
						$_POST['multi_lang'][$mk][$mvk] = str_replace('[removed]', '', $mvv);
					}
				}
			}

			extract($_POST, EXTR_OVERWRITE);

			$status = 'draft';
			if (isset($_POST['submit'])) {
				$status = 'publish';
			} else if (isset($_POST['draft'])) {
				$status = 'draft';
			} else if (isset($_POST['pending'])) {
				$status = 'pending';
			}

			if (isset($multi_lang) && !empty($multi_lang)) {
				$n = 0;
				foreach ($multi_lang as $k => $v) {
					$n++;
					if ($n == 1) {
						$title = $multi_lang[$k]['title'];
						$short_description = $multi_lang[$k]['short_description'];
						$description = $multi_lang[$k]['description'];
						$seo_meta_keywords = $multi_lang[$k]['seo_meta_keywords'];
						$seo_meta_description = $multi_lang[$k]['seo_meta_description'];
						break;
					}
				}
			}

			/*$slug = $this->global_lib->get_slug(trim($title));*/

			$decId = DecryptClientID($b_id);

			/*$qry = "select slug from  blogs where slug = '".$slug."' 
					and b_id != $decId
				order by b_id DESC limit 1";
				$result = $this->Common_model->commonQuery($qry);
				if($result->num_rows() > 0)
				{
						$row = $result->row();
						$old_slug = $row->slug;
						$slug_explode = explode('-',$old_slug);
						if(is_numeric($slug_explode[(count($slug_explode)-1)]))
						{
							$slug_count = $slug_explode[(count($slug_explode)-1)];
							$slug_count++;
						}
						else
							$slug_count = 2;
						$slug = $slug.'-'.$slug_count;
				}*/

			
			$publish_on_timestamp = $this->global_lib->get_timestamp_from_date($publish_on) ;
			if(date("m/d/Y") ==  date("m/d/Y" , $publish_on_timestamp )){
				//$publish_on_timestamp = 
			}


			$datai = array(
				'title' => trim($title),
				'short_description' => trim($short_description),
				'description' => trim($description),
				'updated_on' => time(),
				'publish_on' => $publish_on_timestamp,
				'status' => $status,

				'cat_id' => DecryptClientID($cat_id),
				'image' => $blog_image,
				'seo_meta_keywords' => $seo_meta_keywords,
				'seo_meta_description' => $seo_meta_description,
			);

			if (isset($slug) && isset($old_slug) && !empty($slug) &&  $slug != $old_slug) {

				$config = array('field' => 'slug', 'title' => 'title', 'table' => 'blogs', 'id' => 'b_id',);
				$this->load->library('Slug_lib', $config);

				$datap = array('slug' => $slug,);
				$slug = $this->slug_lib->create_uri($datap);
				$datai['slug'] = $slug;
			}

			$this->Common_model->commonUpdate('blogs', $datai, 'b_id', $decId);

			if (isset($multi_lang) && !empty($multi_lang)) {
				foreach ($multi_lang as $k => $v) {

					if (
						isset($v['blog_delete']) && isset($v['bld_id']) &&
						($v['blog_delete'] == $v['bld_id'])
					) {
						$this->Common_model->commonQuery("delete from blog_lang_details
									where bld_id = " . $v['bld_id']);
						continue;
					}


					if ($v['title'] != '' && $v['description'] != '') {
						$blog_lang_details = $this->Common_model->commonQuery("select * from blog_lang_details
									where blog_id = $decId and language = '$k' ");
						if ($blog_lang_details->num_rows() == 0) {
							$datai = array(
								'title' => addslashes(trim($v['title'])),
								'short_description' => addslashes(trim($v['short_description'])),
								'description' => addslashes(trim($v['description'])),
								'seo_meta_keywords' => addslashes(trim($v['seo_meta_keywords'])),
								'seo_meta_description' => addslashes(trim($v['seo_meta_description'])),
								'blog_id' => $decId,
								'language' => $k
							);

							$this->Common_model->commonInsert('blog_lang_details', $datai);
						} else {
							$this->Common_model->commonQuery("update blog_lang_details set 
									title = '" . addslashes(trim($v['title'])) . "' ,
									description = '" . addslashes(trim($v['description'])) . "' ,
									short_description = '" . addslashes(trim($v['short_description'])) . "' ,
									seo_meta_keywords = '" . addslashes(trim($v['seo_meta_keywords'])) . "',
									seo_meta_description =  '" . addslashes(trim($v['seo_meta_description'])) . "'
									where blog_id =	$decId and language = '$k'");
						}
					}
				}
			} else {
				$default_language_code = $this->default_language;
				$blog_lang_details = $this->Common_model->commonQuery("select * from blog_lang_details
							where blog_id = $decId and language = '$default_language_code' ");
				if ($blog_lang_details->num_rows() == 0) {
					$datai = array(
						'title' => addslashes(trim($title)),
						'short_description' => addslashes(trim($short_description)),
						'description' => addslashes(trim($description)),
						'seo_meta_keywords' => addslashes(trim($seo_meta_keywords)),
						'seo_meta_description' => addslashes(trim($seo_meta_description)),
						'blog_id' => $decId,
						'language' => $default_language_code
					);
					$this->Common_model->commonInsert('blog_lang_details', $datai);
				} else {
					$this->Common_model->commonQuery("update blog_lang_details set 
						title = '" . addslashes(trim($title)) . "' ,
						description = '" . addslashes(trim($description)) . "' ,
						short_description = '" . addslashes(trim($short_description)) . "' ,
						seo_meta_keywords = '" . addslashes(trim($seo_meta_keywords)) . "',
						seo_meta_description =  '" . addslashes(trim($seo_meta_description)) . "'
						where blog_id = $decId and language = '$default_language_code'");
				}
			}

			$user_type = $this->session->userdata('user_type');

			if ($this->site_payments == 'Y') {
				if (isset($current_status) && $current_status == 'pending' && $status == 'publish' && isset($blog_user_id)) {
					$this->package_lib->add_credit_uses('post_blog', $decId, 'blog', $blog_user_id);
					$this->package_lib->update_credits_by_user_id($blog_user_id, 'post_blog_credit', 'minus_credit', 1);
				} else if ($user_type != 'admin'  && $status == 'publish') {
					$user_id = $this->session->userdata('user_id');
					$credit_used = $this->package_lib->check_credit_used('post_blog', $decId, 'blog');
					if (!$credit_used) {
						$this->package_lib->add_credit_uses('post_blog', $decId, 'blog', $user_id);
						$this->package_lib->update_credits_by_user_id($user_id, 'post_blog_credit', 'minus_credit', 1);
					}
				}
			}
			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable"  style="margin-top:10px;margin-bottom:0px;">	
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>		
				' . mlx_get_lang("Blog Updated Successfully") . '
				</div>';
			redirect('/admin/blog/manage', 'location');
		}

		$decId = DecryptClientID($b_id);
		$data['blog_meta'] = $blog_meta =  $this->Common_model->commonQuery("select *		
				from blogs
				where b_id = $decId ");


		/*echo date("F d,Y h:i A") . " - ";
		echo date("F d,Y h:i A" , $blog_meta->row()->publish_on) . "<br>";

		echo time(). " - " . $blog_meta->row()->publish_on;*/

		if ($blog_meta->num_rows() == 0) {
			$_SESSION['msg'] = '<div class="alert alert-danger alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Invalid Blog") . '
			</div>							';
			redirect('/admin/blog/manage', 'location');
		}

		$data['blog_categories'] = $this->Common_model->commonQuery("select title,c_id	
									from blog_categories
									where status = 'Y' order by title ASC");


		$data['content'] = $CI->theme . "/blog/edit";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function category($c_id = null)
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['user_type'] = $this->session->userdata('user_type');
		if (isset($_POST['submit']) || isset($_POST['draft'])) {


			foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}



			if (isset($_POST['multi_lang']) && !empty($_POST['multi_lang'])) {
				foreach ($_POST['multi_lang'] as $mk => $mv) {
					foreach ($mv as $mvk => $mvv) {
						$_POST['multi_lang'][$mk][$mvk] = str_replace('[removed]', '', $mvv);
					}
				}
			}
			extract($_POST);
			$user_id = $this->session->userdata('user_id');


			if (isset($cat_id) && !empty($cat_id)) {
				$decId = DecryptClientID($cat_id);

				$slug = $this->global_lib->get_slug(trim($title));
				$qry = "select slug from blog_categories where slug like '%" . $slug . "%' 
						and c_id != $decId 
						order by c_id DESC limit 1";

				$result = $this->Common_model->commonQuery($qry);
				if ($result->num_rows() > 0) {
					$row = $result->row();
					$old_slug = $row->slug;
					$slug_explode = explode('-', $old_slug);
					if (is_numeric($slug_explode[(count($slug_explode) - 1)])) {
						$slug_count = $slug_explode[(count($slug_explode) - 1)];
						$slug_count++;
					} else
						$slug_count = 2;
					$slug = $slug . '-' . $slug_count;
				}

				$datai = array(
					'title' => trim($title),
					'slug' => $slug,
					'status' => $status,
				);

				$this->Common_model->commonUpdate('blog_categories', $datai, 'c_id', $decId);
				$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Blog Category Updated Successfully") . '
						</div>
						';
			} else {
				$slug = $this->global_lib->get_slug(trim($title));
				$qry = "select slug from blog_categories where slug like '%" . $slug . "%' order by c_id DESC limit 1";
				$result = $this->Common_model->commonQuery($qry);
				if ($result->num_rows() > 0) {
					$row = $result->row();
					$old_slug = $row->slug;
					$slug_explode = explode('-', $old_slug);
					if (is_numeric($slug_explode[(count($slug_explode) - 1)])) {
						$slug_count = $slug_explode[(count($slug_explode) - 1)];
						$slug_count++;
					} else
						$slug_count = 2;
					$slug = $slug . '-' . $slug_count;
				}

				$datai = array(
					'title' => trim($title),
					'slug' => $slug,
					'created_on' => time(),
					'status' => $status,
					'created_by' => $user_id,
				);
				$this->Common_model->commonInsert('blog_categories', $datai);

				$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Blog Category Added Successfully") . '
						</div>
						';
			}

			redirect('/admin/blog/category', 'location');
		}

		$data['query'] = $this->Common_model->commonQuery("
						SELECT * FROM `blog_categories` as cat 
						order by cat.c_id DESC");

		if ($c_id != null) {
			$decId = DecryptClientID($c_id);
			$pt_result = $this->Common_model->commonQuery("select * from blog_categories where c_id = $decId ");
			if ($pt_result->num_rows() > 0) {
				$pt_row = $pt_result->row();
				$data['cat_id'] = $pt_row->c_id;
				$data['title'] = $pt_row->title;
				$data['status'] = $pt_row->status;
			}
		}



		$data['content'] = $CI->theme . "/blog/category";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function delete($rowid)
	{
		$CI = &get_instance();
		$this->load->library('Global_lib');
		if (!is_array($rowid))
			$rowid	= DecryptClientID($rowid);
		$this->load->model('Common_model');
		$tbl = 'blogs';
		$pid = 'b_id';
		$url = '/admin/blog/manage/';
		$fld = mlx_get_lang("Blog");

		$this->Common_model->commonDelete('blog_lang_details', $rowid, 'blog_id');

		$result = $this->Common_model->commonQuery("select image from blogs where b_id = $rowid and image != ''");
		if ($result->num_rows() > 0) {
			$img_row = $result->row();
			$photo_name = $img_row->image;
			if (isset($photo_name) && !empty($photo_name) && file_exists('../uploads/blog/' . $photo_name))
				unlink('../uploads/blog/' . $photo_name);
		}

		$rows = $this->Common_model->commonDelete($tbl, $rowid, $pid);
		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">								
		<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>																
		' . $rows . ' ' . $fld . ' ' . mlx_get_lang("Deleted Successfully") . '							
		</div>							';
		redirect($url, 'location', '301');
	}

	public function delete_cat($rowid)
	{
		$CI = &get_instance();
		$this->load->library('Global_lib');
		if (!is_array($rowid))
			$rowid	= DecryptClientID($rowid);
		$this->load->model('Common_model');
		$tbl = 'blog_categories';
		$pid = 'c_id';
		$url = '/admin/blog/category/';
		$fld = mlx_get_lang("Category");

		$rows = $this->Common_model->commonDelete($tbl, $rowid, $pid);
		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">								
		<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>																
		' . $rows . ' ' . $fld . ' ' . mlx_get_lang("Deleted Successfully") . '							
		</div>							';
		redirect($url, 'location', '301');
	}
}
