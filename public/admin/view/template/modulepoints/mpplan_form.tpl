<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-mpplan" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-mpplan" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
            <li><a href="#tab-link" data-toggle="tab"><?php echo $tab_link; ?></a></li>
            <li><a href="#tab-price" data-toggle="tab"><?php echo $tab_price; ?></a></li>
            <li><a href="#tab-info" data-toggle="tab"><?php echo $tab_info; ?></a></li>
            <li><a href="#tab-color" data-toggle="tab"><?php echo $tab_color; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="mpplan_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-top-description<?php echo $language['language_id']; ?>"><?php echo $entry_top_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="mpplan_description[<?php echo $language['language_id']; ?>][top_description]" placeholder="<?php echo $entry_top_description; ?>" id="input-top-description<?php echo $language['language_id']; ?>" class="form-control" data-toggle="summernote" data-lang="{{ summernote }}"><?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['top_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-bottom-description<?php echo $language['language_id']; ?>"><?php echo $entry_bottom_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="mpplan_description[<?php echo $language['language_id']; ?>][bottom_description]" placeholder="<?php echo $entry_bottom_description; ?>" id="input-bottom-description<?php echo $language['language_id']; ?>" class="form-control" data-toggle="summernote" data-lang="{{ summernote }}"><?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['bottom_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="mpplan_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="mpplan_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                    <div class="col-sm-10">
                      <textarea name="mpplan_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($mpplan_description[$language['language_id']]) ? $mpplan_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane" id="tab-data">
               <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $entry_keyword; ?></div>
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
                        <div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                          <input type="text" name="mpplan_seo_url[<?php echo $store['store_id']; ?>][<?php echo $language['language_id']; ?>]" value="<?php echo isset($mpplan_seo_url[$store['store_id']][$language['language_id']]) ? $mpplan_seo_url[$store['store_id']][$language['language_id']] : ''; ?>" placeholder="<?php echo $entry_keyword; ?>" class="form-control" />
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
            </div>
            <div class="tab-pane" id="tab-color">
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_first_bgcolor; ?></label>
                <div class="col-sm-4">
                  <div class="input-group colorpicker colorpicker-component"> 
                    <input type="text" name="first_bgcolor" value="<?php echo $first_bgcolor; ?>" class="form-control" /> 
                    <span class="input-group-addon"><i></i></span> 
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_first_textcolor; ?></label>
                <div class="col-sm-4">
                  <div class="input-group colorpicker colorpicker-component"> 
                    <input type="text" name="first_textcolor" value="<?php echo $first_textcolor; ?>" class="form-control" /> 
                    <span class="input-group-addon"><i></i></span> 
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_second_bgcolor; ?></label>
                <div class="col-sm-4">
                  <div class="input-group colorpicker colorpicker-component"> 
                    <input type="text" name="second_bgcolor" value="<?php echo $second_bgcolor; ?>" class="form-control" /> 
                    <span class="input-group-addon"><i></i></span> 
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_second_textcolor; ?></label>
                <div class="col-sm-4">
                  <div class="input-group colorpicker colorpicker-component"> 
                    <input type="text" name="second_textcolor" value="<?php echo $second_textcolor; ?>" class="form-control" /> 
                    <span class="input-group-addon"><i></i></span> 
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-price">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-duration"><?php echo $entry_duration; ?></label>
                <div class="col-sm-5">
                  <div class="input-group">
                    <input type="text" name="duration_value" placeholder="<?php echo $entry_duration; ?>" id="input-duration" class="form-control" value="<?php echo $duration_value; ?>" />
                    <span class="input-group-btn" style="width:18%;">
                      <select name="duration_type" class="form-control">
                        <option value="d" <?php echo $duration_type == 'd' ? 'selected="selected"' : ''; ?>><?php echo $text_days; ?></option>
                        <option value="m" <?php echo $duration_type == 'm' ? 'selected="selected"' : ''; ?>><?php echo $text_months; ?></option>
                        <option value="y" <?php echo $duration_type == 'y' ? 'selected="selected"' : ''; ?>><?php echo $text_years; ?></option>
                      </select>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-price"><?php echo $entry_price; ?></label>
                <div class="col-sm-5">
                  <input type="text" name="price" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" value="<?php echo $price; ?>" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-price"><?php echo $entry_discount; ?></label>
                <div class="col-sm-5">
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <td class="text-left"><?php echo $entry_customer_group; ?></td>
                          <td class="text-left"><?php echo $entry_discount; ?></td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($customer_groups as $customer_group) { ?>
                        <tr>
                          <td class="text-left"><?php echo $customer_group['name']; ?></td>
                          <td class="text-left">
                            <div class="input-group">
                              <input type="text" name="mpplan_discount[<?php echo $customer_group['customer_group_id']; ?>][discount]" value="<?php echo isset($mpplan_discount[$customer_group['customer_group_id']]) ? $mpplan_discount[$customer_group['customer_group_id']]['discount'] : ''; ?>" class="form-control" />
                              <span class="input-group-btn">
                              <button type="button" class="btn btn-primary">%</button>
                              </span>
                            </div>
                          </td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-link">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-product"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_product; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="product" value="" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control" />
                  <div id="mpplan-product" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($mpplan_products as $mpplan_product) { ?>
                    <div id="mpplan-product<?php echo $mpplan_product['product_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $mpplan_product['name']; ?>
                      <input type="hidden" name="mpplan_product[]" value="<?php echo $mpplan_product['product_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($stores as $store) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($store['store_id'], $mpplan_store)) { ?>
                        <input type="checkbox" name="mpplan_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                        <?php echo $store['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="mpplan_store[]" value="<?php echo $store['store_id']; ?>" />
                        <?php echo $store['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-info">
              <div class="table-responsive">
                <table id="info" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left" style="width:80%"><?php echo $entry_info; ?></td>
                      <td class="text-left"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $info_row = 0; ?>
                    <?php foreach ($mpplan_infos as $mpplan_info) { ?>
                    <tr id="info-row<?php echo $info_row; ?>">
                      <td class="text-left"><?php foreach ($languages as $language) { ?>
                        <div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                          <input type="text" name="mpplan_info[<?php echo $info_row; ?>][mpplan_info_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($mpplan_info['mpplan_info_description'][$language['language_id']]) ? $mpplan_info['mpplan_info_description'][$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_info; ?>" class="form-control">
                        </div>                        
                        <?php if (isset($error_info[$info_row][$language['language_id']])) { ?>
                         <div class="text-danger"><?php echo $error_info[$info_row][$language['language_id']]; ?></div>
                        <?php } ?>
                        <?php } ?></td>
                      <td class="text-right"><input type="text" name="mpplan_info[<?php echo $info_row; ?>][sort_order]" value="<?php echo $mpplan_info['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#info-row<?php echo $info_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $info_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2"></td>
                      <td class="text-left"><button type="button" onclick="addInfo();" data-toggle="tooltip" title="<?php echo $button_info_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<link href="view/javascript/codemirror/lib/codemirror.css" rel="stylesheet" />
<link href="view/javascript/codemirror/theme/monokai.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/codemirror/lib/codemirror.js"></script> 
<script type="text/javascript" src="view/javascript/codemirror/lib/xml.js"></script> 
<script type="text/javascript" src="view/javascript/codemirror/lib/formatting.js"></script> 

<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/summernote-image-attributes.js"></script>
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<script type="text/javascript"><!--
var info_row = <?php echo $info_row; ?>;

function addInfo() {
    html  = '<tr id="info-row' + info_row + '">';
  html += '  <td class="text-left">';
  <?php foreach ($languages as $language) { ?>
  html += '<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span><input type="text" name="mpplan_info[' + info_row + '][mpplan_info_description][<?php echo $language['language_id']; ?>][name]" value="" placeholder="<?php echo $entry_info; ?>" class="form-control"></div>';
    <?php } ?>
  html += '  </td>';
  html += '  <td class="text-right"><input type="text" name="mpplan_info[' + info_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#info-row' + info_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

  $('#info tbody').append(html);

  info_row++;
}
//--></script>
<script type="text/javascript"><!--
// Products
$('input[name=\'product\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/product/autocomplete&user_token=<?php echo $user_token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
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
    $('input[name=\'product\']').val('');

    $('#mpplan-product' + item['value']).remove();

    $('#mpplan-product').append('<div id="mpplan-product' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="mpplan_product[]" value="' + item['value'] + '" /></div>');
  }
});

$('#mpplan-product').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});
//--></script>
<script type="text/javascript"><!--
$('#language a:first').tab('show');
$(function() { $('.colorpicker').colorpicker(); });
//--></script>
<style type="text/css">
.mp-buttons .btn-primary { background-color : #fff; border-color: #ccc; color: #333;}
.mp-buttons .btn-primary:hover{ background-color: #1E91CF; border-color: #1978ab; color: #fff; }
.mp-buttons .btn-primary.active{ background-color: #3E5776; border-color: #3E5776; color: #fff; }
</style>
</div>
<?php echo $footer; ?>