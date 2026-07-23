<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_images extends MY_Controller {
	
	public function upload_gallery_images_callback_func()
	{
		extract($_POST);		
		$CI =& get_instance();		
		$this->load->library('Global_lib');		
		$this->load->model('Common_model');		
		
		$target = 'media/';
		
		if(!is_dir('uploads/'.$target))
		{
			mkdir('uploads/'.$target,0777,true);
		}
		
		$uploaded_path = 'uploads/'.$target;
		
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		 
		$file_name = $_REQUEST["name"];
		
		$file_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file_name);
		
		$explod = explode(".", $file_name);
		$extension = end($explod);
		$name = str_replace('.'.$extension,'',$file_name);
		
		$first = 1;
		$separator = '-';
		
		if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'thumbs')
		{
			$new_file_name = $thumbnail_image_name = $name.'-300X300.'.$extension;
		}
		else if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
		{
			$new_file_name = $medium_image_name = $name.'-500X300.'.$extension;
		}
		else
		{
			$new_file_name = $file_name;
		}
		
		
		while ( file_exists('uploads/'.$target . $new_file_name ) ) 
		{
			if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'thumbs')
			{
				$new_file_name = $thumbnail_image_name = $name.$separator.$first.'-300X300'.".".$extension;
			}
			else if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
			{
				$new_file_name = $medium_image_name = $name.$separator.$first.'-500X300'.".".$extension;
			}
			else
			{
				$new_file_name = $name.$separator.$first.".".$extension;  
			}
			
			
			$first++;   
		}
		
		$file_name = $new_file_name;
		$filePath = $uploaded_path.$file_name;
		 
		 
		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
		  $in = @fopen($_FILES['file']['tmp_name'], "rb");
		  if ($in) {
			while ($buff = fread($in, 4096))
			  fwrite($out, $buff);
		  } else
			die('{"OK": 0, "info": "Failed to open input stream."}');
		 
		  @fclose($in);
		  @fclose($out);
		  
		   @unlink($_FILES['file']['tmp_name']);
		  
		} else
		  die('{"OK": 0, "info": "Failed to open output stream."}');
		 
		if (!$chunks || $chunk == $chunks - 1) {
		  rename("{$filePath}.part", $filePath);
		  
		  $temp = tempnam(sys_get_temp_dir(), 'TMP_');
		  file_put_contents($temp, file_get_contents("$filePath"));
		  
		  
		  unlink($temp);
		  
		  
		  
		  
		  if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
		  {
			   $post_type = 'media';
			   $origoinal_image_name = str_replace('-500X300.','.',$file_name);
			   $thumbnail_image_name = str_replace('-500X300.','-300X300.',$file_name);
			   $medium_image_name = $file_name;
			   $filePath = '../'.$uploaded_path.$thumbnail_image_name;
			   
			   $datai = array( 'image_name' => $origoinal_image_name,
							'image_path' => 'uploads/'.$target,
							'image_type' => 'original',
							'image_alt' => $origoinal_image_name,
							'post_type' => $post_type,
							'user_id' => $this->session->userdata('user_id')
							);
				
				$p_i_parent_ID = $CI->Common_model->commonInsert('post_images',$datai);
			  	
				$datai = array( 
							'parent_image_id' => $p_i_parent_ID,							
							'image_path' => 'uploads/'.$target,	
							'image_name' => $medium_image_name,
							'image_type' => 'medium',
							'image_alt' => $origoinal_image_name,
							'post_type' => $post_type,
							'user_id' => $this->session->userdata('user_id')
							);
							
				$this->Common_model->commonInsert('post_images',$datai);
				
				$datai = array( 
							'parent_image_id' => $p_i_parent_ID,
							'image_path' => 'uploads/'.$target,	
							'image_name' => $thumbnail_image_name,
							'image_type' => 'thumbnail',
							'image_alt' => $origoinal_image_name,
							'post_type' => $post_type,
							'user_id' => $this->session->userdata('user_id')
							);
							
				$this->Common_model->commonInsert('post_images',$datai);
			  
				
				header('Content-type: application/json');				
				echo json_encode(array('type'=> 'success',
									   'thumb_img_url' => $filePath,
									   'img_name' => $origoinal_image_name,
									   'img_id' => $this->global_lib->EncryptClientId($p_i_parent_ID)));
				exit;
		   }
		}
		
		header('Content-type: application/json');				
		echo json_encode(array('type'=> 'error',));
		exit;
	}
	
	public function delete_gallery_images_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		$this->load->library('Global_lib');		
		$image_name = $img_name;
		
		
		$result = $this->Common_model->commonQuery("select * from post_images 
		where image_alt = '$image_name'");
		
		if($result->num_rows() > 0 )
		{
			foreach($result->result() as $row)
			{
				$img_url = $row->image_path.$row->image_name;
				if(file_exists(''.$img_url))
					unlink(''.$img_url);
				$this->Common_model->commonDelete('post_images',$row->image_id,'image_id' );
			}
		}
		echo 'success';
	}
	
	public function upload_image_callback_func()
	{
		extract($_POST);		
		$CI =& get_instance();		
		$this->load->library('Global_lib');			
		$this->load->model('Common_model');		
		
		$target = $image_type.'/';
		
		if(!is_dir('uploads/'.$target))
		{
			mkdir('uploads/'.$target,0777,true);
		}
		
		$uploaded_path = 'uploads/'.$target;
		
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		 
		$file_name = $_REQUEST["name"];
		
		$file_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file_name);
		
		$explod = explode(".", $file_name);
		$extension = end($explod);
		$name = str_replace('.'.$extension,'',$file_name);
		
		$first = 1;
		$separator = '-';
		
		if($image_type == 'banner' || $image_type == 'site_slider' || $image_type == 'logo'|| $image_type == 'fevicon')
		{
			$new_file_name = $file_name;
		}
		else
		{
			if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'thumbs')
			{
				$new_file_name = $name.'-300X300.'.$extension;
			}
			else if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
			{
				$new_file_name = $medium_image_name = $name.'-500X300.'.$extension;
			}
			else
			{
				$new_file_name = $file_name;
			}
		}
		
		while ( file_exists('uploads/'.$target . $new_file_name ) ) 
		{
			if($image_type == 'banner' || $image_type == 'site_slider' || $image_type == 'logo'|| $image_type == 'fevicon')
			{
				$new_file_name = $name.$separator.$first.".".$extension;  
			}
			else
			{
				if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'thumbs')
				{
					$new_file_name = $name.$separator.$first.'-300X300'.".".$extension;
				}
				else if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
				{
					$new_file_name = $medium_image_name = $name.$separator.$first.'-500X300'.".".$extension;
				}
				else
				{
					$new_file_name = $name.$separator.$first.".".$extension;  
				}
			}
			
			$first++;   
		}
		
		$file_name = $new_file_name;
		$filePath = $uploaded_path.$file_name;
		 
		 
		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
		  $in = @fopen($_FILES['file']['tmp_name'], "rb");
		  if ($in) {
			while ($buff = fread($in, 4096))
			  fwrite($out, $buff);
		  } else
			die('{"OK": 0, "info": "Failed to open input stream."}');
		 
		  @fclose($in);
		  @fclose($out);
		  
		   @unlink($_FILES['file']['tmp_name']);
		  
		} else
		  die('{"OK": 0, "info": "Failed to open output stream."}');
		 
		if (!$chunks || $chunk == $chunks - 1) {
		  rename("{$filePath}.part", $filePath);
		  
		  $temp = tempnam(sys_get_temp_dir(), 'TMP_');
		  file_put_contents($temp, file_get_contents("$filePath"));
		  
		  
		  unlink($temp);
		  
		  if($image_type == 'banner' || $image_type == 'site_slider' || $image_type == 'logo'|| $image_type == 'fevicon')
			{
			   $origoinal_image_name = $file_name;
			   $filePath = '../'.$uploaded_path.$file_name;
			   			header('Content-type: application/json');				
				echo json_encode(array('type'=> 'success',
									   'thumb_img_url' => $filePath,
									   'img_name' => $origoinal_image_name));
				exit;

			}
			else if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
		   {
				
				$origoinal_image_name = str_replace('-500X300.','.',$file_name);
			   $thumbnail_image_name = str_replace('-500X300.','-300X300.',$file_name);
			   $medium_image_name = $file_name;
			   $filePath = '../'.$uploaded_path.$thumbnail_image_name;
			   			header('Content-type: application/json');				
				echo json_encode(array('type'=> 'success',
									   'thumb_img_url' => $filePath,
									   'img_name' => $origoinal_image_name));
				exit;

			}
			else
			{
				header('Content-type: application/json');				
				echo json_encode(array('type'=> 'error',));
				exit;
			}
		}
		
		header('Content-type: application/json');				
		echo json_encode(array('type'=> 'error',));
		exit;
	}
	
	public function delete_image_callback_func()	
	{		 
		extract($_POST);		
		$CI =& get_instance();	
		$this->load->model('Common_model');		
		$this->load->library('global_lib');		
		$target = $img_type;
		
		$explod = explode(".", $img_name);
		$extension = end($explod);
		$name = str_replace('.'.$extension,'',$img_name);
		
		$thumb_img_name = $name.'-300X300.'.$extension;
		$medium_img_name = $name.'-500X300.'.$extension;
		
		if(file_exists('uploads/'.$target.'/'.$img_name))
		{
			unlink('uploads/'.$target.'/'.$img_name);
		}
		if(file_exists('uploads/'.$target.'/'.$medium_img_name))
		{
			unlink('uploads/'.$target.'/'.$medium_img_name);
		}
		if(file_exists('uploads/'.$target.'/'.$thumb_img_name))
		{
			unlink('uploads/'.$target.'/'.$thumb_img_name);
		}
		
		if(isset($element_id) && !empty($element_id))
		{
			$decId = $this->global_lib->DecryptClientId($element_id);
			if($target == 'story')
			{
				$datai = array( 'image' => '');
				$this->Common_model->commonUpdate('wedding_story',$datai,'id',$decId);
			}else if($target == 'relatives')
			{
				$datai = array( 'image' => '');
				$this->Common_model->commonUpdate('wedding_relatives',$datai,'r_id',$decId);
			}
			else if($target == 'blogs')
			{
				$datai = array( 'image' => '');
				$this->Common_model->commonUpdate('blogs',$datai,'b_id',$decId);
			}
			else if($target == 'weddings')
			{
				if(isset($element_column) && !empty($element_column))
				{
					$datai = array( $element_column => '');
					$this->Common_model->commonUpdate('wedding_details',$datai,'id',$decId);
				}
			}
		}
		
		echo 'success';
	}
	
	public function add_image_from_media_ajax_callback_func()
	{
		$CI =& get_instance();
		$this->load->library('Global_lib');
		extract($_POST);
		$this->load->model('Common_model');
		$data = '';
		$user_id = $this->session->userdata('user_id');
		$user_type = $this->session->userdata('user_type');
		if($user_type == 'admin')
		{
			$query2= "select * 
				from post_images
				where image_type = 'thumbnail'
				order by image_id DESC
				";	
		}
		else
		{
			$query2= "select * 
				from post_images
				where image_type = 'thumbnail' and user_id = $user_id
				order by image_id DESC";	
		}
		$result2 = $this->Common_model->commonQuery($query2);
		
		if(isset($img_data))
		{
			$img_array = explode(',',$img_data);
		}
		
		if($result2->num_rows() > 0 )
		{
			$data .= '<ul class="media_img_block">';
			foreach($result2->result() as $img_row)
			{
				if(isset($img_array) && count($img_array) > 0 && in_array($this->global_lib->EncryptClientId($img_row->parent_image_id),$img_array) && file_exists(''.$img_row->image_path.$img_row->image_name))
				{
					$data .= '<li><a class="lazy-load-processing" data-toggle="tooltip" data-continer="body" data-title="'.$img_row->image_alt.'" href="#" data-image-id="'.$this->global_lib->EncryptClientId($img_row->parent_image_id).'"><img class="selected lazy-img-elem" data-src="'.base_url().''.$img_row->image_path.$img_row->image_name.'" ><span class="select-check" style="display:block;"><i class="fa fa-check"></i></span></a></li>';
				}
				else if(file_exists(''.$img_row->image_path.$img_row->image_name))
				{
					$data .= '<li><a class="lazy-load-processing" data-toggle="tooltip" data-continer="body" data-title="'.$img_row->image_alt.'" href="#" data-image-id="'.$this->global_lib->EncryptClientId($img_row->parent_image_id).'"><img class="lazy-img-elem" data-src="'.base_url().''.$img_row->image_path.$img_row->image_name.'" ><span class="select-check"><i class="fa fa-check"></i></span></a></li>';
				}
			}
			$data .= '</ul><p style="font-style: oblique;">'.mlx_get_lang("Click on image to select").'</p>';
		}
		else
			$data .= "<p>".mlx_get_lang('Media Library is Empty')."</p>";
		
		if($result2->num_rows() > 0 ){
			$data .= "<p align='right'><input type='button' class='custom-file-upload insert_in_product' value='".mlx_get_lang('Insert Into Property')."'></p>";
		}
		$str = '';
		$str .= '<html>
		<head>
			<style>
				
			.media_img_block {
				margin-bottom: 0;
				margin-left: -5px;
				margin-right: -5px;
				max-height: 300px;
				overflow: auto;
				padding: 0;
			}
			.media_img_block > li {
				display: inline-block;
				height: 150px;
				width: 20%;
				padding: 8px;
				overflow: hidden;
			}
			.media_img_block > li a {
				position:relative;
				float: left;
				width: 100%;
				height: 100%;
				border: 1px solid #e2e2e2;
			}
			.media_img_block > li a.lazy-load-processing {
				background-image: url("data:image/gif;base64,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");
				background-repeat: no-repeat;
				background-position: 50% 50%;
			}
			.media_img_block img {
				width: auto;
				max-width: 100%;
				height: auto;
				max-height: 100%;
				display: inline-block;
				position: absolute;
				top: 0px;
				bottom: 0px;
				margin: auto;
				left: 0px;
				right: 0px;
				border: 0px none;
				outline: 0px solid #605ca8 !important;
			}
			.custom-file-upload {
				border: 1px solid #ccc;
				display: inline-block;
				padding: 6px 12px;
				cursor: pointer;
				font-weight: 500;
			}
			.media_img_block img.selected {
				border: 1px solid #333;
			}
			.media_img_block span.select-check {
				display:none;
				background-color: rgba(0, 0, 0, 0.5);
				bottom: 0;
				color: #fff;
				left: 0;
				position: absolute;
				right: 0;
				text-align: center;
				top: 0;
			}
			.select-check i {
				position: relative;
				top: 40%;
			}
			.white-popup-block {
				padding: 10px 15px;
			}
			#custom-content > h3 {
				margin-top: 0;
			}
			#custom-content p {
				margin-bottom: 0;
			}

			</style>
			
		</head>
		<body>
			<div id="custom-content" class="white-popup-block" style="max-width:600px; margin: 20px auto;">';
				
		
			$str .= '<h3>'.mlx_get_lang("Media Library").'</h3>
				'.$data;
		
		$str .= '</div>
		</body></html>';
		
		echo $str;
	}

	public function upload_property_images_callback_func()
	{
		extract($_POST);		
		$CI =& get_instance();		
		$this->load->library('Global_lib');		
		$this->load->model('Common_model');		
		
		$target = 'media/';
		
		if(!is_dir('uploads/'.$target))
		{
			mkdir('uploads/'.$target,0777,true);
		}
		
		$uploaded_path = 'uploads/'.$target;
		
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		 
		$file_name = $_REQUEST["name"];
		
		$file_name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $file_name);
		
		$explod = explode(".", $file_name);
		$extension = end($explod);
		$name = str_replace('.'.$extension,'',$file_name);
		
		$first = 1;
		$separator = '-';
		
		if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'thumbs')
		{
			$new_file_name = $thumbnail_image_name = $name.'-300X300.'.$extension;
		}
		else if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
		{
			$new_file_name = $medium_image_name = $name.'-500X300.'.$extension;
		}
		else
		{
			$new_file_name = $file_name;
		}
		
		
		while ( file_exists('uploads/'.$target . $new_file_name ) ) 
		{
			if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'thumbs')
			{
				$new_file_name = $thumbnail_image_name = $name.$separator.$first.'-300X300'.".".$extension;
			}
			else if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
			{
				$new_file_name = $medium_image_name = $name.$separator.$first.'-500X300'.".".$extension;
			}
			else
			{
				$new_file_name = $name.$separator.$first.".".$extension;  
			}
			
			$first++;   
		}
		
		$file_name = $new_file_name;
		$filePath = $uploaded_path.$file_name;
		 
		 
		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
		  $in = @fopen($_FILES['file']['tmp_name'], "rb");
		  if ($in) {
			while ($buff = fread($in, 4096))
			  fwrite($out, $buff);
		  } else
			die('{"OK": 0, "info": "Failed to open input stream."}');
		 
		  @fclose($in);
		  @fclose($out);
		  
		   @unlink($_FILES['file']['tmp_name']);
		  
		} else
		  die('{"OK": 0, "info": "Failed to open output stream."}');
		 
		if (!$chunks || $chunk == $chunks - 1) {
		  rename("{$filePath}.part", $filePath);
		  
		  $temp = tempnam(sys_get_temp_dir(), 'TMP_');
		  file_put_contents($temp, file_get_contents("$filePath"));
		  
		  
		  unlink($temp);
		  
		  if(isset($_GET['diretorio']) && $_GET['diretorio'] == 'medium')
		  {
			   $post_type = 'media';
			   $origoinal_image_name = str_replace('-500X300.','.',$file_name);
			   $thumbnail_image_name = str_replace('-500X300.','-300X300.',$file_name);
			   $medium_image_name = $file_name;
			   $filePath = '../'.$uploaded_path.$thumbnail_image_name;
			   
			   $datai = array( 
							'parent_image_id' => 0, 
							'image_name' => $origoinal_image_name,
							'image_path' => 'uploads/'.$target,
							'image_type' => 'original',
							'image_alt' => $origoinal_image_name,
							'post_type' => $post_type,
							'user_id' => $this->session->userdata('user_id')
							);
				
				$p_i_parent_ID = $CI->Common_model->commonInsert('post_images',$datai);
			  	
				$datai = array( 
							'parent_image_id' => $p_i_parent_ID,							
							'image_path' => 'uploads/'.$target,	
							'image_name' => $medium_image_name,
							'image_type' => 'medium',
							'image_alt' => $origoinal_image_name,
							'post_type' => $post_type,
							'user_id' => $this->session->userdata('user_id')
							);
							
				$this->Common_model->commonInsert('post_images',$datai);
				
				$datai = array( 
							'parent_image_id' => $p_i_parent_ID,
							'image_path' => 'uploads/'.$target,	
							'image_name' => $thumbnail_image_name,
							'image_type' => 'thumbnail',
							'image_alt' => $origoinal_image_name,
							'post_type' => $post_type,
							'user_id' => $this->session->userdata('user_id')
							);
							
				$this->Common_model->commonInsert('post_images',$datai);
			  
				
				header('Content-type: application/json');				
				echo json_encode(array('type'=> 'success',
									   'thumb_img_url' => $filePath,
									   'img_name' => $thumbnail_image_name,
									   'img_id' => $this->global_lib->EncryptClientId($p_i_parent_ID)));
				exit;
		   }
		}
		
		header('Content-type: application/json');				
		echo json_encode(array('type'=> 'error',));
		exit;
	}
	
}
