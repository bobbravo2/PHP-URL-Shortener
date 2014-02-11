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
	$qr_code_download = $("#qr_download_link"),
	$msg = $("#messages"),
	$qr_response_wrap = $("#shorten_responses");
	$longurl.setCursorPosition(7)
	$.ajaxSetup({
		error: function (XHR, status) {
			$msg
			.removeClass('hidden')
			.addClass('alert alert-error')
			.html(XHR.responseText);
		}
	});
	$shortener_form.slideDown(0).on('submit', function () {
		var longurl =  $('#longurl').val();
		$.ajax({
				data: {
					longurl: longurl
					}, 
				url: 'shorten.php',
				success: function  (data) {
					$shorten_button.addClass('btn-success');
					$longurl.val(data.url).addClass('alert alert-success').select();
					$qr_code.attr('src', data.qr_code);
					$qr_code_download.attr('href', data.qr_code_download);
					$("#longy").val(data.long_url);
					$("#shorty").val(data.url); 
					$qr_response_wrap.removeClass('hidden');
				},
				beforeSend: function  () {
					$("#messages").addClass('hidden');
					$longurl.removeClass('alert alert-error alert-success');	
					$shorten_button.removeClass('btn-success');
				}
			});
		return false;
	});
	$(".redir").tooltip({'title':'Redirects to'}) 
	$(".pop").popover();
	$(".shorturl").popover({
		'title':'Short URL',
		'content':'Click to select this short url for use in social media campaigns, and online advertising.'
	});
	//@see http://stackoverflow.com/questions/17074687/filtering-table-rows-using-jquery
	$("#searchInput").keyup(function () {
    //split the current value of searchInput
    var data = this.value.split(" ");
    //create a jquery object of the rows
    var rows = $("#short_url_list").find("tr");
    if (this.value == "") {
        rows.show();
        return;
    }
    //hide all the rows
    rows.hide();

    //Recursively filter the jquery object to get results.
    rows.filter(function (i, v) {
        var $t = $(this);
        for (var d = 0; d < data.length; ++d) {
            if (
								$t.is(":contains('" + data[d] + "')")
									|| $t.find('input:first').val().indexOf(data[d]) != -1
									|| $t.find('input:last').val().indexOf(data[d]) != -1
								) {
                return true;
            }
        }
        return false;
    })
    //show the rows that match.
    .show();
}).focus(function () {
    this.value = "";
    $(this).css({
        "color": "black"
    });
    $(this).unbind('focus');
}).css({
    "color": "#C0C0C0"
});
	$(".longurl").popover({
		title:'Long URL',
		placement: 'top',
		content:'Edit URL redirection. <span class="label label-success">Press Enter to save.</span>'
	}); 
	$(".conversions").popover({
		'title':'What is a conversion?',
		'content':'This is the number of times someone has scanned the QR code, or used the Short URL.',
		'placement': 'top'
	});
	var qr_note_string = 'Click to download this QR code for use in promotional products, literature, etc.<br/> '+
			'<p class="alert alert-danger">Test QR codes for before printing</p>' +
			'<p class="alert alert-info">We recommend using a variety of QR code apps, and different phones, if possible. This will ensure the QR scans with as many devices as possible.</p>';
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
		$("#dialog iframe").attr('src', src);
		$("#dialog").dialog('open');
		return false;
	});
	var current_url = "";
	$(".longurl").on('focus blur keyup', function  (e) {
		var $this = $(this);
		if (e.type == 'focus') {
			//edit state/
			current_url = $this.val();
			console.log('editing')
		} else if (e.type == 'blur') {
			console.log('save')
			if ($this.val() == current_url) return false;
			$.ajax({
				data:{
					'id': $this.data('id'),
					'new_url': $this.val()
				},
				success: function  () {
					$this.parent().addClass('success');
					setTimeout(function  () {
						$this.parent().removeClass('success');
						$msg.addClass('hidden');
					},2000);
				}
			});
		} else if (e.type == 'keyup') {
			if (e.keyCode == 13) {
				//Enter
				$this.trigger('blur');
			}
			//Override window
			return false;
		}
		
	}); 
});