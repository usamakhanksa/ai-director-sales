<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ajax extends MY_Controller
{

	function __construct()
	{
		parent::__construct();

		if (!$this->isAdminLogin()) {
			redirect('/logins', 'location');
		}
	}

	public function update_plugins_setting_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$CI->load->library('Plugins_lib');

		$modules_path = APPPATH . 'modules/';



		$plugin_list = $CI->plugins_lib->get_plugin_headers();

		$plugin_json_list = $CI->plugins_lib->get_plugin_header_from_json();

		if (!empty($plugin_json_list)) {
			$array_keys = array_keys($plugin_json_list);
			foreach ($array_keys as $akk => $avv) {
				if (isset($plugin_list[$avv]) && isset($plugin_json_list[$avv]['status'])) {
					$plugin_list[$avv]['status'] = $plugin_json_list[$avv]['status'];
				}
			}
		}

		$plugin_list[$plugin_name]['status'] = $cur_status;
		
	
		if(file_exists($modules_path . $plugin_name . "/$plugin_name.php")){  
			include_once($modules_path . $plugin_name . "/$plugin_name.php");
		}

		if (isset($cur_status) )
		{
			if( $cur_status == 'Y' )
			{	
				do_action("activate_".$plugin_name);	
			}
			
			if( $cur_status == 'N' )	
				do_action("deactivate_".$plugin_name);	
		}
		

		$modules_path = APPPATH . 'modules/';

		$json_data = json_encode($plugin_list);
		file_put_contents($modules_path . "/modules.json", $json_data);

		$CI->global_lib->update_option('site_modules', $json_data);

		echo 'success';
	}

	/*Homepage Section Ajax Callbacks*/

	public function get_country_list_by_lang_callback_func()
	{

		extract($_POST);
		$CI = &get_instance();

		$loc_tax_settings = $this->global_lib->get_option('loc_tax_settings');

		$locations = $this->global_lib->get_option('locations');

		$country_name = '';
		$country_list = '<option value="all" selected>All Countries</option>';
		$state_list = '<option value="all" selected>All States</option>';
		$city_list = '<option value="all" selected>All Cities</option>';
		$zipcode_list = '<option value="all" selected>All Zipcodes</option>';
		$subarea_list = '<option value="all" selected>All Subarea</option>';
		$country_code = '';

		if (!empty($locations)) {
			$location_array = json_decode($locations, true);

			$lc_val = $this->global_lib->get_option('language_country_' . $lang_code);
			if (!empty($lc_val)) {
				$exp_lc_val = explode(',', $lc_val);
				foreach ($exp_lc_val as $cc) {
					$country_code = $cc;
					if (isset($location_array['countries'][$country_code])) {
						$country_name = $location_array['countries'][$country_code]['loc_title'];

						$country_list .= '<option data-country_code="' . $country_code . '" value="' . mlx_get_norm_string($country_name) . '~' . $country_code . '"';
						/*
									if(isset($lang_state) == $sval['loc_title'])
									{
										$state_code = $skey;
										$country_list .= ' selected="selected" ';
									}
									*/
						$country_list .= '>' . $country_name . '</option>';
					}
				}
			}
		}


		header('Content-type: application/json');
		echo json_encode(array(
			'country_list' => $country_list, 'state_list' => $state_list, 'city_list' => $city_list,
			'zipcode_list' => $zipcode_list, 'subarea_list' => $subarea_list
		));
	}

	public function get_state_or_city_list_by_lang_callback_func()
	{

		extract($_POST);
		$CI = &get_instance();

		$loc_tax_settings = $this->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		if (!empty($loc_tax_settings)) {
			$loc_tax_setting_array = json_decode($loc_tax_settings, true);
			if (isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
		}

		$locations = $this->global_lib->get_option('locations');

		$country_name = '';
		$state_list = '<option value="all" selected>All States</option>';
		$city_list = '<option value="all" selected>All Cities</option>';
		$zipcode_list = '<option value="all" selected>All Zipcodes</option>';
		$subarea_list = '<option value="all" selected>All Subarea</option>';

		$state_code = $city_code = '';

		if (!empty($locations) && $is_state_enable) {
			$location_array = json_decode($locations, true);

			$lc_val = $country_code;
			if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
				$country_name = $location_array['countries'][$lc_val]['loc_title'];

				if (isset($location_array['countries'][$lc_val]['states'])) {
					foreach ($location_array['countries'][$lc_val]['states'] as $skey => $sval) {
						if ($skey != 'no_state')
							$state_list .= '<option data-country_code="' . $lc_val . '" data-state_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
						if (isset($lang_state) && $lang_state == $sval['loc_title']) {
							$state_code = $skey;
							$state_list .= ' selected="selected" ';
						}
						$state_list .= '>' . $sval['loc_title'] . '</option>';
					}
				}
			}
			if (isset($is_edit) && !empty($state_code)) {
				if (isset($location_array['countries'][$lc_val]['states'][$state_code]['cities'])) {
					$cities = $location_array['countries'][$lc_val]['states'][$state_code]['cities'];
					if (!empty($cities)) {
						foreach ($cities as $skey => $sval) {
							$city_list .= '<option data-country_code="' . $lc_val . '" data-state_code="' . $state_code . '" data-city_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
							if (isset($lang_city) && $lang_city == $sval['loc_title']) {
								$city_list .= ' selected="selected" ';

								if (isset($sval['zipcodes']) && !empty($sval['zipcodes'])) {
									foreach ($sval['zipcodes'] as $zipcode) {
										$zipcode_list .= '<option value="' . $zipcode . '"';
										if (isset($lang_zip_code) && $lang_zip_code == $zipcode)
											$zipcode_list .= ' selected="selected" ';
										$zipcode_list .= '>' . $zipcode . '</option>';
									}
								}
								if (isset($sval['sub_areas']) && !empty($sval['sub_areas'])) {
									foreach ($sval['sub_areas'] as $subarea) {
										$subarea_list .= '<option value="' . $subarea . '"';
										if (isset($lang_sub_area) && $lang_sub_area == $subarea)
											$subarea_list .= ' selected="selected" ';
										$subarea_list .= '>' . $subarea . '</option>';
									}
								}
							}
							$city_list .= '>' . $sval['loc_title'] . '</option>';
						}
					}
				}
			}
		} else if (!empty($locations)) {
			$location_array = json_decode($locations, true);

			$lc_val = $country_code;
			if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
				$country_name = $location_array['countries'][$lc_val]['loc_title'];

				if (isset($location_array['countries'][$lc_val]['states']['no_state']['cities'])) {
					foreach ($location_array['countries'][$lc_val]['states']['no_state']['cities'] as $skey => $sval) {
						$city_list .= '<option data-country_code="' . $lc_val . '" data-state_code="no_state" data-city_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
						if (isset($lang_city) && $lang_city == $sval['loc_title'])
							$city_list .= ' selected="selected" ';
						$city_list .= '>' . $sval['loc_title'] . '</option>';
					}
				}
			}
		}

		header('Content-type: application/json');
		echo json_encode(array('state_list' => $state_list, 'city_list' => $city_list, 'zipcode_list' => $zipcode_list, 'subarea_list' => $subarea_list));
	}

	public function get_state_list_from_country_by_lang_callback_func()
	{

		extract($_POST);
		$CI = &get_instance();

		$state_list = '<option value="all" selected>All States</option>';

		if (isset($country_code)) {
			$locations = $this->global_lib->get_option('locations');
			$location_array = json_decode($locations, true);

			$lc_val = $country_code;
			if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
				if (isset($location_array['countries'][$lc_val]['states'])) {
					foreach ($location_array['countries'][$lc_val]['states'] as $skey => $sval) {
						$state_list .= '<option data-country_code="' . $lc_val . '" data-state_code="' . $skey . '" 
										value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
						$state_list .= '>' . $sval['loc_title'] . '</option>';
					}
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode(array('state_list' => $state_list));
	}

	public function get_city_list_from_state_by_lang_callback_func()
	{

		extract($_POST);
		$CI = &get_instance();

		$locations = $this->global_lib->get_option('locations');

		$city_list = '<option value="all" selected>All Cities</option>';

		$location_array = json_decode($locations, true);

		$lc_val = $country_code;
		if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
			if (isset($location_array['countries'][$lc_val]['states'][$state_code]['cities'])) {
				foreach ($location_array['countries'][$lc_val]['states'][$state_code]['cities'] as $skey => $sval) {
					$city_list .= '<option data-country_code="' . $lc_val . '" data-state_code="' . $state_code . '" data-city_code="' . $skey . '" value="' . mlx_get_norm_string($sval['loc_title']) . '~' . $skey . '"';
					$city_list .= '>' . $sval['loc_title'] . '</option>';
				}
			}
		}

		header('Content-type: application/json');
		echo json_encode(array('city_list' => $city_list));
	}

	public function get_zip_subarea_list_from_city_by_lang_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();

		$loc_tax_settings = $this->global_lib->get_option('loc_tax_settings');
		$is_state_enable = false;
		if (!empty($loc_tax_settings)) {
			$loc_tax_setting_array = json_decode($loc_tax_settings, true);
			if (isset($loc_tax_setting_array['state']['enabled']) && $loc_tax_setting_array['state']['enabled'] == true)
				$is_state_enable = true;
		}

		$locations = $this->global_lib->get_option('locations');

		$zipcode_list = '<option value="all" selected>All Zipcodes</option>';
		$subarea_list = '<option value="all" selected>All Sub Areas</option>';


		if (!empty($locations) && $is_state_enable) {

			$location_array = json_decode($locations, true);

			$lc_val = $country_code;

			if (!empty($state_code) && !empty($city_code)) {
				if (isset($location_array['countries'][$lc_val]['states'][$state_code]['cities'][$city_code])) {
					$city = $location_array['countries'][$lc_val]['states'][$state_code]['cities'][$city_code];


					if (isset($city['zipcodes']) && !empty($city['zipcodes'])) {
						foreach ($city['zipcodes'] as $zipcode) {
							$zipcode_list .= '<option value="' . $zipcode . '"';
							$zipcode_list .= '>' . $zipcode . '</option>';
						}
					}
					if (isset($city['sub_areas']) && !empty($city['sub_areas'])) {
						foreach ($city['sub_areas'] as $subarea) {
							$subarea_list .= '<option value="' . $subarea . '"';
							$subarea_list .= '>' . $subarea . '</option>';
						}
					}
				}
			}
		} else if (!empty($locations)) {
			$location_array = json_decode($locations, true);

			$lc_val = $country_code;
			if (!empty($lc_val) && isset($location_array['countries'][$lc_val])) {
				if (isset($location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code])) {
					$city = $location_array['countries'][$lc_val]['states']['no_state']['cities'][$city_code];

					if (isset($city['zipcodes']) && !empty($city['zipcodes'])) {
						foreach ($city['zipcodes'] as $zipcode) {
							$zipcode_list .= '<option value="' . $zipcode . '"';
							$zipcode_list .= '>' . $zipcode . '</option>';
						}
					}
					if (isset($city['sub_areas']) && !empty($city['sub_areas'])) {
						foreach ($city['sub_areas'] as $subarea) {
							$subarea_list .= '<option value="' . $subarea . '"';
							$subarea_list .= '>' . $subarea . '</option>';
						}
					}
				}
			}
		}

		header('Content-type: application/json');
		echo json_encode(array('zipcode_list' => $zipcode_list, 'subarea_list' => $subarea_list));
	}

	/*End of Homepage Section Ajax Callbacks*/


	public function hide_notifications_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$this->load->model('Common_model');

		if (isset($notif_id) && !empty($notif_id)) {
			$datai = array(
				'notif_status' => 'H'
			);
			$encId = $this->DecryptClientId($notif_id);
			$this->Common_model->commonUpdate('notifications', $datai, 'notif_id', $encId);
		}
		echo 'success';
	}

	public function remove_notifications_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$this->load->model('Common_model');

		$encId = $this->DecryptClientId($notif_id);
		$this->Common_model->commonDelete('notifications', $encId, 'notif_id');
		echo 'success';
	}



	
	
}
