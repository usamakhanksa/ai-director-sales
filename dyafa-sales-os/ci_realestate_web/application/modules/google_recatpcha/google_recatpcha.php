<?php
/*
Plugin Name: google captcha
Plugin URI: http://www.google.com
Version: 0.1
Description: Google captcha to Prevent the Boats
Author: Mindlogixtech
Author URI: http://www.facebook.com/mindlogixtech
*/

define("GOOGLE_RECATPCHA_DIR", "google_recatpcha");
define("GOOGLE_RECATPCHA_ASSETS_PATH", "application/modules/" . GOOGLE_RECATPCHA_DIR . "/assets/");
define("GOOGLE_RECATPCHA_PLUGIN_NAME", "google recatpcha");


add_action('cms_init', 'google_recatpcha_init');

function google_recatpcha_init()
{

    $CI = &get_instance();
    /*check_dependency("customer_review', 'foodie_web');*/
    $CI->load->config(GOOGLE_RECATPCHA_DIR . "/google_recatpcha_config");
    //$CI->load->library(GOOGLE_RECATPCHA_DIR . '/Google_recatpcha_lib');
}
