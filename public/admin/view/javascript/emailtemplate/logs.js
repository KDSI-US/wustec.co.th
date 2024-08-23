(function($){
    var ajax_url = 'index.php?route=extension/module/emailtemplate/fetch_logs&user_token=' + $.getUrlParam('user_token');

    var ajax_emailtemplate_id = $('select[name=\'filter_emailtemplate_id\']').val() || $.getUrlParam('filter_emailtemplate_id');
    if (ajax_emailtemplate_id) {
        ajax_url += '&filter_emailtemplate_id=' + encodeURIComponent(ajax_emailtemplate_id);
    }

    var ajax_emailtemplate_key = $.getUrlParam('filter_emailtemplate_key');
    if (ajax_emailtemplate_key) {
        ajax_url += '&filter_emailtemplate_key=' + encodeURIComponent(ajax_emailtemplate_key);
    }

    var ajax_store_id = $('select[name=\'filter_store_id\']').val() || $.getUrlParam('filter_store_id');
    if (ajax_store_id) {
        ajax_url += '&filter_store_id=' + encodeURIComponent(ajax_store_id);
    }

    var ajax_customer_id = $('input[name=\'filter_customer_id\']').val() || $.getUrlParam('filter_customer_id');
    if (ajax_customer_id) {
        ajax_url += '&filter_customer_id=' + encodeURIComponent(ajax_customer_id);
    }

    var ajax_filter_email = $('input[name=\'filter_email\']').val() || $.getUrlParam('filter_email');
    if (ajax_filter_email) {
        ajax_url += '&filter_email=' + encodeURIComponent(ajax_filter_email);
    }

    var ajax_sent = $('input[name=\'filter_sent\']:checked').val() || $.getUrlParam('filter_sent');
    if (ajax_sent) {
        ajax_url += '&filter_sent=' + encodeURIComponent(ajax_sent);
    }

    var ajax_sort = $.getUrlParam('sort');
    if (ajax_sort || $.getUrlParam('sort')) {
        ajax_url += '&sort=' + encodeURIComponent(ajax_sort);
    }

    var ajax_order = $.getUrlParam('order');
    if (ajax_order || $.getUrlParam('order')) {
        ajax_url += '&order=' + encodeURIComponent(ajax_order);
    }

    $.ajax({
        url: ajax_url,
        type: 'GET',
        dataType: 'html',
        success: function(html) {
            if (!html) return;

            var $form_templates = $('#form-logs');
            var $ajax_templates = $('#ajax-logs');

            $ajax_templates.html(html);

            function submitForm(submit_form_url) {
                if (!submit_form_url) submit_form_url = ajax_url;

                var filter_emailtemplate_id = $('select[name=\'filter_emailtemplate_id\']').val();
                if (filter_emailtemplate_id) {
                    ajax_emailtemplate_id = filter_emailtemplate_id;
                    submit_form_url += '&filter_emailtemplate_id=' + encodeURIComponent(filter_emailtemplate_id);
                }

                var filter_store_id = $('select[name=\'filter_store_id\']').val();
                if (filter_store_id !== '') {
                    ajax_store_id = filter_store_id;
                    submit_form_url += '&filter_store_id=' + encodeURIComponent(filter_store_id);
                }

                var filter_customer_id = $('input[name=\'filter_customer_id\']').val();
                if (filter_customer_id) {
                    ajax_customer_id = filter_customer_id;
                    submit_form_url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
                }

                var filter_email = $('input[name=\'filter_email\']').val();
                if (filter_email) {
                    ajax_filter_email = filter_email;
                    submit_form_url += '&filter_email=' + encodeURIComponent(filter_email);
                }

                var filter_sent = $('input[name=\'filter_sent\']:checked').val();
                if (filter_sent) {
                    ajax_sent = filter_sent;
                    submit_form_url += '&filter_sent=' + encodeURIComponent(filter_sent);
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

                $.ajax({
                    url: submit_form_url,
                    type: 'GET',
                    dataType: 'html',
                    success: function(html) {
                        if (!html) return;

                        $ajax_templates.html(html);

                        initHandlers();
                    }
                });

                return false;
            }

            function initHandlers() {
                $ajax_templates.find('.table-row-check > tbody > tr').each(function(){
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

                $ajax_templates.find('input[name=filter_sent], input[name=filter_customer_id], input[name=filter_email], select[name=filter_store_id], select[name=filter_emailtemplate_id]').on('change', function() {
                    return submitForm(ajax_url);
                });

                $ajax_templates.find('input[name=filter_customer_id]').on('keypress', function(e){
                    if (e.which == 13) {
                        submitForm(ajax_url);
                        return false;
                    }
                });

                $ajax_templates.find('input.input-autocomplete-customer').on('change', function(e){
                    if (this.value == '') {
                        $ajax_templates.find('input[name=filter_customer_id]').val(0);
                        submitForm(ajax_url);
                        return false;
                    }
                });

                $ajax_templates.find('.table thead a, .pagination a').on('click', function() {
                    return submitForm(this.getAttribute('href'));
                });

                $ajax_templates.find('.pagination select').removeAttr('onchange').on('change', function(){
                    return submitForm(this.value);
                    return true;
                });

                if ($.fn.autocomplete) {
                    $ajax_templates.find('.input-autocomplete-customer').each(function(){
                        var $el = $(this),
                            $field = $($el.data('field')),
                            $output = $($el.data('output'));

                        $el.autocomplete({
                            source: function(request, response) {
                                $el.after("<span class='input-group-addon input-autocomplete-loading'><span class='input-group-text'><i class='fa fa-circle-o-notch fa-spin'></i></span></span>");
                                $.ajax({
                                    url: ('index.php?route=customer/customer/autocomplete&user_token=' + $.getUrlParam('user_token') + '&filter_name=' + encodeURIComponent(request)),
                                    type: 'GET',
                                    dataType: 'json',
                                    complete: function(json) {
                                        $el.next('.input-autocomplete-loading').remove();
                                    },
                                    success: function(json) {
                                        response($.map(json, function(item) {
                                            return {
                                                label: item['name'],
                                                value: item['customer_id']
                                            }
                                        }));
                                    }
                                });
                            },
                            select: function(item) {
                              if (item['value']) {
                                $el.val(item['label']);
                                $field.val(item['value']).change();
                              }
                              $output.removeClass('hide').find('.list-group').append('<li class="list-group-item" data-id="' + item['value'] + '"><a href="javascript:void(0)" class="badge remove list-group-item-danger"><i class="fa fa-fw fa-times"></i></a> ' + item['label'] + '</li>');
                            }
                        });
                        return true;
                    });
                }

                $ajax_templates.find('[data-resend]').click(function() {
                    var resend_url = $(this).data('resend');
                    if (!resend_url) return;

                    var $btn = $(this);

                    $btn.tooltip('hide');

                    if (window.confirm($(this).data("msg-confirm"))) {
                        $btn.button('loading');

                        $form_templates.children('.alert').remove();

                        $.ajax({
                            url: resend_url,
                            type: 'GET',
                            dataType: 'json',
                            complete: function() {
                                $btn.button('reset');
                            },
                            success: function(json) {
                                if (json) {
                                    if (json['success']) {
                                        $form_templates.prepend("<div class='alert alert-success'><i class='fa fa-check-circle'></i> " + json['success'] + "<button type='button' class='close' data-dismiss='alert'>&times;</button></div>")
                                    }

                                    if (json['warning']) {
                                        $form_templates.prepend("<div class='alert alert-warning'><i class='fa fa-exclamation-circle'></i> " + json['warning'] + "<button type='button' class='close' data-dismiss='alert'>&times;</button></div>")
                                    }

                                    submitForm(ajax_url);
                                }

                                document.body.scrollTop = document.documentElement.scrollTop = 0;
                            }
                        });
                    }

                    return false;
                });

                var $emailBox = $('#emailBox');

                var iframe = document.getElementById('emailBoxFrame');
                if (iframe) {
                    iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
                }

                $ajax_templates.find('.load-email').click(function (e) {
                    var $btn = $(this);
                    var $row = $btn.parents('tr');

                    if ($row.hasClass('active')) return;

                    $btn.button('loading');
                    $btn.tooltip('hide');

                    $row.siblings().removeClass('active');
                    $row.addClass('active');

                    $emailBox.addClass('loading');

                    var id = $(this).parents('tr').data('id');
                    if (!id) return false;

                    $emailBox.data('id', id);

                    $.ajax({
                        url: 'index.php?route=extension/module/emailtemplate/fetch_log&user_token=' + $.getUrlParam('user_token') + '&id=' + id,
                        dataType: 'json',
                        success: function (json) {
                            $btn.button('reset');

                            if (json) {
                                for (var key in json) {
                                    var $field = $emailBox.find('[data-field=' + key + ']');

                                    if ($field && json[key]) {
                                        if ($field.data('type') == 'mailto') {
                                            $field.attr('href', 'mailto:' + json[key] + '?subject=' + json['subject']);
                                        }

                                        $field.html(json[key]);

                                        if ($field.parent().hasClass('hide')) {
                                            $field.parent().show();
                                        }
                                    }
                                }

                                $emailBox.find('[data-field=mailto]').attr('href', 'mailto:' + json['to'] + '?subject=' + json['subject']);

                                var $preview_resend = $emailBox.find('[data-resend]');

                                if (json['resend']) {
                                    $emailBox.find('[data-resend]').data('resend', json['resend'].replace(/&amp;/g, '&'));
                                    $preview_resend.show();
                                } else {
                                    $preview_resend.hide();
                                }

                                iframe.document.open();
                                iframe.document.write(json['html']);
                                iframe.document.close();
                            }

                            if ($emailBox.hasClass("hide")) {
                                $emailBox.removeClass("hide");

                                $("html, body").animate({scrollTop: ($emailBox.offset().top - 10)}, 500, "linear");
                            }

                            $emailBox.removeClass('loading');
                        }
                    });

                    e.stopPropagation();
                });
            }

            initHandlers();
        }
    });
})(jQuery);
