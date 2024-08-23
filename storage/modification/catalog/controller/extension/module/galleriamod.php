<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleGalleriamod extends Controller {
	public function index($setting) {
		static $module = 0;
		$this->load->model('extension/module/galleria');

		$data['image_width'] = $setting['width'];
        $data['image_height'] = $setting['height'];
        $data['album_view'] = $setting['view'];
        $data['album_title'] = $setting['album_title'];
        $data['album_description'] = $setting['album_description'];
        $data['image_title'] = $setting['image_title'];
        $data['image_description'] = $setting['image_description'];
        $data['image_grid'] = $setting['grid'];
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
        $data['album_css'] = $setting['css'];
        $data['animation'] = $setting['animation'];

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

		if (!empty($setting['album'])) {
			$albums = $setting['album'];

			foreach ($albums as $album_id) {
				$album = $this->model_extension_module_galleria->getGallery($album_id);

				if ($album) {
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
			}
		}

		if ($data['albums']) {
			$data['module'] = $module++;
			return $this->load->view('extension/module/galleriamod', $data);
		}
	}
}