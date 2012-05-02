<?php 

function getIDFromShortenedURL ($string, $base = ALLOWED_CHARS)
{
	$length = strlen($base);
	$size = strlen($string) - 1;
	$string = str_split($string);
	$out = strpos($base, array_pop($string));
	foreach($string as $i => $char)
	{
		$out += strpos($base, $char) * pow($length, $size - $i);
	}
	return $out;
}

function getShortenedURLFromID ($integer, $base = ALLOWED_CHARS)
{
	$length = strlen($base);
	while($integer > $length - 1)
	{
		$out = $base[fmod($integer, $length)] . $out;
		$integer = floor( $integer / $length );
	}
	return $base[$integer] . $out;
}
/**
 * Gets the long url from DB
 * @param mixed $shortened_id false on failure, string long url on success
 */
function getLongURL ($shortened_id) {
	$query = 'SELECT `long_url` FROM ' . DB_TABLE . ' WHERE id="' . mysql_real_escape_string($shortened_id) . '"';
	$result = mysql_query($query);
	$long_url = mysql_result($result, 0, 0);
	if ( empty($long_url) ) return false;
	else return $long_url; 
}
function noCacheHeaders () {
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}