<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="mp-content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-ticketrequest').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-ticketrequest"><?php echo $entry_ticketrequest_id; ?></label>
                <input type="text" name="filter_ticketrequest_id" value="<?php echo $filter_ticketrequest_id; ?>" placeholder="<?php echo $entry_ticketrequest_id; ?>" id="input-ticketrequest" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">            	
              <div class="form-group">
                <label class="control-label" for="input-subject"><?php echo $entry_subject; ?></label>
                <input type="text" name="filter_subject" value="<?php echo $filter_subject; ?>" placeholder="<?php echo $entry_subject; ?>" id="input-subject" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">            	
              <div class="form-group">
                <label class="control-label" for="input-ticketdepartment"><?php echo $entry_ticketdepartment; ?></label>
                <select name="filter_ticketdepartment" id="input-ticketdepartment" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($ticketdepartments as $ticketdepartment) { ?>
                  <?php if ($ticketdepartment['ticketdepartment_id'] == $filter_ticketdepartment) { ?>
                  <option value="<?php echo $ticketdepartment['ticketdepartment_id']; ?>" selected="selected"><?php echo $ticketdepartment['title']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $ticketdepartment['ticketdepartment_id']; ?>"><?php echo $ticketdepartment['title']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>              
              <div class="form-group">
                <label class="control-label" for="input-ticketstatus"><?php echo $entry_status; ?></label>
                <select name="filter_ticketstatus" id="input-ticketstatus" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($ticketstatuses as $ticketstatus) { ?>
                  <?php if ($ticketstatus['ticketstatus_id'] == $filter_ticketstatus) { ?>
                  <option value="<?php echo $ticketstatus['ticketstatus_id']; ?>" selected="selected"><?php echo $ticketstatus['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $ticketstatus['ticketstatus_id']; ?>"><?php echo $ticketstatus['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">            	
              <div class="form-group">
                <label class="control-label" for="input-date-modified"><?php echo $entry_date_modified; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="<?php echo $entry_date_modified; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
              	</div>
              </div>
    			<button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-ticketrequest">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 't.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                    <td class="text-left"><?php if ($sort == 't.subject') { ?>
                    <a href="<?php echo $sort_subject; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_subject; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_subject; ?>"><?php echo $column_subject; ?></a>
                    <?php } ?></td>
                    <td class="text-center"><?php if ($sort == 't.ticketrequest_id') { ?>
                    <a href="<?php echo $sort_ticketrequest_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ticketrequest_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ticketrequest_id; ?>"><?php echo $column_ticketrequest_id; ?></a>
                    <?php } ?></td>
                    <td class="text-left"><?php if ($sort == 'ticketdepartment') { ?>
                    <a href="<?php echo $sort_ticketdepartment; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ticketdepartment; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ticketdepartment; ?>"><?php echo $column_ticketdepartment; ?></a>
                    <?php } ?></td>
                    <td class="text-left"><?php if ($sort == 't.email') { ?>
                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
                    <?php } ?></td>
                    <td class="text-center"><?php if ($sort == 'ticketstatus') { ?>
                    <a href="<?php echo $sort_ticketstatus; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ticketstatus; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ticketstatus; ?>"><?php echo $column_ticketstatus; ?></a>
                    <?php } ?></td>
                  <td class="text-right" style="background: #eee; font-weight: bold;"><?php if ($sort == 't.date_modified') { ?>
                    <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($ticketrequests) { ?>
                <?php foreach ($ticketrequests as $ticketrequest) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($ticketrequest['ticketrequest_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ticketrequest['ticketrequest_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ticketrequest['ticketrequest_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $ticketrequest['date_added']; ?></td>
                  <td class="text-left"><?php echo $ticketrequest['subject']; ?></td>
                  <td class="text-center">#<?php echo $ticketrequest['ticketrequest_id']; ?></td>
                  <td class="text-left"><?php echo $ticketrequest['ticketdepartment']; ?></td>
                  <td class="text-left"><?php echo $ticketrequest['email']; ?><br/></td>
                  <td class="text-center"><span style="padding: 5px 10px; border-radius: 3px; color: <?php echo $ticketrequest['textcolor']; ?>; background: <?php echo $ticketrequest['bgcolor']; ?>"><?php echo $ticketrequest['ticketstatus']; ?></span></td>
                  <td class="text-right"><?php echo $ticketrequest['date_modified']; ?></td>
                  <td class="text-right"><a href="<?php echo $ticketrequest['view']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-eye"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script>
<script type="text/javascript"><!--
$('#button-filter').on('click', function() {
  <?php if(VERSION <= '2.3.0.2') { ?>
	url = 'index.php?route=module_ticket/ticketrequest&token=<?php echo $session_token; ?>';
  <?php } else { ?>
  url = 'index.php?route=module_ticket/ticketrequest&user_token=<?php echo $session_token; ?>';
  <?php } ?>
	
	var filter_date_added = $('input[name=\'filter_date_added\']').val();	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

  var filter_subject = $('input[name=\'filter_subject\']').val(); 
  if (filter_subject) {
    url += '&filter_subject=' + encodeURIComponent(filter_subject);
  }
  
	var filter_ticketrequest_id = $('input[name=\'filter_ticketrequest_id\']').val();	
	if (filter_ticketrequest_id) {
		url += '&filter_ticketrequest_id=' + encodeURIComponent(filter_ticketrequest_id);
	}

	var filter_ticketdepartment = $('select[name=\'filter_ticketdepartment\']').val();	
	if (filter_ticketdepartment != '*') {
		url += '&filter_ticketdepartment=' + encodeURIComponent(filter_ticketdepartment);
	}

	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}

	var filter_email = $('input[name=\'filter_email\']').val();	
	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}

	var filter_ticketstatus = $('select[name=\'filter_ticketstatus\']').val();	
	if (filter_ticketstatus != '*') {
		url += '&filter_ticketstatus=' + encodeURIComponent(filter_ticketstatus);
	}
	
	location = url;
});
//--></script> 
</div>
<?php echo $footer; ?>