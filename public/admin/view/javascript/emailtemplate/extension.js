(function($){
    var ajax_url = 'index.php?route=extension/module/emailtemplate/templates&user_token=' + $.getUrlParam('user_token');

    var ajax_filter_type = $.getUrlParam('filter_type');
    if (ajax_filter_type) {
        ajax_url += '&filter_type=' + encodeURIComponent(ajax_filter_type);
    }

    var ajax_filter_preference = $.getUrlParam('filter_preference');
    if (ajax_filter_preference) {
        ajax_url += '&filter_preference=' + encodeURIComponent(ajax_filter_preference);
    }

    var ajax_filter_content = $.getUrlParam('filter_content');

    var ajax_sort = $.getUrlParam('sort');
    if (ajax_sort) {
        ajax_url += '&sort=' + encodeURIComponent(ajax_sort);
    }

    var ajax_order = $.getUrlParam('order');
    if (ajax_order) {
        ajax_url += '&order=' + encodeURIComponent(ajax_order);
    }

    $.ajax({
        url: ajax_url,
        type: 'GET',
        dataType: 'html',
        success: function (html) {
            if (!html) return;

            var $form_templates = $('#form-emailtemplate');
            var $ajax_templates = $('#ajax-templates');

            $ajax_templates.html(html);

            $ajax_templates.on('click', '[data-update]', function () {
                var action = $(this).data('update');
                var key = $(this).parents('tr').data('key');

                if (action && key && window.confirm($(this).data("msg-confirm"))) {
                    $('#form-emailtemplate > .alert').remove();

                    $.ajax({
                        url: 'index.php?route=extension/module/emailtemplate/update&action=' + action + '&key=' + key + '&user_token=' + $.getUrlParam('user_token'),
                        type: 'GET',
                        dataType: 'json',
                        success: function (json) {
                            if (json) {
                                if (json['success']) {
                                    $form_templates.prepend("<div class='alert alert-success'><i class='fa fa-check-circle'></i> " + json['success'] + "<button type='button' class='close' data-dismiss='alert'>&times;</button></div>")
                                }

                                if (json['warning']) {
                                    $form_templates.prepend("<div class='alert alert-warning'><i class='fa fa-exclamation-circle'></i> " + json['warning'] + "<button type='button' class='close' data-dismiss='alert'>&times;</button></div>")
                                }

                                submitForm();
                            }
                        }
                    });
                }

                return false;
            });

            var callSubmitForm;
            var submitFormRequest;
            function clearSubmitForm () {
                if (callSubmitForm) {
                    clearTimeout(callSubmitForm);
                }
                if (submitFormRequest != null) {
                    submitFormRequest.abort();
                }
            }
            function submitForm(submit_form_url) {
                if (submit_form_url === undefined) {
                    submit_form_url = ajax_url
                }
                callSubmitForm = setTimeout(function() {
                    clearSubmitForm();

                    var filter_type = $('input[name=filter_type]:checked').val();

                    if (filter_type || filter_type != ajax_filter_type) {
                        ajax_filter_type = filter_type;
                        submit_form_url += '&filter_type=' + encodeURIComponent(filter_type);
                    }

                    var filter_content = $('input[name=filter_content]').val();

                    if (filter_content || filter_content != ajax_filter_content) {
                        ajax_filter_content = filter_content;
                        submit_form_url += '&filter_content=' + encodeURIComponent(filter_content);
                    }

                    var filter_preference = $('select[name=filter_preference] :selected').val();

                    if (filter_preference || filter_type != ajax_filter_preference) {
                        ajax_filter_preference = filter_preference;
                        submit_form_url += '&filter_preference=' + encodeURIComponent(filter_preference);
                    }

                    if ($.getUrlParam('sort', submit_form_url)) {
                        submit_form_url += '&sort=' + encodeURIComponent($.getUrlParam('sort', submit_form_url));
                    } else if (ajax_sort) {
                        submit_form_url += '&sort=' + encodeURIComponent(ajax_sort);
                    }

                    if ($.getUrlParam('order', submit_form_url)) {
                        submit_form_url += '&order=' + encodeURIComponent($.getUrlParam('order', submit_form_url));
                    } else if (ajax_order) {
                        submit_form_url += '&order=' + encodeURIComponent(ajax_order);
                    }

                    $form_templates.find('.ajax-filter').addClass('ajax-loading').html('<i class="fa fa-spinner fa-spin fa-5x" style="color:#009afd"></i>');

                    submitFormRequest = $.ajax({
                        url: submit_form_url,
                        type: 'GET',
                        dataType: 'html',
                        beforeSend: function () {
                            if (submitFormRequest != null) {
                                submitFormRequest.abort();
                            }
                        },
                        success: function (html) {
                            clearSubmitForm();

                            if (!html) return;

                            $ajax_templates.html(html);

                            initHandlers();
                        }
                    });
                }, 400)

                return false;
            }

            $form_templates.find('input[name=filter_type], select[name=filter_preference]').on('change', function () {
                submitForm();
            });

            $form_templates.find('input[name=filter_content]').on('keypress', function (e) {
                if (e.which == 13) {
                    return false;
                }
            }).on('keyup', function () {
                submitForm();
            });

            function initHandlers() {
                $ajax_templates.find('.table-row-check > tbody > tr').each(function () {
                    // Row click
                    $(this).on('click', function (e) {
                        $(this).find('> td:first-child input[type=checkbox], > td:last-child input[type=checkbox]').trigger('click').each(function () {
                            $(this).parents('tr').toggleClass('selected', this.checked)
                        });
                    });

                    // Checkbox
                    $(this).find('>td:first-child input[type=checkbox], >td:last-child input[type=checkbox]').click(function (e) {
                        e.stopPropagation();
                        $(this).parents('tr').toggleClass('selected', this.checked)
                    });

                    // Anchor
                    $(this).find('a').click(function (e) {
                        e.stopPropagation();
                    });
                });

                $ajax_templates.find('.table thead a, .pagination a').on('click', function(){
                    submitForm($(this).attr('href'));
                    return false;
                });

                $ajax_templates.find('.pagination select').on('change', function () {
                    submitForm(this.value);
                    return false;
                });
            }

            initHandlers();
        }
    });

    (function runCron() {
        var cronActive;
        var cronRequest;
        var cronTotal = 0;

        $('#run-cron').click(function () {
            var $btn = $(this);

            cronActive = true;

            var ajaxCall = function (url) {
                if (!cronActive) return false;

                $('#cancel-cron').removeClass('disabled')

                cronRequest = $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: $('#form-module').find(':input').serialize(),
                    beforeSend: function () {
                        if (cronRequest != null) {
                            cronRequest.abort();
                        }
                    },
                    success: function (json) {
                        if (json['total']) {
                            cronTotal += json['total'];
                        }

                        var message = json['success'] ? json['success'].replace('{TOTAL}', cronTotal) : '';

                        if ($('#cron-alert').length) {
                            $('#cron-alert').html('<i class="fa fa-tick"></i> ' + message);
                        } else {
                            $('#form-emailtemplate').prepend('<div class="alert alert-success" id="cron-alert"><i class="fa fa-tick"></i> ' + message + '</div>');

                            $('html, body').animate({
                              scrollTop: $("#form-emailtemplate").offset().top
                            }, 'fast');
                        }

                        if (json['next']) {
                            ajaxCall(json['next']);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(thrownError + " " + xhr.statusText + " " + xhr.responseText);
                    }
                });
            };

            ajaxCall($btn.data('href'));

            return false;
        });

        $('#cancel-cron').click(function () {
            $(this).addClass('disabled');

            cronActive = false;

            if (cronRequest != null) {
                cronRequest.abort();
            }
        });
    })(); //runCron()

})(jQuery);
