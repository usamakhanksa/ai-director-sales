<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Compare extends MY_Controller
{

	public function _remap($method = null, $args = null)
	{
		$multi_lang = $this->enable_multi_lang;
		$default_lang = $this->default_language;
		if ($method == null || $method == 'index') {
			redirect("main", 'location');
		} else if (count($args) == 0) {
			redirect("main", 'location');
		} else {

			$properties = array();
			if (!$multi_lang) {
				$lang = $default_lang;
				$properties[] = $method;
				foreach ($args as $arg) {
					$properties[] = $arg;
				}
			} else {
				$lang  = $method;
				foreach ($args as $arg) {
					$properties[] = $arg;
				}
			}
			$this->index($lang, $properties);
		}
	}

	public function index($lang = 'en', $properties = array())

	{
		$CI = &get_instance();

		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $data = $this->global_lib->uri_check();

		$data['myHelpers'] = $this;

		$this->load->model('Common_model');
		$fields = array();
		$i = 0;
		$props = array();
		foreach ($properties as $propertyId) {

			$ids = explode("~", $propertyId);

			$sql = "select prop.*,
					   pld.title as title , 
					   pld.description as description, 
					   pld.language as language,
					   pld.price as price
					   from properties  as prop 
			   inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$lang'
			   and pld.title != '' and pld.price != ''
			   where prop.p_id = $ids[1] and prop.deleted = 'N'";
			$property_result = $this->Common_model->commonQuery($sql);

			if ($property_result->num_rows() == 0) {
				$sql = "select prop.* from properties  as prop  where prop.p_id = '$ids[1]'";
				$property_result = $this->Common_model->commonQuery($sql);
			}
			$props[] = $property_result;
		}

		$data['property_list'] = $props;

		$this->load->helper('text');
		$data['theme'] = $theme;

		$data['content'] = "$theme/compare_properties";
		$data['compared_data'] = $properties;


		$this->load->view("$theme/header", $data);

		$data['sidebar'] = 'sidebar-left';
	}
}
