$(document).on("click", "a[data-toggle='sorting']", function (e) {
	var $element = $(this);
	var $popover = $element.data("bs.popover");
	var $sort = $element.parent().find("input").val();
	var $mod_id = $element.parent().find("input").attr("id");
	e.preventDefault();
	$('a[data-toggle="sorting"]').popover("destroy");
	if ($popover) {
		return;
	}
	$element.popover({
		html: true,
		placement: "bottom",
		trigger: "manual",
		animation: false,
		content: function () {
			return (
				'<div class="row"><div class="input-group input-sm" id="forsort"><input id="ssort" autocomplete="off" type="text" class="form-control input-sm" value="' +
				$sort +
				'"><span class="input-group-btn"><button id="button-schange" class="btn btn-primary btn-sm" type="button"> <i class="fa fa-check"></i></button></span><span class="input-group-btn"><button id="button-scancel" type="button" class="btn btn-default btn-sm"><i class="fa fa-times"></i></button></span></div></div>'
			);
		},
	});
	$element.popover("show");
	$("#forsort .btn").css("outline", "none");
	setTimeout(function () {
		$element.parent().find("input").focus();
	}, 10);
	$("#button-schange").on("click", function () {
		var text = $element
			.parent()
			.find("input")
			.val()
			.replace(/[^\-\d]+/g, "");
		text = text.length < 1 ? "0" : text;
		$.ajax({
			url:
				"index.php?route=marketplace/modification/updatesorter&user_token=" +
				getURLVar("user_token") +
				"&modification_id=" +
				$mod_id +
				"&sort_order=" +
				text,
			dataType: "json",
			success: function (json) {
				$("#serror").remove();
				$(".adw-warn").remove();
				if (json["error"]) {
					$("#forsort").append(
						'<div id="serror" class="alert alert-danger" style="display: table-caption;">' +
							json["error"]["warning"] +
							"</div>"
					);
				}
				if (json["success"]) {
					$element.text(text);
					$element.parent().find("input").val(text);
					$element.popover("destroy");
					html =
						'<div class="adw-warn alert alert-success"><i class="fa fa-info-circle"></i> OCMOD execution order has been changed. Do not forget to refresh the modifications\' cache to apply changes!';
					html +=
						' <button type="button" class="close" data-dismiss="alert">&times;</button></br>';
					$("#content > .container-fluid").prepend(html);
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(
					thrownError +
						"\r\n" +
						xhr.statusText +
						"\r\n" +
						xhr.responseText
				);
			},
		});
	});
	$("#button-scancel").on("click", function () {
		$element.popover("destroy");
	});
});
