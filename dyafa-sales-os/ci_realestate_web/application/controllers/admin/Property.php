<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Property extends MY_Controller
{

	var $post_property_credit , $user_id;

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

		$CI = &get_instance();
		$this->load->library('Language_lib');
		$this->load->library('Package_lib');
		$this->load->library('Global_lib');
		$this->load->model('Common_model');
		$this->load->helper('text');
		$this->user_id = apply_filters("get_user_account_id");
		
		$user_id = $this->session->userdata('user_id');
		$this->post_property_credit = $this->package_lib->get_credits_by_user_id($user_id, 'post_property_credit');
	}

	public function index()
	{
		$this->manage();
	}

	public function manage($slug = '')
	{

		$CI = &get_instance();

		$data = $CI->data;

		$data['currency_symbol'] = $CI->global_lib->get_currency_symbol();
		$user_type = $this->session->userdata('user_type');

		$data['page_heading'] = mlx_get_lang('Manage Active Properties');

		if ($user_type == 'admin') {
			if ($slug == 'all') {
				$data['page_heading'] = mlx_get_lang('Manage All Properties');
				$data['query'] = $this->Common_model->commonQuery("
						SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.deleted = 'N'
						order by prop.p_id DESC");
			} else if ($slug == 'active' || $slug == '') {
				$data['page_heading'] = mlx_get_lang('Manage Active Properties');
				/** prop.is_feat = 'N' and 
					and prop.is_feat != 'Y' 
				*/
				$data['query'] = $this->Common_model->commonQuery("
						SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.status = 'publish' and prop.deleted = 'N' 
						order by prop.p_id DESC");
						
						
			} else if ($slug == 'inactive') {
				$data['page_heading'] = mlx_get_lang('Manage In-Active Properties');
				/**prop.is_feat = 'N' and */
				$data['query'] = $this->Common_model->commonQuery("
						SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where  prop.status = 'draft'  and prop.deleted = 'N'
						order by prop.p_id DESC");
			} else if ($slug == 'pending') {
				$data['page_heading'] = mlx_get_lang('Manage Pending Properties');
				$data['query'] = $this->Common_model->commonQuery("
						SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.status = 'pending' and prop.deleted = 'N'
						order by prop.p_id DESC");
			} else if ($slug == 'featured') {
				$data['query'] = $this->Common_model->commonQuery("
						SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.is_feat = 'Y' and prop.deleted = 'N'
						order by prop.p_id DESC");
			} else if ($slug == 'rejected') {
				$data['page_heading'] = mlx_get_lang('Manage Rejected Properties');
				$data['query'] = $this->Common_model->commonQuery("
						SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.status = 'reject' and prop.deleted = 'N'
						order by prop.p_id DESC");
			}

			$data['all_properties'] = $this->Common_model->commonQuery("
					SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
					left join property_types as pt on pt.pt_id = prop.property_type
					where prop.deleted = 'N'
					order by prop.p_id DESC");
			/**prop.is_feat = 'N' and 
				and prop.is_feat != 'Y' */
			$data['active_properties'] = $this->Common_model->commonQuery("
					SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
					left join property_types as pt on pt.pt_id = prop.property_type
					where  prop.status = 'publish' and prop.deleted = 'N' 
					order by prop.p_id DESC");
			/** prop.is_feat = 'N' and */
			$data['inactive_properties'] = $this->Common_model->commonQuery("
					SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
					left join property_types as pt on pt.pt_id = prop.property_type
					where prop.status = 'draft'  and prop.deleted = 'N'
					order by prop.p_id DESC");

			$data['pending_properties'] = $this->Common_model->commonQuery("
					SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
					left join property_types as pt on pt.pt_id = prop.property_type
					where prop.status = 'pending' and prop.deleted = 'N'
					order by prop.p_id DESC");

			$data['featured_properties'] = $this->Common_model->commonQuery("
					SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
					left join property_types as pt on pt.pt_id = prop.property_type
					where prop.is_feat = 'Y' and prop.deleted = 'N'
					order by prop.p_id DESC");

			$data['rejected_properties'] = $this->Common_model->commonQuery("
					SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
					left join property_types as pt on pt.pt_id = prop.property_type
					where prop.status = 'reject' and prop.deleted = 'N'
					order by prop.p_id DESC");
		} else {
			$user_id = 	$this->user_id;	/*	$this->session->userdata('user_id');*/
			/**
				and prop.is_feat != 'Y'
			*/
			if ($slug == 'all') {
				$data['page_heading'] = mlx_get_lang('Manage All Properties');
				$data['query'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
							left join property_types as pt on pt.pt_id = prop.property_type
							where prop.created_by = $user_id  and prop.deleted = 'N'
							order by p_id DESC");
			} else if ($slug == 'active' || $slug == '') {
				$data['page_heading'] = mlx_get_lang('Manage Active Properties');
				$data['query'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
							left join property_types as pt on pt.pt_id = prop.property_type
							where prop.created_by = $user_id 
							 and prop.status = 'publish' and prop.deleted = 'N'
							order by p_id DESC");
			} else if ($slug == 'inactive') {
				$data['page_heading'] = mlx_get_lang('Manage In-Active Properties');
				$data['query'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
							left join property_types as pt on pt.pt_id = prop.property_type
							where prop.created_by = $user_id 
							and prop.is_feat = 'N' and prop.status = 'draft' and prop.deleted = 'N'
							order by p_id DESC");
			} else if ($slug == 'pending') {
				$data['page_heading'] = mlx_get_lang('Manage Pending Properties');
				$data['query'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
							left join property_types as pt on pt.pt_id = prop.property_type
							where prop.created_by = $user_id 
							and prop.status = 'pending' and prop.deleted = 'N'
							order by p_id DESC");
			} else if ($slug == 'featured') {
				$data['page_heading'] = mlx_get_lang('Manage Featured Properties');
				$data['query'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
							left join property_types as pt on pt.pt_id = prop.property_type
							where prop.created_by = $user_id 
							and prop.is_feat = 'Y'  and prop.deleted = 'N'
							order by p_id DESC");
			} else if ($slug == 'rejected') {
				$data['page_heading'] = mlx_get_lang('Manage Rejected Properties');
				$data['query'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
							left join property_types as pt on pt.pt_id = prop.property_type
							where prop.created_by = $user_id 
							and prop.status = 'reject' and prop.deleted = 'N'
							order by p_id DESC");
			}

			$data['all_properties'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.created_by = $user_id  and prop.deleted = 'N'
						order by p_id DESC");
			/** and prop.is_feat = 'N' 
			and prop.is_feat != 'Y' 
			*/
			$data['active_properties'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.created_by = $user_id  and prop.deleted = 'N' 
						and prop.status = 'publish'
						order by p_id DESC");
			$data['inactive_properties'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.created_by = $user_id  and prop.deleted = 'N'
						and prop.is_feat = 'N' and prop.status = 'draft' 
						order by p_id DESC");
			$data['pending_properties'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.created_by = $user_id  and prop.deleted = 'N'
						and prop.status = 'pending'
						order by p_id DESC");
			$data['featured_properties'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.created_by = $user_id  and prop.deleted = 'N'
						and prop.is_feat = 'Y' 
						order by p_id DESC");
			$data['rejected_properties'] = $this->Common_model->commonQuery("SELECT prop.*, pt.title as prop_type_title FROM `properties` as prop 
						left join property_types as pt on pt.pt_id = prop.property_type
						where prop.created_by = $user_id  and prop.deleted = 'N'
						and prop.status = 'reject'
						order by p_id DESC");
		}

		$data['cur_active_tab'] = $slug;
		if($slug != '')
			$data['return'] = "manage_".$slug;
		else	
			$data['return'] = "";

		$data['content'] = $CI->theme . "/property/manage";


		$this->load->view($CI->theme . "/header", $data);
	}

	public function add_new()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');
		$this->load->library('Package_lib');


		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['size_units'] = $CI->config->item('size_units');

		$data['property_type_features'] = $CI->config->item('property_type_features');

		$this->load->library('Email_lib');
		$user_type = $this->session->userdata('user_type');

		$em_args = array();
		$em_args['property_id'] = 10;
		$em_args['email_template'] = "property_submission_email_admin";
		/*$this->global_lib->send_email_notifications_to_admin("property_submission_email_admin", $em_args);*/



		if (isset($_POST['submit']) || isset($_POST['draft']) || isset($_POST['pending'])) {

			clean_post();

			extract($_POST, EXTR_OVERWRITE);
			if (!is_numeric($user_id))
				$user_id = DecryptClientID($user_id);

			$_POST['status'] = $status = 'draft';
			$sess_msg = '';
			$user_email_template = 'property_submission_email';
			$em_args = array();
			if (isset($_POST['submit'])) {
				$_POST['status'] = $status = 'publish';
				$sess_msg = mlx_get_lang("Property Published Successfully");
				/*$user_email_template = '';*/
			} else if (isset($_POST['draft'])) {
				$_POST['status'] = $status = 'draft';
				$sess_msg = mlx_get_lang("Property Saved as Draft Successfully");
				$user_email_template = '';
			} else if (isset($_POST['pending'])) {
				$_POST['status'] = $status = 'pending';
				$sess_msg = mlx_get_lang("Property Submitted for Approval Successfully");
				$user_email_template = 'property_submitted_approval_email';
			}
			$this->load->library('Property_lib');
			$p_id = $this->property_lib->insert_property($_POST);
			/*$p_id = 10;*/
			$_POST['p_id'] = $p_id;
			
			do_action("add_property" , $p_id , $_POST);	
			
			do_action('admin_save_property_location_meta', $_POST);

			do_action('admin_save_property_document_meta', $_POST);
			
			do_action('admin_update_property_meta', $_POST);

			do_action('user_post_property_use_credit', $_POST);



			if ($user_email_template != '') {
				if ($user_type != 'admin') {
					$em_args = array();
					$em_args['property_id'] = $p_id;
					$em_args['email_template'] = "property_submission_email_admin";

					do_action('send_email_to_admin', $em_args);
				}


				if ($status == 'pending') {


					$em_args = array();
					$em_args['property_id'] = $p_id;
					$em_args['email_template'] = $user_email_template;
					$em_args['user_ids'] = array($user_id);
					do_action('send_email_to_user', $em_args);
				}
			}
			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px; margin-bottom:10px;">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									' . $sess_msg . '</div>';

			if(isset($return_to_edit) && $return_to_edit == 'Y')
			{
				redirect('/admin/property/edit/'.$p_id, 'location');
			}
			else if ($status == 'N')
				redirect('/admin/property/manage_inactive', 'location');
			else
				redirect('/admin/property/manage', 'location');
		}

		$data['property_types'] = $this->Common_model->commonQuery("SELECT * FROM `property_types` as prop 
									where prop.status = 'Y'	order by prop.title ASC");

		if (get_option('property_amenities')) {
			$data['amenities_list'] = json_decode(get_option('property_amenities'), true);
		}
		if (get_option('property_distances')) {
			$data['distances_list'] = json_decode(get_option('property_distances'), true);
		}

		$data['user_list'] = $this->Common_model->commonQuery("SELECT user_type,user_id FROM `users` as u 
									where u.user_status = 'Y'
									order by u.user_name ASC");

		$data['content'] = $CI->theme . "/property/add_new";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function edit($p_id = NULL)
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['size_units'] = $CI->config->item('size_units');
		
		$data['property_type_features'] = $CI->config->item('property_type_features');

		if (isset($_POST['submit']) || isset($_POST['draft']) || isset($_POST['pending'])) {
			
			clean_post();

			$user_type = $this->session->userdata('user_type');
			

			extract($_POST, EXTR_OVERWRITE);
			
			/*echo "<pre>";
			print_r($_POST);
			echo "</pre>";
			exit;*/
			$_POST['status'] = $status = 'draft';
			if (isset($_POST['submit'])) {
				$_POST['status'] = $status = 'publish';
			} else if (isset($_POST['draft'])) {
				$_POST['status'] = $status = 'draft';
			} else if (isset($_POST['pending'])) {
				$_POST['status'] = $status = 'pending';
			}

			$decId = DecryptClientID($p_id);
			$this->load->library('Property_lib');
			$this->property_lib->update_property($_POST);

			$_POST['p_id'] = $decId;
			do_action('admin_save_property_location_meta', $_POST);
			do_action('admin_save_property_document_meta', $_POST);
			
			do_action('admin_update_property_meta', $_POST);

			$user_type = $this->session->userdata('user_type');

			/*if ($this->site_payments == 'Y') {*/

			if (isset($current_status) && $current_status == 'pending' && $status == 'publish' && isset($prop_user_id)) {
				$_POST['p_id'] = $decId;
				$_POST['prop_user_id'] = $prop_user_id;
				$_POST['update_user_credit'] = true;



				do_action('user_post_property_use_credit', $_POST);
				/*$this->credit_id = $this->package_lib->get_credit_id_by_user_id($prop_user_id, 'property', 'post_property');

					
					$credit_used = $this->package_lib->check_credit_used('post_property', $decId, 'property');
					if (!$credit_used && $this->credit_id) {
						$this->package_lib->add_credit_uses('post_property', $decId, 'property', $prop_user_id);
						$this->package_lib->update_credits_by_user_id($prop_user_id, 'post_property_credit', 'minus_credit', 1);
						$this->package_lib->update_credits_updated_credit_for_user($this->credit_id);
					}*/
			} else 
					if ($user_type != 'admin'  && $status == 'publish') {

				$_POST['p_id'] = $decId;
				do_action('user_post_property_use_credit', $_POST);
				/*$user_id = $this->session->userdata('user_id');
						$this->credit_id = $this->package_lib->get_credit_id_by_user_id($user_id, 'property', 'post_property');
						$credit_used = $this->package_lib->check_credit_used('post_property', $decId, 'property');
						if (!$credit_used && $this->credit_id) {

							$this->package_lib->add_credit_uses('post_property', $decId, 'property', $user_id);
							$this->package_lib->update_credits_by_user_id($client_id, 'post_property_credit', 'minus_credit', 1);
							$this->package_lib->update_credits_updated_credit_for_user($this->credit_id);
						}*/
			}
			/*}*/

			$_SESSION['msg'] = '
							<div class="alert alert-success alert-dismissable" style="margin-top:10px; margin-bottom:10px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . mlx_get_lang("Property Updated Successfully") . '
							</div>
							';
							
			if(isset($return_to_edit) && $return_to_edit == 'Y')
			{
				redirect('/admin/property/edit/'.$p_id, 'location');
			}
			else if ($status == 'N')
				redirect('/admin/property/manage_inactive', 'location');
			else
				redirect('/admin/property/manage', 'location');
			
		}

		if (get_option('property_amenities')) {
			$data['amenities_list'] = json_decode(get_option('property_amenities'), true);
		}
		if (get_option('property_distances')) {
			$data['distances_list'] = json_decode(get_option('property_distances'), true);
		}

		$decId = DecryptClientID($p_id);
		$data['query'] = $prop_result = $this->Common_model->commonQuery("
				select *
				from properties 
				where p_id = $decId ");
		
		if($prop_result->num_rows() == 0){
			$_SESSION['msg'] = '<div class="alert alert-danger alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Invalid Property") . '
			</div>';
			redirect('/admin/property/manage', 'location');
		}	

		$data['property_types'] = $this->Common_model->commonQuery("SELECT * FROM `property_types` as prop 
									where prop.status = 'Y'
									order by prop.title ASC");


		$property_meta_res = $this->Common_model->commonQuery("select * from property_meta where property_id = $decId ");
		$property_meta = array();
		foreach ($property_meta_res->result() as $row) {


			$property_meta[$row->meta_key] = array('meta_id' =>  $row->meta_id, 'meta_value' =>  $row->meta_value);
		}

		$data['user_list'] = $this->Common_model->commonQuery("SELECT user_type,user_id FROM `users` as u 
									where u.user_status = 'Y'
									order by u.user_name ASC");


		if (get_option('property_custom_fields')) {
			$data['custom_field_list'] = json_decode(get_option('property_custom_fields'), true);
		}

		$isPlugAct = $this->isPluginActive('document');
		if ($isPlugAct == true) {
			$data['document_types'] = $this->Common_model->commonQuery("SELECT * FROM `property_doc_types` as prop order by prop.pdt_order ASC, prop.pdt_id DESC");
		}

		$data['property_meta'] = $property_meta;
		/*$data['theme']=$theme;*/
		$data['content'] = $CI->theme . "/property/edit";

		$this->load->view($CI->theme . "/header", $data);
	}
	
	public function clone($p_id = NULL)
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['size_units'] = $CI->config->item('size_units');
		
		$data['property_type_features'] = $CI->config->item('property_type_features');
		
		if (get_option('property_amenities')) {
			$data['amenities_list'] = json_decode(get_option('property_amenities'), true);
		}
		if (get_option('property_distances')) {
			$data['distances_list'] = json_decode(get_option('property_distances'), true);
		}

		$decId = DecryptClientID($p_id);
		$data['query'] = $prop_result = $this->Common_model->commonQuery("
				select *
				from properties 
				where p_id = $decId ");
		
		if($prop_result->num_rows() == 0){
			$_SESSION['msg'] = '<div class="alert alert-danger alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Invalid Property") . '
			</div>';
			redirect('/admin/property/manage', 'location');
		}	

		$data['property_types'] = $this->Common_model->commonQuery("SELECT * FROM `property_types` as prop 
									where prop.status = 'Y'
									order by prop.title ASC");


		$property_meta_res = $this->Common_model->commonQuery("select * from property_meta where property_id = $decId ");
		$property_meta = array();
		foreach ($property_meta_res->result() as $row) {


			$property_meta[$row->meta_key] = array('meta_id' =>  $row->meta_id, 'meta_value' =>  $row->meta_value);
		}

		$data['user_list'] = $this->Common_model->commonQuery("SELECT user_type,user_id FROM `users` as u 
									where u.user_status = 'Y'
									order by u.user_name ASC");


		if (get_option('property_custom_fields')) {
			$data['custom_field_list'] = json_decode(get_option('property_custom_fields'), true);
		}

		$isPlugAct = $this->isPluginActive('document');
		if ($isPlugAct == true) {
			$data['document_types'] = $this->Common_model->commonQuery("SELECT * FROM `property_doc_types` as prop order by prop.pdt_order ASC, prop.pdt_id DESC");
		}
		
		

		$data['property_meta'] = $property_meta;
		
		$data['is_clone'] = true;
		
		/*$data['theme']=$theme;*/
		$data['content'] = $CI->theme . "/property/edit";

		$this->load->view($CI->theme . "/header", $data);
	}


	public function view($p_id = NULL)
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		/*$data = $this->global_lib->uri_check();
		
		$data['myHelpers']=$this;*/
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['currency_symbol'] = $this->global_lib->get_currency_symbol();

		if (isset($_POST['approve']) || isset($_POST['reject'])) {

			extract($_POST, EXTR_OVERWRITE);

			$decId = DecryptClientID($p_id);
			
			$status = 'pending';
			$notif_text = '';
			$sess_msg = '';
			$prop_action = '';
			$user_email_template = '';
			if (isset($_POST['approve'])) {
				$status = 'publish';
				$notif_text = 'Your request for property approval is accepted by Site Administrator.';
				$notif_icon = 'fa-check text-green';
				$sess_msg = mlx_get_lang("Property Approval Request Accepted Successfully");
				$prop_action = 'approve';
				$user_email_template = 'property_approve_email';
			} else if (isset($_POST['reject'])) {
				$status = 'reject';
				$notif_text = 'Your request for property approval is reject by Site Administrator.';
				$notif_icon = 'fa-ban text-red';
				$sess_msg = mlx_get_lang("Property Approval Request Rejected");
				$prop_action = 'reject';

				$user_email_template = 'property_reject_email';

				$datai = array(
					'meta_key' => 'reject_message',
					'meta_value' => $reject_message,
					'property_id' => $decId,
				);
				$this->Common_model->commonInsert('property_meta', $datai);
			}

			$datai = array('status' => trim($status));
			$this->Common_model->commonUpdate('properties', $datai, 'p_id', $decId);

			$prop_result = $this->Common_model->commonQuery("
				select prop.created_by from properties as prop where prop.p_id = $decId ");

			if ($prop_result->num_rows() > 0) {
				$prop_row = $prop_result->row();
				$client_id = $prop_row->created_by;
				$user_id = $this->session->userdata('user_id');


				if (!empty($user_email_template)) {
					$em_args = array();
					$em_args['property_id'] = $decId;
					//send_email_notifications_to_user(array($client_id), $user_email_template,  $em_args);
					
					$em_args = array();
					$em_args['property_id'] = $p_id;
					$em_args['email_template'] = $user_email_template;
					$em_args['user_ids'] = array($user_id);
					//do_action('send_email_to_user', $em_args);
					
					
				}


				
			}


			$_SESSION['msg'] = '
					<div class="alert alert-success alert-dismissable" style="margin-top:10px; margin-bottom:0px;">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						' . $sess_msg . '
					</div>';
			redirect('/admin/property/manage', 'location');
		}

		$decId = DecryptClientID($p_id);
		$prop_result = $this->Common_model->commonQuery("
				select prop.*,pt.title as prop_type_title, u.user_email, u.user_type from properties  as prop 
			    left join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
			    inner join property_types as pt on pt.pt_id = prop.property_type
				where prop.p_id = $decId ");


		if ($prop_result->num_rows() == 0) {
			$_SESSION['msg'] = '
						<div class="alert alert-danger alert-dismissable" >
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Invalid Property ID") . '
						</div>
						';
			redirect('/admin/property/manage', 'location');
		} else {
			$data['single_prop'] = $prop_result->row();
		}

		$isPlugAct = $this->isPluginActive('document');
		if ($isPlugAct == true) {
			$data['document_types'] = $this->Common_model->commonQuery("SELECT * FROM `property_doc_types` as prop order by prop.pdt_order ASC, prop.pdt_id DESC");
		}

		$data['property_meta'] = get_property_metadata($decId);

		/*$data['theme']=$theme;*/

		$data['content'] = $CI->theme . "/property/view";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function amenities()
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');

		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['user_type'] = $this->session->userdata('user_type');
		if (isset($_POST['submit']) || isset($_POST['draft'])) {
			clean_post();
			extract($_POST);

			if (empty($user_id) || $user_id == 0) {
				$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("Session Expired") . '</p>';
				$_SESSION['logged_in'] = false;
				$this->session->set_userdata('logged_in', false);
				redirect('/admin/logins', 'location');
			}



			if (isset($amenities) && !empty($amenities)) {

				if (isset($amenities['indoor_amenities']) && !empty($amenities['indoor_amenities'])) {
					foreach ($amenities['indoor_amenities'] as $k => $v) {

						$amenities['indoor_amenities'][$k] = $this->language_lib->get_normal_string($v);
					}
				}
				if (isset($amenities['outdoor_amenities']) && !empty($amenities['outdoor_amenities'])) {
					foreach ($amenities['outdoor_amenities'] as $k => $v) {
						$amenities['outdoor_amenities'][$k] = $this->language_lib->get_normal_string($v);
					}
				}
			}

			update_option('property_amenities', json_encode($amenities));

			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Property Amenities Updated Successfully") . '
						</div>
						';
			redirect('/admin/property/amenities', 'location');
		}

		if (get_option('property_amenities')) {
			$data['amenities_list'] = json_decode(get_option('property_amenities'), true);
		}

		/*$data['theme']=$theme;*/

		$data['content'] = $CI->theme . "/property/amenities";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function distances()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');
		
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['user_type'] = $this->session->userdata('user_type');
		if (isset($_POST['submit']) || isset($_POST['draft'])) {
			clean_post();
			extract($_POST);
			if (empty($user_id) || $user_id == 0) {
				$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("Session Expired") . '</p>';
				$_SESSION['logged_in'] = false;
				$this->session->set_userdata('logged_in', false);
				redirect('/admin/logins', 'location');
			}
			update_option('property_distances', json_encode($distances));

			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Property Distances Updated Successfully") . '
						</div>';
			redirect('/admin/property/distances', 'location');
		}

		if (get_option('property_distances')) {
			$data['distances_list'] = json_decode(get_option('property_distances'), true);
		}

		/*$data['theme']=$theme;*/
		$data['content'] = $CI->theme . "/property/distances";
		$this->load->view($CI->theme . "/header", $data);
	}


	public function settings()
	{
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['user_type'] = $this->session->userdata('user_type');
		if (isset($_POST['submit']) || isset($_POST['draft'])) {
			clean_post();
			extract($_POST);
			if (empty($user_id) || $user_id == 0) {
				$_SESSION['msg'] = '<p class="error_msg">' . mlx_get_lang("Session Expired") . '</p>';
				$_SESSION['logged_in'] = false;
				$this->session->set_userdata('logged_in', false);
				redirect('/admin/logins', 'location');
			}

			update_option('property_distances', json_encode($distances));

			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Property Distances Updated Successfully") . '
						</div>';
			redirect('/admin/property/distances', 'location');
		}
		/*$data['theme']=$theme;*/
		$data['content'] = $CI->theme . "/property/settings";
		$this->load->view($CI->theme . "/header", $data);
	}


	public function prop_type($pt_id = null)
	{

		$CI = &get_instance();
		$theme = $CI->config->item('theme');

		$this->load->library('Global_lib');


		/*$data = $this->global_lib->uri_check();
		
		$data['myHelpers']=$this;*/
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');

		$data['user_type'] = $this->session->userdata('user_type');
		if (isset($_POST['submit']) || isset($_POST['draft'])) {

			clean_post();
			extract($_POST);
			$user_id = $this->session->userdata('user_id');

			$meta_options = array();
			if (isset($adv_search_options))
				$meta_options['adv_search_options'] = $adv_search_options;

			if (isset($property_type_id) && !empty($property_type_id)) {
				$decId = DecryptClientID($property_type_id);



				


				$datai = array(
					'title' => trim($property_type_title),
					/*'slug' => $slug,*/
					'img_url' => $img_url,
					'meta_options' => json_encode($meta_options),
					'status' => $status,
				);
				
				if (isset($property_type_slug) && isset($old_slug) && !empty($property_type_slug) &&  $property_type_slug != $old_slug) {
					
					$config = array(
					'field' => 'slug', 'title' => 'title', 'table' => 'property_types',
					'id' => 'pt_id'
					);
					$this->load->library('Slug_lib', $config);

					$property_type_title_for_slug = $this->language_lib->get_normal_string($property_type_slug);

					$datap = array('title' => $property_type_title_for_slug,);
					$slug = $this->slug_lib->create_uri($datap);
					
					
					$datai['slug'] = $slug;
				}
				
				
				
				$this->Common_model->commonUpdate('property_types', $datai, 'pt_id', $decId);
			

				$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Property Type Updated Successfully") . '
						</div>
						';
						
			} else {


				$config = array(
					'field' => 'slug', 'title' => 'title', 'table' => 'property_types',
					'id' => 'pt_id'
				);
				$this->load->library('Slug_lib', $config);

				$property_type_title_for_slug = $this->language_lib->get_normal_string($property_type_title);

				$datap = array('title' => $property_type_title_for_slug,);
				$slug = $this->slug_lib->create_uri($datap);


				$meta_options = array();
				if (isset($adv_search_options))
					$meta_options['adv_search_options'] = $adv_search_options;

				$datai = array(
					'title' => trim($property_type_title),
					'slug' => $slug,
					'img_url' => $img_url,
					'meta_options' => json_encode($meta_options),
					'created_on' => time(),
					'status' => $status,
					'created_by' => $user_id,
				);
				$pt_id = $this->Common_model->commonInsert('property_types', $datai);
				/***
				ALTER TABLE `property_types` ADD `meta_options` TEXT NOT NULL AFTER `img_url`;
				 **/


				$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							' . mlx_get_lang("Property Type Added Successfully") . '
						</div>
						';
			}
			redirect('admin/property/prop_type', 'location');
			//redirect('property/prop_type', 'location');
		}

		$data['query'] = $this->Common_model->commonQuery("
						SELECT * FROM `property_types` as prop 
						order by prop.pt_id DESC");

		if ($pt_id != null) {
			$decId = DecryptClientID($pt_id);
			$pt_result = $this->Common_model->commonQuery("select * from property_types where pt_id = $decId ");
			if ($pt_result->num_rows() > 0) {
				$pt_row = $pt_result->row();
				$data['property_type_id'] = $pt_row->pt_id;
				$data['property_type_title'] = $pt_row->title;
				$data['property_type_slug'] = $pt_row->slug;
				$data['property_type_img'] = $pt_row->img_url;
				$data['status'] = $pt_row->status;
				$data['meta_options'] = $pt_row->meta_options;
			}
		}

		/*$data['theme']=$theme;*/

		$data['content'] = $CI->theme . "/property/prop_type";

		$this->load->view($CI->theme . "/header", $data);
	}

	public function layouts()
	{
		
		
		$CI = &get_instance();
		$theme = $CI->config->item('theme');
		$this->load->library('Global_lib');
		$data = $CI->data;
		$data['site_widgets'] = $CI->config->item('site_widgets');
		$this->load->config('appearance_config');
		$front_end_themes = $CI->config->item('front_end_themes');

		$user_id = $this->session->userdata('user_id');
		$user_type = $this->session->userdata('user_type');

		$this->load->model('Common_model');
		$this->load->helper('text');

		if (isset($_POST['submit']) || isset($_POST['draft'])) {

			extract($_POST);
			extract($_POST, EXTR_OVERWRITE);

			
			
			clean_post();


			if(isset($property_sidebar) && !empty($property_sidebar))
			{
				if($user_type == 'admin')
					update_option('property_sidebar_widgets', json_encode($property_sidebar));
				else
					update_user_meta($user_id,'property_sidebar_widgets', json_encode($property_sidebar));
			}

			if(isset($property_content) && !empty($property_content))
			{
				if($user_type == 'admin')
					update_option('property_content_widgets', json_encode($property_content));
				else
					update_user_meta($user_id,'property_content_widgets', json_encode($property_content));
			}

			$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-top:10px;margin-bottom:0px;">
			<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
			' . mlx_get_lang("Property Layouts Updated Successfully") . '</div>							';
			redirect('admin/property/layouts', 'location');
		}

		

		if($user_type == 'admin')
		{
			$property_sidebar_widgets = get_option('property_sidebar_widgets');
			$property_content_widgets = get_option('property_content_widgets');
		}
		else
		{
			$property_sidebar_widgets = get_user_meta($user_id, 'property_sidebar_widgets');
			$property_content_widgets = get_user_meta($user_id, 'property_content_widgets');
		}

		if(isset($property_sidebar_widgets) && !empty($property_sidebar_widgets))
		{
			$data['property_sidebar_widgets_meta'] = json_decode($property_sidebar_widgets,true);
		}

		if(isset($property_content_widgets) && !empty($property_content_widgets))
		{
			$data['property_content_widgets_meta'] = json_decode($property_content_widgets,true);
		}

		$data['content'] = $CI->theme ."/property/layouts";
		$this->load->view($CI->theme ."/header", $data);
	}

	public function delete($rowid,$return = '')
	{
		$CI = &get_instance();
		$this->load->library('Global_lib');
		if (!is_array($rowid))
			$rowid	= DecryptClientID($rowid);
		$this->load->model('Common_model');

		$tbl = 'properties';
		$pid = 'p_id';
		$url = '/admin/property/manage/';
		if(!empty($return))
		{
			$return = str_replace("manage_","",$return);
			$url .= $return; 
		}
		$fld = mlx_get_lang("Property");

		$enable_property_soft_delete = get_option('enable_property_soft_delete');

		if (isset($enable_property_soft_delete) && $enable_property_soft_delete == 'Y') {
			$datai = array('deleted' => 'Y');
			$this->Common_model->commonUpdate($tbl, $datai, $pid, $rowid);
		} else {
			$this->Common_model->commonDelete('notifications', $rowid, 'p_id');
			$this->Common_model->commonDelete('property_lang_details', $rowid, 'p_id');
			$this->Common_model->commonDelete('property_meta', $rowid, 'property_id');

			$rows = $this->Common_model->commonDelete($tbl, $rowid, $pid);
		}

		$rows = 1;

		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" style="margin-bottom:0px;margin-top:10px;">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . $rows . ' ' . $fld . ' ' . mlx_get_lang("Deleted Successfully") . '
							</div>
							';
		redirect($url, 'location', '301');
	}


	public function delete_type($rowid)
	{

		$CI = &get_instance();
		$this->load->library('Global_lib');
		if (!is_array($rowid))
			$rowid	= DecryptClientID($rowid);
		$this->load->model('Common_model');

		$tbl = 'property_types';
		$pid = 'pt_id';
		$url = '/admin/property/prop_type/';
		$fld = mlx_get_lang("Property");

		$pt_result = $this->Common_model->commonQuery("select img_url from property_types where pt_id = $rowid ");
		if ($pt_result->num_rows() > 0) {
			$pt_row = $pt_result->row();
			$photo_name = $pt_row->img_url;
			if (isset($photo_name) && !empty($photo_name) && file_exists('uploads/prop_type/' . $photo_name))
				unlink('uploads/prop_type/' . $photo_name);
		}

		$rows = $this->Common_model->commonDelete($tbl, $rowid, $pid);

		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" >
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								' . $rows . ' ' . $fld . ' ' . mlx_get_lang("Deleted Successfully") . '
							</div>
							';
		redirect($url, 'location', '301');
	}
}
