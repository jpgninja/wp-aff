<?php
global $wp;

define('DEBUG', true);
// Test ASIN: B002RPCOH8

// Instantiate vars
$affiliate_id = '';
$domain = '';
$visitor_is_cached = !empty( $_COOKIE['affint_location'] );
$visitor = array();
$visitor_country = '';

if ($visitor_is_cached == true) {
	$visitor_country = substr( $_COOKIE['affint_location'], 0, 2 );

	if (DEBUG == true) {
		echo 'Got cached user data from cookie...<br>';
		echo 'Country Code: '. $visitor_country . '<br>';
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

  // if (DEBUG == 'true') {
  //   $ip = '70.81.0.212';
  // }

  // You know what they say about cleanliness
  $ip = filter_var($ip, FILTER_VALIDATE_IP);

	// Get Visitor Object
	$visitor = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
  $have_country = ( ( $visitor['geoplugin_countryCode'] !== '' ) || ( !isset( $visitor['geoplugin_countryCode'] ) ) );
	$visitor_country = ( $have_country ) ? $visitor['geoplugin_countryCode'] : 'US';

	if (DEBUG == true) {
		echo 'Got user data from our Geo Plugin:<br>';
		var_dump($visitor);
		echo '<br><br>';
		echo 'Country Code: ' . $visitor_country . '<br>';
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
  default:
    // Default
    if (DEBUG == true) {
      echo '<strong>Something went wrong.</strong><br>';
      echo 'Using default USA affiliate ID...<br>';
    }

    $domain = 'amazon.com';
    $affiliate_id = get_option('affint_afftag');
    break;
}

/**
 * Override affiliate tag
 */
if (isset($wp->query_vars['aff_id'])){
	$domain = 'amazon.com';
	$affiliate_id = $wp->query_vars['aff_id'];

	if (DEBUG == true) {
		echo 'Forcing to USA with ID';
	}
}

$expires = get_option('affint_user_cookie_expiry');

if (DEBUG == true) {
	echo 'Affiliate ID: '.$affiliate_id.'<br>';
	echo 'Visitor Country: '.$visitor_country.'<br>';
	echo 'Cookie set to expire in '.$expires.' days.<br>';
	echo '<br>';
}

// Clean up data
$domain = preg_replace('~[^a-z.]+~', '', $domain);
$asin = preg_replace('~[^A-Z0-9]+~', '', $wp->query_vars['asin']);
$affiliate_id = preg_replace('~[^a-zA-Z0-9-]+~', '', $affiliate_id);

// Set the cookie
setcookie("affint_location", $visitor_country, time() + ( $expires * 86400 ), '/');  /* expire in 1 hour */

// We're all set! Let's manipulate url string and redirect...
$affiliate_url = "http://_DOMAIN_/gp/product/_ASIN_/?tag=_TAG_ID_";
$affiliate_url = str_replace('_DOMAIN_', $domain, $affiliate_url);
$affiliate_url = str_replace('_ASIN_', $asin, $affiliate_url);
$affiliate_url = str_replace('_TAG_ID_', $affiliate_id, $affiliate_url);


/**
 * Redirect Logic
 *
 */
if (DEBUG == false) {
	header("Location: $affiliate_url");
} else {
	echo 'Affiliate URL: ' . $affiliate_url . '<br>';
  echo 'Cookie is set to: ' . $_COOKIE['affint_location'] . '<br>';
	echo '<br><br>';
}


exit();
