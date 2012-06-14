<?php
if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url']))
{
	die('That is not a valid short url');
}

require_once 'config.php';
require_once 'includes.php';

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
		@mkdir(CACHE_DIR, 0777);
		$handle = fopen(CACHE_DIR . $shortened_id, 'w+');
		fwrite($handle, $long_url);
		fclose($handle);
	}
} else {
	//No caching, just get the long url form DB
	$long_url = getLongURL($shortened_id);
}

if(TRACK) {
	$referrer = isset($_SERVER['HTTP_REFERER']) ? mysql_real_escape_string($_SERVER['HTTP_REFERER']): 'NULL';
	$ua =  isset($_SERVER['HTTP_USER_AGENT']) ? mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']) : 'NULL';
	$ip =  isset($_SERVER['REMOTE_ADDR']) ? mysql_real_escape_string($_SERVER['REMOTE_ADDR']) : 'NULL';
	if (isset($_SERVER['X_FORWARDED_FOR'])) $ip = mysql_real_escape_string($_SERVER['X_FORWARDED_FOR']);
	$sql = "INSERT INTO  `click` 
				(`url_id` , `ua` ,`referrer`, `remote_ip`)
					VALUES 
				('".(int)$shortened_id."','$ua', '$referrer', '$ip');";
	query($sql);
}
//Check if there is no value, if so, redirect to the config's homepage
if ( empty( $long_url ) && defined("HOME_URL") ) $long_url = HOME_URL;
//Send headers and redirect
noCacheHeaders();
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' .  $long_url);
exit;