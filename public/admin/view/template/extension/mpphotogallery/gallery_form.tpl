<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="m-menu-wrap">
  <div class="container-fluid">
    <div class="page-header">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php echo $mtabs; ?>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?> <?php if ($gallery_id) { ?>- #<?php echo $gallery_id; ?><?php } ?></h3>
        <div class="pull-right">
        <button type="submit" form="form-gallery" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
        <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-times"></i> <?php echo $button_cancel; ?></a></div>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-gallery" class="form-horizontal">
          <?php if ($error_warning) { ?>
          <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
          <?php } ?>
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-languagesetting" data-toggle="tab"><?php echo $tab_languagesetting; ?></a></li>
            <li><a href="#tab-albumsetting" data-toggle="tab"><?php echo $tab_albumsetting; ?></a></li>
            <li><a href="#tab-photo" data-toggle="tab"><?php echo $tab_photo; ?></a></li>
            <li><a href="#tab-video" data-toggle="tab"><?php echo $tab_video; ?></a></li>
            <!-- // gallery for product task starts -->
            <li><a href="#tab-links" data-toggle="tab"><?php echo $tab_links; ?></a></li>
            <!-- // gallery for product task ends -->
          </ul>
          <div class="tab-content">
						<div class="tab-pane" id="tab-albumsetting">
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_image; ?></label>
                <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                </div>
              </div>
							<div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="status" id="input-status" class="form-control">
                    <?php if ($status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>


              <!-- // 07-05-2022: updation task start -->
              <?php if (VERSION >= '3.0.0.0') { ?>

                <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $help_keyword; ?></div>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <td class="text-left"><?php echo $entry_store; ?></td>
                        <td class="text-left"><?php echo $entry_keyword; ?></td>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stores as $store) { ?>
                      <tr>
                        <td class="text-left"><?php echo $store['name']; ?></td>
                        <td class="text-left">
                        <?php foreach ($languages as $language) { ?>
                          <div class="input-group"><span class="input-group-addon"><img src="<?php echo $language['lang_flag']; ?>" title="<?php echo $language['name']; ?>" /></span>
                            <input type="text" name="gallery_seo_url[<?php echo $store['store_id']; ?>][<?php echo $language['language_id']; ?>]" value="<?php echo isset($gallery_seo_url[$store['store_id']][$language['language_id']]) ? $gallery_seo_url[$store['store_id']][$language['language_id']] : ''; ?>" placeholder="<?php echo $entry_keyword; ?>" class="form-control" />
                          </div>
                           <?php if (!empty($error_keyword[$store['store_id']][$language['language_id']])) { ?>
                            <div class="text-danger"><?php echo $error_keyword[$store['store_id']][$language['language_id']]; ?></div>
                          <?php } ?>
                          <?php } ?>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>

              <?php } else { ?>

                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-keyword"><?php echo $entry_keyword; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" class="form-control" />
                    <div class="help"><?php echo $help_keyword; ?></div>
                    <?php if ($error_keyword) { ?>
                    <div class="text-danger"><?php echo $error_keyword; ?></div>
                    <?php } ?>
                  </div>
                </div>

              <?php } ?>
              <!-- // 07-05-2022: updation task end -->
            </div>
            <div class="tab-pane active" id="tab-languagesetting">
							<ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab">
                	<?php if (VERSION >= '2.2.0.0') { ?>
                    <img src="<?php echo $language['lang_flag']; ?>" title="<?php echo $language['name']; ?>" />
                    <?php } else{ ?>
                    <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                    <?php } ?>
                  <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-title<?php echo $language['language_id']; ?>"><?php echo $entry_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="gallery_description[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($gallery_description[$language['language_id']]) ? $gallery_description[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group hide">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="gallery_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>" class="form-control summernote" data-toggle="summernote" data-lang="<?php echo $summernote; ?>"><?php echo isset($gallery_description[$language['language_id']]['description']) ? $gallery_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-top-description<?php echo $language['language_id']; ?>"><?php echo $entry_top_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="gallery_description[<?php echo $language['language_id']; ?>][top_description]" placeholder="<?php echo $entry_description; ?>" id="input-top-description<?php echo $language['language_id']; ?>" class="form-control summernote" data-toggle="summernote" data-lang="<?php echo $summernote; ?>"><?php echo isset($gallery_description[$language['language_id']]['top_description']) ? $gallery_description[$language['language_id']]['top_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-bottom-description<?php echo $language['language_id']; ?>"><?php echo $entry_bottom_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="gallery_description[<?php echo $language['language_id']; ?>][bottom_description]" placeholder="<?php echo $entry_description; ?>" id="input-bottom-description<?php echo $language['language_id']; ?>" class="form-control summernote" data-toggle="summernote" data-lang="<?php echo $summernote; ?>"><?php echo isset($gallery_description[$language['language_id']]['bottom_description']) ? $gallery_description[$language['language_id']]['bottom_description'] : ''; ?></textarea>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="gallery_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($gallery_description[$language['language_id']]) ? $gallery_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="gallery_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($gallery_description[$language['language_id']]) ? $gallery_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                    <div class="col-sm-10">
                      <textarea name="gallery_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($gallery_description[$language['language_id']]) ? $gallery_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane" id="tab-photo">
              <div class="form-group">
                <div class="col-sm-12">
                  <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $help_photo_image; ?></div>
                </div>
                <label class="col-sm-2 control-label"><?php echo $entry_photo_image; ?></label>
                <div class="col-sm-5">
                  <div class="input-group">
                  <input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                  </span></div>
                </div>
                <div class="col-sm-5">
                  <div class="input-group">
                  <input type="text" name="height" value="<?php echo $height; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                  </span></div>
                </div>
              </div>
              <div class="table-responsive">
                <table id="photo-value" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left required"><?php echo $entry_title; ?></td>
                      <td class="text-left"><?php echo $entry_photo; ?></td>
                      <td class="text-center"><?php echo $entry_link; ?></td>
                      <td class="text-center"><?php echo $entry_highlight; ?></td>
                      <td class="text-right"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $gallery_photo_row = 0; ?>
                    <?php foreach ($gallery_photos as $gallery_photo) { ?>
                    <tr id="photo-value-row<?php echo $gallery_photo_row; ?>">
                      <td class="text-left"><input type="hidden" name="gallery_photo[<?php echo $gallery_photo_row; ?>][gallery_photo_id]" value="<?php echo $gallery_photo['gallery_photo_id']; ?>" />
                      <?php foreach ($languages as $language) { ?>
                      <div class="input-group"><span class="input-group-addon">
                        <?php if (VERSION >= '2.2.0.0') { ?>
                        <img src="<?php echo $language['lang_flag']; ?>" title="<?php echo $language['name']; ?>" />
                        <?php } else{ ?>
                        <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                        <?php } ?>
                      </span>
                        <input type="text" name="gallery_photo[<?php echo $gallery_photo_row; ?>][gallery_photo_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($gallery_photo['gallery_photo_description'][$language['language_id']]) ? $gallery_photo['gallery_photo_description'][$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" class="form-control" />
                      </div>
                      <?php if (isset($error_gallery_photo[$gallery_photo_row][$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_gallery_photo[$gallery_photo_row][$language['language_id']]; ?></div>
                      <?php } ?>
                      <?php } ?></td>
                      <td class="text-left"><a href="" id="thumb-photo<?php echo $gallery_photo_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $gallery_photo['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                        <input type="hidden" name="gallery_photo[<?php echo $gallery_photo_row; ?>][photo]" value="<?php echo $gallery_photo['photo']; ?>" id="input-photo<?php echo $gallery_photo_row; ?>" /></td>
                      <td class="text-right"><input type="text" name="gallery_photo[<?php echo $gallery_photo_row; ?>][link]" value="<?php echo $gallery_photo['link']; ?>" class="form-control" /></td>
                      <td class="text-center" width="15%">
                          <input type="checkbox" name="gallery_photo[<?php echo $gallery_photo_row; ?>][highlight]" class="highlight" value="1" <?php echo (!empty($gallery_photo['highlight'])) ? 'checked="checked" data-highlight="1"' : 'data-highlight="0"'; ?> />
                      </td>
                      <td class="text-right"><input type="text" name="gallery_photo[<?php echo $gallery_photo_row; ?>][sort_order]" value="<?php echo $gallery_photo['sort_order']; ?>" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#photo-value-row<?php echo $gallery_photo_row; ?>').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></td>
                    </tr>
                    <?php $gallery_photo_row++; ?>
                    <?php } ?>
                 </tbody>
                 <tfoot>
                   <tr>
                      <td colspan="5"></td>
                      <td class="text-left"><button type="button" onclick="addGalleryphoto();" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_photo_add; ?></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-video">
              <div class="form-group">
                <div class="col-sm-12">
                  <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $help_video_thumb_image; ?></div>
                </div>
                <label class="col-sm-2 control-label"><?php echo $entry_video_thumb_size; ?></label>
                <div class="col-sm-5">
                  <div class="input-group">
                  <input type="text" name="video_width" value="<?php echo $video_width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                  </span></div>
                </div>
                <div class="col-sm-5">
                  <div class="input-group">
                  <input type="text" name="video_height" value="<?php echo $video_height; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                  </span></div>
                </div>
              </div>
              <div class="table-responsive">
                <table id="video-value" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left required"><?php echo $entry_title; ?></td>
                      <td class="text-left"><?php echo $entry_video_thumb; ?></td>
                      <td class="text-center"><?php echo $entry_link; ?></td>
                      <td class="text-center"><?php echo $entry_highlight; ?></td>
                      <td class="text-right"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $gallery_video_row = 0; ?>
                    <?php foreach ($gallery_videos as $gallery_video) { ?>
                    <tr id="video-value-row<?php echo $gallery_video_row; ?>">
                      <td class="text-left"><input type="hidden" name="gallery_video[<?php echo $gallery_video_row; ?>][gallery_video_id]" value="<?php echo $gallery_video['gallery_video_id']; ?>" />
                      <?php foreach ($languages as $language) { ?>
                      <div class="input-group"><span class="input-group-addon">
                        <?php if (VERSION >= '2.2.0.0') { ?>
                        <img src="<?php echo $language['lang_flag']; ?>" title="<?php echo $language['name']; ?>" />
                        <?php } else{ ?>
                        <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                        <?php } ?>
                      </span>
                        <input type="text" name="gallery_video[<?php echo $gallery_video_row; ?>][gallery_video_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($gallery_video['gallery_video_description'][$language['language_id']]) ? $gallery_video['gallery_video_description'][$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" class="form-control" />
                      </div>
                      <?php if (isset($error_gallery_video[$gallery_video_row][$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_gallery_video[$gallery_video_row][$language['language_id']]; ?></div>
                      <?php } ?>
                      <?php } ?></td>
                      <td class="text-left"><a href="" id="thumb-video<?php echo $gallery_video_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $gallery_video['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                        <input type="hidden" name="gallery_video[<?php echo $gallery_video_row; ?>][video_thumb]" value="<?php echo $gallery_video['video_thumb']; ?>" id="input-video-thumb<?php echo $gallery_video_row; ?>" /></td>
                      <td class="text-right"><input type="text" name="gallery_video[<?php echo $gallery_video_row; ?>][link]" value="<?php echo $gallery_video['link']; ?>" class="form-control" /></td>
                      <td class="text-center" width="15%">
                        <input type="checkbox" name="gallery_video[<?php echo $gallery_video_row; ?>][highlight]" class="video_highlight" value="1" <?php echo (!empty($gallery_video['highlight'])) ? 'checked="checked" data-highlight="1"' : 'data-highlight="0"'; ?> />
                      </td>
                      <td class="text-right"><input type="text" name="gallery_video[<?php echo $gallery_video_row; ?>][sort_order]" value="<?php echo $gallery_video['sort_order']; ?>" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#video-value-row<?php echo $gallery_video_row; ?>').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></td>
                    </tr>
                    <?php $gallery_video_row++; ?>
                    <?php } ?>
                 </tbody>
                 <tfoot>
                   <tr>
                      <td colspan="5"></td>
                      <td class="text-left"><button type="button" onclick="addGalleryVideo();" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_video_add; ?></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <!-- // gallery for product task starts -->
            <!-- // 07-05-2022: updation task start -->
            <div class="tab-pane" id="tab-links">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-product"><?php echo $entry_product; ?><br/><span class="help"><?php echo $help_product; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="product" value="" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control" /> <br/>
                  <div class="table-responsive">
                    <table id="gallery-product" class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr>
                          <td width="15%" class="text-center"><?php echo $column_product_image; ?></td>
                          <td width="30%" class="text-center"><?php echo $column_product_name; ?></td>
                          <td width="20%" class="text-center"><?php echo $column_gallery_video; ?></td>
                          <td width="20%" class="text-center"><?php echo $column_gallery_image; ?></td>
                          <td width="15%" class="text-center"><?php echo $column_remove; ?></td>
                        </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($gallery_products as $gallery_product) { ?>
                      <tr id="gallery-product<?php echo $gallery_product['product_id']; ?>">
                        <td width="15%" class="text-center"><img src="<?php echo $gallery_product['thumb']; ?>" alt="<?php echo $gallery_product['name']; ?>"><input type="hidden" name="gallery_products[<?php echo $gallery_product['product_id']; ?>][product_id]" value="<?php echo $gallery_product['product_id']; ?>" /></td>
                        <td width="30%" class="text-center"><?php echo $gallery_product['name']; ?></td>
                        <td width="20%" class="text-center">
                          <select name="gallery_products[<?php echo $gallery_product['product_id']; ?>][video]" id="input-gallery_videos<?php echo $gallery_product['product_id']; ?>" class="form-control">
                            <?php if ($gallery_product['video'] == '1') { ?>
                            <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                            <option value="0"><?php echo $text_no; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_yes; ?></option>
                            <option value="0" selected="selected"><?php echo $text_no; ?></option>
                            <?php } ?>
                          </select>
                        </td>
                        <td width="20%" class="text-center">
                          <select name="gallery_products[<?php echo $gallery_product['product_id']; ?>][image]" id="input-gallery_images<?php echo $gallery_product['product_id']; ?>" class="form-control">
                            <?php if ($gallery_product['image'] == '1') { ?>
                            <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                            <option value="0"><?php echo $text_no; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_yes; ?></option>
                            <option value="0" selected="selected"><?php echo $text_no; ?></option>
                            <?php } ?>
                          </select>
                        </td>
                        <td width="15%" class="text-center"><button type="button" onclick="$('#gallery-product<?php echo $gallery_product['product_id']; ?>').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></td>
                      </tr>
                      <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- // 07-05-2022: updation task end -->
            <!-- // gallery for product task ends -->
          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--

$('#language a:first').tab('show');
$('#option a:first').tab('show');
//--></script>
<script type="text/javascript"><!--
var gallery_video_row = <?php echo $gallery_video_row; ?>;

function addGalleryVideo() {
  html  = '<tr id="video-value-row' + gallery_video_row + '">';
    html += '  <td class="text-left"><input type="hidden" name="gallery_video[' + gallery_video_row + '][gallery_video_id]" value="" />';
   <?php foreach ($languages as $language) { ?>
      html += '    <div class="input-group">';
      html += '      <span class="input-group-addon"><img src="<?php echo $language['lang_flag']; ?>" title="<?php echo $language['name']; ?>" /></span><input type="text" name="gallery_video[' + gallery_video_row + '][gallery_video_description][<?php echo $language['language_id']; ?>][name]" value="" placeholder="<?php echo $entry_title; ?>" class="form-control" />';
      html += '    </div>';
  <?php } ?>
  html += '  </td>';
    html += '  <td class="text-left"><a href="" id="thumb-video' + gallery_video_row + '" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="gallery_video[' + gallery_video_row + '][video_thumb]" value="" id="input-video-thumb' + gallery_video_row + '" /></td>';
  html += '  <td class="text-right"><input type="text" name="gallery_video[' + gallery_video_row + '][link]" value="" placeholder="<?php echo $entry_link; ?>" class="form-control" /></td>';
  html += '  <td class="text-center" width="15%"><input type="checkbox" name="gallery_video['+ gallery_video_row +'][highlight]" class="video_highlight" data-highlight="0" value="1" /></td>';
  html += '  <td class="text-right"><input type="text" name="gallery_video[' + gallery_video_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#video-value-row' + gallery_video_row + '\').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></td>';
  html += '</tr>';

  $('#video-value tbody').append(html);

  gallery_video_row++;
}
//--></script>
<script type="text/javascript"><!--
var gallery_photo_row = <?php echo $gallery_photo_row; ?>;

function addGalleryphoto() {
	html  = '<tr id="photo-value-row' + gallery_photo_row + '">';
    html += '  <td class="text-left"><input type="hidden" name="gallery_photo[' + gallery_photo_row + '][gallery_photo_id]" value="" />';
	 <?php foreach ($languages as $language) { ?>
			html += '    <div class="input-group">';
			html += '      <span class="input-group-addon"><img src="<?php echo $language['lang_flag']; ?>" title="<?php echo $language['name']; ?>" /></span><input type="text" name="gallery_photo[' + gallery_photo_row + '][gallery_photo_description][<?php echo $language['language_id']; ?>][name]" value="" placeholder="<?php echo $entry_title; ?>" class="form-control" />';
			html += '    </div>';
	<?php } ?>
	html += '  </td>';
    html += '  <td class="text-left"><a href="" id="thumb-photo' + gallery_photo_row + '" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="gallery_photo[' + gallery_photo_row + '][photo]" value="" id="input-photo' + gallery_photo_row + '" /></td>';
  html += '  <td class="text-right"><input type="text" name="gallery_photo[' + gallery_photo_row + '][link]" value="" placeholder="<?php echo $entry_link; ?>" class="form-control" /></td>';
    html += '  <td class="text-center" width="15%"><input type="checkbox" name="gallery_photo['+ gallery_photo_row +'][highlight]" class="highlight" data-highlight="0" value="1" /></td>';
	html += '  <td class="text-right"><input type="text" name="gallery_photo[' + gallery_photo_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#photo-value-row' + gallery_photo_row + '\').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></td>';
	html += '</tr>';

	$('#photo-value tbody').append(html);

	gallery_photo_row++;
}


$(document).delegate('.highlight', 'change', function() {
  // Remove HighLight
  $('.highlight').each(function() {
    if ($(this).attr('data-highlight') == 1) {
      $(this).prop('checked', false);
      $(this).attr('data-highlight', 0);
    }
  });

   // Remove Me if false
   if ($(this).prop('checked') == false) {
    $(this).prop('checked', false);
    $(this).attr('data-highlight', 0);
   }

   // Remove Me if true
   if ($(this).prop('checked') == true) {
    $(this).prop('checked', true);
    $(this).attr('data-highlight', 1);
   }
});

$(document).delegate('.video_highlight', 'change', function() {
  // Remove HighLight
  $('.video_highlight').each(function() {
    if ($(this).attr('data-highlight') == 1) {
      $(this).prop('checked', false);
      $(this).attr('data-highlight', 0);
    }
  });

   // Remove Me if false
   if ($(this).prop('checked') == false) {
    $(this).prop('checked', false);
    $(this).attr('data-highlight', 0);
   }

   // Remove Me if true
   if ($(this).prop('checked') == true) {
    $(this).prop('checked', true);
    $(this).attr('data-highlight', 1);
   }
});
//--></script>
<?php if (VERSION >= '3.0.0.0') { ?>
<link href="view/javascript/codemirror/lib/codemirror.css" rel="stylesheet" />
<link href="view/javascript/codemirror/theme/monokai.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="view/javascript/codemirror/lib/xml.js"></script>
<script type="text/javascript" src="view/javascript/codemirror/lib/formatting.js"></script>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/summernote-image-attributes.js"></script>
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php } elseif (VERSION <= '2.2.0.0') { ?>
  <script type="text/javascript"><!--
  <?php foreach ($languages as $language) { ?>
  $('#input-description<?php echo $language['language_id']; ?>').summernote({ height: 300 });
  $('#input-bottom-description<?php echo $language['language_id']; ?>').summernote({ height: 300 });
  $('#input-top-description<?php echo $language['language_id']; ?>').summernote({ height: 300 });
  <?php } ?>
  //--></script>
<?php } else { ?>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php } ?>
</div>
<!-- // gallery for product task starts -->
<!-- // 07-05-2022: updation task start -->
<script type="text/javascript">
  // Related
$('input[name=\'product\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=extension/mpphotogallery/gallery/productAutocomplete&<?php echo $get_token; ?>=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['product_id'],
            thumb: item['thumb']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'product\']').val('');

    $('#gallery-product' + item['value']).remove();

    var html = '<tr id="gallery-product' + item['value'] + '">';
      html += '<td width="15%" class="text-center"><img src="' + item['thumb'] + '" alt="' + item['label'] + '"><input type="hidden" name="gallery_products[' + item['value'] + '][product_id]" value="' + item['value'] + '" /></td>';
      html += '<td width="30%" class="text-center">' + item['label'] + '</td>';
      html += '<td width="20%" class="text-center">';
        html += '<select name="gallery_products[' + item['value'] + '][video]" id="input-gallery_videos' + item['value'] + '" class="form-control">';
          html += '<option value="1"><?php echo $text_yes; ?></option>';
          html += '<option value="0" selected="selected"><?php echo $text_no; ?></option>';
        html += '</select>';
      html += '</td>';
      html += '<td width="20%" class="text-center">';
        html += '<select name="gallery_products[' + item['value'] + '][image]" id="input-gallery_images' + item['value'] + '" class="form-control">';
          html += '<option value="1" selected="selected"><?php echo $text_yes; ?></option>';
          html += '<option value="0"><?php echo $text_no; ?></option>';
        html += '</select>';
      html += '</td>';
      html += '<td width="15%" class="text-center"><button type="button" onclick="$(\'#gallery-product' + item['value'] + '\').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></td>';
    html += '</tr>';

    $('#gallery-product > tbody').append(html);
  }
});
</script>
<!-- // 07-05-2022: updation task end -->
<!-- // gallery for product task ends -->
<?php echo $footer; ?>