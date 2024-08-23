// Add MemberShip Plan
var MPMEMBERSHIP = {
	'add': function(product_id, mpplan_id) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id='+ product_id + '&quantity=1&mpplan_id='+ mpplan_id,
			dataType: 'json',
			beforeSend: function() {
				$('.plan_buy'+ mpplan_id).button('loading');
			},
			complete: function() {
				$('.plan_buy'+ mpplan_id).button('reset');
			},
			success: function(json) {
				$('.alert, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					location = 'index.php?route=checkout/cart';
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
}

$(document).ready(function() {
$('.current_plan').click(function() {
	var id = $(this).attr('data-id');
	$.ajax({
		url: 'index.php?route=account/subscriptions/addActivePlan',
		type: 'post',
		data: 'id='+ id,
		dataType: 'json',
		beforeSend: function() {
			$('.current_plan-'+ id +' i').remove();
			$('.current_plan-'+ id +' span').append('<i class="fa fa-spinner fa-spin"></i>');
		},
		complete: function() {
		},
		success: function(json) {
			$('.alert').remove();

			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['success'] && json['default_id']) {
				$('.current_plan i').remove();
				$('.current_plan span').append('<i class="fa fa-circle-o"></i>');
				$('.serialplan').removeClass('success');

				$('.current_plan-'+ json['default_id'] +' i').remove();
				$('.current_plan-'+ json['default_id'] +' span').append('<i class="fa fa-check-circle-o"></i>');
				$('.serialplan#activeplan-'+ json['default_id']).addClass('success');

				// Load Active Default Plan
				$('#oneactive').load('index.php?route=account/subscriptions/ActivePlan' + '&ajax=1');

				$('#oneactive').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> '+ json['success'] +'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

				$('html, body').animate({ scrollTop: $('.alert-success:first-child').parent().parent().offset().top - 10}, 'slow');
			}
		}
	});
});

$('#plan_history').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();

  $('#plan_history').load(this.href + '&ajax=1');
});

$('#payment_history').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();

  $('#payment_history').load(this.href + '&ajax=1');
});
});