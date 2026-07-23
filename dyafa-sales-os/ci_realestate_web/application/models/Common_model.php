<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	function commonSelect($tbl ,$options = NULL)
    {
		$sql = "select * from ".$tbl;
		if($options != NULL)
		{ 
			if(array_key_exists('where',$options))
			{
				$where = array($options['where']);
				//print_r($options['where']);
				//exit;
				if(count($where) > 0)
				{
					$where = current($where);
					$sql .= " where ";
					$once=1;
					foreach($where as $k =>$v)
					{
						$sql .= ($once) ?  " $k = '$v'" : " and $k = '$v'";	
						$once=0;
					}	
				}
			}
	
	
			if(array_key_exists('orderby',$options))
			{
				$orderby = array($options['orderby']);
				//print_r($options['where']);//exit;
				if(count($orderby) > 0)
				{
					$orderby = current($orderby);
					$sql .= " order by ";
					$once=1;
					foreach($orderby as $k =>$v)
					{
						$sql .= ($once) ?  " $k $v" : " , $k  $v ";	
						$once=0;
					}	
				}
			}
		}
		//echo $sql;exit;
		$query = $this->db->query($sql);
//		echo $this->db->last_query();
		return $query; 
    }
	
	function commonSelect2($options)
    {

		//$options['from']='news';
		//$where =array('quest_status'=>'Y','quest_author'=>'1','visibility'=>'1');
		//$array = array('name' => $name, 'title' => $title, 'status' => $status);
		//$options['where']=$where;
		//$orderby =array('quest_status'=>'asc','quest_author'=>'desc','visibility'=>'asc');
		//$options['orderby'] = $orderby;
		if(array_key_exists('from',$options))
			$this->db->from($options['from']);
		
		if(array_key_exists('where',$options))
			$this->db->where($options['where']);
			
		if(array_key_exists('orderby',$options))
			$this->db->order_by($options['orderby']);
		
		$query = $this->db->get();
//		echo $this->db->last_query();
		return $query; 
    }
	
	function commonSelect1($tbl ,$options = NULL)
    {
		$sql = "select * from ".$tbl;
		if($options != NULL)
		{ 
			if(array_key_exists('where',$options))
			{
				$where = array($options['where']);
				//print_r($options['where']);
				//exit;
				if(count($where) > 0)
				{
					$where = current($where);
					$sql .= " where ";
					$once=1;
					foreach($where as $k =>$v)
					{
						$sql .= ($once) ?  " $k = '$v'" : " and $k = '$v'";	
						$once=0;
					}	
				}
			}
	
	
			if(array_key_exists('orderby',$options))
			{
				$orderby = array($options['orderby']);
				//print_r($options['where']);//exit;
				if(count($orderby) > 0)
				{
					$orderby = current($orderby);
					$sql .= " order by ";
					$once=1;
					foreach($orderby as $k =>$v)
					{
						$sql .= ($once) ?  " $k $v" : " , $k  $v ";	
						$once=0;
					}	
				}
			}
		}
		//echo $sql;exit;
		$query = $this->db->query($sql);
//		echo $this->db->last_query();
		return $query; 
    }

	function commonQuery($sql)
    {
		$query = $this->db->query($sql);	
	    return $query; 
    }

    function add_question()
    {
		$user_id =1;
       $data = array(
	   'subject_id' => $_POST['subject'],'quest_title' => $_POST['question'],'quest_date' => time(),  
	   'quest_status' => 'Y','visibility' => $_POST['visibility'],'quest_author' => $user_id  );
		$this->db->insert('questions', $data); 
		
		$quest_id=$this->db->insert_id();
		
		$data = array( 'quest_id' => $quest_id,  'ans_desc' => $_POST['answer1'], 'ans_status' => 'Y', 'is_correct' => 'Y' );
		$this->db->insert('answers', $data); 
		
		$data = array( 'quest_id' => $quest_id,  'ans_desc' => $_POST['answer2'], 'ans_status' => 'Y', 'is_correct' => 'N' );
		$this->db->insert('answers', $data); 
		
		$data = array( 'quest_id' => $quest_id,  'ans_desc' => $_POST['answer3'], 'ans_status' => 'Y', 'is_correct' => 'N' );
		$this->db->insert('answers', $data); 
		
		$data = array( 'quest_id' => $quest_id,  'ans_desc' => $_POST['answer4'], 'ans_status' => 'Y', 'is_correct' => 'N' );
		$this->db->insert('answers', $data); 
		
    }

	function commonInsert($tbl,$data)
    {
        //$data = array(  'subject_id' => $_POST['subject'],'quest_title' => $_POST['question']);
		$this->db->insert($tbl, $data); 
		return $this->db->insert_id();
    }
	function commonUpdate($tbl,$data,$fld,$id)
    {
        //$data = array(  'subject_id' => $_POST['subject'],'quest_title' => $_POST['question']);
		$this->db->where($fld, $id);
		$this->db->update($tbl, $data); 
		return $this->db->affected_rows();
    }

	function commonDelete($tbl,$rowid,$fld)
	{
		//$data = array('quest_status' => $task);
		//echo $fld;	
		$this->db->where_in($fld , $rowid);
		$this->db->delete($tbl);
		return $this->db->affected_rows();
	}
	function commonbulkstatus($tbl,$rowid,$fld,$status)
	{
		$data = array('status' => $status);
		$this->db->where_in($fld , $rowid);
		$this->db->update($tbl, $data );
		return $this->db->affected_rows();
	}

	

	function show_all_questions($whr = NULL,$pager = false)
    {
		$sql = "select qs.*,cat.cat_name as subject ,users.user_name author	from questions qs
				inner join category cat on cat.cat_id=qs.subject_id
				inner join users on users.user_id = qs.quest_author	";
		if($whr != NULL)
		{ 
			if(count($whr) > 0)
			{
				$sql .= " where ";
				$once=1;
				foreach($whr as $k =>$v)
				{
					$sql .= ($once) ?  " $k = '$v'" : " and $k = '$v'";	
					$once=0;
				}	
			}
		}
		$query = $this->db->query($sql);
//		echo $this->db->last_query();
		return $query; 
    }

	function show_quest_subjects()
    {
		$sql = "select cat.cat_id, cat.cat_name as subject from questions qs
				inner join category cat on cat.cat_id=qs.subject_id
				group by cat.cat_id ";
		$query = $this->db->query($sql);	
	    return $query; 
    }

	function show_quest_dates()
    {
		$sql = "select qs.quest_date,from_unixtime(qs.quest_date,'%d/%m/%Y') as newdate
				from questions qs
				inner join category cat on cat.cat_id=qs.subject_id
				group by newdate ";
		$query = $this->db->query($sql);	
	    return $query; 
    }

	function show_quest_authors()
    {
		$sql = "select qs.quest_author	,users.user_name author
				from questions qs
				inner join users on users.user_id = qs.quest_author
				group by qs.quest_author ";
		$query = $this->db->query($sql);	
	    return $query; 
    }
	function set_status($qid,$task)
	{
		$data = array('quest_status' => $task);
		$this->db->where('quest_id' , $qid);
		$this->db->update('questions', $data );
		return $this->db->affected_rows();
	}
	function set_bulkstatus($qid,$task)
	{
		$data = array('quest_status' => $task);
		$this->db->where_in('quest_id' , $qid);
		$this->db->update('questions', $data );
		return $this->db->affected_rows();
	}


}