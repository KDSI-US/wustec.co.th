(function($){

	/**
	 * Get param from url
	 * @param string name
	 */
    $.getUrlParam = function(name, url){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url || window.location.href);
        return (results==null) ? null : (decodeURIComponent(results[1]) || 0);
    }

	/**
	 * Convert string to version int
	 */
	function parseVersion(a) {
	    if (typeof a != "string") return false;
	    var b = 0,
	        c = a.split("-");
	    var d = a.split(".");
	    a = parseInt(d[0]) || 0;
	    c = parseInt(d[1]) || 0;
	    var e = parseInt(d[2]) || 0;
	    d = parseInt(d[3]) || 0;
	    return a * 1E8 + c * 1E6 + e * 1E4 + d * 100 + b
	}

	$(document).ready(function() {

		/**
		 * Remember Tabs
		 */
		if('localStorage' in window && window['localStorage'] !== null){
	        var json, tabsState;
	        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
	            tabsState = localStorage.getItem("tabs-state");
	            json = JSON.parse(tabsState || "{}");
	            json[$(e.target).parents("ul").attr("id")] = $(e.target).data('target');

	            localStorage.setItem("tabs-state", JSON.stringify(json));
	        });

	        tabsState = localStorage.getItem("tabs-state");

	        json = JSON.parse(tabsState || "{}");
            setTimeout(function(){
                $.each(json, function(containerId, target) {
                    return $("#" + containerId + " a[data-target='" + target + "']").tab('show');
                });
            }, 0);

	        $("ul.nav.nav-pills, ul.nav.nav-tabs").each(function() {
	            var $el = $(this);
	            if (!json[$el.attr("id")]) {
	                return $el.find("a[data-toggle=tab]:first, a[data-toggle=pill]:first").tab("show");
	            }
	        });
		}

		/**
		 * Show hidden tab with errors
		 */
		var $hidden_error = $('.tabsHolder .error').eq(0);
		if($hidden_error.length > 0){
		    $('.tabs-nav a[href=#'+$hidden_error.parents(".tab-pane").eq(0).attr('id')+']').click();
		}

		/**
		 * Checkall
		 */
		$("#emailtemplate").on('click', 'table [data-checkall]', function(e) {
		    if (e.target.type === 'checkbox') {
		    	$($(this).data('checkall')).prop('checked', this.checked).each(function(){
					$(this).parents('tr').toggleClass('selected', this.checked)
				});
		    }
		});

		// Table row
		$('.table-row-check > tbody > tr').each(function(){
			// Row click
			$(this).on('click', function(e){
				$(this).find('> td:first-child input[type=checkbox], > td:last-child input[type=checkbox]').trigger('click').each(function(){
					$(this).parents('tr').toggleClass('selected', this.checked)
				});
			});

			// Checkbox
			$(this).find('>td:first-child input[type=checkbox], >td:last-child input[type=checkbox]').click(function(e){
				e.stopPropagation();
				$(this).parents('tr').toggleClass('selected', this.checked)
			});

			// Anchor
			$(this).find('a').click(function(e){
				e.stopPropagation();
			});
		});

        if ($.fn.ckeditor) {
            var defaultEditorOptions = {
                enterMode: CKEDITOR.ENTER_BR,
                autoParagraph: false,
                allowedContent: true
            };

            $('textarea[data-toggle=\'ckeditor\']').each(function() {
                var editorOptions = defaultEditorOptions;
                if (this.getAttribute('data-height')) {
                    editorOptions['height'] = this.getAttribute('data-height');
                }
                $(this).ckeditor(editorOptions);
            });
        } else if ($.fn.summernote) {
            $('textarea[data-toggle=\'summernote\']').each(function() {
				var summernoteOptions = {};
				if (this.getAttribute('data-height')) {
					summernoteOptions['height'] = this.getAttribute('data-height');
				}
				$(this).summernote(summernoteOptions);
            });
        }

		/**
		 * Keyboard Shortcut save form [ctrl+s]
		 */
		$(window).keypress(function(e) {
			if (!(e.which == 115 && e.ctrlKey) && !(e.which == 19)) return true;

			var $button = $("#submit_form_button");

			if($button.length){
				$button.click();

				e.preventDefault();
			}
		});

		/**
		 * Confirm dialog box
		 */
		$("#emailtemplate").on('click', '[data-confirm]', function(){
			if(window.confirm($(this).data("confirm"))){
				$(this).data('confirmed', true);

				return true;
			}

			return false;
		});

		/**
		 * Submit form via url
		 */
		$("#emailtemplate").on('click', '[data-action]', function(){
			if($(this).data('confirm') && !$(this).data('confirmed')){
				return false;
			}

			if ($(this).data('action') == 'cron') {
                startCron(this);
            } else {
                $('#form-emailtemplate').attr('action', $(this).data('action')).submit();
            }

            return true;
		});

		function startCron(btn) {
            var cron_status = true;
            var cronRequest;

            var $btn = $(btn);
            var $alert = $btn.parents('.alert');

            var $cronCancel = $alert.find('.btn-cron-cancel');
            var $alertMessage = $alert.find('.alert-message');

            $cronCancel.removeClass('hide');
            $btn.button('loading');

            var runCron = function(url) {
                if (!cron_status) return false;

                cronRequest = $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    beforeSend : function() {
                        if (cronRequest != null) {
                            cronRequest.abort();
                        }
                    },
                    success: function(json) {
                        if (json['success']) {
                            $alertMessage.html(json['success']);
                        }

                        if (json['next']) {
                            runCron(json['next']);
                        } else {
                            $cronCancel.addClass('hide');

                            $btn.button('reset');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(thrownError + " " + xhr.statusText + " " + xhr.responseText);
                    }
                });
            };

            runCron('index.php?route=extension/module/emailtemplate/cron&user_token=' + $.getUrlParam('user_token'));

            $cronCancel.click(function() {
                $cronCancel.addClass('hide')

                cron_status = false;

                if (cronRequest != null) {
                    cronRequest.abort();
                }

                $btn.button('reset');
            });
        }

		/**
		 * Version check
		 */
		if($.getUrlParam('route') == 'extension/module/emailtemplate'){
			var showVersion = true;
			if(typeof window.localStorage !== "undefined"){
				if(localStorage.getItem('extensionEmailTemplateShowVersion')){
					showVersion = false;
				} else {
					localStorage.setItem('extensionEmailTemplateShowVersion', true)
				}
			}
			if(showVersion) {
				var url = '2f2f7777772e6f70656e636172742d74656d706c617465732e636f2e756b2f76657273696f6e2d616476616e636564332e6a73';
				url = decodeURIComponent(url.replace(/(..)/g, '%$1'));
				$.getScript(url, function () {
					var current = parseVersion($('#form-emailtemplate').data('version')),
						latest = parseVersion(EmailTemplate_latest_version);
					if (latest > current) {
						$("#emailtemplate .support-text").eq(0).append("<p>A new version of this extension is available, the latest version: " + EmailTemplate_latest_version + " was released on: " + EmailTemplate_latest_date + "</p>");
					}
				});
			}
		}
	}); // docReady

})(jQuery);
