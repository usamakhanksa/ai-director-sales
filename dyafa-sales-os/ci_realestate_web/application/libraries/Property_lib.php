<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Property_lib
{

	var $url = "";



	public function Index()
	{
	}



	// Update Get Option
	public function get_url($property_id, $property)
	{
		$CI = &get_instance();

		$return_menu_item = base_url('property/:lang/' . $property->slug . '~' . $property_id);

		if (isset($CI->enable_multi_lang) && $CI->enable_multi_lang  == true) {
			/*$lang = $CI->default_language;	*/
			$lang = $CI->default_lang_code;
			$return_menu_item  =  str_replace(":lang", $lang, $return_menu_item);
		} else {
			$return_menu_item  =  str_replace("/:lang", "", $return_menu_item);
		}



		return $return_menu_item;
	}

	public function get_property_detail($id = NULL, $column = NULL)
	{
		$CI = &get_instance();

		$query = $CI->Common_model->commonQuery("SHOW COLUMNS FROM `properties` LIKE '$column'");
		if ($query->num_rows() > 0) 
		{
			$query = $CI->Common_model->commonQuery("select $column from `properties` where p_id = '$id'");

			if ($query->num_rows() > 0) {
				$row = $query->row();
				return $row->$column;
			}
		}
		return false;
	}

	public function get_property_meta($id = NULL, $key = NULL)
	{
		$CI = &get_instance();

		$query = $CI->Common_model->commonQuery("select meta_value from property_meta where property_id = '$id' AND meta_key = '$key' ");

		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->meta_value;
		}
		return false;
	}

	public function update_property_meta($property_id, $key, $val)
	{
		$CI = &get_instance();
		$query = $CI->Common_model->commonQuery("select meta_id from property_meta where property_id = '$property_id' AND meta_key = '$key' ");

		if ($query->num_rows() > 0) {
			$row = $query->row();
			$meta_id = $row->meta_id;
			$datai = array('meta_value' => $val);

			return $metaid = $CI->Common_model->commonUpdate('property_meta', $datai, 'meta_id', $meta_id);
		} else {
			$datai = array('meta_key' => $key,	'meta_value' => $val, 'property_id' => $property_id);

			return $metaid = $CI->Common_model->commonInsert('property_meta', $datai);
		}
	}




	public function remove_property_images($property_id = NULL){
		
		$CI = &get_instance();
		
		if(!is_numeric($property_id)) $property_id = DecryptClientID($property_id);
		
		$pt_result = $CI->Common_model->commonQuery("select property_images from properties where p_id = $property_id ");
		if ($pt_result->num_rows() > 0) {
			$prop_row = $pt_result->row();
			$property_images = $prop_row->property_images;
			
			$parent_images_sql = "SELECT * FROM post_images where image_id in ($property_images)";
			$parent_images_result = $CI->Common_model->commonQuery($parent_images_sql);
			
			if ($parent_images_result->num_rows() > 0) {
				foreach ($parent_images_result->result() as $row) {
				
					$image_name = $row->image_name;
					$image_path = $row->image_path;
					if (!empty($image_name) &&  file_exists($image_path . $image_name))
						unlink($image_path . $image_name);
				}	
			}
			
			$parent_images_delete_sql = "delete  FROM post_images where image_id in ($property_images)";
			$CI->Common_model->commonQuery($parent_images_delete_sql);
			
			$other_images_sql = "SELECT * FROM post_images where parent_image_id in ($property_images)";
			$other_images_result = $CI->Common_model->commonQuery($other_images_sql);
			
			if ($other_images_result->num_rows() > 0) {
				foreach ($other_images_result->result() as $row) {
				
					$image_name = $row->image_name;
					$image_path = $row->image_path;
					if (!empty($image_name) &&  file_exists($image_path . $image_name))
						unlink($image_path . $image_name);
				}	
			}
			
			$parent_images_delete_sql = "delete  FROM post_images where parent_image_id in ($property_images)";
			$CI->Common_model->commonQuery($parent_images_delete_sql);
			
			
		}
		
		
	}






	public function manage_direction_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$CI->load->library('global_lib');
		$CI->load->model('Common_model');

		/*
        $decId = $CI->global_lib->DecryptClientId($order_id);
        $result = $CI->Common_model->commonQuery("select * from orders where order_id = '$decId'");

        $item_row = $result->row();
		*/

		if ($CI->global_lib->get_option('property_distances')) {
			$distances_list = json_decode($CI->global_lib->get_option('property_distances'), true);
		}
		ob_start();
?>
		<form method="POST" class="direction-modal-form">
			<div class="modal-header">
				<h5 class="modal-title">
					<span><?php echo $direction; ?></span>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</h5>
			</div>

			<div class="modal-body">
				<input type="hidden" name="direction" value="<?php echo $direction; ?>">
				<div class="row">
					<div class="col-md-4">

						<input type="text" id="title" name="title" class="form-control" placeholder="Title here">
					</div>
					<div class="col-md-4">

						<select id="entity" name="entity" class="form-control" required>
							<option value=""><?php echo mlx_get_lang('Select Entities'); ?></option>
							<?php if (isset($distances_list) && !empty($distances_list)) {
								foreach ($distances_list as  $k => $v) {
									echo $k;
							?>
									<option value="<?php echo $v; ?>"><?php echo mlx_get_lang($v); ?></option>
							<?php }
							} ?>
						</select>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<input id="measurement" type="number" min="0" step=".1" value="" name="measurement" class="form-control" required>
							<div class="input-group-btn measurement-group">
								<input type="hidden" name="measurement_type" value="Meter" class="measurement_type" id="measurement_type" >
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Meter&nbsp;&nbsp;<span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu custom-dropdown-menu">
									<li><a  data-val="<?php echo mlx_get_lang('Meter'); ?>"><?php echo mlx_get_lang('Meter'); ?></a></li>
									<li><a  data-val="<?php echo mlx_get_lang('KM'); ?>"><?php echo mlx_get_lang('KM'); ?></a></li>
								</ul>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-<?php echo $CI->global_lib->get_skin_class(); ?>"><?php echo mlx_get_lang('Submit'); ?></button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo mlx_get_lang('Close'); ?></button>
			</div>
			<div class="modal-overlay">
				<i class="fa fa-spinner fa-spin fa-2x"></i>
			</div>
		</form>


	<?php
		$modal_content = ob_get_contents();
		ob_end_clean();

		header('Content-type: application/json');
		echo json_encode(array('modal_content' => $modal_content));
	}

	public function add_direction_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$CI->load->library('global_lib');
		$CI->load->model('Common_model');

		ob_start();
	?>
		<li class="list-group-item">
			<span class="badge badge-danger">X</span>
			<?php if (isset($title) && !empty($title))
				echo ucfirst($title) . '<br />';
			?>
			<?php if (isset($entity) && !empty($entity))
				echo '<strong>' . ucfirst($entity) . '</strong> - ';
			?>
			<?php echo $measurement . ' ' . ucfirst($measurement_type);
			?>
			<input type="hidden" name="distance[<?php echo $direction; ?>][title][]" value="<?php echo $title ?>">
			<input type="hidden" name="distance[<?php echo $direction; ?>][entity][]" value="<?php echo $entity ?>">
			<input type="hidden" name="distance[<?php echo $direction; ?>][measurement][]" value="<?php echo $measurement ?>">
			<input type="hidden" name="distance[<?php echo $direction; ?>][measurement_type][]" value="<?php echo $measurement_type ?>">
		</li>
<?php
		$output = ob_get_contents();
		ob_end_clean();

		header('Content-type: application/json');
		echo json_encode(array('output' => $output, 'direction' => $direction));
	}

	public function insert_property($post_args = null)
	{
		if (isset($_POST['callback'])) {
			extract($_POST);
		} else {
			extract($post_args);
		}

		$CI = &get_instance();
		$CI->load->library('global_lib');
		$CI->load->model('Common_model');

		$config = array('field' => 'slug', 'title' => 'title', 'table' => 'properties', 'id' => 'p_id');
		$CI->load->library('Slug_lib', $config);

		$first_selected_lang_code = '';
		if (isset($multi_lang) && !empty($multi_lang))
		{
			$keys = array_keys($multi_lang);
			foreach($keys as $kk=>$vv)
			{
				if(!empty($multi_lang[$vv]['title']))
				{
					$first_selected_lang_code = $vv;
					$title = $multi_lang[$vv]['title'];
					break;
				}
			}
		}
		
		$datap = array('title' => $title,);
		$slug = $CI->slug_lib->create_uri($datap);

		$property_images = '';
		if (isset($addedImgFromMediaLibrary) && !empty($addedImgFromMediaLibrary)) {
			$data_exp = explode(',', $addedImgFromMediaLibrary);
			$data_exp_array = array();
			foreach ($data_exp as $k => $v) {
				$data_exp_array[] = DecryptClientID($v);
			}
			$property_images = implode(',', $data_exp_array);
		}

		if (!isset($indoor_amenities))
			$indoor_amenities = array();
		if (!isset($outdoor_amenities))
			$outdoor_amenities = array();

		$distance_list_array = array();

		if (isset($distance) && !empty($distance)) {

			foreach ($distance as $dk => $dv) {
				if (isset($dv['title'])) {
					foreach ($dv['title'] as $dtk => $dtv) {
						$distance_list_array[$dk][$dtk]['title'] = $dtv;
						$distance_list_array[$dk][$dtk]['entity'] = $dv['entity'][$dtk];
						$distance_list_array[$dk][$dtk]['measurement'] = $dv['measurement'][$dtk];
						$distance_list_array[$dk][$dtk]['measurement_type'] = $dv['measurement_type'][$dtk];
					}
				}
			}
		}

		$video_url_string = '';
		if (isset($video_url) && !empty($video_url)) {
			$emptyRemoved = array_filter($video_url);
			$video_url_string = implode(',', $emptyRemoved);
		}

		if (!isset($country)) $country = '';
		if (!isset($state)) $state = '';
		if (!isset($city)) $city = '';
		if (!isset($zipcode)) $zipcode = '';
		if (!isset($sub_area)) $sub_area = '';

		if (isset($multi_lang) && !empty($multi_lang)) {
			$keys = array_keys($multi_lang);
			/*
			$description = $multi_lang[$keys[0]]['description'];
			$short_description = $multi_lang[$keys[0]]['short_description'];
			$price = $multi_lang[$keys[0]]['price'];
			$seo_meta_keywords = $multi_lang[$keys[0]]['seo_meta_keywords'];
			$seo_meta_description = $multi_lang[$keys[0]]['seo_meta_description'];
			*/

			$description = $multi_lang[$first_selected_lang_code]['description'];
			$short_description = $multi_lang[$first_selected_lang_code]['short_description'];
			$price = $multi_lang[$first_selected_lang_code]['price'];
			$seo_meta_keywords = $multi_lang[$first_selected_lang_code]['seo_meta_keywords'];
			$seo_meta_description = $multi_lang[$first_selected_lang_code]['seo_meta_description'];

		} else {
			if (!isset($short_description))
				$short_description = '';
			if (!isset($description))
				$description = '';
			if (!isset($address))
				$address = '';
			if (!isset($street_address))
				$street_address = '';
			if (!isset($seo_meta_keywords))
				$seo_meta_keywords = '';
			if (!isset($seo_meta_description))
				$seo_meta_description = '';
		}

		if (!isset($size))
			$size = '';
		else if (!empty($size))
			$size .= "~" . $size_measure;

		if (isset($user_id))
		{	
			if (!is_numeric($user_id))
				$user_id = DecryptClientID($user_id);
			
		}else
			$user_id = 0;

		if (!isset($lat))
			$lat = '';
		if (!isset($long))
			$long = '';
		if (!isset($price))
			$price = '';

		if (isset($property_type))
			$property_type = DecryptClientID($property_type);
		else
			$property_type = 0;

		if (!isset($property_for))
			$property_for = '';
		if (!isset($bedroom))
			$bedroom = '';
		if (!isset($bathroom))
			$bathroom = '';
		if (!isset($garage))
			$garage = '';
		if (!isset($status))
			$status = '';
			$datai = array(
				'title' => trim($title),
				'short_description' => trim($short_description),
				'description' => trim($description),
				'address' => $address,
				'street_address' => $street_address,
				'lat' => $lat,
				'long' => $long,
				'price' => str_replace(',', '', $price),
				'size' => $size,
				'property_type' => $property_type,
				'property_for' => strtolower($property_for),
				'bedroom' => $bedroom,
				'bathroom' => $bathroom,
				'garage' => $garage,
				
				'country' => $country,
				'state' => $state,
				'city' => $city,
				'zip_code' => $zipcode,
				'sub_area' => $sub_area,
				
				
				
				
				'indoor_amenities' => json_encode($indoor_amenities),
				'outdoor_amenities' => json_encode($outdoor_amenities),
				'distance_list' => json_encode($distance_list_array),
				'video_urls' => $video_url_string,
				'property_images' => $property_images,
				'created_on' => time(),
				'status' => $status,
				'created_by' => $user_id,
				'slug' => $slug,
				'seo_meta_keywords' => $seo_meta_keywords,
				'seo_meta_description' => $seo_meta_description
			);

		if (isset($custom_fields) && count($custom_fields) > 0) {
			foreach ($custom_fields as $k => $v) {
				$datai[$k] = $v;
			}
		}
		$datai  =  apply_filters("cms_admin_update_property_additional_fields" , $datai);

		$p_id = $CI->Common_model->commonInsert('properties', $datai);

		if (isset($multi_lang) && !empty($multi_lang)) {
			foreach ($multi_lang as $k => $v) {
				if ($v['title'] != '') {
					$datai = array(
						'title' => addslashes(trim($v['title'])),
						'short_description' => addslashes(trim($v['short_description'])),
						'description' => addslashes(trim($v['description'])),
						'price' => str_replace(',', '', $v['price']),

						'seo_meta_keywords' => addslashes(trim($v['seo_meta_keywords'])),
						'seo_meta_description' => addslashes(trim($v['seo_meta_description'])),
						'p_id' => $p_id,
						'language' => $k
					);
					
					$datai  =  apply_filters("cms_admin_update_property_lang_additional_fields" , $datai);
					
					
					$CI->Common_model->commonInsert('property_lang_details', $datai);
				}
			}
		} else {
			$default_language_code = $CI->default_language;

			$datai = array(
				'title' => addslashes(trim($title)),
				'short_description' => addslashes(trim($short_description)),
				'description' => addslashes(trim($description)),
				'price' => str_replace(',', '', $price),
				'seo_meta_keywords' => addslashes(trim($seo_meta_keywords)),
				'seo_meta_description' => addslashes(trim($seo_meta_description)),
				'p_id' => $p_id,
				'language' => $default_language_code
			);
			$datai  =  apply_filters("cms_admin_update_property_lang_additional_fields" , $datai);
			
			$CI->Common_model->commonInsert('property_lang_details', $datai);
		}

		if (!isset($property_meta) ) 
			$property_meta =  array();
		$property_meta  =  apply_filters("cms_admin_update_property_meta_additional_fields" , $property_meta);

		if (isset($property_meta) && !empty($property_meta)) {
			foreach ($property_meta as $meta_key => $meta_val) {
				$datai = array(
					'meta_key' => $meta_key,
					'meta_value' => $meta_val,
					'property_id' => $p_id,
				);
				$CI->Common_model->commonInsert('property_meta', $datai);
			}
		}

		if (isset($_POST['callback'])) {
			$datai = array(
				'user_email' => $email,
				//'user_password' => addslashes(trim($short_description)),
				//'user_type' => 'visitors',
			);
			$CI->Common_model->commonUpdate('users', $datai, 'user_id', $user_id);
			echo json_encode(array("status" => 200, "msg" => "Property Added Successfully"));
		} else {
			return $p_id;
		}
	}

	public function update_property($post_args)
	{
		extract($post_args);
		$CI = &get_instance();
		$CI->load->library('global_lib');
		$CI->load->model('Common_model');



		$decId = DecryptClientID($p_id);


		/*
		if (isset($multi_lang) && !empty($multi_lang)) {
			$keys = array_keys($multi_lang);
			$title = $multi_lang[$keys[0]]['title'];
		}
		*/
		$first_selected_lang_code = '';
		if (isset($multi_lang) && !empty($multi_lang))
		{
			$keys = array_keys($multi_lang);
			foreach($keys as $kk=>$vv)
			{
				if(!empty($multi_lang[$vv]['title']))
				{
					$first_selected_lang_code = $vv;
					$title = $multi_lang[$vv]['title'];
					break;
				}
			}
		}

		$property_images = '';
		if (isset($addedImgFromMediaLibrary) && !empty($addedImgFromMediaLibrary)) {
			$data_exp = explode(',', $addedImgFromMediaLibrary);
			$data_exp_array = array();
			foreach ($data_exp as $k => $v) {
				$data_exp_array[] = DecryptClientID($v);
			}
			$property_images = implode(',', $data_exp_array);
		}

		if (!isset($indoor_amenities))
			$indoor_amenities = array();
		if (!isset($outdoor_amenities))
			$outdoor_amenities = array();

		$distance_list_array = array();
		if (isset($distance) && !empty($distance)) {

			foreach ($distance as $dk => $dv) {
				if (isset($dv['title'])) {
					foreach ($dv['title'] as $dtk => $dtv) {
						$distance_list_array[$dk][$dtk]['title'] = $dtv;
						$distance_list_array[$dk][$dtk]['entity'] = $dv['entity'][$dtk];
						$distance_list_array[$dk][$dtk]['measurement'] = $dv['measurement'][$dtk];
						$distance_list_array[$dk][$dtk]['measurement_type'] = $dv['measurement_type'][$dtk];
					}
				}
			}
		}



		$video_url_string = '';
		if (isset($video_url) && !empty($video_url)) {
			$emptyRemoved = array_filter($video_url);
			$video_url_string = implode(',', $emptyRemoved);
		}



		if (isset($multi_lang) && !empty($multi_lang)) 
		{
			$keys = array_keys($multi_lang);

			/*
			$description = $multi_lang[$keys[0]]['description'];
			$short_description = $multi_lang[$keys[0]]['short_description'];
			$price = $multi_lang[$keys[0]]['price'];
			$seo_meta_keywords = $multi_lang[$keys[0]]['seo_meta_keywords'];
			$seo_meta_description = $multi_lang[$keys[0]]['seo_meta_description'];
			*/

			$description = $multi_lang[$first_selected_lang_code]['description'];
			$short_description = $multi_lang[$first_selected_lang_code]['short_description'];
			$price = $multi_lang[$first_selected_lang_code]['price'];
			$seo_meta_keywords = $multi_lang[$first_selected_lang_code]['seo_meta_keywords'];
			$seo_meta_description = $multi_lang[$first_selected_lang_code]['seo_meta_description'];
			
		} 
		else
		{
			if (!isset($short_description))
				$short_description = '';
			if (!isset($description))
				$description = '';
			if (!isset($address))
				$address = '';
			if (!isset($street_address))
				$street_address = '';
			if (!isset($seo_meta_keywords))
				$seo_meta_keywords = '';
			if (!isset($seo_meta_description))
				$seo_meta_description = '';
		}

		$description = addslashes($description);
		$short_description =  addslashes($short_description);

		if (!isset($size))
			$size = '';
		else if (!empty($size))
			$size .= "~" . $size_measure;

		if (isset($user_id))
		{
			if (!is_numeric($user_id))
				$user_id = DecryptClientID($user_id);
		}
		else
			$user_id = 0;

		if (!isset($lat))
			$lat = '';
		if (!isset($long))
			$long = '';
		if (!isset($price))
			$price = '';

		if (isset($property_type))
			$property_type = DecryptClientID($property_type);
		else
			$property_type = 0;

		if (!isset($property_for))
			$property_for = '';
		if (!isset($bedroom))
			$bedroom = '';
		if (!isset($bathroom))
			$bathroom = '';
		if (!isset($garage))
			$garage = '';

		$datai = array(
			'title' => trim($title),
			'short_description' => trim($short_description),
			'description' => trim($description),
			'address' => $address,
			'street_address' => $street_address,

			'lat' => $lat,
			'long' => $long,
			'price' => str_replace(',', '', $price),
			'size' => $size,
			'property_type' => $property_type,
			'property_for' => strtolower($property_for),
			'bedroom' => $bedroom,
			'bathroom' => $bathroom,
			'garage' => $garage,
			'indoor_amenities' => json_encode($indoor_amenities),
			'outdoor_amenities' => json_encode($outdoor_amenities),
			'distance_list' => json_encode($distance_list_array),
			'video_urls' => $video_url_string,
			'property_images' => $property_images,
			'status' => $status,
			'slug' => $slug,
			'seo_meta_keywords' => $seo_meta_keywords,
			'seo_meta_description' => $seo_meta_description,
			'created_by' => $user_id,
		);




		if (isset($custom_fields) && count($custom_fields) > 0) {
			foreach ($custom_fields as $k => $v) {
				$datai[$k] = $v;
			}
		}
		
		if (isset($slug) && isset($old_slug) && !empty($slug) &&  $slug != $old_slug) {
			$config = array(
				'field' => 'slug',
				'title' => 'title',
				'table' => 'properties',
				'id' => 'p_id',
			);
			$CI->load->library('Slug_lib', $config);

			$datap = array(
				'slug' => $slug,
			);
			$slug = $CI->slug_lib->create_uri($datap);
			$datai['slug'] = $slug;
		}


		$datai  =  apply_filters("cms_admin_update_property_additional_fields" , $datai);

		/*echo "<pre>"; print_r($datai); exit;
		echo "<pre>";print_r($datai); exit;*/

		$CI->Common_model->commonUpdate('properties', $datai, 'p_id', $decId);

		if (isset($multi_lang) && !empty($multi_lang)) {
			foreach ($multi_lang as $k => $v) {

				if (
					isset($v['property_delete']) && isset($v['pld_id']) && !empty($v['pld_id']) &&
					($v['property_delete'] == $v['pld_id'])
				) {
					$CI->Common_model->commonQuery("delete from property_lang_details
								where pld_id = " . $v['pld_id']);
					continue;
				}


				if ($v['title'] != '') {
					$property_lang_details = $CI->Common_model->commonQuery("select * from property_lang_details
								where p_id = $decId and language = '$k' ");
					if ($property_lang_details->num_rows() == 0) {

						$datai = array(
							'title' => addslashes(trim($v['title'])),
							'description' => addslashes(trim($v['description'])),
							'short_description' => addslashes(trim($v['short_description'])),
							'seo_meta_keywords' => addslashes(trim($v['seo_meta_keywords'])),
							'seo_meta_description' => addslashes(trim($v['seo_meta_description'])),
							'p_id' => $decId,
							'language' => $k,
							'price' => str_replace(',', '', trim($v['price'])),
						);

						$datai  =  apply_filters("cms_admin_update_property_lang_additional_fields" , $datai);

						$CI->Common_model->commonInsert('property_lang_details', $datai);
					} else {
						$CI->Common_model->commonQuery("update property_lang_details set 
								  title = '" . addslashes(trim($v['title'])) . "' 
								, short_description = '" . addslashes(trim($v['short_description'])) . "'
								, description = '" . addslashes(trim($v['description'])) . "'
								, price = '" . str_replace(',', '', trim($v['price'])) . "' 
								
								
								,seo_meta_keywords = '" . addslashes(trim($v['seo_meta_keywords'])) . "' ,
								 seo_meta_description = '" . addslashes(trim($v['seo_meta_description'])) . "' 
								where p_id = $decId and language = '$k'");
					}
				}
			}
		} else {
			$default_language_code = $CI->default_language;

			$property_lang_details = $CI->Common_model->commonQuery("select * from property_lang_details
						where p_id = $decId and language = '$default_language_code' ");
			if ($property_lang_details->num_rows() == 0) {
				$datai = array(
					'title' => addslashes(trim($title)),
					'short_description' => addslashes(trim($short_description)),
					'description' => addslashes(trim($description)),
					'seo_meta_keywords' => addslashes(trim($seo_meta_keywords)),
					'seo_meta_description' => addslashes(trim($seo_meta_description)),
					'p_id' => $decId,
					'language' => $default_language_code,
					'price' => str_replace(',', '', trim($price)),
				);

				$datai  =  apply_filters("cms_admin_update_property_lang_additional_fields" , $datai);
				$CI->Common_model->commonInsert('property_lang_details', $datai);
			} else {
				$CI->Common_model->commonQuery("update property_lang_details set 
						title = '" . addslashes(trim($title)) . "' 
						, short_description = '" . addslashes(trim($short_description)) . "' 
						, description = '" . addslashes(trim($description)) . "' 
						, price = '" . str_replace(',', '', trim($price)) . "' 
						
						
						,seo_meta_keywords = '" . addslashes(trim($seo_meta_keywords)) . "' 
						,seo_meta_description = '" . addslashes(trim($seo_meta_description)) . "'
						where p_id = $decId and language = '$default_language_code'");
			}
		}

		if (!isset($property_meta) ) 
			$property_meta =  array();
		$property_meta  =  apply_filters("cms_admin_update_property_meta_additional_fields" , $property_meta);
		
		
		if (isset($property_meta)) {
			foreach ($property_meta as $meta_key => $meta_value) {

				if (is_array($meta_value)) {
					$key = key($meta_value);

					if ($key === 0) {

						$datai = array(
							'meta_key' => $meta_key,
							'meta_value' => $meta_value[$key],
							'property_id' => $decId,
						);
						$CI->Common_model->commonInsert('property_meta', $datai);
					} else {

						$CI->Common_model->commonQuery("update property_meta set 
								meta_value = '" . addslashes($meta_value[$key]) . "' 
								where property_id = $decId and meta_key = '$meta_key'");
					}
				}
				else
				{
					$this->update_property_meta($decId,$meta_key,$meta_value);
				}
			}
		}
	}

	public function toggle_featured_property_callback_func()
	{
		extract($_POST);
		$CI = &get_instance();
		$CI->load->library('Global_lib');
		$CI->load->model('Common_model');
		$user_type = $CI->session->userdata('user_type');
		$user_id = $CI->session->userdata('user_id');
		$can_feature_property = false;

		if ($user_type == 'admin')
			$can_feature_property = true;

		$update_credit = false;
		$get_featured_property_option = $CI->global_lib->get_option('enable_featured_property_posting');

		if ($get_featured_property_option == 'Y' && $user_type != 'admin') {
			$credit =  $CI->global_lib->get_user_meta($user_id, 'featured_property_credit');
			if ($is_feat == 'Y' && $credit > 0) {
				$can_feature_property = true;
				/** decrement*/
				if (isset($p_id) && !empty($p_id)) {
					$encId = $CI->global_lib->DecryptClientId($p_id);

					$this->credit_id = $CI->package_lib->get_credit_id_by_user_id($user_id, 'property', 'featured_property');
					$credit_used = $CI->package_lib->check_credit_used('featured_property', $encId, 'property');
					if (!$credit_used && $this->credit_id) {
						$CI->package_lib->add_credit_uses('featured_property', $encId, 'property', $user_id);
						$CI->package_lib->update_credits_by_user_id($user_id, 'featured_property_credit', 'minus_credit', 1);
						$CI->package_lib->update_credits_updated_credit_for_user($this->credit_id);
						$update_credit = true;
					}
				}
			} else if ($credit == 0) {
				echo '<div class="alert alert-warning alert-dismissable" style="margin-top:10px; margin-bottom:0px;">
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
					' . mlx_get_lang("You don't have sufficient credits for post featured porperty") . ' 
				</div>';
			} else if ($is_feat == 'N')
				$can_feature_property = true;
		} else {
			$can_feature_property = true;
		}

		if ($can_feature_property) {
			if (isset($p_id) && !empty($p_id)) {
				$datai = array(
					'is_feat' => $is_feat
				);
				$encId = $CI->global_lib->DecryptClientId($p_id);
				$CI->Common_model->commonUpdate('properties', $datai, 'p_id', $encId);
			}
			echo 'success';
		}
	}
}

/* End of file Myhelpers.php */