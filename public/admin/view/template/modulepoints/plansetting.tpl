<?php echo $header; ?>
<style>
.form-group {padding-top: 0px; margin-bottom: 20px;}
.control-label.col-sm-12 { text-align: left; margin-bottom: 5px;}
.ostab{background: #f0f1f5; border:1px solid #ddd; padding: 10px;}
.ostab li{border:1px solid #ddd;background: #e4e6ea;margin-top: 4px;margin-bottom: 4px;}
.ostab li.active{border:none;}
.ostab li a{color: #000;padding: 16px;}
.saveme {padding-top: 12px; padding-bottom: 12px; font-size: 14px;}
.mp-buttons .btn-primary { background-color : #fff; border-color: #ccc; color: #333;}
.mp-buttons .btn-primary:hover{ background-color: #1E91CF; border-color: #1978ab; color: #fff; }
.mp-buttons .btn-primary.active{ background-color: #3E5776; border-color: #3E5776; color: #fff; }
</style>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
        <div class="pull-right">
          <div class="storeset pull-left dropdown">
            <span><?php echo $text_store; ?></span>
            <button type="button" data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle"><span><?php echo $store_name; ?></span> <i class="fa fa-angle-down"></i></button>
            <ul class="dropdown-menu dropdown-menu-right">
              <?php foreach($stores as $store) { ?>
              <?php if(VERSION <= '2.3.0.2') { ?>
              <li><a href="index.php?route=modulepoints/plansetting&token=<?php echo $token; ?>&store_id=<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></a></li>
              <?php } else { ?>
              <li><a href="index.php?route=modulepoints/plansetting&user_token=<?php echo $user_token; ?>&store_id=<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></a></li>
              <?php } ?>
              <?php } ?>
            </ul>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-plansetting" class="form-horizontal">
          <div class="row">
            <div class="col-sm-3">
              <ul class="nav nav-pills nav-stacked ostab">
                <li class="active"><a href="#tab-general" data-toggle="tab"><i class="fa fa-cog"></i> <?php echo $tab_general; ?></a></li>
                <li><a href="#tab-linguial" data-toggle="tab"><i class="fa fa-language"></i> <?php echo $tab_linguial; ?></a></li>
                <li><a href="#tab-information" data-toggle="tab"><i class="fa fa-tag"></i> <?php echo $tab_information; ?></a></li>
              </ul>
              <br>
              <button type="submit" form="form-plansetting" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="saveme btn btn-success active btn-block"><?php echo $button_save; ?></button>
            </div>
            <div class="col-sm-9">              
              <div class="tab-content">
                <div class="tab-pane active" id="tab-general">
                  <div class="form-group mp-buttons">
                    <label class="col-sm-12 control-label"><?php echo $entry_status; ?></label>
                    <div class="col-sm-4">
                      <div class="btn-group btn-group-justified" data-toggle="buttons">
                        <label class="btn btn-primary <?php echo !empty($mpplan_status) ? 'active' : ''; ?>">
                          <input type="radio" name="mpplan_status" value="1" <?php echo (!empty($mpplan_status)) ? 'checked="checked"' : ''; ?> />
                          <?php echo $text_enabled; ?>                            
                        </label>
                        <label class="btn btn-primary <?php echo empty($mpplan_status) ? 'active' : ''; ?>">
                          <input type="radio" name="mpplan_status" value="0" <?php echo (empty($mpplan_status)) ? 'checked="checked"' : ''; ?> />
                          <?php echo $text_disabled; ?>                            
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-12 control-label" for="input-product"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_product; ?></span></label>
                    <div class="col-sm-12">
                      <input type="text" name="mpplan_product" value="<?php echo $mpplan_product; ?>" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control" />
                      <input type="hidden" name="mpplan_product_id" value="<?php echo $mpplan_product_id; ?>" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-12 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                    <div class="col-sm-12">
                      <select name="mpplan_status_id" id="input-order-status" class="form-control">
                        <?php foreach ($order_statuses as $order_statuses) { ?>
                        <?php if ($order_statuses['order_status_id'] == $mpplan_status_id) { ?>
                        <option value="<?php echo $order_statuses['order_status_id']; ?>" selected="selected"><?php echo $order_statuses['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_statuses['order_status_id']; ?>"><?php echo $order_statuses['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-12 control-label"><span data-toggle="tooltip" title="<?php echo $help_product_size; ?>"><?php echo $entry_product_size; ?></span></label>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="mpplan_product_width" value="<?php echo $mpplan_product_width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />                  
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-h"></i></button>
                        </span>
                      </div>
                      <?php if ($error_product_size) { ?>
                      <div class="text-danger"><?php echo $error_product_size; ?></div>
                      <?php } ?>
                    </div>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="mpplan_product_height" value="<?php echo $mpplan_product_height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary"><i class="fa fa-arrows-v"></i></button>
                        </span></div>
                    </div>
                  </div>
                  <div class="form-group mp-buttons">
                    <label class="col-sm-12 control-label"><?php echo $entry_design; ?></label>
                    <div class="col-sm-3">
                      <div class="btn-group btn-group-justified" data-toggle="buttons">
                        <label class="btn btn-primary <?php echo $mpplan_design == '1' ? 'active' : ''; ?>">
                          <input type="radio" name="mpplan_design" value="1" <?php echo $mpplan_design == '1' ? 'checked="checked"' : ''; ?> />
                          <?php echo $text_template_1; ?>                            
                        </label>
                        <label class="btn btn-primary <?php echo $mpplan_design == '2' ? 'active' : ''; ?>">
                          <input type="radio" name="mpplan_design" value="2" <?php echo $mpplan_design == '2' ? 'checked="checked"' : ''; ?> />
                          <?php echo $text_template_2; ?>                            
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="tab-linguial">
                  <ul class="nav nav-tabs" id="language">
                    <?php foreach ($languages as $language) { ?>
                    <li>
                      <a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab">
                        <?php if(VERSION >= '2.2.0.0') { ?>
                        <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" />
                        <?php } else { ?>
                        <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                        <?php } ?>
                        <?php echo $language['name']; ?>
                      </a>
                    </li>
                    <?php } ?>
                  </ul>
                  <div class="tab-content">
                    <?php foreach ($languages as $language) { ?>
                    <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                      <div class="form-group required">
                        <label class="col-sm-12 control-label" for="input-title<?php echo $language['language_id']; ?>"><?php echo $entry_title; ?></label>
                        <div class="col-sm-12">
                          <input type="text" name="mpplan_description[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
                          <?php if (isset($error_title[$language['language_id']])) { ?>
                          <div class="text-danger"><?php echo $error_title[$language['language_id']]; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-12 control-label" for="input-sub-title<?php echo $language['language_id']; ?>"><?php echo $entry_sub_title; ?></label>
                        <div class="col-sm-12">
                          <input type="text" name="mpplan_description[<?php echo $language['language_id']; ?>][sub_title]" value="<?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['sub_title'] : ''; ?>" placeholder="<?php echo $entry_sub_title; ?>" id="input-sub-title<?php echo $language['language_id']; ?>" class="form-control" />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-12 control-label" for="input-top-description<?php echo $language['language_id']; ?>"><?php echo $entry_top_description; ?></label>
                        <div class="col-sm-12">
                          <textarea name="mpplan_description[<?php echo $language['language_id']; ?>][top_description]" placeholder="<?php echo $entry_top_description; ?>" id="input-top-description<?php echo $language['language_id']; ?>" class="form-control" data-toggle="summernote" data-lang="{{ summernote }}"><?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['top_description'] : ''; ?></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-12 control-label" for="input-bottom-description<?php echo $language['language_id']; ?>"><?php echo $entry_bottom_description; ?></label>
                        <div class="col-sm-12">
                          <textarea name="mpplan_description[<?php echo $language['language_id']; ?>][bottom_description]" placeholder="<?php echo $entry_bottom_description; ?>" id="input-bottom-description<?php echo $language['language_id']; ?>" class="form-control" data-toggle="summernote" data-lang="{{ summernote }}"><?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['bottom_description'] : ''; ?></textarea>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-sm-12 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                        <div class="col-sm-12">
                          <input type="text" name="mpplan_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                          <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                          <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-12 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                        <div class="col-sm-12">
                          <textarea name="mpplan_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-12 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                        <div class="col-sm-12">
                          <textarea name="mpplan_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                        </div>
                      </div>
                    </div>
                    <?php } ?>
                  </div>
                </div>
                <div class="tab-pane" id="tab-information">
                  <div class="form-group mp-buttons">
                    <label class="col-sm-12 control-label"><?php echo $entry_product_status; ?></label>
                    <div class="col-sm-4">
                      <div class="btn-group btn-group-justified" data-toggle="buttons">
                        <label class="btn btn-primary <?php echo !empty($mpplan_product_status) ? 'active' : ''; ?>">
                          <input type="radio" name="mpplan_product_status" value="1" <?php echo (!empty($mpplan_product_status)) ? 'checked="checked"' : ''; ?> />
                          <?php echo $text_yes; ?>                            
                        </label>
                        <label class="btn btn-primary <?php echo empty($mpplan_product_status) ? 'active' : ''; ?>">
                          <input type="radio" name="mpplan_product_status" value="0" <?php echo (empty($mpplan_product_status)) ? 'checked="checked"' : ''; ?> />
                          <?php echo $text_no; ?>                            
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-12 control-label"><?php echo $entry_buttontext; ?></label>
                    <div class="col-sm-12">
                      <?php foreach ($languages as $language) { ?>
                      <div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                        <input type="text" name="mpplan_description[<?php echo $language['language_id']; ?>][buttontext]" value="<?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['buttontext'] : ''; ?>" placeholder="<?php echo $entry_buttontext; ?>" class="form-control" />
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('#language a:first').tab('show');

// Product
$('input[name=\'mpplan_product\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/product/autocomplete&user_token=<?php echo $user_token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        json.unshift({
          product_id: 0,
          name: '<?php echo $text_none; ?>'
        });

        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['product_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'mpplan_product\']').val(item['label']);
    $('input[name=\'mpplan_product_id\']').val(item['value']);
  }
});
//--></script>

<link href="view/javascript/codemirror/lib/codemirror.css" rel="stylesheet" />
<link href="view/javascript/codemirror/theme/monokai.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/codemirror/lib/codemirror.js"></script> 
<script type="text/javascript" src="view/javascript/codemirror/lib/xml.js"></script> 
<script type="text/javascript" src="view/javascript/codemirror/lib/formatting.js"></script> 

<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/summernote-image-attributes.js"></script>
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
</div>
<?php echo $footer; ?>