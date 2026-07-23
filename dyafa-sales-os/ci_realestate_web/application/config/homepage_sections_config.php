<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI = &get_instance();


$config['homepage_contents'] = array();

$config['homepage_contents']["slider_section"] = array('title' => 'Slider'
														,'section_key' => 'slider_section'
														,'section_class' => ' fixed-section '
														,'section_type' => 'fixed');

$config['slider_section_fields'] = array();





$config['slider_section_fields'][]  = array(
	'id' => 'slider_show_nav', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['slider_section_fields'][]  = array(
	'id' => 'slider_show_nav_dots', 'name' => 'show_nav_dots', 'title' => 'Show Navigation Dots?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['slider_section_fields'][]  = array(
	'id' => 'slider_auto_start_slider', 'name' => 'auto_start_slider', 'title' => 'Auto Start',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'slider_block_related'),
	'options' => 'yes_no_options'
);

$config['slider_section_fields'][]  = array(
	'id' => 'slider_interval', 'name' => 'slider_interval', 'title' => 'Interval',
	'parent_class' => 'slider_block_related yes_block',
	'type' => 'number-field',	'required' => 'required', 'default' => '3000', 'min' => '0', 'step' => '500', 'max' => '10000'
);



/***	banner slider *****/


$config['homepage_contents']["banner_slider_section"] = array('title' => 'Banner Slider'
														,'section_key' => 'banner_slider_section'
														,'section_class' => ' no-fixed-section '
														,'section_type' => 'fixed');

$config['banner_slider_section_fields'] = array();





$config['banner_slider_section_fields'][]  = array(
	'id' => 'slider_show_nav', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['banner_slider_section_fields'][]  = array(
	'id' => 'slider_show_nav_dots', 'name' => 'show_nav_dots', 'title' => 'Show Navigation Dots?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['banner_slider_section_fields'][]  = array(
	'id' => 'slider_auto_start_slider', 'name' => 'auto_start_slider', 'title' => 'Auto Start',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'slider_block_related'),
	'options' => 'yes_no_options'
);

$config['banner_slider_section_fields'][]  = array(
	'id' => 'slider_interval', 'name' => 'slider_interval', 'title' => 'Interval',
	'parent_class' => 'slider_block_related yes_block',
	'type' => 'number-field',	'required' => 'required', 'default' => '3000', 'min' => '0', 'step' => '500', 'max' => '10000'
);








/* Search Block */

$config['homepage_contents']["search_section"] = array('title' => 'Search'
														,'section_key' => 'search_section'
														,'section_class' => ' fixed-section '
														,'section_type' => 'fixed');

$config['search_section_fields'] = array();

$config['search_section_fields'][]  = array(
	'id' => 'search_show_advance_search', 'name' => 'show_advance_search',
	'title' => 'Enable Advanced Search?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'options' => 'yes_no_options'
);



/* Recent Property Section */

$config['homepage_contents']["recent_property_section"] = array('title' => 'Recent Properties'
																,'section_key' => 'recent_property_section'
																,'section_type' => 'fixed');

$config['recent_property_section_fields'] = array();


$config['recent_property_section_fields'][]  = array(
	'id' => 'recent_property_heading', 'name' => 'heading', 'title' => 'Heading',

	'type' => 'text-field',	'required' => 'required', 'default' => 'Recent Properties',
);

$config['recent_property_section_fields'][]  = array(
	'id' => 'recent_property_sub_heading', 'name' => 'sub_heading', 'title' => 'Sub Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);


$config['recent_property_section_fields'][]  = array('type' => 'hr-field');


$config['recent_property_section_fields'][]  = array(
	'id' => 'recent_property_show_as', 'name' => 'show_as', 'title' => 'Show as',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'grid',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'recent_property_grid_block_related'),
	'options' => array('grid' => 'Grid', 'carousel' => 'Carousel')
);

$config['recent_property_section_fields'][]  = array(
	'id' => 'no_of_recent_property_in_grid_list', 'name' => 'no_of_item_in_grid_list', 'title' => 'No. of Property',
	'parent_class' => 'recent_property_grid_block_related grid_block',
	'type' => 'number-field',	'required' => '', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['recent_property_section_fields'][]  = array(
	'id' => 'no_of_recent_property_in_carousel', 'name' => 'no_of_item_in_carousel',
	'title' => 'No. of Property',
	'parent_class' => 'recent_property_grid_block_related carousel_block',
	'type' => 'number-field',	'required' => 'required', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['recent_property_section_fields'][]  = array(
	'id' => 'show_nav_in_recent_property_carousel', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'parent_class' => 'recent_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['recent_property_section_fields'][]  = array(
	'id' => 'show_nav_dots_in_recent_property_carousel', 'name' => 'show_nav_dots', 'title' => 'Show Navigation Dots?',
	'parent_class' => 'recent_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['recent_property_section_fields'][]  = array(
	'id' => 'auto_start_in_recent_property_carousel', 'name' => 'auto_start', 'title' => 'Auto Start',
	'parent_class' => 'recent_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'recent_property_slider_block_related'),
	'options' => 'yes_no_options'
);

$config['recent_property_section_fields'][]  = array(
	'id' => 'recent_property_carousel_interval', 'name' => 'carousel_interval', 'title' => 'Interval',
	'parent_class' => 'recent_property_slider_block_related yes_block recent_property_grid_block_related carousel_block',
	'type' => 'number-field',	'required' => 'required', 'default' => '5000', 'min' => '0', 'step' => '500', 'max' => '10000'
);


$config['recent_property_section_fields'][]  = array(
	'id' => 'show_view_more_btn_in_recent_property', 'name' => 'show_view_more', 'title' => 'Show View More Button ?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

/* Property by Locations Section */

$config['homepage_contents']["property_type_section"] = array('title' => 'Property Types'
																,'section_key' => 'property_type_section'
																,'section_type' => 'fixed');

$config['property_type_section_fields'] = array();


$config['property_type_section_fields'][]  = array(
	'id' => 'property_type_heading', 'name' => 'heading', 'title' => 'Heading',

	'type' => 'text-field',	'required' => 'required', 'default' => 'Looking for Property',
);

$config['property_type_section_fields'][]  = array(
	'id' => 'property_type_sub_heading', 'name' => 'sub_heading', 'title' => 'Sub Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);


$config['property_type_section_fields'][]  = array('type' => 'hr-field');

$config['property_type_section_fields'][]  = array(
	'id' => 'no_of_property_type_in_carousel', 'name' => 'no_of_item_in_carousel', 'title' => 'No. of Property Type in Carousel',
	'type' => 'number-field',	'required' => 'required', 'default' => '3', 'min' => '3', 'step' => '1', 'max' => '5'
);


$config['property_type_section_fields'][]  = array(
	'id' => 'show_nav_in_property_type_carousel', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['property_type_section_fields'][]  = array(
	'id' => 'show_nav_dots_in_property_type_carousel', 'name' => 'show_nav_dots', 'title' => 'Show Navigation Dots?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['property_type_section_fields'][]  = array(
	'id' => 'auto_start_in_property_type_carousel', 'name' => 'auto_start', 'title' => 'Auto Start',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'property_type_slider_block_related'),
	'options' => 'yes_no_options'
);

$config['property_type_section_fields'][]  = array(
	'id' => 'property_type_slider_block_related_carousel_interval', 'name' => 'carousel_interval', 'title' => 'Interval',
	'parent_class' => 'property_type_slider_block_related yes_block ',
	'type' => 'number-field',	'required' => 'required', 'default' => '5000', 'min' => '0', 'step' => '500', 'max' => '10000'
);


/* Featured Property Section */

$config['homepage_contents']["featured_property_section"] = array('title' => 'Featured Properties'
																	,'section_key' => 'featured_property_section'
																	,'section_type' => 'fixed');

$config['featured_property_section_fields'] = array();


$config['featured_property_section_fields'][]  = array(
	'id' => 'featured_property_heading', 'name' => 'heading', 'title' => 'Heading',

	'type' => 'text-field',	'required' => 'required', 'default' => 'Featured Property',
);

$config['featured_property_section_fields'][]  = array(
	'id' => 'featured_property_sub_heading', 'name' => 'sub_heading', 'title' => 'Sub Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);

$config['featured_property_section_fields'][]  = array('type' => 'hr-field');


$config['featured_property_section_fields'][]  = array(
	'id' => 'featured_property_show_as', 'name' => 'show_as', 'title' => 'Show as',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'grid',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'featured_property_grid_block_related'),
	'options' => array('grid' => 'Grid', 'carousel' => 'Carousel')
);

$config['featured_property_section_fields'][]  = array(
	'id' => 'no_of_featured_property_in_grid_list', 'name' => 'no_of_item_in_grid_list', 'title' => 'No. of Property',
	'parent_class' => 'featured_property_grid_block_related grid_block',
	'type' => 'number-field',	'required' => '', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['featured_property_section_fields'][]  = array(
	'id' => 'no_of_featured_property_in_carousel', 'name' => 'no_of_item_in_carousel',
	'title' => 'No. of Property',
	'parent_class' => 'featured_property_grid_block_related carousel_block',
	'type' => 'number-field',	'required' => 'required', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['featured_property_section_fields'][]  = array(
	'id' => 'show_nav_in_featured_property_carousel', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'parent_class' => 'featured_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['featured_property_section_fields'][]  = array(
	'id' => 'show_nav_dots_in_featured_property_carousel', 'name' => 'show_nav_dots', 'title' => 'Show Navigation Dots?',
	'parent_class' => 'featured_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['featured_property_section_fields'][]  = array(
	'id' => 'auto_start_in_featured_property_carousel', 'name' => 'auto_start', 'title' => 'Auto Start',
	'parent_class' => 'featured_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'featured_property_slider_block_related'),
	'options' => 'yes_no_options'
);

$config['featured_property_section_fields'][]  = array(
	'id' => 'featured_property_carousel_interval', 'name' => 'carousel_interval', 'title' => 'Interval',
	'parent_class' => 'featured_property_slider_block_related yes_block featured_property_grid_block_related ',
	'type' => 'number-field',	'required' => 'required', 'default' => '5000', 'min' => '0', 'step' => '500', 'max' => '10000'
);


$config['featured_property_section_fields'][]  = array(
	'id' => 'show_view_more_btn_in_featured_property', 'name' => 'show_view_more', 'title' => 'Show View More Button ?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

/* Recent Viewed Section */

$config['homepage_contents']["recent_viewed_property_section"] = array('title' => 'Recent Viewed Properties'
																	,'section_key' => 'recent_viewed_property_section'
																	,'section_type' => 'fixed');

$config['recent_viewed_property_section_fields'] = array();


$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'recent_viewed_property_heading', 'name' => 'heading', 'title' => 'Heading',

	'type' => 'text-field',	'required' => 'required', 'default' => 'Recent Viewed Property',
);

$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'recent_viewed_property_sub_heading', 'name' => 'sub_heading', 'title' => 'Sub Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);

$config['recent_viewed_property_section_fields'][]  = array('type' => 'hr-field');


$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'recent_viewed_property_show_as', 'name' => 'show_as', 'title' => 'Show as',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'grid',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'recent_viewed_property_grid_block_related'),
	'options' => array('grid' => 'Grid', 'carousel' => 'Carousel')
);

$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'no_of_recent_viewed_property_in_grid_list', 'name' => 'no_of_item_in_grid_list', 'title' => 'No. of Property',
	'parent_class' => 'recent_viewed_property_grid_block_related grid_block',
	'type' => 'number-field',	'required' => '', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'no_of_recent_viewed_property_in_carousel', 'name' => 'no_of_item_in_carousel',
	'title' => 'No. of Property',
	'parent_class' => 'recent_viewed_property_grid_block_related carousel_block',
	'type' => 'number-field',	'required' => 'required', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'show_nav_in_recent_viewed_property_carousel', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'parent_class' => 'recent_viewed_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'show_nav_dots_in_recent_viewed_property_carousel', 'name' => 'show_nav_dots', 'title' => 'Show Navigation Dots?',
	'parent_class' => 'recent_viewed_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'auto_start_in_recent_viewed_property_carousel', 'name' => 'auto_start', 'title' => 'Auto Start',
	'parent_class' => 'recent_viewed_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'recent_viewed_property_slider_block_related'),
	'options' => 'yes_no_options'
);

$config['recent_viewed_property_section_fields'][]  = array(
	'id' => 'recent_viewed_property_carousel_interval', 'name' => 'carousel_interval', 'title' => 'Interval',
	'parent_class' => 'recent_viewed_property_slider_block_related yes_block recent_viewed_property_grid_block_related ',
	'type' => 'number-field',	'required' => 'required', 'default' => '5000', 'min' => '0', 'step' => '500', 'max' => '10000'
);


/* Latest Blog Section */

$config['homepage_contents']["recent_blog_section"] = array('title' => 'Recent Blogs'
															,'section_key' => 'recent_blog_section'
															,'section_type' => 'fixed');

$config['recent_blog_section_fields'] = array();


$config['recent_blog_section_fields'][]  = array(
	'id' => 'recent_blog_heading', 'name' => 'heading', 'title' => 'Heading',

	'type' => 'text-field',	'required' => 'required', 'default' => 'Recent Blog',
);

$config['recent_blog_section_fields'][]  = array(
	'id' => 'recent_blog_sub_heading', 'name' => 'sub_heading', 'title' => 'Sub Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);

$config['recent_blog_section_fields'][]  = array('type' => 'hr-field');


$config['recent_blog_section_fields'][]  = array(
	'id' => 'recent_blog_show_as', 'name' => 'show_as', 'title' => 'Show as',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'grid',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'recent_blog_grid_block_related'),
	'options' => array('grid' => 'Grid', 'carousel' => 'Carousel')
);

$config['recent_blog_section_fields'][]  = array(
	'id' => 'no_of_recent_blog_in_grid_list', 'name' => 'no_of_item_in_grid_list',
	'title' => 'No. of Blogs',
	'parent_class' => 'recent_blog_grid_block_related grid_block',
	'type' => 'number-field',	'required' => '', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['recent_blog_section_fields'][]  = array(
	'id' => 'no_of_recent_blog_in_carousel', 'name' => 'no_of_item_in_carousel',
	'title' => 'No. of Blogs',
	'parent_class' => 'recent_blog_grid_block_related carousel_block',
	'type' => 'number-field',	'required' => 'required', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['recent_blog_section_fields'][]  = array(
	'id' => 'show_nav_in_recent_blog_carousel', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'parent_class' => 'recent_blog_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['recent_blog_section_fields'][]  = array(
	'id' => 'show_nav_dots_in_recent_blog_carousel', 'name' => 'show_nav_dots', 'title' => 'Show Navigation Dots?',
	'parent_class' => 'recent_blog_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['recent_blog_section_fields'][]  = array(
	'id' => 'auto_start_in_recent_blog_carousel', 'name' => 'auto_start', 'title' => 'Auto Start',
	'parent_class' => 'recent_blog_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'recent_blog_slider_block_related'),
	'options' => 'yes_no_options'
);

$config['recent_blog_section_fields'][]  = array(
	'id' => 'recent_blog_carousel_interval', 'name' => 'carousel_interval', 'title' => 'Interval',
	'parent_class' => 'recent_blog_slider_block_related yes_block recent_blog_grid_block_related ',
	'type' => 'number-field',	'required' => 'required', 'default' => '5000', 'min' => '0', 'step' => '500', 'max' => '10000'
);


$config['recent_blog_section_fields'][]  = array(
	'id' => 'show_view_more_btn_in_recent_blog', 'name' => 'show_view_more',
	'title' => 'Show View More Button ?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);


/* Video Section */

$config['homepage_contents']["video_section"] = array('title' => 'Videos (Youtube/Vimeo/Facebook/Self Hosted)'
														, 'section_type' => 'dynamic'	
														, 'section_title' => 'Video Section'	
														,'section_key' => 'video_section');

$config['video_section_fields'] = array();

$config['video_section_fields'][]  = array(
	'id' => 'video_section_heading', 'name' => 'heading', 'title' => 'Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);

$config['video_section_fields'][]  = array(
	'id' => 'video_section_sub_heading', 'name' => 'sub_heading', 'title' => 'Sub Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);

$config['video_section_fields'][]  = array(
	'id' => 'video_section_for_lang', 'name' => 'video_lang', 
	'element' => 'site_language',
	'title' => 'Video for Languages',
	'type' => 'radio-toggle',	'required' => '', 'default' => '',
	'class' => 'video_for_lang_opt',
	'options' => 'callback',
);

$config['video_section_fields'][]  = array('type' => 'hr-field');


$config['video_section_fields'][]  = array(
	'id' => 'video_url', 'name' => 'video_url', 'title' => 'Video URL',
	'type' => 'video-url',	'required' => '', 'default' => '',
);

/* Dynamic Property Section */

$config['homepage_contents']["properties_section"] = array('title' => 'Properties Section'
															,'section_type' => 'dynamic' 
															, 'section_title' => 'Properties Section'	
															,'section_key' => 'properties_section');

$config['properties_section_fields'] = array();


$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_property_heading', 'name' => 'heading', 'title' => 'Heading',

	'type' => 'text-field',	'required' => 'required', 'default' => '',
);

$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_property_sub_heading', 'name' => 'sub_heading', 'title' => 'Sub Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);

$config['properties_section_fields'][]  = array('type' => 'hr-field');


$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_property_for', 'name' => 'property_for', 'title' => 'Property For',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'sale',
	'class' => '',
	'options' => array('sale' => 'Sale', 'rent' => 'Rent')
);

$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_property_type', 'name' => 'property_type', 'title' => 'Property Type',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => '',
	'class' => '',
	'options' => 'callback',
);

$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_property_for_lang', 'name' => 'property_for_lang', 
	'element' => 'site_language',
	'title' => 'Property for Languages',
	'type' => 'radio-toggle',	'required' => '', 'default' => '',
	'class' => 'dynamic_property_for_lang_opt',
	'options' => 'callback',
);

$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_location_country', 'name' => 'property_country', 'title' => 'Country',
	'type' => 'select',	'required' => '', 'default' => '',
	'class' => 'dynamic_property_for_lang_country parent_hidden_elems',
	'options' => 'callback',
);

$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_location_state', 'name' => 'property_state', 'title' => 'State',
	'type' => 'select',	'required' => '', 'default' => '',
	'class' => 'dynamic_property_for_lang_state parent_hidden_elem',
	'parent_classXXX' => 'dynamic_property_for_lang_country ',
	'options' => 'callback',
);

$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_location_city', 'name' => 'property_city', 'title' => 'City',
	'type' => 'select',	'required' => '', 'default' => '',
	'class' => 'dynamic_property_for_lang_city parent_hidden_elem',
	'options' => 'callback',
);


$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_location_zipcode', 'name' => 'property_zipcode', 'title' => 'Zipcode',
	'type' => 'select',	'required' => '', 'default' => '',
	'class' => 'dynamic_property_for_lang_zipcode parent_hidden_elem',
	'options' => 'callback',
);

$config['properties_section_fields'][]  = array(
	'id' => 'dynamic_location_sub_area', 'name' => 'property_sub_area', 'title' => 'Sub Area',
	'type' => 'select',	'required' => '', 'default' => '',
	'class' => 'dynamic_property_for_lang_sub_area parent_hidden_elem',
	'options' => 'callback',
);


$config['properties_section_fields'][]  = array('type' => 'hr-field');


$config['properties_section_fields'][]  = array(
	'id' => 'featured_property_show_as', 'name' => 'show_as', 'title' => 'Show as',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'grid',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'featured_property_grid_block_related'),
	'options' => array('grid' => 'Grid', 'carousel' => 'Carousel')
);

$config['properties_section_fields'][]  = array(
	'id' => 'no_of_featured_property_in_grid_list', 'name' => 'no_of_item_in_grid_list', 'title' => 'No. of Property',
	'parent_class' => 'featured_property_grid_block_related grid_block',
	'type' => 'number-field',	'required' => '', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['properties_section_fields'][]  = array(
	'id' => 'no_of_featured_property_in_carousel', 'name' => 'no_of_item_in_carousel',
	'title' => 'No. of Property',
	'parent_class' => 'featured_property_grid_block_related carousel_block',
	'type' => 'number-field',	'required' => 'required', 'default' => '6', 'min' => '0', 'step' => '1', 'max' => 12
);

$config['properties_section_fields'][]  = array(
	'id' => 'show_nav_in_featured_property_carousel', 'name' => 'show_nav', 'title' => 'Show Navigation Icon?',
	'parent_class' => 'featured_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['properties_section_fields'][]  = array(
	'id' => 'show_nav_dots_in_featured_property_carousel', 'name' => 'show_nav_dots', 'title' => 'Show Navigation Dots?',
	'parent_class' => 'featured_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

$config['properties_section_fields'][]  = array(
	'id' => 'auto_start_in_featured_property_carousel', 'name' => 'auto_start', 'title' => 'Auto Start',
	'parent_class' => 'featured_property_grid_block_related carousel_block',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'no',
	'class' => 'show_hide_block_btn',
	'attributes' => array('data-target' => 'featured_property_slider_block_related'),
	'options' => 'yes_no_options'
);

$config['properties_section_fields'][]  = array(
	'id' => 'featured_property_carousel_interval', 'name' => 'carousel_interval', 'title' => 'Interval',
	'parent_class' => 'featured_property_slider_block_related yes_block featured_property_grid_block_related ',
	'type' => 'number-field',	'required' => 'required', 'default' => '5000', 'min' => '0', 'step' => '500', 'max' => '10000'
);


$config['properties_section_fields'][]  = array(
	'id' => 'show_view_more_btn_in_featured_property', 'name' => 'show_view_more', 'title' => 'Show View More Button ?',
	'type' => 'radio-toggle',	'required' => 'required', 'default' => 'yes',
	'options' => 'yes_no_options'
);

/* Recent Property Section */

$config['homepage_contents']["top_links_section"] = array('title' => 'Top Links Section'
																,'section_key' => 'top_links_section'
																,'section_type' => 'fixed');

$config['top_links_section_fields'] = array();


$config['top_links_section_fields'][]  = array(
	'id' => 'top_links_section_heading', 'name' => 'heading', 'title' => 'Heading',

	'type' => 'text-field',	'required' => 'required', 'default' => 'Browse Top Links',
);

$config['top_links_section_fields'][]  = array(
	'id' => 'top_links_section_sub_heading', 'name' => 'sub_heading', 'title' => 'Sub Heading',

	'type' => 'text-field',	'required' => '', 'default' => '',
);


/*
$config ['homepage_contents'] ["dyn_features_section"] = array('title' => 'Features Section','section_key' => 'dyn_features_section'
															  ,'section_type' => 'fixed'  );  

		$config ['dyn_features_section_fields'] = array();  
	 
		$config ['dyn_features_section_fields'] []  = array( 	
													'id'=> 'features_heading', 
													'name' => 'heading', 
													'title' => 'Heading', 
													'type' => 'text-field',	
													'required' => '', 
													'default' => '', );	
		
		$config ['dyn_features_section_fields'] []  = array( 	
													'id'=> 'features_sub_heading', 
													'name' => 'sub_heading', 
													'title' => 'Sub Heading', 
													'type' => 'text-field',	
													'required' => '', 
													'default' => '', );	
		
		$config ['dyn_features_section_fields'] []  = array( 	'id'=> 'dyna_feature_section_link', 
																'name' => 'section_link', 
																'title' => 'Section Link', 
												
													'type' => 'hidden',	'required' => '', 'default' => '#feature', );*/
													
	
		$config ['homepage_contents'] ["dyn_pricing_tables_section"] = array('title' => 'Pricing Table Section',
																			'section_key' => 'dyn_pricing_tables_section'
																			,'section_type' => 'fixed'  );   

		$config ['dyn_pricing_tables_section_fields'] = array();  
	 
		$config ['dyn_pricing_tables_section_fields'] []  = array( 	
													'id'=> 'pricing_tables_heading', 
													'name' => 'heading', 
													'title' => 'Heading', 
													'type' => 'text-field',	
													'required' => '', 
													'default' => '', );	
		
		$config ['dyn_pricing_tables_section_fields'] []  = array( 	
													'id'=> 'pricing_tables_sub_heading', 
													'name' => 'sub_heading', 
													'title' => 'Sub Heading', 
													'type' => 'text-field',	
													'required' => '', 
													'default' => '', );		
		
		$config ['dyn_pricing_tables_section_fields'] []  = array( 	'id'=> 'dyna_pricing_table_section_link', 'name' => 'section_link', 'title' => 'Section Link', 
												
												'type' => 'hidden',	'required' => '', 'default' => '#pricing_table', );
		
		$config ['dyn_pricing_tables_section_fields'] []  = array( 	'id'=> 'no_of_plan_in_grid', 'name' => 'no_of_plan_in_grid', 'title' => 'No. of Plan to Show', 
											'type' => 'number-field',	'required' => 'required', 'default' => '3', 'min' => '1', 'step' => '1', 'max' => '4');
												
	
	

	

