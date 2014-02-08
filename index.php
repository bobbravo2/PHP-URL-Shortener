<?php
require_once 'config.php';
require_once 'includes.php';
if (isset($_SERVER['REQUEST_URI']) && ! isset($_GET['url'])) {
  //Hack the url param from the request URI for non-apache servers
  $_GET['url'] = ltrim($_SERVER['REQUEST_URI'], '/');
}
if (isset($_GET['url']) && preg_match('|^[0-9a-zA-Z]{1,6}$|', $_GET['url'])) {
  require_once 'redirect.php';
  die;
}
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
        <br/>
				<a href="#" id="qr_download_link">Download</a>
			</div>
			<div class="span1"><span class="redir"><span class="icon-arrow-right"></span></span></div>
			<div class="span3">
				<input class="input-xlarge uneditable-input" type="text" id="shorty" value=""/>
			</div>
			<div class="span1" ><span class="redir"><span class="icon-arrow-right"></span></span></div>
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
    <th class="conversions<?php echo ' ' . get_sort_class('clicks') ?>">
      <a href="<?php echo $_SERVER['PHP_SELF'] . '?orderby=clicks&order=' . get_order_string_from_request() ?>">Conversions</a>
    </th>
    <th class="<?php echo get_sort_class('date') ?>">
      <a href="<?php echo $_SERVER['PHP_SELF'] . '?orderby=date&order=' . get_order_string_from_request() ?>">Date</a>
    </th>
    <th>QR Code</th>
    <th>Cached?</th>
	</tr>
	<tbody id="short_url_list">
	<?php echo render_table(); ?>
	</tbody>
	</table>
</div>
  <div class="recent">
    <h3>Recent Scans</h3>
    <?php $recent = isset($_GET['recent']) ? (int) $_GET['recent'] : 10; ?>
    <?php $recent = $recent >= 10 ? $recent : 10; ?>
    <?php $recent_next = $recent + 10; ?>
    <?php $results = query('SELECT * FROM `click` ORDER BY `time` DESC LIMIT ' . $recent) ?>
    <?php if (mysql_num_rows($results) > 0) :?>
      <table class="table table-bordered">
        <tr>
          <th>ID</th>
          <th>Short URL</th>
          <th>Long URL</th>
          <th>When</th>
          <th>User's IP</th>
        </tr>
        <?php while ($row = mysql_fetch_assoc($results)): ?>
          <tr>
            <td><?php echo $row['id'] ?></td>
            <td><?php echo BASE_HREF . getShortenedURLFromID($row['url_id']) ?></td>
            <td><?php echo getLongURL($row['url_id']); ?></td>
            <td><?php echo time_ago($row['time']); ?></td>
            <?php $user_data =
              '<b>IP:</b> ' . $row['remote_ip'] . '<br/>'.
              '<b>User-agent:</b> ' . $row['ua'] .
              ($row['referrer'] != 'NULL' ? '<br/><b>Referred by:</b> ' . $row['referrer']  : ''); ?>
            <td class="pop" data-title="User Data" data-placement="top" data-content="<?php echo $user_data ?>"><?php echo $row['remote_ip'] ?></td>
          </tr>
        <?php endwhile; ?>
        <tr>
          <td colspan="5">
            <a class="btn btn-block" href="<?php echo BASE_HREF . 'index.php?recent=' . $recent_next ?>">More</a>
          </td>
        </tr>
      </table>
    <?php else: ?>
      <div class="alert alert-info">
       No Results Yet. Scan a QR code, or use a short URL!
      </div>
    <?php endif;?>
  </div>
</div>

	<div id="dialog" style="display:none" title="Statistics">
		<iframe src="#"></iframe>
	</div>
	<script defer="defer" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script defer="defer" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
	<link type="text/css" rel="Stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css" />
	<script defer="defer" type="text/javascript" src="js/bootstrap.min.js"></script>
	<script defer="defer" type="text/javascript" src="js/jquery.custom.js"></script>
</body>
</html>