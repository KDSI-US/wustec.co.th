<?php echo $header; ?>
<section class="supprt-banner" style="background-image: url('<?php echo $support_banner; ?>')">
  <div class="container">
    <h1><?php echo $banner_title; ?></h1>
  </div>
</section>
<div class="container">
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <div class="ticketview-wrap">
        <table class="table table-striped table-hover">
          <thead class="table-head">
            <tr>
              <th><strong><?php echo $column_department; ?></strong></th>
              <th><?php echo $department_name; ?></th>
              <th><strong><?php echo $column_date; ?></strong></th>
              <th><?php echo $date_added; ?></th>
              <th><strong><?php echo $column_status; ?></strong></th>
              <th><?php echo $status; ?></th>
            </tr>
          </thead>
        </table>
        <div class="reply-wrap">
          <div class="ticket-header">
            <h3><?php echo $subject; ?></h3>
            <ul class="ticket-status list-inline">
              <li><span class="label" style="color: <?php echo $textcolor; ?>; background: <?php echo $bgcolor; ?>"><?php echo $status; ?></span></li>
              <li><?php echo $department_name; ?></li>
            </ul>
            <div class="ticket-time"><?php echo $ticketuser_name; ?> || <?php echo $text_client; ?> <?php echo $date_added; ?></div>
          </div>
          <div class="ticket-content">
            <?php echo $message; ?>
          </div>
          <?php if($attachments) { ?>
          <div class="ticket-attachments">
            <?php foreach($attachments as $attachment) { ?>
            <p><a href="<?php echo $attachment['href']; ?>"><i class="fa fa-paperclip"></i> <?php echo $attachment['name']; ?></a></p>
            <?php } ?>
          </div>
          <?php } ?>
        </div>
        <?php if($chats) { ?>
          <?php foreach($chats as $chat) { ?>
          <div class="reply-wrap reply-chat">
            <div class="ticket-header">
              <?php if($chat['profile_photo']) { ?>
              <img src="<?php echo $chat['profile_photo']; ?>" class="img-responsive img-circle">
              <?php } ?>
              <h3><?php echo $chat['client_name']; ?> <span class="<?php echo $chat['client_type'] == 'staff' ? 'purple-tag' : 'yellow-tag'; ?>"><?php echo $chat['client_type_name']; ?></span></h3>
              <div class="ticket-time"><?php echo $chat['date_added']; ?></div>
            </div>
            <div class="ticket-content">
              <?php echo $chat['message']; ?>
            </div>
            <?php if($chat['attachments']) { ?>
            <div class="ticket-attachments">
              <?php foreach($chat['attachments'] as $attachment) { ?>
              <p><a href="<?php echo $attachment['href']; ?>"><i class="fa fa-paperclip"></i> <?php echo $attachment['name']; ?></a></p>
              <?php } ?>
            </div>
            <?php } ?>
          </div>
          <?php } ?>

          <?php if($pagination) { ?>
          <div class="row">
            <div class="col-sm-12 text-right"><?php echo $pagination; ?></div>
          </div>
          <?php } ?>
        <?php } ?>
      </div>
      <?php if(!$ticket_close) { ?>
      <div class="reply-section">
        <div class="form-group row">
        	<label class="col-sm-12 control-label"><?php echo $entry_message; ?></label>
          <div class="col-sm-12">
            <textarea name="message" rows="8" class="form-control"></textarea>
          </div>
        </div>
        <div class="form-group row">
	        <label class="col-sm-12 control-label"><?php echo $entry_attachments; ?></label>
	        <div class="col-sm-3">
          		<button type="button" id="button-upload" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block"><i class="fa fa-paperclip"></i> <?php echo $button_add_file; ?></button>
        	</div>
      	</div>
      	<div class="form-group row">
	        <div class="col-sm-6 upload-group">
	        </div>
      	</div>
        <br/>
        <div class="buttons-holder text-center">
          <button type="button" class="button btn-blue button-reply-submit-open"><?php echo $button_submit; ?></button>
          <button type="button" class="button btn-blue button-reply-submit-close"><?php echo $button_submit_close; ?></button>
          <a class="button btn-gray" href="<?php echo $request_list; ?>"><?php echo $button_request_list; ?></a>
        </div>
      </div>
      <?php } ?>

      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
<script type="text/javascript"><!--
$('button[id^=\'button-upload\']').on('click', function() {
  var node = this;

  $('#form-upload').remove();

  $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

  $('#form-upload input[name=\'file\']').trigger('click');

  if (typeof timer != 'undefined') {
      clearInterval(timer);
  }

  timer = setInterval(function() {
    if ($('#form-upload input[name=\'file\']').val() != '') {
      clearInterval(timer);

      $.ajax({
        url: 'index.php?route=support/request_form/fileupload',
        type: 'post',
        dataType: 'json',
        data: new FormData($('#form-upload')[0]),
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

            var html = '';
            html += '<div class="new-file">';
            html += '<i class="fa fa-paperclip"></i>';
            html += '<input type="hidden" name="attachments[]" value="'+ json['code'] +'" />';
            html += (json['filename']) ? json['filename'] : json['code'];
            html += '<a class="me-remove pull-right"><i class="fa fa-times"></i></a>';
            html += '</div>';


            $('.upload-group').append(html);
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    }
  }, 500);
});
//--></script>
<script type="text/javascript"><!--
$('.button-reply-submit-open').click(function() {
  var type = 'open';
  addTicketReply(type);
});

$('.button-reply-submit-close').click(function() {
  var type = 'close';
  addTicketReply(type);
});

function addTicketReply(type) {
  $.ajax({
    url: '<?php echo $reply_action; ?>&type='+ type,
    type: 'post',
    data: $('.reply-section input, .reply-section textarea'),
    dataType: 'json',
    beforeSend: function() {
      $('.button-reply-submit-'+ type).button('loading');
    },
    complete: function() {
        $('.button-reply-submit-'+ type).button('reset');        
    },
    success: function(json) {
      $('.reply-section .alert, .reply-section .text-danger').remove();

      if (json['redirect']) {
        location = json['redirect'];
      } else if (json['warning']) {
        $('.reply-section').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      }
    }
  });
}

$(document).delegate('.me-remove', 'click', function() {
  $(this).parent().remove();
});
//--></script>
</div>
<?php echo $footer; ?>