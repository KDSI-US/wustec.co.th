(function ($) {
    $(document).ready(function () {
        if ($.fn.colorpicker) {
            $('.input-colorpicker').each(function () {
                var $this = $(this);
                $this.colorpicker({
                    'input': 'input[type=text]'
                });
                var $input = $(this).find('input[type=text]');
                $input.attr('data-value', $input.val());
                $input.on('change', function () {
                    if ($input.val() !== $input.attr('data-value')) {
                        $input.attr('data-value', $input.val());
                    }
                }).on('focus', function () {
                    if ($input.val() == '') {
                        $this.colorpicker('show');
                    }
                });
            });
        }

        if ($.fn.autocomplete) {
            $('.input-autocomplete-product').each(function () {
                var $el = $(this),
                    $group = $el.parents('.input-group'),
                    $field = $($el.data('field')),
                    $output = $($el.data('output'));

                $el.autocomplete({
                    source: function (request, response) {
                        $group.find('.input-group-addon .fa').attr("class", "fa fa-fw fa-circle-o-notch fa-spin").addClass('input-autocomplete-loading');
                        $.ajax({
                            url: ('index.php?route=catalog/product/autocomplete&user_token=' + $.getUrlParam('user_token') + '&filter_name=' + encodeURIComponent(request)),
                            type: 'GET',
                            dataType: 'json',
                            success: function (json) {
                                response($.map(json, function (item) {
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
                    select: function (item) {
                        if ($field.val() == '') {
                            $field.val(item['value']);
                        } else {
                            var selection = $field.val().split(',');
                            if ($.inArray(item['value'], selection) == -1) {
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

        // Collapse custom showcase products
        $('#emailtemplate_config_showcase').on('change', function () {
            $('.collapse-showcase').collapse(this.value == 'products' ? 'show' : 'hide');
        });

        // Remove showcase product
        $('#emailtemplate_config_showcase_output').on('click', '.remove', function () {
            var $item = $(this).parents('li');
            var $field = $('input[name=emailtemplate_config_showcase_selection]');

            var values = $.map($field.val().split(','), function (value) {
                return parseInt(value, 10)
            });
            var index = $.inArray($item.data('id'), values);
            if (index !== -1) {
                values.splice(index, 1);
            }
            $field.val(values.join(','));

            $item.remove();
        });

        // Send test email ajax
        $('#send-test-email').click(function (e) {
            var $btn = $(this);

            $btn.button('loading');

            var requestUrl = 'index.php?route=extension/module/emailtemplate/send_email' + '&emailtemplate_config_id=' + $.getUrlParam('id') + '&user_token=' + $.getUrlParam('user_token');
            if (document.getElementById('store_id')) {
                requestUrl += '&store_id=' + document.getElementById('store_id').value;
            }
            if ($preview.data('language')) {
                requestUrl += '&language_id=' + $preview.data('language');
            }

            $.ajax({
                url: requestUrl,
                type: 'POST',
                data: {
                    send_test_email: document.getElementById('field-send-test-email').value,
                    data: $("#form-emailtemplate").serialize()
                },
                success: function (data) {
                    $btn.button('reset');

                    $('#send-test-email-modal').modal('hide');

                    if (data['success']) {
                        $('#form-emailtemplate > .alert').remove();
                        $('#form-emailtemplate').prepend("<div class='alert alert-success'><i class='fa fa-check-circle'></i> " + data['success'] + "<button type='button' class='close' data-dismiss='alert'>&times;</button></div>");

                        $('html, body').animate({
                            scrollTop: $("#form-emailtemplate").offset().top
                        }, 'fast');

                        $('html, body').animate({
                            scrollTop: $("#form-emailtemplate").offset().top
                        }, 'fast');
                    }
                }
            });

            return false;
        });

        // Preview
        var $preview = $('#preview-mail');
        if ($preview.length) {
            var requestData = {};

            requestData['emailtemplate_config_id'] = $.getUrlParam('id');

            requestData['emailtemplate_id'] = 1;

            if (document.getElementById('store_id')) {
                requestData['store_id'] = document.getElementById('store_id').value;
            } else {
                requestData['store_id'] = 0;
            }

            if (document.getElementById('language_id')) {
                requestData['language_id'] = document.getElementById('language_id').value;
            }

            // OnLoad fetch preview
            $.ajax({
                url: ('index.php?route=extension/module/emailtemplate/preview_email&user_token=' + $.getUrlParam('user_token')),
                type: 'GET',
                dataType: 'text',
                data: requestData,
                success: function (data) {
                    if (data) {
                        var iframe = $preview.find('#preview-with').html('<iframe></iframe>').children().get(0);
                        if (iframe) {
                            iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
                            iframe.document.open();
                            iframe.document.write(data);
                            iframe.document.close();
                        }

                        var $src = $($preview.find('#preview-with > iframe').contents().find("html:eq(0)").clone());

                        $src.find("img").removeAttr("src");
                        $src.find("table,td,div").css("backgroundImage", "").removeAttr("background");

                        var iframe = $preview.find('#preview-without').html('<iframe></iframe>').children().get(0);
                        if (iframe) {
                            iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
                            iframe.document.open();
                            iframe.document.write($src.html());
                            iframe.document.close();
                        }
                    }
                },
                complete: function() {
                    $preview.find('#preview-with, #preview-without').removeClass('ajax-loading');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(thrownError + " " + xhr.statusText + " " + xhr.responseText);
                }
            });

            $preview.find('.media-icon').click(function () {
                $(this).siblings().removeClass('selected');
                $(this).addClass('selected');
                $preview.find('.preview-frame > iframe').css('width', $(this).data('width'));
            });

            $preview.find('.preview-image').click(function () {
                var $el = $(this);

                if ($el.hasClass('preview-no-image')) {
                    // With Image
                    $preview.find('#preview-without').hide();
                    $preview.find('#preview-with').show();

                    $el.addClass('hide');
                    $el.prev().removeClass('hide');
                } else {
                    // Without Image
                    $preview.find('#preview-without:eq(0)').each(function () {
                        if ($(this).is(':empty')) {
                            var $src = $($preview.find('#preview-with > iframe').contents().find("html:eq(0)").clone());

                            $src.find("img").removeAttr("src");
                            $src.find("table,td,div").css("backgroundImage", "").removeAttr("background");

                            var iframe = $(this).html('<iframe style="width:100%"></iframe>').children().get(0);
                            if (iframe) {
                                iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
                                iframe.document.open();
                                iframe.document.write($src.html());
                                iframe.document.close();
                            }
                        }
                        $preview.find('#preview-with').hide();
                        $(this).show();
                    });

                    $el.addClass('hide');
                    $el.next().removeClass('hide');
                }
            });

            /**
             * Preview
             */
            function loadPreview($preview, callback) {
                var iframe_width;
                $preview.find('.preview-frame').each(function () {
                    iframe_width = $('> iframe', this).css('width');

                    $(this).addClass('ajax-loading').html('<i class="fa fa-spinner fa-spin fa-5x" style="color:#009afd"></i>')
                });

                var requestUrl = 'index.php?route=extension/module/emailtemplate/preview_email&user_token=' + $.getUrlParam('user_token') + '&emailtemplate_config_id=' + $.getUrlParam('id') + '&emailtemplate_id=1';

                if (document.getElementById('store_id')) {
                    requestUrl += '&store_id=' + document.getElementById('store_id').value;
                }

                if (document.getElementById('language_id')) {
                    requestUrl += '&language_id=' + document.getElementById('language_id').value;
                } else if ($preview.data('language')) {
                    requestUrl += '&language_id=' + $preview.data('language');
                }

                $.ajax({
                    url: requestUrl,
                    type: 'post',
                    dataType: 'text',
                    data: {
                        'data': $("#form-emailtemplate").serialize()
                    },
                    success: function (data) {
                        if (data) {
                            var iframe = $preview.find('#preview-with').html('<iframe ' + (iframe_width ? ('style="width:' + iframe_width) : '') + '"></iframe>').contents().get(0);
                            if (iframe) {
                                iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
                                iframe.document.open();
                                iframe.document.write(data);
                                iframe.document.close();
                            }

                            var $src = $($preview.find('#preview-with > iframe').contents().find("html:eq(0)").clone());

                            $src.find("img").removeAttr("src");
                            $src.find("table,td,div").css("backgroundImage", "").removeAttr("background");

                            var iframe = $preview.find('#preview-without').html('<iframe style="width:' + iframe_width + '"></iframe>').contents().get(0);
                            if (iframe) {
                                iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
                                iframe.document.open();
                                iframe.document.write($src.html());
                                iframe.document.close();
                            }

                            if (typeof callback === 'function') {
                                callback();
                            }
                        }
                    },
                    complete: function() {
                        $preview.find('#preview-with, #preview-without').removeClass('ajax-loading');
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(thrownError + " " + xhr.statusText + " " + xhr.responseText);
                    }
                });
            }

            $preview.find('.template-update').click(function (e) {
                e.preventDefault();

                var $this = $(this);

                $this.find('.fa').addClass('fa-spin');

                loadPreview($preview, function () {
                    $this.find('.fa').removeClass('fa-spin');
                });
            });
        }

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

            loadPreview($preview, function () {
                $updateBtn.find('.fa').removeClass('fa-spin');
            });
        })

        $('.controls-page-shadow .form-check-input').on('change', function (e) {
            if (this.value == 'combine-shadow') {
                $('#fieldset-combine-shadow').collapse('show');
                $('#fieldset-box-shadow').collapse('hide');
            } else if (this.value == 'box-shadow') {
                $('#fieldset-combine-shadow').collapse('hide');
                $('#fieldset-box-shadow').collapse('show');
            } else {
                $('#fieldset-combine-shadow, #fieldset-box-shadow').collapse('hide');
            }
        })
    });

})(jQuery);
