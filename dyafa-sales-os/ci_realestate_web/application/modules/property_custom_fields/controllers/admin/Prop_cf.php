<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Prop_cf extends MY_Controller {
	
	var $post_property_credit;
	
	function __construct() 
	{
        parent::__construct();
        /*if(!$this->isLogin())
		{
			redirect('/logins','location');
		}*/
		$CI =& get_instance();
		
		/*$this->load->library('Language_lib');
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
		$this->custom_fields();	
	}
	
	
	public function custom_fields()
	{
		
		/*if(!$this->isLogin())
		{
			redirect('/logins','location');
		}
		*/
		$CI =& get_instance();
		$theme = $CI->config->item('theme') ;
		
		$this->load->library('global_lib');
		
		/*$data = $this->global_lib->uri_check();
		
		$data['myHelpers']=$this;*/
		$data = $this->data;
		$this->load->model('Common_model');
		$this->load->helper('text');
		$data['user_type'] = $this->session->userdata('user_type');
		if(isset($_POST['submit']) || isset($_POST['draft']))
		{
			extract($_POST);
			
			if(empty($user_id) || $user_id == 0)
			{	
				$_SESSION['msg'] = '<p class="error_msg">Session Expired.</p>';
				$_SESSION['logged_in'] = false;	
				$this->session->set_userdata('logged_in', false);
				redirect('/logins','location');
			}
			
			
			$custom_field_meta = array();
			if(isset($custom_field_name) && count($custom_field_name) > 0)
			{
				foreach($custom_field_name as $k=>$v)
				{
					if(!empty($v))
					{
						$slug = $custom_field_slug[$k];
						//$req = $custom_field_required[$k];
						$req = '';
						$inner_array = array('title' => trim($v),
											'slug' => $slug,
											'is_req' => $req
											);
						if(isset($custom_field_type) && isset($custom_field_type[$k]) && !empty($custom_field_type[$k]))
						{
							$inner_array['type'] = $custom_field_type[$k];
							if(isset($custom_field_option) && isset($custom_field_option[$k]))
							{
								$inner_array['options'] = $custom_field_option[$k];
							}
						}
					
						$custom_field_meta[] = $inner_array;
						
						$col_result = $this->Common_model->commonQuery("SHOW COLUMNS FROM properties LIKE '$slug'");
						if($col_result->num_rows() == 0)
						{
							$sql = "ALTER TABLE properties ADD  $slug varchar(255) DEFAULT ''";
							$this->Common_model->commonQuery($sql);							
						}
						
					}
				}
			}
			
			
			$this->global_lib->update_option('property_custom_fields',json_encode($custom_field_meta));
			
			$_SESSION['msg'] = '
						<div class="alert alert-success alert-dismissable" >
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							Custom Fields Updated Successfully.
						</div>
						';
			redirect('admin/property/custom_fields','location');	
				
		}
		
		if($this->global_lib->get_option('property_custom_fields'))
		{
			$data['custom_field_list'] = json_decode($this->global_lib->get_option('property_custom_fields'),true);
		}		
		
		
		
		//$data['content'] = $this->theme."/property/custom_fields";
		$data['content'] = "property_custom_fields/admin/custom_fields";
		
		$this->load->view($this->theme."/header",$data);
		
	}
	
	
	public function delete_cf($slug = null)
	{
		
		$CI =& get_instance();
		$this->load->model('Common_model');
		$this->load->library('global_lib');
		if($this->global_lib->get_option('property_custom_fields'))
		{
			$custom_field_list = json_decode($this->global_lib->get_option('property_custom_fields'),true);
			if(isset($custom_field_list) && !empty($custom_field_list)) { 
				foreach($custom_field_list as $cfk => $cfv)
				{
					if($cfv['slug'] == $slug)
					{
						unset($custom_field_list[$cfk]);
					}
				}
			}
			$this->global_lib->update_option('property_custom_fields',json_encode($custom_field_list));
			
			$this->Common_model->commonQuery("ALTER TABLE properties DROP $slug");
		}
		
		$url=$this->theme.'/property/custom_fields';	 	
		$fld='Custom Field';
					
		$_SESSION['msg'] = '<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								'.$fld.' Deleted Successfully.
							</div>
							';
		redirect($url,'location','301');	
	}
	
	
	
}
