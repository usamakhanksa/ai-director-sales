<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pagination_lib {

	public function get_pagination_links($args = array()){
		
		$CI =& get_instance();
		$CI->load->library('global_lib');
		
		
		
		$CI->load->library('pagination');
		
		$config['base_url'] = base_url(uri_string());
		$config['total_rows'] = $total_rows = $args['total_rows'];
		$config['per_page'] = $per_page = $args['per_page'];
		$config['num_links'] = 4;
		$config['enable_query_strings'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';
		$config['reuse_query_string'] = TRUE;
		
		$config['full_tag_open'] = "<ul class='pagination'>";
		$config['full_tag_close'] = '</ul>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a >';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['prev_link'] = '<i class="fa fa-chevron-left"></i>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		$config['next_link'] = '<i class="fa fa-chevron-right"></i>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$config['use_page_numbers'] = true;
		
		$start = 0;
		$limit = $per_page;
		
		$num_pages = ceil($total_rows / $per_page);
		if(isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $num_pages)
		{
			$start = (($_GET['page'] - 1) * $limit);
		}
		else if(isset($_GET['page']) && $_GET['page'] > $num_pages)
		{
			$start = (($num_pages - 1) * $limit);
		}
		
		$CI->pagination->initialize($config);
		$pagination_links = $CI->pagination->create_links();
		$output = array('start' => $start, 'limit' => $limit, 'pagination_links' => $pagination_links);
		
		return $output;
		
	}	
	
}

