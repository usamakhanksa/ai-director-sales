<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_ajax_orders_lib
{

	
	public function order_creation($args = array())
	{

		$CI = &get_instance();
		$CI->load->library('Global_lib');
		$CI->load->library('package_lib');
		extract($args);

		if (!isset($customer_id)) $customer_id = 0;

		if (!isset($order_key) ) {
			$order_key = generate_random_string(16);
		} 
		
		
		if (empty($order_price) && !empty($totalAmount)) {
			$order_price = $totalAmount;
		}

		$order_price = $order_price;

		if (!isset($order_status)) {
			$order_status = 'temp_order';
		}

		if (!is_numeric($package_id)) {
			$package_id = DecryptClientID($package_id);
		}

		if (!is_numeric($customer_id)) {
			$customer_id = DecryptClientID($customer_id);
		}

		if (isset($payment_method) && $payment_method == 'razorpay') {
			$order_price = $order_price / 100;
		}

		$package_details = $CI->package_lib->get_features_by_package_id($package_id);

		$datai = array(
			'order_key' => $order_key,
			'package_id' => $package_id,
			'order_details' => json_encode($package_details),
			'customer_id' => $customer_id,
			'payment_method' => $payment_method,
			'payment_status' => 'pending',
			'order_status' => $order_status, 
			'order_price' => $order_price,
			'order_created_on' => time(),
			'order_updated_on' => time(),
		);
		$order_id = $CI->Common_model->commonInsert('orders', $datai);
		return $order_id;
	}

}