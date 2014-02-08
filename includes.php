<?php 
// connect to database
$handle = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (! is_resource($handle)) {
	die('Error connecting to database');
}
unset($handle);

$select = @mysql_select_db(DB_NAME);
if (! $select ) {
	die('Error selecting the db: '.DB_NAME);
}
unset($select);
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
	$out = "";
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
	$assoc = mysql_fetch_assoc((query($sql)));
	$long_url = $assoc['long_url'];
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
function downloadQR ($id, $zoom = 5) {
	require_once 'phpqrcode.php';
	$name = preg_replace('/[^a-zA-Z0-9\.]/', '', ltrim(getLongURL($id),'http://') );
	header('Content-Disposition: attachment; filename="'.$name.'.QR.gif"');
	QRcode::png(BASE_HREF.getShortenedURLFromID($id), false, QR_ECLEVEL_H, QR_FULLSIZE);
}
if (isset($_REQUEST['download'])) {
	downloadQR( (int) $_REQUEST['download']);
	die;
}
function displayQR ($id, $zoom = 4) {
	require_once 'phpqrcode.php';
	QRcode::png(BASE_HREF.getShortenedURLFromID($id));
}
if (isset($_REQUEST['qr'])) {
	displayQR((int) $_REQUEST['qr'] );
	die;
}
function install () {
	userIsAuthorized();
	@mkdir(CACHE_DIR, 0777);
	$raw_sql = file_get_contents('shortenedurls.sql');
	$parts = explode('CREATE', $raw_sql);
	foreach ($parts as $part) {
		if (strstr($part, 'TABLE')) {
			$sql = "CREATE ".$part;
			$result = query($sql);
		}
	}
	if ($result) {
		unlink('shortenedurls.sql');
		do301($_SERVER['PHP_SELF'].'?success');
	}
}
if (isset($_GET['install'])) install();
//AJAX Request for editing long urls
if (isset($_REQUEST['id']) && isset($_REQUEST['new_url'])) {
	$id = (int)$_REQUEST['id'];
	$valid = filter_var($_REQUEST['new_url'], FILTER_VALIDATE_URL);
	if (! $valid) {
		noCacheHeaders();
		header("HTTP/1.0 400 Bad Request");
		die('Invalid URL ('.$_REQUEST['new_url'].'). Please try again.');
	}
	$clean_url = filter_var($_REQUEST['new_url'], FILTER_SANITIZE_URL);
	$sql = 'UPDATE `url` set `long_url` = \''.mysql_real_escape_string($clean_url).'\' WHERE `id`='.$id;
	query($sql);
  if (CACHE && file_exists(CACHE_DIR . $id)) {
    unlink(CACHE_DIR . $id);
  }
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
		if (! headers_sent() )
			header("HTTP/1.0 400 Bad Request");
		echo '<div class="alert alert-error">';
			if (mysql_errno() == 1146 )  {
				echo 'No tables found. <b>Double check</b> to make sure you have';
				echo ' selected the correct database table, then try';
				echo ' <a href="?install">installing</a>.';				
			} else {
				echo mysql_error().PHP_EOL.mysql_errno().PHP_EOL;
				echo $sql.PHP_EOL;
			}
		echo '</div>';
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

/**
 * Get clicks over time
 * @param $url_id
 *
 * @return array
 */
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
function get_order_string_from_request () {
	return isset($_REQUEST['order']) && ($_REQUEST['order'] == 'asc') ? 'desc' : 'asc';
}
function get_sort_class ($orderby) {
	if (isset($_REQUEST['orderby']) && $_REQUEST['orderby'] == $orderby) {
		return get_order_string_from_request();
	} else {
		return 'null';
	}
}
/**
 * Table view
 * @param int $url_id optional. shows just one row of specified id
 */
function default_query ($order_col, $order, $url_id = null) {
	$sql = 'SELECT * FROM `url`';
	if ( isset($url_id) ) 
		$sql .= ' WHERE `id`='. (int) $url_id;
	$sql .= ' ORDER BY `'.$order_col.'` '.$order;
	return $sql;
}
/**
 * @param string $timestamp
 * @param int    $granularity
 * @param string $format
 *
 * @return bool|string
 */
function time_ago ($timestamp, $granularity = 1, $format='Y-m-d H:i:s'){
	$difference = time() - strtotime($timestamp);
	if($difference < 5) return 'just now';
	elseif($difference < (31556926 * 5 )) { //5 years
		$periods = array(
			'year' => 31556926,
			'month' => 2629743,
			'week' => 604800,
			'day' => 86400,
			'hour' => 3600,
			'minute' => 60,
			'second' => 1
		);
		$output = '';
		if ($difference > 31556926 )
			$granularity++; //If longer than a year, increase granularity
		foreach($periods as $label => $value){
			if($difference >= $value){
				$time = round($difference / $value);
				$difference %= $value;
				$output .= ($output ? ' ' : '').$time.' ';
				$output .= (($time > 1 ) ? $label.'s' : $label);
				$granularity--;
			}
			if($granularity == 0) break;
		}
		return $output . ' ago';
	}
	else return date($format, $timestamp);
}
function render_table ($url_id = NULL) {
	//Setup asc / desc order
	if (isset($_REQUEST['order']) && $_REQUEST['order'] == 'asc') {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}
	
	if (isset($_REQUEST['orderby'])) {
		if ($_REQUEST['orderby'] == 'clicks') {
			$sql = "SELECT *, count(click.id) AS clicks
				FROM `click`
				LEFT JOIN `url` ON url.id = click.url_id
				GROUP BY url_id
				ORDER BY clicks " . $order;
		} elseif ($_REQUEST['orderby'] == 'date') {
			$sql = default_query('created', $order);
		}
	} else {
		$sql = default_query('id', $order);
	}
	
	$result = query($sql);
	$return = '';
	if (! $result ) return '<tr><td colspan="4"><div class="well alert alert-error">No Short URLS yet.</div></td></tr>';
	while ($row = mysql_fetch_assoc($result)) {
		$return .='<tr>';
			$return .='<td>';
			$return .= '<div class="control-group">';
				$return .='<input class="shorturl uneditable-input" onclick="$(this).select();" style="cursor:pointer;" type="text" size="30" name="short['.$row['id'].']" value="'.BASE_HREF.getShortenedURLFromID($row['id']).'"/>
							<span class="redir">&rarr;</span> ';
					$return .= '<input type="text" name="longurl'.$row['id'].'" data-id="'.$row['id'].'" class="input-xxlarge longurl"';
					$return .= ' value="'.$row['long_url'].'" ';
					$return .= ' />';
				$return .= '</div>';
			$return .='</td>';
			$return .='<td>';
				$return .= '<a class="analytics btn" href="clicks.php?id='.$row['id'].'">';
					$return .= get_total_clicks($row['id']);
				$return .= '</a>';
			$return .='</td>';
			$return .='<td>';
				$return .= date('n/j/Y', strtotime($row['created']));
			$return .='</td>';
			$return .='<td class="qr_code_td">';
				$return .='<a class="qrcode btn" href="?download='.$row['id'].'">';
					$return .='<img src="?qr='.$row['id'].'" height="'.QR_PREVIEW.'" width="'.QR_PREVIEW.'" alt="QR Code" />';
				$return .='</a>';
			$return .='</td>';
    $return .= '<td>' . (file_exists(CACHE_DIR . $row['id']) ? 'Cached' : 'Not-Cached') . '</td>';
		$return .='</tr>';
	}
	unset($result);
	return $return;
}
/**
 * Checks if the user is authorized, stops execution if not;
 */
function userIsAuthorized () {
	if ( AUTH ) return;
	doRedirectOrDie();
}
function do301 ($url) {
	noCacheHeaders();
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' .  $url, TRUE, 301);
	die;
}
function doRedirectOrDie($diemsg = 'Authorized Users Only') {
	noCacheHeaders();
	if (defined('REDIRECT_URL')) do301(REDIRECT_URL);
	else die($diemsg);
}