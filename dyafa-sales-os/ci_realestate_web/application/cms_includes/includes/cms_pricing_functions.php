<?php



 

function get_currency_symbol($currency_code = null)
{

	$CI = &get_instance();

	$currency_symbols = $CI->config->item('currency_symbols');

	$selected_currency = $CI->site_currency;
	if ($currency_code != null) {
		if (array_key_exists($currency_code, $currency_symbols))
			return $currency_symbols[$currency_code];
	} else if (isset($currency_symbols) && !empty($currency_symbols) && $selected_currency && $selected_currency != '') {
		if (array_key_exists($selected_currency, $currency_symbols))
			return $currency_symbols[$selected_currency];
	}
	return '';
}


function moneyFormatDollar($num, $args = array())
{

	extract($args);
	$CI = &get_instance();

	if (isset($CI->currency_pos)) $currency_pos =  $CI->currency_pos;
	else $currency_pos = 'left';
	if (isset($CI->thousand_sep)) $thousand_sep =  $CI->thousand_sep;
	else $thousand_sep = ',';
	if (isset($CI->decimal_sep))  $decimal_sep =  $CI->decimal_sep;
	else $decimal_sep = '.';
	if (isset($CI->num_decimals)) $num_decimals =  $CI->num_decimals;
	else $num_decimals = '2';

	if(is_numeric($num)){
		
		$num = (int) $num;
		$amount = number_format($num, $num_decimals, $decimal_sep, $thousand_sep);
		
	}else{
		$amount = $num;
	}
	

	if (isset($currency_symbol)) {
		if ($currency_pos == 'left')
			$amount = $currency_symbol . $amount;
		if ($currency_pos == 'left_space')
			$amount = $currency_symbol . " " . $amount;

		if ($currency_pos == 'right')
			$amount .= $currency_symbol;
		if ($currency_pos == 'right_space')
			$amount .= " " . $currency_symbol;
	}
	return $amount;
}

function show_price_with_currency($price, $currency)
{


	$args = array("currency_symbol" => get_currency_symbol($currency));
	return moneyFormatDollar($price, $args);
}



function isPluginActive($plugin_slug = null)
{
	$CI = &get_instance();
	$site_plugins_json = $CI->global_lib->get_option('site_plugins');
	if (!empty($site_plugins_json) && $plugin_slug != null) {
		$site_plugins = json_decode($site_plugins_json, true);
		if (in_array($plugin_slug, $site_plugins)) {
			return true;
		}
	}
	return false;
}
