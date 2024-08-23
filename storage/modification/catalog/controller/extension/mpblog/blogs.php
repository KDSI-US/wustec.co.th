<?php
/* This file is under Git Control by KDSI. */
use \MpBlog\ControllerCatalog as Controller;
class ControllerExtensionMpBlogBlogs extends Controller {
	use \MpBlog\trait_mpblog_catalog;

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlogCatalog($registry);
	}
	public function index() {
		if ($this->config->get('mpblog_status')) {
		$this->load->language('mpblog/blogs');


		$this->load->model('extension/mpblog/mpblogpost');

		$this->load->model('tool/image');

		if ($this->config->get('theme_default_directory')) {
			$data['theme_name'] = $this->config->get('theme_default_directory');
		} else if ($this->config->get('config_template')) {
			$data['theme_name'] = $this->config->get('config_template');
		} else {
			$data['theme_name'] = 'default';
		}


		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = null;
		}

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} else {
			$tag = null;
		}

		if (isset($this->request->get['author'])) {
			$author = $this->request->get['author'];
		} else {
			$author = '';
		}


		if (isset($this->request->get['description'])) {
			$description = $this->request->get['description'];
		} else {
			$description = '';
		}

		if (isset($this->request->get['date'])) {
			$date = $this->request->get['date'];
		} else {
			$date = '';
		}

		if (isset($this->request->get['y'])) {
			$year = $this->request->get['y'];
		} else {
			$year = '';
		}

		if (isset($this->request->get['m'])) {
			$month = $this->request->get['m'];
		} else {
			$month = '';
		}


		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = ($this->config->get('mpblog_blog_page_limit')) ? $this->config->get('mpblog_blog_page_limit') : $this->config->get($this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		];

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/mpblog/mpblog.js');

		$this->document->addStyle('catalog/view/theme/default/stylesheet/mpblog/mpblog.css');

		$data['heading_title'] = $this->language->get('heading_title');

		// Set the last mpblogcategory breadcrumb
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpblog/blogs', '' )
		];

		$data['show_author'] = $this->config->get('mpblog_blog_author');
		$data['show_viewed'] = $this->config->get('mpblog_blog_viewcount');
		$data['show_wishlist'] = $this->config->get('mpblog_blog_viewwishlist');
		$data['show_date'] = $this->config->get('mpblog_blog_date');
		$data['show_comments'] = $this->config->get('mpblog_blog_show_comment');
		$data['show_rating'] = $this->config->get('mpblog_blog_show_rating');
		$data['show_readmore'] = $this->config->get('mpblog_blog_show_readmore');
		$data['show_tag'] = $this->config->get('mpblog_blog_show_tags');
		$data['show_sdescription'] = $this->config->get('mpblog_blog_sdescription');
		$data['mpblog_blog_design'] = $this->config->get('mpblog_blog_design');

		$data['text_readmore'] = $this->language->get('text_readmore');
		$data['text_blog'] = $this->language->get('text_blog');
		$data['text_comment'] = $this->language->get('text_comment');
		$data['text_empty'] = $this->language->get('text_empty');


		$data['mpblogposts'] = [];

		$filter_data = [
			'filter_name'	=> $search,
			'filter_tag'	=> $tag,
			'filter_author'	=> $author,
			'filter_date'	=> $date,
			'filter_year'	=> $year,
			'filter_month'	=> $month,
			'sort'	=> $sort,
			'order'	=> $order,
			'start'	=> ($page - 1) * $limit,
			'limit'	=> $limit
		];

		$mpblogpost_total = $this->model_extension_mpblog_mpblogpost->getTotalMpBlogPosts($filter_data);

		$results = $this->model_extension_mpblog_mpblogpost->getMpBlogPosts($filter_data);

		foreach ($results as $result) {
			if (!empty($result['image']) && file_exists(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('mpblog_image_post_thumb_width'), $this->config->get('mpblog_image_post_thumb_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('mpblog_image_post_thumb_width'), $this->config->get('mpblog_image_post_thumb_height'));
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
			/*foreach (explode(",", $result['tag']) as $rtag) {
				$tags[] = [
					'tag' => $rtag,
					'href' => $this->url->link('extension/mpblog/blogs', 'tag=' . trim($rtag))
				];
			}*/

			$authorurl = $this->url->link('extension/mpblog/blogs', 'author=' . trim($result['author']));
			$date_availableurl = $this->url->link('extension/mpblog/blogs', 'date=' . trim($result['date_available']));


			$data['mpblogposts'][] = [
				'mpblogpost_id'  => $result['mpblogpost_id'],
				'thumb'       	=> $image,
				'isLikeByMe'	=> $this->model_extension_mpblog_mpblogpost->isLikeByMe($result['mpblogpost_id']),
				'name'        => $result['name'],
				'posttype'		=> $result['posttype'],
				'tag'		=> $tags,
				'author'		=> $result['author'],
				'authorurl'		=> $authorurl,
				'date_available'		=> date( ($this->config->get('mpblog_blog_date_format') ? $this->config->get('mpblog_blog_date_format') : $this->language->get('date_format_short')) ,strtotime($result['date_available'])),
				'date_availableurl'		=> $date_availableurl,
				'viewed'		=> $result['viewed'],
				'rating'		=> $result['rating'],
				'comments'		=> $result['comments'] ,
				'showImage'		=> $showImage,
				'wishlist'		=> $result['likes'],
				'iframeVideo'		=> $iframeVideo,
				'width'		=> $this->config->get('mpblog_image_post_thumb_width'),
				'height'		=> $this->config->get('mpblog_image_post_thumb_height'),
				'sdescription' => substr(html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'), 0, $this->config->get('mpblog_blog_sdescription_length')),
				'sdescription1' => html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'),
				'href'        => $this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . $result['mpblogpost_id'])
			];
		}


		$url = '';

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

		if (isset($this->request->get['y'])) {
			$url .= '&y=' . $this->request->get['y'];
		}

		if (isset($this->request->get['m'])) {
			$url .= '&m=' . $this->request->get['m'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$pagination = new Pagination();
		$pagination->total = $mpblogpost_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/mpblog/blogs',  $url . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mpblogpost_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($mpblogpost_total - $limit)) ? $mpblogpost_total : ((($page - 1) * $limit) + $limit), $mpblogpost_total, ceil($mpblogpost_total / $limit));

		// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
		if ($page == 1) {
		    $this->document->addLink($this->url->link('extension/mpblog/blogs', '' , true), 'canonical');
		} elseif ($page == 2) {
		    $this->document->addLink($this->url->link('extension/mpblog/blogs', '' , true), 'prev');
		} else {
		    $this->document->addLink($this->url->link('extension/mpblog/blogs', '' . '&page='. ($page - 1), true), 'prev');
		}

		if ($limit && ceil($mpblogpost_total / $limit) > $page) {
		    $this->document->addLink($this->url->link('extension/mpblog/blogs', '' . '&page='. ($page + 1), true), 'next');
		}

		$data['date'] = $date;
		$data['tag'] = $tag;
		$data['author'] = $author;
		$data['year'] = $year;
		$data['month'] = $month;
		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;

		if (isset($data['theme_name']) && $data['theme_name'] == 'journal2') {
			$data['journal_class'] = 'journal-mblog';
		} else {
			$data['journal_class'] = '';
		}

		$data['button_continue'] = $this->language->get('button_continue');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if ($this->config->get('mpblog_blog_view') == 'GRID') {
		$this->response->setOutput($this->load->view('mpblog/blogs_grid', $data));
		} else {
		$this->response->setOutput($this->load->view('mpblog/blogs_list', $data));
		}
	}
  }
}
