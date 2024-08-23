(function($) {
	var settings;
	$.fn.mpfaqAccordian = function(actionOrSettings, parameter) {
		if (typeof actionOrSettings === 'object' || actionOrSettings === undefined) {
			// Default settings:
			settings = $.extend({
				
				headline: 'h3.mpfaq-que',
				
				prefix: false,
				// Only 1 accordion can be open at any given time
				singleopen: true,

				// Allow or disallow last open accordion to be closed
				collapsible: false,
				
				arrow: true,
				collapseIcons: {
					opened: '<i class="fa fa-caret-up" aria-hidden="true"></i>',
					closed: '<i class="fa fa-caret-down" aria-hidden="true"></i>'
				},
				collapseIconsAlign: 'right',
				scroll: true
			}, actionOrSettings);
		}
		// actions
		if (actionOrSettings == "open") {
			if (settings.singleopen) {
				$(this).mpfaqAccordian('forceCloseAll');
			}
			var ogThis = $(this);
			$(this).addClass('active').next('div').slideDown(400, function() {
				if (settings.collapseIcons) {
					$('.collapseIcon', ogThis).html(settings.collapseIcons.opened);
				}
				// parameter: scroll to opened element
				if (parameter !== false) {
					//smoothScrollTo($(this).prev(settings.collapseIcons));
				}
			});
			return this;
		} else if (actionOrSettings == "close" || actionOrSettings == "forceClose") {
			// forceClose ignores collapsible setting
			if (actionOrSettings == "close" && !settings.collapsible && $(this).find(settings.headline + '[class="active"]').length == 1) {
				return this;
			}
			var ogThis = $(this);
			$(this).removeClass('active').next('div').slideUp(400, function() {
				if (settings.collapseIcons) {
					$('.collapseIcon', ogThis).html(settings.collapseIcons.closed);
				}
			});
			return this;
		} else if (actionOrSettings == "closeAll") {
			$(settings.headline).mpfaqAccordian('close');
		} else if (actionOrSettings == "forceCloseAll") {
			// forceCloseAll ignores collapsible setting
			$(settings.headline).mpfaqAccordian('forceClose');
		}

		if (settings.prefix) {
			$(settings.headline, this).attr('data-prefix', settings.prefix);
		}
		if (settings.arrow) {
			$(settings.headline, this).append('<div class="arrowDown"></div>');
		}
		if (settings.collapseIcons) {
			$(settings.headline, this).each(function(index, el) {
				if ($(this).hasClass('active')) {
					$(this).append('<div class="collapseIcon">'+settings.collapseIcons.opened+'</div>');
				} else {
					$(this).append('<div class="collapseIcon">'+settings.collapseIcons.closed+'</div>');
				}
			});
		}
		if (settings.collapseIconsAlign == 'left') {
			$('.collapseIcon, ' + settings.headline).addClass('alignLeft');
		}

		$(settings.headline, this).off().on('click',function() {
			if ($(this).hasClass('active')) {
				$(this).mpfaqAccordian('close');
			} else {
				$(this).mpfaqAccordian('open', settings.scroll);
			}
		});
	};
}(jQuery));

function smoothScrollTo(element, callback) {
	// var time = 400;
	// $('html, body').animate({
	// 	scrollTop: $(element).offset().top
	// }, time, callback);
}