<?php
/*
 * First authored by Brian Cray
 * License: http://creativecommons.org/licenses/by/3.0/
 * Contact the author at http://briancray.com/
 */

if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url']))
{
	die('That is not a valid short url');
}

require_once 'config.php';
require_once 'includes.php';

$shortened_id = getIDFromShortenedURL($_GET['url']);

if( CACHE ) {
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
	$long_url = getLongURL($shortened_id);
}

if(TRACK) {
	$query = 	'UPDATE ' . DB_TABLE . ' SET referrals = referrals + 1 
				WHERE id="' . mysql_real_escape_string($shortened_id) . '"';
	mysql_query($query);
}
//Send headers and redirect
noCacheHeaders();
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' .  $long_url);
exit;

