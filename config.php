<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */

// db options
$local_config = 'config.local.php';
if ( ! file_exists( $local_config ) ) {
	//Set these to your deployment environment
	define('DB_USER', 'user');
	define('DB_PASSWORD', 'password');
	define('DB_NAME', 'database_name');
	//Defaults
	define('DB_TABLE', 'shortenedurls');
	define('DB_HOST', 'localhost');
} else {
	require_once 'config.local.php';
}

// connect to database
$handle = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (! is_resource($handle)) {
	die('Error connecting to database');
}
unset($handle);

//Select the database
$select = mysql_select_db(DB_NAME);
if (! $select ) {
	die('Error selecting the db: '.DB_NAME);
}
unset($select);


//Suppress PHP errors
ini_set('display_errors', 0);

// base location of script (include trailing slash)
define('BASE_HREF', 'http://' . $_SERVER['HTTP_HOST'] . '/');

// change to limit short url creation to a single IP
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
$check_url = true;
if (! function_exists('curl_init') ) $check_url = false;
define('CHECK_URL', $check_url);

// change the shortened URL allowed characters
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// do you want to cache?
define('CACHE', FALSE);

// if so, where will the cache files be stored? (include trailing slash)
define('CACHE_DIR', dirname(__FILE__) . '/cache/');