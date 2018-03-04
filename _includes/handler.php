<?php
global $wp;

define('DEBUG', false);
// Test ASIN: B002RPCOH8

// Instantiate vars
$affiliate_id = '';
$domain = '';
$visitor_is_cached = !empty( $_COOKIE['affint_location'] );
$visitor = array();
$visitor_country = '';

// @TODO: Check for geolocation cookie


if ($visitor_is_cached == true) {
	$visitor_country = substr( $_COOKIE['affint_location'], 0, 2 );

	if (DEBUG == true) {
		echo 'Got cached user data from cookie:<br>';
		echo 'Country Code: '. $visitor_country;
		echo '<br>';
	}
} else {
	//Get IP
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	  $ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	  $ip = $_SERVER['REMOTE_ADDR'];
	}

	// Get Visitor Object
	$visitor = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
	$visitor_country = $visitor['geoplugin_countryCode'];
	
	if (DEBUG == true) {
		echo 'Got user data from our Geo Plugin:<br>';
		var_dump($visitor);
		echo '<br><br>';
		echo 'Country Code: ';
		echo $visitor['geoplugin_countryCode'];
		echo '<br>';
	}
}

/**
 * Domain Logic
 *
 * Internationalize Amazon's domain
 *
 */
switch($visitor_country) {
	case 'US':
		$domain = 'amazon.com';
		$affiliate_id = get_option('affint_afftag');
		break;
	case 'CA':
		$domain = 'amazon.ca';
		$affiliate_id = get_option('affint_afftag_ca');
		break;
	case 'DE':
		$domain = 'amazon.de';
		$affiliate_id = get_option('affint_afftag_de');
		break;
	case 'FR':
		$domain = 'amazon.fr';
		$affiliate_id = get_option('affint_afftag_fr');
		break;
	case 'JP':
		$domain = 'amazon.co.jp';
		$affiliate_id = get_option('affint_afftag_jp');
		break;
	case 'IN':
		$domain = 'amazon.in';
		$affiliate_id = get_option('affint_afftag_in');
		break;
	case 'UK':
	case 'EU':
		$domain = 'amazon.co.uk';
		$affiliate_id = get_option('affint_afftag_uk');
		break;
}

/** 
 * Override affiliate tag
 */
if (isset($wp->query_vars['aff_id'])){
	$domain = 'amazon.com';
	$affiliate_id = $wp->query_vars['aff_id'];

	if (DEBUG == true) {
		echo 'Forcing ';
	}
}

if (DEBUG == true) {
	echo 'Affiliate ID: '.$affiliate_id;
	echo '<br>';
}

// @TODO: Set cookie to reduce fetching
setcookie("affint_location", $visitor_country, time() + ( get_option('affint_user_cookie_expiry') * 86400 ));  /* expire in 1 hour */

// We're all set! Let's manipulate url string and redirect...
$affiliate_url = "http://_DOMAIN_/gp/product/_ASIN_/?tag=_TAG_ID_";
$affiliate_url = str_replace('_DOMAIN_', $domain, $affiliate_url);
$affiliate_url = str_replace('_ASIN_', $wp->query_vars['asin'], $affiliate_url);
$affiliate_url = str_replace('_TAG_ID_', $affiliate_id, $affiliate_url);


/**
 * Redirect Logic
 *
 */
if (DEBUG == false) {
	header("Location: $affiliate_url");
} else {
	echo 'Affiliate URL: '.$affiliate_url;
	echo '<br><br>';
}

exit();
