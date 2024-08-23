<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleMpBlogFeatured extends Controller {
	public function index($setting) {
		if ($this->config->get('mpblog_status')) {
			static $module = 0;
			$this->load->language('extension/module/mpblogfeatured');

			$this->document->addScript('catalog/view/javascript/mpblog/mpblog.js');

			$this->document->addStyle('catalog/view/theme/default/stylesheet/mpblog/mpblog.css');

			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_readmore'] = $this->language->get('text_readmore');
			$data['text_comment'] = $this->language->get('text_comment');

			$data['position'] = 'column_left';
			if (isset($setting['position'])) {
				$data['position'] = $setting['position'];
			}

			$this->load->model('extension/mpblog/mpblogpost');

			$data['themename'] = $this->model_extension_mpblog_mpblogpost->themename();
			$data['themeclass'] = $this->model_extension_mpblog_mpblogpost->themeclass();

			$this->load->model('tool/image');

			$data['mpblogposts'] = [];

			if (!$setting['limit']) {
				$setting['limit'] = 4;
			}

			if (!empty($setting['mpblogpost'])) {
				$mpblogposts = array_slice($setting['mpblogpost'], 0, (int)$setting['limit']);

				if ($data['position'] == 'column_left' || $data['position'] == 'column_right') {
					$setting['width'] = 82;
					$setting['height']= 52;
				}

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
							];
						}*/

						$authorurl = $this->url->link('extension/mpblog/blogs', 'author=' . trim($mpblogpost_info['author']));
						$date_availableurl = $this->url->link('extension/mpblog/blogs', 'date=' . trim($mpblogpost_info['date_available']));

						$data['mpblogposts'][] = [
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


			if ($data['mpblogposts']) {
				$data['module'] = $module++;

				if ($data['position'] == 'column_left' || $data['position'] == 'column_right') {
					return $this->load->view('extension/module/mpblogfeatured_list', $data);
				} else {
					return $this->load->view('extension/module/mpblogfeatured_grid', $data);
				}
			}
		}
	}
}