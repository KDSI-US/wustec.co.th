$(document).ready(function() {
/********************** Account Options Buttons Starts **********************/
$('#mp-checkout .accountoption-button').click(function() {

	var accountoption_value = $(this).find('input').val();
	if(accountoption_value == 'register') {
		// Signup Display
		$('#mp-checkout .account-signup').removeClass('hide');

		// Login Hide
		$('#mp-checkout .account-login').addClass('hide');

		// Social Media Display
		$('#mp-checkout .mp-social-logins').removeClass('hide');
	} else if(accountoption_value == 'guest') {
		// Signup Display
		$('#mp-checkout .account-signup').removeClass('hide');

		// Login Hide
		$('#mp-checkout .account-login').addClass('hide');

		// Social Media Display
		$('#mp-checkout .mp-social-logins').addClass('hide');
	} else if(accountoption_value == 'login') {
		// Signup Hide
		$('#mp-checkout .account-signup').addClass('hide');

		// Login Display
		$('#mp-checkout .account-login').removeClass('hide');

		// Social Media Display
		$('#mp-checkout .mp-social-logins').removeClass('hide');
	}



	if(accountoption_value == 'register') {
		// Signup Fields Display
		$('#mp-checkout .signup-click-me').removeClass('hide');
		$('#mp-checkout .guest-click-me').addClass('hide');
	} else if(accountoption_value == 'guest') {
		$('#mp-checkout .signup-click-me').addClass('hide');
		$('#mp-checkout .guest-click-me').removeClass('hide');
	}

});
/********************** Account Options Buttons Ends **********************/

/********************** Same Shipping Starts **********************/
$('#mp-checkout input[name=\'same_address\']').change(function() {
	if($(this).prop('checked')) {
		$('#mp-checkout .shipping-addresses').slideUp();

		MPSHIPPINGMETHODS.refresh(true);
	} else {
		$('#mp-checkout .shipping-addresses').slideDown();

		// Calling Refresh Shipping Address
		// var customer_group_id = $('input[name=\'signup[customer_group_id]\']:checked').val();
		var same_address = $('input[name=\'same_address\']:checked').val();
		MPSHIPPINGADDRESS.refresh(same_address);
	}
});
/********************** Same Shipping Ends **********************/

/********************** Custom Fields Starts **********************/
$('.signup-panel .form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('.signup-panel .form-group').length) {
		$('.signup-panel .form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('.signup-panel .form-group').length) {
		$('.signup-panel .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') == $('.signup-panel .form-group').length) {
		$('.signup-panel .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('.signup-panel .form-group').length) {
		$('.signup-panel .form-group:first').before(this);
	}
});

$('.signup-panel input[name=\'signup[customer_group_id]\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=mpcheckout/checkout/customfield&customer_group_id=' + this.value,
		dataType: 'json',
		success: function(json) {
			$('.signup-panel .custom-field').hide();
			$('.signup-panel .custom-field').removeClass('required');

			for (i = 0; i < json.length; i++) {
				custom_field = json[i];

				$('#signup-custom-field' + custom_field['custom_field_id']).show();

				if (custom_field['required']) {
					$('#signup-custom-field' + custom_field['custom_field_id']).addClass('required');
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('.signup-panel input[name=\'signup[customer_group_id]\']:checked').trigger('change');

$('.signup-panel button[id^=\'button-signup-custom-field\']').on('click', function() {
	var node = this;

	$('#signup-form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="signup-form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#signup-form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#signup-form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#signup-form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
					$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
					$('.mpdisable').removeClass('hide');
					$('.comment-panel').addClass('blur');
				},
				complete: function() {
					$(node).button('reset');

				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input[name^=\'signup[custom_field]\']').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input[name^=\'signup[custom_field]\']').val(json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$('.payment-address-panel button[id^=\'button-payment-custom-field\']').on('click', function() {
	var node = this;

	$('#payment-address-form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="payment-address-form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#payment-address-form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#payment-address-form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#payment-address-form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
					$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
					$('.mpdisable').removeClass('hide');
					$('.comment-panel').addClass('blur');
				},
				complete: function() {
					$(node).button('reset');

				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input[name^=\'payment_address[custom_field]\']').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input[name^=\'payment_address[custom_field]\']').val(json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$(document).delegate('.shipping-address-panel button[id^=\'button-shipping-custom-field\']', 'click', function() {
	var node = this;

	$('#shipping-address-form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="shipping-address-form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#shipping-address-form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#shipping-address-form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#shipping-address-form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
					$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
					$('.mpdisable').removeClass('hide');
					$('.comment-panel').addClass('blur');
				},
				complete: function() {
					$(node).button('reset');

				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input[name^=\'shipping_address[custom_field]\']').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input[name^=\'shipping_address[custom_field]\']').val(json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$('.payment-address-panel .form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('.payment-address-panel .form-group').length) {
		$('.payment-address-panel .form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('.payment-address-panel .form-group').length) {
		$('.payment-address-panel .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') == $('.payment-address-panel .form-group').length) {
		$('.payment-address-panel .form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('.payment-address-panel .form-group').length) {
		$('.payment-address-panel .form-group:first').before(this);
	}
});
/********************** Custom Fields Ends **********************/

/********************** Signup Starts **********************/
$('select[name=\'signup[country_id]\']').on('change', function() {
	var text_select = $(this).attr('data-select');
	var text_none = $(this).attr('data-none');
	var zone_id = $(this).attr('data-zone');

	$.ajax({
		url: 'index.php?route=mpcheckout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'signup[zone_id]\']').attr('disabled', true);
			$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
			$('.mpdisable').removeClass('hide');
			$('.comment-panel').addClass('blur');
		},
		complete: function() {
			$('select[name=\'signup[zone_id]\']').attr('disabled', false);

		},
		success: function(json) {
			html = '<option value="">'+ text_select +'</option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == zone_id) {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected">'+ text_none +'</option>';
			}

			$('select[name=\'signup[zone_id]\']').html(html);

			MPPAYMENTMETHODS.refresh(true, function() {

			});

			var same_address = $('input[name=\'same_address\']:checked').val();
				if(same_address) {
					MPSHIPPINGMETHODS.refresh(true);
				}


		}
	});
});
$('select[name=\'signup[country_id]\']').trigger('change');

// Zone
$(document).delegate('select[name=\'signup[zone_id]\']', 'change', function() {
	MPPAYMENTMETHODS.refresh(true);
	MPSHIPPINGMETHODS.refresh(true);
});

$('input[name=\'signup[customer_group_id]\']').on('change', function() {
	var customer_group_id = $('input[name=\'signup[customer_group_id]\']:checked').val();

	var same_address = $('input[name=\'same_address\']:checked').val();

});

$('input[name=\'signup[customer_group_id]\']').on('click', function() {
	var customer_group_id = $('input[name=\'signup[customer_group_id]\']:checked').val();

	var same_address = $('input[name=\'same_address\']:checked').val();

	// Update Session customer group ID refresh
	MPSHOPPINGCART.refresh(true);
	 setTimeout(function(){
		MPSHOPPINGCART.refresh(true);
	 }, 3000);
});

$('select[name=\'signup[customer_group_id]\']').trigger('change');
/********************** Signup Ends **********************/

/********************** Payment Address Starts **********************/
$('#mp-checkout input[name=\'payment_address[payment_address]\']').on('change', function() {
	if (this.value == 'new') {
		$('#mp-checkout #payment-existing').hide();
		$('#mp-checkout #payment-new').show();
	} else {
		$('#mp-checkout #payment-existing').show();
		$('#mp-checkout #payment-new').hide();
	}

	MPPAYMENTMETHODS.refresh(true);

	var same_address = $('input[name=\'same_address\']:checked').val();
	if(same_address) {
		MPSHIPPINGMETHODS.refresh(true);
	}
});

$(document).delegate('select[name=\'payment_address[address_id]\']', 'change', function() {
	MPPAYMENTMETHODS.refresh(true);

	var same_address = $('input[name=\'same_address\']:checked').val();
	if(same_address) {
		MPSHIPPINGMETHODS.refresh(true);
	}
});

$(document).delegate('select[name=\'payment_address[country_id]\']', 'change', function() {
	var text_select = $(this).attr('data-select');
	var text_none = $(this).attr('data-none');
	var zone_id = $(this).attr('data-zone');

	$.ajax({
		url: 'index.php?route=mpcheckout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'payment_address[zone_id]\']').attr('disabled', true);
			$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
			$('.mpdisable').removeClass('hide');
			$('.comment-panel').addClass('blur');
		},
		complete: function() {
			$('select[name=\'payment_address[zone_id]\']').attr('disabled', false);

		},
		success: function(json) {
			html = '<option value="">'+ text_select +'</option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == zone_id) {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected">'+ text_none +'</option>';
			}

			$('select[name=\'payment_address[zone_id]\']').html(html);

			MPPAYMENTMETHODS.refresh(true);

			var same_address = $('input[name=\'same_address\']:checked').val();
			if(same_address) {
				MPSHIPPINGMETHODS.refresh(true);
			}
		}
	});
});
$('select[name=\'payment_address[country_id]\']').trigger('change');

// Zone
$(document).delegate('select[name=\'payment_address[zone_id]\']', 'change', function() {
	MPPAYMENTMETHODS.refresh(true);

	var same_address = $('input[name=\'same_address\']:checked').val();
	if(same_address) {
		MPSHIPPINGMETHODS.refresh(true);
	}
});
/********************** Payment Address Ends **********************/

/********************** Shipping Address Starts **********************/
$(document).delegate('#mp-checkout input[name=\'shipping_address[shipping_address]\']', 'change', function() {
	if (this.value == 'new') {
		$('#mp-checkout #shipping-existing').hide();
		$('#mp-checkout #shipping-new').show();
	} else {
		$('#mp-checkout #shipping-existing').show();
		$('#mp-checkout #shipping-new').hide();
	}

	MPSHIPPINGMETHODS.refresh(true);
});

$(document).delegate('select[name=\'shipping_address[address_id]\']', 'change', function() {
	MPSHIPPINGMETHODS.refresh(true);
});

$(document).delegate('select[name=\'shipping_address[country_id]\']', 'change', function() {
	var text_select = $(this).attr('data-select');
	var text_none = $(this).attr('data-none');
	var zone_id = $(this).attr('data-zone');

	$.ajax({
		url: 'index.php?route=mpcheckout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'shipping_address[zone_id]\']').attr('disabled', true);
			$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
			$('.mpdisable').removeClass('hide');
			$('.comment-panel').addClass('blur');
		},
		complete: function() {
			$('select[name=\'shipping_address[zone_id]\']').attr('disabled', false);

		},
		success: function(json) {
			html = '<option value="">'+ text_select +'</option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == zone_id) {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected">'+ text_none +'</option>';
			}

			$('select[name=\'shipping_address[zone_id]\']').html(html);

			MPSHIPPINGMETHODS.refresh(true);
		}
	});
});

$('select[name=\'shipping_address[country_id]\']').trigger('change');

// Zone
$(document).delegate('select[name=\'shipping_address[zone_id]\']', 'change', function() {
	MPSHIPPINGMETHODS.refresh(true);
});

// Post Code
$(document).delegate('input[name=\'signup[postcode]\']', 'change', function() {
	var same_address = $('input[name=\'same_address\']:checked').val();
	if(same_address) {
		MPSHIPPINGMETHODS.refresh(true);
	}
});
$(document).delegate('input[name=\'payment_address[postcode]\']', 'change', function() {
	var same_address = $('input[name=\'same_address\']:checked').val();
	if(same_address) {
		MPSHIPPINGMETHODS.refresh(true);
	}
});

$(document).delegate('input[name=\'shipping_address[postcode]\']', 'change', function() {
	MPSHIPPINGMETHODS.refresh(true);
});
/********************** Shipping Address Ends **********************/

/********************** Shopping Cart Starts **********************/
// Increment Decrement QTY
$(document).on('click', '.increment-decrement button', function () {
	var button = $(this),
	oldValue = button.closest('.increment-decrement').find('input').val().trim(),
	qty_new = 1;

	if (button.attr('data-action') == 'plus') {
		qty_new = parseInt(oldValue) + 1;
	} else if (button.attr('data-action') == 'minus') {
		if (oldValue > 1) {
			qty_new = parseInt(oldValue) - 1;
		} else {
			qty_new = 1;
		}
	}
	button.closest('.increment-decrement').find('input').val(qty_new);

	// Calling Edit & Refresh Shopping Cart
	MPSHOPPINGCART.editrefresh($(this).attr('data-key'), qty_new);
});

$(document).on('change', '.cart-input-qty', function () {
	// Calling Edit & Refresh Shopping Cart
	MPSHOPPINGCART.editrefresh($(this).attr('data-key'), $(this).val());
});
/********************** Shopping Cart Ends **********************/

/********************** Shipping Method Starts **********************/
$(document).delegate('#mp-checkout input[name=\'shipping_method\']', 'change', function() {
	// also need cart rehresh
	var need_cart_refresh = true;

	// Save Shipping method calling
	MPSHIPPINGMETHODS.save(need_cart_refresh);
});
/********************** Shipping Method Ends **********************/

/********************** Payment Method Starts **********************/
$(document).delegate('#mp-checkout input[name=\'payment_method\']', 'change', function() {
	// also need cart rehresh
	var need_cart_refresh = true;

	MPPAYMENTMETHODS.save(need_cart_refresh);
});
/********************** Payment Method Ends **********************/

/********************** Login Starts **********************/
$('.login-panel input[name=\'email\'], .login-panel input[name=\'password\']').on("keydown", function (e) {
 	if (e.keyCode === 13) {
        $('#mp-checkout #button-login').trigger('click');
    }
});

$('#mp-checkout #button-login').on('click', function() {
	$.ajax({
        url: 'index.php?route=mpcheckout/login/save',
        type: 'post',
        data: $('.login-panel :input'),
        dataType: 'json',
        beforeSend: function() {
        	$('#mp-checkout #button-login').button('loading');
        	$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
        	$('.mpdisable').removeClass('hide');
        	$('.comment-panel').addClass('blur');
		},
        complete: function() {
            $('#mp-checkout #button-login').button('reset');

        },
        success: function(json) {
            $('.login-panel .alert, .login-panel .text-danger').remove();
            $('.login-panel .form-group').removeClass('has-error');

            if (json['redirect']) {
                location = json['redirect'];
            } else if (json['error']) {
                $('.login-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

				// Highlight any found errors
				$('.login-panel input[name=\'email\']').parent().addClass('has-error');
				$('.login-panel input[name=\'password\']').parent().addClass('has-error');

				$('.login-panel input[name=\'email\']').focus();
		   }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
/********************** Login Ends **********************/


/********************** Checkout Buttons Starts **********************/
$(document).delegate('#mp-checkout #button-checkout', 'click', function() {
	MPCHECKOUTBUTTON.createValidOrder();
});
/********************** Checkout Buttons Ends **********************/


/********************** Date Time Starts **********************/
$('.date').datetimepicker({
	pickTime: false
});

$('.time').datetimepicker({
	pickDate: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});
/********************** Date Time Ends **********************/
/********************** Session Agree Starts **********************/
$(document).delegate('.comment-panel input[name=\'agree\']', 'change', function() {
	var agree_session = $(this).prop('checked');
	if(agree_session == true) {
		var agree_session = 1;
	} else{
		var agree_session = '';
	}

	/*$.ajax({
		url: 'index.php?route=mpcheckout/checkout_button/saveAgreeSession',
		type: 'post',
		data: 'agree_session='+ agree_session,
		dataType: 'json',
		beforeSend: function() {
		},
		complete: function() {
		},
		success: function(json) {

		}
	});*/
});

$(document).delegate('.comment-panel textarea[name=\'comment\']', 'change', function() {
	var comment_session = $('.comment-panel textarea[name=\'comment\']').val();
	$.ajax({
		url: 'index.php?route=mpcheckout/checkout_button/saveCommentSession',
		type: 'post',
		data: 'comment_session='+ comment_session,
		dataType: 'json',
		beforeSend: function() {
		},
		complete: function() {
		},
		success: function(json) {

		}
	});
});
/********************** Session Agree Ends **********************/

});

var ship_session = '';

/********************** Shopping Cart Starts **********************/
// Declaration Edit & Refresh Shopping Cart
var MPSHOPPINGCART = {
	'refresh': function(need_methods_refresh) {
		need_methods_refresh = typeof need_methods_refresh != 'undefined' ? need_methods_refresh : false;

		var post_data = $('input[name=\'signup[customer_group_id]\']:checked, .accountoption-panel input[name=\'accountoption\']:checked, .signup-panel input[type=\'radio\']:checked').serialize();

		if(ship_session) {
			post_data += '&ship_session=' + JSON.stringify(ship_session);
		}

		$.ajax({
			url: 'index.php?route=mpcheckout/shoppingcart/refresh',
			type: 'post',
			dataType: 'json',
			data: post_data,
			beforeSend: function() {
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {
			},
			success: function(json) {
				if(json['redirect']) {
					location = json['redirect'];
				} else {
					if(json['html']) {
						$('#mp-checkout .shoppingcart').html(json['html']);
					}

					// For Forcefully trigger if shipping required yes or no...
					var same_address = $('input[name=\'same_address\']:checked').val();
					if(json['shipping_required']) {
						if(!same_address) {
							$('#mp-checkout .shipping-addresses').slideDown();
						}

						$('#mp-checkout .sameasaddress').removeClass('hide');
						$('.norequire_saddress').remove();

						$('.delivery_date_status').removeClass('hide');
					} else {
						$('#mp-checkout .shipping-addresses').slideUp();
						$('#mp-checkout .sameasaddress').addClass('hide');

						$('.norequire_saddress').remove();
						$('#mp-checkout .sameasaddress').after('<p class="norequire_saddress"><i class="fa fa-bell-o" aria-hidden="true"></i> <strong>'+ json['text_norequire_saddress'] +'</strong></p>');

						$('.delivery_date_status').addClass('hide');
					}

					$('#cart > ul').load('index.php?route=common/cart/info ul li');

					/*if(need_methods_refresh) {*/
						// Refresh Shipping Methods
						MPSHIPPINGMETHODS.refresh();

						// Refresh Payment Methods
						MPPAYMENTMETHODS.refresh();
					/*}*/

					// Refresh Checkout Button
				 	// MPCHECKOUTBUTTON.refresh();
				}
			}
		});
	},
	'editrefresh': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=mpcheckout/shoppingcart/editrefresh',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
				$('.shoppingcart-loader').html('<div class="loader-wrap"><div class="cssload-box-loading"></div><i class="fa fa-bolt" aria-hidden="true"></i></div><div class="loader-overlay"></div>');
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {
				$('#cart > button').button('reset');
				$('.shoppingcart-loader').html('');

			},
			success: function(json) {
				if(json['redirect']) {
					location = json['redirect'];
				} else {

					if(json['html']) {
						$('#mp-checkout .shoppingcart').html(json['html']);
					}

					// For Forcefully trigger if shipping required yes or no...
					var same_address = $('input[name=\'same_address\']:checked').val();
					if(json['shipping_required']) {
						if(!same_address) {
							$('#mp-checkout .shipping-addresses').slideDown();
						}

						$('#mp-checkout .sameasaddress').removeClass('hide');
						$('.norequire_saddress').remove();

						$('.delivery_date_status').removeClass('hide');
					} else{
						$('#mp-checkout .shipping-addresses').slideUp();
						$('#mp-checkout .sameasaddress').addClass('hide');

						$('.norequire_saddress').remove();
						$('#mp-checkout .sameasaddress').after('<p class="norequire_saddress"><i class="fa fa-bell-o" aria-hidden="true"></i> <strong>'+ json['text_norequire_saddress'] +'</strong></p>');

						$('.delivery_date_status').addClass('hide');
					}

					setTimeout(function () {
						if(typeof Journal != 'undefined') {
							$('#cart-total').html(json['total']);
						} else {
							$('#cart > button').html('<i class="fa fa-shopping-bag"></i> <span id="cart-total">' + json['total'] + '</span>');
						}
					}, 100);

					$('#cart > ul').load('index.php?route=common/cart/info ul li');

					// Refresh Shipping Methods
					MPSHIPPINGMETHODS.refresh(true);

					// Refresh Payment Methods
					MPPAYMENTMETHODS.refresh();
				}

			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'removerefresh': function(key) {
		$.ajax({
			url: 'index.php?route=mpcheckout/shoppingcart/removerefresh',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
				$('.shoppingcart-loader').html('<div class="loader-wrap"><div class="cssload-box-loading"></div><i class="fa fa-bolt" aria-hidden="true"></i></div><div class="loader-overlay"></div>');
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {
				$('#cart > button').button('reset');
				$('.shoppingcart-loader').html('');

			},
			success: function(json) {
				if(json['redirect']) {
					location = json['redirect'];
				} else {
					if(json['html']) {
						$('#mp-checkout .shoppingcart').html(json['html']);
					}

					// For Forcefully trigger if shipping required yes or no...
					var same_address = $('input[name=\'same_address\']:checked').val();
					if(json['shipping_required']) {
						if(!same_address) {
							$('#mp-checkout .shipping-addresses').slideDown();
						}

						$('#mp-checkout .sameasaddress').removeClass('hide');
						$('.norequire_saddress').remove();

						$('.delivery_date_status').removeClass('hide');
					} else{
						$('#mp-checkout .shipping-addresses').slideUp();
						$('#mp-checkout .sameasaddress').addClass('hide');

						$('.norequire_saddress').remove();
						$('#mp-checkout .sameasaddress').after('<p class="norequire_saddress"><i class="fa fa-bell-o" aria-hidden="true"></i> <strong>'+ json['text_norequire_saddress'] +'</strong></p>');

						$('.delivery_date_status').addClass('hide');
					}

					setTimeout(function () {
						if(typeof Journal != 'undefined') {
							$('#cart-total').html(json['total']);
						} else {
							$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
						}
					}, 100);

					$('#cart > ul').load('index.php?route=common/cart/info ul li');

					// Refresh Shipping Methods
					MPSHIPPINGMETHODS.refresh();

					// Refresh Payment Methods
					MPPAYMENTMETHODS.refresh();
				}

			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}
/********************** Shopping Cart Ends **********************/

/********************** Shipping Address Starts **********************/
// Declaration Refresh Shipping Address
var MPDELIVERYDATE = {
	'refresh': function() {
		$.ajax({
			url: 'index.php?route=mpcheckout/delivery_date/ajax',
			type: 'post',
			dataType: 'html',
			beforeSend: function() {
			},
			complete: function() {
			},
			success: function(html) {
				$('#mp-checkout .delivery_date').html(html);
			}
		});
	},
}
/********************** Shipping Address Ends **********************/

/********************** Shipping Address Starts **********************/
// Declaration Refresh Shipping Address
var MPSHIPPINGADDRESS = {
	'refresh': function(same_address) {
		$.ajax({
			url: 'index.php?route=mpcheckout/shipping_address/ajax',
			type: 'post',
			dataType: 'html',
			beforeSend: function() {
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {
			},
			success: function(html) {
				$('#mp-checkout .shipping-addresses').html(html);

				// Trigger Country
				$('select[name=\'shipping_address[country_id]\']').trigger('change');
			}
		});
	},
}
/********************** Shipping Address Ends **********************/

/********************** Shipping Method Starts **********************/
var MPSHIPPINGMETHODS = {
	'refresh': function(need_cart_refresh) {
		need_cart_refresh = typeof need_cart_refresh != 'undefined' ? need_cart_refresh : false;

		$.ajax({
			url: 'index.php?route=mpcheckout/shipping_method/ajax',
			type: 'post',
			data: $('.accountoption-panel input[name=\'accountoption\']:checked, .signup-panel input[type=\'text\'], .signup-panel input[type=\'date\'], .signup-panel input[type=\'datetime-local\'], .signup-panel input[type=\'time\'], .signup-panel input[type=\'password\'], .signup-panel input[type=\'checkbox\']:checked, .signup-panel input[type=\'radio\']:checked, .signup-panel input[type=\'hidden\'], .signup-panel textarea, .signup-panel select, .shipping-address-panel input[type=\'text\'], .shipping-address-panel input[type=\'date\'], .shipping-address-panel input[type=\'datetime-local\'], .shipping-address-panel input[type=\'time\'], .shipping-address-panel input[type=\'password\'], .shipping-address-panel input[type=\'checkbox\']:checked, .shipping-address-panel input[type=\'radio\']:checked, .shipping-address-panel input[type=\'hidden\'], .shipping-address-panel textarea, .shipping-address-panel select, .payment-address-panel input[type=\'text\'], .payment-address-panel input[type=\'date\'], .payment-address-panel input[type=\'datetime-local\'], .payment-address-panel input[type=\'time\'], .payment-address-panel input[type=\'password\'], .payment-address-panel input[type=\'checkbox\']:checked, .payment-address-panel input[type=\'radio\']:checked, .payment-address-panel input[type=\'hidden\'], .payment-address-panel textarea, .payment-address-panel select, input[name=\'same_address\']:checked'),
			dataType: 'html',
			beforeSend: function() {
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {
			},
			success: function(html) {
				$('#mp-checkout .shipping_methods').html(html);

				// Save Shipping Methods
				if($('.ship_one').length && ($('input[name=\'shipping_method\']:checked').val() == '' || typeof($('input[name=\'shipping_method\']:checked').val()) === 'undefined')) {
					$('.ship_one').first().prop('checked', true);
				  	var new_need_cart_refresh = true;
				} else if(need_cart_refresh) {
					var new_need_cart_refresh = true;
				} else{
				  	var new_need_cart_refresh = false;
				}

				MPSHIPPINGMETHODS.save(new_need_cart_refresh);

				// Refresh Checkout Button
				// I Am Stop
				/* MPCHECKOUTBUTTON.refresh(); */
			}
		});
	},
	'save': function(need_cart_refresh) {
		need_cart_refresh = typeof need_cart_refresh != 'undefined' ? need_cart_refresh : false;

		$.ajax({
			url: 'index.php?route=mpcheckout/shipping_method/save',
			type: 'post',
			data: $('input[name=\'shipping_method\']:checked'),
			dataType: 'json',
			beforeSend: function() {
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {


			},
			success: function(json) {
				$('.shipping-method-panel .alert').remove();

				if (json['redirect']) {
	                location = json['redirect'];
	            } else if (json['error']) {
	                if (json['error']['warning']) {
	                    $('.shipping-method-panel .panel-body').prepend('<div class="alert alert-danger warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
	                }
	            } else{
	            	if(need_cart_refresh) {
	            		if(json['ship_session']) {
	            			ship_session = json['ship_session'];
	            		}

	            		MPSHOPPINGCART.refresh();
	            	}
	            }
			}
		});
	},
}
/********************** Shipping Methods Ends **********************/

/********************** Payment Methods Starts **********************/
var MPPAYMENTMETHODS = {
	'refresh': function(need_cart_refresh, callback) {
		need_cart_refresh = typeof need_cart_refresh != 'undefined' ? need_cart_refresh : false;
		callback = callback || function() { };

		$.ajax({
			url: 'index.php?route=mpcheckout/payment_method/ajax',
			type: 'post',
			data: $('.accountoption-panel input[name=\'accountoption\']:checked, .signup-panel input[type=\'text\'], .signup-panel input[type=\'date\'], .signup-panel input[type=\'datetime-local\'], .signup-panel input[type=\'time\'], .signup-panel input[type=\'password\'], .signup-panel input[type=\'checkbox\']:checked, .signup-panel input[type=\'radio\']:checked, .signup-panel input[type=\'hidden\'], .signup-panel textarea, .signup-panel select, .shipping-address-panel input[type=\'text\'], .shipping-address-panel input[type=\'date\'], .shipping-address-panel input[type=\'datetime-local\'], .shipping-address-panel input[type=\'time\'], .shipping-address-panel input[type=\'password\'], .shipping-address-panel input[type=\'checkbox\']:checked, .shipping-address-panel input[type=\'radio\']:checked, .shipping-address-panel input[type=\'hidden\'], .shipping-address-panel textarea, .shipping-address-panel select, .payment-address-panel input[type=\'text\'], .payment-address-panel input[type=\'date\'], .payment-address-panel input[type=\'datetime-local\'], .payment-address-panel input[type=\'time\'], .payment-address-panel input[type=\'password\'], .payment-address-panel input[type=\'checkbox\']:checked, .payment-address-panel input[type=\'radio\']:checked, .payment-address-panel input[type=\'hidden\'], .payment-address-panel textarea, .payment-address-panel select, input[name=\'same_address\']:checked'),
			dataType: 'html',
			beforeSend: function() {
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {
			},
			success: function(html) {
				$('#mp-checkout .payment_methods').html(html);

				// Save Payment Methods
				if($('.pay_one').length && ($('input[name=\'payment_method\']:checked').val() == '' || typeof($('input[name=\'payment_method\']:checked').val()) === 'undefined')) {
					$('.pay_one').first().prop('checked', true);
				  	var new_need_cart_refresh = true;
				} else if(need_cart_refresh) {
					var new_need_cart_refresh = true;
				} else {
					var new_need_cart_refresh = false;
				}

				MPPAYMENTMETHODS.save(new_need_cart_refresh);
			}
		}).always(function() {
			callback();
		});
	},
	'save': function(need_cart_refresh) {
		need_cart_refresh = typeof need_cart_refresh != 'undefined' ? need_cart_refresh : false;
		$.ajax({
			url: 'index.php?route=mpcheckout/payment_method/save',
			type: 'post',
			data: $('input[name=\'payment_method\']:checked'),
			dataType: 'json',
			beforeSend: function() {
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {

			},
			success: function(json) {
				$('.payment-method-panel .alert').remove();

				if (json['redirect']) {
	                location = json['redirect'];
	            } else if (json['error']) {
	                if (json['error']['warning']) {
	                    $('.payment-method-panel .panel-body').prepend('<div class="alert alert-danger warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
	                }
	            } else {
	            	// Refresh Checkout Button
	            	if(need_cart_refresh) {
	            		MPSHOPPINGCART.refresh(true);
	            	} else {
            			MPCHECKOUTBUTTON.refresh();
            		}
	            }
			}
		});
	},
}
/********************** Payment Methods Ends **********************/
/********************** Checkout Buttons Starts **********************/
var MPCHECKOUTBUTTON = {
	'refresh': function() {
		$.ajax({
			url: 'index.php?route=mpcheckout/checkout_button/ajax',
			type: 'post',
			data: '',
			dataType: 'html',
			beforeSend: function() {
				$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', true);
				$('.mpdisable').removeClass('hide');
				$('.comment-panel').addClass('blur');
			},
			complete: function() {
			},
			success: function(html) {
				$('.checkout-button-position .panel-body').html(html);
			}
		});
	},
	'createValidOrder': function() {
		$.ajax({
			url: 'index.php?route=mpcheckout/checkout_button/createValidOrder',
			type: 'post',
			data: $('#mp-checkout input[type=\'text\'], #mp-checkout input[type=\'date\'], #mp-checkout input[type=\'datetime-local\'], #mp-checkout input[type=\'time\'], #mp-checkout input[type=\'password\'], #mp-checkout input[type=\'checkbox\']:checked, #mp-checkout input[type=\'radio\']:checked, #mp-checkout input[type=\'hidden\'], #mp-checkout textarea, #mp-checkout select, #mp-checkout input[type=\'email\'], #mp-checkout input[type=\'password\']'),
			dataType: 'json',
			beforeSend: function() {
				$('#mp-checkout #button-checkout').button('loading');
			},
			complete: function() {

			},
			success: function(json) {
				$('#mp-checkout .alert').remove();
				$('.form-group').removeClass('has-error');

				if (json['redirect']) {
	                location = json['redirect'];
	            } else if (json['error']) {
	            	$('#mp-checkout #button-checkout').button('reset');

	            	// For Signup And Guest
	            	if (json['error']['signup']) {
		                if (json['error']['signup']['warning']) {
		                    $('.signup-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['signup']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		                }

						for (i in json['error']['signup']) {
							var element = $('#input-signup-' + i.replace('_', '-'));

							if ($(element).parent().hasClass('input-group')) {
								$(element).parent().parent().addClass('has-error');
							} else {
								$(element).parent().addClass('has-error');
							}
						}
					}

					// For Password
					if (json['error']['pass']) {
		                if (json['error']['pass']['warning']) {
		                    $('.signup-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['pass']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		                }

						for (i in json['error']['pass']) {
							var element = $('#input-pass-' + i.replace('_', '-'));

							if ($(element).parent().hasClass('input-group')) {
								$(element).parent().parent().addClass('has-error');
							} else {
								$(element).parent().addClass('has-error');
							}
						}
					}

					// For Signup Privacy
					if (json['error']['signup_privacy']) {
		                if (json['error']['signup_privacy']['warning']) {
		                    $('.privacy-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['signup_privacy']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		                }
					}

					// For Login
	            	if (json['error']['login']) {
	                    $('.login-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['login'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

	                    $('.login-panel input[name=\'email\']').parent().addClass('has-error');
						$('.login-panel input[name=\'password\']').parent().addClass('has-error');
					}

					// For Payment Address
	            	if (json['error']['payment']) {
	            		if (json['error']['payment']['warning']) {
		                    $('.payment-address-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['payment']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		                }

						for (i in json['error']['payment']) {
							var element = $('#input-payment-' + i.replace('_', '-'));

							if ($(element).parent().hasClass('input-group')) {
								$(element).parent().parent().addClass('has-error');
							} else {
								$(element).parent().addClass('has-error');
							}
						}
					}


					// For Shipping Method
	            	if (json['error']['shipping_method']) {
	            		if (json['error']['shipping_method']['warning']) {
		                    $('.shipping-method-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['shipping_method']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		                }
					}

					// For Payment Method
	            	if (json['error']['payment_method']) {
	            		if (json['error']['payment_method']['warning']) {
		                    $('.payment-method-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['payment_method']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		                }
					}

					// For Shipping Address
	            	if (json['error']['shipping']) {
		                if (json['error']['shipping']['warning']) {
		                    $('.shipping-address-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['shipping']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		                }

						for (i in json['error']['shipping']) {
							var element = $('#input-shipping-' + i.replace('_', '-'));

							if ($(element).parent().hasClass('input-group')) {
								$(element).parent().parent().addClass('has-error');
							} else {
								$(element).parent().addClass('has-error');
							}
						}
					}

					// For Shopping Cart
	            		if (json['error']['cart_warning']) {
		                    $('#mp-checkout .shoppingcart .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['cart_warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					}

					// For Devliery Date
					if (json['error']['deliverydate']) {
						$('.delivery-date-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['deliverydate'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					}

					// For Checkout Agree
            		if (json['error']['comment_warning']) {
	                    $('.comment-panel .panel-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['comment_warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
	                }

	                $('html, body').animate({ scrollTop: $('.alert-danger:first-child').parent().parent().offset().top - 10}, 'slow');
				} else if (json['lastconfirmbutton']) {
            		// Load Last Confirm Button from payment method files.
            		if (json['agree']) {
            			var agree = json['agree'];
            		}  else{
						var agree = '';
            		}

            		if (json['comment']) {
            			var comment = json['comment'];
            		}  else{
						var comment = '';
            		}

            		$.ajax({
						url: 'index.php?route=mpcheckout/checkout_button/LastConfirmButton',
						type: 'post',
						data: 'comment='+ comment + '&agree='+ agree,
						dataType: 'html',
						beforeSend: function() {
							$('#mp-checkout #button-checkout').button('loading');
						},
						complete: function() {
							$('#mp-checkout #button-checkout').button('reset');
						},
						success: function(html) {
							$('.checkout-button-position .panel-body').html(html);
						}
					});
            	}
			}
		});
	},
}
/********************** Checkout Buttons Ends **********************/

$(document).ajaxStop(function() {
	$('#mp-checkout #button-checkout, #mp-checkout #button-confirm').attr('disabled', false);

	$('.mpdisable').addClass('hide');
	$('.comment-panel').removeClass('blur');
});