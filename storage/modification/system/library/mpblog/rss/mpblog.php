<?php
/* This file is under Git Control by KDSI. */
namespace mpblog\rss;
class MpBlog extends \Mpblog\Rss\Feed {
    public function __construct($rss_type, $registry) {

        parent::__construct($rss_type, $registry);
    }
}