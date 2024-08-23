$(document).ready(function() {
// Login
$('#panel-login input[type=\'email\'], #panel-login input[type=\'password\']').on("keydown", function (e) {
  if (e.keyCode === 13) {
        $('.button-login-submit').trigger('click');
    }
});
$('.button-login-submit').click(function() {
  var $el = $(this);
  $.ajax({
    url: 'index.php?route=support/ticketuser/login',
    type: 'post',
    data: $('#panel-login input'),
    dataType: 'json',
    beforeSend: function() {
      $el.button('loading');
    },
    complete: function() {
      $el.button('reset');        
    },
    success: function(json) {
      $('#panel-login .alert, #panel-login .text-danger').remove();
      $('#panel-login .input-group').removeClass('has-error');

      if (json['redirect']) {
        location = json['redirect'];
      } else if (json['error']) {
        $('#panel-login .modal-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

        // Highlight any found errors
        $('#panel-login input[name=\'email\']').parent().addClass('has-error');
        $('#panel-login input[name=\'password\']').parent().addClass('has-error');

        $('#panel-login input[name=\'email\']').focus();
      }
    }
  });
});

// Forgot
$('#panel-forgot input[name=\'email\']').on("keydown", function (e) {
  if (e.keyCode === 13) {
        $('.button-forgot-submit').trigger('click');
    }
});

$('.button-forgot-submit').click(function() {
  var $el = $(this);
  $.ajax({
    url: 'index.php?route=support/ticketuser/forgot',
    type: 'post',
    data: $('#panel-forgot input'),
    dataType: 'json',
    beforeSend: function() {
      $el.button('loading');
    },
    complete: function() {
      $el.button('reset');        
    },
    success: function(json) {
      $('#panel-forgot .alert, #panel-forgot .text-danger').remove();
      $('#panel-forgot .input-group').removeClass('has-error');

      if (json['redirect']) {
        location = json['redirect'];
      } else if (json['error']) {
        $('#panel-forgot .modal-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

        // Highlight any found errors
        $('#panel-forgot input[name=\'email\']').parent().addClass('has-error');
        $('#panel-forgot input[name=\'password\']').parent().addClass('has-error');

        $('#panel-forgot input[name=\'email\']').focus();
      } else if(json['success']) {
        $('#panel-forgot .modal-body').prepend('<div class="alert alert-success success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

        $('#panel-forgot input[name=\'email\']').val('');
      }
    }
  });
});

// Register
$('#panel-register input[type=\'text\'], #panel-register input[type=\'email\'], #panel-register input[type=\'password\']').on("keydown", function (e) {
  if (e.keyCode === 13) {
        $('.button-register-submit').trigger('click');
    }
});
$('.button-register-submit').click(function() {
  var $el = $(this);
  $.ajax({
    url: 'index.php?route=support/ticketuser/register',
    type: 'post',
    data: $('#panel-register input[type=\'email\'], #panel-register input[type=\'password\'], #panel-register input[type=\'text\']'),
    dataType: 'json',
    beforeSend: function() {
      $el.button('loading');
    },
    complete: function() {
      $el.button('reset');        
    },
    success: function(json) {
      $('#panel-register .alert, #panel-register .text-danger').remove();
      $('#panel-register .input-group').removeClass('has-error');

      if (json['redirect']) {
        location = json['redirect'];
      } else if (json['error']) {
        if (json['error']['warning']) {
          $('#panel-register .modal-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        }

        if (json['error']['option']) {
          for (i in json['error']['option']) {
            var element = $('#ticket-' + i.replace('_', '-'));
            
            if (element.parent().hasClass('input-group')) {
              element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
            } else {
              element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
            }
          }
        }

        // Highlight any found errors
        $('#panel-register .text-danger').parent().parent().addClass('has-error');
      } else if(json['success']) {
        location = json['redirect'];
      }
    }
  });
});

// Edit User
$('#modalEditForm input[type=\'text\']').on("keydown", function (e) {
  if (e.keyCode === 13) {
        $('.btn-edituser').trigger('click');
    }
});
$('.btn-edituser').click(function() {
  var $el = $(this);
  $.ajax({
    url: 'index.php?route=support/ticketuser/edit',
    type: 'post',
    data: $('#modalEditForm input[type=\'email\'], #modalEditForm input[type=\'text\'], #modalEditForm input[type=\'hidden\']'),
    dataType: 'json',
    beforeSend: function() {
      $el.button('loading');
    },
    complete: function() {
      $el.button('reset');        
    },
    success: function(json) {
      $('#modalEditForm .alert, #modalEditForm .text-danger').remove();
      $('#modalEditForm .input-group, #modalEditForm .form-group').removeClass('has-error');

      if (json['redirect']) {
        location = json['redirect'];
      } else if (json['error']) {
        if (json['error']['warning']) {
          $('#modalEditForm .modal-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        }

        if (json['error']['option']) {
          for (i in json['error']['option']) {
            var element = $('#editticket-' + i.replace('_', '-'));
            
            if (element.parent().hasClass('input-group')) {
              element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
            } else {
              element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
            }
          }
        }

        // Highlight any found errors
        $('#modalEditForm .text-danger').parent().parent().addClass('has-error');
      } else if(json['success']) {
        $('#modalEditForm .modal-body').prepend('<div class="alert alert-success success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      }
    }
  });
});

// Edit Password
$('#modalEditPassword input[type=\'text\']').on("keydown", function (e) {
  if (e.keyCode === 13) {
        $('.btn-editpassword').trigger('click');
    }
});
$('.btn-editpassword').click(function() {
  var $el = $(this);
  $.ajax({
    url: 'index.php?route=support/ticketuser/editPassword',
    type: 'post',
    data: $('#modalEditPassword input[type=\'password\']'),
    dataType: 'json',
    beforeSend: function() {
      $el.button('loading');
    },
    complete: function() {
        $el.button('reset');        
    },
    success: function(json) {
      $('#modalEditPassword .alert, #modalEditPassword .text-danger').remove();
      $('#modalEditPassword .input-group, #modalEditPassword .form-group').removeClass('has-error');

      if (json['redirect']) {
        location = json['redirect'];
      } else if (json['error']) {
        if (json['error']['warning']) {
          $('#modalEditPassword .modal-body').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        }

        if (json['error']['option']) {
          for (i in json['error']['option']) {
            var element = $('#editticket-' + i.replace('_', '-'));
            
            if (element.parent().hasClass('input-group')) {
              element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
            } else {
              element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
            }
          }
        }

        // Highlight any found errors
        $('#modalEditPassword .text-danger').parent().parent().addClass('has-error');
      } else if(json['success']) {
        $('#modalEditPassword .modal-body').prepend('<div class="alert alert-success success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      }
    }
  });
});

// Image Upload
$('.pic-upload-button').on('click', function() {
  var node = this;

  $('#form-image-upload').remove();

  $('body').prepend('<form enctype="multipart/form-data" id="form-image-upload" style="display: none;"><input type="file" name="file" /></form>');

  $('#form-image-upload input[name=\'file\']').trigger('click');

  if (typeof timer != 'undefined') {
      clearInterval(timer);
  }

  timer = setInterval(function() {
    if ($('#form-image-upload input[name=\'file\']').val() != '') {
      clearInterval(timer);

      $.ajax({
        url: 'index.php?route=support/ticketuser/imageupload',
        type: 'post',
        dataType: 'json',
        data: new FormData($('#form-image-upload')[0]),
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
          $(node).button('loading');
        },
        complete: function() {
          $(node).button('reset');
        },
        success: function(json) {
          $('.text-danger').remove();

          if (json['error']) {
            alert(json['error']);
          }

          if (json['success']) {
            $('.pic-upload input').val(json['customer_profile']);

            $('.pic-upload img').attr('src', json['customer_thumb']);
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    }
  }, 500);
});

$('#modalLoginForm').on('hidden.bs.modal', function() {
  $(this).find('.alert, .text-danger').remove();
  $(this).find('.input-group').removeClass('has-error');
});
$('#modalEditPassword').on('hidden.bs.modal', function() {
  $(this).find('.alert, .text-danger').remove();
  $(this).find('.form-group').removeClass('has-error');
  $(this).find('.form-group').find('input').val('');
});
});