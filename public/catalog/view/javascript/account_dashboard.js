$("button[id^='button-image']").on("click", function () {
	var node = this;

	$("#form-image").remove();

	$("body").prepend(
		'<form enctype="multipart/form-data" id="form-image" style="display: none;"><input type="file" name="profile_picture" /></form>'
	);

	$("#form-image input[name='profile_picture']").trigger("click");

	if (typeof timer_image != "undefined") {
		clearInterval(timer_image);
	}

	timer_image = setInterval(function () {
		if ($("#form-image input[name='profile_picture']").val() != "") {
			clearInterval(timer_image);

			$.ajax({
				url: "index.php?route=account_dashboard/upload_image",
				type: "post",
				dataType: "json",
				data: new FormData($("#form-image")[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					$(node).button("loading");
				},
				complete: function () {
					$(node).button("reset");
				},
				success: function (json) {
					$("#profile-picture-error").remove();

					if (json["error"]) {
						$(node)
							.parent()
							.find("input[name='profile_picture']")
							.after(
								'<div class="text-danger" id="profile-picture-error">' +
									json["error"] +
									"</div>"
							);
					}

					if (json["success"]) {
						// alert(json['success']);

						$(node)
							.parent()
							.find("input[name='profile_picture']")
							.val(json["profile_picture"]);

						$(".p-image img").attr(
							"src",
							json["profile_picture_thumb"]
						);
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
		}
	}, 500);
});

$("#button-clear").click(function () {
	$("input[name='profile_picture']").val("");
	$(".p-image img").attr("src", "/image/cache/no_image-125x125.png");
});

$("button[id^='button-file']").on("click", function () {
	var node = this;

	$("#form-image").remove();

	$("body").prepend(
		'<form enctype="multipart/form-data" id="form-image" style="display: none;"><input type="file" name="seller_permit_file" /></form>'
	);

	$("#form-image input[name='seller_permit_file']").trigger("click");

	if (typeof timer_file != "undefined") {
		clearInterval(timer_file);
	}

	timer_file = setInterval(function () {
		if ($("#form-image input[name='seller_permit_file']").val() != "") {
			clearInterval(timer_file);

			$.ajax({
				url: "index.php?route=account_dashboard/upload_file",
				type: "post",
				dataType: "json",
				data: new FormData($("#form-image")[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					$(node).button("loading");
				},
				complete: function () {
					$(node).button("reset");
				},
				success: function (json) {
					$("#seller-permit-file-error").remove();

					if (json["error"]) {
						$(node)
							.parent()
							.find("input[name='seller_permit_file']")
							.after(
								'<div class="text-danger" id="seller-permit-file-error">' +
									json["error"] +
									"</div>" 
							);
					}

					if (json["success"]) {
						// alert(json['success']);

						$(node)
							.parent()
							.find("input[name='seller_permit_file']")
							.val(json["seller_permit_file"]);

						$(".s-image img").attr(
							"src",
							json["seller_permit_file_thumb"]
						);

						document.getElementById("file_name").textContent = json["seller_permit_file_name"];
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
		}
	}, 500);
});

$("#button-clear_permit").click(function () {
	$("input[name='seller_permit_file']").val("");
	$(".s-image img").attr("src", "/image/cache/no_image-125x125.png");
});

// New
$(document).on('focusin', '.customer-info', function(){
  var input = $(this);
	$(input).parent().find('div').remove();
  input.attr('style', 'background:pink;');
});

$(document).on('focusout', '.customer-info', function(){
	//var form = document.getElementById('form_edit');
  var input = $(this);
	var name = input.attr('name')
	//var id = input.attr('id')
	var value = input.val()
	var post_data = new FormData()
	post_data.append(name, value)
  input.attr('style', 'background:white;');

	$.ajax({
		url: "index.php?route=account/instant_editor",
		type: "post",
		dataType: "json",
		data: post_data,
		cache: false,
		contentType: false,
		processData: false,
		success: function (json) {
			
			if (json["error"]) {
				$(input).after(
					'<div class="text-danger" id="warning_message">' +
						json["error"][name] +
						"</div>"
				);
			}

			if (json["success"]) {
				$(input).val(value);
			}
		}
	})
})
