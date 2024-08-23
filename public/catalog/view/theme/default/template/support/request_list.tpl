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
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <div class="support-list-wrap">
        <div class="ticket-record">
          <div class="row">
            <div class="submitticket col-sm-12 text-right">
              <a href="<?php echo $link_submission; ?>" class="btn btn-success"><?php echo $button_submit; ?></a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-head">
                <tr>
                  <th>
                    <a><div class="filter_click"><?php echo $column_date; ?></div></a>
                  </th>
                  <th>
                    <a><div class="filter_click"><?php echo $column_department; ?></div></a>
                  </th>
                  <th>
                    <a><div class="filter_click"><?php echo $column_ticketid; ?></div></a>
                  </th>
                  <th>
                    <a><div class="filter_click"><?php echo $column_subject; ?></div></a>
                  </th>
                  <th>
                    <a><div class="filter_click"><?php echo $column_status; ?></div></a>
                  </th>
                  <th class="filtered" >
                    <a><div class="filter_click"><?php echo $column_date_modified; ?></div></a>
                  </th>
                  <th>
                    <a><div class="filter_click text-center"><?php echo $column_action; ?></div></a>
                  </th>
                </tr>
            </thead>
            <tbody class="table-body">
              <?php if($ticketrequests) { ?>
              <?php foreach($ticketrequests as $ticketrequest) { ?>
              <tr>
                  <td><?php echo $ticketrequest['date_added']; ?></td>
                  <td><?php echo $ticketrequest['department']; ?></td>
                  <td>#<?php echo $ticketrequest['ticketrequest_id']; ?></td>
                  <td><?php echo $ticketrequest['subject']; ?></td>
                  <td class="text-center"><span class="label label-active" style="color: <?php echo $ticketrequest['textcolor']; ?>; background: <?php echo $ticketrequest['bgcolor']; ?>"><?php echo $ticketrequest['status']; ?></span></td>
                  <td><?php echo $ticketrequest['date_modified']; ?></td>
                  <td class="text-center"><a href="<?php echo $ticketrequest['view']; ?>" class="button btn-gray"><?php echo $button_view; ?></a></td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                  <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>
