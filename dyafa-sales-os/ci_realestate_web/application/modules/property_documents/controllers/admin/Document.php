<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document extends MY_Controller {
	
	var $post_property_credit;
	
	function __construct() 
	{
        parent::__construct();
       
	    if(!$this->isAdminLogin())
		{
			
			redirect('/logins','location');
		}
	   
		$CI =& get_instance();
		
		$this->load->library('Language_lib');
		$this->load->library('Global_lib');
		/*
		$this->load->library('Package_lib');
		$user_id = $this->session->userdata('user_id');
		$this->post_property_credit = $this->package_lib->get_credits_by_user_id($user_id,'post_property_credit');*/
		
		$this->data = $this->global_lib->uri_check();
		$this->data['myHelpers']=$this;
		$this->data['CI']=$CI;
		
		$this->theme = $CI->config->item('admin_theme') ;
		$this->data['theme']=$this->theme;
		
	}
	
	public function index()
	{
		$this->manage();	
	}
	
	public function manage()
	{
		
		$CI =& get_instance();
		
		
		$data = $this->data;
		
		
		$user_id = $this->session->userdata('user_id');
		$user_type = $this->session->userdata('user_type');
		if($user_type == 'admin')
		{
			$qry = "select * from attachments as att
				where att_type = 'document' and att_status = 'Y'
				order by att.att_id DESC";
		}
		else
		{
			$qry = "select * from attachments as att
				where att_type = 'document' and user_id = $user_id and att_status = 'Y'
				order by att.att_id DESC";
		}
		$data['document_list'] = $this->Common_model->commonQuery($qry);	
		
		
		$data['content'] = "property_documents/admin/manage";
		
		$this->load->view("$this->theme/header",$data);
		
	}
	
	public function settings()
	{
		if(!$this->isLogin())
		{
			redirect('/logins','location');
		}
		
		$CI =& get_instance();
		$theme = $CI->config->item('theme') ;
		
		$this->load->library('Global_lib');
		$data = $this->global_lib->uri_check();
		
		$data['myHelpers']=$this;
		$this->load->model('Common_model');
		$this->load->helper('text');
		
		if(isset($_POST['submit']))
		{
			extract($_POST);
			$this->form_validation->set_error_delimiters('<div class="box-body"><div class="alert alert-danger alert-dismissable" style="margin-bottom:0px;">
				<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				', "</div></div>");
			
				extract($_POST,EXTR_OVERWRITE);
				
				if($this->session->userdata('user_id') != 1)
				{	
					redirect('/','location');
				}
				
				
				if(isset($options['document_file_type_options']) && $options['document_file_type_options'] != 'all')
				{
					
					$document_file_type = array();
					if(isset($options['document_file_type']) && !empty($options['document_file_type']))
						$document_file_type = $options['document_file_type'];
					
				}
				else
				{
					$document_file_type = array();
				}
				$options['document_file_type'] = json_encode($document_file_type);
				
				$document_image_types = array();
				if(isset($options['document_image_types']) && !empty($options['document_image_types']))
				{
					foreach($options['document_image_types']['title'] as $k=>$v)
					{
						$disable = false;
						if($v == 'Thumbnail')
						{
							$disable = true;
						}
						$document_image_types[] = array('title' => $v, 'width' => $options['document_image_types']['width'][$k], 
									'height' => $options['document_image_types']['height'][$k], 'disable' => $disable);
					}
				}
				else
				{
					$document_image_types[] = array('title' => 'Thumbnail', 'width' => 150, 
									'height' => 150, 'disable' => true);
				}
				$options['document_image_types'] = json_encode($document_image_types);
				
				
				foreach($options as $key=>$value)
				{
					if(empty($key)) continue;
					
					$this->global_lib->update_option($key,$value);
				}
				$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable">
										<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
										Document Settings Update Successfully. </div>';
										
					

			
		}
		
		$data['options_list'] = $this->Common_model->commonQuery("select * from options");
		
		$data['theme']=$theme;
		
		$data['content'] = "$theme/documents/settings";
		
		$this->load->view("$theme/header",$data);
		
	}
	
	
	
	public function type($pdt_id = null)
	{
		
		$CI =& get_instance();
		$theme = $CI->config->item('theme') ;
		
		
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		
		$data['user_type'] = $this->session->userdata('user_type');
		if(isset($_POST['submit']) || isset($_POST['draft']))
		{
			
			extract($_POST);
			foreach($_POST as $k=>$v)
			{
				$_POST[$k] = $this->security->xss_clean($v);
				$_POST[$k] = str_replace('[removed]','',$_POST[$k]);
			}
			
			
			
			if(isset($_POST['multi_lang']) && !empty($_POST['multi_lang']))
			{
				foreach($_POST['multi_lang'] as $mk=>$mv)
				{
					foreach($mv as $mvk=>$mvv)
					{
						$_POST['multi_lang'][$mk][$mvk] = str_replace('[removed]','',$mvv);
					}
				}
			}
				
			$user_id = $this->session->userdata('user_id');
			
			
			if(isset($doc_type_id) && !empty($doc_type_id))
			{
				$decId = $this->DecryptClientId($doc_type_id);
				
				$pt_result = $this->Common_model->commonQuery("select slug from property_doc_types where pdt_id = $decId ");
				if($pt_result->num_rows() > 0)
				{
					$pt_row = $pt_result->row();
					$old_slug = $pt_row->slug;
				}
				
				$config = array( 'field' => 'slug', 'title' => 'title', 'table' => 'property_doc_types', 
				'id' => 'pdt_id');
				$this->load->library('Slug_lib', $config);
				
				$slug = $this->language_lib->get_normal_string($title);
				
				$datap = array( 'title' => $slug, );
				$slug = $this->slug_lib->create_uri($datap);
				
				$datai = array( 
							'title' => trim($title),
							'description' => trim($description),
							'is_required' => $is_required,
							'error_message' => trim($error_message),
							'slug' => $slug,
							'status' => $status,
							'pdt_order' => $pdt_order,
							);
				
				$this->Common_model->commonUpdate('property_doc_types',$datai,'pdt_id',$decId);
				
				
				$this->Common_model->commonQuery("UPDATE property_meta 
								SET meta_key = replace(meta_key, '$old_slug-ids', '$slug-ids')");
				
				$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable" style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							'.mlx_get_lang("Document Type Updated Successfully").'
						</div>
						';
			}
			else
			{
				
				$config = array( 'field' => 'slug', 'title' => 'title', 'table' => 'property_doc_types', 
				'id' => 'pdt_id');
				$this->load->library('Slug_lib', $config);
				
				$slug = $this->language_lib->get_normal_string($title);
				
				$datap = array( 'title' => $slug, );
				$slug = $this->slug_lib->create_uri($datap);
				
				
				$datai = array( 
							'title' => trim($title),
							'description' => trim($description),
							'is_required' => $is_required,
							'error_message' => trim($error_message),
							'slug' => $slug,
							'created_on' => time(),
							'status' => $status,
							'created_by' => $user_id,
							'pdt_order' => $pdt_order,
							);
							
				$pdt_id = $this->Common_model->commonInsert('property_doc_types',$datai);
				
				$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable"  style="margin-bottom:0px; margin-top:10px;">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							'.mlx_get_lang("Document Type Added Successfully").'
						</div>
						';
			}
			
			redirect($this->theme.'/document/type','location');	
				
		}
		
		$data['query'] = $this->Common_model->commonQuery("
						SELECT * FROM `property_doc_types` as prop 
						order by prop.pdt_id DESC");		
		
		if($pdt_id != null)
		{
			$decId = $this->DecryptClientId($pdt_id);
			$pt_result = $this->Common_model->commonQuery("select * from property_doc_types where pdt_id = $decId ");
			if($pt_result->num_rows() > 0)
			{
				$pt_row = $pt_result->row();
				$data['doc_type_id'] = $pt_row->pdt_id;
				$data['title'] = $pt_row->title;
				$data['description'] = $pt_row->description;
				$data['is_required'] = $pt_row->is_required;
				$data['error_message'] = $pt_row->error_message;
				$data['status'] = $pt_row->status;
			}
		}
		
		
		$data['content'] = "property_documents/admin/types";
		
		$this->load->view($this->theme."/header",$data);
		
	}
	
	public function delete_type($rowid)
	{
		
		$CI =& get_instance();
		$this->load->library('Global_lib');
		if(!is_array($rowid))
			$rowid	= $this->global_lib->DecryptClientId($rowid);
		$this->load->model('Common_model');
			
		$tbl='property_doc_types';
		$pid='pdt_id';
		$url=$this->theme.'/document/type/';	 	
		$fld=mlx_get_lang("Documnent Type");
		
		
		$rows = $this->Common_model->commonDelete($tbl,$rowid,$pid );	
		
		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable" >
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								'.$rows.' '.$fld.' '.mlx_get_lang("Deleted Successfully").'
							</div>
							';
		redirect($url,'location','301');	
	}
	
	
}
