<?php
$url_to_shorten = get_magic_quotes_gpc() ? stripslashes(trim($_REQUEST['longurl'])) : trim($_REQUEST['longurl']);

if(!empty($url_to_shorten) && preg_match('|^https?://|', $url_to_shorten)) {
	require_once 'config.php';
	require_once 'includes.php';
	
	//Check if the shortened url is a resubmit of an already shortened url
	if (preg_match('|^'.BASE_HREF.'|', $url_to_shorten)) {
		header("HTTP/1.0 400 Bad Request");
		die('This url is already short!');
	}
	
	// check if the client IP is allowed to shorten
	if( ! AUTH ) {
		header("HTTP/1.0 403 Forbidden");
		die('You are not allowed to shorten URLs with this service.');
	}
	
	// check if the URL is valid
	if( CHECK_URL ) {
		$ch = curl_init();
		//Make sure a valid cURL handle is open
		if ($ch) {
			curl_setopt($ch, CURLOPT_URL, $url_to_shorten);
			curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($ch);
			$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($response_code == '404') {
				header("HTTP/1.0 400 Bad Request");
				die('Not a valid URL');
			}
		}
	}
	
	// check if the URL has already been shortened
	$already_shortened = mysql_result(mysql_query('SELECT id FROM ' . DB_TABLE. ' WHERE long_url="' . mysql_real_escape_string($url_to_shorten) . '"'), 0, 0);
	if(! empty($already_shortened) ) {
		// URL has already been shortened
		$shortened_url = getShortenedURLFromID($already_shortened);
	} else {
		// URL not in database, insert
		mysql_query('LOCK TABLES ' . DB_TABLE . ' WRITE;');
		mysql_query('INSERT INTO ' . DB_TABLE . ' (long_url, created, creator) VALUES ("' . mysql_real_escape_string($url_to_shorten) . '", "' . time() . '", "' . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . '")');
		$shortened_url = getShortenedURLFromID(mysql_insert_id());
		mysql_query('UNLOCK TABLES');
	}
	noCacheHeaders();
	header("HTTP/1.0 200 OK");
	echo BASE_HREF . $shortened_url;
	exit;
} else {
	header("HTTP/1.0 400 Bad Request");
	die('Invalid URL');
}