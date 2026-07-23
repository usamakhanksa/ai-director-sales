<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller
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
		redirect('/admin/main/', 'location');
	}

	public function general_settings()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {



			clean_post();
			extract($_POST);
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");

			$this->form_validation->set_rules('options[website_logo_text]', 'Website Logo Text', 'trim|required');
			if ($this->form_validation->run() != FALSE) {
				
				if (isset($multi_lang) && !empty($multi_lang)) {
					$n = 0;
					foreach ($multi_lang as $k => $v) {
						$n++;
						
						//echo "<pre>"; print_r($v); //exit;/**/
						
						foreach ($v as $vk => $vv) {
							if ($vv == '')
								continue;
							if ($n == 1) {
								update_option($vk, $vv);
								${$vk . '_id'} = get_option_id($vk);
								if(!${$vk . '_id'}) continue;
							}
							
							/*echo " vk id $vk ". ${$vk . '_id'} ; 
							continue;*/

							$options_lang_details = $this->Common_model->commonQuery("select * from options_lang_details
								where opt_id = '" . ${$vk . '_id'} . "' and language = '$k' ");
							if ($options_lang_details->num_rows() == 0) {
								$datai = array(
									'lang_text' => addslashes(trim($vv)),
									'opt_id' => ${$vk . '_id'},
									'language' => $k,
								);

								$this->Common_model->commonInsert('options_lang_details', $datai);
							} else {
								$this->Common_model->commonQuery("update options_lang_details set 
									  lang_text = '" . addslashes(trim($vv)) . "'
									where opt_id = '" . ${$vk . '_id'} . "' and language = '$k'");
							}
						}
						
						//exit;
					}
				}

				if (!isset($options['property_for_cities'])) {
					$options['property_for_cities'] = '';
				}
				if (!isset($options['property_for_states'])) {
					$options['property_for_states'] = '';
				}

				if (isset($options) && !empty($options)) {
					foreach ($options as $key => $value) {
						if (is_array($value))
							$value = json_encode($value);
						update_option($key, $value);
					}
				}

				$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("General Settings Updated Successfully") . '
							</div>
							';
				redirect('/admin/settings/general_settings', 'location');
			}
		}

		$options_list = $data['options_list'] = $this->Common_model->commonQuery("select * from options");


		if ($options_list->num_rows() > 0) {

			foreach ($options_list->result() as $row) {
				$data[$row->option_key] = $row->option_value;

				/*if($row->option_key == 'site_plugins')
				{
					${$row->option_key} = json_decode($row->option_value,true);
				}*/
			}
		}


		$data['currency_symbols'] = $CI->config->item('currency_symbols');

		/*$this->load->config("general_settings_config");*/
		
		do_action("general_settings_append");
		$general_settings_sections  = $CI->config->item('general_settings_sections');
		$data['general_settings_sections'] = $general_settings_sections;

		$data['content'] = $CI->theme . "/settings/general_settings";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function seo_settings()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');
		$data = $this->data;
		$data['languages'] = $CI->config->item('languages');
		$data['seo_static_pages'] = $CI->config->item('seo_static_pages');
		$data['seo_detail_fieds'] = $CI->config->item('seo_detail_fieds');
		$this->load->model('Common_model');
		$this->load->helper('text');
		if (isset($_POST['submit'])) {
			extract($_POST);
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" >
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");

			/*echo "<pre>";print_r($_POST); exit;	*/

			/*$this->form_validation->set_rules('language_title', 'Language Title', 'trim|required');
			if ($this->form_validation->run() != FALSE)
			{*/
			extract($_POST, EXTR_OVERWRITE);


			/*foreach($_POST as $k=>$v)
				{
					$_POST[$k] = $this->security->xss_clean($v);
					$_POST[$k] = str_replace('[removed]','',$_POST[$k]);
				}

				
				*/
			update_option("seo_static_page_details",  json_encode($seo_static_pages));


			$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable" >
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("SEO setting updated Successfully") . '
							</div>
							';
			redirect('admin/settings/seo_settings', 'location');


			/*}*/
		}
		$key = 'site_language';
		$data['site_language'] = get_option($key);
		$key = 'seo_static_page_details';
		$data['seo_static_page_details'] = get_option($key);
		$data['content'] = $CI->theme . "/settings/seo_settings";

		$this->load->view($CI->theme . "/header", $data);
	}
	public function site_languages()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->data;
		$data['languages'] = $CI->config->item('languages');
		$data['currency_symbols'] = $CI->config->item('currency_symbols');


		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {
			$msg_text = '';
			
			clean_post();
			
			
			extract($_POST, EXTR_OVERWRITE);
			if (isset($options) && isset($options['site_language']) && !empty($options['site_language'])) {
				$site_language = get_option('site_language');
				$default_language = get_option('default_language');
				$site_language_array = json_decode($site_language, true);

				foreach ($options['site_language'] as $k => $v) {
					if (!isset($v['language'])) continue;
					$lang_exp = explode('~', $v['language']);
					$lang_name = $lang_exp[0];
					$lang_code = $lang_exp[1];
					$lang_slug = $this->global_lib->get_slug($lang_name, '_');

					if (isset($default_language) && $v['language'] == $default_language && $v['status'] == 'disable') {
						$options['site_language'][$k]['status'] = 'enable';
						$msg_text = '
							<div class="alert alert-danger alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("You cann't Disable Default Language") . '
							</div>
							';
					}

					foreach ($site_language_array as $slak => $slav) {
						if (!isset($slav['language'])) continue;
						if ($slav['language'] == $v['language'])
							unset($site_language_array[$slak]);
					}

					$sql_result = $this->Common_model->commonQuery("SHOW COLUMNS FROM languages LIKE '$lang_slug'");
					if ($sql_result->num_rows() == 0) {
						$this->Common_model->commonQuery("ALTER TABLE languages
							ADD COLUMN $lang_slug VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT ''");
					}

					/*front*/
					if (!is_dir("application/language")) {
						mkdir("application/language", 0777);
					}

					if (!is_dir("application/language/$lang_slug")) {
						mkdir("application/language/$lang_slug", 0777);
					}
					if (file_exists("application/language/$lang_slug/" . $lang_slug . "_lang.php")) {
						if ($lang_slug != 'english')
							unlink("application/language/$lang_slug/" . $lang_slug . "_lang.php");
					}

					$fp = fopen("application/language/$lang_slug/" . $lang_slug . "_lang.php", "wb");
					if ($fp) {
						$output = "<?php \n\n";
						$keyword_result = $this->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'front'
												order by lang_id DESC");
						if ($keyword_result->num_rows() > 0) {
							foreach ($keyword_result->result() as $row) {
								/*$output .= '$lang["'.$row->keyword.'"] = "'.$row->$lang_slug.'";'."\n";*/
								$output .= '$lang' . "['" . $row->keyword . "'] = '" . addslashes($row->$lang_slug) . "';\n";
							}
						}
						fwrite($fp, $output);
						fclose($fp);
					}

					/*back*/
					if (!is_dir("application/language/admin")) {
						mkdir("application/language/admin", 0777);
					}

					if (!is_dir("application/language/admin/$lang_slug")) {
						mkdir("application/language/admin/$lang_slug", 0777);
					}
					if (file_exists("application/language/admin/$lang_slug/" . $lang_slug . "_lang.php")) {
						if ($lang_slug != 'english')
							unlink("application/language/admin/$lang_slug/" . $lang_slug . "_lang.php");
					}

					$fp = fopen("application/language/admin/$lang_slug/" . $lang_slug . "_lang.php", "wb");
					if ($fp) {
						$output = "<?php \n\n";
						$keyword_result = $this->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'back'
												order by lang_id DESC");
						if ($keyword_result->num_rows() > 0) {
							foreach ($keyword_result->result() as $row) {
								/*$output .= '$lang["'.$row->keyword.'"] = "'.$row->$lang_slug.'";'."\n";*/
								$output .= '$lang' . "['" . $row->keyword . "'] = '" . addslashes($row->$lang_slug) . "';\n";
							}
						}
						fwrite($fp, $output);
						fclose($fp);
					}
				}

				if (isset($site_language_array) && !empty($site_language_array)) {
					foreach ($site_language_array as $k => $v) {
						if (!isset($v['language'])) continue;
						$lang_exp = explode('~', $v['language']);
						$lang_name = $lang_exp[0];
						$lang_code = $lang_exp[1];
						$lang_slug = $this->global_lib->get_slug($lang_name, '_');
						$this->Common_model->commonQuery("ALTER TABLE `languages` DROP `$lang_slug`");

						$this->Common_model->commonDelete('options_lang_details', $lang_code, 'language');
						$this->Common_model->commonDelete('page_lang_details', $lang_code, 'language');
						$this->Common_model->commonDelete('property_lang_details', $lang_code, 'language');


						if (is_dir("application/language/$lang_slug")  && $lang_slug != 'english') {

							array_map('unlink', glob("application/language/$lang_slug/*.*"));
							rmdir("application/language/$lang_slug");
						}

						/*if (is_dir("application/admin/language/$lang_slug") && $lang_slug != 'english') {
							array_map('unlink', glob("application/admin/language/$lang_slug/*.*"));
							rmdir("application/admin/language/$lang_slug");
						}
						if (is_dir("application/language/admin/$lang_slug") && $lang_slug != 'english') {
							array_map('unlink', glob("application/language/admin/$lang_slug/*.*"));
							rmdir("application/language/admin/$lang_slug");
						}*/
						
						if (is_dir("application/language/$lang_slug/admin") && $lang_slug != 'english') {
							array_map('unlink', glob("application/language/$lang_slug/admin/*.*"));
							rmdir("application/language/$lang_slug/admin");
						}
					}
				}
			}


			foreach ($options as $k => $v) {
				if ($k == 'site_language')
					$val = json_encode($v);
				else
					$val = $v;
				update_option($k, $val);
			}

			if ($msg_text == '') {
				$msg_text = '
					<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						' . mlx_get_lang("Site Languages Saved Successfully") . '
					</div>
					';
			}

			$_SESSION['msg'] = $msg_text;
			redirect('admin/settings/site_languages', 'location');
		}

		$data['site_language'] = get_option('site_language');
		$data['enable_multi_language'] = get_option('enable_multi_language');
		$data['default_language'] = get_option('default_language');


		$data['content'] = $CI->theme . "/settings/site_languages";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function sitemaps()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {
			
			extract($_POST, EXTR_OVERWRITE);


			$multi_lang = get_option('enable_multi_language');
			$default_lang = get_option('default_language');


			$this->load->library('Sitemap_lib');

			$front_url = front_url();

			if ($sitemap_attachments == 'save_to_server') {
				if ($multi_lang == 'N') {
					$filename = 'sitemap.xml';
					$args = array("front_url" => $front_url, "filename" => $filename);
					$this->sitemap_lib->save_sitemap($args);
				} else if ($multi_lang == 'Y') {
					$filename = 'sitemap.xml';
					$args = array("front_url" => $front_url, "filename" => $filename, "multi_lang" => 'Y');
					$this->sitemap_lib->save_sitemaps($args);
				}
			}

			if ($sitemap_attachments == "download_as_attachment") {

				$filename = 'sitemap.xml';
				$args = array("front_url" => $front_url, "filename" => $filename);
				
				if ($multi_lang == 'Y')
					$args["multi_lang"] = "Y";

				$this->sitemap_lib->download_sitemaps($args);
			}

			if ($sitemap_attachments != 'download_as_attachment') {
				$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									' . mlx_get_lang("Sitemaps Updated Successfully") . '
								</div>';

				redirect('/admin/settings/sitemaps', 'location');
			}
		}



		$data['content'] = $CI->theme . "/settings/sitemaps";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function front_keyword_settings()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->data;
		$data['languages'] = $CI->config->item('languages');


		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {
			
			
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");

			$this->form_validation->set_rules('language_title', 'Language Title', 'trim|required');
			if ($this->form_validation->run() != FALSE) {
				clean_post();
				extract($_POST, EXTR_OVERWRITE);
				$lang_exp = explode('~', $language_title);
				$lang_name = $lang_exp[0];
				$lang_code = $lang_exp[1];

				$lang_slug = $this->global_lib->get_slug($lang_name, '_');
				$site_lang_array = array();
				$key = 'website_languages';
				$site_lang = get_option($key);
				if (!empty($site_lang)) {
					$site_lang_array = json_decode($site_lang, true);
					if (array_key_exists($lang_slug, $site_lang_array)) {
						$_SESSION['msg'] = '
							<div class="alert alert-danger alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Language Already Exists") . '
							</div>';
						redirect('/admin/settings/front_keyword_settings', 'location');
					}
					$site_lang_array[$lang_slug] = array('title' => $lang_name, 'code' => $lang_code);
				} else {

					$site_lang_array[$lang_slug] = array('title' => $lang_name, 'code' => $lang_code);

					update_option($key, json_encode($site_lang_array));
				}
				update_option($key, json_encode($site_lang_array));

				$this->Common_model->commonQuery("ALTER TABLE languages
								ADD COLUMN $lang_slug VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL");

				$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Website Language Added Successfully") . '
							</div>
							';
				redirect('/admin/settings/front_keyword_settings', 'location');
			}
		} else if (isset($_POST['lang_update'])) {
			clean_post();
			extract($_POST);
			
			if (isset($lang_ids) && !empty($lang_ids)) {
				foreach ($lang_ids as $k => $v) {
					$datai = array($lang_slug => addslashes($v));
					$this->Common_model->commonUpdate('languages', $datai, 'lang_id', $k);
				}
			}



			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Website Language Updated Successfully") . '
						</div>
						';
			redirect('/admin/settings/front_keyword_settings', 'location');
		} else if (isset($_POST['delete_lang'])) {
			clean_post();
			extract($_POST);
			
			$key = 'website_languages';
			$site_lang = get_option($key);
			if (!empty($site_lang)) {
				$site_lang_array = json_decode($site_lang, true);
				if (array_key_exists($lang_slug, $site_lang_array)) {
					unset($site_lang_array[$lang_slug]);
				}
				update_option($key, json_encode($site_lang_array));
			}
			$this->Common_model->commonQuery("ALTER TABLE languages DROP COLUMN $lang_slug ");

			if (is_dir("application/language/$lang_slug")) {
				array_map('unlink', glob("application/language/$lang_slug/*.*"));
				rmdir("application/language/$lang_slug");
			}

			/*if (is_dir("application/admin/language/$lang_slug")) {
				array_map('unlink', glob("application/admin/language/$lang_slug/*.*"));
				rmdir("application/admin/language/$lang_slug");
			}
			if (is_dir("application/language/$lang_slug/admin")) {
				array_map('unlink', glob("application/language/$lang_slug/admin/*.*"));
				rmdir("application/language/$lang_slug/admin");
			}*/
			if (is_dir("application/language/admin/$lang_slug")) {
				array_map('unlink', glob("application/language/admin/$lang_slug/*.*"));
				rmdir("application/language/admin/$lang_slug");
			}

			$def_lang = get_option('language');
			if (!empty($def_lang) && $def_lang == $lang_slug) {
				update_option('language', 'english');
			}

			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Website Language Deleted Successfully") . '
						</div>
						';
			redirect('/admin/settings/front_keyword_settings', 'location');
		} else if (isset($_POST['update_lang_file'])) {
			
			clean_post();
			extract($_POST);
	
			$site_language = get_option('site_language');
			$site_language_array = json_decode($site_language, true);
			foreach ($site_language_array as $k => $v) {
				$lang_exp = explode('~', $v['language']);
				$lang_name = $lang_exp[0];
				$lang_code = $lang_exp[1];
				$lang_slug = $this->global_lib->get_slug($lang_name, '_');

				/*front*/
				if (!is_dir("application/language")) {
					mkdir("application/language", 0777);
				}

				if (!is_dir("application/language/$lang_slug")) {
					mkdir("application/language/$lang_slug", 0777);
				}
				if (file_exists("application/language/$lang_slug/" . $lang_slug . "_lang.php")) {
					if ($lang_slug != 'english')
						unlink("application/language/$lang_slug/" . $lang_slug . "_lang.php");
				}

				$fp = fopen("application/language/$lang_slug/" . $lang_slug . "_lang.php", "wb");
				if ($fp) {
					$output = "<?php \n\n";
					$keyword_result = $this->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'front'
										order by lang_id DESC");
					if ($keyword_result->num_rows() > 0) {
						foreach ($keyword_result->result() as $row) {
							//$output .= '$lang["'.$row->keyword.'"] = "'.$row->$lang_slug.'";'."\n";
							$output .= '$lang' . "['" . $row->keyword . "'] = '" . addslashes($row->$lang_slug) . "';\n";
						}
					}
					fwrite($fp, $output);
					fclose($fp);
				}
				/*back*/
				/*if (!is_dir("application/admin/language")) {
					mkdir("application/admin/language", 0777);
				}*/

				/*if (!is_dir("application/language/$lang_slug/admin")) {
					mkdir("application/language/$lang_slug/admin", 0777);
				}
				if (file_exists("application/language/$lang_slug/admin/" . $lang_slug . "_lang.php")) {
					if ($lang_slug != 'english')
						unlink("application/language/$lang_slug/admin/" . $lang_slug . "_lang.php");
				}

				$fp = fopen("application/language/$lang_slug/admin/" . $lang_slug . "_lang.php", "wb");
				if ($fp) {
					$output = "<?php \n\n";
					$keyword_result = $this->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'back'
										order by lang_id DESC");
					if ($keyword_result->num_rows() > 0) {
						foreach ($keyword_result->result() as $row) {
							//$output .= '$lang["'.$row->keyword.'"] = "'.$row->$lang_slug.'";'."\n";
							$output .= '$lang' . "['" . $row->keyword . "'] = '" . addslashes($row->$lang_slug) . "';\n";
						}
					}
					fwrite($fp, $output);
					fclose($fp);
				}*/
			}

			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Language File Updated Successfully") . '
						</div>
						';
			redirect('/admin/settings/front_keyword_settings', 'location');
		}

		$key = 'site_language';
		$data['site_language'] = get_option($key);



		$data['content'] = $CI->theme . "/settings/front_keyword_settings";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function manage_front_keywords()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {
			
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");

			$this->form_validation->set_rules('keyword', 'Keyword', 'trim|required');
			if ($this->form_validation->run() != FALSE) {

				clean_post();
				extract($_POST, EXTR_OVERWRITE);

				$query = $this->Common_model->commonQuery("select keyword from languages where keyword = '$keyword' and lang_for = 'front'");
				if ($query->num_rows() > 0) {
					$_SESSION['msg'] = '
							<div class="alert alert-danger alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Keyword Already Exists") . '
							</div>
							';
					redirect('/admin/settings/manage_front_keywords', 'location');
				} else {
					$datai = array('keyword' => $keyword, 'lang_for' => 'front');
					$this->Common_model->commonInsert('languages', $datai);
				}

				$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Website Keyword Update Successfully") . '
							</div>
							';
				redirect('/admin/settings/manage_front_keywords', 'location');
			}
		}

		$data['website_keywords'] = $this->Common_model->commonQuery("select keyword,lang_id from languages where lang_for = 'front' order by lang_id DESC ");

		$data['content'] = $CI->theme . "/settings/manage_front_keywords";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function import_front_keywords()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->helper('directory');
		$map = directory_map(FCPATH . 'application/language/import-language-files');
		$data['lang_file_list'] = $map;


		$data['site_language'] = get_option('site_language');

		$data['content'] = $CI->theme . "/settings/import_front_keywords";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function export_front_keywords()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');
		$data = $this->data;

		$data['site_language'] = get_option('site_language');

		$data['content'] = $CI->theme . "/settings/export_front_keywords";

		$this->load->view($CI->theme . "/header", $data);
	}


	public function admin_keyword_settings()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->data;

		$data['languages'] = $CI->config->item('languages');


		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {
			
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");

			$this->form_validation->set_rules('language_title', 'Language Title', 'trim|required');
			if ($this->form_validation->run() != FALSE) {

				clean_post();

				extract($_POST, EXTR_OVERWRITE);
				$lang_exp = explode('~', $language_title);
				$lang_name = $lang_exp[0];
				$lang_code = $lang_exp[1];

				$lang_slug = $this->global_lib->get_slug($lang_name, '_');
				$site_lang_array = array();
				$key = 'website_languages';
				$site_lang = get_option($key);
				if (!empty($site_lang)) {
					$site_lang_array = json_decode($site_lang, true);
					if (array_key_exists($lang_slug, $site_lang_array)) {
						$_SESSION['msg'] = '
							<div class="alert alert-danger alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Language Already Exists") . '
							</div>';
						redirect('/admin/settings/admin_keyword_settings', 'location');
					}
					$site_lang_array[$lang_slug] = array('title' => $lang_name, 'code' => $lang_code);
				} else {

					$site_lang_array[$lang_slug] = array('title' => $lang_name, 'code' => $lang_code);

					update_option($key, json_encode($site_lang_array));
				}
				update_option($key, json_encode($site_lang_array));

				$this->Common_model->commonQuery("ALTER TABLE languages
								ADD COLUMN $lang_slug VARCHAR(255) CHARACTER SET utf8mb4_unicode_ci COLLATE utf8mb4_unicode_ci NOT NULL");

				$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Website Language Added Successfully") . '
							</div>
							';
				redirect('/admin/settings/admin_keyword_settings', 'location');
			}
		} else if (isset($_POST['lang_update'])) {

			clean_post();
			extract($_POST);
			if (isset($lang_ids) && !empty($lang_ids)) {
				foreach ($lang_ids as $k => $v) {
					$datai = array($lang_slug => addslashes($v));
					$this->Common_model->commonUpdate('languages', $datai, 'lang_id', $k);
				}
			}
			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Website Language Updated Successfully") . '
						</div>
						';
			redirect('/admin/settings/admin_keyword_settings', 'location');
		} else if (isset($_POST['delete_lang'])) {
			
			clean_post();
			extract($_POST);

			$key = 'website_languages';
			$site_lang = get_option($key);
			if (!empty($site_lang)) {
				$site_lang_array = json_decode($site_lang, true);
				if (array_key_exists($lang_slug, $site_lang_array)) {
					unset($site_lang_array[$lang_slug]);
				}
				update_option($key, json_encode($site_lang_array));
			}
			$this->Common_model->commonQuery("ALTER TABLE languages DROP COLUMN $lang_slug ");

			if (is_dir("application/language/admin/$lang_slug")) {
				array_map('unlink', glob("application/language/admin/$lang_slug/*.*"));
				rmdir("application/language/admin/$lang_slug");
			}
			if (is_dir("application/language/$lang_slug")) {
				array_map('unlink', glob("application/language/$lang_slug/*.*"));
				rmdir("application/language/$lang_slug");
			}
			$def_lang = get_option('language');
			if (!empty($def_lang) && $def_lang == $lang_slug) {
				update_option('language', 'english');
			}

			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Website Language Deleted Successfully") . '
						</div>
						';
			redirect('/admin/settings/admin_keyword_settings', 'location');
		} else if (isset($_POST['update_lang_file'])) { 
			
			clean_post();
			extract($_POST);
			$site_language = get_option('site_language');
			$site_language_array = json_decode($site_language, true);
			foreach ($site_language_array as $k => $v) {
				$lang_exp = explode('~', $v['language']);
				$lang_name = $lang_exp[0];
				$lang_code = $lang_exp[1];
				$current_lang_slug = $this->global_lib->get_slug($lang_name, '_');


				if ($current_lang_slug == $lang_slug) {


					/*back*/
					if (!is_dir("application/language/" . $lang_slug . "/admin")) {
						mkdir("application/language/" . $lang_slug . "/admin", 0777);
					}

					if (!is_dir("application/language/" . $lang_slug . "/admin")) {
						mkdir("application/language/" . $lang_slug . "/admin", 0777);
					}
					if (file_exists("application/language/" . $lang_slug . "/admin/" . $lang_slug . "_lang.php")) {
						if ($lang_slug != 'english')
							unlink("application/language/" . $lang_slug . "/admin/" . $lang_slug . "_lang.php");
					}

					$fp = fopen("application/language/" . $lang_slug . "/admin/" . $lang_slug . "_lang.php", "wb");
					
					
					if ($fp) {
						$output = "<?php \n\n";
						$keyword_result = $this->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'back'
												order by lang_id DESC");
						if ($keyword_result->num_rows() > 0) {
							foreach ($keyword_result->result() as $row) {
								
								$output .= '$lang' . "['" . $row->keyword . "'] = '" . addslashes($row->$lang_slug) . "';\n";
							}
						}
						fwrite($fp, $output);
						fclose($fp);
					}
				}
			}

			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Language File Updated Successfully") . '
						</div>
						';
			redirect('/admin/settings/admin_keyword_settings', 'location');
		}else if (isset($_POST['update_english_keywords'])) { 
			
			clean_post();
			extract($_POST);
			$site_language = get_option('site_language');
			$site_language_array = json_decode($site_language, true);
			foreach ($site_language_array as $k => $v) {
				$lang_exp = explode('~', $v['language']);
				$lang_name = $lang_exp[0];
				$lang_code = $lang_exp[1];
				$current_lang_slug = $this->global_lib->get_slug($lang_name, '_');


				if ($current_lang_slug == $lang_slug) {


					/*back*/
					if (!is_dir("application/language/" . $lang_slug . "/admin")) {
						mkdir("application/language/" . $lang_slug . "/admin", 0777);
					}

					if (!is_dir("application/language/" . $lang_slug . "/admin")) {
						mkdir("application/language/" . $lang_slug . "/admin", 0777);
					}
					if (file_exists("application/language/" . $lang_slug . "/admin/" . $lang_slug . "_lang.php")) {
						if ($lang_slug != 'english')
							unlink("application/language/" . $lang_slug . "/admin/" . $lang_slug . "_lang.php");
					}

					$fp = fopen("application/language/" . $lang_slug . "/admin/" . $lang_slug . "_lang.php", "wb");
					
					
					if ($fp) {
						$output = "<?php \n\n";
						$keyword_result = $this->Common_model->commonQuery("select lang_id, keyword,$lang_slug from languages where lang_for = 'back'
												order by lang_id DESC");
						if ($keyword_result->num_rows() > 0) {
							foreach ($keyword_result->result() as $row) {
								
								/*$row = $query->row();*/
								$lang_id = $row->lang_id;
								$datai = array($lang_slug => $row->keyword);
								$this->Common_model->commonUpdate('languages', $datai, 'lang_id', $lang_id);
								
								$output .= '$lang' . "['" . $row->keyword . "'] = '" . $row->keyword . "';\n";
							}
						}
						fwrite($fp, $output);
						fclose($fp);
					}
				}
			}

			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Language File Updated Successfully") . '
						</div>
						';
			redirect('/admin/settings/admin_keyword_settings', 'location');
		}

		$key = 'site_language';
		$data['site_language'] = get_option($key);
		$data['content'] = $CI->theme . "/settings/admin_keyword_settings";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function manage_admin_keywords()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {
			
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");

			$this->form_validation->set_rules('keyword', 'Keyword', 'trim|required');
			if ($this->form_validation->run() != FALSE) {
				clean_post();
				extract($_POST, EXTR_OVERWRITE);
				$query = $this->Common_model->commonQuery("select keyword from languages where keyword = '$keyword' and lang_for = 'back'");
				if ($query->num_rows() > 0) {
					$_SESSION['msg'] = '
							<div class="alert alert-danger alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Keyword Already Exists") . '
							</div>
							';
					redirect('/admin/settings/manage_admin_keywords', 'location');
				} else {
					$datai = array('keyword' => $keyword, 'lang_for' => 'back');
					$this->Common_model->commonInsert('languages', $datai);
				}

				$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable"  style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Website Keyword Update Successfully") . '
							</div>
							';
				redirect('/admin/settings/manage_admin_keywords', 'location');
			}
		}

		$data['website_keywords'] = $this->Common_model->commonQuery("select keyword,lang_id from languages where lang_for = 'back' order by lang_id DESC ");

		$data['content'] = $CI->theme . "/settings/manage_admin_keywords";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function import_admin_keywords()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');

		$data = $this->data;

		$this->load->helper('directory');
		$map = directory_map(FCPATH . 'application/language/import-language-files/admin');
		$data['lang_file_list'] = $map;


		$data['site_language'] = get_option('site_language');

		$data['content'] = $CI->theme . "/settings/import_admin_keywords";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function export_admin_keywords()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');
		$data = $this->data;

		$data['site_language'] = get_option('site_language');

		$data['content'] = $CI->theme . "/settings/export_admin_keywords";

		$this->load->view($CI->theme . "/header", $data);
	}


	public function social_settings()
	{


		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['social_medias'] = $CI->config->item('social_medias');

		if (isset($_POST['submit'])) {
			
			clean_post();

			extract($_POST, EXTR_OVERWRITE);
			$social_media = array();
			foreach ($options as $key => $value) {
				$social_media[$key] = $value;
			}


			$query = $this->Common_model->commonQuery("select * from options where option_key = 'social_media' ");
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$options_id = $row->option_id;
				$datai = array('option_value' => json_encode($social_media));
				$this->Common_model->commonUpdate('options', $datai, 'option_id', $options_id);
			} else {
				$datai = array('option_key' => 'social_media',	'option_value' => json_encode($social_media));
				$this->Common_model->commonInsert('options', $datai);
			}

			$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Social Settings Update Successfully") . '
							</div>
							';
			redirect('/admin/settings/social_settings', 'location');
		}

		$data['options_list'] = $this->Common_model->commonQuery("select * from options where option_key = 'social_media'");



		$data['content'] = $CI->theme . "/settings/social_settings";

		$this->load->view($CI->theme . "/header", $data);
	}



	public function change_password()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {
			
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");

			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			$this->form_validation->set_rules('repeat_password', 'Repeat Password', 'trim|required|matches[password]');
			if ($this->form_validation->run() != FALSE) {

				clean_post();
				extract($_POST, EXTR_OVERWRITE);

				$datai = array(
					'user_pass' => md5($password),
				);


				$this->Common_model->commonUpdate('users', $datai, 'user_id', $this->session->userdata('user_id'));

				$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Password Change Successfully") . '
							</div>
							';
				redirect('/admin/settings/change_password', 'location');
			}
		}


		$data['content'] = $CI->theme . "/settings/change_password";

		$this->load->view($CI->theme . "/header", $data);
	}


	public function db_settings()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');
		$data = $this->data;

		$isPlugAct = $this->isPluginActive('database_backup');
		if ($isPlugAct != true) {
			redirect('/admin/settings/general_settings', 'location');
		}



		if (isset($_POST['submit'])) {
			extract($_POST);

			$CI->load->dbutil();
			$prefs = array(
				'format' => 'zip',
				'filename' => 'backup_' . date('d_m_Y_H_i_s') . '.sql',
				'add_drop' => TRUE,
				'add_insert' => TRUE,
				'newline' => "\n"
			);
			// Backup your entire database and assign it to a variable 
			$backup =  $CI->dbutil->backup($prefs);
			// Load the file helper and write the file to your server 
			$CI->load->helper('file');
			write_file(FCPATH . '/data/backups/' . 'dbbackup_' . date('d_m_Y_H_i_s') . '.zip', $backup);
			// Load the download helper and send the file to your desktop 
			$CI->load->helper('download');
			force_download('dbbackup_' . date('d_m_Y_H_i_s') . '.zip', $backup);
		}




		$data['content'] = $CI->theme . "/settings/db_settings";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function email_setting()
	{


		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');


		if (isset($_POST['submit'])) {
			clean_post();
			extract($_POST, EXTR_OVERWRITE);
			/*if($this->session->userdata('user_id') != 1)
				{	
					redirect('/','location');
				}*/

			$email_setting = array();
			foreach ($options as $key => $value) {
				if ($key == 'default_mailer') {
					update_option($key, $value);
				} else {
					update_option($key, json_encode($value));
				}
			}

			$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable" >
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Email Settings Updated Successfully.") . '
							</div>
							';
			redirect('/admin/settings/email_setting', 'location');
		}

		$data['email_setting'] = get_option('email_setting');

		$data['default_mailer'] = get_option('default_mailer');

		$data['content'] = $CI->theme . "/settings/email_settings";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function email_templates()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {


			$user_id = $this->session->userdata('user_id');
			$content = array();
			
			clean_post();
			foreach ($_POST as $k => $v) {
				if (is_array($v) && $k != 'submit')
					$content[$k] = $v;
			}

			/*echo "<pre>";print_r($_POST); exit;*/

			extract($_POST, EXTR_OVERWRITE);
			update_option('email_templates', json_encode($content));

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Email Template Updated Successfully") . '</div>							';
			redirect('/admin/settings/email_templates', 'location');
		}



		do_action("email_templates_sections_append");
		$data['email_templates_sections'] = $CI->config->item('email_templates_sections');

		$data['email_template_shortcodes'] = $CI->config->item('email_template_shortcodes');

		$email_templates = get_option('email_templates');
		if (isset($email_templates) && !empty($email_templates)) {
			$data['meta_content_lists'] = json_decode($email_templates, true);
		}



		$data['content'] = $CI->theme . "/settings/email_templates";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function profile()
	{
		$CI = &get_instance();
		
		$this->load->library('Global_lib');

		$user_id = $this->session->userdata('user_id');
		$user_type = $this->session->userdata('user_type');
		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit'])) {
			extract($_POST);
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");

			$this->form_validation->set_rules('user_email', 'Email', 'trim|required');

			if ($this->form_validation->run() != FALSE) {
				clean_post();

				extract($_POST, EXTR_OVERWRITE);

				$datai = array('user_email' => $user_email,);

				if (isset($user_type) && !empty($user_type))
					$datai['user_type'] = $user_type;

				$this->Common_model->commonUpdate('users', $datai, 'user_id', $this->session->userdata('user_id'));

				foreach ($user_meta as $key => $val) {
					if (is_array($val))
						$val = json_encode($val);
					$user_id = $this->session->userdata('user_id');
					$sql = "select * from user_meta where meta_key='$key' and user_id=$user_id";
					$result = $this->Common_model->commonQuery($sql);

					if ($result->num_rows() > 0) {

						$meta_id = $result->row()->meta_id;
						$datai = array(
							'meta_value' => trim($val),
						);
						$this->Common_model->commonUpdate('user_meta', $datai, 'meta_id', $meta_id);
					} else {
						$datai = array(
							'meta_key' => trim($key),
							'meta_value' => trim($val),
							'user_id' => $user_id
						);
						$this->Common_model->commonInsert('user_meta', $datai);
					}
				}

				$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Profile Updated Successfully") . '
							</div>
							';
				redirect('/admin/settings/profile', 'location');
			}
		}


		$qry = "select * from users where user_id = $user_id";
		$user_data = $this->Common_model->commonQuery($qry);

		if ($user_data->num_rows() > 0) {
			$user_data = $user_data->row();
		}

		$data['user_data'] = $user_data;

		$data['website_languages'] = get_option('site_language');


		$data['social_medias'] = $CI->config->item('social_medias');

		$data['content'] = $CI->theme . "/settings/profile";

		$this->load->view($CI->theme . "/header", $data);
	}



	public function delete_keyword($rowid , $slug = 'admin')
	{
		$CI = &get_instance();
		$this->load->library('Global_lib');
		if (!is_array($rowid))
			$rowid	= DecryptClientID($rowid);
		$this->load->model('Common_model');

		$tbl = 'languages';
		$pid = 'lang_id';
		if ($slug == 'front')
			$url = '/admin/settings/manage_front_keywords/';
		else
			$url = '/admin/settings/manage_admin_keywords/';
		$fld = mlx_get_lang("Keyword");


		$rows = $this->Common_model->commonDelete($tbl, $rowid, $pid);
		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px; margin-bottom:0px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . $rows . ' ' . $fld . ' ' . mlx_get_lang("Deleted Successfully") . '
							</div>
							';
		redirect($url, 'location', '301');
	}


}
