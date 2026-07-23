<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_ajax_lib
{

	var $site_options = "";




	public function site_migrations($args = array())
	{
		
		

		if (empty($args)) return false;
		if (!isset($args['migration_file'])) return false;
		if (!isset($args['migration_key'])) return false;
		if (!isset($args['iteration'])) return false;
		$CI = &get_instance();
		extract($args);
		$CI->load->library('Global_lib');



		$iteration = $args['iteration'];

		$return = "error";

		if (file_exists($migration_file)) {
			
			$file_content = file_get_contents($migration_file);

			$migration_value = get_option($migration_key);

			$file_content = json_decode($file_content, true);
			$last_version = 0;
			
			
			$cur_version_db = $migration_value;

			

			foreach ($file_content as $version => $statements) {
				$cur_version_cms = $version;
				if ( empty($cur_version_db) ||    version_compare($cur_version_db, $cur_version_cms, '<')) {
					
					
					if (isset($statements[$iteration])) {
						foreach ($statements[$iteration] as $stm) {
							if(is_array($stm))
								$sql_statement = current($stm);
							else
								$sql_statement = $stm;
							$CI->Common_model->commonQuery($sql_statement);
						}
					}
				}
				$last_version = $version;
			}

			update_option($migration_key, $last_version);
			$return = "success";
		}else {	echo $migration_file;}

		return $return;
	}


	public function submit_contact_form_callback_func() {
	  
	  $CI =& get_instance();		
	  //$this->load->model('Common_model');
	  //$this->load->library('Global_lib');
	  
	  extract($_POST);
	  
		$is_recaptcha_enable = false;
		$isPlugAct = isPluginActive('google_recaptcha');
		if($isPlugAct == true)
		{
			$is_recaptcha_enable = true;
		}/**/
	  
	  $is_verifieid = true;
	  if($is_recaptcha_enable)
	  {
	  
		if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
		{ 
				$recaptcha_secret_key = get_option('recaptcha_secret_key');
				$secretKey = $recaptcha_secret_key; 
				$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']); 
				$responseData = json_decode($verifyResponse); 
				if($responseData->success)
				{ 
					$is_verifieid = true;
				}
				else{ 
					$return_type = 'error';
					$output = '<div class="alert alert-danger alert-dismissable" style="margin-top:0px;">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						Robot verification failed, please try again.
					</div>';
					$is_verifieid = false;
				} 
		}
		else
		{
			$return_type = 'error';
			$output = '<div class="alert alert-danger alert-dismissable" style="margin-top:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				Please check on the reCAPTCHA box.
			</div>';
			$is_verifieid = false;
		}
	  }
	  $CI->load->library('Email_lib');
	  
		if($is_verifieid)
		{
			
			$args = array();
			$args ['post'] = $_POST;
			if(isset($contact_email))
			{
			  /*$args['to_email'] = $contact_email;
				
			  $args['email_template'] = "contact_us_email";	
			  $CI->email_lib->send_email_notification($args);*/	
			  
				$em_args = array();
				$em_args ['post'] = $_POST;
				$em_args['user_email'] = $contact_email;
				$em_args['email_template'] = "contact_us_email";
				do_action('send_email_to_user_email', $em_args);
			  
			}	
			
				
				
			/*$em_args = array();
			$em_args['post'] = $_POST;
			$CI->global_lib->send_email_notifications_to_admin("contact_us_email_admin" ,$em_args );	*/
				
			$em_args = array();
			$em_args['post'] = $_POST;
			$em_args['email_template'] = "contact_us_email_admin";
			/*$em_args['subject'] = $contact_subject;
			$em_args['message'] = $contact_message;
			$em_args['sending'] = "general_message";
			$em_args['email_template'] = "";*/
			do_action('send_email_to_admin', $em_args);	
				
				
			$return_type = 'success';
			$output = '<div class="alert alert-success alert-dismissable" style="margin-top:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				'.mlx_get_lang('Your contact request has been submitted successfully.').'
			</div>';
		
		}		
				
		  
							
		  header('Content-type: application/json');
		  echo json_encode(array('return_type'=>$return_type,'output' => $output));
		  return;
		  
	}

	
}
