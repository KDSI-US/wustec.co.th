<?php
/* This file is under Git Control by KDSI. */
use \MpBlog\ControllerCatalog as Controller;
class ControllerExtensionMpBlogBlogCategory extends Controller {
	use \MpBlog\trait_mpblog_catalog;

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlogCatalog($registry);
	}
	public function index() {
		if ($this->config->get('mpblog_status')) {
		$this->load->language('mpblog/blogcategory');

		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');

		$this->load->model('extension/mpblog/mpblogcategory');

		$this->load->model('extension/mpblog/mpblogpost');

		$this->load->model('tool/image');


		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} else {
			$tag = '';
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
			$limit = $this->config->get('mpblog_category_page_limit');
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_blog'),
			'href' => $this->url->link('extension/mpblog/blogs', '', true)
		];

		if (isset($this->request->get['mpcpath'])) {
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

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$mpcpath = '';

			$parts = explode('_', (string)$this->request->get['mpcpath']);

			$mpblogcategory_id = (int)array_pop($parts);

			foreach ($parts as $mpcpath_id) {
				if (!$mpcpath) {
					$mpcpath = (int)$mpcpath_id;
				} else {
					$mpcpath .= '_' . (int)$mpcpath_id;
				}

				$mpblogcategory_info = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategory($mpcpath_id);

				if ($mpblogcategory_info) {
					$data['breadcrumbs'][] = [
						'text' => $mpblogcategory_info['name'],
						'href' => $this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $mpcpath . $url)
					];
				}
			}
		} else {
			$mpblogcategory_id = 0;
		}

		$mpblogcategory_info = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategory($mpblogcategory_id);

		if ($mpblogcategory_info) {
			$this->document->setTitle($mpblogcategory_info['meta_title']);
			$this->document->setDescription($mpblogcategory_info['meta_description']);
			$this->document->setKeywords($mpblogcategory_info['meta_keyword']);

			$this->document->addScript('catalog/view/javascript/mpblog/mpblog.js');

			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpblog/mpblog.css');

			$data['heading_title'] = $mpblogcategory_info['name'];

			
			$data['text_empty'] = $this->language->get('text_empty');
			$data['text_readmore'] = $this->language->get('text_readmore');
			$data['text_comment'] = $this->language->get('text_comment');
			$data['text_categories'] = $this->language->get('text_categories');
			
			// Set the last mpblogcategory breadcrumb
			$data['breadcrumbs'][] = [
				'text' => $mpblogcategory_info['name'],
				'href' => $this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $this->request->get['mpcpath'])
			];

			$data['thumb'] = '';
			if (!empty($mpblogcategory_info['image']) && file_exists(DIR_IMAGE . $mpblogcategory_info['image'] ) && $this->config->get('mpblog_category_image')) {
				$data['thumb'] = $this->model_tool_image->resize($mpblogcategory_info['image'], $this->config->get('mpblog_image_category_width'), $this->config->get('mpblog_image_category_height'));
			}

			$data['description'] = '';
			if ($this->config->get('mpblog_category_description')) {
			$data['description'] = html_entity_decode($mpblogcategory_info['description'], ENT_QUOTES, 'UTF-8');
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

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['mpblogcategories'] = [];

			$results = $this->model_extension_mpblog_mpblogcategory->getMpBlogCategories($mpblogcategory_id);

			foreach ($results as $result) {
				$filter_data = [
					'filter_mpblogcategory_id'  => $result['mpblogcategory_id'],
					'filter_sub_mpblogcategory' => true
				];

				if (!empty($result['image']) && file_exists(DIR_IMAGE . $result['image'])  && $this->config->get('mpblog_category_image')) {
					$thumb = $this->model_tool_image->resize($result['image'], $this->config->get('mpblog_image_category_thumb_width'), $this->config->get('mpblog_image_category_thumb_height'));
				} else {
					$thumb = $this->model_tool_image->resize('placeholder.png', $this->config->get('mpblog_image_category_thumb_width'), $this->config->get('mpblog_image_category_thumb_height'));
				}

				$data['mpblogcategories'][] = [
					'name' => $result['name'] . ($this->config->get('mpblog_category_post_count') ? ' (' . $this->model_extension_mpblog_mpblogpost->getTotalMpBlogPosts($filter_data) . ')' : ''),
					'thumb' => $thumb,
					'href' => $this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $this->request->get['mpcpath'] . '_' . $result['mpblogcategory_id'] . $url)
				];
			}



			$data['show_author'] = $this->config->get('mpblog_blog_author');
			$data['show_viewed'] = $this->config->get('mpblog_blog_viewcount');
			$data['show_wishlist'] = $this->config->get('mpblog_blog_viewwishlist');
			$data['show_date'] = $this->config->get('mpblog_blog_date');
			$data['show_comments'] = $this->config->get('mpblog_blog_show_comment');
			$data['show_rating'] = $this->config->get('mpblog_blog_show_rating');
			$data['show_readmore'] = $this->config->get('mpblog_blog_show_readmore');
			$data['show_tag'] = $this->config->get('mpblog_blog_show_tags');
			$data['show_sdescription'] = $this->config->get('mpblog_blog_sdescription');
			$data['mpblog_category_design'] = $this->config->get('mpblog_category_design');


			$data['mpblogposts'] = [];

			$filter_data = [
				'filter_mpblogcategory_id' => $mpblogcategory_id,
				'filter_date'               => $date,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
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
					'href'        => $this->url->link('extension/mpblog/blog', 'mpcpath='. $this->request->get['mpcpath'] .'&mpblogpost_id=' . $result['mpblogpost_id'])
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
			$pagination->url = $this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $this->request->get['mpcpath'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($mpblogpost_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($mpblogpost_total - $limit)) ? $mpblogpost_total : ((($page - 1) * $limit) + $limit), $mpblogpost_total, ceil($mpblogpost_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $mpblogcategory_info['mpblogcategory_id'], true), 'canonical');
			} elseif ($page == 2) {
			    $this->document->addLink($this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $mpblogcategory_info['mpblogcategory_id'], true), 'prev');
			} else {
			    $this->document->addLink($this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $mpblogcategory_info['mpblogcategory_id'] . '&page='. ($page - 1), true), 'prev');
			}

			if ($limit && ceil($mpblogpost_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('extension/mpblog/blogcategory', 'mpcpath=' . $mpblogcategory_info['mpblogcategory_id'] . '&page='. ($page + 1), true), 'next');
			}

			$data['date'] = $date;
			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');
			$data['button_continue'] = $this->language->get('button_continue');

			$data['themename'] = $this->model_extension_mpblog_mpblogpost->themename();
			$data['themeclass'] = $this->model_extension_mpblog_mpblogpost->themeclass();

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if ($this->config->get('mpblog_category_view') == 'GRID') {
			$data['view_posts'] = $this->load->view('mpblog/blogcategory_post_grid', $data);
			} else {
			$data['view_posts'] = $this->load->view('mpblog/blogcategory_post_list', $data);
			}

			$this->response->setOutput($this->load->view('mpblog/blogcategory', $data));
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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/mpblog/blogcategory', $url)
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
}
