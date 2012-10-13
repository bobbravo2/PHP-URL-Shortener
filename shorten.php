<?php

$url_to_shorten = get_magic_quotes_gpc() ? stripslashes(trim($_REQUEST['longurl'])) : trim($_REQUEST['longurl']);

if(!empty($url_to_shorten)) {
	require_once 'config.php';
	require_once 'includes.php';
	userIsAuthorized();
	//Check if the shortened url is a resubmit of an already shortened url
	if (preg_match('|^'.BASE_HREF.'|', $url_to_shorten)) {
		header("HTTP/1.0 400 Bad Request");
		die('This url is already short! See the full table below');
	}
	
	if (! preg_match('|^https?://|', $url_to_shorten) ) {
		//Missing protocol, try http
		$url_to_shorten = 'http://'.$url_to_shorten;
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
			if($response_code == '404' || $response_code == 0) {
				header("HTTP/1.0 400 Bad Request");
				die('Not a valid URL');
			}
		} else {
			die('error checking URL');
		}
	}
	//Tralingslash the url
	$url_to_shorten = rtrim($url_to_shorten,'/').'/';
	// check if the URL has already been shortened
	$already_shortened = mysql_fetch_assoc(query('SELECT id FROM `url` WHERE `long_url` = "' . mysql_real_escape_string($url_to_shorten) . '"'));
	if(! empty($already_shortened) ) {
		// URL has already been shortened
		$url_id = $already_shortened['id'];
		$shortened_url = getShortenedURLFromID($url_id);
	} else {
		// URL not in database, insert
		$sql = 'INSERT INTO url (long_url, remote_ip) 
					VALUES ("' . mysql_real_escape_string($url_to_shorten) . '", 
					"' . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . '")';
		query($sql);
		$url_id = mysql_insert_id();
		$shortened_url = getShortenedURLFromID($url_id);
	}
	noCacheHeaders();
	header("HTTP/1.0 200 OK");
	header('Content-type: application/json');
	$qr_code_url = BASE_HREF.'?qr='.$url_id;
	$qr_code_download_url = BASE_HREF.'?download='.$url_id;
	$response = array(
			'url'=>BASE_HREF . $shortened_url, 
			'qr_code'=> $qr_code_url,
			'qr_code_download'=>$qr_code_download_url,
		);
	echo json_encode($response);
	die;
} else {
	header("HTTP/1.0 400 Bad Request");
	die('Invalid URL. Please make sure to include the http:// as part of a full address');
}