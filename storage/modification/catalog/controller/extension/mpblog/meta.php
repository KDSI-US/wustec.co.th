<?php
/* This file is under Git Control by KDSI. */
use \MpBlog\ControllerCatalog as Controller;
class ControllerExtensionMpBlogMeta extends Controller {
	use \MpBlog\trait_mpblog_catalog;

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlogCatalog($registry);
	}

	public function index () {
		return $this->mpblogmeta->getMeta();
	}
}
