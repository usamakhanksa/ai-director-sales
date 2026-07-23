<?php




add_filter("cms_get_details", "cms_get_details_callback");
function cms_get_details_callback($default = "" ,$detail_key =''  ){

	//echo $detail_key . $default;
	$detail = get_option($detail_key);
	return $detail;
}



add_filter("cms_search_properties_extend_where", "cms_search_properties_extend_where_callback");
function cms_search_properties_extend_where_callback($sql ='' ){

	return $sql;
}
/*


add_filter("cms_recent_properties_extend_sql", "cms_recent_properties_extend_sql_callback");
function cms_recent_properties_extend_sql_callback($sql ='' ){

	return $sql;
}

add_filter("cms_recent_properties_extend_where", "cms_recent_properties_extend_where_callback");
function cms_recent_properties_extend_where_callback($sql ='' ){

	return $sql;
}

add_filter("cms_recent_properties_extend_order_by", "cms_recent_properties_extend_order_by_callback");
function cms_recent_properties_extend_order_by_callback($sql ='' ){

	return $sql;
}


add_filter("cms_featured_properties_extend_sql", "cms_featured_properties_extend_sql_callback");
function cms_featured_properties_extend_sql_callback($sql ='' ){

	return $sql;
}

add_filter("cms_featured_properties_extend_where", "cms_featured_properties_extend_where_callback");
function cms_featured_properties_extend_where_callback($sql ='' ){

	return $sql;
}

add_filter("cms_featured_properties_extend_order_by", "cms_featured_properties_extend_order_by_callback");
function cms_featured_properties_extend_order_by_callback($sql ='' ){

	return $sql;
}
*/
