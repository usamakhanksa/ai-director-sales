<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


$config['front_end_themes'] = array();
$config['front_end_themes']["twenty_twenty"] = array('name' => 'Twenty Twenty', 'theme_image' => 'screenshot.png');
//$config['front_end_themes']["mordern"] = array('name' => 'Modern Theme', 'theme_image' => 'screenshot.png');


$config['customization'] = array();

$twenty20 = array();


$twenty20[] = array(
	"element_selector" => ".form-search ",
	"element_title" => "Search Form",
	"element_name" => "search_form",
	"element_styles"	=> array(
		array(
			"text" => "Backgound color",
			"default" => "#7c1ebd", "style" => "background-color",
			"name" => "form-bg-color", "type" => "color_box"
		),
	),
);




$twenty20[] = array(
	"element_selector" => ".form-search label:not(.default-label)",
	"element_title" => "Form Label",
	"element_name" => "search_form_label",
	"element_styles"	=> array(
		array(
			"text" => "Color",
			"default" => "#f1c202", "style" => "color",
			"name" => "form-search-label-color", "type" => "color_box"
		),
	),
);




$twenty20[] = array(
	"element_selector" => ".form-search .btn-success",
	"element_title" => "Form Submit Button",
	"element_name" => "search_form_submit",
	"element_styles"	=> array(
		array(
			"text" => "Text color",
			"default" => "#ffffff", "style" => "color",
			"name" => "form-submit-color", "type" => "color_box"
		),
		array(
			"text" => "Backgound color",
			"default" => "#7c1ebd", "style" => "background-color",
			"name" => "form-submit-bg-color", "type" => "color_box"
		),
		array(
			"text" => "Border color",
			"default" => "#7c1ebd", "style" => "border-color",
			"name" => "form-submit-border-color", "type" => "color_box"
		),

	),
);


$config['customization']['twenty20'] = $twenty20;
