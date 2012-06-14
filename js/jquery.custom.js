/**
 * 
 */
;(function($){
  $.fn.setCursorPosition = function(pos) {
	  return this.each( function  () {
		var $this = $(this);
		if ($this.get(0).setSelectionRange) {
		      $this.get(0).setSelectionRange(pos, pos);
		    } else if ($this.get(0).createTextRange) {
		      var range = $this.get(0).createTextRange();
		      range.collapse(true);
		      range.moveEnd('character', pos);
		      range.moveStart('character', pos);
		      range.select();
	    }
	  });
  };
})(jQuery);
//READY
jQuery(function($) {
	  
	$(window).bind('keyup', function  (e) {
		if (e.keyCode == 13) {
			$("#shortener").trigger('submit');
			return false;
		}
	});
	$("#ajax_loading").ajaxStart(function  () {
			$(this).fadeIn();
		}).ajaxStop(function  () {
			$(this).fadeOut();
	});
	var $longurl = $("#longurl"),
	$shorten_button = $("#shortenButton"), 
	$shortener_form = $('#shortener'),
	$qr_code = $("#short_url_qr_code"),
	$qr_code_download = $("#qr_download_link"); 
	$shortener_form.slideDown(0).on('submit', function () {
		$(".message").hide(); 
		$.ajax({
				data: {
					longurl: $('#longurl').val()
					}, 
				url: 'shorten.php',
				success: function  (data,status) {
					$shorten_button.addClass('btn-success');
					$longurl.val(data.url).addClass('alert alert-success').select();
					$("#short_url_list").prepend(data.table);
					$qr_code.attr('src', data.qr_code).slideDown();
					$qr_code_download.attr('href', data.qr_code_download).show();
				},
				error: function (XMLHttpRequest, textStatus) {
					$('.message').addClass('alert alert-error').html(XMLHttpRequest.responseText).show();
					$longurl.addClass('alert alert-error');
				},
				beforeSend: function  () {
					$longurl.removeClass('alert alert-error alert-success');	
					$shorten_button.removeClass('btn-success');
				}
			});
		return false;
	});
	$(".pop").popover();
	$(".analytics").popover({
		'title':'Analytics',
		'content':'Click to view conversions over time for this redirect/qr code.'
	}); 
	$(".shorturl").popover({
		'title':'Short URL',
		'content':'Click to select this short url for use in social media campaigns, and online advertising.'
	}); 
	var qr_note_string = 'Click to download and save this QR code for use in promotional products, '+
						'literature, etc.<br/><p class="alert alert-warning">Test QR codes for "scannability" before print runs.</p>';
	$(".qrcode").popover({
		'title':'QR Code Download',
		'content': qr_note_string,
		'placement': 'left'
	});  
	$("abbr").add('.tip').tooltip(); 
	$("#dialog").dialog({
		autoOpen: false,
		modal: true,
		width: 680,
		buttons: {
			'Close':  function  () {
				$(this).dialog('close');
			},
			"Refresh": function  () {
				$( '#dialog iframe' ).attr( 'src', function ( i, val ) { return val; });
			}
		}
	});
	$(".analytics").on('click', function  () {
		var src = $(this).attr('href');
		console.log(src)
		$("#dialog iframe").attr('src', src);
		$("#dialog").dialog('open');
		return false;
	});
});