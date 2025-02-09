<?php
/*
 * PHP URL Shortener by Circle Tree
 */

//Set this to your local config file
//make sure to add it to >> .gitignore
$local_config = 'config.local.php';
if ( ! file_exists( $local_config ) ) {
	//Set these to your deployment environment
	define('DB_USER', 'user');
	define('DB_PASSWORD', 'password');
	define('DB_NAME', 'database_name');
	define('DB_HOST', 'localhost');
	ini_set('display_errors', 0);
} else {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	require_once $local_config;
}
//Uncomment this to redirect unauthorized users
// define('REDIRECT_URL', 'http://google.com/');

/**
 * @var int QR code preview size
 */
define('QR_PREVIEW',150);
/**
 * QR fullsize links
 * @var unknown_type
 */
define('QR_FULLSIZE',150);
// base location of script (include trailing slash)
define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/');

$allowed_ips = array('127.0.0.1');
//Is the current IP authorized?
if( in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) ) {
	$auth = true;
} else {
	$auth = false;
}

// Uncomment below to make it a public URL shortening service
// $auth = true;

define('AUTH', $auth);

// change to TRUE to start tracking referrals
define('TRACK', TRUE);

// check if URL exists first
define('CHECK_URL', TRUE);

// change the shortened URL allowed characters
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// do you want to cache?
defined('CACHE') or define('CACHE', FALSE);

/**
 * Should the script check the domain for the current service URL?
 * This prevents re-submitting already shortened URLs
 */
defined('DISABLE_SHORTENING_CHECK') or define('DISABLE_SHORTENING_CHECK', false);

/**
 * Cache directory, trailing slash included
 */
define('CACHE_DIR', dirname(__FILE__) . '/cache/');