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
      <div class="article-wrap">
        <h2><?php echo $heading_title; ?></h2>
        <div class="article-content"><?php echo $description; ?></div>
      </div>
      <hr>

      <?php /*
      <div class="article-help">Was this article helpful? 
        <a class="vote-btn" rel="like"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a> 
        <a class="vote-btn" rel="dislike"><i class="fa fa-thumbs-down" aria-hidden="true"></i></a> 
        1 out of 1 found this helpful
      </div>
      */ ?>
      
      <?php if($relateds) { ?>
      <div class="related-article">
        <h4><?php echo $text_related; ?></h4>
        <ul>
          <?php foreach($relateds as $related) ?>
          <li><a href="<?php echo $related['href']; ?>"><?php echo $related['title']; ?></a></li>
        </ul>
      </div>
      <?php } ?>
     </div>
      <?php echo $content_bottom; ?>
    </div>
    <?php echo $column_right; ?>
  </div>
</div>
<?php echo $footer; ?>