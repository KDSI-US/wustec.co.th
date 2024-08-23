/* Search */
$(document).ready(function() {

	$('#blogsearch input[name=\'blogsearch\']').parent().find('button').on('click', function() {
		var url = $('#blogsearch input[name=\'blogsearch\']').attr('data-url');

		var value = $('#blogsearch input[name=\'blogsearch\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#blogsearch input[name=\'blogsearch\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('#blogsearch input[name=\'blogsearch\']').parent().find('button').trigger('click');
		}
	});


	$('.mpbloglike').on('click', function() {
		var $this = $(this);
		$this.addClass('avoide-click');
		var id = $this.attr('data-id');

		if(!$this.hasClass('liked')) {

		$.ajax({
			url: 'index.php?route=mpblog/blog/like&action=do',
			type: 'post',
			data: 'do=1&id=' + id,
			dataType: 'json',
			beforeSend: function() {
				
			},
			complete: function() {
				
			},
			success: function(json) {
				$('.alert').remove();

				if (json['success']) {

				$('span[data-id="'+ id +'"]').addClass('liked');
				// remove fa-heart-o
				$('span[data-id="'+ id +'"]').find('i').removeClass('fa-heart-o');



				$('span[data-id="'+ id +'"]').find('span').html(json['total']);
				$('span[data-id="'+ id +'"]').attr('title', json['total']);

				$this.removeClass('avoide-click');

				}

			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});

		} else {

		$.ajax({
			url: 'index.php?route=mpblog/blog/unlike&action=do',
			type: 'post',
			data: 'do=1&id=' + id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();


				if (json['success']) {
					
				

				$('span[data-id="'+ id +'"]').removeClass('liked');
// add fa-heart-o
				$('span[data-id="'+ id +'"]').find('i').addClass('fa-heart-o');

				$('span[data-id="'+ id +'"]').find('span').html(json['total']);
				$('span[data-id="'+ id +'"]').attr('title', json['total']);
				$this.removeClass('avoide-click');
				}

			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});


		}

	});
    
    $('.mp-subscribe').on('click', function() {
        var $this = $(this);
        var $parent = $(this).parents('.mp-subscribes').first();
        var data = $parent.find('input[type="text"]').serialize();
        
        $.ajax({
            url: 'index.php?route=extension/module/mpblogsubscribe/subscribe',
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $this.button('loading');
            },
            complete: function() {
                $this.button('reset');  
            },
            success: function(json) {
                $parent.find('.alert, .text-danger').remove();
                if (json['warning']) {
                    $parent.append('<div class="alert alert-danger"><i class="fa fa-check-circle"></i> ' + json['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }

                if (json['error']) {
                    if(json['error']['email']) {
                        $parent.find('input[name="msubscribe"]').after('<div class="text-danger"><i class="fa fa-check-circle"></i> ' + json['error']['email'] + '</div>');
                    }

                    if(json['error']['exists']) {
                         $parent.append('<div class="alert alert-danger"><i class="fa fa-check-circle"></i> ' + json['error']['exists'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                }

                if (json['success']) {
                    $parent.append('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    $parent.find('input[name="msubscribe"]').val('');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    $('.mp-unsubscribe').on('click', function() {
        var $this = $(this);
        var $parent = $(this).parents('.mp-subscribes').first();
        var data = $parent.find('input[type="text"]').serialize();
        
        $.ajax({
            url: 'index.php?route=extension/module/mpblogsubscribe/unSubscribe',
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $this.css('pointer-events','none');
                $this.after('<i class="mfa fa fa-loader fa-spin"></i>');
            },
            complete: function() {
              $this.css('pointer-events','');
            },
            success: function(json) {
                $parent.find('.alert, .text-danger, .mfa').remove();
                if (json['warning']) {
                    $parent.append('<div class="alert alert-danger"><i class="fa fa-check-circle"></i> ' + json['warning'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }

                if (json['error']) {
                    if(json['error']['email']) {
                        $parent.find('input[name="msubscribe"]').after('<div class="text-danger"><i class="fa fa-check-circle"></i> ' + json['error']['email'] + '</div>');
                    }

                    if(json['error']['nonexists']) {
                         $parent.append('<div class="alert alert-danger"><i class="fa fa-check-circle"></i> ' + json['error']['nonexists'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                }

                if (json['success']) {
                    $parent.append('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                    if(json['unsubscribe_popup']) {
                      html  = '<div id="modal-unsubscribe_popup" class="modal">';
                      html += '  <div class="modal-dialog">';
                      html += '    <div class="modal-content">';
                      html += '      <div class="modal-header">';
                      html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
                      html += '        <h4 class="modal-title">' + json['popup_title'] + '</h4>';
                      html += '      </div>';
                      html += '      <div class="modal-body">' + json['popup_content'] + '</div>';
                      html += '    </div';
                      html += '  </div>';
                      html += '</div>';
                      $('body').append(html);
                      $('#modal-unsubscribe_popup').modal('show');
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    

	RATING.init();

});

var RATING = function() {

  var highlight = function(value) {
    if(typeof value == 'undefined' || !value) { value = 0; }
    if(value >= 1 && value <= 5) {

      var currentStar = $(RATING.starContainer).find(RATING.starSelector+'[data-value='+ value +']');

      // highlight previous & current stars
      toggleActive(currentStar.prevAll(RATING.starSelector).andSelf(),true);

      // un highlight next stars
      toggleActive(currentStar.nextAll(RATING.starSelector),false);

    } else {

      // un highlight all stars
      toggleActive($(RATING.starContainer).find(RATING.starSelector), false);

    }

  };

  var toggleActive = function(el,active) {
    el.removeClass(active ? '' : 'full').addClass(active ? 'full' : '');
  };

  var setValue = function(value) {
    highlight(value);
    updateInput(value);
  };

  var updateInput = function(value) {

    if ($(RATING.starContainer).find(RATING.ratintInput).val() != value) {
      $(RATING.starContainer).find(RATING.ratintInput).val(value).change();
    }

  };

  var clear = function() {
    setValue(0);
  };

  // here are private functions and vars
  var initRating = function() {
    $(RATING.starContainer).find(RATING.starSelector).each(function() {
      var $this = $(this)

        $this.on('mouseenter', function () {
          highlight($this.data('value'));
        })
        .on('mouseleave', function () {
          highlight($(RATING.starContainer).find(RATING.ratintInput).val());
        })
        .on('click', function() {
          setValue($this.data('value'));
        });

    });

  };


  return  {

    // these are the public functions and vars
    init : function() {
      initRating();
    },
    clearRating : function() {
      clear();
    },
    starContainer : '.rating-container',
    starSelector : '.rating-icons',
    starFull : 'full',
    ratintInput : '.rating'
    
  };
}();

