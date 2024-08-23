$.getUrlParameter = function(name, url){
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url || window.location.href);
  return (results==null) ? null : (decodeURIComponent(results[1]) || 0);
  }

$(document).on('click', '#button-sendSMS', function(){
  var node = this;
  $(node).parent().find('div').remove();
	var post_data = new FormData();
  post_data.append('tracking_number', $(this).attr('data-tracking'));
  post_data.append('firstname', $(this).attr('data-firstname'));
  post_data.append('telephone', $(this).attr('data-telephone'));
  post_data.append('sendSMS', true);
	$.ajax({
		url: "index.php?route=sale/twilio&user_token=" + $.getUrlParameter('user_token'),
		type: "post",
		dataType: "json",
		data: post_data,
		cache: false,
		contentType: false,
		processData: false,
		success: function (json) {
			if (json["error"]) {
        $(node)
        .parent()
        .find("button[id='button-sendSMS']")
        .after(
          '<div class="text-danger" id="send-tracking-error">' +
            json["error"] +
            "</div>"
        );
			}
			if (json["success"]) {
        $(node)
        .parent()
        .find("button[id='button-sendSMS']")
        .after(
          '<div class="text-success" id="send-tracking-success">' +
            json["success"] +
            "</div>"
        );			}
		}
	})
})