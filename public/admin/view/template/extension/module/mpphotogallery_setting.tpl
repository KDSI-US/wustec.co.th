<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="m-menu-wrap">
  <div class="container-fluid">
    <div class="page-header">
      <h1><i class="fa fa-file-image-o"></i> <?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php echo $mtabs; ?>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($files) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $text_files_permission; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <button type="button" class="btn btn-primary" id="addfiles"><i class="fa fa-plus-circle" data-class="fa fa-plus-circle"></i> <?php echo $button_files_permission; ?></button>
    </div>
    <script type="text/javascript">
      $('#addfiles').on('click', function() {
        var button = this;
        $.ajax({
          url: 'index.php?route=extension/module/mpphotogallery_setting/updatePermissions&<?php echo $get_token; ?>=<?php echo $token; ?>',
          type: 'get',
          data: '',
          dataType: 'json',
          beforeSend: function() {
            $(button).attr('disabled','disabled');
            $(button).find('i').attr('class', 'fa fa-refresh fa-spin');
          },
          complete: function() {
            $(button).removeAttr('disabled');
            $(button).find('i').attr('class', $(button).find('i').attr('data-class'));
          },
          success: function(json) {
            $('.alert-dismissible, .text-danger').remove();

            if (json['redirect']) {
              location = json['redirect'];
            }

            if (json['success']) {
              $('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
      });
    </script>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <i class="fa fa-cog"></i>
          <?php echo $text_edit; ?></h3>
        <div class="pull-right">
        <button type="submit" form="form-account" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
        <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-times"></i> <?php echo $button_cancel; ?></a></div>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $text_gsetting; ?></a></li>
          <li><a href="#tab-colors" data-toggle="tab"><?php echo $text_colors; ?></a></li>
          <!-- // gallery for product task starts -->
          <li><a href="#tab-product" data-toggle="tab"><?php echo $text_product; ?></a></li>
          <!-- // gallery for product task ends -->
        </ul>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-account" class="form-horizontal">
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
            <fieldset>
              <h3><?php echo $fieldset_general; ?></h3>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_status) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_status" value="1" <?php echo (!empty($gallery_setting_status)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_enabled; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_status) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_status" value="0" <?php echo (empty($gallery_setting_status)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_disabled; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_social_status; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_social_status) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_social_status" value="1" <?php echo (!empty($gallery_setting_social_status)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_social_status) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_social_status" value="0" <?php echo (empty($gallery_setting_social_status)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-popup_width"><?php echo $entry_popup; ?></label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_popup_width" value="<?php echo $gallery_setting_popup_width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-popup_width" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                        </span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_popup_height" value="<?php echo $gallery_setting_popup_height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-popup_height" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                        </span></div>
                    </div>
                  </div>
                  <span class="help"><?php echo $help_popup; ?></span>
                  <?php if ($error_popup_size) { ?>
                  <div class="text-danger"><?php echo $error_popup_size; ?></div>
                  <?php } ?>
                </div>
              </div>
              <!-- // 07-05-2022: updation task start -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_album_photo; ?></label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_albumphoto_width" value="<?php echo $gallery_setting_albumphoto_width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                        </span></div>
                    </div>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_albumphoto_height" value="<?php echo $gallery_setting_albumphoto_height; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                        </span></div>
                    </div>
                  </div>
                  <span class="help"><?php echo $help_album_photo; ?></span>
                  <?php if ($error_albumphoto_size) { ?>
                  <div class="text-danger"><?php echo $error_albumphoto_size; ?></div>
                  <?php } ?>
                </div>
              </div>
              <!-- // 07-05-2022: updation task end -->
            </fieldset>
            <br/><br/><br/><br/>
            <fieldset>
              <h3><?php echo $fieldset_album_page; ?></h3>
              <div class="form-group hide mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_album_description; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_album_description) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_album_description" value="1" <?php echo (!empty($gallery_setting_album_description)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_album_description) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_album_description" value="0" <?php echo (empty($gallery_setting_album_description)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_album_limit; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="gallery_setting_album_limit" value="<?php echo $gallery_setting_album_limit; ?>" placeholder="<?php echo $entry_album_limit; ?>"class="form-control" />
                  <?php if ($error_album_limit) { ?>
                  <div class="text-danger"><?php echo $error_album_limit; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_album; ?></label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_album_width" value="<?php echo $gallery_setting_album_width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                        </span></div>
                    </div>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_album_height" value="<?php echo $gallery_setting_album_height; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                        </span></div>
                    </div>
                  </div>
                  <span class="help"><?php echo $help_album; ?></span>
                  <?php if ($error_album_size) { ?>
                  <div class="text-danger"><?php echo $error_album_size; ?></div>
                  <?php } ?>
                </div>
              </div>
            </fieldset>
            <br/><br/><br/><br/>
            <fieldset>
              <h3><?php echo $fieldset_photo_page; ?></h3>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_cursive_font; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_photo_cursive_font) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_photo_cursive_font" value="1" <?php echo (!empty($gallery_setting_photo_cursive_font)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_photo_cursive_font) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_photo_cursive_font" value="0" <?php echo (empty($gallery_setting_photo_cursive_font)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_show_videos; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_show_videos) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_show_videos" value="1" <?php echo (!empty($gallery_setting_show_videos)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_show_videos) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_show_videos" value="0" <?php echo (empty($gallery_setting_show_videos)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
            </fieldset>
            <br/><br/><br/><br/>
            <fieldset>
              <h3><?php echo $fieldset_albumn_photo; ?></h3>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_cursive_font; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_albumphoto_cursive_font) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_albumphoto_cursive_font" value="1" <?php echo (!empty($gallery_setting_albumphoto_cursive_font)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_albumphoto_cursive_font) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_albumphoto_cursive_font" value="0" <?php echo (empty($gallery_setting_albumphoto_cursive_font)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_albumphoto_description; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_albumphoto_description) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_albumphoto_description" value="1" <?php echo (!empty($gallery_setting_albumphoto_description)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_albumphoto_description) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_albumphoto_description" value="0" <?php echo (empty($gallery_setting_albumphoto_description)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_albumphoto_limit; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="gallery_setting_albumphoto_limit" value="<?php echo $gallery_setting_albumphoto_limit; ?>" placeholder="<?php echo $entry_albumphoto_limit; ?>" class="form-control" />
                  <?php if ($error_albumphoto_limit) { ?>
                  <div class="text-danger"><?php echo $error_albumphoto_limit; ?></div>
                  <?php } ?>
                </div>
              </div>
            </fieldset>
            <fieldset>
              <h3><?php echo $fieldset_albumn_video; ?></h3>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_cursive_font; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_albumvideo_cursive_font) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_albumvideo_cursive_font" value="1" <?php echo (!empty($gallery_setting_albumvideo_cursive_font)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_albumvideo_cursive_font) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_albumvideo_cursive_font" value="0" <?php echo (empty($gallery_setting_albumvideo_cursive_font)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_albumvideo_description; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_albumvideo_description) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_albumvideo_description" value="1" <?php echo (!empty($gallery_setting_albumvideo_description)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_albumvideo_description) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_albumvideo_description" value="0" <?php echo (empty($gallery_setting_albumvideo_description)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_albumvideo_limit; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="gallery_setting_albumvideo_limit" value="<?php echo $gallery_setting_albumvideo_limit; ?>" placeholder="<?php echo $entry_albumphoto_limit; ?>" class="form-control" />
                  <?php if ($error_albumvideo_limit) { ?>
                  <div class="text-danger"><?php echo $error_albumvideo_limit; ?></div>
                  <?php } ?>
                </div>
              </div>
              <!-- // 07-05-2022: updation task start -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_album_video; ?></label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_albumvideo_width" value="<?php echo $gallery_setting_albumvideo_width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                        </span></div>
                    </div>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_albumvideo_height" value="<?php echo $gallery_setting_albumvideo_height; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                        </span></div>
                    </div>
                  </div>
                  <span class="help"><?php echo $help_album_video; ?></span>
                  <?php if ($error_albumvideo_size) { ?>
                  <div class="text-danger"><?php echo $error_albumvideo_size; ?></div>
                  <?php } ?>
                </div>
              </div>
              <!-- // 07-05-2022: updation task end -->
            </fieldset>
            </div>
            <!-- // gallery for product task starts -->
            <div class="tab-pane" id="tab-product">
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_galleryproduct_status) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_status" value="1" <?php echo (!empty($gallery_setting_galleryproduct_status)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_enabled; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_galleryproduct_status) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_status" value="0" <?php echo (empty($gallery_setting_galleryproduct_status)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_disabled; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $entry_product_title; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($languages as $language) { ?>
                  <div class="input-group">
                    <span class="input-group-addon">
                    <?php if(VERSION >= '2.2.0.0') { ?>
                    <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" />
                    <?php } else { ?>
                    <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                    <?php } ?></span>
                    <input type="text" name="gallery_setting_galleryproduct_description[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($gallery_setting_galleryproduct_description[$language['language_id']]) ? $gallery_setting_galleryproduct_description[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_product_title; ?>" class="form-control" />
                  </div>
                  <?php if (isset($error_galleryproduct_description[$language['language_id']]['title'])) { ?>
                  <div class="text-danger"><?php echo $error_galleryproduct_description[$language['language_id']]['title']; ?></div>
                  <?php } ?>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_galleryproduct_viewas; ?></label>
                <div class="col-sm-5">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo (!$gallery_setting_galleryproduct_viewas || $gallery_setting_galleryproduct_viewas == 'GA') ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_viewas" value="GA" <?php echo (!$gallery_setting_galleryproduct_viewas || $gallery_setting_galleryproduct_viewas == 'GA') ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_gallery_album; ?>
                    </label>
                    <label class="btn btn-primary <?php echo ($gallery_setting_galleryproduct_viewas == 'GAP') ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_viewas" value="GAP" <?php echo ($gallery_setting_galleryproduct_viewas == 'GAP') ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_gallery_album_photos; ?>
                    </label>
                  </div>
                </div>
              </div>
              <!-- // 07-05-2022: updation task start -->
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_override_video; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_galleryproduct_override_video) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_override_video" value="1" <?php echo (!empty($gallery_setting_galleryproduct_override_video)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_galleryproduct_override_video) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_override_video" value="0" <?php echo (empty($gallery_setting_galleryproduct_override_video)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_override_image; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_galleryproduct_override_image) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_override_image" value="1" <?php echo (!empty($gallery_setting_galleryproduct_override_image)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_galleryproduct_override_image) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_override_image" value="0" <?php echo (empty($gallery_setting_galleryproduct_override_image)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <!-- // 07-05-2022: updation task end -->
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-galleryproduct_width"><?php echo $entry_galleryproduct_size; ?></label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_galleryproduct_width" value="<?php echo $gallery_setting_galleryproduct_width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-galleryproduct_width" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                        </span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_galleryproduct_height" value="<?php echo $gallery_setting_galleryproduct_height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-galleryproduct_height" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                        </span></div>
                    </div>
                  </div>
                  <span class="help"><?php echo $help_galleryproduct_size; ?></span>
                  <?php if ($error_galleryproduct_size) { ?>
                  <div class="text-danger"><?php echo $error_galleryproduct_size; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-galleryproduct_photo_width"><?php echo $entry_galleryproduct_photo_size; ?></label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_galleryproduct_photo_width" value="<?php echo $gallery_setting_galleryproduct_photo_width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-galleryproduct_photo_width" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                        </span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="gallery_setting_galleryproduct_photo_height" value="<?php echo $gallery_setting_galleryproduct_photo_height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-galleryproduct_photo_height" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                        </span></div>
                    </div>
                  </div>
                  <span class="help"><?php echo $help_galleryproduct_photo_size; ?></span>
                  <?php if ($error_galleryproduct_photo_size) { ?>
                  <div class="text-danger"><?php echo $error_galleryproduct_photo_size; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_carousel; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_galleryproduct_carousel) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_carousel" value="1" <?php echo (!empty($gallery_setting_galleryproduct_carousel)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_galleryproduct_carousel) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_carousel" value="0" <?php echo (empty($gallery_setting_galleryproduct_carousel)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_extitle; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($gallery_setting_galleryproduct_extitle) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_extitle" value="1" <?php echo (!empty($gallery_setting_galleryproduct_extitle)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_yes; ?>
                    </label>
                    <label class="btn btn-primary <?php echo empty($gallery_setting_galleryproduct_extitle) ? 'active' : ''; ?>">
                      <input type="radio" name="gallery_setting_galleryproduct_extitle" value="0" <?php echo (empty($gallery_setting_galleryproduct_extitle)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_no; ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <!-- // gallery for product task ends -->
            <div class="tab-pane" id="tab-colors">
              <fieldset>
                <h3><?php echo $fieldset_albumn_allphoto; ?></h3>
                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="control-label" for="input-color_title_text"><?php echo $entry_color_title_text; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[title_text]" value="<?php echo !empty($gallery_setting_color['title_text']) ? $gallery_setting_color['title_text']:''; ?>" placeholder="<?php echo $entry_color_title_text; ?>" id="input-color_title_text" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-color_title_text"><?php echo $entry_albumtitle_bg; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[album_title_bg]" value="<?php echo !empty($gallery_setting_color['album_title_bg']) ? $gallery_setting_color['album_title_bg']:''; ?>" placeholder="<?php echo $entry_albumtitle_bg; ?>" id="input-color_album_title_bg" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-color_title_text"><?php echo $entry_color_albumtitle_text; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[albumtitle_text]" value="<?php echo !empty($gallery_setting_color['albumtitle_text']) ? $gallery_setting_color['albumtitle_text']:''; ?>" placeholder="<?php echo $entry_color_albumtitle_text; ?>" id="input-color_albumtitle_text" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-photo_tilte_color"><?php echo $entry_photo_tilte_color; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[photo_tilte_color]" value="<?php echo !empty($gallery_setting_color['photo_tilte_color']) ? $gallery_setting_color['photo_tilte_color']:''; ?>" placeholder="<?php echo $entry_photo_tilte_color; ?>" id="input-photo_tilte_color" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-photo_zoomicon_color"><?php echo $entry_photo_zoomicon_color; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[photo_zoomicon_color]" value="<?php echo !empty($gallery_setting_color['photo_zoomicon_color']) ? $gallery_setting_color['photo_zoomicon_color']:''; ?>" placeholder="<?php echo $entry_photo_zoomicon_color; ?>" id="input-photo_zoomicon_color" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-photo_hoverbg_color"><?php echo $entry_photo_hoverbg_color; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[photo_hoverbg_color]" value="<?php echo !empty($gallery_setting_color['photo_hoverbg_color']) ? $gallery_setting_color['photo_hoverbg_color']:''; ?>" placeholder="<?php echo $entry_photo_hoverbg_color; ?>" id="input-photo_hoverbg_color" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <h3><?php echo $fieldset_albumns; ?></h3>
                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="control-label" for="input-albumsapge_title_text"><?php echo $entry_albumsapge_title_text; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[albumsapge_title_text]" value="<?php echo !empty($gallery_setting_color['albumsapge_title_text']) ? $gallery_setting_color['albumsapge_title_text']:''; ?>" placeholder="<?php echo $entry_albumsapge_title_text; ?>" id="input-albumsapge_title_text" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-albums_detail_text"><?php echo $entry_albums_detail_text; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[albums_detail_text]" value="<?php echo !empty($gallery_setting_color['albums_detail_text']) ? $gallery_setting_color['albums_detail_text']:''; ?>" placeholder="<?php echo $entry_albums_detail_text; ?>" id="input-albums_detail_text" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-albums_wrapbg"><?php echo $entry_albums_wrapbg; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[albums_wrapbg]" value="<?php echo !empty($gallery_setting_color['albums_wrapbg']) ? $gallery_setting_color['albums_wrapbg']:''; ?>" placeholder="<?php echo $entry_albums_wrapbg; ?>" id="input-albums_wrapbg" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <h3><?php echo $fieldset_sharethis; ?></h3>
                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="control-label" for="input-sharethis_bg"><?php echo $entry_sharethis_bg; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[sharethis_bg]" value="<?php echo !empty($gallery_setting_color['sharethis_bg']) ? $gallery_setting_color['sharethis_bg']:''; ?>" placeholder="<?php echo $entry_sharethis_bg; ?>" id="input-sharethis_bg" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-sharethis_bg"><?php echo $entry_sharethis_color; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[sharethis_color]" value="<?php echo !empty($gallery_setting_color['sharethis_color']) ? $gallery_setting_color['sharethis_color']:''; ?>" placeholder="<?php echo $entry_sharethis_color; ?>" id="input-sharethis_color" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <h3><?php echo $fieldset_extension; ?></h3>
                <div class="form-group">
                  <div class="col-sm-6">
                    <label class="control-label" for="input-extitle_bgcolor"><?php echo $entry_extitle_bgcolor; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[extitle_bgcolor]" value="<?php echo !empty($gallery_setting_color['extitle_bgcolor']) ? $gallery_setting_color['extitle_bgcolor']:''; ?>" placeholder="<?php echo $entry_extitle_bgcolor; ?>" id="input-extitle_bgcolor" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-extitle_textcolor"><?php echo $entry_extitle_textcolor; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[extitle_textcolor]" value="<?php echo !empty($gallery_setting_color['extitle_textcolor']) ? $gallery_setting_color['extitle_textcolor']:''; ?>" placeholder="<?php echo $entry_extitle_textcolor; ?>" id="input-extitle_textcolor" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-extitle_bordercolor"><?php echo $entry_extitle_bordercolor; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[extitle_bordercolor]" value="<?php echo !empty($gallery_setting_color['extitle_bordercolor']) ? $gallery_setting_color['extitle_bordercolor']:''; ?>" placeholder="<?php echo $entry_extitle_bordercolor; ?>" id="input-extitle_bordercolor" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-exview_all_color"><?php echo $entry_exview_all_color; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[exview_all_color]" value="<?php echo !empty($gallery_setting_color['exview_all_color']) ? $gallery_setting_color['exview_all_color']:''; ?>" placeholder="<?php echo $entry_exview_all_color; ?>" id="input-exview_all_color" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-carousel_arrow_bgcolor"><?php echo $entry_carousel_arrow_bgcolor; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[carousel_arrow_bgcolor]" value="<?php echo !empty($gallery_setting_color['carousel_arrow_bgcolor']) ? $gallery_setting_color['carousel_arrow_bgcolor']:''; ?>" placeholder="<?php echo $entry_carousel_arrow_bgcolor; ?>" id="input-carousel_arrow_bgcolor" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="control-label" for="input-carousel_arrow_color"><?php echo $entry_carousel_arrow_color; ?></label>
                    <div class="colorpicker colorpicker-component input-group">
                      <input type="text" name="gallery_setting_color[carousel_arrow_color]" value="<?php echo !empty($gallery_setting_color['carousel_arrow_color']) ? $gallery_setting_color['carousel_arrow_color']:''; ?>" placeholder="<?php echo $entry_carousel_arrow_color; ?>" id="input-carousel_arrow_color" class="form-control" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(function() { $('.colorpicker').colorpicker(); });
//--></script>
<?php echo $footer; ?>