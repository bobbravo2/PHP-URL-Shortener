<?php 
require_once 'config.php';
require_once 'includes.php';
noCacheHeaders();
userIsAuthorized();
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
<a href="/">
<h1 class="logo" >URL Shortener &amp; QR Code Generator</h1>
</a>
<?php if (isset($_GET['success'])): ?>
	<div class="alert alert-success">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
		<h1>Installation Successful</h1>
	</div>
<?php endif;?>
<div id="shortener_wrapper" class="well" >
	<div id="ajax_loading"></div>
	<form method="post" action="shorten.php" id="shortener" style="display:none;" >
	<fieldset class="control-group input-append form-horizontal" >
		<label class="control-label" for="longurl">Create New Short URL</label>
		<div class="controls">
			<input type="text" name="longurl" id="longurl" value="http://" class="pop span8" title="URL to Shorten" data-content="Redirect users to this Web Address. We will generate a short URL and QR code that points to this address."> 
			<input type="submit" value="Shorten" id="shortenButton" class="btn btn-primary tip" title="Go time!">
		</div>
	</fieldset>
		<div id="messages" class="hidden"></div>
		<div id="shorten_responses" class="well row hidden" >
			<div class="offset1 span3">
				<iframe src="#" id="short_url_qr_code"></iframe>
				<a href="#" id="qr_download_link">Download</a>
			</div>
			<div class="span1"><span class="redir" >&rarr;</span></div>
			<div class="span3">
				<input class="input-xlarge uneditable-input" type="text" id="shorty" value=""/>
			</div>
			<div class="span1" ><span class="redir">&rarr;</span></div>
			<div class="span3">
				<input class="input-xlarge uneditable-input" type="text" id="longy" value=""/>
			</div>
		</div>
	</form>
</div>
<div id="current_shorturl_hwrapper">
<h1>Current Short URLS <a href="<?php echo $_SERVER['PHP_SELF']?>" class="btn" >Refresh</a></h1>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-striped table table-condensed" >
	<tr>
		<th>Short URL <abbr title="Redirects to">&rarr;</abbr> Long URL</th>
		<th class="conversions" >Conversions</th>
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
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js" language="javascript"></script>
	<link type="text/css" rel="Stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css" />
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery.custom.js"></script>
</div>
</body>
</html>