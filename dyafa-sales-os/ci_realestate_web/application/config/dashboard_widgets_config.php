<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI = &get_instance();


$config['dashboard_widgets'] = array();




$config['dashboard_widgets']['admin']["total_properties"] = array(
	'widget_title' => 'Total Properties', 'widget_key' => 'total_properties', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_admin_total_properties', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_properties'
);


/*
$config['dashboard_widgets']['admin']['widget_sections']["enquiries"] = array(
	'widget_title' => 'Total Enquiries', 'widget_key' => 'total_enquiries',
	'widget_class' => '', 'widget_icon' => '',
	'widget_callback' => 'dashboard_widget_total_enquiries',
	'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_enquiries'
);

$config['dashboard_widgets']['agent']['widget_sections']["enquiries"] = array(
	'widget_title' => 'Total Enquiries', 'widget_key' => 'total_enquiries',
	'widget_class' => '', 'widget_icon' => '',
	'widget_callback' => 'agent_widget_total_enquiries',
	'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_enquiries'
);
*/

$config['dashboard_widgets']['admin']["total_agents"] = array(
	'widget_title' => 'Total Agents', 'widget_key' => 'total_agents', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_total_agents', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_agents'
);

$config['dashboard_widgets']['admin']["total_owners"] = array(
	'widget_title' => 'Total Owners', 'widget_key' => 'total_owners', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_total_owners', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_owners'
);

$config['dashboard_widgets']['admin']["total_builders"] = array(
	'widget_title' => 'Total Builders', 'widget_key' => 'total_builders', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_total_builders', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_builders'
);

$config['dashboard_widgets']['admin']["total_landlords"] = array(
	'widget_title' => 'Total Landlords', 'widget_key' => 'total_landlords', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_total_landlords', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_landlords'
);
/*
$config['dashboard_widgets']['admin']["enquiries"] = array(
	'widget_title' => 'Total Enquiries', 'widget_key' => 'total_enquiries',
	'widget_class' => '', 'widget_icon' => '',
	'widget_callback' => 'dashboard_widget_total_enquiries',
	'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_enquiries'
);
*/
$config['dashboard_widgets']['admin']["total_blogs"] = array(
	'widget_title' => 'Total Blogs', 'widget_key' => 'total_blogs', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_total_blogs', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/admin_total_blogs'
);



$config['dashboard_widgets']['agent']["my_total_properties"] = array(
	'widget_title' => 'Total Properties', 'widget_key' => 'my_total_properties', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_user_total_properties', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/user_total_properties'
);


$config['dashboard_widgets']['owner']["my_total_properties"] = array(
	'widget_title' => 'Total Properties', 'widget_key' => 'my_total_properties', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_user_total_properties', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/user_total_properties'
);


$config['dashboard_widgets']['landlord']["my_total_properties"] = array(
	'widget_title' => 'Total Properties', 'widget_key' => 'my_total_properties', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_user_total_properties', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/user_total_properties'
);


$config['dashboard_widgets']['builder']["my_total_properties"] = array(
	'widget_title' => 'Total Properties', 'widget_key' => 'my_total_properties', 'widget_class' => '', 'widget_icon' => '', 'widget_callback' => 'dashboard_widget_user_total_properties', 'widget_path' => CMS_DASHBOARD_WIDGETS . '/user_total_properties'
);
														
														



/*$config['slider_section_fields'] = array();

$config['slider_section_fields'][]  = array(
	'id' => 'slider_show_nav', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

*/