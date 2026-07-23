<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class Package_lib
{


	public function getToken($length)
	{
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
		//$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet);

		for ($i = 0; $i < $length; $i++) {
			$token .= $codeAlphabet[rand(0, $max - 1)];
		}

		return $token;
	}

	public function add_credit_uses($credit_uses_for, $using_id, $uses_type, $user_id)
	{

		$CI = &get_instance();
		$datai = array(
			'credit_uses_for' => $credit_uses_for,
			'using_id' => $using_id,
			'uses_type' => $uses_type,
			'user_id' => $user_id
		);

		if (isset($CI->credit_id))
			$datai['credit_id'] = $CI->credit_id;

		$CI->Common_model->commonInsert('credit_uses', $datai);
	}

	public function is_subscription_expires()
	{

		$CI = &get_instance();
		$user_type = $CI->session->userdata('user_type');

		if ($user_type == 'admin') {
			return false;
		} else {
			if ($CI->is_subscription == 'Y') {
				$user_id = $CI->session->userdata('user_id');
				$subscription_credit = $this->get_credits_by_user_id($user_id, 'subscription_credit');

				if ($subscription_credit > time()) {
					/*echo date("d/m/Y",$subscription_credit) . "  -  " . date("d/m/Y",time());*/
					return true;
				} else
					return false;
			}
		}
		return false;
	}

	public function check_credit_used($uses_for,  $using_id, $uses_type)
	{

		$CI = &get_instance();
		$result = $CI->Common_model->commonQuery("select * from credit_uses where credit_uses_for = '$uses_for' and  
										using_id = $using_id and 
										uses_type = '$uses_type'");
		if ($result->num_rows() > 0)
			return true;
		else
			return false;
	}



	public function get_credits_by_user_id($user_id, $slug)
	{

		$CI = &get_instance();
		$CI->load->library('Global_lib');
		$ret_val = $CI->global_lib->get_user_meta($user_id, $slug);
		if (empty($ret_val))
			$ret_val = 0;
		return $ret_val;
	}

	public function get_credit_id_by_user_id($user_id, $credit_for, $credit_type)
	{

		$CI = &get_instance();

		/***
		$credit_for, $credit_type
		property      post_property
		 **/
		$cur_time = time();
		$credits_result = $CI->Common_model->commonQuery("select * from credits  
							where user_id = '$user_id' and 
							status='Active' and 
							updated_credit > 0 and
							credit_for='$credit_for' and 
							credit_type='$credit_type' and 	
							(credit_expires= 0 or credit_expires > $cur_time)
							ORDER BY credit_id ASC
							limit 1  ");

		$credit_id = 0;
		if ($credits_result->num_rows() > 0) {
			$credits_row = $credits_result->row();
			$credit_id = $credits_row->credit_id;
		}
		return $credit_id;
	}

	public function update_credits_updated_credit_for_user($credit_id)
	{

		$CI = &get_instance();
		$CI->load->model('Common_model');
		/***
		$credit_for, $credit_type
		property      post_property
		 **/
		$cur_time = time();
		$credits_result = $CI->Common_model->commonQuery("update credits  
							set updated_credit = (updated_credit - 1)
							where credit_id = $credit_id ");
		//echo "ho gaya. baat khatam ";	
	}


	public function update_credits_by_user_id($user_id, $slug, $action = '', $action_val = 1)
	{

		$CI = &get_instance();
		$CI->load->library('Global_lib');
		$ret_val = $CI->global_lib->get_user_meta($user_id, $slug);
		if ($ret_val != '') {
			if ($action == 'minus_credit') {
				$CI->Common_model->commonQuery("UPDATE user_meta set meta_value = meta_value - $action_val where user_id = $user_id and meta_key = '$slug'");
			} else if ($action == 'add_credit') {
				$CI->Common_model->commonQuery("UPDATE user_meta set meta_value = meta_value + $action_val where user_id = $user_id and meta_key = '$slug'");
			}
		} else {
			$this->update_user_meta_credit($user_id, $slug, $action_val);
		}
	}

	public function get_features_by_package_id_old($id)
	{

		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from package_features where package_id = '$id'");

		$pkg = $CI->Common_model->commonQuery("select * from packages where package_id = '$id'");

		if ($query->num_rows() > 0) {
			$output = '';
			// $output .= '<p class="group inner list-group-item-text">';
			foreach ($query->result() as $key => $row) {
				if ($row->feature_for === 'subscription') {
					//var_dump($row->feature_for);exit;
					//$output .=	'<div class="ribbon-wrapper"> <div class="ribbon bg-primary">'.ucfirst($pkg->row()->package_type).'</div></div>';
					$output = '<div class="wrapper subscription">
						<div class="ribbon-wrapper-green"><div class="ribbon-green">' . ucfirst($pkg->row()->package_type) . '</div></div></div>';
					$output .= '<h4 class="text-center">' . ucfirst($row->feature_for) . '</h4>';

					$subs = explode('-', $row->feature_type);
					$output .= $row->feature_value . ' ';
					$life = '';
					if ($subs[0] == 'daily') {
						if ($row->feature_value == 1) {
							$life = 'Day';
						} else {
							$life = 'Daily';
						}
					} elseif ($subs[0] == 'weekly') {
						if ($row->feature_value == 1) {
							$life = 'Week';
						} else {
							$life = 'weekly';
						}
						$life = 'Months';
					} elseif ($subs[0] == 'monthly') {
						if ($row->feature_value == 1) {
							$life = 'Month';
						} else {
							$life = 'monthly';
						}
					} elseif ($subs[0] == 'yearly' && $row->feature_value == 1) {
						if ($row->feature_value == 1) {
							$life = 'Year';
						} else {
							$life = 'Yearly';
						}
					}

					$output .= $life . ' ' . ucwords($subs[1]) . '<br/>';
				}

				$output .= '<ul class="nav nav-stacked"> ';

				if ($row->feature_type == 'subscription') {

					$output .= $row->feature_type . '<br/>';
					$output .= $row->feature_value;
				} elseif ($row->feature_type == 'post_property') {
					$output = '<div class="wrapper">
						<div class="ribbon-wrapper-orange">
						<div class="ribbon-orange">' . ucfirst($pkg->row()->package_type) . '
						</div>
						</div>
						</div>';
					$output .= '<li><a href="#">' . ucfirst($pkg->row()->package_type) . '<span class="pull-right badge bg-blue">' . $row->feature_value . '</span></a></li>';
					//$output .= ' Post '.$row->feature_value.' Properties'.'<br/>';
				} elseif ($row->feature_type == 'featured_property') {
					$output = '<div class="wrapper">
						<div class="ribbon-wrapper-orange">
						<div class="ribbon-orange">' . ucfirst($pkg->row()->package_type) . '
						</div>
						</div>
						</div>';
					$output .= '<li><a href="#">' . ucfirst($pkg->row()->package_type) . '<span class="pull-right badge bg-blue">' . $row->feature_value . '</span></a></li>';
					//$output .= $row->feature_value.' Featured Property Submission'.'<br/>';
				} elseif ($row->feature_type == 'post_blog') {
					$output = '<div class="wrapper">
						<div class="ribbon-wrapper-orange">
						<div class="ribbon-orange">' . ucfirst($pkg->row()->package_type) . '
						</div>
						</div>
						</div>';
					$output .= '<li><a href="#">' . ucfirst($pkg->row()->package_type) . '<span class="pull-right badge bg-blue">' . $row->feature_value . '</span></a></li>';
					//$output .= 'Post '.$row->feature_value.' Blogs'.'<br/>';
				} elseif ($row->feature_type == 'urgent_property') {
					$output = '<div class="wrapper">
						<div class="ribbon-wrapper-orange">
						<div class="ribbon-orange">' . ucfirst($pkg->row()->package_type) . '
						</div>
						</div>
						</div>';
					$output .= '<li><a href="#">' . ucfirst($pkg->row()->package_type) . '<span class="pull-right badge bg-blue">' . $row->feature_value . '</span></a></li>';
					//$output .= ' Post '.$row->feature_value.' Urgant Properties'.'<br/>';
				}
			}
			$output .= '</ul>';
			echo $output;
		} else {
			return false;
		}
	}


	public function get_features_by_package_id($id)
	{

		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select * from package_features where package_id = '$id'");

		$pkg = $CI->Common_model->commonQuery("select * from packages where package_id = '$id'");

		/*$package_features = array();
		$package_features['subscription'] = array('title' => 'Subscription', 'details' => '',);
		$package_features['post_property'] = array('title' => 'Property Posting', 'details' => '',);
		$package_features['featured_property'] = array('title' => 'Featured Property Posting', 'details' => '',);
		$package_features['post_blog'] = array('title' => 'Blog Posting', 'details' => '',);*/


		/*$package_features = apply_filters("ssdsdgh" ,$package_features);*/

		$package_features = $package_features_add = $CI->config->item('package_features');
		
		/*echo "<pre>"; print_r($package_features); echo "</pre>";*/
		$package_features_arr = array();
		if(count($package_features_add)){
			foreach($package_features_add as $pfkey => $pck_features){
				if(isset($pck_features['features'])){
					foreach($pck_features['features'] as $feature){	
						$package_features_arr [$feature['feature_type']] = $feature;
					}	
				}
			}
		}
		
		/*echo "<pre>"; print_r($package_features); echo "</pre>";*/
		if ($query->num_rows() > 0) {
			$output = '';
			
			foreach ($query->result() as $key => $row) {
				$details = '';
				if ($row->feature_for === 'subscription') {

					$life = $lifetime = '';
					$lifetime =  $row->feature_value;

					$subs = explode('-', $row->feature_type);
					if ($subs[0] == 'daily') {
						if ($row->feature_value == 1) {
							$life = 'Day';
						} else {
							$life = 'Days';
						}
					} elseif ($subs[0] == 'weekly') {
						if ($row->feature_value == 1) {
							$life = 'Week';
						} else {
							$life = 'weeks';
						}
					} elseif ($subs[0] == 'monthly') {
						if ($row->feature_value == 1) {
							$life = 'Month';
						} else {
							$life = 'months';
						}
					} elseif ($subs[0] == 'yearly') {
						if ($row->feature_value == 1) {
							$life = 'Year';
						} else {
							$life = 'Years';
						}
					}

					$package_features['subscription']['details'] = $lifetime . " " . $life;
				}
				
				else{
					//echo $row->feature_for . " -= = ";
					//$package_features[$row->feature_type]['details'] = $row->feature_value;
					
					if(isset($package_features[$row->feature_type]['title'])){
						$package_features[$row->feature_type]['title'] =  $package_features[$row->feature_type]['title'];
						$package_features[$row->feature_type]['details'] = $row->feature_value;
					}	
					/*else if(isset($package_features_arr[$row->feature_type]['feature_title']))
						$package_features[$row->feature_type]['title'] =  $package_features_arr[$row->feature_type]['feature_title'];*/
					else{
						$feature_for = $row->feature_for;
						//$package_data['feature_for'] = $feature_for;
						//$package_features[$row->feature_type]['title'] =  $package_features_arr[$row->feature_type]['feature_title'];
						$package_data['feature_for'] = $package_features[$feature_for];
						$package_data['row'] = $row;
						$package_features[$feature_for] =  apply_filters($feature_for."_package_details" , $package_data);
						
						//print_r($fff);
					}	
					//else
						//$package_features[$row->feature_type]['title'] = $row->feature_type;
				}


			}
		}
		/*echo "<pre> 123 "; print_r($package_features); echo "</pre>";*/
		return $package_features;
	}

	public function create_credits($package_id, $transaction_id, $user_id, $transaction_status = 'Pending')
	{

		$CI = &get_instance();
		$package_features = $CI->Common_model->commonQuery("select * from package_features where package_id = '$package_id'");
		$package = $CI->Common_model->commonQuery("select * from packages where package_id = '$package_id'");

		$cur_time = time();
		$credit_expires = 0;
		$row = $package->row();
		$package_life =  $package->row()->package_life;
		$credit_expires = strtotime("+" . $package_life);
		if ($credit_expires == $cur_time)
			$credit_expires = 0;
			
		
		foreach ($package_features->result() as $data) {
			
			
			$credit_type = $data->feature_type;
			$credit_value = $data->feature_value;
			$credit_for = $data->feature_for;
			
			
			$credits = $CI->Common_model->commonQuery("select * from credits 	
															where user_id = '$user_id'
															and credit_for = '$credit_for'							");
	
			$credit_expires = 0;
			$package_life =  $package_row->package_life;
			$credit_expires = strtotime("+" . $package_life);
			
			$action = "add";	
			
			if(  $credits->num_rows() > 0)
			{
				$credits_row = $credits->row();
				$action = "update";
				$credit_expires = strtotime("+" . $package_life , $credits_row->credit_expires);
			}
			
			if( preg_match("/subscription/", $credit_for) ){
				
				
				$ftype = $data->feature_type;
				$fvalue = $data->feature_value;

				if ($ftype == 'daily-subscription') {
					
					if($action == "update")
						$credit_expires = strtotime("+" . $fvalue . " days", $credits_row->credit_expires);
					else
						$credit_expires = strtotime("+" . $fvalue . " days");
				} elseif ($ftype == 'weekly-subscription') {
					
					if($action == "update")
						$credit_expires = strtotime("+" . $fvalue . " weeks", $credits_row->credit_expires);
					else
						$credit_expires = strtotime("+" . $fvalue . " weeks");
				} elseif ($ftype == 'monthly-subscription') {
					
					if($action == "update")
						$credit_expires = strtotime("+" . $fvalue . " months", $credits_row->credit_expires);
					else
						$credit_expires = strtotime("+" . $fvalue . " months");
				} elseif ($ftype == 'yearly-subscription') {
					
					if($action == "update")
						$credit_expires = strtotime("+" . $fvalue . " years", $credits_row->credit_expires);
					else
						$credit_expires = strtotime("+" . $fvalue . " years");
				}
				
				$diff = $credit_expires - $cur_time;
				$credit_value =  abs(round($diff / 86400));
				
				
				if($action == "update"){
					
					$credit_id = $credits_row->credit_id;
										
					$datau = array(
								'transaction_id' =>  $transaction_id,
								'credit_type' => $credit_type,
								'updated_credit' => $credit_value,
								'credit_value' => $credit_value,
								'credit_expires' => $credit_expires,
								'status' => $transaction_status,
								'updated_at' => $cur_time,
							);
							
					$CI->Common_model->commonUpdate('credits', $datau, 'credit_id', $credit_id);		
					
					
				}else{
					
					$datai = array(
								'transaction_id' =>  $transaction_id,
								'credit_type' => $credit_type,
								'updated_credit' => $credit_value,
								'credit_value' => $credit_value,
								'user_id' => $user_id,
								'credit_expires' => $credit_expires,

								'credit_for' => $credit_for,
								'status' => $transaction_status,
								'created_at' => $cur_time,
								'updated_at' => $cur_time,
							);
							
					
					/*//if(empty($credit_value)) continue;*/
									
					$credit_id = $CI->Common_model->commonInsert('credits', $datai);
					
				}
				
			}else{
				
				$datai = array(
					'transaction_id' =>  $transaction_id,
					'credit_type' => $credit_type,
					'updated_credit' => $credit_value,
					'credit_value' => $credit_value,
					'user_id' => $user_id,
					'credit_expires' => $credit_expires,

					'credit_for' => $credit_for,
					'status' => $transaction_status,
					'created_at' => $cur_time,
					'updated_at' => $cur_time,
				);
				
				if(empty($credit_value)) continue;
				$credit_id = $CI->Common_model->commonInsert('credits', $datai);
				
			}
			
		}
		
		
		if ($credit_id > 0) {
			return $credit_id;
		} else {
			return 'something went wrong';
		}
	}


	public function update_user_credit_while_login()
	{
		$CI = &get_instance();
		$user_type = $CI->session->userdata('user_type');
		$user_id = $CI->session->userdata('user_id');
		/*if(isset($user_type) && isset($user_id)){
			$result = $CI->Common_model->commonQuery("select * from credits 
						where user_id = $user_id and credit_expires != 0 and status='Active'");
			$current_date = time();
			
			foreach($result->result() as $row){
				echo '<pre>';
					print_r($row);			
			}
			exit;
		}*/
	}

	public function get_total_credits($user_id)
	{
		$CI = &get_instance();

		if (empty($user_id)) {
			return 0;
		} else {

			$total_credits = $CI->Common_model->commonQuery("select sum(meta_value) as total_credits from user_meta where user_id=$user_id 
			and ( meta_key like '%_credit') and meta_key != 'subscription_credit' ");
			if ($total_credits->num_rows() > 0) {
				return $total_credits->row()->total_credits;
			} else {
				return 0;
			}
		}
		//var_dump($total_credits->result());

	}


	public function update_user_meta_credit($user_id, $credit_type, $credit_value)
	{

		$CI = &get_instance();
		$CI->load->library('Global_lib');
		$CI->load->model('Common_model');

		$user_meta_get = $CI->Common_model->commonQuery('select * from user_meta where user_id=' . $user_id .
			' and meta_key="' . $credit_type . '"  ');

		/*echo "UPDATE user_meta set meta_value = '$credit_value'
								where user_id = $user_id and meta_key = '".$credit_type."'"; exit;		*/
		if ($user_meta_get->num_rows() == 0) {


			$datai = array(
				'meta_key' =>  $credit_type,
				'meta_value' => $credit_value,
				'user_id' => $user_id,
			);
			$user_meta_id = $CI->Common_model->commonInsert('user_meta', $datai);
		} else {


			/*if($credit_type != 'subscription_credit'  && $credit_type != 'subscription_credited')
			{
				$CI->package_lib->update_credits_by_user_id($user_id,$credit_type,'add_credit',$credit_value);
			}else{*/
			/*echo "UPDATE user_meta set meta_value = '$credit_value'
								where user_id = $user_id and meta_key = '".$credit_type."'"; */

			$CI->Common_model->commonQuery("UPDATE user_meta set meta_value = '$credit_value'
								where user_id = $user_id and meta_key = '" . $credit_type . "'");


			/*}*/
		}
	}

	public function update_user_meta_credit_changed($user_id, $credit_type, $credit_value)
	{

		$CI = &get_instance();
		$CI->load->library('Global_lib');
		$CI->load->model('Common_model');

		$user_meta_get = $CI->Common_model->commonQuery('select * from user_meta where user_id=' . $user_id .
			' and meta_key="' . $credit_type . '_credit"  ');

		if ($user_meta_get->num_rows() == 0) {


			$datai = array(
				'meta_key' =>  $credit_type . "_credit",
				'meta_value' => $credit_value,
				'user_id' => $user_id,
			);
			$user_meta_id = $CI->Common_model->commonInsert('user_meta', $datai);
		} else {


			if ($credit_type != 'subscription') {
				$CI->package_lib->update_credits_by_user_id($user_id, $credit_type . '_credit', 'add_credit', $credit_value);
			} else {

				$CI->Common_model->commonQuery("UPDATE user_meta set meta_value = '$credit_value'
								where user_id = $user_id and meta_key = '" . $credit_type . "_credit'");
			}
		}
	}


	public function payment_paid_action($args = array())
	{

		extract($args);
		$CI = &get_instance();
		$CI->load->library('Global_lib');
		$CI->load->model('Common_model');
		if (!is_numeric($package_id))
			$package_id = DecryptClientID($package_id);

		$package_info = $CI->Common_model->commonQuery('select * from packages where package_id=' . $package_id);

		if ($package_info->num_rows() == 0) {
			redirect('/packages/payment_error/?error=package_not_found', 'location');
			return false;
		}
		if ($transaction_id > 0) {

			$res = $this->create_credits($package_id, $transaction_id, $user_id, $transaction_status = 'Active');
		}

		$username = get_user_meta($user_id, 'first_name');
		$trans_details = 'hi, Admin ' . $username . ' has been created order for ' . $package_info->row()->package_name . ' and its Price : ' . $package_info->row()->package_price . ' with payment method ' . $trans_type . '';
		$cur_time = time();
		$datalog = array(
			'transaction_id' => $transaction_id,
			'trans_details' => $trans_details,
			'trans_type' => $transaction_type,
			'created_by' => $user_id,
			'created_on' => $cur_time,
		);
		$log_id = $CI->Common_model->commonInsert('transaction_logs', $datalog);


		$package_features_info = $CI->Common_model->commonQuery('select * from package_features where package_id=' . $package_id);


		

		$credit_expires = 0;
		$package_life =  $package_info->row()->package_life;
		$credit_expires = strtotime("+" . $package_life);
		if ($credit_expires == $cur_time)
			$credit_expires = 0;



		foreach ($package_features_info->result() as $package_feature) {

			$credit_type = $package_feature->feature_type;
			$credit_value = $package_feature->feature_value;
			$credit_for = $package_feature->feature_for;
			
			
			if( preg_match("/subscription/", $credit_for) ){
				
				$credits = $CI->Common_model->commonQuery("select * from credits 
															where user_id = '$user_id'
															and credit_for = '$credit_for'		");
	
				
				$action = "add";	
				if(  $credits->num_rows() > 0)
				{
					$credits_row = $credits->row();
					$action = "update";
					$credit_expires =  $credits_row->credit_expires;
				}	
				
				
				$ftype = $package_feature->feature_type;
				$fvalue = $package_feature->feature_value;

				if ($ftype == 'daily-subscription') {
					if($action == "add"){
						$credit_expires = strtotime("+" . $fvalue . " days");
					}
				} elseif ($ftype == 'weekly-subscription') {
					if($action == "add"){
						$credit_expires = strtotime("+" . $fvalue . " weeks");
					}
				} elseif ($ftype == 'monthly-subscription') {
					if($action == "add"){
						$credit_expires = strtotime("+" . $fvalue . " months");
					}	
				} elseif ($ftype == 'yearly-subscription') {
					if($action == "add"){
						$credit_expires = strtotime("+" . $fvalue . " years");
					}	
					
				}
				
				$credit_value = $credit_expires;
				$credit_type_var = $credit_for . "_credit";
				$this->update_user_meta_credit($user_id, $credit_type_var, $credit_value);
			
			}else{
				
					$credit_type_var = $credit_type . "_credit";
					$this->update_credits_by_user_id($user_id,	$credit_type_var, 'add_credit', $credit_value);

					$credited_val = get_user_meta($user_id, $credit_type_var);

					$credit_type_var = $credit_type . "_credited";
					if (!$credited_val)
						$credited_val = $credit_value;

					$this->update_user_meta_credit($user_id, $credit_type_var, $credited_val);
					
				
			}


			
		}
		
		/***	here do_action will work doing additional things in payment_paid_action	***/
		do_action("after_payment_paid_success" , $args);
		

		/**'/packages/payment_success/'*/
		if (isset($redirect) && !empty($redirect))
			redirect($redirect, 'location');
	}

	public function package_name_by($id)
	{
		$CI = &get_instance();
		$CI->load->model('Common_model');
		$CI->load->library('Global_lib');

		$qry = $CI->Common_model->commonQuery("select * from packages where package_id=$id");
		if ($qry->num_rows() > 0) {
			return $qry->row()->package_name;
		} else {
			return 'No Name';
		}
	}
}
