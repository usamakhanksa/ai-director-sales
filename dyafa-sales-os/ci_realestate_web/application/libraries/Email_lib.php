<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP; 
 
class Email_lib{ 
    
    var $api_error; 
	var $default_mailer;
    var $smtp_host , $smtp_port , $smtp_username , $smtp_password , $smtp_encryption , $smtp_auth ;
	
	var $email_template_shortcodes;
	
    function __construct(){ 
        
		$this->api_error = ''; 
        $CI =& get_instance(); 
        
        	
		$email_setting = get_option('email_setting');
		$email_setting = json_decode($email_setting);
		
		
		
		$this->email_template_shortcodes = '';
		
		$this->default_mailer = get_option('default_mailer');
		
		if($this->default_mailer == 'smtp'){
			
			if(	isset($email_setting->smtp_host) && !empty($email_setting->smtp_host) ){
				$this->smtp_host  = $email_setting->smtp_host;
			}else{ echo "SMTP host not set ..."; exit;}
			
			if(	isset($email_setting->smtp_port) && !empty($email_setting->smtp_port) ){
				$this->smtp_port  = $email_setting->smtp_port;
			}else{ echo "SMTP port not set ..."; exit;}
			
			if(	isset($email_setting->smtp_username) && !empty($email_setting->smtp_username) ){
				$this->smtp_username  = $email_setting->smtp_username;
			}else{ echo "SMTP username not set ..."; exit;}
			
			if(	isset($email_setting->smtp_password) && !empty($email_setting->smtp_password) ){
				$this->smtp_password  = $email_setting->smtp_password;
			}else{ echo "SMTP pasword not set ..."; exit;}
			
			if(	isset($email_setting->smtp_encryption) && !empty($email_setting->smtp_encryption) ){
				$this->smtp_encryption  = $email_setting->smtp_encryption;
			}else{ echo "SMTP encryption not set ..."; exit;}
			
			if(	isset($email_setting->smtp_auth) && !empty($email_setting->smtp_auth) ){
				$this->smtp_auth  = $email_setting->smtp_auth;
			}else{ echo "SMTP host not set ..."; exit;}
			
			require_once(APPPATH . 'third_party/php_mailer/vendor/autoload.php'); 
		}
		
		
		
		
    } 
	
	function get_email_template_details($email_template){
		
		$CI =& get_instance();
		
		$CI->load->library('Global_lib');
		
		$email_templates = get_option('email_templates');
		if(isset($email_templates) && !empty($email_templates))
		{
			$email_templates = json_decode($email_templates,true);
		}
		
		/*print_r($email_templates); exit;*/
		
		if(count($email_templates) > 0){
			
			foreach($email_templates  as $key => $val ){
				if($key == $email_template){
					return json_encode($val);
				}
			}
		}
		return 'invalid_template';
		
	}
	
	function get_email_template_shortcodes($args = array()){
		
		$CI =& get_instance();
		$CI->load->library('Global_lib');
		$CI->load->model('Common_model');
		$email_template_shortcodes = $CI->config->item('email_template_shortcodes');
		$email_template_shortcodes_parsed = array();
		extract($args);
		
		
		if(!empty($email_template_shortcodes)  )
		{
			$n=0;
			$user_data = array();
			if( isset($user_id) && !empty($user_id)){
				
				
				$options=array('where'=>array("user_id" => $user_id));
				$user_exists = $CI->Common_model->commonSelect('users',$options);
				
				if($user_exists->num_rows() > 0 )
				{
					$user_data = (array) $user_exists->row(); 	
					
				}	
			}
			$property_data = array();
			if( isset($property_id) && !empty($property_id)){
				
				
				$options=array('where'=>array("p_id" => $property_id));
				$property_exists = $CI->Common_model->commonSelect('properties',$options);
				
				if($property_exists->num_rows() > 0 )
				{
					$property_data = (array) $property_exists->row(); 	
					
				}	
			}
			
			
			foreach($email_template_shortcodes as $shortcode_type => $shortcode_list)
			{
				foreach($shortcode_list as $k => $v)
				{
					$key = str_replace("}","",str_replace("{","",$k));
					
					if($shortcode_type == 'user_shortcode' && isset($user_id) && !empty($user_id)){
						if(isset($user_data['user_id']) && $user_data['user_id'] == $user_id){
							
							
							
							$email_template_shortcodes_parsed[$k] =	$user_data[$key];
						}
					}
					
					if($shortcode_type == 'property_shortcode' && isset($property_id) && !empty($property_id)){
						if(isset($property_data['p_id']) && $property_data['p_id'] == $property_id){
							
							$key = str_replace("property_","",$key);
							
							if(isset($property_data[$key]))
							{
								$email_template_shortcodes_parsed[$k] =	$property_data[$key];
							}else if($key == 'link'){
								
								
									$segments = array('property',$property_data['slug']."~".$property_data['p_id']); 
									$link = str_replace("/admin","",  site_url($segments));
									$email_template_shortcodes_parsed[$k] =	$link;
								
							}else if($key == 'title_linkable'){
								
									$title = $property_data['title'];
									$segments = array('property',$property_data['slug']."~".$property_data['p_id']); 
									$link = str_replace("/admin","",  site_url($segments));
									$email_template_shortcodes_parsed[$k] = "<a href='$link'>$title</a>";
								
							}	
						}
					}
					
					
					if($shortcode_type == 'option_shortcode'){
						$option_val = $CI->global_lib->get_option($key);
						
						$email_template_shortcodes_parsed[$k] =	$option_val;
					}
					if($shortcode_type == 'keyword_shortcode'){
						if(function_exists($key))
							$email_template_shortcodes_parsed[$k] =	call_user_func($key);
					}
					if($shortcode_type == 'link_shortcode'){
						
						if(isset($args[$key.'_url']))
							$email_template_shortcodes_parsed[$k] =	$args[$key.'_url'];	
					}
					if($shortcode_type == 'user_meta_shortcode' && isset($user_id) && !empty($user_id)){
						
						$user_meta_val = $CI->global_lib->get_user_meta($user_id , $key);
						$email_template_shortcodes_parsed[$k] =	$user_meta_val;
					}
				}
			}
		}		
				
		
		$this->email_template_shortcodes = $email_template_shortcodes_parsed;
	}
 
	function get_email_template_parsed($message){
		
		/*print_r($this->email_template_shortcodes);*/
		if(count($this->email_template_shortcodes) > 0){
			
			foreach($this->email_template_shortcodes as $shortcode => $replace_val){
				$message = str_replace($shortcode , $replace_val , $message);
			}
		}
		
		return $message;
	}
	
	function get_email_template_parsed_postvars($message , $post){
		
		if(count($post) > 0){
			
			foreach($post as $shortcode => $replace_val){
				$message = str_replace("{".$shortcode."}" , $replace_val , $message);
			}
		}
		
		return $message;
	}
	
 
 
	function send_email_notification($args = array()){
		
		
		
		$CI =& get_instance();
		/*$email_templates_sections = $CI->config->item('email_templates_sections');*/
		
		extract($args);
		
		//print_r($args);
		
		if(!isset($to_email) || empty($to_email)){ echo "no email selected"; exit;}
		if(!isset($email_template) || empty($email_template)){ echo "no email template selected"; exit;}

		$template_details =  $this->get_email_template_details($email_template);
		if($template_details == 'invalid_template' ) { echo 'wrong or invalid template selected'; exit;}

		$template_details = json_decode($template_details,true);
		extract($template_details);
		
		$this->get_email_template_shortcodes($args);
		
		$subject = $this->get_email_template_parsed($subject);
		$message = $this->get_email_template_parsed($message);
		
		if(isset($post)){
			$message = $this->get_email_template_parsed_postvars($message , $post);
		}
		/*print_r($subject); 
		print_r($message); return;
		exit;*/
		
		if($this->default_mailer == '' || $this->default_mailer == 'php_mail'){
		
			$nargs = array();
			$nargs ['to'] = $to_email;
			$nargs ['subject'] = $subject;
			$nargs ['msg'] = $message;
			
			$this->sendMailByPHP($nargs);
			
			return false;
		}
		
		
		$mail = new PHPMailer(true);
		
		try {
			
			$mail->SMTPDebug = 0;                      
			$mail->isSMTP(true);                       
			 
			 
			$mail->Host       = $this->smtp_host;                    
			$mail->SMTPAuth   = $this->smtp_auth;
			$mail->Username   = $this->smtp_username;
			$mail->Password   = $this->smtp_password;
			
			
			$mail->SMTPSecure = $this->smtp_encryption;
			
			$mail->Port       = $this->smtp_port;                                    
			
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);


			
			$mail->setFrom($this->smtp_username);
			
			
			$mail->addAddress($to_email);               

			
			$mail->isHTML(true);                                  
			$mail->Subject = $subject;
			$mail->Body    = $message;
			
			
			$mail->send();
			/*echo 'Message has been sent';*/
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
		
		
	}
	
	
	
	
	
	function send_email_message($args = array()){
		
		
		
		$CI =& get_instance();
		
		
		extract($args);
		
		if(!isset($to_email) || empty($to_email)){ echo "no email selected"; exit;}
		

		/*$subject = $this->get_email_template_parsed($subject);
		$message = $this->get_email_template_parsed($message);*/
		
		
		/*print_r($subject); 
		print_r($message); return;
		exit;*/
		
		if($this->default_mailer == '' || $this->default_mailer == 'php_mail'){
		
			$nargs = array();
			$nargs ['to'] = $to_email;
			$nargs ['subject'] = $subject;
			$nargs ['msg'] = $message;
			
			$this->sendMailByPHP($nargs);
			
			return false;
		}
		
		
		$mail = new PHPMailer(true);
		
		try {
			
			$mail->SMTPDebug = 0;                      
			$mail->isSMTP(true);                       
			 
			 
			$mail->Host       = $this->smtp_host;                    
			$mail->SMTPAuth   = $this->smtp_auth;
			$mail->Username   = $this->smtp_username;
			$mail->Password   = $this->smtp_password;
			
			
			$mail->SMTPSecure = $this->smtp_encryption;
			
			$mail->Port       = $this->smtp_port;                                    
			
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);


			
			$mail->setFrom($this->smtp_username);
			
			
			$mail->addAddress($to_email);               

			if(isset($from_email))
				$mail->From = $from_email;
			if(isset($from))
				$mail->FromName = $from;

			
			
			$mail->isHTML(true);                                  
			$mail->Subject = $subject;
			$mail->Body    = $message;
			
			
			$mail->send();
			/*echo 'Message has been sent';*/
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
		
		
	}
	
	public function sendMailByPHP($args = array()){
		
		extract($args);
		
		$CI =& get_instance();
		$CI->load->library('Global_lib');
		$site_domain_name = $CI->global_lib->get_option('site_domain');
		$site_domain_email = $CI->global_lib->get_option('site_domain_email');
		
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
 
 
	function test_email_notifications($args = array()){
		
		
		
		$mail = new PHPMailer(true);
		$CI =& get_instance();
		$email_templates_sections = $CI->config->item('email_templates_sections');
		
		extract($args);
		
		if(!isset($to_email) || empty($to_email)){ echo "no email selected"; exit;}
		if(!isset($email_template) || empty($email_template)){ echo "no email template selected"; exit;}

		$template_details =  $this->get_email_template_details($email_template);
		if($template_details == 'invalid_template' ) { echo 'wrong or invalid template selected'; exit;}

		$template_details = json_decode($template_details,true);
		extract($template_details);
		
		/*print_r($template_details); exit;*/
		
		$args = array();
		$this->get_email_template_shortcodes($args);
		
		$subject = $this->get_email_template_parsed($subject);
		$message = $this->get_email_template_parsed($message);
		
		try {
			
			$mail->SMTPDebug = 0;                      
			$mail->isSMTP(true);                       
			 
			 
			$mail->Host       = $this->smtp_host;                    
			$mail->SMTPAuth   = $this->smtp_auth;
			$mail->Username   = $this->smtp_username;
			$mail->Password   = $this->smtp_password;
			
			
			$mail->SMTPSecure = $this->smtp_encryption;
			
			$mail->Port       = $this->smtp_port;                                    
			
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);


			$mail->setFrom($this->smtp_username);
			
			
			$mail->addAddress($to_email);               

			$mail->isHTML(true);                                  
			$mail->Subject = $subject;
			$mail->Body    = $message;
			
			
			$mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
		
		
	}
 
	
}