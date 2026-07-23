<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "Main";

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*print_r($_SERVER['REQUEST_URI']);*/


$uri = $_SERVER['REQUEST_URI'];
$admin = "/\/admin/";
$is_admin = (preg_match($admin, $uri)) ? true : false;

/* Modules Routes goes here */
/*$modules_path = APPPATH . 'modules/';
$plugin_json_list = file_get_contents($modules_path . 'modules.json');
$list = json_decode($plugin_json_list, true);
foreach ($list as $key => $value) {
    $file = $key . '.php';
    if (is_dir($modules_path . $key) && file_exists($modules_path . $key . '/' . $file)) {
        if (isset($value['status']) && $value['status'] == 'Y') {

            $modules = scandir($modules_path);
            foreach ($modules as $module) {
                if ($module === '.' || $module === '..') continue;
				
                if (is_dir($modules_path) . '/' . $module    && $key == $module) {
                    $routes_path = $modules_path . $module . '/config/routes.php';

                    if (file_exists($routes_path)) {
                        require($routes_path);
                    } else {
                        continue;
                    }
                }
            }
        }
    }
}*/

$route['admin_ajax'] = 'admin_ajax/index';

/* Dyafa Sales OS routes - must be placed before the legacy catch-all routes below. */
$route['dyafa'] = 'dyafa/dashboard';
$route['dyafa/login'] = 'dyafa/auth/login';
$route['dyafa/logout'] = 'dyafa/auth/logout';
$route['dyafa/portal'] = 'dyafa/portal/dashboard';
$route['dyafa/(:any)'] = 'dyafa/$1';
/* end Dyafa Sales OS routes */

if (!$is_admin) {


    $route['contact/(:any)'] = 'main/contact/$1';
    $route['contact'] = 'main/contact';

    $route['register/(:any)'] = 'main/register/$1';
    $route['register'] = 'main/register';

    $route['google_login/(:any)'] = 'main/google_login/$1';
    $route['google_login'] = 'main/google_login';

    $route['logout/(:any)'] = 'main/logout/$1';
    $route['logout'] = 'main/logout';


    $route['compare'] = 'compare';



    /**
$route['search/(:any)/property-type-([a-zA-Z0-9_-]+)/state-(:any)'] = 
			'main/search/$1/property-type-$2/state-$3';
			
$route['search/property-type-([a-zA-Z0-9_-]+)/state-(:any)'] = 
			'main/search/property-type-$1/state-$2';
     */


    $route['search/(:any)/property-for-sale/property-type-([a-zA-Z0-9_-]+)/state-(:any)/city-(:any)'] =
        'main/search/$1/property-for-sale/property-type-$2/state-$3/city-$4';

    $route['search/property-for-sale/property-type-([a-zA-Z0-9_-]+)/state-(:any)/city-(:any)'] =
        'main/search/property-for-sale/property-type-$1/state-$2/city-$3';

    $route['search/(:any)/property-for-rent/property-type-([a-zA-Z0-9_-]+)/state-(:any)/city-(:any)'] =
        'main/search/$1/property-for-rent/property-type-$2/state-$3/city-$4';

    $route['search/property-for-rent/property-type-([a-zA-Z0-9_-]+)/state-(:any)/city-(:any)'] =
        'main/search/property-for-rent/property-type-$1/state-$2/city-$3';


    $route['search/(:any)/property-for-sale/state-(:any)/city-(:any)'] =
        'main/search/$1/property-for-sale/state-$2/city-$3';

    $route['search/property-for-sale/state-(:any)/city-(:any)'] =
        'main/search/property-for-sale/state-$1/city-$2';

    $route['search/(:any)/property-for-rent/state-(:any)/city-(:any)'] =
        'main/search/$1/property-for-rent/state-$2/city-$3';

    $route['search/property-for-rent/state-(:any)/city-(:any)'] =
        'main/search/property-for-rent/state-$1/city-$2';

    /**/

    $route['search/(:any)/property-type-([a-zA-Z0-9_-]+)/state-(:any)/city-(:any)'] =
        'main/search/$1/property-type-$2/state-$3/city-$4';

    $route['search/property-type-([a-zA-Z0-9_-]+)/state-(:any)/city-(:any)'] =
        'main/search/property-type-$1/state-$2/city-$3';


    /**/

    $route['search/(:any)/property-for-sale/property-type-([a-zA-Z0-9_-]+)/city-(:any)'] = 'main/search/$1/property-for-sale/property-type-$2/city-$3';
    $route['search/property-for-sale/property-type-([a-zA-Z0-9_-]+)/city-(:any)'] = 'main/search/property-for-sale/property-type-$1/city-$2';

    $route['search/(:any)/property-for-rent/property-type-([a-zA-Z0-9_-]+)/city-(:any)'] = 'main/search/$1/property-for-rent/property-type-$2/city-$3';
    $route['search/property-for-rent/property-type-([a-zA-Z0-9_-]+)/city-(:any)'] = 'main/search/property-for-rent/property-type-$1/city-$2';

    $route['search/(:any)/property-for-sale/property-type-([a-zA-Z0-9_-]+)/state-(:any)'] = 'main/search/$1/property-for-sale/property-type-$2/state-$3';
    $route['search/property-for-sale/property-type-([a-zA-Z0-9_-]+)/state-(:any)'] = 'main/search/property-for-sale/property-type-$1/state-$2';

    $route['search/(:any)/property-for-rent/property-type-([a-zA-Z0-9_-]+)/state-(:any)'] = 'main/search/$1/property-for-rent/property-type-$2/state-$3';
    $route['search/property-for-rent/property-type-([a-zA-Z0-9_-]+)/state-(:any)'] = 'main/search/property-for-rent/property-type-$1/state-$2';



    $route['search/(:any)/property-for-sale/property-type-([a-zA-Z0-9_-]+)'] = 'main/search/$1/property-for-sale/property-type-$2';
    $route['search/property-for-sale/property-type-([a-zA-Z0-9_-]+)'] = 'main/search/property-for-sale/property-type-$1';

    $route['search/(:any)/property-for-rent/property-type-([a-zA-Z0-9_-]+)'] = 'main/search/$1/property-for-rent/property-type-$2';
    $route['search/property-for-rent/property-type-([a-zA-Z0-9_-]+)'] = 'main/search/property-for-rent/property-type-$1';


    $route['search/(:any)/property-for-sale/city-(:any)'] = 'main/search/$1/property-for-sale/city-$2';
    $route['search/property-for-sale/city-(:any)'] = 'main/search/property-for-sale/city-$1';

    $route['search/(:any)/property-for-rent/city-(:any)'] = 'main/search/$1/property-for-rent/city-$2';
    $route['search/property-for-rent/city-(:any)'] = 'main/search/property-for-rent/city-$1';

    $route['search/(:any)/property-for-sale/state-(:any)'] = 'main/search/$1/property-for-sale/state-$2';
    $route['search/property-for-sale/state-(:any)'] = 'main/search/property-for-sale/state-$1';

    $route['search/(:any)/property-for-rent/state-(:any)'] = 'main/search/$1/property-for-rent/state-$2';
    $route['search/property-for-rent/state-(:any)'] = 'main/search/property-for-rent/state-$1';

    $route['search/(:any)/property-type-([a-zA-Z0-9_-]+)/city-(:any)'] = 'main/search/$1/property-type-$2/city-$3';
    $route['search/property-type-([a-zA-Z0-9_-]+)/city-(:any)'] = 'main/search/property-type-$1/city-$2';

    $route['search/(:any)/property-type-([a-zA-Z0-9_-]+)/state-(:any)'] = 'main/search/$1/property-type-$2/state-$3';
    $route['search/property-type-([a-zA-Z0-9_-]+)/state-(:any)'] = 'main/search/property-type-$1/state-$2';


    /*$route['search/(:any)/state-([a-zA-Z0-9_-]+)/city-([a-zA-Z0-9_-]+)'] = 'main/search/$1/state-$2/city-$3';*/
    $route['search/(:any)/state-(:any)/city-(:any)'] = 'main/search/$1/state-$2/city-$3';

    /*$route['search/state-(:any)/city-(:any)'] = 'main/search/state-$1/city-$2';*/
    $route['search/state-(:any)/city-(:any)'] = 'main/search/state-$1/city-$2';

    $route['search/(:any)/property-for-sale'] = 'main/search/$1/property-for-sale';
    $route['search/property-for-sale'] = 'main/search/property-for-sale';

    $route['search/(:any)/property-for-rent'] = 'main/search/$1/property-for-rent';
    $route['search/property-for-rent'] = 'main/search/property-for-rent';

    $route['search/(:any)/property-type-([a-zA-Z0-9_-]+)'] = 'main/search/$1/property-type-$2';
    $route['search/property-type-([a-zA-Z0-9_-]+)'] = 'main/search/property-type-$1';

    $route['search/(:any)/city-(:any)'] = 'main/search/$1/city-$2';
    $route['search/city-(:any)'] = 'main/search/city-$1';

    $route['search/(:any)/state-(:any)'] = 'main/search/$1/state-$2';
    $route['search/state-(:any)'] = 'main/search/state-$1';

    $route['search/(:any)/agents-([a-zA-Z0-9_-]+)'] = 'main/search/$1/agents-$2';
    $route['search/agents-([a-zA-Z0-9_-]+)'] = 'main/search/agents-$1';

    $route['search/(:any)/agents'] = 'main/search/$1/agents';
    $route['search/agents'] = 'main/search/agents';


    $route['search'] = 'main/search';
    $route['search/(:any)'] = 'main/search/$1';

    $route['blogs'] = 'blog/index';
    $route['blogs/(:any)'] = 'blog/index/$1';


    $route['blog/category/(:any)'] = 'blog/category/$1';
    $route['blog/category/(:any)/(:any)'] = 'blog/category/$1/$2';

    $route['blog/(:any)'] = 'blog/single/$1';
    $route['blog/(:any)/(:any)'] = 'blog/single/$1/$2';

    $route['ajax/(:any)'] = 'ajax/$1';

    $route['ajax_locations/(:any)'] = 'ajax_locations/$1';

    $route['ajax_images/(:any)'] = 'ajax_images/$1';

    $route['home/(:any)'] = 'main/index/$1';
	
	
	
	
    $route['property'] = 'property/index';
	
	$route['property_404'] = 'property/property_404';
	$route['property_404/(:any)'] = 'property/property_404/$1';

    $route['property/([a-zA-Z~0-9_-]+)'] = 'property/single/$1';
    $route['property/(:any)/([a-zA-Z~0-9_-]+)'] = 'property/single/$1/$2';

    $route['property/(:any)'] = 'property/index/$1';

	

    $route['main'] = 'main/index';
    $route['main/(:any)'] = 'main/index/$1';

    /** Payment Getaways Routes */
    $route['packages/subscribe/(:any)'] = 'admin/packages/subscribe/$1';
    $route['packages/confirmation'] = 'admin/packages/confirmation';
    /*$route['packages/confirmation'] = 'admin/packages/confirmation';*/

    /*$route['(:any)/([a-zA-Z0-9_-]+)'] = 'main/page/$2/$1';
    $route['([a-zA-Z0-9_-]+)'] = 'main/page/$1';*/
}






$modules_path = APPPATH . 'modules/';
$plugin_json_list = file_get_contents($modules_path . 'modules.json');
$list = json_decode($plugin_json_list, true);
foreach ($list as $key => $value) {
    $file = $key . '.php';
    if (is_dir($modules_path . $key) && file_exists($modules_path . $key . '/' . $file)) {
        if (isset($value['status']) && $value['status'] == 'Y') {

            $modules = scandir($modules_path);
            foreach ($modules as $module) {
                if ($module === '.' || $module === '..') continue;
				
                if (is_dir($modules_path) . '/' . $module    && $key == $module) {
                    $routes_path = $modules_path . $module . '/config/routes.php';

                    if (file_exists($routes_path)) {
                        require($routes_path);
                    } else {
                        continue;
                    }
                }
            }
        }
    }
}

if (!$is_admin) {
	
	
	$route['(:any)/([a-zA-Z0-9_-]+)'] = 'main/page/$2/$1';
    $route['([a-zA-Z0-9_-]+)'] = 'main/page/$1';
	
}	


//echo "<pre>";print_r( $route); exit;/**/


