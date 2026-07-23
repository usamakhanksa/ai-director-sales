<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI = &get_instance();


$config['site_widgets'] = array();




$config['site_widgets']['property_sidebar']["agency_details"] = array(
				'widget_title' => 'Agency Details', 
				'widget_key' => 'agency_details', 
				'widget_class' => '', 
				'widget_icon' => '', 
				'widget_callback' => 'property_agent_details_widget', 
				'widget_path' =>  'property_agent_details_widget'
);

$config['site_widgets']['property_sidebar']["social_share_details"] = array(
				'widget_title' => 'Social Share Details', 
				'widget_key' => 'social_share_details', 
				'widget_class' => '', 
				'widget_icon' => '', 
				'widget_callback' => 'property_social_share_details_widget', 
				'widget_path' =>  'property_social_share_details_widget'
);


$config['site_widgets']['property_sidebar']["property_recent_viewed_details"] = array(
				'widget_title' => 'Property Recent Viewed Details', 
				'widget_key' => 'property_recent_viewed_details', 
				'widget_class' => '', 
				'widget_icon' => '', 
				'widget_callback' => 'property_recent_viewed', 
				'widget_path' =>  'property_recent_viewed'
);

$config['site_widgets']['property_sidebar']["property_agent_contact_form"] = array(
				'widget_title' => 'Property Agent Contact Form', 
				'widget_key' => 'property_agent_contact_form', 
				'widget_class' => '', 
				'widget_icon' => '', 
				'widget_callback' => 'property_agent_contact_form', 
				'widget_path' =>  'property_agent_contact_form'
);

$config['site_widgets']['property_sidebar']["mortgage_calculator"] = array(
				'widget_title' => 'Mortgage Calculator', 
				'widget_key' => 'mortgage_calculator', 
				'widget_class' => '', 
				'widget_icon' => '', 
				'widget_callback' => 'property_mortgage_calculator', 
				'widget_path' =>  'property_mortgage_calculator'
);

$config['site_widgets']['property_contents']["description"] = array(
	'widget_title' => 'Property Description', 
	'widget_key' => 'description', 
	'widget_class' => '', 
	'widget_icon' => '', 
	'widget_callback' => 'property-description', 
	'widget_path' =>  'property/template-part/property-description'
);

$config['site_widgets']['property_contents']["google_map"] = array(
	'widget_title' => 'Google Map', 
	'widget_key' => 'google_map', 
	'widget_class' => '', 
	'widget_icon' => '', 
	'widget_callback' => 'google_map', 
	'widget_path' =>  'property/template-part/property-google-map'
);

$config['site_widgets']['property_contents']["open_street_map"] = array(
	'widget_title' => 'Open Street Map', 
	'widget_key' => 'open_street_map', 
	'widget_class' => '', 
	'widget_icon' => '', 
	'widget_callback' => 'open_street_map', 
	'widget_path' =>  'property/template-part/property-open-street-map'
);

$config['site_widgets']['property_contents']["amenities"] = array(
	'widget_title' => 'Property Amenities', 
	'widget_key' => 'amenities', 
	'widget_class' => '', 
	'widget_icon' => '', 
	'widget_callback' => 'amenities', 
	'widget_path' =>  'property/template-part/property-amenities-part'
);

$config['site_widgets']['property_contents']["distances"] = array(
	'widget_title' => 'Property Distances', 
	'widget_key' => 'distances', 
	'widget_class' => '', 
	'widget_icon' => '', 
	'widget_callback' => 'distances', 
	'widget_path' =>  'property/template-part/property-distances-part'
);

$config['site_widgets']['property_contents']["videos"] = array(
	'widget_title' => 'Property Videos', 
	'widget_key' => 'videos', 
	'widget_class' => '', 
	'widget_icon' => '', 
	'widget_callback' => 'videos', 
	'widget_path' =>  'property/template-part/property-videos-part'
);

$config['site_widgets']['property_contents']["images"] = array(
	'widget_title' => 'Property Images', 
	'widget_key' => 'images', 
	'widget_class' => '', 
	'widget_icon' => '', 
	'widget_callback' => 'images', 
	'widget_path' =>  'property/template-part/property-images-part'
);