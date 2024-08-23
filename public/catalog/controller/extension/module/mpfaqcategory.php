<?php
class ControllerExtensionModuleMpfaqcategory extends Controller {
	public function index() {
		$this->load->language('extension/module/mpfaqcategory');

		$data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['faqpath'])) {
			$data['filter_mpfaqcategory_id'] = $this->request->get['faqpath'];
		} else {
			$data['filter_mpfaqcategory_id'] = '';
		}

		$this->load->model('extension/mpfaq/faq');

		$data['mpcategories'] = [];

		$categories = $this->model_extension_mpfaq_faq->getMpFaqCategories();

		foreach ($categories as $category) {
			$data['mpcategories'][] = [
				'mpfaqcategory_id' => $category['mpfaqcategory_id'],
				'name'        => $category['name'] . ' (' . $this->model_extension_mpfaq_faq->getTotalMpFaqQuestions(['filter_mpfaqcategory_id'  => $category['mpfaqcategory_id']]) . ')',
				'href'        => $this->url->link('extension/mpfaq/faq', 'faqpath=' . $category['mpfaqcategory_id'])
			];
		}

		return $this->load->view('extension/module/mpfaqcategory', $data);
	}
}