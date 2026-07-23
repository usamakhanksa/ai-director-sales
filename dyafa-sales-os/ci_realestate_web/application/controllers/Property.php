<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Property extends MY_Controller
{


	function __construct()
	{
		parent::__construct();
	}

	function _remap($method = null, $args = null)
	{

		$multi_lang = $this->enable_multi_lang;
		$default_lang = $this->default_language;
		$default_lang_code = $this->default_lang_code;
		$default_lang_code = $this->default_lang_code_small;
		/*print_r($method); print_r($args);exit;*/
		if ($multi_lang) {
			$curl = current_url();
			$parts = explode("/", $curl);
			$find_lang = false;
			foreach ($parts as $part) {
				/*if($part == $default_lang)
					echo "yes";*/
				
				if ($part == $default_lang_code) {
					
					$find_lang = true;
				}
			}
			
			$is_lang_exists = false;

			if (!$find_lang) {

				$CI = &get_instance();
				$site_language = get_option('site_language');
				$site_language_array = json_decode($site_language, true);
				
				$url_lang_code = isset($args[1]) ?  $args[0]  : $this->default_lang_code_small;

				foreach ($site_language_array as $slak => $slav) {
					/*echo "<pre>";print_r($slav);echo "</pre>";	*/
					$lang_exp = explode('~', $slav['language']);
					$lang_code = $lang_exp[1];
					$lang_title = $lang_exp[0];

					$lang_code_combi = $lang_exp[1];
					$lang_code_exp = explode('-', $lang_code_combi);

					/*$flag_code = $lang_code = $lang_code_title = $lang_exp[1];*/
					/*if (isset($lang_code_exp[1])) {
						$lang_code = strtolower($lang_code_exp[1]);
					} else
						$lang_code = $lang_code_exp[0];*/
				
					$lang_code_small = strtolower($lang_code);	
					

					if ($url_lang_code == $lang_code_small && $slav['status'] == 'enable') {
						$is_lang_exists = true;
						$this->site_direction = $slav['direction'];
						$this->site_currency = $slav['currency'];
						$this->set_default_timezone($slav['timezone']);
						$lang_slug = $CI->global_lib->get_slug($lang_title);
						$this->default_language = $lang_exp[1];
						/*$this->default_lang_code = $lang_code;*/
						$default_lang_code = $this->default_lang_code = $lang_code_small;
						$this->default_language_title = $lang_title;
						$this->lang->load($lang_slug, $lang_slug);
						$_SESSION['default_lang_front'] = $slav['language'];
						break;
					}
				}
				
				
			}
			
			if (!$find_lang  ){
				/*&& !$is_lang_exists ) {*/

					if ($is_lang_exists) {	
						/*echo  $method; exit;*/

						if ($method == null  || $method == 'index') {
							$url = "/property/" . $this->default_lang_code . "/";
						} else if ($method == 'single') {
							echo $url = "/property/" . $this->default_lang_code;
							if (isset($args[0])) $url .= "/" . $args[0];
							if (isset($args[1])) $url .= "/" . $args[1];
							if (isset($args[2])) $url .= "/" . $args[2];
							if (isset($args[3])) $url .= "/" . $args[3];
						}
						if (isset($_GET['view']))
							$url .= '?view=' . $_GET['view'];

						

						redirect($url, 'location');
					
					}
	
			}
			
		}

		
		
		if ($method == null  || $method == 'index') {
			$this->index();
		} 
		else if($method == 'property_404'){
			if (empty($args)) {
				$this->property404();
			} else {

				$property_id = $lang = "";
				
				if (!$multi_lang) {
					$lang = $default_lang;
					$this->property404($lang);
				} else {
					if (isset($args[0]))
						$lang = $args[0];
					else
						$lang = $default_lang_code;
					$this->property404($lang);
				}
			}
		}
		else if (is_array($args) && count($args) > 0) {

			if (empty($args)) {
				$this->index();
			} else {

				$property_id = $lang = "";
				
				if (!$multi_lang) {
					$lang = $default_lang;
					if (isset($args[0])) $property_id = $args[0];
					$this->single($property_id, $lang);
				} else {
					if (isset($args[0]))
						$lang = $args[0];
					else
						$lang = $default_lang_code;
					if (isset($args[1])) {
						$property_id = $args[1];
						$this->single($property_id, $lang);
					} else
						$this->index($lang);
				}
			}
		}
	}

	public function index($lang = 'en')
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['theme'] = $theme;
		$data['currency_symbol'] = $this->global_lib->get_currency_symbol();
		$data['page_header_title'] = 'All Properties';
		$data['has_banner'] = false;

		$sql = "select b.b_image from banners as b
			inner join banner_assigned_to as bs on bs.banner_id = b.b_id and bs.assign_type = 'static' and bs.assign_id = 'all_properties'
			where b.b_status = 'Y' order by b.b_id ASC limit 1";
		$banner_result = $this->Common_model->commonQuery($sql);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}

		$query = "select * from properties as prop 
			    inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
				where prop.status = 'publish' and prop.deleted = 'N' order by prop.p_id DESC ";

		$total_search_properties = $this->Common_model->commonQuery($query);

		$no_of_property_in_search_page = 12;

		$args = array(
			'total_rows' => $total_search_properties->num_rows(),
			'per_page' => $no_of_property_in_search_page
		);
		$this->load->library('Pagination_lib');
		$pagination_args = $this->pagination_lib->get_pagination_links($args);

		$data['pagination_links'] = $pagination_args['pagination_links'];


		$query .= " limit " . $pagination_args['start'] . "," . $pagination_args['limit'] . "";
		$data['property_list'] = $this->Common_model->commonQuery($query);

		$data['meta_keywords'] = $this->global_lib->get_seo_settings("property", 'meta_keywords');
		$data['meta_description'] = $this->global_lib->get_seo_settings("property", 'meta_description');

		$data['content'] = "$theme/property/all";

		$data['page_title'] = "All Properties";

		$this->load->view("$theme/header", $data);
	}

	public function single($property_id = NULL, $lang = 'en') //, $slug = NULL)
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->helper('property_helper');

		$this->load->library('Global_lib');

		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['theme'] = $theme;

		/*echo $property_id ." - ". $lang ;  exit;*/

		if ($property_id   == NULL) {
			redirect('/', 'location');
		}
		//echo $property_id; exit;
		$data['currency_symbol'] = $this->global_lib->get_currency_symbol();
		$property_values = explode("~", $property_id);
		$slug = $property_values[0];
		$prop_id = $property_values[1];

		

		$qry = "select * from property_meta where property_id=$prop_id";
		$property_meta_result = $this->Common_model->commonQuery($qry);

		$property_meta = array();

		foreach ($property_meta_result->result() as $row) {
			$property_meta[$row->meta_key] = $row->meta_value;
		}

		$data['meta_result'] = $property_meta;

		$sql = "select prop.*,
					   pld.title as title , 
					   pld.description as description, 
					   pld.language as language,
					   pld.price as price,
					   prop.address as address,
					   pt.title as prop_type_title, 
					   pt.slug as prop_type_slug, 
					   u.user_email, u.user_type 
					   from properties  as prop 
			   inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
			   inner join property_types as pt on pt.pt_id = prop.property_type
			   inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$this->default_language'
			   and pld.title != '' 
			   where prop.slug = '$slug' and prop.p_id = '$prop_id' and prop.status = 'publish' and prop.deleted = 'N' ";
		/*and pld.description != '' and pld.short_description != '' and pld.price != ''*/
		$property_result = $this->Common_model->commonQuery($sql);

		if ($property_result->num_rows() == 0) {
			/*
			$sql = "select prop.*,pt.title as prop_type_title, u.user_email, u.user_type from properties  as prop 
			   inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
			   inner join property_types as pt on pt.pt_id = prop.property_type
			   where prop.slug = '$slug' and prop.status = 'Y'";
			$property_result = $this->Common_model->commonQuery($sql );
			*/
		}
		
		$data['property_type_features'] = $CI->config->item('property_type_features');

		if ($property_result->num_rows() > 0) {
			$property_row = $property_result->row();
			$data['single_property'] = $single_property = $property_row;

			$data['page_title'] = ucfirst($property_row->title);

			/*$r_sql = "select *,pld.title as title,pld.language as language,
			pld.seo_meta_keywords as seo_meta_keywords, pld.seo_meta_description as seo_meta_description  
			from properties as prop
			inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
			inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$this->default_language'
			where prop.property_type = $single_property->property_type and
			prop.status = 'publish' and prop.p_id != $single_property->p_id and prop.deleted = 'N' limit 8";

			$related_property_result = $this->Common_model->commonQuery($r_sql);

			if ($related_property_result->num_rows() == 0) {
				$r_sql = "select * from properties as prop
				inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
				where prop.property_type = $single_property->property_type and 
				prop.status = 'publish' and prop.p_id != $single_property->p_id limit 8";

				$related_property_result = $this->Common_model->commonQuery($r_sql);
			}

			if ($related_property_result->num_rows() > 0) {
				$data['related_properties'] = $related_property_result;
			}*/
		} else {
			
			echo $sql = "select prop.*,
					   pld.title as title , 
					   pld.description as description, 
					   pld.language as language,
					   pld.price as price,
					   prop.address as address,
					   pt.title as prop_type_title, 
					   pt.slug as prop_type_slug, 
					   u.user_email, u.user_type 
					   from properties  as prop 
			   inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
			   inner join property_types as pt on pt.pt_id = prop.property_type
			   inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$this->default_language'
			   and pld.title != '' 
			   where prop.p_id = '$prop_id' and prop.status = 'publish' and prop.deleted = 'N' ";
				/*and pld.description != '' and pld.short_description != '' and pld.price != ''*/
				$property_result = $this->Common_model->commonQuery($sql);

				if ($property_result->num_rows() > 0) {
					$property_row = $property_result->row();
					$data['single_property'] = $single_property = $property_row;

					$data['page_title'] = ucfirst($property_row->title);
					if (isset($this->enable_multi_lang) && $this->enable_multi_lang == true) 
						redirect("property/$lang/".$property_row->slug.'~'.$property_row->p_id, 'location');
					else
						redirect("property/".$property_row->slug.'~'.$property_row->p_id, 'location');
				}
				else{
					if (isset($this->enable_multi_lang) && $this->enable_multi_lang == true) 
						redirect("property_404/$lang", 'location');
					else
						redirect('property_404', 'location');
				}
		}

		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$page_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


		$og_meta = array();
		$og_meta['og:url'] = $page_url;
		$og_meta['og:type'] = 'website';
		$og_meta['og:title'] = ucfirst(stripslashes($single_property->title)).((get_option('website_title')) ? ' - '.get_option('website_title'):'');
		$og_meta['og:description'] = $single_property->short_description;
		$og_meta['og:site_name'] = mlx_get_lang('MagicEstate');
		if (!empty($single_property->property_images)) {

			//$p_images = $myHelpers->global_lib->get_property_gallery($single_property->p_id);
			$p_images = $this->global_lib->get_property_gallery($single_property->p_id);
			//print_r($p_images);exit;
			if (!empty($p_images)) {
				$p_image = current($p_images);
				//$post_image_url = base_url().$p_images[0]['large'];
				$post_image_url = base_url() . $p_image['original'];
				$og_meta['og:image'] = $post_image_url;
			} 
			/*
			else {
				$post_image_url = base_url() . 'themes/' . $theme . '/images/single-no-property-image.jpg';
				$og_meta['og:image'] = $post_image_url;
			}
			*/
		} 
		/*
		else {
			$post_image_url = base_url() . 'themes/' . $theme . '/images/single-no-property-image.jpg';
			$og_meta['og:image'] = $post_image_url;
		}
		*/

		if (isset($_POST['export_pdf'])) {

			extract($_POST);


			if (!empty($prop)) {

				$qry = $this->Common_model->commonQuery(
					"select prop.*,pt.title as prop_type_title from properties as prop
				inner join property_types as pt on pt.pt_id = prop.property_type
				 where prop.status = 'publish' and prop.deleted = 'N' and prop.p_id=" . $prop . ""
				);
			}
			$r = $qry->row();

			$html = '
				<link href="' . base_url() . '/themes/default/css/bootstrap.min.css" rel="stylesheet" media="all">
				
				  </style>
				<div class="row">
				<div class="col-md-12 text-center">';
			$p_images = $this->global_lib->get_property_gallery($r->p_id);
			if (!empty($p_images)) {
				$img_src = '';
				foreach ($p_images as $k => $v) {
					$img_src = $v['large'];
					break;
				}
				$currency_symbol = $this->global_lib->get_currency_symbol();

				$is_price_set = true;

				if (isset($this->enable_multi_lang) && $this->enable_multi_lang == true) {
					$def_lang_code = $this->default_lang_code;

					$ret_data = $this->global_lib->get_property_price_by_lang($r->p_id, $def_lang_code);
					if (!empty($ret_data)) {
						$r->price = $ret_data['price'];
						$currency_symbol = $ret_data['currency'];
					} else {
						$is_price_set = false;
					}
				}



				$args = array("currency_symbol" => $currency_symbol);
				$price = $this->global_lib->moneyFormatDollar($r->price, $args);

				$html .= '<img src="' . base_url() . $img_src . '" class="img-fluid"/>';
			}
			$html .= '</div></div>
				
				<div class="row mt-3">
					<div class="col-md-12 mt-3 text-center">
						<h4 class="text-black">' . stripslashes($r->title) . '</h4>
						<p><strong>Address</strong> :  ' . $r->address . '</p>
					</div>
				</div>
				
				<div class="row mt-3">
					<div Class="col-md-12">
						<table class="table table-bordered text-center" >
						  <thead class="thead-light">
							<tr>';
			if (!empty($price))
				$html .= '<th scope="col">Price</th>';
			if (!empty($r->property_for))
				$html .= '<th scope="col">Property For</th>';
			if (!empty($r->prop_type_title))
				$html .= '<th scope="col">Property Type</th>';
			if (!empty($r->size))
				$html .= '<th scope="col">Size</th>';
			if (!empty($r->bedroom) && $r->bedroom > 0)
				$html .= '<th scope="col">Beds</th>';
			if (!empty($r->bathroom) && $r->bathroom > 0)
				$html .= '<th scope="col">Baths</th>';
			$html .= '</tr>
						  </thead>
						  <tbody>
							<tr>';
			if (!empty($price))
				$html .= '<td>' . $price . '</td>';
			if (!empty($r->property_for))
				$html .= '<td>' . ucfirst($r->property_for) . '</td>';
			if (!empty($r->prop_type_title))
				$html .= '<td>' . ucfirst($r->prop_type_title) . '</td>';
			if (!empty($r->size))
				$html .= '<td>' . str_replace('~', ' ', $r->size) . '</td>';
			if (!empty($r->bedroom) && $r->bedroom > 0)
				$html .= '<td>' . $r->bedroom . '</td>';
			if (!empty($r->bathroom) && $r->bathroom > 0)
				$html .= '<td>' . $r->bathroom . '</td>';
			$html .= '</tr>
						  </tbody>
						</table>
					</div>
              </div>';

			if (!empty($r->description)) {
				$html .= '<div class="row mt-3">
					<div class="col-md-12 ">
						<h4 class="text-black">Description</h4>
						<p class="lead pl-4">';
				if (!empty($r->description)) {
					$html .= $r->description;
				}
				$html .= '</p>
					</div>
				</div>';
			}

			$Indoor_amenities = json_decode($r->indoor_amenities, true);

			if (!empty($Indoor_amenities)) {

				$html .= '
				<div class="row mt-3">
				<div class="col-md-12 ">
					<h4 class="text-black">Indoor Amenities</h4>';
				$html .= '<ul class="list-styled pl-4">';
				foreach ($Indoor_amenities as $amenity) {
					$html .= '<li>' . $amenity . '</li>';
				}
				$html .= '</ul></div></div>';
			}

			$outdoor_amenities = json_decode($r->outdoor_amenities, true);
			if (!empty($outdoor_amenities)) {
				$html .= '<div class="row mt-3">
					<div class="col-md-12 ">
					<h4 class="text-black">Outdoor Amenities</h4>';


				$html .= '<ul class="list-styled pl-4">';
				foreach ($outdoor_amenities as $amenity) {

					$html .= '<li> ' . $amenity . '</li>';
				}
				$html .= '</ul></div></div>';
			}

			$distance  = json_decode($r->distance_list, true);
			if (!empty($distance)) {
				$html .= '
				<div class="row mt-3">
					<div class="col-md-12 ">
					<h4 class="text-black">Distances</h4>';

				$html .= '<ul class="list-styled pl-4">';

				foreach ($distance as $key => $val) {
					$html .= '<li><i class="fa fa-arrows"></i> <span>' . $key . '</span><span> ' . $val['direction'] . ' 	</span> : <strong>
					<span> ' . $val['distance'] . ' 	</span>
					<span> ' . $val['distance_text'] . '</span></strong>
					</li>';
				}

				$html .= '</ul></div></div>';
			}

			$website_title = $this->global_lib->get_option("website_title");

			$prop_title = stripslashes($r->title);
			if (!empty($website_title))
				$prop_title .= " - " . $website_title;
			$args_pdf = array("prop_title" => $prop_title);


			if (version_compare(PHP_VERSION, '7.0.0') >= 0) {

				$CI->load->library('Dompdf_lib');
				$CI->dompdf_lib->write($html, $args_pdf);
			} else {

				$CI->load->library('Mpdf_lib');
				$CI->mpdf_lib->genHtml($html, $args_pdf);
			}
			exit;
		}

		$data['og_meta'] = $og_meta;

		$data['meta_keywords'] = $single_property->seo_meta_keywords;
		$data['meta_description'] = $single_property->seo_meta_description;
		$data['page_title'] = $single_property->title;

		$data['has_banner'] = false;

		$sql = "select b.b_image from banners as b
			inner join banner_assigned_to as bs on bs.banner_id = b.b_id and bs.assign_type = 'property' and bs.assign_id = '$single_property->p_id'
			where b.b_status = 'Y' order by b.b_id ASC limit 1";
		$banner_result = $this->Common_model->commonQuery($sql);

		if ($banner_result->num_rows() > 0) {
			$data['banner_row'] = $banner_result->row();
			$data['has_banner'] = true;
		}


		$data['content'] = "$theme/property/single";


		$this->load->view("$theme/header", $data);
	}
	
	public function property404($lang = 'en')
	{
		
		
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->helper('property_helper');

		$this->load->library('Global_lib');

		$data = $data = $this->global_lib->uri_check();
		$data['myHelpers'] = $this;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['theme'] = $theme;

		
		$data['content'] = "$theme/property/property_404";
		
		$this->load->view("$theme/header", $data);
	}
}
