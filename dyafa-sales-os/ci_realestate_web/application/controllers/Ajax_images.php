<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_images extends MY_Controller {
	
	public function upload_image_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();		
		$this->load->library('global_lib');		
		$CI->load->library('simpleimage');		
		$target = $user_type."/";
		
		if(!is_dir('uploads/'.$target))
		{
			mkdir('uploads/'.$target,0777);
		}
		
		if(isset($_FILES) && !empty($_FILES))		
		{				
			$name = $_FILES["img"]["name"];				
			$path_parts = pathinfo($_FILES["img"]["name"]);								
			$extension = $path_parts['extension'];				
			$actual_name= $path_parts['filename'];								
			$exp_data = explode(' ',$actual_name);				
			$actual_name = implode('_',$exp_data);				
			$name = $actual_name.'-'.time().".".$extension;								
			$CI->simpleimage->load($_FILES['img']['tmp_name']);				
			$CI->simpleimage->save('uploads/'.$target.$name);								
			header('Content-type: application/json');				
			echo json_encode(array('img_url'=> base_url().'uploads/'.$target.$name,'img_name' => $name));
		}			
	}	
	
	public function delete_image_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		$this->load->library('global_lib');		
		$target = $user_type."/";
		
		if(file_exists('uploads/'.$target.$img_name))
		{
			unlink('uploads/'.$target.$img_name);
		}
		
		echo 'success';
	}
	
}
