<?php
class ControllerExtensionModulePastorColumn extends Controller
{
	public function index($setting)
	{
		$this->load->language('extension/module/pastor_column');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('extension/pastor_column_category');
		$this->load->model('extension/pastor_column');

		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');

		$this->load->model('tool/image');

		$data['pastor_columns'] = array();

		$filter_data = array(
			'start' => 0,
			'limit' => 20
		);

		$pastor_column_info = $this->model_extension_pastor_column_category->getPastorColumnPhotos($filter_data);

		if ($pastor_column_info) {
			foreach ($pastor_column_info as $info) {
				$filter_data = array(
					'pastor_column_category_id' => $info['pastor_column_category_id']
				);
				$pastor_total = 0;
				$pastor_total = $this->model_extension_pastor_column->getTotalPastorColumns($filter_data);
				if ($info['image']) {
					$image = $this->model_tool_image->resize($info['image'], 360, 200);
				} else {
					$image = false;
				}
				$data['pastor_columns'][] = array(
					'pastor_column_category_id' => $info['pastor_column_category_id'],
					'title'	   		=> $info['title'],
					'description'	=> html_entity_decode($info['description']),
					'chk_title'	=> $info['chk_title'],
					'chk_description'	=> $info['chk_description'],
					'chk_image'	  => $info['chk_image'],
					'image'	      => $image,
					'href'	      => $this->url->link('extension/pastor_column_all' . '&pastor_column_category_id=' . $info['pastor_column_category_id'])
				);
			}
			if ($data['pastor_columns']) {
				return $this->load->view('extension/module/pastor_column', $data);
			}
		}
	}
}
