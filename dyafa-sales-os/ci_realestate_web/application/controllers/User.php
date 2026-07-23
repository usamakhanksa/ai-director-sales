<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller
{


	function _remap($method = null, $args = null)
	{
		$multi_lang = $this->enable_multi_lang;
		$default_lang = $this->default_language;

		if ($multi_lang) {
			$curl = current_url();
			$parts = explode("/", $curl);

			$find_lang = false;
			if (in_array($default_lang, $parts)) {
				$find_lang = true;
			}

			if (!$find_lang) {
				if ($method == null  || $method == 'index')
					$url = "search/$default_lang/agents";
				else if ($method == 'agent') {
					$url = "user/$method/$default_lang/" . $args[0];
				}
				redirect($url, 'location');
			}
		}

		if ($method == null  || $method == 'index') {
			$url = "search/$default_lang/agents";
			redirect($url, 'location');
		} else if ($method == 'agent') {
			$this->agent($default_lang, $args[1]);
		}
	}

	public function agent($lang = 'en', $agents = NULL)
	{
		$this->load->model('Common_model');
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		if ($agents == null)
			redirect('/', 'location');

		$this->load->library('Global_lib');
		$this->load->helper('text');
		$data = $this->global_lib->uri_check();

		$agent_exp = explode('~', $agents);
		$agent_name = $agent_exp[0];
		$agent_id = $agent_exp[1];
		$dec_id = $this->DecryptClientID($agent_exp[1]);
		$sql = "SELECT p.* FROM properties as p 
				inner JOIN users AS u ON p.created_by = u.user_id AND u.user_status = 'Y' 
				and u.user_type = 'agent'  and u.user_id = $dec_id
				order by p.p_id DESC";
		$data['agents_propeties'] = $this->Common_model->commonQuery($sql);

		$sql = "SELECT user_id,user_email,user_type FROM users AS u 
				where u.user_status = 'Y' and u.user_type = 'agent'  and u.user_id = $dec_id
				";
		$user_result = $this->Common_model->commonQuery($sql);

		$data['single_user'] = $user_result->row();

		$data['theme'] = $theme;
		$data['myHelpers'] = $this;
		$data['page_title'] = "Agent Single";
		$data['has_banner'] = false;

		$data['content'] = "$theme/agent_single";
		$this->load->view("$theme/header", $data);
	}
}
