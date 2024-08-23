<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionModuleGalleria extends Controller {

    private $error = array();
    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
    }

    public function index() {
        if ($this->config->get($this->prefix . 'galleria_status') && $this->config->get($this->prefix . 'galleria_page_status')) {

            $this->load->model('extension/module/galleria');

            $data['heading_title'] = $this->config->get($this->prefix . 'galleria_page_title')[$this->config->get('config_language_id')];
            $data['page_description'] = html_entity_decode($this->config->get($this->prefix . 'galleria_page_description')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
            $data['page_meta_title'] = $this->config->get($this->prefix . 'galleria_page_meta_title')[$this->config->get('config_language_id')];
            $data['page_meta_description'] = html_entity_decode($this->config->get($this->prefix . 'galleria_page_meta_description')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
            $data['page_meta_keyword'] = $this->config->get($this->prefix . 'galleria_page_meta_keyword')[$this->config->get('config_language_id')];
            $data['page_view'] = $this->config->get($this->prefix . 'galleria_page_view');
            $data['page_grid'] = $this->config->get($this->prefix . 'galleria_page_grid');
            if ($data['page_grid'] == 1) {
                $data['column'] = 12;
            } elseif ($data['page_grid'] == 2) {
                $data['column'] = 6;
            } elseif ($data['page_grid'] == 3) {
                $data['column'] = 4;
            } elseif ($data['page_grid'] == 4) {
                $data['column'] = 3;
            } elseif ($data['page_grid'] == 6) {
                $data['column'] = 2;
            } else {
                $data['column'] = 4;
            }
            $data['page_limit'] = $this->config->get($this->prefix . 'galleria_page_limit');
            $data['page_album_link'] = $this->config->get($this->prefix . 'galleria_page_album_link');
            $data['page_album_title'] = $this->config->get($this->prefix . 'galleria_page_album_title');
            $data['page_album_description'] = $this->config->get($this->prefix . 'galleria_page_album_description');
            $data['page_album_text'] = $this->config->get($this->prefix . 'galleria_page_album_text');
            $data['page_css'] = html_entity_decode($this->config->get($this->prefix . 'galleria_page_css'), ENT_QUOTES, 'UTF-8');
            $data['album_width'] = $this->config->get($this->prefix . 'galleria_album_width');
            $data['album_height'] = $this->config->get($this->prefix . 'galleria_album_height');
            $data['animation'] = $this->config->get($this->prefix . 'galleria_album_animation');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
                'text' => $data['heading_title'],
                'href' => $this->url->link('extension/module/galleria')
            );

            if ($data['page_meta_title']) {
                $this->document->setTitle($data['page_meta_title']);
            } else {
                $this->document->setTitle($data['heading_title']);
            }
            $this->document->setDescription($data['page_meta_description']);
            $this->document->setKeywords($data['page_meta_keyword']);
            $this->document->addLink($this->url->link('extension/module/galleria', '', true), 'canonical');
            if ($data['page_album_link']) {
                $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
                $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
            }
            if ($data['page_view'] == 2) {
                $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
                $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
                $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
            } elseif ($data['page_view'] == 3) {
                $this->document->addScript('catalog/view/javascript/jquery/masonry.pkgd.min.js');
            }
            
            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else { 
                $page = 1;
            }   

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = $data['page_limit'];
            }

            $data['gallery'] = array();

            $filter_data = array(
                'start' => ($page - 1) * $data['page_limit'],
                'limit' => $data['page_limit']
            );

            $results_total = $this->model_extension_module_galleria->getTotalGalleries($filter_data);
            $results = $this->model_extension_module_galleria->getGalleries($filter_data);

            foreach ($results as $result) {
                if ($result['inpage']) {
                    $thumb = $this->model_extension_module_galleria->getAlbumPoster($result['galleria_id']);
                    if ($thumb) {
                        if ($data['page_view'] == 3) {
                            $image = $this->model_extension_module_galleria->resize($thumb, $data['album_width'], $data['album_height'], 'msnr');
                        } else {
                            $image = $this->model_extension_module_galleria->resize($thumb, $data['album_width'], $data['album_height']);
                        }
                    } else {
                        $image = $this->model_extension_module_galleria->resize('placeholder.png', $data['album_width'], $data['album_height']);
                    }
                    $data['gallery'][] = array(
                        'galleria_id' => $result['galleria_id'],
                        'name' => $result['name'],
                        'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'image' => $image,
                        'date_added' => $result['date_added'],
                        'date_published' => $result['date_published'],
                        'date_modified' => $result['date_modified'],
                        'href' => $this->url->link('extension/module/galleria/info', ($this->config->get('module_galleria_page_status') ? 'galleria_path=1&galleria_id=' . $result['galleria_id'] : 'galleria_id=' . $result['galleria_id']), true)
                    );
                }
            }

            $url = '';

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $pagination = new Pagination();
            $pagination->total = $results_total;
            $pagination->page = $page;
            $pagination->limit =  $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $this->url->link('extension/module/galleria', $url . '&page={page}',true);

            $data['pagination'] = $pagination->render();
            $data['results'] = sprintf($this->language->get('text_pagination'), ($results_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($results_total - $limit)) ? $results_total : ((($page - 1) * $limit) + $limit), $results_total, ceil($results_total / $limit));

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('extension/module/galleria/galleria', $data));

        } else {

            $this->load->language('error/not_found');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('extension/module/galleria', '', true)
            );

            $this->document->setTitle($this->language->get('text_error'));

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

    public function info() {
        if ($this->config->get($this->prefix . 'galleria_status') && isset($this->request->get['galleria_id'])) {
            $this->load->language('extension/module/galleria');
            $this->load->model('extension/module/galleria');

            $data['album_id'] = $this->request->get['galleria_id'];
            $data['image_width'] = $this->config->get($this->prefix . 'galleria_image_width');
            $data['image_height'] = $this->config->get($this->prefix . 'galleria_image_height');
            $data['album_view'] = $this->config->get($this->prefix . 'galleria_album_view');
            $data['album_title'] = $this->config->get($this->prefix . 'galleria_album_title');
            $data['album_description'] = $this->config->get($this->prefix . 'galleria_album_description');
            $data['image_title'] = $this->config->get($this->prefix . 'galleria_album_image_title');
            $data['image_description'] = $this->config->get($this->prefix . 'galleria_album_image_description');
            $data['image_grid'] = $this->config->get($this->prefix . 'galleria_image_grid');
            if ($data['image_grid'] == 1) {
                $data['column'] = 12;
            } elseif ($data['image_grid'] == 2) {
                $data['column'] = 6;
            } elseif ($data['image_grid'] == 3) {
                $data['column'] = 4;
            } elseif ($data['image_grid'] == 4) {
                $data['column'] = 3;
            } elseif ($data['image_grid'] == 6) {
                $data['column'] = 2;
            } else {
                $data['column'] = 4;
            }
            $data['album_css'] = $this->config->get($this->prefix . 'galleria_album_css');
            $data['animation'] = $this->config->get($this->prefix . 'galleria_image_animation');

            $album_info = $this->model_extension_module_galleria->getGallery($this->request->get['galleria_id']);

            $data['heading_title'] = $album_info['name'];
            $data['description'] = html_entity_decode($album_info['description'], ENT_QUOTES, 'UTF-8');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            if ($album_info['inpage'] && $this->config->get($this->prefix . 'galleria_page_status')) {
                $data['breadcrumbs'][] = array(
                    'text' => $this->config->get($this->prefix . 'galleria_page_title')[$this->config->get('config_language_id')],
                    'href' => $this->url->link('extension/module/galleria')
                );
            }

            $data['breadcrumbs'][] = array(
                'text' => $data['heading_title'],
                'href' => $this->url->link('extension/module/galleria/info', ($album_info['inpage'] && $this->config->get('module_galleria_page_status') ? 'galleria_path=1&galleria_id=' . $this->request->get['galleria_id'] : 'galleria_id=' . $this->request->get['galleria_id']), true)
            );

            if ($album_info['meta_title']) {
                $this->document->setTitle($album_info['meta_title']);
            } else {
                $this->document->setTitle($album_info['name']);
            }
            $this->document->setDescription($album_info['meta_description']);
            $this->document->setKeywords($album_info['meta_keyword']);
            $this->document->addLink($this->url->link('extension/module/galleria/info', ($album_info['inpage'] && $this->config->get('module_galleria_page_status') ? 'galleria_path=1&galleria_id=' . $this->request->get['galleria_id'] : 'galleria_id=' . $this->request->get['galleria_id']), true), 'canonical');
            
            $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
            
            if ($data['album_view'] == 2) {
                $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
                $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
                $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
            } elseif ($data['album_view'] == 3) {
                $this->document->addScript('catalog/view/javascript/jquery/masonry.pkgd.min.js');
            }

            $data['images'] = array();
            $images = $this->model_extension_module_galleria->getAlbumImages($this->request->get['galleria_id']);

            foreach ($images as $image) {
   
                if ($image['image']) {
                    if ($data['album_view'] == 3) {
                        $thumb = $this->model_extension_module_galleria->resize($image['image'], $data['image_width'], $data['image_height'], 'msnr');
                    } else {
                        $thumb = $this->model_extension_module_galleria->resize($image['image'], $data['image_width'], $data['image_height']);
                    }
                } else {
                        $thumb = $this->model_extension_module_galleria->resize('placeholder.png', $data['image_width'], $data['image_height']);
                }
                    
                $data['images'][] = array(
                    'name' => $image['description']['name'],
                    'description' => html_entity_decode($image['description']['description'], ENT_QUOTES, 'UTF-8'),
                    'thumb' => $thumb,
                    'popup' => '/image/'.$image['image']
                );
                
            }

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('extension/module/galleria/galleria_info', $data));

        } else {

            $this->load->language('error/not_found');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('extension/module/galleria', '', true)
            );

            $this->document->setTitle($this->language->get('text_error'));

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

    public function widget($filter_data) {
        if ($this->config->get($this->prefix . 'galleria_status')) {
            $this->load->language('extension/module/galleria');
            $this->load->model('extension/module/galleria');

            $data['image_width'] = $this->config->get($this->prefix . 'galleria_widget_width');
            $data['image_height'] = $this->config->get($this->prefix . 'galleria_widget_height');
            $data['album_view'] = $this->config->get($this->prefix . 'galleria_widget_view');
            $data['album_title'] = $this->config->get($this->prefix . 'galleria_widget_title');
            $data['album_description'] = $this->config->get($this->prefix . 'galleria_widget_description');
            $data['image_title'] = $this->config->get($this->prefix . 'galleria_widget_image_title');
            $data['image_description'] = $this->config->get($this->prefix . 'galleria_widget_image_description');
            $data['image_grid'] = $this->config->get($this->prefix . 'galleria_widget_grid');
            if ($data['image_grid'] == 1) {
                $data['column'] = 12;
            } elseif ($data['image_grid'] == 2) {
                $data['column'] = 6;
            } elseif ($data['image_grid'] == 3) {
                $data['column'] = 4;
            } elseif ($data['image_grid'] == 4) {
                $data['column'] = 3;
            } elseif ($data['image_grid'] == 6) {
                $data['column'] = 2;
            } else {
                $data['column'] = 4;
            }
            $data['album_css'] = $this->config->get($this->prefix . 'galleria_widget_css');
            $data['animation'] = $this->config->get($this->prefix . 'galleria_widget_animation');

            $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
            
            if ($data['album_view'] == 2) {
                $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
                $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
                $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
            } elseif ($data['album_view'] == 3) {
                $this->document->addScript('catalog/view/javascript/jquery/masonry.pkgd.min.js');
            }

            $data['albums'] = array();
            $albums = $this->model_extension_module_galleria->getLinkedGalleries($filter_data);

            foreach ($albums as $album) {
                $album_images = array();
                $images = $this->model_extension_module_galleria->getAlbumImages($album['galleria_id']);

                foreach ($images as $image) {
       
                    if ($image['image']) {
                        if ($data['album_view'] == 3) {
                            $thumb = $this->model_extension_module_galleria->resize($image['image'], $data['image_width'], $data['image_height'], 'msnr');
                        } else {
                            $thumb = $this->model_extension_module_galleria->resize($image['image'], $data['image_width'], $data['image_height']);
                        }
                    } else {
                            $thumb = $this->model_extension_module_galleria->resize('placeholder.png', $data['image_width'], $data['image_height']);
                    }
                        
                    $album_images[] = array(
                        'name' => $image['description']['name'],
                        'description' => html_entity_decode($image['description']['description'], ENT_QUOTES, 'UTF-8'),
                        'thumb' => $thumb,
                        'date_added' => $result['date_added'],
                        'date_published' => $result['date_published'],
                        'date_modified' => $result['date_modified'],
                        'popup' => '/image/'.$image['image']
                    );
                    
                }

                $data['albums'][] = array(
                    'album_id' => $album['galleria_id'],
                    'name' => $album['name'],
                    'description' => html_entity_decode($album['description'], ENT_QUOTES, 'UTF-8'),
                    'images' => $album_images,
                    'href' => $this->url->link('extension/module/galleria/info', ($album['inpage'] && $this->config->get('module_galleria_page_status') ? 'galleria_path=1&galleria_id=' . $album['galleria_id'] : 'galleria_id=' . $album['galleria_id']), true)
                );

            }

            return $this->load->view('extension/module/galleria/galleria_widget', $data);

        }
    }

}
