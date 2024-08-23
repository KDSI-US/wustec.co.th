$(document).ready(function () {
	// Set last page opened on the menu
	$("#menu a[href]").on("click", function () {
		sessionStorage.setItem("menu", $(this).attr("href"));
	});

	if (!sessionStorage.getItem("menu")) {
		$("#menu #dashboard").addClass("active");
	} else {
		// Sets active and open to selected page in the left column menu.
		$("#menu a[href='" + sessionStorage.getItem("menu") + "']")
			.parents("li")
			.addClass("active open");
	}

	var my = { window: $(window) };

	if (my.window.width() > 640) {
		if (localStorage.getItem("column-left-fix") == "active") {
			$("#button-menu-fix i").replaceWith(
				'<i class="fa fa-dedent fa-lg"></i>'
			);

			$("#column-left-fix").addClass("active");

			// Slide Down Menu
			$("#menu li.active")
				.has("ul")
				.children("ul")
				.addClass("collapse in");
			$("#menu li")
				.not(".active")
				.has("ul")
				.children("ul")
				.addClass("collapse");
		} else {
			$("#button-menu-fix i").replaceWith(
				'<i class="fa fa-indent fa-lg"></i>'
			);

			$("#menu li li.active")
				.has("ul")
				.children("ul")
				.addClass("collapse in");
			$("#menu li li")
				.not(".active")
				.has("ul")
				.children("ul")
				.addClass("collapse");
		}
	}

	// Menu button
	$("#button-menu-fix").on("click", function () {
		// Checks if the left column is active or not.
		if ($("#column-left-fix").hasClass("active")) {
			localStorage.setItem("column-left-fix", "");

			$("#button-menu-fix i").replaceWith(
				'<i class="fa fa-indent fa-lg"></i>'
			);

			$("#column-left-fix").removeClass("active");

			$("#menu > li > ul").removeClass("in collapse");
			$("#menu > li > ul").removeAttr("style");
		} else {
			localStorage.setItem("column-left-fix", "active");

			$("#button-menu-fix i").replaceWith(
				'<i class="fa fa-dedent fa-lg"></i>'
			);

			$("#column-left-fix").addClass("active");

			// Add the slide down to open menu items
			$("#menu li.open").has("ul").children("ul").addClass("collapse in");
			$("#menu li")
				.not(".open")
				.has("ul")
				.children("ul")
				.addClass("collapse");
		}
	});

	// Menu
	$("#menu")
		.find("li")
		.has("ul")
		.children("a")
		.on("click", function () {
			if ($("#column-left-fix").hasClass("active")) {
				$(this)
					.parent("li")
					.toggleClass("open")
					.children("ul")
					.collapse("toggle");
				$(this)
					.parent("li")
					.siblings()
					.removeClass("open")
					.children("ul.in")
					.collapse("hide");
			} else if (!$(this).parent().parent().is("#menu")) {
				$(this)
					.parent("li")
					.toggleClass("open")
					.children("ul")
					.collapse("toggle");
				$(this)
					.parent("li")
					.siblings()
					.removeClass("open")
					.children("ul.in")
					.collapse("hide");
			}
		});
});
