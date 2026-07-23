<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_mailer_lib
{



	public function register_user_form_callback_func()
	{
		extract($_POST);
		$CI =& get_instance();
		$CI->load->model('Common_model');
		/*$this->load->library('Global_lib');
		$this->load->library('Email_lib');*/
		$cur_time = time();
		
		if(!isset($user_type) || (isset($user_type) && empty($user_type)))
			$user_type = 'agent';
		
		$enbale_reg_auto_login = get_option('enbale_reg_auto_login');
		$default_user_status_after_reg = get_option('default_user_status_after_reg');
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
		
		$CI->load->helper('string');
		$user_code = random_string('alnum',12);
		
		if($enbale_reg_auto_login == 'Y')
		{
			$user_args['user_verified'] = 'Y';
			$user_args['user_status'] = 'Y';
		}
		else if($default_user_status_after_reg == 'Y')
		{
			$user_args['user_verified'] = 'Y';
			$user_args['user_status'] = 'Y';
		}else{
			$user_args['user_code'] = $user_code;
		}
		
		/*$user_id = $this->Common_model->commonInsert('users',$datai);*/
		
		if(isset($user_meta) && is_array($user_meta)){
			$user_meta ['first_name'] = $first_name;
			$user_meta ['last_name'] = $last_name;
			
		}else {
		
			$user_meta = array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					);
		}			
		if(isset($photo_url) && !empty($photo_url))
		{
			$user_meta['photo_url'] = $photo_url;
		}
		if(isset($att_photo_hidden) && !empty($att_photo_hidden))
		{
			$user_meta['photo_url'] = $att_photo_hidden;
		}
		
		
		
		$user_args ['user_meta'] = $user_meta;
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
		
	
		$to = $email; 
		
		$args['to_email'] = $email;
		$args['user_id'] = $user_id;	
		
		
		
		if($enbale_reg_auto_login == 'Y'  || $default_user_status_after_reg == 'Y')
	    {
			$args['email_template'] = "register_email";	
			/*$this->email_lib->send_email_notification($args);	*/
			
		}else{
			
			
			$args['account_confirmation_link_url'] = base_url(array('logins','account_confirm?email='.$email.'&user_code='.$user_code));
			$args['email_template'] = "account_confirmation_email";	
			/*$this->email_lib->send_email_notification($args);	*/
		}
		
		/*$em_args = array();*/
		$args ['post'] = $_POST;
		$args['user_email'] = $email;
		/*$args['email_template'] = "contact_us_email";*/
		do_action('send_email_to_user_email', $args);
		
		
		/*$this->global_lib->send_email_notifications_to_admin("new_user_registered_email_admin");*/
		$args = array();
		$args['post'] = $_POST;
		$args['email_template'] = "new_user_registered_email_admin";
		
		do_action('send_email_to_admin', $args);	
		
		
		
		$auto_redirect = 'N';
		
		
		$output = '<div class="alert alert-success alert-dismissable" >
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			Registered Successfully. You can login after admin verification.
		</div>';
		if($enbale_reg_auto_login == 'Y')
		{
			
			
			if (isset($_COOKIE["PHPSESSID"])) {
				session_destroy();
				session_unset();
				session_start();
			}
			
			$auto_redirect = 'Y';
			$sql="select * from users where user_id = '".$user_id."'"; 
			$detail = $CI->Common_model->commonQuery($sql); 
			$site_url = site_url();	
			$user_data=$detail->row();
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
			foreach($newdata as $k=>$v)
			{
				$_SESSION[$k] = $v;
			}
			$CI->session->set_userdata($newdata);
			$output = '<div class="alert alert-success alert-dismissable" >
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				Registered Successfully. Redirecting...
			</div>';
		}
		else if($default_user_status_after_reg == 'Y')
		{
			$datai['user_verified'] = 'Y';
			$datai['user_status'] = 'Y';
			$output = '<div class="alert alert-success alert-dismissable" >
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				Registered Successfully. You can login with your login credentials.
			</div>';
		}
		
		header('Content-type: application/json');
		echo json_encode(array('status'=>'success','output' => $output,'auto_redirect' => $auto_redirect));
		return;
		
	}


	public function submit_contact_form_callback_func() {
	  
	  $CI =& get_instance();		
	  
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
			
			
			
			$em_args = array();
			$em_args ['post'] = $_POST;
			do_action('send_email_contact_form_after', $em_args);
			
				
				
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
	
	
	
	public function submit_site_contact_agent_form_callback() {
	  
	  $CI =& get_instance();		
	  $CI->load->model('Common_model');
	  $CI->load->library('Global_lib');
	  
	  extract($_POST);
	  
	  
	  $is_recaptcha_enable = false;
	  
		$isPlugAct = isPluginActive('google_recaptcha');
		if($isPlugAct == true)
		{			
			$is_recaptcha_enable = true;
		}
	  
	  $is_verifieid = true;
	  if($is_recaptcha_enable)
	  {
		  if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
			{ 
				$recaptcha_secret_key = $CI->global_lib->get_option('recaptcha_secret_key');
				$secretKey = $recaptcha_secret_key; 
				$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']); 
				$responseData = json_decode($verifyResponse); 
				if($responseData->success)
				{ 
					$is_verifieid = true;
				}
				else
				{ 
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
	  
	  if($is_verifieid)
	  {
				$property_id = DecryptClientID($p_id);
				
				$sql = "select prop.*,
				   pld.title as title , 
				   pld.price as price,
				   u.user_email,
				   pt.title as prop_type_title
				   from properties  as prop 
				   inner join property_types as pt on pt.pt_id = prop.property_type
					inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
				   inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$CI->default_language'
				   where prop.p_id = '$property_id'";
				$property_result = $CI->Common_model->commonQuery($sql );
				
				if($property_result->num_rows() >0)
				{
					$single_property = $property_result->row();
					
					$to = $single_property->user_email; 
					$user_id = $single_property->created_by; 
					$from = trim($email); 
					$fromName = ucfirst(trim($name));
					
					$is_price_set = true;
					if(isset($CI->enable_multi_lang) && $CI->enable_multi_lang == true)
					{
						$def_lang_code = $CI->default_language;
						
						$ret_data = $CI->global_lib->get_property_price_by_lang($single_property->p_id,$def_lang_code);
						if(!empty($ret_data))
						{
							$single_property->price = $ret_data['price'];
							$currency_symbol = $ret_data['currency'];
						}
						else
						{
							$is_price_set = false;
						}
						
					}
					
					
					$subject = "A Contact Agent Form Submitted.";
					$htmlContent = ' 
						<html> 
						<head> 
							<title>A Contact Form Submitted</title> 
						</head> 
						<body> 
							<h1>A Contact agent form submitted with following details :- </h1> 
							<table cellspacing="0" style="border: 1px solid #dddddd; width: 100%; text-align:left;"> 
								<tr> 
									<th>Name </th><td>'.trim($name).'</td> 
								</tr> 
								<tr style="background-color: #e0e0e0;"> 
									<th>Email </th><td>'.trim($email).'</td> 
								</tr> 
								<tr> 
									<th>Message </th><td>'.trim($message).'</td> 
								</tr> 
							</table> 
							<br>
							<h3 style="text-align:center;">Property Details</h3>
							<table cellspacing="0" style="border: 1px solid #dddddd; width: 100%;  text-align:left;"> 
								<tr> 
									<th>Title </th><td>'.ucfirst(stripslashes($single_property->title)).'</td> 
								</tr> 
								<tr> 
									<th>Address </th><td>'.$single_property->address.'</td> 
								</tr> 
								<tr> 
									<th>Price </th><td>';
								/*if($is_price_set)
								{
									$htmlContent .=  $currency_symbol.$this->global_lib->moneyFormatDollar($single_property->price); 
									if($single_property->property_for == 'Rent'){ $htmlContent .= '/'.mlx_get_lang('Month'); } 
								}
								else{
									$htmlContent .=  mlx_get_lang('Price Not Set');
								}*/
					$htmlContent .= '</td> 
								</tr> 
								<tr> 
									<th>Type </th><td>'.mlx_get_lang(ucfirst($single_property->prop_type_title)).'</td> 
								</tr> 
								<tr> 
									<th>Size </th><td>'.$single_property->size.' '.mlx_get_lang('Sq. Foot').'</td> 
								</tr> 
								<tr> 
									<th>Bedroom</th><td>'.$single_property->bedroom.'</td> 
								</tr> 
								<tr> 
									<th>Bathroom </th><td>'.$single_property->bathroom.'</td> 
								</tr> 
							</table> 
						</body> 
						</html>'; 
					
					
					$em_args = array();
					$em_args ['post'] = $_POST;
					$em_args['to_email'] = $to;
					$em_args['user_id'] = $user_id;
					$em_args['sending'] = "general_message";
					$em_args['subject'] = "Property Contact Form Submitted";
					$em_args['message'] = $htmlContent;
					$em_args['from_email'] = $_POST['email'];
					$em_args['from'] = $_POST['name'];
					do_action('send_email_contact_agent', $em_args);
					
					
					$return_type = 'success';
					$output = '<div class="alert alert-success alert-dismissable" style="margin-top:0px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							Your contact agent request has been submitted successfully.
						</div>';
					
				}	
				else{ 
					$return_type = 'error';
					$output = '<div class="alert alert-danger alert-dismissable" style="margin-top:0px;">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						Invalid Property.
					</div>';
				} 					
		}	
		  
		  header('Content-type: application/json');
		  echo json_encode(array('return_type'=>$return_type,'output' => $output));
		  return;
		  
	}
	
	
	public function sendMailByPHP($args = array()){
		
		extract($args);
		
		$site_domain_name = get_option('site_domain');
		$site_domain_email = get_option('site_domain_email');
		
		$headers = "MIME-Version: 1.0" . "\r\n"; 
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
		$headers .= 'From: '.$site_domain_name.'<'.$site_domain_email.'>' . "\r\n";
		$headers .= "X-Mailer: PHP ". phpversion();
		
		if(mail($to, $subject, $msg, $headers)){
			return true;
		}else{
			return false;
		}
		
	}	
	

	
}
