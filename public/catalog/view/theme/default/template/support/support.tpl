<?php echo $header; ?>
<section class="supprt-banner" style="background-image: url('<?php echo $support_banner; ?>')">
  <div class="container">
    <h1><?php echo $heading_title; ?></h1>
  </div>
</section>
<div class="container">
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <div class="support-wrap">
        <div class="support-type">
          <ul class="list-inline clearfix nav nav-tabs">
            <?php if($ticketsubmission_status) { ?>
            <li><a href="#ticket-sub" data-toggle="tab"><i class="fa fa-ticket" aria-hidden="true"></i> <span><?php echo $tab_ticket_submission; ?></span></a></li>
            <?php } ?>

            <?php if($videos_status && $ticketvideos) { ?>
            <li><a href="#video-guide" data-toggle="tab"><i class="fa fa-play" aria-hidden="true"></i> <span><?php echo $tab_video_guide; ?></span></a></li>
            <?php } ?>
            
            <?php if($knowledgebase_status && $ticketknowledge_sections) { ?>
            <li><a href="#knwlgdebase-guide" data-toggle="tab"><i class="fa fa-book" aria-hidden="true"></i> <span><?php echo $tab_knowledge_base; ?></span></a></li>
            <?php } ?>
          </ul>
        </div>
        <div class="tab-content">
          <?php if($ticketsubmission_status) { ?>
          <div id="ticket-sub" class="support-dept tab-pane ticket-sub clearfix">
            <h3><?php echo $text_departments; ?></h3> 
            <ul class="list-unstyled">
              <?php foreach ($ticketdepartments as $ticketdepartment) { ?>
              <li>
                <a href="<?php echo $ticketdepartment['href']; ?>">
                  <i class="fa <?php echo $ticketdepartment['icon_class']; ?>"></i>                  
                  <span><?php echo $ticketdepartment['title']; ?> <small><?php echo $ticketdepartment['sub_title']; ?></small></span>
                </a>
              </li>
              <?php } ?>
            </ul>
          </div>
          <?php } ?>

          <?php if($videos_status && $ticketvideos) { ?>
          <div id="video-guide" class="support-dept tab-pane video-guide clearfix">
            <?php foreach ($ticketvideos as $ticketvideo) { ?>
            <article>
            	<h1><?php echo $ticketvideo['title']; ?></h1>
            	<p><?php echo $ticketvideo['sub_title']; ?></p>
            	<a href="<?php echo $ticketvideo['url']; ?>" class="play-btn ticket-video"><i class="fa fa-play" aria-hidden="true"></i></a>
            </article>
            <?php } ?>  
          </div>
          <?php } ?>

          <?php if($knowledgebase_status && $ticketknowledge_sections) { ?>
          <div id="knwlgdebase-guide" class="support-dept tab-pane knwlgdebase-guide clearfix">
            <?php foreach($ticketknowledge_sections as $ticketknowledge_section) { ?>
            <a href="<?php echo $ticketknowledge_section['href']; ?>">
  	        <article>
  	        	<i class="fa <?php echo $ticketknowledge_section['icon_class']; ?>" aria-hidden="true"></i>
  	          	<h1><?php echo $ticketknowledge_section['title']; ?></h1>
  	          	<p><?php echo $ticketknowledge_section['sub_title']; ?></p>
  	        </article>
        	  </a>
            <?php } ?>
          </div>
          <?php } ?>
        </div>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>


<script type="text/javascript">
<?php if($videos_status) { ?>
$('.ticket-video').magnificPopup({
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,
    fixedContentPos: false
});
<?php } ?>

$(document).ready(function () {
  $('.support-type .nav-tabs li:first a').tab('show');
});
</script>
</div>
<?php echo $footer; ?>
