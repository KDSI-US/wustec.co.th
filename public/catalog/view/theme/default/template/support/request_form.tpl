<?php echo $header; ?>
<section class="supprt-banner" style="background-image: url('<?php echo $support_banner; ?>')">
  <div class="container">
    <h1><?php echo $banner_title; ?></h1>
  </div>
</section>
<div class="container">
  <ul class="breadcrumb sp-breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <div class="support-form-wrap">
        <h2><?php echo $heading_title; ?></h2>
        <br>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal ticket-form">
          <?php if(!$logged) { ?>
          <div class="form-group required">
            <label class="col-sm-12 control-label"><?php echo $entry_email; ?></label>
            <div class="col-sm-12">
              <input type="email" name="email" class="form-control" placeholder="<?php echo $entry_email; ?>" value="<?php echo $email; ?>" />
              <?php if ($error_email) { ?>
              <div class="text-danger"><?php echo $error_email; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <div class="form-group required">
            <label class="col-sm-12 control-label"><?php echo $entry_subject; ?></label>
            <div class="col-sm-12">
              <input type="text" name="subject" class="form-control" placeholder="<?php echo $entry_subject; ?>" value="<?php echo $subject; ?>" />
              <?php if ($error_subject) { ?>
              <div class="text-danger"><?php echo $error_subject; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-12 control-label"><?php echo $entry_choose; ?></label>
            <div class="col-sm-12">
              <select class="form-control" name="ticketdepartment_id">
                <option value=""><?php echo $text_select; ?></option>
                <?php foreach($ticketdepartments as $ticketdepartment) { ?>
                <?php if($ticketdepartment['ticketdepartment_id'] == $ticketdepartment_id) { ?>
                <option selected="selected" value="<?php echo $ticketdepartment['ticketdepartment_id']; ?>"><?php echo $ticketdepartment['title']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $ticketdepartment['ticketdepartment_id']; ?>"><?php echo $ticketdepartment['title']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if ($error_ticketdepartment) { ?>
              <div class="text-danger"><?php echo $error_ticketdepartment; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-12 control-label"><?php echo $entry_message; ?></label>
            <div class="col-sm-12">
              <textarea class="form-control" name="message" rows="10"><?php echo $message; ?></textarea>
              <?php if ($error_message) { ?>
              <div class="text-danger"><?php echo $error_message; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-12 control-label"><?php echo $entry_attachments; ?></label>
            <div class="col-sm-3">
              <button type="button" id="button-upload" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block"><i class="fa fa-paperclip"></i> <?php echo $button_add_file; ?></button>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-6 upload-group">
              <?php foreach($attachments as $attachment) { ?>
              <div class="new-file">
                <i class="fa fa-paperclip"></i>
                <input type="hidden" name="attachments[]" value="<?php echo $attachment['code']; ?>" />
                <?php echo $attachment['name'] ? $attachment['name'] : $attachment['code']; ?>
                <a class="me-remove pull-right"><i class="fa fa-times"></i></a>
              </div>
              <?php } ?>
            </div>
          </div>
          <?php echo $captcha; ?>
          <div class="buttons-holder text-center">
            <input type="submit" class="button btn-blue" value="<?php echo $button_submit; ?>" />
            <a href="<?php echo $cancel; ?>" class="button btn-gray"><?php echo $button_cancel; ?></a>
            <a class="button btn-gray" href="<?php echo $request_list; ?>"><?php echo $button_request_list; ?></a>
          </div>
        </form>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
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
$(document).delegate('.me-remove', 'click', function() {
  $(this).parent().remove();
});
//--></script>
<?php echo $footer; ?>