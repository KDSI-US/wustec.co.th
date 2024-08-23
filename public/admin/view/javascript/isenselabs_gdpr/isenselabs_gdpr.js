// Initialize the main tabs
$(document).ready(function() {
	var url_addr = document.location.toString();
	
	if (window.localStorage && window.localStorage['currentTab']) {
		if (url_addr.match('#')) {
			$('.nav-tabs a[href="#' + url_addr.split('#')[1] + '"]').click();
		} else {
			$('.mainMenuTabs a[href="'+window.localStorage['currentTab']+'"]').click();
		}
	} else {
		if (url_addr.match('#')) {
			$('.nav-tabs a[href="#' + url_addr.split('#')[1] + '"]').click();
		} else {
			$('.mainMenuTabs a:first').click();
		}
	}
	
	$('#langtabs a:first').click();
	$('#langtabs-cc a:first').click();
	$('#langtabs-seo a:first').click();
	
	$('.mainMenuTabs a[data-toggle="tab"]').click(function() {
		if (window.localStorage) {
			window.localStorage['currentTab'] = $(this).attr('href');
		}
	});
		
	// Change hash for page-reload
	$('.nav-tabs a').on('shown.bs.tab', function (e) {
		window.location.hash = e.target.hash;
		window.scrollTo(0, 0);
	})
}); 

// Load some of the main sub-tabs in the module
$(document).ready(function(e) {
    $.ajax({
        url: "index.php?route=" + modulePath + "/get_requests&store_id=" + storeId + "&" + token_string + "=" + token,
        type: 'get',
        dataType: 'html',
        success: function(data) { 
            $('.requests-view').html(data);
        }
    });
	
	$.ajax({
        url: "index.php?route=" + modulePath + "/get_acceptances&store_id=" + storeId + "&" + token_string + "=" + token,
        type: 'get',
        dataType: 'html',
        success: function(data) { 
            $('.acceptances-view').html(data);
        }
    });

    $.ajax({
        url: "index.php?route=" + modulePath + "/get_optins&store_id=" + storeId + "&" + token_string + "=" + token,
        type: 'get',
        dataType: 'html',
        success: function(data) { 
            $('.optins-view').html(data);
        }
    });
	
	$.ajax({
        url: "index.php?route=" + modulePath + "/get_deletions&store_id=" + storeId + "&" + token_string + "=" + token,
        type: 'get',
        dataType: 'html',
        success: function(data) { 
            $('.deletions-view').html(data);
        }
    });
});

// Requests input button auto-send query
$(document).ready(function() {
    $('.requests_input').on('keydown', function(e) {
		if (e.keyCode == 13) {
            e.preventDefault();
            e.stopImmediatePropagation();
			$('.btn-requests-filter').trigger('click');
		}
	});
	
	$('.acceptances_input').on('keydown', function(e) {
		if (e.keyCode == 13) {
            e.preventDefault();
            e.stopImmediatePropagation();
			$('.btn-acceptances-filter').trigger('click');
		}
	});
	
	$('.optins_input').on('keydown', function(e) {
		if (e.keyCode == 13) {
            e.preventDefault();
            e.stopImmediatePropagation();
			$('.btn-optins-filter').trigger('click');
		}
	});
	
	$('.deletions_input').on('keydown', function(e) {
		if (e.keyCode == 13) {
            e.preventDefault();
            e.stopImmediatePropagation();
			$('.btn-deletions-filter').trigger('click');
		}
	});
});

// Export Requests
function exportRequests(selector, event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    var export_data = $(selector).parents('.requests-filter-data').find('input, select, textarea').serialize();
    var export_link = "index.php?route=" + modulePath + "/export_requests&store_id=" + storeId + "&" + token_string + "=" + token + '&' + export_data;    
    var window_open = window.open(export_link, '_blank');
    return false;
}

// Filter Requests
function filterRequests(selector, event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    var filter_data = $(selector).parents('.requests-filter-data').find('input, select, textarea').serialize();

    $.ajax({
        url: "index.php?route=" + modulePath + "/get_requests&store_id=" + storeId + "&" + token_string + "=" + token,
        dataType: 'html',
        type: 'get',
        data: filter_data,
        success: function(data) {
           $('.requests-view').html(data);
        },
        beforeSend: function() {
           $('.requests-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
        }
    });
    
    return false;
}

// Pagination Requests
$('document').ready(function() {
   $('.requests-view').delegate('.pagination a', 'click', (function(e){
        e.preventDefault();
        $.ajax({
            url: this.href,
            type: 'get',
            dataType: 'html',
            beforeSend: function() {
                $('.requests-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
            },
            success: function(data) {				
                $('.requests-view').html(data);
            }
        });
    })); 
});

// Export Acceptances
function exportAcceptances(selector, event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    var export_data = $(selector).parents('.acceptances-filter-data').find('input, select, textarea').serialize();
    var export_link = "index.php?route=" + modulePath + "/export_acceptances&store_id=" + storeId + "&" + token_string + "=" + token + '&' + export_data;    
    var window_open = window.open(export_link, '_blank');
    return false;
}

// Filter Acceptances
function filterAcceptances(selector, event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    var filter_data = $(selector).parents('.acceptances-filter-data').find('input, select, textarea').serialize();

    $.ajax({
        url: "index.php?route=" + modulePath + "/get_acceptances&store_id=" + storeId + "&" + token_string + "=" + token,
        dataType: 'html',
        type: 'get',
        data: filter_data,
        success: function(data) {
           $('.acceptances-view').html(data);
        },
        beforeSend: function() {
           $('.acceptances-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
        }
    });
    
    return false;
}

// Pagination Acceptances
$('document').ready(function() {
   $('.acceptances-view').delegate('.pagination a', 'click', (function(e){
        e.preventDefault();
        $.ajax({
            url: this.href,
            type: 'get',
            dataType: 'html',
            beforeSend: function() {
                $('.acceptances-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
            },
            success: function(data) {				
                $('.acceptances-view').html(data);
            }
        });
    })); 
});

// Export Opt-ins
function exportOptins(selector, event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    var export_data = $(selector).parents('.optins-filter-data').find('input, select, textarea').serialize();
    var export_link = "index.php?route=" + modulePath + "/export_optins&store_id=" + storeId + "&" + token_string + "=" + token + '&' + export_data;    
    var window_open = window.open(export_link, '_blank');
    return false;
}

// Filter Opt-ins
function filterOptins(selector, event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    var filter_data = $(selector).parents('.optins-filter-data').find('input, select, textarea').serialize();

    $.ajax({
        url: "index.php?route=" + modulePath + "/get_optins&store_id=" + storeId + "&" + token_string + "=" + token,
        dataType: 'html',
        type: 'get',
        data: filter_data,
        success: function(data) {
           $('.optins-view').html(data);
        },
        beforeSend: function() {
           $('.optins-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
        }
    });
    
    return false;
}

// Pagination Opt-ins 
$('.optins-view').delegate('.pagination a', 'click', (function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		type: 'get',
		dataType: 'html',
		beforeSend: function() {
			$('.optins-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
		},
		success: function(data) {               
			$('.optins-view').html(data);
		}
	});
}));

// Filter Deletions
function filterDeletions(selector, event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    var filter_data = $(selector).parents('.deletions-filter-data').find('input, select, textarea').serialize();

    $.ajax({
        url: "index.php?route=" + modulePath + "/get_deletions&store_id=" + storeId + "&" + token_string + "=" + token,
        dataType: 'html',
        type: 'get',
        data: filter_data,
        success: function(data) {
           $('.deletions-view').html(data);
        },
        beforeSend: function() {
           $('.deletions-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
        }
    });
    
    return false;
}

// Pagination Deletions
$('.deletions-view').delegate('.pagination a', 'click', (function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		type: 'get',
		dataType: 'html',
		beforeSend: function() {
			$('.deletions-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
		},
		success: function(data) {               
			$('.deletions-view').html(data);
		}
	});
}));

// Right to be Forgotten Functions
function denyRequest(deletion_id) {
	$.ajax({
        url: "index.php?route=" + modulePath + "/deny_deletion&deletion_id=" + deletion_id + "&" + token_string + "=" + token,
        dataType: 'html',
        type: 'get',
        success: function(data) {
           $('.deletions-view').html(data);
        },
        beforeSend: function() {
           $('.deletions-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
        }
    });
}

function approveRequest(deletion_id) {
	$.ajax({
        url: "index.php?route=" + modulePath + "/approve_deletion&deletion_id=" + deletion_id + "&" + token_string + "=" + token,
        dataType: 'html',
        type: 'get',
        success: function(data) {
           $('.deletions-view').html(data);
        },
        beforeSend: function() {
           $('.deletions-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
        }
    });
}

function goBack(element) {
	$.ajax({
        url: "index.php?route=" + modulePath + "/get_deletions&store_id=" + storeId + "&" + token_string + "=" + token,
		type: 'get',
		dataType: 'html',
		beforeSend: function() {
			$('.deletions-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
		},
		success: function(data) {               
			$('.deletions-view').html(data);
		}
	});
}

function denyDeletionRequest(element) {
	var deny_reason = $('.row textarea#deny_request_text').val();
	if (deny_reason) {
		bootbox.confirm({
			title: text_confim_window_heading,
			message: text_confirm_action + ' ' + text_irreversible_action,
			buttons: {
				cancel: {
					label: '<i class="fa fa-times"></i> ' + text_cancel
				},
				confirm: {
					label: '<i class="fa fa-check"></i> ' + text_confirm
				}
			},
			callback: function (result) {
				if (result) {
					$.ajax({
						url: "index.php?route=" + modulePath + "/deny_deletion_action&" + token_string + "=" + token,
						dataType: 'JSON',
						type: "POST",
						data: $('div.deny-form').find('textarea,input').serialize(),
						beforeSend: function() {
							$('.deletions-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
						},
						success: function(data) {
							if (data['success']) {}
								var dialogSuccess = bootbox.dialog({
									message: '<p class="text-center">' + text_deny_deletion_success + '</p>',
									closeButton: false
								});
								setTimeout(function() {
									dialogSuccess.modal('hide');
								}, 2000);
							if (data['error']) {
								bootbox.alert(text_unexpected_error);
							}
														
							$.ajax({
								url: "index.php?route=" + modulePath + "/get_deletions&store_id=" + storeId + "&" + token_string + "=" + token,
								type: 'get',
								dataType: 'html',
								success: function(data) {               
									$('.deletions-view').html(data);
								}
							});
						},
						complete: function() {}
					});  
				}
			}
		});
	} else {
		bootbox.alert(text_data_deletion_warning);
	}
}

function approveDeletionRequest(element) {
	var date_deletion = $('.row input#date_deletion').val();
	if (date_deletion) {
		bootbox.confirm({
			title: text_confim_window_heading,
			message: text_confirm_action,
			buttons: {
				cancel: {
					label: '<i class="fa fa-times"></i> ' + text_cancel
				},
				confirm: {
					label: '<i class="fa fa-check"></i> ' + text_confirm
				}
			},
			callback: function (result) {
				if (result) {
					$.ajax({
						url: "index.php?route=" + modulePath + "/approve_deletion_action&" + token_string + "=" + token,
						dataType: 'JSON',
						type: "POST",
						data: $('div.approve-form').find('textarea,input,radio,checkbox').serialize(),
						beforeSend: function() {
							$('.deletions-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
						},
						success: function(data) {
							if (data['success']) {}
								var dialogSuccess = bootbox.dialog({
									message: '<p class="text-center">' + text_successful_deletion_approval + '</p>',
									closeButton: false
								});
								setTimeout(function() {
									dialogSuccess.modal('hide');
								}, 2000);
							if (data['error']) {
								bootbox.alert(text_unexpected_error);
							}
														
							$.ajax({
								url: "index.php?route=" + modulePath + "/get_deletions&store_id=" + storeId + "&" + token_string + "=" + token,
								type: 'get',
								dataType: 'html',
								success: function(data) {               
									$('.deletions-view').html(data);
								}
							});
						},
						complete: function() {}
					});  
				}
			}
		});
	} else {
		bootbox.alert(text_data_deletion_date_warning);
	}
}

function deleteData(deletion_id) {
	bootbox.confirm({
		title: text_confim_window_heading,
		message: text_confirm_action + ' ' + text_irreversible_action,
		buttons: {
			cancel: {
				label: '<i class="fa fa-times"></i> ' + text_cancel
			},
			confirm: {
				label: '<i class="fa fa-check"></i> ' + text_confirm
			}
		},
		callback: function (result) {
			if (result) {
				$.ajax({
					url: "index.php?route=" + modulePath + "/actual_deletion&" + token_string + "=" + token,
					dataType: 'JSON',
					type: "POST",
					data: { deletion_id: deletion_id },
					beforeSend: function() {
						$('.deletions-view').html('<p><h2 class="text-center">' + text_loading_data + '</h2></p><br /><div class="loader"></div>');
					},
					success: function(data) {
						if (data['success']) {}
							var dialogSuccess = bootbox.dialog({
								message: '<p class="text-center">' + text_successful_removal + '</p>',
								closeButton: false
							});
							setTimeout(function() {
								dialogSuccess.modal('hide');
							}, 2600);
						if (data['error']) {
							bootbox.alert(text_unexpected_error);
						}

						$.ajax({
							url: "index.php?route=" + modulePath + "/get_deletions&store_id=" + storeId + "&" + token_string + "=" + token,
							type: 'get',
							dataType: 'html',
							success: function(data) {               
								$('.deletions-view').html(data);
							}
						});
					},
					complete: function() {}
				});  
			}
		}
	});
}
