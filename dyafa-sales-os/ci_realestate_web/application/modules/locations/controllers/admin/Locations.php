<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Locations extends MY_Controller
{


	var $countries,
		$countries_states,
		$countries_states_cities,
		$states_cities;

	function __construct()
	{
		parent::__construct();
		if (!$this->isAdminLogin()) {

			redirect('/admin/logins', 'location');
		}

		/*if(!$this->has_method_access())
		{
			redirect('/admin/main/','location');
		}*/


		if (!is_dir("locations")) mkdir("locations", 0777);


		$this->countries = "countries.json";

		$this->countries_states = "countries+states.json";
		$this->countries_states_cities = "countries+states+cities.json";
		$this->states_cities = "states+cities.json";

		$repo_location = "http://realestate.mindlogixtech.com/locations/";
		$CI = &get_instance();



		$this->data = $this->global_lib->uri_check();
		$this->data['myHelpers'] = $this;
		$this->data['CI'] = $CI;

		$this->theme = $CI->config->item('admin_theme');
		$this->data['theme'] = $this->theme;
	}

	public function index()
	{


		$this->manage();
	}

	public function manage_repo_files()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('admin_theme');

		$this->load->library('Global_lib');


		$data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['theme'] = $theme;

		$repo_location = "http://realestate.mindlogixtech.com/locations/";
		$data['repo_location'] = $repo_location;

		$repo_files = array();
		if (!file_exists("locations/" . $this->countries)) {
			$repo_files[] = $this->countries;
		}

		if (!file_exists("locations/" . $this->countries_states)) {
			$repo_files[] = $this->countries_states;
		}
		if (!file_exists("locations/" . $this->countries_states_cities)) {
			$repo_files[] = $this->countries_states_cities;
		}
		if (!file_exists("locations/" . $this->states_cities)) {
			$repo_files[] = $this->states_cities;
		}

		$data['repo_files'] = $repo_files;





		$data['content'] = "$theme/locations/admin/manage_repo_files";

		$this->load->view("$theme/header", $data);
	}

	public function manage()
	{

		if (!is_dir("locations/json")) {
			$this->manage_repo_files();
			return false;
		}

		$CI = &get_instance();
		$theme = $CI->config->item('admin_theme');

		$this->load->library('Global_lib');


		/*$data = $this->global_lib->uri_check();
		$data['myHelpers']=$this;*/
		$data = $this->data;

		$this->load->model('Common_model');
		$this->load->helper('text');

		$user_id = $this->session->userdata('user_id');
		$user_type = $this->session->userdata('user_type');

		if (isset($_POST['submit'])) {
			extract($_POST);

			//print_r($_POST); exit;	
			if (isset($loc_tax_settings)) {
				$this->global_lib->update_option('loc_tax_settings', $loc_tax_settings);
				redirect("admin/locations/manage");
			}
			if ($_POST['submit'] == 'add_country') {
				print_r($_POST);
				exit;
			}
		}

		if (isset($_POST['add_country'])) {
			extract($_POST);

			$locations = get_option('locations');
			$locations = json_decode($locations, true);
			if (empty($locations)) {
				$locations = array();
			}

			if (isset($country) && !empty($country)) {
				foreach ($country as $k => $v) {
					$cExp = explode('~', $v);
					$loc_code = $cExp[0];
					$loc_title = $cExp[1];
					$loc_id = $cExp[2];

					$locations['countries'][$loc_code] =
						array(
							"loc_title" => $loc_title,
							"loc_type" => "country",
							"country_id" => $loc_id,
						);
				}
			}

			$locations = json_encode($locations);
			$this->global_lib->update_option('locations', $locations);


			redirect("admin/locations/manage");
		}

		if (isset($_POST['add_country_state'])) {
			extract($_POST);

			$locations = get_option('locations');
			$locations = json_decode($locations, true);
			if (empty($locations)) {
				$locations = array();
			}

			if (isset($cnt_country) && !empty($cnt_country) && isset($cnt_state) && !empty($cnt_state)) {
				foreach ($cnt_state as $state) {
					$cExp = explode('~', $state);
					$loc_code = $cExp[0];
					$loc_title = $cExp[1];
					$loc_id = $cExp[2];

					$locations['countries'][$cnt_country]['states'][$loc_code] =
						array(
							"loc_title" => $loc_title,
							"loc_type" => "state",
							"state_id" => $loc_id
						);
				}
			}

			$locations = json_encode($locations);
			$this->global_lib->update_option('locations', $locations);

			redirect("admin/locations/manage");
		}

		if (isset($_POST['add_state_city'])) {
			extract($_POST);


			$locations = get_option('locations');
			$locations = json_decode($locations, true);
			if (empty($locations)) {
				$locations = array();
			}


			//cnt_state_cities
			if (isset($cnt_state_cities) && !empty($cnt_state_cities)) {
				foreach ($cnt_state_cities as $state) {
					$cExp = explode('~', $state);
					$country_code = $cExp[0];
					$state_code = $cExp[1];
					$loc_code = $cExp[2];
					$loc_title = $cExp[3];
					$loc_id = $cExp[4];

					$locations['countries'][$country_code]['states'][$state_code]['cities'][$loc_code] =
						array(
							"loc_title" => $loc_title,
							"loc_type" => "city",
							"city_id" => $loc_id
						);
				}
			}

			$locations = json_encode($locations);
			$this->global_lib->update_option('locations', $locations);

			redirect("admin/locations/manage");
		}

		if (isset($_POST['add_country_city'])) {
			extract($_POST);


			$locations = get_option('locations');
			$locations = json_decode($locations, true);
			if (empty($locations)) {
				$locations = array();
			}

			/*echo "<pre>";
			print_r($locations);
			print_r($_POST);*/
			$cities_array = array();
			
			//$locations['countries'][$country_code]['states']['no_state']
			/*if(	array_key_exists('states' , $locations['countries'][$country_code])	 &&
				 	array_key_exists('no_state' , $locations['countries'][$country_code]['states'])  &&
				array_key_exists('cities' , $locations['countries'][$country_code]['states']['no_state']) 			
								) 
			*/					
								
								//echo "yes"; else echo "no"
				//$cities_array = $locations['countries'][$country_code]['states']['no_state']['cities'];
			
			//echo "ct ";
			//print_r($locations['countries'][$country_code]['states']['no_state']['cities']);
			//print_r($cities_array);
			
			
			if (isset($cnt_state_cities) && !empty($cnt_state_cities)) {
				
				
				if(	array_key_exists('states' , $locations['countries'][$cnt_country_code_for_city])	 &&
						array_key_exists('no_state' , $locations['countries'][$cnt_country_code_for_city]['states'])  &&
						array_key_exists('cities' , $locations['countries'][$cnt_country_code_for_city]['states']['no_state']) )			
						$cities_array = $locations['countries'][$cnt_country_code_for_city]['states']['no_state']['cities'];	
						
					
				foreach ($cnt_state_cities as $state) {
					$cExp = explode('~', $state);
					$country_code = $cExp[0];
					$state_code = $cExp[1];
					$loc_code = $cExp[2];
					$loc_title = $cExp[3];
					$loc_id = $cExp[4];

					$cities_array[$loc_code] =  array(
						"loc_title" => $loc_title,
						"loc_type" 	=> "city",
						"city_id" 	=> $loc_id
					);

				}
			}
			
			$locations['countries'][$country_code]['states']['no_state'] = array(
						'loc_title' => 'No State',
						'loc_type' => 'state',
						'state_id' => '0',
						'cities' => $cities_array
					);

			
			$locations = json_encode($locations);
			update_option('locations', $locations);

			redirect("admin/locations/manage");
		}

		if (isset($_POST['add_city_zip_sub_area'])) {
			extract($_POST);

			$locations = get_option('locations');
			$locations = json_decode($locations, true);
			if (empty($locations)) {
				$locations = array();
			}

			$country_id = 0;
			$country_code = '';
			$state_id = '';
			$city_id = '0';
			if (isset($cnt_city_id_for_zip_sub_area)) {
				$countries = $locations['countries'];
				foreach ($countries as $country_key => $country) {

					if (isset($country['states'])) {
						$states = $country['states'];
						foreach ($states as $state_key => $state) {
							if (isset($state['cities'])) {
								$cities = $state['cities'];
								foreach ($cities as $city_key => $city) {
									if ($city_key == $cnt_city_id_for_zip_sub_area) {
										$city_id = $city_key;
										$state_code = $state_key;
										$country_code = $country_key;

										$state_id = $state['state_id'];
										$country_id = $country['country_id'];

										break;
									}
								}
							}
							if ($city_id != '0') break;
						}
					}
					if ($city_id != '0') break;
				}


				if ($city_id != '0') {


					$zipcodes = explode(",",   trim($cnt_city_zipcode));

					$sub_areas = explode(",",  trim($cnt_city_sub_area));

					if (count($zipcodes) > 0 && !empty($cnt_city_zipcode)) {
						if (isset($locations['countries'][$country_code]['states'][$state_code]['cities'][$city_id]['zipcodes'])) {
							$old_zipcodes = $locations['countries'][$country_code]['states'][$state_code]['cities'][$city_id]['zipcodes'];
							$locations['countries'][$country_code]['states'][$state_code]['cities'][$city_id]['zipcodes'] = array_merge($old_zipcodes, $zipcodes);
						} else {
							$locations['countries'][$country_code]['states'][$state_code]['cities'][$city_id]['zipcodes'] =  $zipcodes;
						}
					}


					if (count($sub_areas) > 0 && !empty($sub_areas)) {

						if (isset($locations['countries'][$country_code]['states'][$state_code]['cities'][$city_id]['sub_areas'])) {
							$old_sub_areas = $locations['countries'][$country_code]['states'][$state_code]['cities'][$city_id]['sub_areas'];
							$locations['countries'][$country_code]['states'][$state_code]['cities'][$city_id]['sub_areas'] = array_merge($old_sub_areas, $sub_areas);
						} else {
							/*if(count($sub_areas) == 1 && !empty($sub_areas[0])) 
							{*/
							$locations['countries'][$country_code]['states'][$state_code]['cities'][$city_id]['sub_areas'] =  $sub_areas;
							/*}*/
						}
					}

					$locations = json_encode($locations);
					$this->global_lib->update_option('locations', $locations);
				}
			}

			redirect("admin/locations/manage");
		}

		if (isset($_GET['action']) && isset($_GET['tax'])) {
			if (isset($_GET['status']) && ($_GET['status'] == 'active' || $_GET['status'] == 'inactive')) {
				$loc_tax_settings = get_option('loc_tax_settings');
				$loc_tax_settings = json_decode($loc_tax_settings, true);

				foreach ($loc_tax_settings as $loc_key => $loc_taxes) {

					if ($_GET['tax'] == $loc_key) {

						if ($_GET['status'] == 'active')
							$loc_tax_settings[$loc_key]['enabled'] = true;
						if ($_GET['status'] == 'inactive')
							$loc_tax_settings[$loc_key]['enabled'] = false;
					} else if ($_GET['tax'] ==   $loc_taxes['tax_type']) {


						if ($_GET['status'] == 'active')
							$loc_tax_settings[$loc_key]['enabled'] = true;
						if ($_GET['status'] == 'inactive')
							$loc_tax_settings[$loc_key]['enabled'] = false;
					}/**/

					/*if($_GET['status'] == 'active')
						$loc_tax_settings[$loc_taxes['tax_type']]['enabled'] = true; 
					if($_GET['status'] == 'inactive')
						$loc_tax_settings[$loc_taxes['tax_type']]['enabled'] = false; */
				}
				$loc_tax_settings = json_encode($loc_tax_settings);
				$this->global_lib->update_option('loc_tax_settings', $loc_tax_settings);
				redirect("admin/locations/manage");
			}
		}

		
		if (isset($_POST['show_city_zip_sub_area'])) {
			extract($_POST);

			
			if (isset($options) && !empty($options)) {
					foreach ($options as $key => $value) {
						if (is_array($value))
							$value = json_encode($value);
						update_option($key, $value);
					}
				}

			redirect("admin/locations/manage");
		}
		
		
		
		$data['loc_tax_settings'] = get_option('loc_tax_settings');
		$data['locations'] = get_option('locations');

		$data['theme'] = $theme;

		$data['page_title'] = 'Manage Locations Hierarchy';

		$data['content'] = "locations/admin/manage";

		$this->load->view("$theme/header", $data);
	}
}
