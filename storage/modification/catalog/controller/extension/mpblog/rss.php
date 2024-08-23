<?php
/* This file is under Git Control by KDSI. */
use \MpBlog\ControllerCatalog as Controller;
use \MpBlog\Rss\Item;
use \MpBlog\Rss\Feed;
use \MpBlog\Rss\InvalidOperationException;
use \MpBlog\Rss\LogicException;
use \MpBlog\Rss\InvalidArgumentException;
use \MpBlog\Rss\MpBlog;
class ControllerExtensionMpBlogRss extends Controller {
	use \MpBlog\trait_mpblog_catalog;

	const ATOM = 'Atom';
	const RSS_2 = 'RSS 2.0';
	const RSS_1 = 'RSS 1.0';

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlogCatalog($registry);
	}

	public function index() {
		if($this->config->get('mpblog_status')) {
		
		// include library using require_once
		// require_once DIR_SYSTEM ."library/mpblog/rss/Item.php";
		// require_once DIR_SYSTEM ."library/mpblog/rss/Feed.php";
		// require_once DIR_SYSTEM ."library/mpblog/rss/InvalidOperationException.php";
		// require_once DIR_SYSTEM ."library/mpblog/rss/LogicException.php";
		// require_once DIR_SYSTEM ."library/mpblog/rss/InvalidArgumentException.php";
		// require_once DIR_SYSTEM ."library/mpblog/rss/mpblog.php";

		$TestFeed = new \MpBlog\Rss\MpBlog(\MpBlog\Rss\Feed::ATOM, $this->registry);

		$this->load->model("tool/image");
		$this->load->model("extension/mpblog/mpblogpost");

		$rssfeed_title = $this->config->get('mpblog_rssfeed_title');
		$rssfeed_description = $this->config->get('mpblog_rssfeed_description');
		$rssfeed_web_master = $this->config->get('mpblog_rssfeed_web_master');
		$rssfeed_copy_write = $this->config->get('mpblog_rssfeed_copy_write');
		$rssfeed_format = $this->config->get('mpblog_rssfeed_format');
		$rssfeed_limit = $this->config->get('mpblog_rssfeed_limit');


		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}


		//Setting the channel elements
		//Use wrapper functions for common elements
		$TestFeed->setTitle($rssfeed_title);
		$TestFeed->setDescription($rssfeed_description);
		$TestFeed->setLink($server);
		$TestFeed->setDate(new DateTime());

		if($this->config->get('config_logo')) {
			$TestFeed->setImage($this->model_tool_image->resize($this->config->get('config_logo'), 100, 100));
		}
		
		//For other channel elements, use setChannelElement() function

		// if not rss 1 include below 3
		if($rssfeed_format == self :: ATOM || $rssfeed_format == self :: RSS_2) {
		$TestFeed->setChannelElement('author', ['name' => $rssfeed_web_master . "<br/>" . $rssfeed_copy_write]);

		//You can add additional link elements, e.g. to a PubSubHubbub server with custom relations.
		$TestFeed->setSelfLink($server);
		$TestFeed->setAtomLink($server, 'hub');

		}

		if(isset($this->request->get['limit']) && $this->request->get['limit'] > 0) {
			$rssfeed_limit = $this->request->get['limit'];
		}
		
		$page = 1;

		$mpblogposts = $this->model_extension_mpblog_mpblogpost->getMpBlogPosts([
			'start' => ($page -1) * $rssfeed_limit,
			'limit' => $rssfeed_limit
		]);

		foreach ($mpblogposts as $key => $value) {			
			//Adding a feed. Generally this portion will be in a loop and add all feeds.
			//Create an empty Item
			$newItem = $TestFeed->createNewItem();
			//Add elements to the feed item
			//Use wrapper functions to add common feed elements
			$newItem->setTitle($value['name']);
			$newItem->setLink($this->url->link('extension/mpblog/blog','mpblogpost_id=' . $value['mpblogpost_id'], true));
			$newItem->setDate($value['date_available']);

			if($rssfeed_format == self :: ATOM || $rssfeed_format == self :: RSS_2) {
			// if not rss 1 include below 1
			$newItem->setAuthor($value['author']);
			}


			if($rssfeed_format == self :: RSS_2) {
			// if rss2 only
			// You can set a globally unique identifier. This can be a URL or any other string.
			// If you set permaLink to true, the identifier must be an URL. The default of the
			// permaLink parameter is false.
			$newItem->setId($this->url->link('extension/mpblog/blog','mpblogpost_id=' . $value['mpblogpost_id'], true), true);
			}

			$image = null;
			if(!empty($value['image']) && file_exists(DIR_IMAGE . $value['image'])) {

			$image = $this->model_tool_image->resize($value['image'], $this->config->get('mpblog_image_post_thumb_width'), $this->config->get('mpblog_image_post_thumb_height'));
			$image_size = filesize(DIR_IMAGE . str_replace($server.'image/', '', $image));

			$image_mime = image_type_to_mime_type(exif_imagetype($image));

			// $sq =	getimagesize($image); 
			$newItem->addEnclosure($image, $image_size, $image_mime);
			}

			//Internally changed to "summary" tag for ATOM feed
			if($image) {
				$description = '<img src="'. $image .'" alt="'. $value['name'] .'" />';
				$description .= $value['description'];
			} else {
				$description = $value['description'];
			}
			$newItem->setDescription(html_entity_decode($description,  ENT_QUOTES , 'UTF-8'));

			if($rssfeed_format == self :: ATOM) {
			// if atom only. We setcontent same as title			
			$newItem->setContent($value['meta_title']);
			//Now add the feed item
			}

			$TestFeed->addItem($newItem);


		}

		//OK. Everything is done. Now generate the feed.
		$TestFeed->printFeed();

	}
	}
}