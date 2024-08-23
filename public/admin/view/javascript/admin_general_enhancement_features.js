document.addEventListener(
	"keydown",
	function (e) {
		if (
			e.keyCode == 83 &&
			(navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)
		) {
			e.preventDefault();
			var formid = $(".page-header")
				.find("button[type=submit]")
				.attr("form");
			if (jQuery.type(formid) == "string") {
				$("#" + formid).submit();
			}
		}
	},
	false
);

$("#button-reset").on("click", function () {
	$(this).closest("[id^=filter]").find("input").val("");
	$(this)
		.closest("[id^=filter]")
		.find("select")
		.each(function (index, element) {
			$(element).find("option:eq(0)").prop("selected", true);
		});
});
