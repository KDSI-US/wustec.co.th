<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="mp-content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid ticket-view">
    <h3 class="main-heading"><i class="fa fa-list"></i> <?php echo $heading_title_info; ?></h3>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="row">
      <div class="col-sm-4 status-col">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-ticket"></i> <?php echo $text_ticket; ?> #<?php echo $ticketrequest_id; ?></h3>
          </div>
          <table class="table">
            <tr>
              <td><?php echo $text_status; ?>:</td>
              <td class="text-right"><span style="padding: 5px 10px; border-radius: 3px; color: <?php echo $textcolor; ?>; background: <?php echo $bgcolor; ?>"><?php echo $status; ?></span></td>
            </tr>
            <?php if($ticketuser) { ?>
            <tr>
              <td><?php echo $text_name; ?>:</td>
              <td class="text-right"><a href="<?php echo $ticketuser_link; ?>"><?php echo $ticketuser; ?></a></td>
            </tr>
            <?php } ?>
            <tr>
              <td><?php echo $text_registered_status; ?>:</td>
              <td class="text-right">
                <?php if($ticketuser_link) { ?>
                  <label class="label label-success"><?php echo $registered_status; ?></label>
                <?php } else { ?>
                <label class="label label-danger"><?php echo $registered_status; ?></label>
                <?php } ?>
                </td>
            </tr>
            <tr>
              <td><?php echo $text_email; ?>:</td>
              <td class="text-right"><?php echo $email; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_subject; ?>:</td>
              <td class="text-right"><?php echo $subject; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_department; ?>:</td>
              <td class="text-right"><?php echo $ticketdepartment; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_ip; ?>:</td>
              <td class="text-right"><?php echo $ip; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_date_added; ?>:</td>
              <td class="text-right"><?php echo $date_added; ?></td>
            </tr>
            <tr>
              <td><?php echo $text_date_modified; ?>:</td>
              <td class="text-right"><?php echo $date_modified; ?></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="col-sm-8">
        <div class="alert alert-success ticket-thred">
          <h2><?php echo $subject; ?></h2>
          <?php echo $message; ?>
          <br/><br/>
          <?php if($attachments) { ?>
          <div class="ticket-attachments">
            <?php foreach($attachments as $attachment) { ?>
            <p><a href="<?php echo $attachment['href']; ?>"><i class="fa fa-paperclip"></i> <?php echo $attachment['name']; ?></a></p>
            <?php } ?>
          </div>
          <?php } ?>
        </div>
        <div class="msg-list">
          <?php if($chats) { ?>
          <ul class="list-unstyled">
            <?php foreach($chats as $chat) { ?>
            <li>
              <div class="f-row clearfix">
                <?php if($chat['profile_photo']) { ?>
                <div class="icon"><img src="<?php echo $chat['profile_photo']; ?>" class="img-responsive"></div>
                <?php } ?>
                <div class="name">
                  <h4><?php echo $chat['client_name']; ?> <span class="<?php echo $chat['client_type'] == 'staff' ? 'purple-tag' : 'yellow-tag'; ?>"><?php echo $chat['client_type_name']; ?></span></h4>
                  <time><?php echo $chat['date_added']; ?></time>
                </div>
              </div>
              <div class="matter">
                <p><?php echo $chat['message']; ?></p>
              </div>
              <?php if($chat['attachments']) { ?>
	            <div class="ticket-attachments">
	              <?php foreach($chat['attachments'] as $attachment) { ?>
	              <p><a href="<?php echo $attachment['href']; ?>"><i class="fa fa-paperclip"></i> <?php echo $attachment['name']; ?></a></p>
	              <?php } ?>
	            </div>
            	<?php } ?>
            </li>
            <?php } ?>
          </ul>
          <?php } ?>
          <?php if($pagination) { ?>
          <div class="row">
            <div class="col-sm-12 text-right"><?php echo $pagination; ?></div>
          </div>
          <?php } ?>
          <?php if($reply_section) { ?>
          <div class="reply-section">
	          <div class="post-reply">
	            <label><?php echo $text_reply; ?></label>
	            <textarea name="message" class="form-control" rows="7" id="responsetext"></textarea>
	          </div>
	          <div class="form-group row">
		          <label class="col-sm-12 control-label"><?php echo $entry_attachments; ?></label>
	        		<div class="col-sm-6">
	        			<button type="button" id="button-upload" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block"><i class="fa fa-paperclip"></i> <?php echo $button_add_file; ?></button>
	        		</div>
	          </div>
	          <div class="form-group row">
			        <div class="col-sm-6 upload-group">
			        </div>
		      	</div>
	          <div class="row"> 
	            <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
	            <div class="col-sm-4">
	              <select name="ticketstatus_id" class="form-control">
	                <?php foreach ($ticketstatuses as $ticketstatus) { ?>
	                <?php if ($ticketstatus['ticketstatus_id'] == $ticketstatus_id) { ?>
	                <option value="<?php echo $ticketstatus['ticketstatus_id']; ?>" selected="selected"><?php echo $ticketstatus['name']; ?></option>
	                <?php } else { ?>
	                <option value="<?php echo $ticketstatus['ticketstatus_id']; ?>"><?php echo $ticketstatus['name']; ?></option>
	                <?php } ?>
	                <?php } ?>
	              </select>
	            </div>
	            <div class="col-sm-2">
	              <button type="button" class="btn btn-primary btn-block button-reply-submit"><i class="fa fa-send"></i> <?php echo $button_submit; ?></button>
	            </div>
	          </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
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
        <?php if(VERSION <= '2.3.0.2') { ?>
        url: 'index.php?route=module_ticket/ticketrequest/fileupload&token=<?php echo $session_token; ?>',
        <?php } else { ?>
        url: 'index.php?route=module_ticket/ticketrequest/fileupload&user_token=<?php echo $session_token; ?>',
        <?php } ?>
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

$(document).delegate('.me-remove', 'click', function() {
  $(this).parent().remove();
});

$('.button-reply-submit').click(function() {
	$.ajax({
    url: '<?php echo $reply_action; ?>',
    type: 'post',
    data: $('.reply-section input, .reply-section textarea, .reply-section select'),
    dataType: 'json',
    beforeSend: function() {
      $('.button-reply-submit').button('loading');
    },
    complete: function() {
        $('.button-reply-submit').button('reset');        
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
});
//--></script>
</div>
<?php echo $footer; ?>