<?php
/*
 * PHP URL Shortener by Circle Tree
 */
//Suppress PHP errors
ini_set('display_errors', 0);

//Set this to your local config file
//make sure to add it to >> .gitignore
$local_config = 'config.local.php';
if ( ! file_exists( $local_config ) ) {
	//Set these to your deployment environment
	define('DB_USER', 'user');
	define('DB_PASSWORD', 'password');
	define('DB_NAME', 'database_name');
	define('DB_HOST', 'localhost');
} else {
	require_once $local_config;
}
//Uncomment this to redirect unauthorized users
// define('REDIRECT_URL', 'http://yourhomepage.com/');

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

//Redirect unauthorized users and failed redirects here
define("HOME_URL", 'http://mycircletree.com/');

// change the shortened URL allowed characters
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// do you want to cache?
define('CACHE', FALSE);

// if so, where will the cache files be stored? (include trailing slash)
define('CACHE_DIR', dirname(__FILE__) . '/cache/');