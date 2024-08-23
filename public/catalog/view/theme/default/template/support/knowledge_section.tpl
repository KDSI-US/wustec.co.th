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
     <div class="knwledge-wrap">
        <div class="knwldge-head">
          <h2><?php if($icon_class) { ?><i class="fa <?php echo $icon_class; ?>"></i><?php } ?> <?php echo $heading_title; ?></h2>
          <p><?php echo $description; ?></p>
        </div>
        <?php if($ticketknowledge_articles) { ?>
        <ul class="knwldge-list">
          <?php foreach($ticketknowledge_articles as $ticketknowledge_article) { ?>
          <li>
            <a href="<?php echo $ticketknowledge_article['href']; ?>"><?php echo $ticketknowledge_article['title']; ?></a>
          </li>
          <?php } ?>
        </ul>
        <?php } ?>
      </div>
      <?php echo $content_bottom; ?>
    </div>
  <?php echo $column_right; ?>
  </div>
</div>
<?php echo $footer; ?>