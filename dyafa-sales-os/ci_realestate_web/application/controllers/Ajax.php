<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller
{


	public function set_default_language_frontend()
	{
		extract($_POST);

		if (isset($default_lang))
			$_SESSION['default_lang_front'] = $default_lang;
	}

	public function update_compare_settion_callback_func()
	{
		extract($_POST);

		if (isset($p_id) && !empty($p_id) && isset($action) && !empty($action)) {
			if (!isset($_SESSION['comparable_properties'])) {
				$_SESSION['comparable_properties'] = array();
			}
			if ($action == 'add') {
				if (!array_key_exists($p_id, $_SESSION['comparable_properties'])) {
					$_SESSION['comparable_properties'][$p_id] = array(
						'title' => $productTitle,
						'url' => $productURL,
						'img' => $productIMG
					);
				}
			} else if ($action == 'remove') {
				if (array_key_exists($p_id, $_SESSION['comparable_properties'])) {
					unset($_SESSION['comparable_properties'][$p_id]);
				}
			}
		}
	}


	public function favirate_callback_func()
	{

		extract($_POST);


		$user_id = $this->session->userdata('user_id');
		if (isset($p_id) && !empty($p_id) && isset($action) && !empty($action)) {
			if (!isset($_SESSION['favirate_properties'])) {
				$_SESSION['favirate_properties'] = array();
			}

			if ($action == 'add') {

				$decId = $this->DecryptClientId($p_id);
				$url = base_url() . 'property/' . $productURL;

				$page_title = $productTitle;
				$cur_time = time();
				$datai = array(
					'p_id' => trim($decId),
					'title' => trim($page_title),
					'url' => trim($url),
					'user_id' => $user_id,
					'craeted_at' => $cur_time,
					'updated_at' => $cur_time,
				);

				$res = $this->Common_model->commonInsert('favorite_table', $datai);
				if ($res >= 1) {
					echo 'Added to Favourite';
				} else {
					echo 'Error';
				}
			} else if ($action == 'remove') {
				$decId = $this->DecryptClientId($p_id);


				$result = $this->Common_model->commonQuery("DELETE FROM `favorite_table` WHERE p_id=$decId and user_id=$user_id");

				echo 'Add to Favorite';
			}
		}
	}






	public function sendMailByPHP($args = array())
	{

		extract($args);

		$site_domain_name = $this->global_lib->get_option('site_domain');
		$site_domain_email = $this->global_lib->get_option('site_domain_email');

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: ' . $site_domain_name . '<' . $site_domain_email . '>' . "\r\n";
		$headers .= "X-Mailer: PHP " . phpversion();

		if (mail($to, $subject, $msg, $headers)) {
			return true;
		} else {
			return false;
		}
	}



	public function user_field_validation_callback_func()
	{
		extract($_POST);
		$this->load->model('Common_model');
		$options = array('where' => array($field_type => $field_value));
		$user_exsist = $this->Common_model->commonSelect('users', $options);

		if ($user_exsist->num_rows() > 0) {
			echo 'false';
		} else {
			echo 'true';
		}
		return;
	}

	public function register_user_form_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$this->load->model('Common_model');
		$this->load->library('Global_lib');
		/*$this->load->library('Email_lib');*/
		$cur_time = time();

		if (!isset($user_type) || (isset($user_type) && empty($user_type)))
			$user_type = 'agent';

		$enbale_reg_auto_login = $this->global_lib->get_option('enbale_reg_auto_login');
		$default_user_status_after_reg = $this->global_lib->get_option('default_user_status_after_reg');
		
		
		$user_args = array(
			'user_name' => trim($username),
			'user_pass' => md5(trim($password)),
			'user_email' => trim($email),
			'user_type' => $user_type,
			'user_registered_date' => $cur_time,
			'user_update_date' => $cur_time,
			'user_link_id' => '',
			'user_code' => '',
			'user_verified' => 'N',
			'user_status' => 'N',
		);

		$this->load->helper('string');
		$user_code = random_string('alnum', 12);

		if ($enbale_reg_auto_login == 'Y') {
			$user_args['user_verified'] = 'Y';
			$user_args['user_status'] = 'Y';
		} else if ($default_user_status_after_reg == 'Y') {
			$user_args['user_verified'] = 'Y';
			$user_args['user_status'] = 'Y';
		} else {
			$user_args['user_code'] = $user_code;
		}

		/*$user_id = $this->Common_model->commonInsert('users', $datai);*/
		
		
		
		$user_id   = cms_create_user(  $user_args );
		
		if(!$user_id){
			$output = '<div class="alert alert-success alert-dismissable" >
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				Registered Failed. Contact site admin.
			</div>';	
			header('Content-type: application/json');
			echo json_encode(array('status' => 'failed', 'output' => $output, 'auto_redirect' => 'N'));
			return;
			
		}

		$user_meta = array(
			'first_name' => $first_name,
			'last_name' => $last_name,
		);
		if (isset($photo_url) && !empty($photo_url)) {
			$user_meta['photo_url'] = $photo_url;
		}
		if (isset($att_photo_hidden) && !empty($att_photo_hidden)) {
			$user_meta['photo_url'] = $att_photo_hidden;
		}
		foreach ($user_meta as $key => $val) {
			$datai = array(
				'meta_key' => trim($key),
				'meta_value' => trim($val),
				'user_id' => $user_id
			);
			$this->Common_model->commonInsert('user_meta', $datai);
		}

		$to = $email;

		$args['to_email'] = $email;
		$args['user_id'] = $user_id;



		if ($enbale_reg_auto_login == 'Y'  || $default_user_status_after_reg == 'Y') {
			$args['email_template'] = "register_email";
			$this->email_lib->send_email_notification($args);
		} else {

			$args['url'] = base_url(array('logins', 'account_confirm?email=' . $email . '&user_code=' . $user_code));
			$args['email_template'] = "account_confirmation_email";
			$this->email_lib->send_email_notification($args);
		}


		$auto_redirect = 'N';


		$output = '<div class="alert alert-success alert-dismissable" >
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			Registered Successfully. You can login after admin verification.
		</div>';
		if ($enbale_reg_auto_login == 'Y') {
			

			if (isset($_COOKIE["PHPSESSID"])) {
				session_destroy();
				session_unset();
				session_start();
			}

			$auto_redirect = 'Y';
			$sql = "select * from users where user_id = '" . $user_id . "'";
			$detail = $this->Common_model->commonQuery($sql);
			$site_url = site_url();
			$user_data = $detail->row();
			$newdata = array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'username'  => $username,
				'user_name'     => $username,
				'user_email'     => $email,
				'user_id'     => $user_id,
				'user_type'     => $user_data->user_type,
				'user_status'     => $user_data->user_status,
				'logged_in' => TRUE,
				'site_url' => $site_url
			);
			foreach ($newdata as $k => $v) {
				$_SESSION[$k] = $v;
			}
			$this->session->set_userdata($newdata);
			$output = '<div class="alert alert-success alert-dismissable" >
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				Registered Successfully. Redirecting...
			</div>';
		} else if ($default_user_status_after_reg == 'Y') {
			$datai['user_verified'] = 'Y';
			$datai['user_status'] = 'Y';
			$output = '<div class="alert alert-success alert-dismissable" >
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				Registered Successfully. You can login with your login credentials.
			</div>';
		}

		header('Content-type: application/json');
		echo json_encode(array('status' => 'success', 'output' => $output, 'auto_redirect' => $auto_redirect));
		return;
	}
}
