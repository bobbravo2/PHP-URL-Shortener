<?php 

/**
 * Gets id from short url (pretty string)
 * @param short url $string
 * @param string $base (optional) allowed characters 
 * @return number $id primary key of record in db
 */
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
/**
 * Gets the human pretty short url from the url.id primary key
 * @param int $integer id of record in database
 * @param string $base (optional) allowed characters
 * @return string $url short url
 */
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
	$sql = 'SELECT `long_url` FROM `url` WHERE id="' . mysql_real_escape_string($shortened_id) . '"';
	$long_url = mysql_result(query($sql), 0, 0);
	if ( empty($long_url) ) return false;
	else return $long_url; 
}
/**
 * sends no-cache headers
 */
function noCacheHeaders () {
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}
/**
 * gets google chart API img src url
 * @param string $id 
 * @param int $zoom relative QR code zoom
 * @return string image/gif image
 */
function getQR ($id, $zoom=4) {
	require_once 'qrcode.php';
	$a = new QR(BASE_HREF.getShortenedURLFromID($id), QR::ECC_H);
	//Image Output
	return $a->image($zoom);
}
function downloadQR ($id, $zoom = 5) {
	header('Content-type: image/gif');
	$name = preg_replace('/[^a-zA-Z0-9\.]/', '', ltrim(getLongURL($id),'http://') );
	header('Content-Disposition: attachment; filename="'.$name.'.QR.gif"');
	echo getQR($id, $zoom);
}
if (isset($_REQUEST['download'])) {
	downloadQR( (int) $_REQUEST['download'] );
	die;
}
function displayQR ($id, $zoom = 4) {
	header('Content-type: image/gif');
	$qr_data = getQR($id, $zoom);
	header('Content-length:'.strlen($qr_data));
	echo $qr_data;
}
if (isset($_REQUEST['qr'])) {
	displayQR((int) $_REQUEST['qr'] );
	die;
}
/**
 * Simple Query wrapper function for error handling
 * @param string $sql
 */
function query ($sql) {
	$result = mysql_query($sql);
	if ( is_resource($result) ) return $result;
	elseif ($result) return true;
	else {
		header("HTTP/1.0 400 Bad Request");
		echo mysql_error().PHP_EOL;
		echo $sql.PHP_EOL;
	}
}
function is_ajax () {
	return  isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest';
}
function get_total_clicks ($id) {
	$sql = 'SELECT COUNT(*) AS total FROM `click` WHERE `url_id`='.(int)$id;
	$result = mysql_fetch_assoc(query($sql));
	return $result['total'];
}
function get_clicks ($url_id) {
	$sql = 'SELECT `time` FROM `click` WHERE `url_id`='.(int)$url_id. ' ORDER BY `time` ASC';
	$result = query($sql);
	$return = array();
	while ($row = mysql_fetch_assoc($result)) {
		$return[] = $row;
	}
	return $return;
}
function formatJSdate ($time) {
	$timestamp = strtotime($time);
	$js_date_format = 'new Date(%s, %s, %s, %s, %s, %s)';
	return sprintf($js_date_format,
			date('Y', $timestamp),
			date('m', $timestamp),
			date('d', $timestamp),
			date('G', $timestamp),
			date('i', $timestamp),
			date('s', $timestamp)
	);
}
/**
 * Table view
 * @param int $url_id optional. shows just one row of specified id
 */
function render_table ($url_id = NULL) {
	$order_col = 'created';
	$sql = 'SELECT * FROM `url`';
	if (isset($url_id)) $sql .= ' WHERE `id`='. (int) $url_id;
	$sql .= ' ORDER BY `'.$order_col.'` DESC';
	$result = query($sql);
	$return = '';
	while ($row = mysql_fetch_assoc($result)) {
		$return .='<tr>';
			$return .='<td>';
			$return .=' <input class="shorturl" onclick="$(this).select();" style="cursor:pointer;" type="text" size="30" name="short['.$row['id'].'" value="'.BASE_HREF.getShortenedURLFromID($row['id']).'"/><abbr title="Redirects to">&rarr;</abbr> ';
				$return .=$row['long_url'];
			$return .='</td>';
			$return .='<td>';
				$return .= '<a class="analytics" href="clicks.php?id='.$row['id'].'">';
					$return .= get_total_clicks($row['id']);
				$return .= '</a>';
			$return .='</td>';
			$return .='<td>';
				$return .= $row['created'];
			$return .='</td>';
			$return .='<td>';
				$return .='<a class="qrcode" href="?download='.$row['id'].'">';
					$return .='<img src="data:image/gif;base64,'.base64_encode(getQR($row['id'])).'" height="'.QR_PREVIEW.'" width="'.QR_PREVIEW.'" alt="QR Code" />';
				$return .='</a>';
			$return .='</td>';
		$return .='</tr>';
	}
	unset($result);
	return $return;
}