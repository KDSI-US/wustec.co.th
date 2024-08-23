<?php echo $header; ?>
<link href="view/javascript/modulepoints/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<link href="view/javascript/modulepoints/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css" rel="stylesheet" />
<script src="view/javascript/modulepoints/fontawesome-iconpicker/js/fontawesome-iconpicker.js"></script>
<?php echo $column_left; ?>
<div id="content" class="mp-content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-ticketdepartment" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ticketdepartment" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php if(VERSION >= '2.2.0.0') { ?>
                <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> 
                <?php } else{ ?>
                <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                <?php } ?> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-title<?php echo $language['language_id']; ?>"><?php echo $entry_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="ticketdepartment_description[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($ticketdepartment_description[$language['language_id']]) ? $ticketdepartment_description[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-sub-title<?php echo $language['language_id']; ?>"><?php echo $entry_sub_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="ticketdepartment_description[<?php echo $language['language_id']; ?>][sub_title]" value="<?php echo isset($ticketdepartment_description[$language['language_id']]) ? $ticketdepartment_description[$language['language_id']]['sub_title'] : ''; ?>" placeholder="<?php echo $entry_sub_title; ?>" id="input-sub-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_sub_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_sub_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane" id="tab-data">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-3">
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
                <div class="col-sm-3">
                  <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-icon-class"><?php echo $entry_icon_class; ?></label>
                <div class="col-sm-3">
                  <input type="text" name="icon_class" value="<?php echo $icon_class; ?>" placeholder="<?php echo $entry_icon_class; ?>" id="input-icon-class"  data-placement="bottomLeft" class="form-control iconpicker iconpicker-auto" />
                  <?php if($error_icon_class) { ?>
                  <div class="text-danger"><?php echo $error_icon_class; ?></div>
                  <?php } ?>
                </div>
                <div class="col-sm-3">
                  <i class="myfa fa fa-3x <?php echo $icon_class; ?>"></i>
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

$('.iconpicker-auto').iconpicker();

$('.iconpicker-auto').on('iconpickerSelected', function(el) {
   var classes = $('.myfa').attr('class').split(' ');
    classes.splice(3, 1);
   $('.myfa').attr('class', classes.join(' ') + ' ' + el.iconpickerValue);
});
//--></script></div>
<?php echo $footer; ?>