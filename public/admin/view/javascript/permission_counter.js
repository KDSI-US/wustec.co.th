function count_permission() {
	for (var n in { access: null, modify: null, hiden: null }) {
		var e = $('[name^="permission[' + n + ']"]'),
			i = (che = 0);
		e.each(function (n) {
			1 == $(this).prop("checked") && (che += 1), (i = n + 1);
		}),
			$("." + n + "_cnt").remove(),
			$("#" + n).append(
				' <cnt class="' +
					n +
					'_cnt" style="display:block;"> ' +
					che +
					" / " +
					i +
					"</cnt>"
			);
	}
}
$(document).ready(function () {
	$('[name^="permission[access]"]')
		.first()
		.parentsUntil(".form-group")
		.parent()
		.find("label")
		.first()
		.attr("id", "access"),
		$('[name^="permission[modify]"]')
			.first()
			.parentsUntil(".form-group")
			.parent()
			.find("label")
			.first()
			.attr("id", "modify"),
		$('[name^="permission[hiden]"]')
			.first()
			.parentsUntil(".form-group")
			.parent()
			.find("label")
			.first()
			.attr("id", "hiden"),
		count_permission();
}),
	$("input[type=checkbox]").on("change", function () {
		count_permission();
	});
