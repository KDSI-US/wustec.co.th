var objList = new List("listFilter", {
	valueNames: ["name", "status"],
});
$("#typefilter").focus();
$("#toglechild").click(function () {
	$("#typefilter").val("");
	objList.search("");
	if ($(this).data("state") == 0) {
		$(".child").parent().show();
		$(this).data("state", "1");
		$(this).html('<i class="fa fa-arrow-up"></i>');
	} else {
		$(".child").parent().hide();
		$(this).data("state", "0");
		$(this).html('<i class="fa fa-arrow-down"></i>');
	}
});
$("#refreshlist").click(function () {
	objList.clear();
	$('select[name="type"]').trigger("change");
});