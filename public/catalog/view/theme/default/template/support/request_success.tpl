<?php echo $header; ?>
<section class="supprt-banner" style="background-image: url('<?php echo $support_banner; ?>')">
  <div class="container">
    <h1><?php echo $banner_title; ?></h1>
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
      <div class="sp-content">
        <div class="icon"><i class="fa fa-check"></i></div>
        <h1>Thank You!</h1>
        <h4><?php echo $heading_title; ?></h4>
        <?php echo $text_message; ?>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>