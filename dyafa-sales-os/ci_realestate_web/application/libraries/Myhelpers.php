<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myhelpers {

	public function Index(){}
	
	
	public function make_thumb($src,$thumb, $dest, $desired_width) 
	{

	/* read the source image */
	$source_image = imagecreatefromjpeg($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	
	/* find the "desired height" of this thumbnail, relative to the desired width  */
	$desired_height = floor($height * ($desired_width / $width));
	
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	
	/* create the physical thumbnail image to its destination */
	if(!is_writeable(dirname($thumb)))
            {
                echo 'unable to write image in ' . dirname($thumb);
            }
            else
            {
				$dest = $dest.'/'.$thumb;
				imagejpeg($virtual_image, $dest);
            }
	}
	
	// Get Post_meta
	public function get_post_meta($id = NULL ,$key = NULL)
	{
		/*if($id != 0)
		return "kuch to aaya hai..";
		else
		return "kuch bhi nhi aaya..";*/
		$this->load->model('Common_model');
		
		//return $key;	
		
		//echo "select * from post_meta where 'post_id' = '$id' AND 'meta_key' = '$key' ";
		
		$query = $this->Common_model->commonQuery("select * from post_meta where post_id = '$id' AND meta_key = '$key' ");	
		
		if($query->num_rows()>0)
		{
			$row = $query->row();
			return $val = $row->meta_value;
		}
		else
			return false;
	}
	
	// Update Post_meta
	public function update_post_meta($post_id , $key  ,$val)
	{
		$this->load->model('Common_model');
		//$data['query'] = $this->Common_model->commonQuery("select * from post_meta where `post_id` = $post_id ");
		$query = $this->Common_model->commonQuery("select * from post_meta where post_id = '$post_id' AND meta_key = '$key' ");			
		//echo $query->num_rows();

		if($query->num_rows() > 0)
		{
			$row=$query->row();
			$meta_id=$row->meta_id;
			$datai = array('meta_value' => $val);
			
			return $metaid = $this->Common_model->commonUpdate('post_meta',$datai,'meta_id',$meta_id);			
		}
		else
		{
			$datai = array( 'meta_key' => $key,	'meta_value' => $val, 'post_id' => $post_id);
								
			return $metaid=$this->Common_model->commonInsert('post_meta',$datai);
		}
		//exit;
	}
	
	
	
	
	
	public function encrypt_decrypt($action, $string) {
	   $output = false;
	   $key = '$)*&hj8I@?@!~587^mC~8l3|6@>D';
	   $iv = md5(md5($key));
	   if( $action == 'encrypt' ) {
		   $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, $iv);
		   $output = base64_encode($output);
	   }
	   else if( $action == 'decrypt' ){
		   $output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, $iv);
		   $output = rtrim($output, "");
	   }
	   return $output;
	}
	
	
	public function getUserCode($useremail)
	{
		$this->load->model('Common_model');
		
		$chars = md5(uniqid( $useremail, true));
		$user_code = substr($chars,0,8);
		
		$options = array('where'=> array('user_code'=>$user_code));
		$query = $this->Common_model->commonSelect('users',$options );			
	
		if ($query->num_rows() > 0 )
		{
			return $this->getUserCode($useremail);
		}
		else
			return $user_code;
		
	}
	
	public function getRegisterLists()
	{
		
		$this->load->model('Common_model');
		extract($_REQUEST);
		//print_r($_REQUEST);
		//die;
		if($username == '' or $useremail == '' or $userpass == '' or $confpass == '')
		{ echo "Please enter all the values."; return;}
		else
		{
			$Username = $this->Common_model->commonQuery("select * from users where user_name='".$username."'");
			if($Username->num_rows > 0)
			{ echo "Username already exists."; return;}
			
			if(preg_match("/^[a-zA-Z0-9._\-]\w+(\.\w+)*\@\w+(\.[a-zA-Z0-9._-]+)*\.[a-zA-Z.]{2,6}$/", $useremail) == 0)
			{ echo "Please enter a valid Email Id."; return;}	
			
			$Useremail = $this->Common_model->commonQuery("select * from users where user_email='".$useremail."'");
			if($Useremail->num_rows > 0)
			{ echo "Useremail already registered."; return;}
			
			if($userpass != $confpass)
			{ echo "Confirm password doesn't match."; return;}
		
		}
		
		
		
		echo "Success";
		
	}
	
	
	public function getUserLists()
	{
		
		$this->load->model('Common_model');
		extract($_REQUEST);
		
		$options = array('where'=>array('user_name'=>$username,'user_pass'=>md5($userpass))); // , 'user_status'=>'Y'));
		$query = $this->Common_model->commonSelect('users',$options);	
	
		if($query->num_rows() > 0)
		{
		 echo "TRUE";
		}
		else
		{
			echo "FALSE";
		}
	}
	
	
	public function show_sub_cats($cat_type,$parent_id,$cat_level)
	{
		$CI =& get_instance();
		$CI->load->model('Categories_model');
		$query = $CI->Categories_model->show_sub_cats($cat_type,$parent_id,$cat_level);	
		return $query;		
	}
	public function get_sub_cat_count($cat_type,$parent_id)
	{
		$CI =& get_instance();
		$CI->load->model('Categories_model');
		$query = $CI->Categories_model->get_sub_cat_count($cat_type,$parent_id);	
		return $query;		
	}
	
	public function update_hit_count($field_name,$field_id)
	{
		$CI =& get_instance();
		$CI->load->model('Myhelper_model');
		$CI->Myhelper_model->update_hit_count($field_name,$field_id);	
		//return $query;		
	}
	

}

/* End of file Myhelpers.php */