<?php 
require_once 'config.php';
require_once 'includes.php';
noCacheHeaders();
?>
<!DOCTYPE html>
<html>
<head>
<title>Circletr.ee Orlando Web Design URL shortener</title>
<link type="text/css" rel="stylesheet" href="style.css" />
<link type="text/css" rel="stylesheet" media="only screen and (max-width: 480px)" href="mobile.css">
<meta name = "viewport" content = "width = device-width">
</head>
<body>
<div id="wrapper">
<a href="http://mycircletree.com" title="Circle Tree Orlando Web Design Logo"><span id="logo"></span></a>
<?php if (AUTH) { ?>
<form method="post" action="shorten.php" id="shortener">
	<label for="longurl">URL to shorten</label> 
	<input type="text" name="longurl" id="longurl" size="40" value="http://"> 
	<input type="submit" value="Shorten">
	<div class="error" style="display:none"></div>
</form>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript">
new function($) {
	  $.fn.setCursorPosition = function(pos) {
	    if ($(this).get(0).setSelectionRange) {
	      $(this).get(0).setSelectionRange(pos, pos);
	    } else if ($(this).get(0).createTextRange) {
	      var range = $(this).get(0).createTextRange();
	      range.collapse(true);
	      range.moveEnd('character', pos);
	      range.moveStart('character', pos);
	      range.select();
	    }
	  }
}(jQuery);
//READY
$(function () {
	$(window).bind('keyup', function  (e) {
		if (e.keyCode == 13) $("#shortener").trigger('submit'); 
	})
	var $longurl = $("#longurl");
$longurl.setCursorPosition(7); 
	$('#shortener').submit(function () {
	$(".error").hide(); 
		$.ajax({
				data: {
					longurl: $('#longurl').val()
					}, 
				url: 'shorten.php',
				success: function  (data,status) {
					$longurl.val(data).addClass('success').select(); 
					//createSelection(0,100, $("#longurl")); 
				},
				error: function (XMLHttpRequest, textStatus) {
					$('.error').html(XMLHttpRequest.responseText).show();
				},
				beforeSend: function  () {
					$longurl.removeClass('error success');	
				}
			});
		return false;
	});
});
</script>
<?php } else { ?>
	<h1>Orlando Web Design by Circle Tree</h1>
	<a href="http://mycircletree.com/">Circle Tree WordPress Web Design in Orlando Florida</a>
	<h2>Hello there!</h2> 
	<p>
		This is our vanity URL shortening service. It's private - which is why you're seeing this</h2>
		To learn more about <strong>vanity url shorteners</strong> and <strong>Orlando Web design</strong> <a href="http://mycircletree.com/contact-orlando-web-design-company/" title="Contact Orlando Web Design Company Circle Tree">contact us</a>.
	</p>
<?php }?>
</div>
</body>
</html>