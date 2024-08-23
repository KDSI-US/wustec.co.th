<?php
class ControllerExtensionMpfaqFaq extends Controller {
	public function index() {
		$filter_mpfaqcategory_id = $data['faqpath'] = 0;
		if (isset($this->request->get['faqpath'])) {
			$filter_mpfaqcategory_id = $data['faqpath'] = $this->request->get['faqpath'];
		}

		$this->load->language('mpfaq/faq');

		$this->load->model('extension/mpfaq/faq');

		$this->document->addStyle('catalog/view/javascript/mpfaq/mpfaq.css');
		$this->document->addScript('catalog/view/javascript/mpfaq/mpfaq.js');

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/mpfaq/faq')
		];

		$this->document->setTitle($this->language->get('heading_title'));

		$category_info = $this->model_extension_mpfaq_faq->getMpFaqCategory($filter_mpfaqcategory_id);
		if($category_info) {
			$data['breadcrumbs'][] = [
				'text' => $category_info['name'],
				'href' => $this->url->link('extension/mpfaq/faq', 'faqpath='. $filter_mpfaqcategory_id, true)
			];
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_no_faq'] = $this->language->get('text_no_faq');
		$data['text_search'] = $this->language->get('text_search');
		$data['text_clear'] = $this->language->get('text_clear');

		$categories = $this->model_extension_mpfaq_faq->getMpFaqCategories([
			'filter_mpfaqcategory_id'				=> $filter_mpfaqcategory_id,
		]);

		$data['categories'] = [];
		foreach ($categories as $key => $category) {
			$questions = $this->model_extension_mpfaq_faq->getMpFaqQuestions([
				'filter_mpfaqcategory_id'  => $category['mpfaqcategory_id']
			]);

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

		$data['faqs'] = $this->load->view('mpfaq/faqs', $data);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('mpfaq/faq', $data));		
	}

	public function search() {
		$this->response->addHeader('Content-Type: application/json');
		$json = [];

		$this->request->get['filter_name'] =html_entity_decode(implode('', explode(',',$this->request->get['filter_name'])), ENT_QUOTES, 'UTF-8');

		if(!empty($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}


		$this->load->language('mpfaq/faq');

		$this->load->model('extension/mpfaq/faq');

		$filter_mpfaqcategory_id = 0;
		if (isset($this->request->get['faqpath'])) {
			$filter_mpfaqcategory_id = $this->request->get['faqpath'];
		}

		$data = [];

		$categories = $this->model_extension_mpfaq_faq->getMpFaqCategories([
			'filter_mpfaqcategory_id'				=> $filter_mpfaqcategory_id,
		]);

		$data['categories'] = [];

		$highlight = false;
		$highlight = 'combine';

		foreach ($categories as $key => $category) {
			$filter_data = [
				'filter_mpfaqcategory_id'  => $category['mpfaqcategory_id'],
				'filter_name'  => $filter_name,
				'filter_answer'  => true,
			];
			$questions = $this->model_extension_mpfaq_faq->getMpFaqQuestions($filter_data);

			$questions_data = [];
			foreach ($questions as $question) {
				$this->request->get['filter_name'] = html_entity_decode ($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8');

				$question['question'] = html_entity_decode ($question['question'], ENT_QUOTES, 'UTF-8');
				$question_question=str_ireplace($this->request->get['filter_name'],'<span class="highlight">'. htmlentities(substr($question['question'],stripos($question['question'],$this->request->get['filter_name']),strlen($this->request->get['filter_name']))) .'</span>',$question['question']);

				$question['answer'] = html_entity_decode ($question['answer'], ENT_QUOTES, 'UTF-8');
				$question_answer=str_ireplace($this->request->get['filter_name'],'<span class="highlight">'. htmlentities(substr($question['answer'],stripos($question['answer'],$this->request->get['filter_name']),strlen($this->request->get['filter_name']))) .'</span>',$question['answer']);

				$questions_data[] = [
					'question'			=> $question_question,
					'answer'			=> $question_answer,
				];
			}

			if($questions_data) {
				$data['categories'][] = [
					'name'				=> $category['name'],
					'questions'			=> $questions_data,
				];
			}
		}

		$data['text_no_faq'] = $this->language->get('text_no_faq');
		$json['faqs'] = $this->load->view('mpfaq/faqs', $data);

		

		$this->response->setOutput(json_encode($json));
	}
}