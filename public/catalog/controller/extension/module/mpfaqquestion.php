<?php
class ControllerExtensionModuleMpfaqquestion extends Controller {
	public function index($setting) {

		static $module = 0;	

		$this->document->addStyle('catalog/view/javascript/mpfaq/mpfaq.css');
		$this->document->addScript('catalog/view/javascript/mpfaq/mpfaq.js');

		
		$this->load->language('extension/module/mpfaqquestion');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('extension/mpfaq/faq');

		$data['categories'] = [];
		if (!empty($setting['mpfaqcategory'])) {
			$mpfaqcategories = $setting['mpfaqcategory'];

			foreach ($mpfaqcategories as $mpfaqcategory_id) {
				$category = $this->model_extension_mpfaq_faq->getMpFaqCategory($mpfaqcategory_id);
				if($category) {
					$questions = $this->model_extension_mpfaq_faq->getMpFaqQuestions(['filter_mpfaqcategory_id'  => $mpfaqcategory_id]);

					$questions_data = [];
					foreach ($questions as $question) {
						$questions_data[] = [
							'question'			=> $question['question'],
							'answer'			=> html_entity_decode($question['answer'], ENT_QUOTES, 'UTF-8'),
						];
					}

					if($questions_data) {
						$data['categories'][] = [
							'name'				=> $category['name'],
							'questions'			=> $questions_data,
						];
					}
				}
			}
		}

		if ($data['categories']) {
			$data['module'] = $module++;
			
			return $this->load->view('extension/module/mpfaqquestion', $data);
		}
	}
}