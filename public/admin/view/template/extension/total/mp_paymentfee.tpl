<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="mp-content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-mp-paymentfee" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-mp-paymentfee" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-setting" data-toggle="tab"><i class="fa fa-cog"></i> <?php echo $tab_setting; ?></a></li>
            <li><a href="#tab-feerules" data-toggle="tab"><i class="fa fa-tag"></i> <?php echo $tab_feerules; ?></a></li>
            <li><a href="#tab-support" data-toggle="tab"><i class="fa fa-thumbs-up"></i> <?php echo $tab_support; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-setting">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-md-3 col-sm-6">
                  <div class="mp-buttons">
                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                      <label class="btn-group btn btn-primary <?php echo !empty($total_mp_paymentfee_status) ? 'active' : ''; ?>">
                        <input type="radio" name="total_mp_paymentfee_status" value="1" <?php echo !empty($total_mp_paymentfee_status) ? 'checked="checked"' : ''; ?> /> <?php echo $text_enabled; ?>
                      </label>
                      <label class="btn-group btn btn-primary <?php echo empty($total_mp_paymentfee_status) ? 'active' : ''; ?>">
                        <input type="radio" name="total_mp_paymentfee_status" value="0" <?php echo empty($total_mp_paymentfee_status) ? 'checked="checked"' : ''; ?> /> <?php echo $text_disabled; ?>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="total_mp_paymentfee_sort_order" value="<?php echo $total_mp_paymentfee_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_tax_class; ?></label>
                <div class="col-sm-10">
                  <select name="total_mp_paymentfee_tax" class="form-control">
                    <option value="0"><?php echo $text_none; ?></option>
                    <?php foreach ($tax_classes as $tax_class) { ?>
                    <?php if ($tax_class['tax_class_id'] == $total_mp_paymentfee_tax) { ?>
                    <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-feerules">
              <div class="row">
                <div class="col-sm-3">
                  <ul class="nav nav-pills nav-stacked ostab" id="feerules">
                    <?php $paymentfee_row = 0; ?>
                    <?php foreach($mp_paymentfee_rules as $mp_paymentfee_rule) { ?>
                      <li><a href="#tab-feerule<?php echo $paymentfee_row; ?>" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$('a[href=\'#tab-feerule<?php echo $paymentfee_row; ?>\']').parent().remove(); $('#tab-feerule<?php echo $paymentfee_row; ?>').remove(); $('#feerules a:first').tab('show');"></i> <?php echo (!empty($mp_paymentfee_rule['payment_name']) ? $mp_paymentfee_rule['payment_name'] : $tab_paymentfee_rules . ' '. ($paymentfee_row + 1)); ?></a></li>
                      <?php $paymentfee_row++; ?>
                    <?php } ?>
                    <li><button type="button" class="btn btn-success btn-block" onclick="addPaymentFeeRule();"><i class="fa fa-plus-circle"></i> <?php echo $button_add_feerule; ?></button></li>
                  </ul>
                </div>
                <div class="col-sm-9">
                  <div class="tab-content">
                    <?php $paymentfee_row = 0; ?>
                    <?php foreach($mp_paymentfee_rules as $mp_paymentfee_rule) { ?>
                    <div class="tab-pane" id="tab-feerule<?php echo $paymentfee_row; ?>">
                      <fieldset>
                        <legend><?php echo $heading_payment; ?></legend>
                        <div class="form-group required">
                          <label class="col-sm-12 control-label"><?php echo $entry_title; ?></label>
                          <div class="col-sm-12">
                            <?php foreach ($languages as $language) { ?>
                            <div class="input-group"><span class="input-group-addon"><img src="<?php echo $language['lang_flag']; ?>" title="<?php echo $language['name']; ?>" /></span>
                              <input type="text" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][description][<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($mp_paymentfee_rule['description'][$language['language_id']]['title']) ? $mp_paymentfee_rule['description'][$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" class="form-control" />
                            </div>
                            <?php if (isset($error_title[$paymentfee_row][$language['language_id']])) { ?>
                            <div class="text-danger"><?php echo $error_title[$paymentfee_row][$language['language_id']]; ?></div>
                            <?php } ?>
                            <?php } ?>
                          </div>
                        </div>
                        <div class="form-group required">
                          <label class="col-sm-12 control-label"><?php echo $entry_payment_method; ?></label>
                          <div class="col-sm-12">
                            <select name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][code]" class="form-control">
                              <?php foreach($payment_methods as $payment_method) { ?>
                              <?php if($payment_method['code'] == $mp_paymentfee_rule['code']) { ?>
                              <option value="<?php echo $payment_method['code']; ?>" selected="selected"><?php echo $payment_method['name']; ?></option>
                              <?php } else { ?>
                              <option value="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['name']; ?></option>
                              <?php } ?>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </fieldset>
                      <br><br>
                      <fieldset>
                        <legend><?php echo $heading_group; ?></legend>
                        <div class="table-responsive">
                          <table class="table table-bordered table-striped">
                            <thead>
                              <tr>
                                <td class="text-center"><?php echo $column_customer_group; ?></td>
                                <td class="text-center" style="width: 100px;"><?php echo $column_total; ?></td>
                                <td class="text-center"><?php echo $column_condition; ?></td>
                                <td class="text-center" style="width: 100px;"><?php echo $column_fee; ?></td>
                                <td class="text-center"><?php echo $column_ruletype; ?></td>
                                <td class="text-center"><?php echo $column_type; ?></td>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach($customer_groups as $customer_group) { ?>
                              <tr>
                                <td><?php echo $customer_group['name']; ?></td>
                                <td>
                                  <input type="text"  name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][groups][<?php echo $customer_group['customer_group_id']; ?>][total]" class="form-control" value="<?php echo isset($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['total']) ? $mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['total'] : 0; ?>">
                                </td>
                                <td>
                                  <div class="mp-buttons">
                                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                                      <label class="btn-group btn btn-primary <?php echo !empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['condition']) ? 'active' : ''; ?>">
                                        <input type="radio" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][groups][<?php echo $customer_group['customer_group_id']; ?>][condition]" value="1" <?php echo !empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['condition']) ? 'checked="checked"' : ''; ?> /> <?php echo $condition_sub_total; ?>
                                      </label>
                                      <label class="btn-group btn btn-primary <?php echo empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['condition']) ? 'active' : ''; ?>">
                                        <input type="radio" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][groups][<?php echo $customer_group['customer_group_id']; ?>][condition]" value="0" <?php echo empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['condition']) ? 'checked="checked"' : ''; ?> /> <?php echo $condition_total; ?>
                                      </label>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input type="text"  name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][groups][<?php echo $customer_group['customer_group_id']; ?>][fee]" class="form-control" value="<?php echo isset($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['fee']) ? $mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['fee'] : 0; ?>" />
                                  <?php if(isset($error_fee[$paymentfee_row][$customer_group['customer_group_id']])) { ?>
                                    <div class="alert alert-danger"><b><i class="fa fa-exclamation-circle"></i> <?php echo $error_fee[$paymentfee_row][$customer_group['customer_group_id']]; ?></b></div>
                                    <?php } ?>
                                </td>
                                <td>
                                  <div class="mp-buttons">
                                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                                      <label class="btn-group btn btn-primary <?php echo !empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['ruletype']) ? 'active' : ''; ?>">
                                        <input type="radio" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][groups][<?php echo $customer_group['customer_group_id']; ?>][ruletype]" value="1" <?php echo !empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['ruletype']) ? 'checked="checked"' : ''; ?> /> <?php echo $text_rulefee; ?>
                                      </label>
                                      <label class="btn-group btn btn-primary <?php echo empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['ruletype']) ? 'active' : ''; ?>">
                                        <input type="radio" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][groups][<?php echo $customer_group['customer_group_id']; ?>][ruletype]" value="0" <?php echo empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['ruletype']) ? 'checked="checked"' : ''; ?> /> <?php echo $text_rulediscount; ?>
                                      </label>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <div class="mp-buttons">
                                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                                      <label class="btn-group btn btn-primary <?php echo !empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['type']) ? 'active' : ''; ?>">
                                        <input type="radio" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][groups][<?php echo $customer_group['customer_group_id']; ?>][type]" value="1" <?php echo !empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['type']) ? 'checked="checked"' : ''; ?> /> <?php echo $text_percent; ?>
                                      </label>
                                      <label class="btn-group btn btn-primary <?php echo empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['type']) ? 'active' : ''; ?>">
                                        <input type="radio" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][groups][<?php echo $customer_group['customer_group_id']; ?>][type]" value="0" <?php echo empty($mp_paymentfee_rule['groups'][$customer_group['customer_group_id']]['type']) ? 'checked="checked"' : ''; ?> /> <?php echo $text_fixed; ?>
                                      </label>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-sm-12"><?php echo $entry_status; ?></label>
                          <div class="col-md-3 col-sm-6">
                            <div class="mp-buttons">
                              <div class="btn-group btn-group-justified" data-toggle="buttons">
                                <label class="btn-group btn btn-primary <?php echo !empty($mp_paymentfee_rule['status']) ? 'active' : ''; ?>">
                                    <input type="radio" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][status]" value="1" <?php echo !empty($mp_paymentfee_rule['status']) ? 'checked="checked"' : ''; ?> /> <?php echo $text_enabled; ?>
                                </label>
                                <label class="btn-group btn btn-primary <?php echo empty($mp_paymentfee_rule['status']) ? 'active' : ''; ?>">
                                    <input type="radio" name="total_mp_paymentfee_rule[<?php echo $paymentfee_row; ?>][status]" value="0" <?php echo empty($mp_paymentfee_rule['status']) ? 'checked="checked"' : ''; ?> /> <?php echo $text_disabled; ?>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                    <?php $paymentfee_row++; ?>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-support">
              <div class="bs-callout bs-callout-info">
                <h4>ModulePoints <?php echo $heading_title; ?></h4>
                <center><strong><?php echo $heading_title; ?> </strong></center> <br/>
              </div>
              <fieldset>
                <div class="form-group">
                  <div class="col-md-12 col-xs-12">
                    <h4 class="text-mpsuccess text-center"><i class="fa fa-thumbs-up" aria-hidden="true"></i> Thanks For Choosing Our Extension</h4>
                    <h4 class="text-mpsuccess text-center"><i class="fa fa-phone" aria-hidden="true"></i>Kindly Write Us At Support Email For Support</h4>
                    <ul class="list-group">
                      <li class="list-group-item clearfix">support@modulepoints.com <span class="badge"><a href="mailto:support@modulepoints.com?Subject=Request Support: <?php echo $heading_title; ?> Extension"><i class="fa fa-envelope"></i> Contact Us</a></span></li>
                    </ul>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
  $('#feerules a:first').tab('show');

  var paymentfee_row = <?php echo $paymentfee_row; ?>;

  function addPaymentFeeRule() {
    html = '<div class="tab-pane" id="tab-feerule'+ paymentfee_row +'">';
      html += '<fieldset>';
        html += '<legend><?php echo $heading_payment; ?></legend>';
        html += '<div class="form-group required">';
          html += '<label class="col-sm-12 control-label"><?php echo $entry_title; ?></label>';
          html += '<div class="col-sm-12">';
            <?php foreach ($languages as $language) { ?>
            html += '<div class="input-group"><span class="input-group-addon"><img src="<?php echo $language['lang_flag']; ?>" title="<?php echo $language['name']; ?>" /></span>';
              html += '<input type="text" name="total_mp_paymentfee_rule['+ paymentfee_row +'][description][<?php echo $language['language_id']; ?>][title]" value="" placeholder="<?php echo $entry_title; ?>" class="form-control" />';
            html += '</div>';
            <?php } ?>
          html += '</div>';
        html += '</div>';
        html += '<div class="form-group required">';
          html += '<label class="col-sm-12 control-label"><?php echo $entry_payment_method; ?></label>';
          html += '<div class="col-sm-12">';
            html += '<select name="total_mp_paymentfee_rule['+ paymentfee_row +'][code]" class="form-control">';
              <?php foreach($payment_methods as $payment_method) { ?>
              html += '<option value="<?php echo $payment_method['code']; ?>" selected="selected"><?php echo $payment_method['name']; ?></option>';
              <?php } ?>
            html += '</select>';
          html += '</div>';
        html += '</div>';
      html += '</fieldset>';
      html += '<br><br>';
      html += '<fieldset>';
        html += '<legend><?php echo $heading_group; ?></legend>';
        html += '<div class="table-responsive">';
          html += '<table class="table table-bordered table-striped">';
            html += '<thead>';
              html += '<tr>';
                html += '<td class="text-center"><?php echo $column_customer_group; ?></td>';
                html += '<td class="text-center" style="width: 100px;"><?php echo $column_total; ?></td>';
                html += '<td class="text-center"><?php echo $column_condition; ?></td>';
                html += '<td class="text-center" style="width: 100px;"><?php echo $column_fee; ?></td>';
                html += '<td class="text-center"><?php echo $column_ruletype; ?></td>';
                html += '<td class="text-center"><?php echo $column_type; ?></td>';
              html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
              <?php foreach($customer_groups as $customer_group) { ?>
              html += '<tr>';
                html += '<td><?php echo $customer_group['name']; ?></td>';
                html += '<td>';
                  html += '<input type="text" name="total_mp_paymentfee_rule['+ paymentfee_row +'][groups][<?php echo $customer_group['customer_group_id']; ?>][total]" class="form-control" value="0">';
                html += '</td>';
                html += '<td>';
                  html += '<div class="mp-buttons">';
                    html += '<div class="btn-group btn-group-justified" data-toggle="buttons">';
                      html += '<label class="btn-group btn btn-primary active">';
                        html += '<input type="radio" name="total_mp_paymentfee_rule['+ paymentfee_row +'][groups][<?php echo $customer_group['customer_group_id']; ?>][condition]" value="1" checked="checked" /> <?php echo $condition_sub_total; ?>';
                      html += '</label>';
                      html += '<label class="btn-group btn btn-primary">';
                        html += '<input type="radio" name="total_mp_paymentfee_rule['+ paymentfee_row +'][groups][<?php echo $customer_group['customer_group_id']; ?>][condition]" value="0" /> <?php echo $condition_total; ?>';
                      html += '</label>';
                    html += '</div>';
                  html += '</div>';
                html += '</td>';
                html += '<td>';
                  html += '<input type="text"  name="total_mp_paymentfee_rule['+ paymentfee_row +'][groups][<?php echo $customer_group['customer_group_id']; ?>][fee]" class="form-control" value="0" />';
                html += '</td>';
                html += '<td>';
                  html += '<div class="mp-buttons">';
                    html += '<div class="btn-group btn-group-justified" data-toggle="buttons">';
                      html += '<label class="btn-group btn btn-primary active">';
                        html += '<input type="radio" name="total_mp_paymentfee_rule['+ paymentfee_row +'][groups][<?php echo $customer_group['customer_group_id']; ?>][ruletype]" value="1" checked="checked" /> <?php echo $text_rulefee; ?>';
                      html += '</label>';
                      html += '<label class="btn-group btn btn-primary">';
                        html += '<input type="radio" name="total_mp_paymentfee_rule['+ paymentfee_row +'][groups][<?php echo $customer_group['customer_group_id']; ?>][ruletype]" value="0" /> <?php echo $text_rulediscount; ?>';
                      html += '</label>';
                    html += '</div>';
                  html += '</div>';
                html += '</td>';
                html += '<td>';
                  html += '<div class="mp-buttons">';
                    html += '<div class="btn-group btn-group-justified" data-toggle="buttons">';
                      html += '<label class="btn-group btn btn-primary active">';
                        html += '<input type="radio" name="total_mp_paymentfee_rule['+ paymentfee_row +'][groups][<?php echo $customer_group['customer_group_id']; ?>][type]" value="1" checked="checked" /> <?php echo $text_percent; ?>';
                      html += '</label>';
                      html += '<label class="btn-group btn btn-primary">';
                        html += '<input type="radio" name="total_mp_paymentfee_rule['+ paymentfee_row +'][groups][<?php echo $customer_group['customer_group_id']; ?>][type]" value="0" /> <?php echo $text_fixed; ?>';
                      html += '</label>';
                    html += '</div>';
                  html += '</div>';
                html += '</td>';
              html += '</tr>';
              <?php } ?>
            html += '</tbody>';
          html += '</table>';
        html += '</div>';
        html += '<div class="form-group">';
          html += '<label class="control-label col-sm-12"><?php echo $entry_status; ?></label>';
          html += '<div class="col-md-3 col-sm-6">';
            html += '<div class="mp-buttons">';
              html += '<div class="btn-group btn-group-justified" data-toggle="buttons">';
                html += '<label class="btn-group btn btn-primary active">';
                    html += '<input type="radio" name="total_mp_paymentfee_rule['+ paymentfee_row +'][status]" value="1" checked="checked" /> <?php echo $text_enabled; ?>';
                html += '</label>';
                html += '<label class="btn-group btn btn-primary">';
                    html += '<input type="radio" name="total_mp_paymentfee_rule['+ paymentfee_row +'][status]" value="0" /> <?php echo $text_disabled; ?>';
                html += '</label>';
              html += '</div>';
            html += '</div>';
          html += '</div>';
        html += '</div>';
      html += '</fieldset>';
    html += '</div>';

    $('#tab-feerules .tab-content').append(html);

    $('#feerules > li:last-child').before('<li><a href="#tab-feerule' + paymentfee_row + '" data-toggle="tab"><i class="fa fa-minus-circle" onclick=" $(\'#feerules a:first\').tab(\'show\');$(\'a[href=\\\'#tab-feerule' + paymentfee_row + '\\\']\').parent().remove(); $(\'#tab-feerule' + paymentfee_row + '\').remove();"></i> <?php echo $tab_paymentfee_rules; ?> '+ (paymentfee_row + parseInt(1)) +'</li>');

    $('#feerules a[href=\'#tab-feerule' + paymentfee_row + '\']').tab('show');

    $('[data-toggle=\'tooltip\']').tooltip({
      container: 'body',
      html: true
    });

    paymentfee_row++;
  }
  //--></script>
</div>
<?php echo $footer; ?>