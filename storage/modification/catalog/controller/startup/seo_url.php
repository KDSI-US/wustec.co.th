<?php
/* This file is under Git Control by KDSI. */
class ControllerStartupSeoUrl extends Controller
{
	public function index()
	{

		try {
			$subfolder_check = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = 'subfolder_prefixes' AND `value` = '1' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1")->num_rows;
		} catch (Exception $e) {
			$subfolder_check = false;
		}
		try {
			$default_lang_prefix = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = 'default_lang_prefix' AND `value` = '1' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1")->num_rows;
		} catch (Exception $e) {
			$default_lang_prefix = false;
		}

		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL

		if ($subfolder_check) {
			$lquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "language;");
			$lparts = isset($this->request->get['_route_']) ? explode('/', $this->request->get['_route_']) : array();
			$lcode  = isset($lparts[0]) ? $lparts[0] : '';

			$default_lang_code  = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_language'")->row['value'];
			$active_lang_prefix = $active_lang_code = isset($this->session->data['language']) ? $this->session->data['language'] : $default_lang_code;
			$subfolder_prefixes_alias = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = '" . $this->db->escape('subfolder_prefixes_alias') . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1");
			$subfolder_prefixes_alias = json_decode($subfolder_prefixes_alias->row['value'], true);

			if (in_array($lcode, $subfolder_prefixes_alias)) {
				$active_lang_prefix = $active_lang_code = array_search($lcode, $subfolder_prefixes_alias);
			}
			if (isset($subfolder_prefixes_alias[$active_lang_prefix])) {
				$active_lang_prefix = $subfolder_prefixes_alias[$active_lang_prefix];
			}

			if ($default_lang_prefix) {
				$table_check = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "seo_module_settings'")->num_rows;

				if ($table_check) {
					$redirect_active_lang_prefix = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = 'redirect_active_lang_prefix' AND `value` = '1' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1")->num_rows;

					if ($redirect_active_lang_prefix) {
						if (isset($this->request->get['_route_']) || empty($this->request->get)) {
							$missing_lang_code = true;
							if ($lcode == $active_lang_prefix) {
								$missing_lang_code = false;
							}

							if ($missing_lang_code) {
								$base = $this->config->get('config_url');
								if ($this->request->server['HTTPS']) {
									$base = $this->config->get('config_ssl');
								}

								/* mps */
								$mcustomseo = [];
								$mcustomseo['m-blogs'] =  'extension/mpblog/blogs';
								/* mpe */

								if (isset($this->request->get['_route_'])) {
									$this->response->redirect($base . $active_lang_prefix . '/' . $this->request->get['_route_']);
								} else {
									$this->response->redirect($base . $active_lang_prefix . '/');
								}
							}
						}
					}
				}
			}

			/* mps */
			$mcustomseo = [];
			$mcustomseo['m-blogs'] =  'extension/mpblog/blogs';
			/* mpe */

			if (isset($this->request->get['_route_'])) {
				foreach ($lquery->rows as $language) {
					if ($language['code'] == $active_lang_code) {
						$this->session->data['language'] = $language['code'];

						$this->language = new Language($language['code']);
						$this->language->load($language['code']);

						$this->registry->set('language', $this->language);
						$this->config->set('config_language_id', $language['language_id']);

						if ($default_lang_prefix || $default_lang_code != $active_lang_code) {
							$this->request->get['_route_'] = substr($this->request->get['_route_'], strlen($active_lang_prefix . '/'));
						}
					}
				}
			}
		}

		/* mps */
		$mcustomseo = [];
		$mcustomseo['m-blogs'] =  'extension/mpblog/blogs';
		/* mpe */

		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			$this->load->model('setting/setting');
			$moduleSettings = $this->model_setting_setting->getSetting('isenselabs_gdpr', $this->config->get('config_store_id'));
			$gdprSettings = !empty($moduleSettings['isenselabs_gdpr']) ? $moduleSettings['isenselabs_gdpr'] : array();

			$gdprSeoSlugs = !empty($gdprSettings['Slug']) ? $gdprSettings['Slug'] : array('gdpr-tools');
			$parts = array_filter($parts);
			foreach ($gdprSeoSlugs as $gdpr_slug) {
				if (count($parts) == 1 && ($parts[0] == $gdpr_slug)) {
					$this->request->get['route'] = 'extension/module/isenselabs_gdpr';
					return new Action($this->request->get['route']);
				}
			}

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			$langIdSwitchTo = 0;

			foreach ($parts as $part) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

				if (!$subfolder_check && !empty($query->row['language_id'])) {
					$langIdSwitchTo = $query->row['language_id'];
				}

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'post_id') {
						$this->request->get['post_id'] = $url[1];
					}
					if ($url[0] == 'blog_category_id') {
						if (!isset($this->request->get['bpath'])) {
							$this->request->get['bpath'] = $url[1];
						} else {
							$this->request->get['bpath'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'video_gallery_category_id') {
						$this->request->get['video_gallery_category_id'] = $url[1];
					}

					if ($url[0] == 'video_gallery_category_id') {
						if (!isset($this->request->get['video_path'])) {
							$this->request->get['video_path'] = $url[1];
						} else {
							$this->request->get['video_path'] .= '_' . $url[1];
						}
					}

					/* mps */
					if ($url[0] == 'mpblogpost_id') {
						$this->request->get['mpblogpost_id'] = $url[1];
					}
					if ($url[0] == 'mpblogcategory_id') {
						if (!isset($this->request->get['mpcpath'])) {
							$this->request->get['mpcpath'] = $url[1];
						} else {
							$this->request->get['mpcpath'] .= '_' . $url[1];
						}
					}
					/* mpe */

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

					if ($url[0] == 'extension/module/galleria' && isset($this->request->get['galleria_id'])) {
						$url[0] = 'galleria_id';
						$this->request->get['route'] = 'extension/module/galleria/info';
					}
					if ($url[0] == 'galleria_id') {
						$this->request->get['route'] = 'extension/module/galleria/info';
						if (isset($url[1])) {
							$this->request->get['galleria_id'] = $url[1];
						}
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

					if ($url[0] == 'mpplan_id') {
						$this->request->get['mpplan_id'] = $url[1];
					}

					if ($url[0] == 'gallery_id') {
						$this->request->get['gallery_id'] = $url[1];
					}

					if (
						$query->row['query'] && $url[0] != 'mpblogcategory_id' && $url[0] != 'mpblogpost_id'
						&& $url[0] != 'gallery_id' &&  $url[0] != 'mpplan_id' &&  $url[0] != 'information_id' && $url[0] != 'galleria_path' && $url[0] != 'galleria_id' && $url[0] != 'manufacturer_id' &&
						$url[0] != 'category_id' && $url[0] != 'product_id' && $url[0] != 'video_gallery_category_id' && $url[0] != 'video_gallery_category_id' && $url[0] != 'post_id' && $url[0] != 'blog_category_id'
					) {

						$this->request->get['route'] = $query->row['query'];
					}
				} else {

					/* mps */
					if (!isset($mcustomseo[$part])) {
						$this->request->get['route'] = 'error/not_found';
					} else {
						$this->request->get['route'] = $mcustomseo[$part];
					}
					/* mpe */

					break;
				}
			}

			if (!empty($langIdSwitchTo) && $this->config->get('config_language_id') != $langIdSwitchTo) {
				$this->load->model('localisation/language');
				$language_info = $this->model_localisation_language->getLanguage($langIdSwitchTo);

				if (!empty($language_info['code'])) {
					$this->session->data['language'] = $language_info['code'];

					$language = new Language($language_info['code']);
					$language->load($language_info['code']);

					$this->registry->set('language', $language);
					$this->config->set('config_language_id', $language_info['language_id']);
				}
			}

			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['galleria_id']) || isset($this->request->get['galleria_path'])) {
					$this->request->get['route'] = 'extension/module/galleria/info';
				} elseif (isset($this->request->get['mpblogpost_id'])) {
					$this->request->get['route'] = 'extension/mpblog/blog';
				} elseif (isset($this->request->get['mpcpath'])) {
					$this->request->get['route'] = 'extension/mpblog/blogcategory';
				} elseif (isset($this->request->get['path'])) {

					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['post_id'])) {
					$this->request->get['route'] = 'extension/post';
				} elseif (isset($this->request->get['bpath'])) {
					$this->request->get['route'] = 'extension/blogcategory';
				} elseif (isset($this->request->get['mpplan_id'])) {
					$this->request->get['route'] = 'membership/plan_details';
				} elseif (isset($this->request->get['gallery_id'])) {
					$this->request->get['route'] = 'extension/mpphotogallery/photo';
				} elseif (isset($this->request->get['information_id'])) {

					$this->request->get['route'] = 'information/information';
				} elseif (isset($this->request->get['video_path'])) {
					$this->request->get['route'] = 'extension/video_gallery_all';
				} elseif (isset($this->request->get['video_gallery_category_id'])) {
					$this->request->get['route'] = 'extension/video_gallery_category';
				}
			}
		} else {

			$table_check = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "seo_module_settings'")->num_rows;

			if ($table_check && $this->request->server['REQUEST_METHOD'] == 'GET') {
				$redirect_to_seo_links_check = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = 'redirect_to_seo_links' AND `value` = '1' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1")->num_rows;

				if ($redirect_to_seo_links_check) {
					$redirect_to_seo_links_check = true;
				} else {
					$redirect_to_seo_links_check = false;
				}
			} else {
				$redirect_to_seo_links_check = false;
			}

			if (isset($this->request->get['route']) && !empty($this->request->get['route']) && $redirect_to_seo_links_check) {
				$request_uri = isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
				$request_uri_segment = '';
				$request_uri_segment_pos = strpos($request_uri, '&');

				if ($request_uri_segment_pos) {
					$request_uri_segment = substr($request_uri, $request_uri_segment_pos + 1);
				}

				$SEO_link = $this->url->link($this->request->get['route'], $request_uri_segment, 'SSL');
				$isSEOFriendy = strpos($SEO_link, 'route=') ? false : true;

				if ($isSEOFriendy) {
					$this->response->redirect($SEO_link);
				}
			}
		}
	}

	protected function islCustomAlias($link)
	{
		$url        = '';
		$data       = array();
		$alias_fail = false;

		$url_info   = parse_url(str_replace('&amp;', '&', $link));

		if (isset($url_info['query'])) {
			parse_str($url_info['query'], $data);

			$mcustomseo = [];
			$mcustomseo['extension/mpblog/blogs'] = 'm-blogs';

			if (isset($data['route']) && $data['route'] == 'extension/module/isenselabs_gdpr') {
				$this->load->model('setting/setting');
				$moduleSettings = $this->model_setting_setting->getSetting('isenselabs_gdpr', $this->config->get('config_store_id'));
				$gdprSettings = !empty($moduleSettings['isenselabs_gdpr']) ? $moduleSettings['isenselabs_gdpr'] : array();

				$gdprSeoSlug = isset($gdprSettings['Slug'][$this->config->get('config_language_id')]) ? $gdprSettings['Slug'][$this->config->get('config_language_id')] : 'gdpr-tools';
				$url .= '/' . $gdprSeoSlug;
			}
		}

		if (isset($data['route'])) {
			foreach ($data as $key => $value) {
				if (is_array($value)) {
					continue;
				}

				$custom_seo_urls = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_custom_urls` WHERE `query` = '" . $this->db->escape($value) . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");

				if ($custom_seo_urls->num_rows) {
					$url .= '/' . $custom_seo_urls->row['keyword'];
					unset($data['key']);
				}
			}
		}

		return array(
			'data'  => $data,
			'url'   => $url
		);
	}

	public function rewrite_for_related($link)
	{

		$subfolder_check = false;
		$unify_check = false;
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		$mcustomseo = [];
		$mcustomseo['extension/mpblog/blogs'] = 'm-blogs';

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {

				if (
					($data['route'] == 'product/product' && $key == 'product_id') || ($data['route'] == 'extension/mpblog/blog' && $key == 'mpblogpost_id') ||
					($data['route'] == 'extension/video_gellry_all' && $key == 'video_gallery_category_id') || ($data['route'] == 'extension/post' && $key == 'post_id') ||
					(($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id') || ($data['route'] == 'extension/mpphotogallery/photo' && $key == 'gallery_id') || ($data['route'] == 'membership/plan_details' && $key == 'mpplan_id') || ($data['route'] == 'membership/plan_details' && $key == 'mpplan_id')
				) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {

						if ($unify_check) { // SEO Backpack Unify Script
							$url = '/' . $query->row['keyword'];
						} else {
							$url = '/' . $query->row['keyword'];
						}
						unset($data[$key]);
					}
				} elseif ($data['route'] == 'extension/home') {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($data['route']) . "'");
					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];
					} else {
						$url = '';
						break;
					}

					unset($data[$key]);
				} elseif ($key == 'video_path') {
					$video_gallery_categories = explode('_', $value);
					foreach ($video_gallery_categories as $video_gallery_category) {
						$strSql = "
						SELECT * 
						FROM `" . DB_PREFIX . "seo_url` 
						WHERE 
							`query` = 'video_gallery_category_id=" . (int)$video_gallery_category . "' 
							AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' 
							AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' 
					";
						$query = $this->db->query($strSql);
						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';
							break;
						}
					}
					unset($data[$key]);
				} elseif ($key == 'bpath') {
					$categories = explode('_', $value);
					foreach ($categories as $category) {

						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'blog_category_id=" . (int)$category . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				} elseif ($data['route'] == 'extension/module/galleria/info' && $key == 'galleria_id') {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($data['route'] == 'extension/module/galleria/info' && $key == 'galleria_path') {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'extension/module/galleria' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($key == 'video_path') {
					$video_gallery_categories = explode('_', $value);
					foreach ($video_gallery_categories as $video_gallery_category) {
						$strSql = "
						SELECT * 
						FROM `" . DB_PREFIX . "seo_url` 
						WHERE 
							`query` = 'video_gallery_category_id=" . (int)$video_gallery_category . "' 
							AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' 
							AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' 
					";
						$query = $this->db->query($strSql);
						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';
							break;
						}
					}
					unset($data[$key]);
				} elseif ($key == 'mpcpath') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'mpblogcategory_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				} elseif (isset($mcustomseo[$data['route']])) {
					$url .= '/' . $mcustomseo[$data['route']];
					unset($data[$key]);
				} elseif ($key == 'path') {

					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {

							if ($unify_check) { // SEO Backpack Unify Script
								$url = '/' . $query->row['keyword'];
							} else {
								$url = '/' . $query->row['keyword'];
							}
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		if (($url) && ($url <> '/' . $this->session->data['language'])) {
			unset($data['route']);

			$query = '';

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}

	public function rewrite($link)
	{

		try {
			$subfolder_check = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = 'subfolder_prefixes' AND `value` = '1' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1")->num_rows;
		} catch (Exception $e) {
			$subfolder_check = false;
		}
		try {
			$default_lang_prefix = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = 'default_lang_prefix' AND `value` = '1' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1")->num_rows;
		} catch (Exception $e) {
			$default_lang_prefix = false;
		}

		if ($subfolder_check) {
			$active_lang_code = isset($this->session->data['language']) ? $this->session->data['language'] : $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_language'")->row['value'];
			$subfolder_prefixes_alias = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = '" . $this->db->escape('subfolder_prefixes_alias') . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1");
			$subfolder_prefixes_alias = json_decode($subfolder_prefixes_alias->row['value'], true);
		}

		try { // SEO Backpack Unify Script
			$unify_check = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "seo_module_settings` WHERE `key` = 'unify_urls' AND `value` = '1' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1")->num_rows;
		} catch (Exception $e) {
			$unify_check = false;
		}

		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		$mcustomseo = [];
		$mcustomseo['extension/mpblog/blogs'] = 'm-blogs';

		if (isset($data['route']) && $data['route'] == 'extension/module/isenselabs_gdpr') {
			$this->load->model('setting/setting');
			$moduleSettings = $this->model_setting_setting->getSetting('isenselabs_gdpr', $this->config->get('config_store_id'));
			$gdprSettings = !empty($moduleSettings['isenselabs_gdpr']) ? $moduleSettings['isenselabs_gdpr'] : array();

			$gdprSeoSlug = isset($gdprSettings['Slug'][$this->config->get('config_language_id')]) ? $gdprSettings['Slug'][$this->config->get('config_language_id')] : 'gdpr-tools';
			$url .= '/' . $gdprSeoSlug;
		}

		if (isset($data['route']) && $data['route'] == 'common/home') { //Common Home Fix
			$is_common_home = true;
		} else {
			$is_common_home = false;
		}

		if ($is_common_home) {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			if ($subfolder_check) {
				$language_string = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_language'")->row['value'];

				if (isset($this->session->data['language'])) {
					if ($this->session->data['language'] <> $language_string || $default_lang_prefix) {
						$url_prefix = '/' . $this->session->data['language'] . '/';

						if (!empty($subfolder_prefixes_alias) && isset($subfolder_prefixes_alias[$active_lang_code])) {
							$url_prefix = '/' . $subfolder_prefixes_alias[$active_lang_code] . '/';
						}
					} else {
						$url_prefix = '';
					}
				} else {
					$url_prefix = '';
				}

				return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url_prefix . $url . $query;
			} else {
				$new_link = $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
				return $new_link;
			}
		}

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {

				if (
					($data['route'] == 'product/product' && $key == 'product_id') || ($data['route'] == 'extension/mpblog/blog' && $key == 'mpblogpost_id') ||
					($data['route'] == 'extension/video_gellry_all' && $key == 'video_gallery_category_id') || ($data['route'] == 'extension/post' && $key == 'post_id') ||
					(($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id') || ($data['route'] == 'extension/mpphotogallery/photo' && $key == 'gallery_id') || ($data['route'] == 'membership/plan_details' && $key == 'mpplan_id')
				) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {

						if ($unify_check) { // SEO Backpack Unify Script
							$url = '/' . $query->row['keyword'];
						} else {
							$url .= '/' . $query->row['keyword'];
						}

						unset($data[$key]);
					}
				} elseif ($data['route'] == 'extension/home') {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($data['route']) . "'");
					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];
					} else {
						$url = '';
						break;
					}

					unset($data[$key]);
				} elseif ($key == 'video_path') {
					$video_gallery_categories = explode('_', $value);
					foreach ($video_gallery_categories as $video_gallery_category) {
						$strSql = "
						SELECT * 
						FROM `" . DB_PREFIX . "seo_url` 
						WHERE 
							`query` = 'video_gallery_category_id=" . (int)$video_gallery_category . "' 
							AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' 
							AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' 
					";
						$query = $this->db->query($strSql);
						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';
							break;
						}
					}
					unset($data[$key]);
				} elseif ($key == 'bpath') {
					$categories = explode('_', $value);
					foreach ($categories as $category) {

						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'blog_category_id=" . (int)$category . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				} elseif ($data['route'] == 'extension/module/galleria/info' && $key == 'galleria_id') {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($data['route'] == 'extension/module/galleria/info' && $key == 'galleria_path') {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'extension/module/galleria' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($key == 'video_path') {
					$video_gallery_categories = explode('_', $value);
					foreach ($video_gallery_categories as $video_gallery_category) {
						$strSql = "
						SELECT * 
						FROM `" . DB_PREFIX . "seo_url` 
						WHERE 
							`query` = 'video_gallery_category_id=" . (int)$video_gallery_category . "' 
							AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' 
							AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' 
					";
						$query = $this->db->query($strSql);
						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';
							break;
						}
					}
					unset($data[$key]);
				} elseif ($key == 'mpcpath') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'mpblogcategory_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				} elseif (isset($mcustomseo[$data['route']])) {
					$url .= '/' . $mcustomseo[$data['route']];
					unset($data[$key]);
				} elseif ($key == 'path') {

					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {

							if ($unify_check) { // SEO Backpack Unify Script
								$url = '/' . $query->row['keyword'];
							} else {
								$url .= '/' . $query->row['keyword'];
							}
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		$islCustomAlias = $this->islCustomAlias($link);
		if (!empty($islCustomAlias['url'])) {
			$data = $islCustomAlias['data'];
			$url .= $islCustomAlias['url'];
		}

		if (($url) && ($url <> '/' . $this->session->data['language'])) {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			if ($subfolder_check) {
				$language_string = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_language'")->row['value'];

				if (isset($this->session->data['language'])) {
					if ($this->session->data['language'] <> $language_string || $default_lang_prefix) {
						$url_prefix = '/' . $this->session->data['language'];

						if (!empty($subfolder_prefixes_alias) && isset($subfolder_prefixes_alias[$active_lang_code])) {
							$url_prefix = '/' . $subfolder_prefixes_alias[$active_lang_code];
						}
					} else {
						$url_prefix = '';
					}
				} else {
					$url_prefix = '';
				}

				return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url_prefix . $url . $query;
			} else {
				return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
			}
		} else {
			return $link;
		}
	}
}
