<?php echo $header; ?>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<link href="view/javascript/modulepoints/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css" rel="stylesheet">
<script src="view/javascript/modulepoints/fontawesome-iconpicker/js/fontawesome-iconpicker.js"></script>
<?php echo $column_left; ?>
<div id="content" class="mp-content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-ticketknowledge-section" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ticketknowledge-section" class="form-horizontal">
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
                      <input type="text" name="ticketknowledge_article_description[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($ticketknowledge_article_description[$language['language_id']]) ? $ticketknowledge_article_description[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-banner-title<?php echo $language['language_id']; ?>"><?php echo $entry_banner_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="ticketknowledge_article_description[<?php echo $language['language_id']; ?>][banner_title]" value="<?php echo isset($ticketknowledge_article_description[$language['language_id']]) ? $ticketknowledge_article_description[$language['language_id']]['banner_title'] : ''; ?>" placeholder="<?php echo $entry_banner_title; ?>" id="input-banner-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_banner_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_banner_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="ticketknowledge_article_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>" class="form-control summernote" data-toggle="summernote" data-lang="<?php echo $summernote; ?>"><?php echo isset($ticketknowledge_article_description[$language['language_id']]) ? $ticketknowledge_article_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="ticketknowledge_article_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($ticketknowledge_article_description[$language['language_id']]) ? $ticketknowledge_article_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="ticketknowledge_article_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($ticketknowledge_article_description[$language['language_id']]) ? $ticketknowledge_article_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                    <div class="col-sm-10">
                      <textarea name="ticketknowledge_article_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($ticketknowledge_article_description[$language['language_id']]) ? $ticketknowledge_article_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane" id="tab-data">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-ticketknowledge-section"><span data-toggle="tooltip" title="<?php echo $help_ticketknowledge_section; ?>"><?php echo $entry_ticketknowledge_section; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="ticketknowledge_section" value="<?php echo $ticketknowledge_section; ?>" placeholder="<?php echo $entry_ticketknowledge_section; ?>" id="input-ticketknowledge-section" class="form-control" />
                  <input type="hidden" name="ticketknowledge_section_id" value="<?php echo $ticketknowledge_section_id; ?>" />
                  <?php if ($error_ticketknowledge_section) { ?>
                  <div class="text-danger"><?php echo $error_ticketknowledge_section; ?></div>
                  <?php } ?>
                </div>
              </div>
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
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-related"><span data-toggle="tooltip" title="<?php echo $help_related; ?>"><?php echo $entry_related; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="related" value="" placeholder="<?php echo $entry_related; ?>" id="input-related" class="form-control" />
                  <div id="ticketknowledge-article-related" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($ticketknowledge_article_relateds as $ticketknowledge_article_related) { ?>
                    <div id="ticketknowledge-article-related<?php echo $ticketknowledge_article_related['ticketknowledge_article_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $ticketknowledge_article_related['title']; ?>
                      <input type="hidden" name="ticketknowledge_article_related[]" value="<?php echo $ticketknowledge_article_related['ticketknowledge_article_id']; ?>" />
                    </div>
                    <?php } ?>
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
$('input[name=\'ticketknowledge_section\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      <?php if(VERSION <= '2.3.0.2') { ?>
      url: 'index.php?route=module_ticket/ticketknowledge_section/autocomplete&token=<?php echo $session_token; ?>&filter_title=' +  encodeURIComponent(request),
      <?php } else { ?>
        url: 'index.php?route=module_ticket/ticketknowledge_section/autocomplete&user_token=<?php echo $session_token; ?>&filter_title=' +  encodeURIComponent(request),
      <?php } ?>
      dataType: 'json',
      success: function(json) {
        json.unshift({
          ticketknowledge_section_id: 0,
          title: '<?php echo $text_none; ?>'
        });

        response($.map(json, function(item) {
          return {  
            label: item['title'],
            value: item['ticketknowledge_section_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'ticketknowledge_section\']').val(item['label']);
    $('input[name=\'ticketknowledge_section_id\']').val(item['value']);
  }
});

// Related
$('input[name=\'related\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      <?php if(VERSION <= '2.3.0.2') { ?>
      url: 'index.php?route=module_ticket/ticketknowledge_article/autocomplete&token=<?php echo $session_token; ?>&filter_title=' +  encodeURIComponent(request),
      <?php } else { ?>
      url: 'index.php?route=module_ticket/ticketknowledge_article/autocomplete&user_token=<?php echo $session_token; ?>&filter_title=' +  encodeURIComponent(request),
      <?php } ?>
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['title'],
            value: item['ticketknowledge_article_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'related\']').val('');

    $('#ticketknowledge-article-related' + item['value']).remove();

    $('#ticketknowledge-article-related').append('<div id="ticketknowledge-article-related' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="ticketknowledge_article_related[]" value="' + item['value'] + '" /></div>');
  }
});

$('#ticketknowledge-article-related').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

$('#language a:first').tab('show');

$('.iconpicker-auto').iconpicker();
//--></script>
<?php if(VERSION <= '2.2.0.0') { ?>
<?php foreach ($languages as $language) { ?>
<script type="text/javascript"><!--
$('#input-description<?php echo $language['language_id']; ?>').summernote({ height: 300 });
//--></script>
<?php } ?>
<?php } else if (VERSION <= '2.3.0.2') { ?>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php } else { ?>
<link href="view/javascript/codemirror/lib/codemirror.css" rel="stylesheet" />
<link href="view/javascript/codemirror/theme/monokai.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/codemirror/lib/codemirror.js"></script> 
<script type="text/javascript" src="view/javascript/codemirror/lib/xml.js"></script> 
<script type="text/javascript" src="view/javascript/codemirror/lib/formatting.js"></script> 
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/summernote-image-attributes.js"></script> 
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script> 
<?php } ?>
</div>
<?php echo $footer; ?>