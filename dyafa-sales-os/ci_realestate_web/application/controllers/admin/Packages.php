<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Packages extends MY_Controller
{

	public $_api_context;

	function __construct()
	{

		parent::__construct();

		if (!$this->isAdminLogin()) {
			redirect('/admin/logins', 'location');
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
		$data['query'] = $this->Common_model->commonQuery("select * from packages order by package_id DESC");

		$data['content'] = $CI->theme . "/packages/manage";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function payment_methods()
	{

		$CI = &get_instance();
		
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		/*$payments_methods = $CI->config->item('payment_methods') ;*/
		
		if (isset($_POST['submit']) || isset($_POST['draft'])) {

			$content = array();
			//print_r($_POST); exit;
			clean_post();
			
			extract($_POST, EXTR_OVERWRITE);
			$user_id = $CI->user_id;
			
			
			foreach($_POST as $k=>$v)
			{
				if(is_array($v) && $k != 'submit')
					$content[$k] = $v;
			}
			//print_r($_POST); exit;
			update_option('site_payment_methods', json_encode($content));

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Payment Methods Updated Successfully") . '
			</div>							';
			redirect('/admin/packages/payment_methods', 'location');
		}

		$meta_site_payment_methods = get_option('site_payment_methods');


		if (!empty($meta_site_payment_methods)) {
			$meta_site_payment_methods = json_decode($meta_site_payment_methods, true);
		}

		$data['meta_site_payment_methods'] = $meta_site_payment_methods;
		do_action("site_payment_methods_append");

		$data['content'] = $CI->theme . "/packages/payment_methods";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function add_new()
	{

		$CI = &get_instance();
		
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

			


			$applicabled_for = '';
			if (!empty($user_types)) {
				$applicabled_for = implode(',', $user_types);
			}
			if (empty($applicabled_for)) {
				$applicabled_for = 'all';
			}

			$pacakge_type = 'topup';
			if (isset($feature) && isset($feature['is_subscription']['enable'])) {
				$pacakge_type = 'subscription';
			}
			/*echo "<pre>"; print_r($_POST); exit;*/
			$cur_time = time();
			$datai = array(
				'package_name' => $package,
				'package_price' => $packages_price,
				'package_currency' => $currency_code,
				'package_life' => $package_lifetime,
				'package_type' => $pacakge_type,
				'applicable_for' => $applicabled_for,
				'purchase_limit' => $limit_purchase_by_user,
				'purchase_button_text' => $purchase_button_text,
				'package_order' => $package_order,

				'created_at' => $cur_time,
				'updated_at' => $cur_time
			);

			$package_id = $this->Common_model->commonInsert('packages', $datai);

			if (isset($feature) && !empty($feature)) {

				foreach ($feature as $k => $v) {

					if ($k == 'is_subscription'  && isset($v['enable'])) {

						$exp_sv = explode(' ', $v['subscription_validity']);

						if (count($exp_sv) > 1) {
							if ($exp_sv[1] == 'days') {
								$exp_sv[1] = 'daily-subscription';
							} elseif ($exp_sv[1] == 'weeks') {
								$exp_sv[1] = 'weekly-subscription';
							} elseif ($exp_sv[1] == 'months') {
								$exp_sv[1] = 'monthly-subscription';
							} elseif ($exp_sv[1] == 'year') {
								$exp_sv[1] = 'yearly-subscription';
							}
							$datai = array(
								'package_id' => $package_id,
								'feature_for' => 'subscription',
								'feature_type' => strtolower($exp_sv[1]),
								'feature_value' => $exp_sv[0]
							);
							$this->Common_model->commonInsert('package_features', $datai);
						}
					} else {
						
						/*echo "<pre>"; print_r($feature); exit;*/
						
						if (isset($v['enable'])) {
							unset($v['enable']);
							foreach ($v as $v_key => $v_val) {


								if (isset($v['subscription_validity']))
								{
									$exp_sv = explode(' ', $v['subscription_validity']);
									
									if (count($exp_sv) > 1) {
										if ($exp_sv[1] == 'days') {
											$exp_sv[1] = 'daily-subscription';
										} elseif ($exp_sv[1] == 'weeks') {
											$exp_sv[1] = 'weekly-subscription';
										} elseif ($exp_sv[1] == 'months') {
											$exp_sv[1] = 'monthly-subscription';
										} elseif ($exp_sv[1] == 'year') {
											$exp_sv[1] = 'yearly-subscription';
										}
										$datai = array(
											'package_id' => $package_id,
											'feature_for' => $k,
											'feature_type' => strtolower($exp_sv[1]),
											'feature_value' => $exp_sv[0]
										);
										
										$this->Common_model->commonInsert('package_features', $datai);
									}
								}else{
									
									
									$datai = array(
										'package_id' => $package_id,
										'feature_for' => $k,
										'feature_type' => $v_key,
										'feature_value' => $v_val
									);
									$this->Common_model->commonInsert('package_features', $datai);
								}

							}
						}
					}
				}
			}


			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Package Added Successfully") . '
			</div>							';
			redirect('admin/packages/manage', 'location');
		}

		$data['currency_symbols'] = $CI->config->item('currency_symbols');

		$data['package_features'] = $CI->config->item('package_features');



		$data['content'] = $CI->theme . "/packages/add_new";
		$this->load->view($CI->theme . "/header", $data);
	}


	public function edit($b_id = NULL)
	{
		$CI = &get_instance();
		
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		if (isset($_POST['submit']) || isset($_POST['draft'])) {

			clean_post();
			extract($_POST, EXTR_OVERWRITE);


			$decId = DecryptClientID($b_id);

			$applicabled_for = '';
			if (!empty($user_types)) {
				$applicabled_for = implode(',', $user_types);
			}
			if (empty($applicabled_for)) {
				$applicabled_for = 'all';
			}

			$package_type = 'topup';
			if (isset($feature) && isset($feature['is_subscription']['enable'])) {
				$package_type = 'subscription';
			}
			
			/*echo "<pre>"; print_r($_POST); exit;*/
			$cur_time = time();
			$datai = array(
				'package_name' => $package,
				'package_price' => $packages_price,
				'package_currency' => $currency_code,
				'package_life' => $package_lifetime,
				'package_type' => $package_type,
				'applicable_for' => $applicabled_for,
				'purchase_limit' => $limit_purchase_by_user,
				'purchase_button_text' => $purchase_button_text,
				'package_order' => $package_order,
				'updated_at' => $cur_time
			);

			$this->Common_model->commonUpdate('packages', $datai, 'package_id', $decId);

			$this->Common_model->commonDelete('package_features', $decId, 'package_id');

			if (isset($feature) && !empty($feature)) {

				foreach ($feature as $k => $v) {

					if ($k == 'is_subscription' && isset($v['enable'])) {
						$exp_sv = explode(' ', $v['subscription_validity']);
						/*print_r($exp_sv);
							exit;*/
						if (count($exp_sv) > 1) {
							if ($exp_sv[1] == 'days') {
								$exp_sv[1] = 'daily-subscription';
							} elseif ($exp_sv[1] == 'weeks') {
								$exp_sv[1] = 'weekly-subscription';
							} elseif ($exp_sv[1] == 'months') {
								$exp_sv[1] = 'monthly-subscription';
							} elseif ($exp_sv[1] == 'year') {
								$exp_sv[1] = 'yearly-subscription';
							}
							$datai = array(
								'package_id' => $decId,
								'feature_for' => 'subscription',
								'feature_type' => strtolower($exp_sv[1]),
								'feature_value' => $exp_sv[0]
							);
							$this->Common_model->commonInsert('package_features', $datai);
						}
					} else {

						/*echo "<pre>"; print_r($feature); exit;*/

						if (isset($v['enable'])) {
							unset($v['enable']);
							foreach ($v as $v_key => $v_val) {

								//echo $k; print_r($v);

								//if($v_key == 'subscription_validity')
								if (isset($v['subscription_validity']))
								{
									$exp_sv = explode(' ', $v['subscription_validity']);
									
									if (count($exp_sv) > 1) {
										if ($exp_sv[1] == 'days') {
											$exp_sv[1] = 'daily-subscription';
										} elseif ($exp_sv[1] == 'weeks') {
											$exp_sv[1] = 'weekly-subscription';
										} elseif ($exp_sv[1] == 'months') {
											$exp_sv[1] = 'monthly-subscription';
										} elseif ($exp_sv[1] == 'year') {
											$exp_sv[1] = 'yearly-subscription';
										}
										$datai = array(
											'package_id' => $decId,
											'feature_for' => $k,
											'feature_type' => strtolower($exp_sv[1]),
											'feature_value' => $exp_sv[0]
										);
										
										$this->Common_model->commonInsert('package_features', $datai);
									}
								}else{
									
									
									$datai = array(
										'package_id' => $decId,
										'feature_for' => $k,
										'feature_type' => $v_key,
										'feature_value' => $v_val
									);
									$this->Common_model->commonInsert('package_features', $datai);
								}


								/*$datai = array(
									'package_id' => $decId,
									'feature_for' => $k,
									'feature_type' => $v_key,
									'feature_value' => $v_val
								);
								$this->Common_model->commonInsert('package_features', $datai);*/
							}
						}
					}
					
					
					
				}
					
			}

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable"  style="margin-top:10px;margin-bottom:0px;">	
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>		
				' . mlx_get_lang("Package Updated Successfully") . '
				</div>							';
			redirect('/admin/packages/manage', 'location');
		}

		$decId = DecryptClientID($b_id);
		$data['blog_meta'] = $blog_meta =  $this->Common_model->commonQuery("select *from packages where package_id = $decId ");

		$data['package_features'] = $CI->config->item('package_features');

		$data['package_id'] = $decId;
		
		$current_package_features_result = $this->Common_model->commonQuery("select feature_for,feature_type,feature_value 
															from package_features where package_id = $decId ");


		$data['current_package_features'] = $current_package_features_result;
		/*->result_array();*/

		if ($blog_meta->num_rows() == 0) {
			$_SESSION['msg'] = '<div class="alert alert-danger alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Invalid Packages") . '
			</div>							';
			redirect('/admin/packages/manage', 'location');
		}

		$data['currency_symbols'] = $CI->config->item('currency_symbols');

		$data['content'] = $CI->theme . "/packages/edit";
		$this->load->view($CI->theme . "/header", $data);
	}


	public function delete($rowid)
	{
		$CI = &get_instance();
		$this->load->library('Global_lib');
		if (!is_array($rowid))
			$rowid	= DecryptClientID($rowid);
		$this->load->model('Common_model');
		$tbl = 'packages';
		$pid = 'package_id';
		$url = '/admin/packages/manage/';
		$fld = mlx_get_lang("package");

		$this->Common_model->commonDelete('package_features', $rowid, 'package_id');

		$rows = $this->Common_model->commonDelete($tbl, $rowid, $pid);
		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">								
		<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>																
		' . $rows . ' ' . $fld . ' ' . mlx_get_lang("Deleted Successfully") . '							
		</div>							';
		redirect($url, 'location', '301');
	}

	public function transaction()
	{
		$CI = &get_instance();
		
		$this->load->library('Package_lib');
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['myHelpers'] = $this;
		$data['query'] = $this->Common_model->commonQuery("select * from orders
															inner join packages pck on pck.package_id = orders.package_id
														 order by order_id DESC");
		$data['content'] = $CI->theme . "/packages/transactions";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function my_transactions()
	{

		$CI = &get_instance();
		
		$this->load->library('Package_lib');
		$data = $this->data;
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$user_id = $CI->user_id;
		$data['query'] = $this->Common_model->commonQuery("select * from transaction 
															inner join packages pck on pck.package_id = transaction.package_id
															where user_id = $user_id order by transaction_id DESC");
		$data['content'] = $CI->theme . "/packages/my_transactions";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function change($t_id = null)
	{
		$CI = &get_instance();
		
		$this->load->library('Package_lib');
		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');

		$user_credit_types = array("post_property_credit", "post_blog_credit", "featured_property_credit", "subscription_credit");

		if (isset($_POST['submit'])) {
			clean_post();
			extract($_POST, EXTR_OVERWRITE);
			$decId = DecryptClientID($t_id);
			$uid = $user_id;
			$datai = array(
				'status' => $status,
			);
			$this->Common_model->commonUpdate('transaction', $datai, 'transaction_id', $decId);
			/* Creadit table's status update query*/
			if ($status == 'Completed') {

				$package_info = $this->Common_model->commonQuery('select * from packages where package_id=' . $package_id);
				if ($package_info->num_rows() == 0)
					return false;

				$cur_time = time();
				$datai = array(
					'status' => 'Active',
					'updated_at' => $cur_time,
				);
				$this->Common_model->commonUpdate('credits', $datai, 'transaction_id', $decId);



				$package_features_info = $this->Common_model->commonQuery('select * from package_features where package_id=' . $package_id);


				$credit_expires = 0;
				$package_life =  $package_info->row()->package_life;
				$credit_expires = strtotime("+" . $package_life);
				if ($credit_expires == $cur_time)
					$credit_expires = 0;


				foreach ($package_features_info->result() as $package_feature) {
					$credit_type = $package_feature->feature_type;
					$credit_value = $package_feature->feature_value;
					$credit_for = $package_feature->feature_for;

					if ($package_feature->feature_for == 'subscription') {
						$ftype = $package_feature->feature_type;
						$fvalue = $package_feature->feature_value;

						if ($ftype == 'daily-subscription') {
							$credit_expires = strtotime("+" . $fvalue . " days");
						} elseif ($ftype == 'weekly-subscription') {
							$credit_expires = strtotime("+" . $fvalue . " weeks");
						} elseif ($ftype == 'monthly-subscription') {
							$credit_expires = strtotime("+" . $fvalue . " months");
						} elseif ($ftype == 'yearly-subscription') {
							$credit_expires = strtotime("+" . $fvalue . " years");
						}
						$credit_for = $credit_type = "subscription";
						//$diff = strtotime($credit_expires) - strtotime($cur_time); 
						//$diff = $credit_expires - $cur_time; 
						/*$credit_value =  abs(round($diff / 86400)); */
						$credit_value = $credit_expires;
					}
					/*echo $credit_expires . $credit_type ; exit;*/


					/*$credit_type_var = $credit_type . "_credit";
					$this->package_lib->update_user_meta_credit( $user_id, $credit_type_var , $credit_value);*/

					if ($credit_type == 'subscription') {

						$credit_type_var = $credit_type . "_credit";
						$this->package_lib->update_user_meta_credit($user_id, $credit_type_var, $credit_value);

						$credit_type_var = $credit_type . "_credited";
						$this->package_lib->update_user_meta_credit($user_id, $credit_type_var, $cur_time);
					} else {
						$credit_type_var = $credit_type . "_credit";
						$this->package_lib->update_credits_by_user_id(
							$user_id,
							$credit_type_var,
							'add_credit',
							$credit_value
						);




						/*$credit_type_var = $credit_type . "_credit";
						$credited_var = $this->global_lib->get_user_meta($user_id  ,$credit_type_var);		
						if($credited_var )
							$credited_var += $credit_value;
						else
							$credited_var = $credit_value;*/


						$credited_val = $this->global_lib->get_user_meta($user_id, $credit_type_var);

						$credit_type_var = $credit_type . "_credited";
						if (!$credited_val)
							$credited_val = $credit_value;

						//echo $credited_val ; exit;
						$this->package_lib->update_user_meta_credit($user_id, $credit_type_var, $credited_val);
					}
				}
			}


			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
		<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
		' . mlx_get_lang("Transaction Status Updated Successfully") . '		
		</div>	';
			redirect('/admin/packages/transaction', 'location');
		}
		
		
		echo $decId = DecryptClientID($t_id);
		$data['transaction'] = $blog_meta =  $this->Common_model->commonQuery("select *from transaction where transaction_id = $decId ");

		

		$data['content'] = $CI->theme . "/packages/change_transaction";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function choose_package($package_type = '')
	{
		$CI = &get_instance();
		
		$this->load->library('Package_lib');

		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');
		$uid = $this->session->userdata('user_id');

		

		$member_type = $this->session->userdata('user_type');

		if ($package_type == '') {

			$data['query'] = $this->Common_model->commonQuery("select * from packages where applicable_for LIKE '%$member_type%' 
				or applicable_for='all' ");
		} else {

			if ($package_type == 'subscription') {
				$query = "select * from packages as pk 
				 where pk.package_type = 'subscription' and
				 pk.applicable_for LIKE '%$member_type%' or pk.applicable_for='all' ";
			} elseif ($package_type == 'topup') {
				$query = "select * from packages as pk 
				Where pk.package_type = 'topup' and
				pk.applicable_for LIKE '%$member_type%' or pk.applicable_for='all' ";
			} else {
				$query = "select * from packages as pk 
				where pk.applicable_for LIKE '%$member_type%' or pk.applicable_for='all' ";
			}

			$data['query'] = $this->Common_model->commonQuery($query);
		}


		$data['content'] = $CI->theme . "/packages/choose_package";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function my_credits()
	{
		$CI = &get_instance();
		
		$this->load->library('Package_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$user_id = $this->session->userdata('user_id');

		/*echo "select * from user_meta  where user_id=$user_id and meta_key LIKE '%_credit'";*/
		$data['query'] = $this->Common_model->commonQuery("select * from user_meta  where user_id=$user_id and meta_key LIKE '%_credit'");

		$data['package_features_add'] = $CI->config->item('package_features');

		$data['content'] = $CI->theme . "/packages/my_credits";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function subscribe($package_id = null)
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');

		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');
		$price = 0;
		
		$args = array();
		$args['package_id'] = $data['package_id'] = $package_id;

		$package_id_dec = DecryptClientID($package_id);

		$package_info = $CI->Common_model->commonQuery('select * from packages where package_id=' . $package_id_dec);

		

		if ($package_info->num_rows() == 0) {
			redirect('/admin/packages/payment_error/?error=package_not_found', 'location');
			return false;
		}else{
			
			if($package_info->row()->package_price === '0') 
			{
				$args['package_detail'] = $package_info->row();
				
				$this->apply_free_package($args);
				
				redirect("admin/packages/my_transactions" , 'location');
				
			}
			
			//echo "<pre>"; print_r($package_info->row() ); exit;
			
			
		}
		
		do_action("site_payment_methods_append");
		
		
		
		$args['package_currency'] = $data['package_currency'] = $package_info->row()->package_currency;
		$args['price'] = $data['price'] = $package_info->row()->package_price;
		$args['payment_method_currency_supports'] = $data['payment_method_currency_supports'] = $CI->config->item('payment_method_currency_supports');

		$data['query'] = $this->Common_model->commonQuery("select * from packages order by package_id DESC");
		$args['payment_methods'] = $data['methods'] = get_option('site_payment_methods');
		
		$args['order_price'] = $data['order_price'] = $price;
		
		$customer_id = $CI->session->userdata('user_id');
		$args['customer_id'] = $data['customer_id'] = $customer_id;
		$args['customer_email'] = $data['customer_email'] = get_user_meta($customer_id, "user_email");
		
		if(isset($url) && !empty($url)){
			$data['post_url'] = $url;
		}else{
			$data['post_url'] = "admin/packages/my_transactions";
		}
		
		
		$data['args'] = $args;
		
		$data['content'] = $CI->theme . "/packages/methods";

		$this->load->view($CI->theme . "/header", $data);
	}
	
	
	
	public function apply_free_package($args = array())
	{
		$CI = &get_instance();
		$CI->load->library('Package_lib');
		$CI->load->library('Admin_ajax_orders_lib');
		extract($args);
		
		if (!is_numeric($package_id)) {
			$package_id = DecryptClientID($package_id);
		}
		
		
		$customer_id = $CI->session->userdata('user_id');
		$args['customer_id' ] = $customer_id;
		
		$order_price = 0;
		$args['order_price'] = $order_price;
		
		$order_status = "Completed";
		$args['order_status'] = $order_status;
		
		$payment_method = "free_package";
		$args['payment_method'] = $payment_method;
		
		
		
		$order_id = $CI->admin_ajax_orders_lib->order_creation($args);
		
		$qry = $CI->Common_model->commonQuery('select * from orders where order_id=' . $order_id . '');
		if ($qry->num_rows() > 0) {
			
			$order_key = $qry->row()->order_key;
			$order_details = $qry->row()->order_details;
			$payment_method = $qry->row()->payment_method;
			
			
			
			$datai = array(
					'transaction_key' => $order_key,
					'package_id' => $package_id,
					'package_detail' => $order_details,
					'user_id' => $customer_id,
					'payment_mode' => $payment_method,
					'transaction_meta' => '',
					'transaction_amount' => $order_price,
					'transaction_date' => time(),
					'status' => $order_status,
				);
				
			$trxn_id = $CI->Common_model->commonInsert('transaction', $datai);
			
			$dataU = array('payment_status' => $order_status, 'order_status' => $order_status, 'transaction_id' => $trxn_id);
			$CI->Common_model->commonUpdate('orders', $dataU, 'order_id', $order_id);

			
			$new_args = array();
			$new_args['payment_id'] = 0;
			$new_args['transaction_id'] = $trxn_id;
			$new_args['user_id'] = $customer_id;
			$new_args['package_id'] = $package_id;
			$new_args['trans_type'] = 'Free Package';
			$new_args['transaction_type'] = 'Free Package Subscription';

			$CI->package_lib->payment_paid_action($new_args);

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				' . mlx_get_lang("Free Packages Credits Added Successfully") . '
				</div>';


			
		}
		
	}	
	
	

	public function confirmation($id = null)
	{
		$CI = &get_instance();
		$this->load->model('Common_model');
		$this->load->library('Package_lib');
		$this->load->library('admin_ajax_orders_lib');
		$this->load->library('Global_lib');


		if (isset($_POST['submit'])) {

			extract($_POST);

			if (!is_numeric($package_id)) {
				$package_id = DecryptClientID($package_id);
			}
			if (isset($user_id)) {
				$customer_id = $user_id;
				unset($_POST['user_id']);
				$_POST['customer_id'] = $customer_id;
			}
			$qry = $this->Common_model->commonQuery("select * from packages where package_id=" . $package_id . "");
			if ($qry->num_rows() > 0) {
				$_POST['order_price'] = $qry->row()->package_price;
			}
			$_POST['order_key'] = $this->global_lib->generate_random_string(16);

			$order_id = $this->admin_ajax_orders_lib->order_creation($_POST);
			if ($order_id) {


				$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				' . mlx_get_lang("Order Request Added Successfully") . '
				</div>';

				redirect(base_url() . 'admin/packages/choose_package');
			}
		}
	}

	

	public function payment_success()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');
		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');


		$data['content'] = $CI->theme . "/packages/payment_success";
		$this->load->view($CI->theme . "/header", $data);
	}

	public function payment_error()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');


		$data['content'] = $CI->theme . "/packages/payment_error";
		$this->load->view($CI->theme . "/header", $data);
	}
}
