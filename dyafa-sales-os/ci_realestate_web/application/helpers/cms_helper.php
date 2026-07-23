<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function cms_header()
{
	$CI = &get_instance();

	if (isset($CI->header_scripts))
		$header_scripts = $CI->header_scripts;
	else return;

	if (is_array($header_scripts) &&  count($header_scripts)) {
		foreach ($header_scripts as $script) {
			echo $script . "\n\r";
		}
	}
}

function create_log($log){
	
	
	file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);
}

function cms_footer()
{
	$CI = &get_instance();

	if (isset($CI->footer_scripts))
		$footer_scripts = $CI->footer_scripts;
	else return;

	if (is_array($footer_scripts) &&  count($footer_scripts)) {
		foreach ($footer_scripts as $script) {
			echo $script . "\n\r";
		}
	}
}

function cms_admin_header()
{
	$CI = &get_instance();


	if (isset($CI->admin_header_scripts))
		$admin_header_scripts = $CI->admin_header_scripts;
	else return;

	if (is_array($CI->admin_header_scripts) &&  count($CI->admin_header_scripts)) {
		foreach ($CI->admin_header_scripts as $script) {
			echo $script . "\n\r";
		}
	}
}


function cms_admin_footer()
{
	$CI = &get_instance();

	if (isset($CI->admin_footer_scripts))
		$admin_footer_scripts = $CI->admin_footer_scripts;
	else return;

	if (is_array($CI->admin_footer_scripts) &&  count($CI->admin_footer_scripts)) {
		foreach ($CI->admin_footer_scripts as $script) {
			echo $script . "\n\r";
		}
	}
}

function cms_checkout_footer_scripts()
{
	$CI = &get_instance();

	if (isset($CI->checkout_footer_scripts))
		$checkout_footer_scripts = $CI->checkout_footer_scripts;
	else return;

	if (is_array($CI->checkout_footer_scripts) &&  count($CI->checkout_footer_scripts)) {
		foreach ($CI->checkout_footer_scripts as $script) {
			echo $script . "\n\r";
		}
	}
}

function cms_admin_property_edit_scripts()
{
	$CI = &get_instance();

	if (isset($CI->admin_property_edit_scripts))
		$admin_property_edit_scripts = $CI->admin_property_edit_scripts;
	else return;

	if (is_array($CI->admin_property_edit_scripts) &&  count($CI->admin_property_edit_scripts)) {
		foreach ($CI->admin_property_edit_scripts as $script) {
			echo $script . "\n\r";
		}
	}
}


function cms_admin_header_top_nav_links()
{
	$CI = &get_instance();

	if (isset($CI->admin_header_top_nav_links))
		$admin_header_top_nav_links = $CI->admin_header_top_nav_links;
	else return;

	if (is_array($CI->admin_header_top_nav_links) &&  count($CI->admin_header_top_nav_links)) {
		foreach ($CI->admin_header_top_nav_links as $nav_link) {
			echo $nav_link . "\n\r";
		}
	}
}


function cms_property_custom_metaboxes()
{

	$CI = &get_instance();

	if (isset($CI->property_custom_metaboxes))
		$property_custom_metaboxes = $CI->property_custom_metaboxes;
	else return;

	if (is_array($CI->property_custom_metaboxes) &&  count($CI->property_custom_metaboxes)) {
		foreach ($CI->property_custom_metaboxes as $custom_metabox) {
			echo $custom_metabox . "\n\r";
		}
	}
}


function cms_language_menus()
{
	$CI = &get_instance();
	$site_language = $CI->global_lib->get_option('site_language');
	$default_language = $CI->global_lib->get_option('default_language');

	ob_start();
?>
	<div class="container">
		<div class="position-relative">
			<div class="position-absolute text-end end-0 mt-3">

				<div class="btn-group multi_language w-auto float-end">
					<button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						<?php
						$flag_codes =  array(
							"en" => "us",
							"hi" => "in", "cs" => "cz",
							"he" => "is", "ja" => "jp", "ko" => "sk", "da" => "dk",
						);

						if (
							isset($CI->default_language) && !empty($CI->default_language) &&
							isset($CI->default_language_title) && !empty($CI->default_language_title)
						) {

							$lang_title = $CI->default_language_title;
							$flag_code = $lang_code = $lang_code_title =  $CI->default_lang_code;
							if (array_key_exists($flag_code, $flag_codes))
								$flag_code = $flag_codes[$flag_code];
						?>
							<span class="flag-icon flag-icon-<?php echo $flag_code; ?>"></span> <?php echo strtoupper($lang_code_title); ?>
						<?php } else if (isset($default_language) && !empty($default_language)) {
							$lang_exp = explode('~', $default_language);

							$lang_title = $lang_exp[0];
							$lang_code = $lang_code_title = $lang_exp[1];

							$lang_code_combi = $lang_exp[1];
							$lang_code_exp = explode('-', $lang_code_combi);
							if (isset($lang_code_exp[1])) {
								$lang_code = strtolower($lang_code_exp[1]);
							} else
								$lang_code = $lang_code_exp[0];
						?>
							<span class="flag-icon flag-icon-<?php echo $lang_code; ?>"></span> <?php echo strtoupper($lang_code_title); ?>
						<?php } else { ?>
							<span class="flag-icon flag-icon-us"></span> En
						<?php } ?>
					</button>
					<div class="dropdown-menu dropdown-menu-end language">
						<?php if (isset($site_language) && !empty($site_language)) {
							$site_language_array = json_decode($site_language, true);
							foreach ($site_language_array as $aak => $aav) {

								if ($aav['language'] == $default_language) {
									$new_value = $site_language_array[$aak];
									unset($site_language_array[$aak]);
									array_unshift($site_language_array, $new_value);
									break;
								}
							}


							$current_user_lang_code = 'en';


							if (isset($_SESSION['default_lang_front']) && !empty($_SESSION['default_lang_front'])) {
								$sesson_def_lang =   $_SESSION['default_lang_front'];
								$lang_exp = explode('~', $_SESSION['default_lang_front']);

								$lang_code_full = $lang_code = $lang_exp[1];
								$lang_title = $lang_exp[0];
								$currency = $lang_exp[1];
								$lang_code_combi = $lang_exp[1];
								$lang_code_exp = explode('-', $lang_code_combi);
								if (isset($lang_code_exp[1])) {
									$current_user_lang_code = strtolower($lang_code_exp[1]);
								} else
									$current_user_lang_code = $lang_code_exp[0];
							}


							foreach ($site_language_array as $k => $v) {

								if (!isset($v['status']) || (isset($v['status']) && $v['status'] != 'enable'))
									continue;
								$lang_exp = explode('~', $v['language']);
								//print_r($lang_exp);
								$lang_title = $lang_exp[0];
								$currency = $v['currency'];
								$lang_code_combi = $lang_exp[1];
								$lang_code_exp = explode('-', $lang_code_combi);

								if (isset($lang_code_exp[1])) {
									$lang_code_title = $lang_code_exp[1];
									$flag_code = $lang_code = strtolower($lang_code_exp[1]);
								} else
									$flag_code = $lang_code = $lang_code_title = $lang_code_exp[0];

								if (array_key_exists($flag_code, $flag_codes))
									$flag_code = $flag_codes[$flag_code];

								$parts = explode('?', $_SERVER['REQUEST_URI'], 2);

								$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

								$current_url = $protocol . $_SERVER['HTTP_HOST'] . $parts[0] . (isset($parts[1]) ? '?' . $parts[1] : '');

								if (substr($current_url, -1) != '/')
									$current_url .= "/";

								if (isset($page_title) && $page_title == 'Home') {
									$current_url = str_replace("/home/$current_user_lang_code/", "/", $current_url);
									$current_url .= "home/$lang_code/";
								}

								$current_url = str_replace("/$current_user_lang_code/", "/" . $lang_code . "/", $current_url);


						?>
								<a class="dropdown-item" href="<?php echo $current_url; ?>" data-lang_code="<?php echo $flag_code; ?>" data-lang_code_or="<?php echo $lang_code_combi; ?>" data-currency="<?php echo $currency; ?>" data-lang_title="<?php echo $lang_title; ?>"><span class="flag-icon flag-icon-<?php echo $flag_code; ?>"></span> <?php echo strtoupper($lang_code_title); ?> </a>

						<?php }
						} ?>
					</div>
				</div>

			</div>
		</div>
	</div>
<?php
	$language_menus = ob_get_contents();
	ob_end_clean();

	echo $language_menus;
}


if ( ! function_exists('script_tag')) {
    function script_tag($src = '', $language = 'javascript', $type = 'text/javascript', $index_page = FALSE)
    {
        $CI =& get_instance();
        $script = '<scr'.'ipt';
        if (is_array($src)) {
            foreach ($src as $k=>$v) {
                if ($k == 'src' AND strpos($v, '://') === FALSE) {
                    if ($index_page === TRUE) {
                        $script .= ' src="'.$CI->config->site_url($v).'"';
                    }
                    else {
                        $script .= ' src="'.$CI->config->slash_item('base_url').$v.'"';
                    }
                }
                else {
                    $script .= "$k=\"$v\"";
                }
            }

            $script .= "></scr"."ipt>\n";
        }
        else {
            if ( strpos($src, '://') !== FALSE) {
                $script .= ' src="'.$src.'" ';
            }
            elseif ($index_page === TRUE) {
                $script .= ' src="'.$CI->config->site_url($src).'" ';
            }
            else {
                $script .= ' src="'.$CI->config->slash_item('base_url').$src.'" ';
            }

            $script .= 'language="'.$language.'" type="'.$type.'"';
            $script .= ' /></scr'.'ipt>'."\n";
        }
        return $script;
    }
}