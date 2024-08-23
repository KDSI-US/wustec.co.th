function AdminBoxToggleBtn(
	triggerEl,
	event,
	fromLevelUp = 2,
	boxSelector = "div.well,textarea"
) {
	if (event) {
		event.preventDefault();
	}
	$("body > .tooltip").remove();
	var boxes, bHtml;
	if (fromLevelUp == 1) {
		boxes = $(triggerEl).parent().find(boxSelector);
	} else {
		boxes = $(triggerEl).parent().parent().find(boxSelector);
	}
	bHtml = $(triggerEl).html();
	if (bHtml.indexOf("down") >= 0) {
		$(triggerEl).html('<i class="fa fa-caret-up"></i>');
		$(boxes).each(function () {
			$(this).attr("data-height-save", $(this).css("height"));
			if ($(this).is("textarea")) {
				$(this).css("height", $(this)[0].scrollHeight);
			} else {
				$(this).css("height", "auto");
			}
		});
	} else {
		$(triggerEl).html('<i class="fa fa-caret-down"></i>');
		$(boxes).each(function () {
			$(this).css("height", $(this).attr("data-height-save"));
		});
	}
	setTimeout(function () {
		$(triggerEl).removeAttr("disabled");
	}, 200);
}
