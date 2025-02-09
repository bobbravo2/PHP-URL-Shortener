<?php

require_once 'config.php';
require_once 'includes.php';
if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url']))
{
	doRedirectOrDie('Invalid Short URL');
}
$shortened_id = getIDFromShortenedURL($_GET['url']);

if( CACHE ) {
	//Caching is enabled
	$cache_filepath = CACHE_DIR . $shortened_id;
	if ( file_exists($cache_filepath) ) {
		$long_url = file_get_contents($cache_filepath);
	} else {
		$long_url = NULL;
	}
	if(empty($long_url) || !preg_match('|^https?://|', $long_url)) {
		$long_url = getLongURL($shortened_id);
		if (! empty($long_url) ) {
			$handle = fopen(CACHE_DIR . $shortened_id, 'w+');
			fwrite($handle, $long_url);
			fclose($handle);
		}
	}
} else {
	//No caching, just get the long url from DB
	$long_url = getLongURL($shortened_id);
}
if (empty($long_url)) doRedirectOrDie();
//Check if there is no value, if so, redirect to the config's homepage
if ( empty( $long_url ) && defined("REDIRECT_URL") ) $long_url = HOME_URL;
noCacheHeaders();

// IF yes tracking, do not die, continue asynchronously
$die = TRACK ? false : true;
do301($long_url, $die);
//Begin Async
ob_end_clean();
header("Connection: close");
ignore_user_abort(true);
ob_start();
header("Content-Length: 0");
ob_end_flush();
flush();
//End Async
if(TRACK) {
	$referrer = isset($_SERVER['HTTP_REFERER']) ? mysql_real_escape_string($_SERVER['HTTP_REFERER']): 'NULL';
	$ua =  isset($_SERVER['HTTP_USER_AGENT']) ? mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']) : 'NULL';
	$ip =  isset($_SERVER['REMOTE_ADDR']) ? mysql_real_escape_string($_SERVER['REMOTE_ADDR']) : 'NULL';
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = mysql_real_escape_string($_SERVER['HTTP_X_FORWARDED_FOR']);
	$sql = "INSERT INTO  `click`
				(`url_id` , `ua` ,`referrer`, `remote_ip`)
					VALUES 
				('".(int)$shortened_id."','$ua', '$referrer', '$ip');";
	query($sql);
}
die;