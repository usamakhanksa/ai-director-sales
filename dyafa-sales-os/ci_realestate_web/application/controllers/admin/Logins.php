<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logins extends MY_Controller
{

	public function index()
	{
		$this->login();
	}


	public function login()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('admin_theme');
		$CI->load->library('Global_lib');
		$site_url = site_url();

		if (isset($_POST['submit'])) {



			clean_post();
			/*foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}*/

			/*$username=$_POST['username'];*/
			$username = $this->db->escape($_POST['username']);
			$username = str_replace("'", "", $username);
			$userpass = $_POST['userpass'];
			$this->load->model('Common_model');
			$sql = "select * from users where user_name='" . $username . "' and user_pass = '" . md5($userpass) . "'";
			$detail = $this->Common_model->commonQuery($sql);

			if ($detail->num_rows() > 0) {

				$data = $detail->row();

				$newdata = array(
					'first_name' => get_user_meta($data->user_id, 'first_name'),
					'last_name' => get_user_meta($data->user_id, 'last_name'),
					'username'  => $username,
					'user_name'     => $data->user_name,
					'user_email'     => $data->user_email,
					'user_id'     => $data->user_id,
					'user_type'     => $data->user_type,
					'user_status'     => $data->user_status,
					'logged_in' => TRUE,
					'site_url' => $site_url
				);
				foreach ($newdata as $k => $v) {
					$_SESSION[$k] = $v;
				}

				/*print_r($_POST); exit;*/
				$this->session->set_userdata($newdata);
				if (isset($_POST['redirect_to']) && !empty($_POST['redirect_to']))
					redirect($_POST['redirect_to'], 'location');
				else
					redirect('/admin/main/', 'location');
			} else {
				$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("Username/Password Mismatch") . '</p>';
				$data['myHelpers'] = $this;
				$data['theme'] = $theme;
				$this->load->view($theme . '/login', $data);
			}
		} else {

			$data['myHelpers'] = $this;
			$data['theme'] = $theme;
			$this->load->view($theme . '/login', $data);
		}
	}

	public function logout()
	{
		session_destroy();
		$newdata = array(
			'first_name',
			'last_name',
			'username',
			'user_name',
			'user_email',
			'user_id',
			'user_type',
			'user_status',
			'site_url'
		);
		foreach ($newdata as $k => $v) {
			unset($_SESSION[$v]);
		}
		$this->session->unset_userdata($newdata);
		$this->session->set_userdata('logged_in', false);
		$_SESSION['logged_in'] = false;
		$_SESSION['msg'] = '<p class="success_msg">' . mlx_get_lang("Logged Out Successfully") . '</p>';
		redirect('/admin/logins', 'location');
	}

	public function forgot_password()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$CI->load->library('Global_lib');

		if (isset($_POST['submit'])) {
			foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}

			$email = $_POST['email'];
			$this->load->model('Common_model');
			$sql = "select * from users where user_email='" . $email . "'";
			$detail = $this->Common_model->commonQuery($sql);

			if ($detail->num_rows() > 0) {
				$data = $detail->row();

				$this->load->helper('string');
				$user_code = random_string('alnum', 12);

				$datai = array(
					'user_code' => $user_code
				);
				$this->Common_model->commonUpdate('users', $datai, 'user_id', $data->user_id);

				$to = $email;

				$this->load->library('Email_lib');

				$args['to_email'] = $email;

				$args['email_template'] = "forgot_password_email";
				$args['user_id'] = $data->user_id;
				$args['user_code'] = $user_code;
				$args['forgot_password_link_url'] = base_url(array('logins', 'reset_password?email=' . $email . '&user_code=' . $user_code));

				$this->email_lib->send_email_notification($args);

				/*$site_domain_name = $this->global_lib->get_option('site_domain');
				$site_domain_email = $this->global_lib->get_option('site_domain_email');
				
				$first_name = $this->global_lib->get_user_meta($data->user_id,'first_name');
				$last_name = $this->global_lib->get_user_meta($data->user_id,'last_name');
			    $full_name = strtolower($first_name).' '.strtolower($last_name);
				
				$reset_password_link = base_url(array('logins','reset_password?email='.$email.'&user_code='.$user_code));
				$subject = "Reset Password Link";
				$htmlContent = ' 
					<html> 
					<head> 
						<title>'.mlx_get_lang("Reset Password Link").'</title> 
					</head> 
					<body> 
						<p>'.mlx_get_lang("Hello").' <strong>'.$full_name.'</strong></p>
						<p>'.mlx_get_lang("Please click on folowing link to reset your password.").'</p>
						<p><a href="'.$reset_password_link.'" target="_blank">'.$reset_password_link.'</a></p>
					</body> 
					</html>'; 
				
				$args = array();
				$args['CI'] = $CI; 
				$args['to'] = $to; 
				$args['msg'] = $htmlContent; 
				$args['subject'] = $subject; 
				
				$default_mailer = $this->global_lib->get_option('default_mailer');
				
				$response = false;
				if($default_mailer == "php_mail" || empty($default_mailer))			
					$response = $this->sendMailByPHP($args);
				else if($default_mailer == "smtp")			
					$response = $this->sendMailBySMTP($args);*/

				$_SESSION['msg'] = '<p class="success_msg">' . mlx_get_lang("Please check your email for reset password link") . '</p>';
				redirect('/logins/forgot_password', 'location');
			} else {
				$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("Email not registered") . '</p>';
			}
		}

		$data['myHelpers'] = $this;
		$data['theme'] = $theme;
		$this->load->view($theme . '/forgot_password', $data);
	}


	public function account_confirm()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$CI->load->library('Global_lib');


		if (isset($_GET['email'])) {
			$email =  $_GET['email'];
		} else {
			redirect("/", 'location');
		}
		if (isset($_GET['user_code'])) {
			$user_code =  $_GET['user_code'];
		} else {
			redirect("/", 'location');
		}


		$this->load->model('Common_model');
		$sql = "select * from users where user_email='" . $email . "' and user_code='" . $user_code . "' ";
		$detail = $this->Common_model->commonQuery($sql);

		if ($detail->num_rows() > 0) {
			$data = $detail->row();


			$datai = array(
				'user_verified' => 'Y',
				'user_status' => 'Y',
				'user_code' => '',
			);
			$this->Common_model->commonUpdate('users', $datai, 'user_id', $data->user_id);

			$to = $email;

			$this->load->library('Email_lib');

			$args['to_email'] = $email;

			$args['email_template'] = "account_confirmed_email";
			$args['user_id'] = $data->user_id;

			$this->email_lib->send_email_notification($args);


			$_SESSION['msg'] = '<p class="success_msg">' . mlx_get_lang("Account Confirmed successfully") . '</p>';
			redirect('/', 'location');
		} else {
			$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("Email not registered") . '</p>';
		}


		redirect("/", 'location');
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

	public function sendMailBySMTP($args = array())
	{

		extract($args);
		$CI->load->library('email');

		$email_setting = $CI->global_lib->get_option('email_setting');
		$website_title = $CI->global_lib->get_option('website_title');
		$email_setting = json_decode($email_setting, true);

		$config = $email_setting;

		$config['protocol'] = "smtp";
		$config['mailtype'] = "html";
		$config['charset'] = "utf-8";
		$config['validation'] = TRUE;
		$this->email->initialize($config);

		$this->email->set_newline("\r\n");
		$this->email->clear();
		$htmlContent = $msg;

		$this->email->to($to);
		$this->email->from($email_setting['smtp_user'], $website_title);
		$this->email->subject($subject);
		$this->email->message($htmlContent);

		return $this->email->send();
	}


	public function reset_password()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$CI->load->library('Global_lib');

		if (isset($_POST['submit'])) {
			foreach ($_POST as $k => $v) {
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]', '', $_POST[$k]);
			}

			if ($_POST['password'] == $_POST['repeat_password']) {
				$user_id = $this->global_lib->DecryptClientId($_POST['user_id']);
				$this->load->model('Common_model');
				$sql = "select * from users where user_id='" . $user_id . "'";
				$detail = $this->Common_model->commonQuery($sql);

				if ($detail->num_rows() > 0) {
					$user_row = $detail->row();

					$datai = array(
						'user_pass' => md5($_POST['password']),
						'user_code' => ''
					);
					$this->Common_model->commonUpdate('users', $datai, 'user_id', $user_row->user_id);


					$_SESSION['msg'] = '<p class="success_msg">' . mlx_get_lang("Password Reset Successfully") . '</p>';
					redirect('/logins', 'location');
				}
			} else {
				$_SESSION['msg'] = '<p class="error_msg">Password/Repeat Password must be same.</p>';
				$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' .  $_SERVER['HTTP_HOST'];
				$cur_url = $base_url . $_SERVER["REQUEST_URI"];
				redirect($cur_url, 'location');
			}
		}

		if (isset($_GET['email']) && isset($_GET['user_code'])) {
			$email = $_GET['email'];
			$user_code = $_GET['user_code'];
			$sql = "select user_id from users where user_email='" . $email . "' and user_code = '" . $user_code . "'";
			$detail = $this->Common_model->commonQuery($sql);

			if ($detail->num_rows() > 0) {
				$user_row = $detail->row();

				$data['user_id'] = $this->global_lib->EncryptClientId($user_row->user_id);
			} else {
				$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("Invalid Reset Password Link") . '</p>';
			}
		} else {
			redirect('/logins', 'location');
		}




		$data['theme'] = $theme;
		$this->load->view($theme . '/reset_password', $data);
	}
}
