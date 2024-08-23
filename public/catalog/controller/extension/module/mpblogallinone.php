<?php
class ControllerExtensionModuleMpBlogAllInOne extends Controller {
	public function index($setting) {
		if ($this->config->get('mpblog_status')) {
			static $module = 0;
			$this->load->language('extension/module/mpblogallinone');

			$this->document->addScript('catalog/view/javascript/mpblog/mpblog.js');

			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpblog/mpblog.css');

			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_readmore'] = $this->language->get('text_readmore');
			$data['text_comment'] = $this->language->get('text_comment');


			$data['position'] = 'column_left';
			if (isset($setting['position'])) {
				$data['position'] = $setting['position'];
			}

			foreach ($setting['limit'] as $key => &$limit) {
				if (!$limit) { $limit = 4; }
			}

			// tabs
			foreach ($setting['sort_order'] as $sort => &$order) {
				if (!$order) { $order = 1; }
			}

			asort($setting['sort_order']);

			$data['tabs'] = [];
			$data['text_empty'] = [];

			foreach ($setting['sort_order'] as $key => $value) {
				$data['tabs'][$key] = $this->language->get('text_'.$key);
				$data['text_empty'][$key] = $this->language->get('text_empty_'.$key);
			}

			$this->load->model('extension/mpblog/mpblogpost');
			$this->load->model('tool/image');

			$data['themename'] = $this->model_extension_mpblog_mpblogpost->themename();
			$data['themeclass'] = $this->model_extension_mpblog_mpblogpost->themeclass();

			if ($data['position'] == 'column_left' || $data['position'] == 'column_right') {
				$setting['width'] = 82;
				$setting['height']= 52;
			}


			// featured blogs

			$data['mpblogposts_featured'] = [];

			if (!empty($setting['mpblogpost'])) {
				$mpblogposts = array_slice($setting['mpblogpost'], 0, (int)$setting['limit']['featured']);

				foreach ($mpblogposts as $mpblogpost_id) {
					$mpblogpost_info = $this->model_extension_mpblog_mpblogpost->getMpBlogPost($mpblogpost_id);

					if ($mpblogpost_info) {
						if ($mpblogpost_info['image']) {
							$image = $this->model_tool_image->resize($mpblogpost_info['image'], $setting['width'], $setting['height']);
						} else {
							$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
						}

						if ($this->config->get('mpblog_blog_show_rating')) {
							$rating = (int)$mpblogpost_info['rating'];
						} else {
							$rating = false;
						}

						$iframeVideo = $this->model_extension_mpblog_mpblogpost->mpVideoEmbedURL($mpblogpost_info['video']);

						$showImage = true;

						if ($mpblogpost_info['posttype'] == 'VIDEO' && !empty($iframeVideo)) {
							$showImage = false;
						}

						$tags = [];
						/*foreach (explode(",", $mpblogpost_info['tag']) as $rtag) {
							$tags[] = [
								'tag' => $rtag,
								'href' => $this->url->link('extension/mpblog/blogs', 'tag=' . trim($rtag))
							;
						}*/

						$authorurl = $this->url->link('extension/mpblog/blogs', 'author=' . trim($mpblogpost_info['author']));
						$date_availableurl = $this->url->link('extension/mpblog/blogs', 'date=' . trim($mpblogpost_info['date_available']));

						$data['mpblogposts_featured'][] = [
							'mpblogpost_id'  => $mpblogpost_info['mpblogpost_id'],
							'thumb'       => $image,
							'isLikeByMe'	=> $this->model_extension_mpblog_mpblogpost->isLikeByMe($mpblogpost_info['mpblogpost_id']),
							'name'        => $mpblogpost_info['name'],
							'posttype'		=> $mpblogpost_info['posttype'],
							'tag'		=> $tags,
							'author'		=> $mpblogpost_info['author'],
							'authorurl'		=> $authorurl,
							'date_available'		=> date( ($this->config->get('mpblog_blog_date_format') ? $this->config->get('mpblog_blog_date_format') : $this->language->get('date_format_short')) ,strtotime($mpblogpost_info['date_available'])),
							'date_availableurl'		=> $date_availableurl,
							'viewed'		=> $mpblogpost_info['viewed'],
							'rating'		=> $mpblogpost_info['rating'],
							'comments'		=> $mpblogpost_info['comments'] ,
							'showImage'		=> $showImage,
							'wishlist'		=> $mpblogpost_info['likes'],
							'iframeVideo'	=> $iframeVideo,
							'width'			=> $setting['width'], // 97
							'height'		=> $setting['height'], // 67

							'sdescription' => substr(html_entity_decode($mpblogpost_info['sdescription'], ENT_QUOTES, 'UTF-8'), 0, $this->config->get('mpblog_blog_sdescription_length')),
							'sdescription1' => html_entity_decode($mpblogpost_info['sdescription'], ENT_QUOTES, 'UTF-8'),
							'href'        => $this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . $mpblogpost_info['mpblogpost_id'])
						];
					}
				}
			}

			// latest blogs
			$data['mpblogposts_latest'] = [];

			$filter_data = [
				'sort'  => 'p.date_added',
				'order' => 'DESC',
				'start' => 0,
				'limit' => $setting['limit']['latest']
			];

			$results = $this->model_extension_mpblog_mpblogpost->getMpBlogPosts($filter_data);

			if ($results) {
				foreach ($results as $result) {

					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
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

					$data['mpblogposts_latest'][] = [
						'mpblogpost_id'  => $result['mpblogpost_id'],
						'thumb'       => $image,
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
						'width'		=> $setting['width'], // 97
						'height'		=> $setting['height'], // 67
						'sdescription' => substr(html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'), 0, $this->config->get('mpblog_blog_sdescription_length')),
						'sdescription1' => html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'),
						'href'        => $this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . $result['mpblogpost_id'])
					];
				}
			}

			// popular blogs

			$data['mpblogposts_popular'] = [];


			$results = $this->model_extension_mpblog_mpblogpost->getPopularMpBlogPosts($setting['limit']['popular']);

			if ($data['position'] == 'column_left' || $data['position'] == 'column_right') {
				$setting['width'] = 82;
				$setting['height']= 52;
			}

			if ($results) {
				foreach ($results as $result) {

					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
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

					$data['mpblogposts_popular'][] = [
						'mpblogpost_id'  => $result['mpblogpost_id'],
						'thumb'       => $image,
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
						'width'			=> $setting['width'], // 97
						'height'		=> $setting['height'], // 67
						'sdescription' => substr(html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'), 0, $this->config->get('mpblog_blog_sdescription_length')),
						'sdescription1' => html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'),
						'href'        => $this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . $result['mpblogpost_id'])
					];
				}
			}

			// trending blogs

			$data['mpblogposts_trending'] = [];

			$results = $this->model_extension_mpblog_mpblogpost->getTrendingMpBlogPosts($setting['limit']['trending']);

			if ($results) {
				foreach ($results as $result) {

					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
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

					$data['mpblogposts_trending'][] = [
						'mpblogpost_id'  => $result['mpblogpost_id'],
						'thumb'       => $image,
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
						'width'		=> $setting['width'], // 97
						'height'		=> $setting['height'], // 67
						'sdescription' => substr(html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'), 0, $this->config->get('mpblog_blog_sdescription_length')),
						'sdescription1' => html_entity_decode($result['sdescription'], ENT_QUOTES, 'UTF-8'),
						'href'        => $this->url->link('extension/mpblog/blog', 'mpblogpost_id=' . $result['mpblogpost_id'])
					];
				}
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
			$data['mpblog_blog_designs'] = $this->config->get('mpblog_blog_design');

			$data['module'] = $module++;

			if ($data['position'] == 'column_left' || $data['position'] == 'column_right') {
				return $this->load->view('extension/module/mpblogallinone_list', $data);
			} else {
				return $this->load->view('extension/module/mpblogallinone_grid', $data);
			}
		}
	}
}