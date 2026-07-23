<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Agency_settings extends MY_Controller
{

	var $user_id;

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
		$this->user_id = apply_filters("get_user_account_id");
	}

	public function index()
	{
		$this->settings();
	}
	
	public function settings()
	{

		$CI = &get_instance();

		$data = $CI->data;
		$data['sub_class'] = $data['class'];
		$data['class'] = ABMS_MENU_CLASS;
		
		
		$data['currency_symbol'] = $CI->global_lib->get_currency_symbol();
		$user_type = $this->session->userdata('user_type');

		$data['page_heading'] = mlx_get_lang('Manage All Tenants');
		
		
		
		if (isset($_POST['submit']) ) {

			
			extract($_POST, EXTR_OVERWRITE);
			
			/*echo "<pre>"; print_r($_POST); exit;*/
			
			if(!isset($country)) $country = "";
			if(!isset($state)) $state = "";
			if(!isset($city)) $city = "";
			if(!isset($zipcode)) $zipcode = "";
			if(!isset($sub_area)) $sub_area = "";
			
			if(isset($agency_meta)){
				$agency_meta['country'] = $country;
				$agency_meta['state'] = $state;
				$agency_meta['city'] = $city;
				$agency_meta['zipcode'] = $zipcode;
				$agency_meta['sub_area'] = $sub_area;

				$agency_meta['description'] = trim($agency_meta['description']);
				update_user_meta($this->user_id, 'agency_meta', json_encode($agency_meta));
			}

			if(isset($user_meta)){
				foreach($user_meta as $meta_key => $meta_value){
					
					if(is_array($meta_value)) $meta_value = json_encode($meta_value);
					
					update_user_meta($this->user_id, $meta_key, $meta_value);
				}
			}
			
			$sess_msg =	mlx_get_lang("My Agency Updated Successfully"); 
			
			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px; margin-bottom:10px;">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									' . $sess_msg . '
								</div>';

			
			redirect('/admin/agency_settings', 'location');
			
			
		}	
	
		$data['social_medias'] = $CI->config->item('social_medias');
		$data['agency_meta_data'] = get_user_meta($this->user_id , 'agency_meta');


		$data['content'] = $CI->theme . "/settings/my_agency";


		$this->load->view($CI->theme . "/header", $data);
	}

}
