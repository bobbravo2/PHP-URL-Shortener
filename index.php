<?php 
require_once 'config.php';
require_once 'includes.php';
noCacheHeaders();
?>
<!DOCTYPE html>
<html>
<head>
<title>Circletr.ee Orlando Web Design URL shortener</title>
<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="css/style.css" />
<meta name = "viewport" content = "width = device-width">
</head>
<body>
<div id="wrapper">
<a href="http://mycircletree.com" title="Circle Tree Orlando Web Design Logo"><span id="logo"></span></a>
<?php if (AUTH) { ?>
<h1>PHP URL Shortener</h1>

<div id="shortener_wrapper" class="well" >
	<div id="ajax_loading"></div>
	<h2>Create New Short URL</h2>
	<form method="post" action="shorten.php" id="shortener" style="display:none;" >
		<input type="text" name="longurl" id="longurl" size="60" value="http://" class="pop" title="URL to Shorten" data-content="Redirect users to this Web Address. We will generate a short URL and QR code that points to this address."> 
		<input type="submit" value="Shorten" id="shortenButton" class="btn btn-primary tip" title="Go time!">
		<div class="error message" style="display:none"></div>
		<iframe src="#" id="short_url_qr_code" style="display:none;"></iframe>
		<a href="#" id="qr_download_link" style="display:none;">Download</a>
	</form>
</div>
<div id="current_shorturl_wrapper">
<h2>Current Short URLS</h2>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-striped table-condensed" >
	<tr>
		<th>Redirection</th>
		<th>Conversions/Clicks</th>
		<th>Date</th>
		<th>QR Code</th>
	</tr>
	<tbody id="short_url_list">
	<?php echo render_table(); ?>
	</tbody>
	</table>
</div>
	<div id="dialog" style="display:none" title="Statistics">
		<iframe src="#"></iframe>
	</div>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js" language="javascript"></script>
	<link type="text/css" rel="Stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/themes/base/jquery-ui.css" />
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery.custom.js"></script>
<?php 
} else {
	//Unauthorized
}