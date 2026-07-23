<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banner_lib {

	
	

	public function __construct(){
	
	}
	
	public function get_banners($args = array()){
		
		$CI =& get_instance();
		
		extract($args);
		if(!isset($args['assign_type'])) $assign_type = 'static';
		if(!isset($args['assign_id'])) $assign_id = '';
		
		$multi_lang = $CI->enable_multi_lang;
		$default_lang = $CI->default_language;
		if($multi_lang )
		{
			$for_lang = " and ba.for_lang = '".$CI->default_language."' ";
			
		}else $for_lang = "";
		
		$where = " where ban.b_status = 'Y'   ";
		
		if(isset($created_by))
			$where .= " and ban.created_by = '".$created_by."' ";
		
		$where = apply_filters("cms_banners_extend_where", $where);
		
		$sql = "select ban.b_image from banners as ban
			inner join banner_assigned_to as ba on ba.banner_id = ban.b_id and 
								ba.assign_type='".$assign_type."' and 
								ba.assign_id='".$assign_id."' $for_lang  	
			$where 
			order by ban.b_title ASC";

		$banners  = $CI->Common_model->commonQuery($sql );			
		return $banners;
	}	
	
	
}
