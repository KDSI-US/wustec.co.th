(function($){

	$(document).ready(function(){
		if ($.fn.autocomplete) {
			$('.input-autocomplete-product').each(function(){
				var $el = $(this),
					$group = $el.parents('.input-group'),
					$field = $($el.data('field')),
					$output = $($el.data('output'));

				$el.autocomplete({
					source: function(request, response) {
						$group.find('.input-group-addon .fa').attr("class", "fa fa-fw fa-circle-o-notch fa-spin").addClass('input-autocomplete-loading');
                        $.ajax({
                            url: ('index.php?route=catalog/product/autocomplete&user_token=' + $.getUrlParam('user_token') + '&filter_name=' + encodeURIComponent(request)),
                            type: 'GET',
                            dataType: 'json',
                            success: function(json) {
                                response($.map(json, function(item) {
                                    return {
                                        label: item['name'],
                                        value: item['product_id']
                                    }
                                }));
                            },
							complete: function () {
								$group.find('.input-group-addon .fa').attr("class", "fa fa-fw fa-search").removeClass('input-autocomplete-loading');
							}
                        });
					},
					select: function(item) {
						if($field.val() == '') {
							$field.val(item['value']);
						} else {
							var selection = $field.val().split(',');
							if($.inArray(item['value'], selection) == -1){
								selection.push(item['value']);
								$field.val(selection.join(','));
							}
						}
						$output.removeClass('hide').find('.list-group').append('<li class="list-group-item" data-id="' + item['value'] + '"><a href="javascript:void(0)" class="badge remove list-group-item-danger"><i class="fa fa-fw fa-times"></i></a> ' + item['label'] + '</li>');
					}
				});
				return true;
			});
		}

		var initControls = function() {
            $('#add-condition').off('change').change(function () {
                var $conditions = $('#emailtemplate_conditions');
                var i = $conditions.children(':last-child').data('count');
                if (i >= 1) {
                    i++;
                } else {
                    i = 1;
                }

                var html = '<div class="row mt-1" id="template-condition-' + i + '" data-count="' + i + '">';
                html += '	<div class="col-md-4"><input type="text" name="emailtemplate_condition[' + i + '][key]" class="form-control" value="' + $(this).find(":selected").text() + '" readonly="readonly" /></div>';
                html += '	<div class="col-md-2"><select name="emailtemplate_condition[' + i + '][operator]" class="form-control">';
                html += '		<option value="==">(==) Equal</option>';
                html += '		<option value="!=">(!=) &nbsp;Not Equal</option>';
                html += '		<option value="&gt;">(&gt;) &nbsp;&nbsp;Greater than</option>';
                html += '		<option value="&lt;">(&lt;) &nbsp;&nbsp;Less than</option>';
                html += '		<option value="&gt;=">(&gt;=) Greater than or equal to </option>';
                html += '		<option value="&lt;=">(&lt;=) Less than or equal to </option>';
                html += '		<option value="IN">(IN) Checks if a value exists in comma-delimited string </option>';
                html += '		<option value="NOTIN">(NOTIN) Checks if a value does not exist in comma-delimited string </option>';
                html += '	</select></div>';
                html += '	<div class="col-md-4"><input type="text" name="emailtemplate_condition[' + i + '][value]" class="form-control" value="" placeholder="Value" /><label class="checkbox-inline"><input type="checkbox" name="emailtemplate_condition[' + i + '][required]" value="1" /> Required</label></div>';
                html += '	<div class="col-md-2 text-right"><button type="button" class="btn btn-danger btn-remove-row" data-remove="#template-condition-' + i + '"><i class="fa fa-times"></i></button></div>';
                html += '</div>';

                var $element = $(html);

                $element.find('.btn-remove-row').click(function () {
                    $($(this).data('remove')).remove();
                });

                $conditions.append($element);

                $(this).find('option:selected').prop("selected", false);
            })

			$('.add-condition-action').off('change').change(function () {
				var $conditions = $('#emailtemplate_conditions');
				var i = $conditions.children(':last-child').data('count');
				if (i >= 1) {
					i++;
				} else {
					i = 1;
				}

				let condition_type = $(this).data('type');
				let condition_value = $(this).val();
				let condition_label = $(this).find(":selected").text();

				var html = '<div class="row mt-1" id="template-condition-' + i + '" data-count="' + i + '">';
				html += '	<div class="col-md-4"><input type="text" name="emailtemplate_condition[' + i + '][key]" class="form-control" value="' + condition_type + '" readonly="readonly" /></div>';
				html += '	<div class="col-md-2"><select name="emailtemplate_condition[' + i + '][operator]" class="form-control">';
				html += '		<option value="==">(==) Equal</option>';
				html += '		<option value="!=">(!=) &nbsp;Not Equal</option>';
				html += '	</select></div>';
				html += '	<div class="col-md-4">' +
					'<input type="hidden" name="emailtemplate_condition[' + i + '][value]" class="form-control" value="' + condition_value +'" />' +
					'<input type="text" readonly name="emailtemplate_condition[' + i + '][label]" class="form-control" value="' + condition_label + '" placeholder="Value" />' +
					'</div>';

				html += '	<div class="col-md-2 text-right"><button type="button" class="btn btn-danger btn-remove-row" data-remove="#template-condition-' + i + '"><i class="fa fa-times"></i></button></div>';
				html += '</div>';

				var $element = $(html);

				$element.find('.btn-remove-row').click(function () {
					$($(this).data('remove')).remove();
				});

				$conditions.append($element);

				$(this).find('option:selected').prop("selected", false);
			})

            $('.shortcode-select').off('click').click(function () {
                $(this).select();
                return false;
            });

            $('.pagination select').removeAttr('onchange').off('change').change(function(){
                refreshPage(this.value);
                return false;
            });

            $('.pagination a').off('click').click(function(){
                refreshPage($(this).attr('href'));
                return false;
            });

            $('.add-editor').off('click').click(function(){
                var $el = $('#emailtemplate_description_content' + $(this).data('content') + '_' + $(this).data('lang'));
                $el.parents('.emailtemplate_content').removeClass('hide');

                if (typeof CKEDITOR !== "undefined") {
                    CKEDITOR.replace($el.attr('id'));
                }

                $(this).remove();
            });
        }

        initControls();

		// Change showcase type
		$('#emailtemplate_showcase').change(function(){
			var $tab = $(this).parents('.tab-pane');

			switch($(this).val()){
				case 'products':
					$tab.find('.showcase_products').removeClass('hide');
					break;
				default:
					$('#emailtemplate_showcase_selection').val('');

					$('#emailtemplate_showcase_output').empty('');
					$tab.find('.showcase_products').addClass('hide');
			}
		});

		// Remove showcase product
		$('#emailtemplate_showcase_output').on('click', '.remove', function(){
			var $item = $(this).parents('li');
			var $field = $('input[name=emailtemplate_showcase_selection]');

			var values = $.map($field.val().split(','), function(value){ return parseInt(value, 10) });
			var index = $.inArray($item.data('id'), values);
			if(index !== -1){
				values.splice(index, 1);
			}
			$field.val(values.join(','));

			$item.remove();
		});

		var refreshPage = function(url) {
			if (!url) return false;
			var activeTab = $('#tabs-main .active > a');
			var activeTabTarget = activeTab.data('target');
			var activeTabIndex = activeTab.parent().index();
			$.ajax({
				url: url,
				type: 'GET',
				success: function(html) {
					$(activeTabTarget).html(
						$(html).find(activeTabTarget).html()
					);

					if (window.history.pushState) {
						window.history.pushState(null, null, url);
					}

                    initControls();

					// select tab
					var tab = $('#tabs-main').children().eq(activeTabIndex);
					if (tab.length) {
						tab.find('a').tab("show");
					}
				}
			});
		}

		$('.btn-remove-row').click(function(){
            $($(this).data('remove')).remove();
		});

		/**
		 * Preview
		 */
		var loadPreview = function($preview, callback) {
			var requestUrl = 'index.php?route=extension/module/emailtemplate/preview_email&emailtemplate_id=' + $.getUrlParam('id') + '&user_token=' + $.getUrlParam('user_token');
			if (document.getElementById('store_id')){
				requestUrl += '&store_id=' + document.getElementById('store_id').value;
			}
			if ($preview.data('language')){
				requestUrl += '&language_id=' + $preview.data('language');
			}

			if(typeof CKEDITOR !== "undefined"){
				for(var instanceName in CKEDITOR.instances){
					CKEDITOR.instances[instanceName].updateElement();
				}
			}

			var iframe_width;
			$preview.find('.preview-frame').each(function(){
				iframe_width = $('> iframe', this).css('width');

				$(this).addClass('ajax-loading').html('<i class="fa fa-spinner fa-spin fa-5x" style="color:#009afd"></i>')
			});

			$.ajax({
				url: requestUrl,
				type: 'POST',
				data: {
					data: $("#form-emailtemplate").serialize()
				},
				dataType: 'text',
				beforeSend: function() {
					$preview.find('.preview-frame').html('')
				},
				success: function(data) {
					if(data){
						var iframe = $preview.find('.preview-frame').html('<iframe></iframe>').children().get(0);
						if (iframe) {
							iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
							iframe.document.open();
							iframe.document.write(data);
							iframe.document.close();
						}

						$preview.removeClass('hide');

						$preview.find('.media-icon').removeClass('selected');
						$preview.find('.media-icon').eq(0).addClass('selected');

						if (typeof callback === 'function') {
							callback();
						}
					}
				},
				complete: function() {
					$preview.find('.preview-frame').removeClass('ajax-loading')
				},
				error: function(xhr, ajaxOptions, thrownError) {
					console.log(thrownError + " " + xhr.statusText + " " + xhr.responseText);
				}
			});

			$preview.find('.media-icon').off('click').click(function(){
				$(this).siblings().removeClass('selected');
				$(this).addClass('selected');
				$preview.find('.preview-frame > iframe').css('width', $(this).data('width'));
			});
		};

		var $preview;

		$('.btn-inbox-preview').one('click', function(e){
			e.preventDefault();

			var $this = $(this);
			$this.button('loading');

			$preview = $($this.data('target'));

			loadPreview($preview, function() {
				$this.button('reset').remove();
			});
		});

		$('.template-update').on('click', function(e){
			e.preventDefault();

			var $this = $(this);

			$this.find('.fa').addClass('fa-spin');

			loadPreview($preview, function() {
                $this.find('.fa').removeClass('fa-spin');
			});
		});

    /**
     * Preview Language switch bs.dropdown
     */
    $("#preview-language .dropdown-item").click(function (e) {
      e.preventDefault();

      $("#preview-language .active").removeClass('active');
      $(this).parent().addClass('active');

      $preview.data('language', $(this).data('language'));

      var $btn = $("#preview-language").prev();

      $btn.find('img').attr('title', $(this).find('span').text())
        .attr('src', $(this).find('img').attr('src'))

      // Update preview
      var $updateBtn = $preview.find('.template-update');
      $updateBtn.find('.fa').addClass('fa-spin');
      loadPreview($preview, function() {
        $updateBtn.find('.fa').removeClass('fa-spin');
      });
    })

		// Send test email ajax
		$('#send-test-email').click(function(e){
			e.preventDefault();
			var $icon = $(this);

			if (!$icon.is('i')) {
				$icon = $icon.find('i');
			}

			$icon.removeClass('fa-envelope');
			$icon.addClass('fa-refresh fa-spin');

      var requestUrl = 'index.php?route=extension/module/emailtemplate/send_email' + '&emailtemplate_id=' + $.getUrlParam('id') + '&user_token=' + $.getUrlParam('user_token');
      if (document.getElementById('store_id')){
        requestUrl += '&store_id=' + document.getElementById('store_id').value;
      }
      if ($preview.data('language')){
        requestUrl += '&language_id=' + $preview.data('language');
      }

			$.ajax({
				url: requestUrl,
				type: 'POST',
				data: {
					data: $("#form-emailtemplate").serialize()
				},
				complete: function() {
					$icon.removeClass('fa-refresh fa-spin');
					$icon.addClass('fa-envelope');
				},
				success: function(data) {
          			$('#send-test-email-modal').modal('hide');

					if (data['success']) {
						$('#form-emailtemplate > .alert').remove();
						$('#form-emailtemplate').prepend("<div class='alert alert-success'><i class='fa fa-check-circle'></i> " + data['success'] + "<button type='button' class='close' data-dismiss='alert'>&times;</button></div>");

						$('html, body').animate({
						  scrollTop: $("#form-emailtemplate").offset().top
						}, 'fast');
					}
				}
			});
		});

		var $model_frame = $('#modal-frame');

		var initModalFrame = function(e){
			e.preventDefault();

			var $el = $(this);

			$model_frame.find('.modal-content').html('');

			$model_frame.modal({
                backdrop: 'static',
                keyboard: false
            });

			switch($(this).data('modal')){
				case 'remote':
					$.ajax({
						url: $el.data('url'),
						type: 'GET',
						dataType: 'html',
						success: function(response_load) {
							if(response_load){
								$model_frame.find('.modal-content').html(response_load);

								var initEventHandlers = function() {
                                    $model_frame.find('[data-action=save]').off('click').click(function (e) {
                                        $.ajax({
                                            url: $el.data('url'),
                                            type: 'POST',
                                            data: $model_frame.find('.modal-content form').serialize(),
                                            success: function (response_save) {
                                                if (response_save['success']) {
                                                    $('#form-emailtemplate').prepend("<div class='alert alert-success'><i class='fa fa-check-circle'></i> " + response_save['success'] + "<button type='button' class='close' data-dismiss='alert'>&times;</button></div>");

                                                  $('html, body').animate({
                                                    scrollTop: $("#form-emailtemplate").offset().top
                                                  }, 'fast');

                                                    if ($el.data('refresh')) {
                                                        $.ajax({
                                                            url: $el.data('refresh-url') || window.location.href,
                                                            dataType: 'html',
                                                            success: function (response_html) {
                                                                $($el.data('refresh')).removeClass('ajax-loading').html($('<div />').html(response_html).find($el.data('refresh')).html())
                                                                    .find('[data-modal]').click(initModalFrame);

                                                                $model_frame.modal('hide');
                                                            }
                                                        });
                                                    } else {
                                                        $model_frame.modal('hide');
                                                    }

                                                    $model_frame.removeClass('show');

                                                    $model_frame.find('.modal-content').html('');
                                                } else if (typeof response_save === 'string' || response_save instanceof String) {
                                                    $model_frame.find('.modal-content').html(response_save);

                                                    initEventHandlers();
                                                } else {
                                                    $model_frame.modal('hide');
                                                }
                                            }
                                        });
                                        e.preventDefault();
                                    });
                                }

                                initEventHandlers();
							}
						}
					});
					break;
			}
		};

    $('#emailtemplate').on('click', '[data-modal]', initModalFrame);

		$('#emailtemplate_key_select').change(function() {
			var val = $(this).val();
			if (!val) return;

			var $placeholder = $('#emailtemplate_option_placeholder .ajax-loading');
			$placeholder.html('<i class="fa fa-spinner fa-spin fa-3x" style="color:#009afd"></i>');

			$.ajax({
				url: 'index.php?route=extension/module/emailtemplate/template_option&user_token=' + $.getUrlParam('user_token') + '&id=' + val,
				type: 'get',
				dataType: 'html',
				success: function(html) {
					$placeholder.html(html);
				}
			});
		}).change();

        var $shortcodes_list = $('#shortcodes-list');
        if ($shortcodes_list.length) {
            var currentShortcodesRequest;

            var loadShortcodes = function (shortcodes_url) {
                $shortcodes_list.addClass('ajax-loading').html('<i class="fa fa-spinner fa-spin fa-3x" style="color:#009afd"></i>');

                currentShortcodesRequest = $.ajax({
                    url: shortcodes_url || ('index.php?route=extension/module/emailtemplate/shortcodes&user_token=' + $.getUrlParam('user_token') + '&id=' + $.getUrlParam('id')),
                    type: 'post',
                    data: {
                        'filter_shortcodes_language': $('#filter_shortcodes_language').is(':checked') ? 1 : 0,
                        'filter_shortcodes_search': $('#filter_shortcodes_search').val()
                    },
                    beforeSend: function () {
                        if (currentShortcodesRequest != null) {
                            currentShortcodesRequest.abort();
                        }
                    },
                    dataType: 'html',
                    success: function (html) {
                        $shortcodes_list.removeClass('ajax-loading').html(html);

                        $shortcodes_list.find('a[data-target="#tab-shortcodes"]').on('show.bs.tab', function (e) {
                            loadShortcodes();
                        });

                        $shortcodes_list.find('.table thead a, .pagination a').on('click', function () {
                            loadShortcodes(this.getAttribute('href'));
                            return false;
                        });
                    }
                });
            }

            $('a[data-target="#tab-shortcodes"]').on('show.bs.tab', function (e) {
                loadShortcodes();
            });

            // Form filter
            $('#filter_shortcodes_search').on('keyup', function (e) {
                var keyCode = e.keyCode || e.which;

                // Disable enter submit
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }

                loadShortcodes();
            });

            $('#filter_shortcodes_language').on('change', function (e) {
                loadShortcodes();
            });
        }
	});

})(jQuery);
