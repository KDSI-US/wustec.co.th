<?php
use \MpBlog\ControllerCatalog as Controller;
class ControllerExtensionMpBlogBlog extends Controller {
	use \MpBlog\trait_mpblog_catalog;

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlogCatalog($registry);
	}

	public function index() {
		if ($this->config->get('mpblog_status')) {
		$this->load->language('mpblog/blog');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpblog/mpblog.css');

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		];


		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_blog'),
			'href' => $this->url->link('extension/mpblog/blogs', '', true)
		];


		$this->load->model('extension/mpblog/mpblogcategory');

		if ($this->config->get('theme_default_directory')) {
			$data['theme_name'] = $this->config->get('theme_default_directory');
		} elseif ($this->config->get('config_template')) {
			$data['theme_name'] = $this->config->get('config_template');
		} else {
			$data['theme_name'] = 'default';
		}


		if (isset($this->request->get['mpcpath'])) {
			$mpcpath = '';

			$parts = explode('_', (string)$this->request->get['mpcpath']);

			$mpblogcategory_id = (int)array_pop($parts);

			foreach ($parts as $mpcpath_id) {
				if (!$mpcpath) {
					$mpcpath = $mpcpath_id;
				} else {
					$mpcpath .= '_' . $mpcpath_id;
				}

				$mpblogcategory_info = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategory($mpcpath_id);

				if ($mpblogcategory_info) {
					$data['breadcrumbs'][] = [
						'text' => $mpblogcategory_info['name'],
						'href' => $this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $mpcpath)
					];
				}
			}

			// Set the last mpblogcategory breadcrumb
			$mpblogcategory_info = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategory($mpblogcategory_id);

			if ($mpblogcategory_info) {
				$url = '';

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				$data['breadcrumbs'][] = [
					'text' => $mpblogcategory_info['name'],
					'href' => $this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $this->request->get['mpcpath'] . $url)
				];
			}
		}


		if (isset($this->request->get['mpblogpost_id'])) {
			$mpblogpost_id = (int)$this->request->get['mpblogpost_id'];
		} else {
			$mpblogpost_id = 0;
		}

		$this->load->model('extension/mpblog/mpblogpost');

		$mpblogpost_info = $this->model_extension_mpblog_mpblogpost->getMpBlogPost($mpblogpost_id);


		if ($mpblogpost_info) {
			$url = '';

			if (isset($this->request->get['mpcpath'])) {
				$url .= '&mpcpath=' . $this->request->get['mpcpath'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['author'])) {
				$url .= '&author=' . $this->request->get['author'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['date'])) {
				$url .= '&date=' . $this->request->get['date'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}


			$data['breadcrumbs'][] = [
				'text' => $mpblogpost_info['name'],
				'href' => $this->url->link('extension/mpblog/blog', $url . '&mpblogpost_id=' . $this->request->get['mpblogpost_id'])
			];

			$this->document->setTitle($mpblogpost_info['meta_title']);
			$this->document->setDescription($mpblogpost_info['meta_description']);
			$this->document->setKeywords($mpblogpost_info['meta_keyword']);
			$this->document->addLink($this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . $this->request->get['mpblogpost_id']), 'canonical');

			if ($this->config->get('mpblog_blog_image_popup')) {
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			}

			$this->document->addScript('catalog/view/javascript/mpblog/mpblog.js');

			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpblog/mpblog.css');


			$data['heading_title'] = $mpblogpost_info['name'];

			$data['heading_give_rating'] = $this->language->get('heading_give_rating');

			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_rating'] = $this->language->get('entry_rating');
			$data['entry_comment'] = $this->language->get('entry_comment');
			$data['text_give_rating'] = $this->language->get('text_give_rating');
			$data['text_not_good'] = $this->language->get('text_not_good');
			$data['text_good'] = $this->language->get('text_good');
			$data['text_comments'] = $this->language->get('text_comments');
			$data['text_write_comment'] = $this->language->get('text_write_comment');
			$data['text_comment'] = $this->language->get('text_comment');
			$data['text_readmore'] = $this->language->get('text_readmore');
			$data['text_loading'] = $this->language->get('text_loading');
			$data['text_related'] = $this->language->get('text_related');
			$data['text_related_products'] = $this->language->get('text_related_products');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['text_related_categories'] = $this->language->get('text_related_categories');
			$data['text_note'] = $this->language->get('text_note');
			$data['text_follow'] = $this->language->get('text_follow');
			$data['text_share'] = $this->language->get('text_share');
			$data['text_tags'] = $this->language->get('text_tags');

			$data['text_login_rating'] = sprintf($this->language->get('text_login_rating'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
			$data['text_login_comment'] = sprintf($this->language->get('text_login_comment'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));


			$data['button_give_rating'] = $this->language->get('button_give_rating');
			$data['button_add_comment'] = $this->language->get('button_add_comment');
			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['button_wishlist'] = $this->language->get('button_wishlist');

			$data['mpblogpost_id'] = (int)$this->request->get['mpblogpost_id'];

			$data['description'] = '';
			if ($this->config->get('mpblog_blog_description')) {
				$data['description'] = html_entity_decode($mpblogpost_info['description'], ENT_QUOTES, 'UTF-8');
			}

			$data['author'] = $mpblogpost_info['author'];
			$data['authorurl'] = $this->url->link('extension/mpblog/blogs', 'author=' . trim($mpblogpost_info['author']));

			$data['comments'] = $mpblogpost_info['comments'];
			$data['wishlists'] = $mpblogpost_info['likes'];

			$data['isLikeByMe']	= $this->model_extension_mpblog_mpblogpost->isLikeByMe($mpblogpost_info['mpblogpost_id']);

			$data['date_available'] = date( ($this->config->get('mpblog_blog_date_format') ? $this->config->get('mpblog_blog_date_format') : $this->language->get('date_format_short')) ,strtotime($mpblogpost_info['date_available']));
			$data['date_availableurl'] = $this->url->link('extension/mpblog/blogs', 'date=' . trim($mpblogpost_info['date_available']));


			$this->load->model('tool/image');

			if ($mpblogpost_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($mpblogpost_info['image'], $this->config->get('mpblog_image_post_width'), $this->config->get('mpblog_image_post_height'));
			} else {
				$data['thumb'] = '';
			}

			if ($mpblogpost_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($mpblogpost_info['image'], $this->config->get('mpblog_image_post_popup_width'), $this->config->get('mpblog_image_post_popup_height'));
			} else {
				$data['popup'] = '';
			}

			$data['width'] = $this->config->get('mpblog_image_post_width');
			$data['height'] = $this->config->get('mpblog_image_post_height');

			$data['iframeVideo'] = $this->model_extension_mpblog_mpblogpost->mpVideoEmbedURL($mpblogpost_info['video']);

			$data['showImage'] = true;

			if ($mpblogpost_info['posttype'] == 'VIDEO' && !empty($data['iframeVideo'])) {
				$data['showImage'] = false;
			}

			/*og meta tags starts*/
			$this->mpblogmeta->setMeta([
				'attribute' => 'property',
				'attribute_value' => 'og:title',
				'content' => $mpblogpost_info['name'],
			]);
			$this->mpblogmeta->setMeta([
				'attribute' => 'property',
				'attribute_value' => 'og:description',
				'content' =>  htmlspecialchars($mpblogpost_info['meta_description'], ENT_QUOTES, 'UTF-8') ,
			]);
			$this->mpblogmeta->setMeta([
				'attribute' => 'property',
				'attribute_value' => 'og:url',
				'content' => $this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . $this->request->get['mpblogpost_id']),
			]);

			$popup = $this->model_tool_image->resize('no_image.png', $this->config->get('mpblog_image_post_popup_width'), $this->config->get('mpblog_image_post_popup_height'));;
			if ($data['showImage']) {

				if ($mpblogpost_info['image']) {
					$popup = $this->model_tool_image->resize($mpblogpost_info['image'], $this->config->get('mpblog_image_post_popup_width'), $this->config->get('mpblog_image_post_popup_height'));
				}
			} else {
				$youtubethumb = $this->model_extension_mpblog_mpblogpost->mpYouTubeThumb($mpblogpost_info['video']);
				if ($youtubethumb['youTubeThumb']) {
					$popup = $youtubethumb['defaultThumb'];
				}

				$this->mpblogmeta->setMeta([
					'attribute' => 'property',
					'attribute_value' => 'og:video',
					'content' => $mpblogpost_info['video'],
				]);
				$this->mpblogmeta->setMeta([
					'attribute' => 'property',
					'attribute_value' => 'og:video:width',
					'content' => $this->config->get('mpblog_image_post_width'),
				]);
				$this->mpblogmeta->setMeta([
					'attribute' => 'property',
					'attribute_value' => 'og:video:height',
					'content' => $this->config->get('mpblog_image_post_height'),
				]);
				$this->mpblogmeta->setMeta([
					'attribute' => 'property',
					'attribute_value' => 'og:video:type',
					'content' => 'application/x-shockwave-flash',
				]);
			}

			$this->mpblogmeta->setMeta([
				'attribute' => 'property',
				'attribute_value' => 'og:image',
				'content' => $popup,
			]);
			/*og meta tags ends*/

			$data['images'] = [];

			if ($mpblogpost_info['posttype'] == 'IMAGES') {

				$results = $this->model_extension_mpblog_mpblogpost->getMpBlogPostImages($this->request->get['mpblogpost_id']);

				foreach ($results as $result) {
					$data['images'][] = [
						'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('mpblog_image_post_popup_width'), $this->config->get('mpblog_image_post_popup_height')),
						'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('mpblog_image_post_additional_width'), $this->config->get('mpblog_image_post_additional_height'))
					];
				}
			}


			$data['comment_guest'] = (($this->config->get('mpblog_comments_default_guest') || $this->customer->isLogged()));

			$data['rating_guest'] = ( ($this->config->get('mpblog_blog_guest_rating') || $this->customer->isLogged()));


			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['rating'] = (int)$mpblogpost_info['rating'];

			// Captcha

			if ($this->config->get($this->extension_prefix['captcha'] . $this->config->get('config_captcha') . '_status') && $this->config->get('mpblog_blog_captcha_comment')) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}


			$data['share'] = $this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . (int)$this->request->get['mpblogpost_id']);

			// get next and previous blog starts
			$url = '';

			if (isset($this->request->get['mpcpath'])) {
				$url .= '&mpcpath=' . $this->request->get['mpcpath'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['author'])) {
				$url .= '&author=' . $this->request->get['author'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['date'])) {
				$url .= '&date=' . $this->request->get['date'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$blogcategory = 0;
			if (isset($this->request->get['mpcpath'])) {
				$parts = explode('_', (string)$this->request->get['mpcpath']);
				$blogcategory = (int)array_pop($parts);
			}
			$nextprev = [];
			if ((int)$this->config->get('mpblog_blog_nextprev')) {
				$nextprev = $this->model_extension_mpblog_mpblogpost->getNextPrevMpBlogPost($blogcategory, $this->request->get['mpblogpost_id']);

			}

			$href = '';
			$image = '';
			$name = '';
			$posttype = '';
			if (!empty($nextprev['next'])) {
				$href = $this->url->link('extension/mpblog/blog', $url . '&mpblogpost_id=' . $nextprev['next']['mpblogpost_id']);
				$image = $this->model_tool_image->resize('no_image.png', 180, 111);
				$posttype = $nextprev['next']['posttype'];
				if ($nextprev['next']['posttype'] == 'VIDEO') {

					$youthumb = $this->model_extension_mpblog_mpblogpost->mpYouTubeThumb($nextprev['next']['video'
						]);
					if ($youthumb['defaultThumb']) {
						$image = $youthumb['defaultThumb'];
					}

				} else {
					if (!empty($nextprev['next']['image']) && file_exists(DIR_IMAGE . $nextprev['next']['image'] )) {
						$image = $this->model_tool_image->resize($nextprev['next']['image'], 180, 111);
					}

				}

				if ((int)$this->config->get('mpblog_blog_nextprev_title')) {
				$name = utf8_strlen($nextprev['next']['name'] > 45) ? utf8_substr($nextprev['next']['name'], 0,45) .'...' : $nextprev['next']['name'];
				}
			}
			$data['nextprev']['next'] = [
				'width' => 180,
				'height' => 111,
				'posttype' => $posttype == 'VIDEO',
				'href' => $href,
				'image' => $image,
				'name' => $name
			];

			$href = '';
			$image = '';
			$name = '';
			$posttype = '';
			if (!empty($nextprev['prev'])) {
				$href = $this->url->link('extension/mpblog/blog', $url . '&mpblogpost_id=' . $nextprev['prev']['mpblogpost_id']);
				$image = $this->model_tool_image->resize('no_image.png', 180, 111);
				$posttype = $nextprev['prev']['posttype'];
				if ($nextprev['prev']['posttype'] == 'VIDEO') {
					$youthumb = $this->model_extension_mpblog_mpblogpost->mpYouTubeThumb($nextprev['prev']['video']);
					if ($youthumb['defaultThumb']) {
						$image = $youthumb['defaultThumb'];
					}
				} else {
					if (!empty($nextprev['prev']['image']) && file_exists(DIR_IMAGE . $nextprev['prev']['image'] )) {
						$image = $this->model_tool_image->resize($nextprev['prev']['image'], 180, 111);
					}
				}
				if ((int)$this->config->get('mpblog_blog_nextprev_title')) {
				$name = utf8_strlen($nextprev['prev']['name'] > 45) ? utf8_substr($nextprev['prev']['name'], 0, 45).'...' : $nextprev['prev']['name'];
				}
			}
			$data['nextprev']['prev'] = [
				'width' => 180,
				'height' => 111,
				'posttype' => $posttype == 'VIDEO',
				'href' => $href,
				'image' => $image,
				'name' => $name
			];

			// get next and previous blog ends

			// Related Blogs

			$data['mpblogposts'] = [];

			$results = $this->model_extension_mpblog_mpblogpost->getMpBlogPostRelated($this->request->get['mpblogpost_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('mpblog_image_post_related_width'), $this->config->get('mpblog_image_post_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('mpblog_image_post_related_width'), $this->config->get('mpblog_image_post_related_height'));
				}


				if ($this->config->get('mpblog_blog_show_rating')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$iframeVideo = $this->model_extension_mpblog_mpblogpost->mpVideoEmbedURL($result['video']);

				$showImage = true;

				if ($result['posttype'] == 'VIDEO' && !empty($iframeVideo)) {
					$showImage = false;
				}

				$tags = [];
				/*foreach(explode(",", $result['tag']) as $rtag) {
					$tags[] = [
						'tag' => $rtag,
						'href' => $this->url->link('extension/mpblog/blogs', 'tag=' . trim($rtag))
					];
				}*/

				$authorurl = $this->url->link('extension/mpblog/blogs', 'author=' . trim($result['author']));
				$date_availableurl = $this->url->link('extension/mpblog/blogs', 'date=' . trim($result['date_available']));

				$data['mpblogposts'][] = [
					'mpblogpost_id'  => $result['mpblogpost_id'],
					'thumb'       	 => $image,
					'isLikeByMe'	=> $this->model_extension_mpblog_mpblogpost->isLikeByMe($result['mpblogpost_id']),
					'name'        	 => $result['name'],
					'posttype'		 => $result['posttype'],
					'tag'			 => $tags,
					'author'		 => $result['author'],
					'authorurl'		 => $authorurl,
					'date_available' => date( ($this->config->get('mpblog_blog_date_format') ? $this->config->get('mpblog_blog_date_format') : $this->language->get('date_format_short')) ,strtotime($result['date_available'])),
					'date_availableurl' => $date_availableurl,
					'viewed'		 => $result['viewed'],
					'rating'		 => $result['rating'],
					'comments'		 => $result['comments'] ,
					'showImage'		 => $showImage,
					'wishlist'		 => $result['likes'],
					'iframeVideo'	 => $iframeVideo,
					'width'			 => $this->config->get('mpblog_image_post_related_width'),
					'height'		 => $this->config->get('mpblog_image_post_related_height'),
					'sdescription' 	 => substr(html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'), 0, $this->config->get('mpblog_blog_sdescription_length')),
					'sdescription1'  => html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'),
					'href'        	 => $this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . $result['mpblogpost_id'])
				];
			}

			$data['tags'] = [];

			if ($mpblogpost_info['tag']) {
				$tags = explode(',', $mpblogpost_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = [
						'tag'  => trim($tag),
						'href' => $this->url->link('extension/mpblog/blogs', 'tag=' . trim($tag))
					];
				}
			}

			// Related Products
			$data['products'] = [];

			$results = $this->model_extension_mpblog_mpblogpost->getMpBlogPostRelatedProducts($this->request->get['mpblogpost_id']);
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('mpblog_image_post_related_width'), $this->config->get('mpblog_image_post_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('mpblog_image_post_related_width'), $this->config->get('mpblog_image_post_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = [
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('mpblog_blog_sdescription_length')) ,
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] )
				];
			}

			// Related categories
			$data['categories'] = [];

			$results = $this->model_extension_mpblog_mpblogpost->getMpBlogPostRelatedCategories($this->request->get['mpblogpost_id']);
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('mpblog_image_post_related_width'), $this->config->get('mpblog_image_post_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('mpblog_image_post_related_width'), $this->config->get('mpblog_image_post_related_height'));
				}

				$data['categories'][] = [
					'category_id'  => $result['category_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('mpblog_blog_sdescription_length')),
					'href'        => $this->url->link('product/category', 'path=' . $result['category_id'] )
				];
			}


			$this->model_extension_mpblog_mpblogpost->updateViewed($this->request->get['mpblogpost_id']);

			$data['socialtop'] = false;
			$data['socialbottom'] = false;
			$data['socials'] = [];

			if ($this->config->get('mpblog_blog_viewsocial') && $this->config->get('mpblog_blog_sociallocation') && $this->config->get('mpblog_social')) {

				$sociallocation = $this->config->get('mpblog_blog_sociallocation');

				foreach($sociallocation as $loc) {
					if ($loc == 'TOP') {
						$data['socialtop'] = true;
					}

					if ($loc == 'BOTTOM') {
						$data['socialbottom'] = true;
					}
				}

				$socials = $this->config->get('mpblog_social');



				$sort_social = [];
				foreach($socials as $key => $social) {
					$sort_social[$key] = $social['sort_order'];
				}

				array_multisort($sort_social, SORT_ASC, $socials);

				foreach($socials as $social) {
					$social['sname'] = isset($social['name'][(int)$this->config->get('config_language_id')]) ? $social['name'][(int)$this->config->get('config_language_id')] : '';
					$data['socials'][] = $social;
				}

			}

			$data['show_author'] = $this->config->get('mpblog_blog_author');
			$data['show_viewed'] = $this->config->get('mpblog_blog_viewcount');
			$data['show_wishlist'] = $this->config->get('mpblog_blog_viewwishlist');
			$data['show_date'] = $this->config->get('mpblog_blog_date');
			$data['show_comments'] = $this->config->get('mpblog_blog_show_comment');
			$data['use_comments'] = $this->config->get('mpblog_blog_use_comment');
			$data['show_rating'] = $this->config->get('mpblog_blog_show_rating');
			$data['show_readmore'] = $this->config->get('mpblog_blog_show_readmore');
			$data['show_tag'] = $this->config->get('mpblog_blog_show_tags');
			$data['show_sdescription'] = $this->config->get('mpblog_blog_sdescription');
			$data['mpblog_blog_designs'] = $this->config->get('mpblog_blog_design');

			$data['allow_comment'] = $this->config->get('mpblog_blog_allow_comment');
			$data['allow_rating'] = $this->config->get('mpblog_blog_allow_rating');


			$data['sharethis'] = $this->config->get('mpblog_blog_sharethis');

			// facebook comment settings
			$data['mpblog_facebook_appid'] = $this->config->get('mpblog_facebook_appid');
			$data['mpblog_facebook_nocomment'] = $this->config->get('mpblog_facebook_nocomment');
			$data['mpblog_facebook_color'] = $this->config->get('mpblog_facebook_color');
			$data['mpblog_facebook_order'] = $this->config->get('mpblog_facebook_order');
			$data['mpblog_facebook_width'] = $this->config->get('mpblog_facebook_width');

			// disqus comment settigs
			$data['comment_disqus_code'] = html_entity_decode( $this->config->get('mpblog_comment_disqus_code'), ENT_QUOTES, 'UTF-8');
			$data['comment_disqus_count'] = html_entity_decode( $this->config->get('mpblog_comment_disqus_count'), ENT_QUOTES, 'UTF-8');



			$data['blog_image'] = $this->config->get('mpblog_blog_image');
			$data['blog_image_popup'] = $this->config->get('mpblog_blog_image_popup');
			$data['blog_description'] = $this->config->get('mpblog_blog_description');







			$data['comment_tabs'] = [];

			if ($this->config->get('mpblog_blog_use_comment')) {
				if ($this->config->get('mpblog_comments_default')) {
					$data['comment_tabs']['default'] = $this->language->get('text_comments_default');
				}

				if ($this->config->get('mpblog_comments_facebook')) {
					$data['comment_tabs']['facebook'] = $this->language->get('text_comments_facebook');
				}

				if ($this->config->get('mpblog_comments_google')) {
					$data['comment_tabs']['google'] = $this->language->get('text_comments_google');
				}

				if ($this->config->get('mpblog_comments_disqus')) {
					$data['comment_tabs']['disqus'] = $this->language->get('text_comments_disqus');
				}
			}

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$data['text_loading'] = $this->language->get('text_loading');
			$data['error_rating'] = $this->language->get('error_rating');

			$data['action'] = $this->url->link('extension/mpblog/blog', 'mpblogpost_id='.$mpblogpost_info['mpblogpost_id'], true);
			$data['mpblogpost_id'] = $mpblogpost_info['mpblogpost_id'];

			$data['themename'] = $this->model_extension_mpblog_mpblogpost->themename();
			$data['themeclass'] = $this->model_extension_mpblog_mpblogpost->themeclass();

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');


			$this->response->setOutput($this->load->view('mpblog/blog', $data));

		} else {
			$url = '';

			if (isset($this->request->get['mpcpath'])) {
				$url .= '&mpcpath=' . $this->request->get['mpcpath'];
			}


			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['author'])) {
				$url .= '&author=' . $this->request->get['author'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['date'])) {
				$url .= '&date=' . $this->request->get['date'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}


			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/mpblog/blog', $url . '&mpblogpost_id=' . $mpblogpost_id)
			];

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
	}

	public function addRating() {
		$this->load->language('mpblog/blog');

		$json = [];

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			if (!isset($json['error'])) {
				$this->load->model('extension/mpblog/mpblogpost');

				$this->model_extension_mpblog_mpblogpost->addRating($this->request->get['mpblogpost_id'], $this->request->post);

				$json['success'] = $this->language->get('text_rating_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function comment() {
		$this->load->language('mpblog/blog');

		$this->load->model('extension/mpblog/mpblogpost');

		$data['text_no_comments'] = $this->language->get('text_no_comments');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 5;

		$data['comments'] = [];

		$comment_total = $this->model_extension_mpblog_mpblogpost->getTotalCommentsByMpBlogPostId($this->request->get['mpblogpost_id']);

		$results = $this->model_extension_mpblog_mpblogpost->getCommentsByMpBlogPostId($this->request->get['mpblogpost_id'], ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['comments'][] = [
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'date_added' => date( ($this->config->get('mpblog_blog_date_format') ? $this->config->get('mpblog_blog_date_format') : $this->language->get('date_format_short')) ,strtotime($result['date_added']))
			];
		}

		$pagination = new Pagination();
		$pagination->total = $comment_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/mpblog/blog/comment', 'mpblogpost_id=' . $this->request->get['mpblogpost_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($comment_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($comment_total - $limit)) ? $comment_total : ((($page - 1) * $limit) + $limit), $comment_total, ceil($comment_total / $limit));

		$this->response->setOutput($this->load->view('mpblog/mpblogcomment', $data));
	}

	public function writeComment() {
		$this->load->language('mpblog/blog');

		$json = [];

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			// Captcha
			if ($this->config->get($this->extension_prefix['captcha'] . $this->config->get('config_captcha') . '_status') && $this->config->get('mpblog_blog_captcha_comment')) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('extension/mpblog/mpblogpost');

				$this->model_extension_mpblog_mpblogpost->addComment($this->request->get['mpblogpost_id'], $this->request->post);

				$json['success'] = $this->language->get('text_comment_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function like() {
		$json = [];

		if (!empty($this->request->get['action']) && $this->request->post[$this->request->get['action']] == 1) {

			$this->load->language('mpblog/blog');
			$this->load->model('extension/mpblog/mpblogpost');

			$mpblogpost_info = $this->model_extension_mpblog_mpblogpost->getMpBlogPost($this->request->post['id']);
			if ($mpblogpost_info) {

				if ($this->customer->isLogged()) {
					$this->model_extension_mpblog_mpblogpost->likedMpBlogPost($this->request->post['id']);
				} else {
					// add session cookie into user browser.
					setcookie('mpblog'. $this->request->post['id'] .'liked', 'true'); // Not ask until browser is open. Once browser closes ask again to confirm.

				}
				$this->model_extension_mpblog_mpblogpost->likeMpBlogPost($this->request->post['id']);
			}

			$json['total'] = $this->model_extension_mpblog_mpblogpost->totalMpBlogPostLikes($this->request->post['id']);


			$json['success'] = 1;
			$json['logged'] = $this->customer->isLogged();

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function unlike() {
		$json = [];

		if (!empty($this->request->get['action']) && $this->request->post[$this->request->get['action']] == 1) {

			$this->load->language('mpblog/blog');
			$this->load->model('extension/mpblog/mpblogpost');

			$mpblogpost_info = $this->model_extension_mpblog_mpblogpost->getMpBlogPost($this->request->post['id']);
			if ($mpblogpost_info) {
				if ($this->customer->isLogged()) {
					$this->model_extension_mpblog_mpblogpost->unlikedMpBlogPost($this->request->post['id']);
				} else {
					// delete cookie from user browser
					setcookie('mpblog'. $this->request->post['id'] .'liked', 'true', time() - 3600); // Not ask until browser is open. Once browser closes ask again to confirm.
				}
				$this->model_extension_mpblog_mpblogpost->unlikeMpBlogPost($this->request->post['id']);
			}

			$json['total'] = $this->model_extension_mpblog_mpblogpost->totalMpBlogPostLikes($this->request->post['id']);
			$json['success'] = 1;
			$json['logged'] = $this->customer->isLogged();

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
