<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ajax_widgets extends MY_Controller {
	
	
	public function save_widget_to_sidebar_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		
		$site_widgets_res = $this->Common_model->commonQuery("
				select * from options 	where option_key = 'site_widgets' 		");	
		
		if($site_widgets_res->num_rows() > 0){
			
			$row = $site_widgets_res->row();
			$option_id = $row->option_id;
			$widgets = $row->option_value;
			$widgets = json_decode($widgets,true);
			
			if(!isset($sidebar_for) || !isset($sidebar_widget)) exit;
			$widget_options = array();
			foreach($_POST as $k => $v){
				if($k == 'sidebar_for' || $k == 'sidebar_widget') continue;
				
				$widget_options[$k] = $v;
			}
			$widget = array('sidebar_for' => $sidebar_for , 'sidebar_widget' => $sidebar_widget ,'sidebar_options' => $widget_options );
			
			$widgets [$sidebar_for] [] = $widget;
			$datai = array( 
					'option_key' => 'site_widgets',	
					'option_value' => json_encode( $widgets )			
					); 
			$this->Common_model->commonUpdate('options',$datai,'option_id',$option_id);
			
			print_r($widgets); exit;
		}else{
			
			$widgets = array();
			
			$widget_options = array();
			foreach($_POST as $k => $v){
				if($k == 'sidebar_for' || $k == 'sidebar_widget') continue;
				
				$widget_options[$k] = $v;
			}
			$widget = array('sidebar_for' => $sidebar_for , 'sidebar_widget' => $sidebar_widget ,'sidebar_options' => $widget_options );
			//$widget = array('sidebar_options' => $widget_options );
			$widgets [$sidebar_for] [] = $widget;
			$datai = array( 
					'option_key' => 'site_widgets',	
					'option_value' => json_encode( $widgets )			
					); 
			$this->Common_model->commonInsert('options',$datai);
		}
		
		echo 'success';
	}
	
	
	public function remove_widget_from_sidebar_callback_func()	
	{		 
		extract($_POST);		
		//print_r($_POST); exit;
		//echo json_encode($_POST);exit;
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		
		$site_widgets_res = $this->Common_model->commonQuery("
				select * from options 	where option_key = 'site_widgets' 		");	
		
		if($site_widgets_res->num_rows() > 0){
			
			$row = $site_widgets_res->row();
			$option_id = $row->option_id;
			$widgets = $row->option_value;
			$widgets = json_decode($widgets,true);
			
			if(!isset($sidebar_for) || !isset($sidebar_widget)) exit;
			/*$widget_options = array();
			foreach($_POST as $k => $v){
				if($k == 'sidebar_for' || $k == 'sidebar_widget') continue;
				
				$widget_options[$k] = $v;
			}
			$widget = array('sidebar_for' => $sidebar_for , 'sidebar_widget' => $sidebar_widget ,'sidebar_options' => $widget_options );
			
			$widgets [$sidebar_for] [] = $widget;
			*/
			if(isset($widgets [$sidebar_for] [$widget_id - 1]))
			{
				unset($widgets [$sidebar_for] [$widget_id - 1]);
				$datai = array( 
					'option_key' => 'site_widgets',	
					'option_value' => json_encode( $widgets )			
					);
					
				$this->Common_model->commonUpdate('options',$datai,'option_id',$option_id);
			}	
			//commonInsert('options',$datai);
			
			
			//print_r($widgets); exit;
		}else{
			
			
		}
		
		echo 'success';
	}
	
	
	
	public function hide_notifications_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		
		if(isset($notif_id) && !empty($notif_id))
		{
			$datai = array( 
					'notif_status' => 'H'
			);
			$encId = $this->DecryptClientId($notif_id);	
			$this->Common_model->commonUpdate('notifications',$datai,'notif_id',$encId);
		}
		echo 'success';
	}
	
	public function remove_notifications_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		
		$encId = $this->DecryptClientId($notif_id);	
		$this->Common_model->commonDelete('notifications',$encId,'notif_id' );
		echo 'success';
	}
	
	public function toggle_featured_property_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		
		if(isset($p_id) && !empty($p_id))
		{
			$datai = array( 
					'is_feat' => $is_feat
			);
			$encId = $this->DecryptClientId($p_id);	
			$this->Common_model->commonUpdate('properties',$datai,'p_id',$encId);
		}
		
		echo 'success';
	}
	
	
	
	
	
}
