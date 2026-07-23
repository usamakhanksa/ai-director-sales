<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings_lib
{

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

		if (isset($cur_status) && $cur_status == 'Y' && file_exists($modules_path . $plugin_name . '/libraries/' . ucfirst($plugin_name) . '_lib.php')) {



			$CI->load->library($plugin_name . '/' . ucfirst($plugin_name) . '_lib', null, $plugin_name . '_lib_obj');

			/*$plugin_obj = $plugin_name.'_obj';*/
			$plugin_obj = $plugin_name . '_lib_obj';

			if (method_exists($CI->$plugin_obj, 'module_install'))
				$CI->$plugin_obj->module_install();
		} else if (isset($cur_status) && $cur_status == 'N' && file_exists($modules_path . $plugin_name . '/libraries/' . ucfirst($plugin_name) . '_lib.php')) {
			$CI->load->library($plugin_name . '/' . ucfirst($plugin_name) . '_lib', null, $plugin_name . '_lib_obj');

			/**$plugin_obj = $plugin_name.'_obj';*/
			$plugin_obj = $plugin_name . '_lib_obj';


			if (method_exists($CI->$plugin_obj, 'module_uninstall'))
				$CI->$plugin_obj->module_uninstall();
		}

		$modules_path = APPPATH . 'modules/';

		$json_data = json_encode($plugin_list);
		file_put_contents($modules_path . "/modules.json", $json_data);

		$CI->global_lib->update_option('site_modules', $json_data);

		echo 'success';
	}

	public function update_keywords_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$CI->load->model('Common_model');


		$datai = array(
			$lang_slug => addslashes($value)
		);
		
		/*/print_r($datai);*/
		$encId = DecryptClientID($lang_id);
		$CI->Common_model->commonUpdate('languages', $datai, 'lang_id', $encId);

		echo 'success';
	}

	public function import_lang_keyword_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$CI->load->model('Common_model');

		/*if ($lang_for == 'front') {
			$file_content = $CI->global_lib->get_include_contents(FCPATH . 'application/language/import-language-files/' . $lang_file);
		} else {
			$file_content = $CI->global_lib->get_include_contents(FCPATH . 'application/language/import-language-files/admin/' . $lang_file);
		}*/

		
		$file_content ="";
		if ($lang_for == 'front') {
			$filename = APPPATH .'language/import-language-files/' . $lang_file;	
			if(file_exists($filename))
			{
				
				$file_content = $CI->global_lib->get_include_contents(FCPATH . 'application/language/import-language-files/' . $lang_file);
			}
		}else{	
			/*$filename = APPPATH .'language/import-language-files/admin/' . $lang_file;
			if(file_exists($filename ))
			{	
				
				$file_content = $CI->global_lib->get_include_contents(FCPATH . 'application/language/import-language-files/admin/' . $lang_file);
				
			}*/
			
			$filename = APPPATH .'language/import-language-files/admin/' . $lang_file;
			if(file_exists($filename ))
			{	
				
				$file_content = $CI->global_lib->get_include_contents(FCPATH . 'application/language/import-language-files/admin/' . $lang_file);
				
			}
			
			
		}
		
		//print_r(file_get_contents(FCPATH . 'application/language/import-language-files/admin/' . $lang_file));
		
		//print_r( utf8_decode(  $file_content));/**/
		
		$myarray = $CI->global_lib->strtoarray($file_content);
		/**/
		//header('Content-Type: text/html; charset=ISO-8859-1');
		//echo $filename;
		//print_r($myarray); //exit; 
		
		
		
		$sql = "select * from languages where lang_for = '$lang_for'";
		$result = $CI->Common_model->commonQuery($sql);
		if ($result->num_rows() > 0 && !empty($myarray)) {
			
			/*print_r($result->result());*/
			foreach ($result->result() as $row) {
				$keyword = trim($row->keyword);
				if (array_key_exists($keyword, $myarray)) {
					if ($overright_keyword == 'Y' || empty($row->$lang)) {
						$datai = array(
							/*$lang => $myarray[$keyword]*/
							$lang => addslashes(trim(utf8_encode($myarray[$keyword])))
							
						);
						
						/**
						mlx_get_norm_string($keyword = "")
						*/
						
						
						$CI->Common_model->commonUpdate('languages', $datai, 'lang_id', $row->lang_id);
					}
					unset($myarray[$keyword]);
				}
			}
		}
		

		if (!empty($myarray)) {
			
			foreach ($myarray as $k => $v) {
				$datai = array('keyword' => addslashes(trim(utf8_encode($k))), 
								'lang_for' => $lang_for, 
								$lang => addslashes(utf8_encode($v)));
				$CI->Common_model->commonInsert('languages', $datai);
			}
		}

		$site_language = get_option('site_language');
		$site_language_array = json_decode($site_language, true);
		foreach ($site_language_array as $k => $v) {
			$lang_exp = explode('~', $v['language']);
			$lang_name = $lang_exp[0];
			$lang_code = $lang_exp[1];
			$lang_slug = $CI->global_lib->get_slug($lang_name, '_');
			
			/*if($lang == $current_lang_slug)*/
			/*front*/
			
			if ($lang_for == 'front') {
			
			if (!is_dir("application/language")) {
				mkdir("application/language", 0777);
			}

			if (!is_dir("application/language/$lang_slug")) {
				mkdir("application/language/$lang_slug", 0777);
			}
			if (file_exists("application/language/$lang_slug/" . $lang_slug . "_lang.php")) {
				if ($lang_slug != 'english')
					unlink("application/language/$lang_slug/" . $lang_slug . "_lang.php");
			}

			$fp = fopen("application/language/$lang_slug/" . $lang_slug . "_lang.php", "wb");
			if ($fp) {
				$output = "<?php \n\n";
				$keyword_result = $CI->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'front'
									order by lang_id DESC");
				if ($keyword_result->num_rows() > 0) {
					foreach ($keyword_result->result() as $row) {
						//$output .= '$lang["'.$row->keyword.'"] = "'.$row->$lang_slug.'";'."\n";
						$output .= '$lang' . "['" . $row->keyword . "'] = '" . addslashes($row->$lang_slug) . "';\n";
					}
				}
				fwrite($fp, $output);
				fclose($fp);
			}
			
			} /** front */
			/*back*/
			
			if ($lang_for == 'back') {
			
			if (!is_dir("application/language/admin/".$lang_slug)) {
				mkdir("application/language/admin/".$lang_slug, 0777);
			}

			if (!is_dir("application/language/admin/".$lang_slug)) {
				mkdir("application/language/admin/".$lang_slug, 0777);
			}
			if (file_exists("application/language/admin/".$lang_slug."/" . $lang_slug . "_lang.php")) {
				if ($lang_slug != 'english')
					unlink("application/language/admin/".$lang_slug."/" . $lang_slug . "_lang.php");
			}
			/*echo "select keyword,$lang_slug from languages where lang_for = 'back'
									order by lang_id DESC <br/>"; continue;*/
			$fp = fopen("application/language/admin/".$lang_slug."/" . $lang_slug . "_lang.php", "wb");
			if ($fp) {
				$output = "<?php \n\n";
				$keyword_result = $CI->Common_model->commonQuery("select keyword,$lang_slug from languages where lang_for = 'back'
									order by lang_id DESC");
				if ($keyword_result->num_rows() > 0) {
					foreach ($keyword_result->result() as $row) {
						//$output .= '$lang["'.$row->keyword.'"] = "'.$row->$lang_slug.'";'."\n";
						$output .= '$lang' . "['" . $row->keyword . "'] = '" . addslashes($row->$lang_slug) . "';\n";
					}
				}
				fwrite($fp, $output);
				fclose($fp);
			}
			}
			/**  back ***/
			
		}
		
		echo 'success';
	}

	public function export_lang_keyword_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$CI->load->model('Common_model');

		$output = "";
		$sql = "select * from languages where lang_for = '$lang_for'";
		$result = $CI->Common_model->commonQuery($sql);
		if ($result->num_rows() > 0) {
			foreach ($result->result() as $row) {
				$output .= '$lang' . "['" . $row->keyword . "'] = '" . addslashes($row->$lang) . "';\n";
			}
		}
		$time_stamp = time();
		$file_name = '';
		$file_path = '';

		if ($lang_for == 'front' && $export_type == 'save') {
			if (!is_dir("application/language/import-language-files")) {
				mkdir("application/language/import-language-files", 0777);
			}

			$fp = fopen("application/language/import-language-files/" . $lang . "-$time_stamp.lang", "wb");
			$file_name = $lang . "-$time_stamp.lang";
			$file_path = base_url("application/language/import-language-files/");
			if ($fp) {
				fwrite($fp, $output);
				fclose($fp);
			}
		} else if ($lang_for == 'back' && $export_type == 'save') {
			if (!is_dir("application/language/import-language-files/admin")) {
				mkdir("application/language/import-language-files/admin", 0777);
			}

			$fp = fopen("application/language/import-language-files/admin/" . $lang . "-$time_stamp.lang", "wb");
			$file_name = $lang . "-$time_stamp.lang";
			$file_path = base_url("application/language/import-language-files/admin/");
			if ($fp) {
				fwrite($fp, $output);
				fclose($fp);
			}
		} else if ($export_type == 'download') {
			$file_name = $lang . "-$time_stamp.lang";

			header('Content-type: application/json');
			echo json_encode(array('file_name' => $file_name, 'file_content' => $output));
		} else {
			echo 'success';
		}
	}
}
