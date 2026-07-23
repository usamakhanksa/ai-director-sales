<?php


	function get_property_meta($property_id = NULL, $meta_key = NULL)
	{
		$CI = &get_instance();
		if(!is_numeric($property_id)) $property_id = DecryptClientID($property_id);
		$property_meta = "";
		if (!isset($CI->site_properties[$property_id])) {
			get_property_metadata($property_id);
		}
		
		if (isset($CI->site_properties[$property_id][$meta_key])) 
		{
			$property_meta = $CI->site_properties[$property_id][$meta_key];
		}
		return $property_meta;
	}
	
	function get_property_metadata($property_id = NULL)
	{
		$CI =& get_instance();
		
		if(!is_numeric($property_id)) $property_id = DecryptClientID($property_id);
		
		$query = $CI->Common_model->commonQuery("select * from property_meta where property_id = '$property_id'");	
		if($query->num_rows()>0)
		{
			$metadata_array = array();
			foreach($query->result() as $row)
			{	
				$metadata_array[$row->meta_key] = $row->meta_value;
			}
			$CI->site_properties[$property_id] = 		$metadata_array;
		}
		
		
		
		$query = $CI->Common_model->commonQuery("select * from properties where p_id = '$property_id'");

		if ($query->num_rows() > 0) {
			$row = $query->row();
			$CI->site_properties[$property_id]['created_by'] = $row->created_by;
			$CI->site_properties[$property_id]['title'] = $row->title;
			$CI->site_properties[$property_id]['slug'] = $row->slug;
			
		}
		
		
	}

	function update_property_meta($property_id, $key, $val)
	{
		$CI = &get_instance();
		if (get_property_meta($property_id, $key)) {
			
			
			$CI->Common_model->commonQuery("update property_meta set meta_value ='" . $val . "' where property_id=" . $property_id . " and meta_key='" . $key . "'");
		} else {
			$datai = array('meta_key' => $key,	'meta_value' => $val, 'property_id' => $property_id);
			$mid =  $CI->Common_model->commonInsert('property_meta', $datai);
			$CI->site_properties[$property_id][$key] = $val;
		}
	}




		
	function get_property_types()
	{

		$CI = &get_instance();
		
		$property_types  =  array();
		
		$query = $CI->Common_model->commonQuery("select *  from property_types ");
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$property_types[$row->pt_id] = array( "title"  =>  $row->title , "slug"  =>  $row->slug , 
													"status"  =>  $row->status , "img_url"  =>  $row->img_url , 
													"pt_id"  =>  $row->pt_id ,"meta_options"  =>  $row->meta_options, );
			}
		}
		$CI->property_types =  $property_types;

	}

	function get_property_type_field( $id = "" , $field = ""){
		
		
		$CI = &get_instance();
		
		if(!property_exists($CI , "property_types" ))
			get_property_types();
			
		if(!is_numeric($id))
			$id = DecryptClientID($id);
		
		/*print_r($CI->property_types);*/
		if(count($CI->property_types)  > 0 ){
			foreach($CI->property_types as $pid => $pvals){
				if($pid == $id ){
					return $pvals[$field];
				}
			}
		}	
		return false;
	}








	/*function get_property_meta($id = NULL ,$key = NULL)
	{

		$CI =& get_instance();

		$query = $CI->Common_model->commonQuery("select * from property_meta where property_id = '$id' AND meta_key = '$key' ");	
		if($query->num_rows()>0)
		{
			$row = $query->row();
			return $val = $row->meta_value;
		}
		else
			return false;
	}

	function get_property_metadata($id = NULL)
	{
		$CI =& get_instance();
		$query = $CI->Common_model->commonQuery("select * from property_meta where property_id = '$id'");	
		if($query->num_rows()>0)
		{
			$metadata_array = array();
			foreach($query->result() as $row)
			{	
				$metadata_array[$row->meta_key] = $row->meta_value;
			}
			return $metadata_array;
		}
		else
			return false;

	}*/