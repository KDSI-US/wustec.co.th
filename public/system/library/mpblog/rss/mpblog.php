<?php
namespace mpblog\rss;
class MpBlog extends \Mpblog\Rss\Feed {
    public function __construct($rss_type, $registry) {

        parent::__construct($rss_type, $registry);
    }
}