<?php

class ControllerExtensionReportLowStock extends Controller
{
	public function index()
	{
		$this->load->language('extension/report/low_stock');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('report_low_stock', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/report/low_stock', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/report/low_stock', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true);

		if (isset($this->request->post['report_low_stock_status'])) {
			$data['report_low_stock_status'] = $this->request->post['report_low_stock_status'];
		} else {
			$data['report_low_stock_status'] = $this->config->get('report_low_stock_status');
		}

		if (isset($this->request->post['report_low_stock_sort_order'])) {
			$data['report_low_stock_sort_order'] = $this->request->post['report_low_stock_sort_order'];
		} else {
			$data['report_low_stock_sort_order'] = $this->config->get('report_low_stock_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/report/low_stock_form', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/report/low_stock')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function report()
	{
		$this->load->language('extension/report/low_stock');
		$this->load->model('extension/report/low_stock');

		$limit = $this->config->get('shortagelimit');
		$status = 1;
		if ($status == 1) {
			$data['shortage'] = $status;
			$data['lowstock'] = $limit;
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = '';
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = '';
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}


		if (isset($this->request->get['filter_quantity_check'])) {
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = $data['lowstock'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['export_csv_link'] = $this->url->link('extension/report/low_stock/exportCSV', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . $url, 'SSL');
		$data['export_xls_link'] = $this->url->link('extension/report/low_stock/exportXLS', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . $url, 'SSL');

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_quantity_check' => $filter_quantity_check,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_extension_report_low_stock->getTotalProducts($filter_data);

		$results = $this->model_extension_report_low_stock->getProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_extension_report_low_stock->getProductSpecials($result['product_id']);
			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}


		if (isset($this->request->get['filter_quantity_check'])) {
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}


		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check=' . $this->request->get['filter_quantity_check'];
		}

		$data['sort_name'] = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}


		if (isset($this->request->get['filter_quantity_check'])) {
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}


		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}


		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check=' . $this->request->get['filter_quantity_check'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=low_stock' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;

		$data['filter_quantity_check'] = $filter_quantity_check;

		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('extension/report/low_stock_info', $data);
	}

	public function exportCSV()
	{
		$this->load->model('extension/report/low_stock'); // Loading the Model of Products
		$temp_data = $this->model_extension_report_low_stock->getProducts(array('filter_status' => 1, 'filter_quantity_check' => $this->config->get('shortagelimit'))); // Fetch all the Products where Status is Enabled
		/* CSV Header Starts Here */
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=LowStockReportCSV-" . date('d-m-Y') . ".csv");
		/* Disable caching */
		header("Cache-Control: no-cache, no-store, must-revalidate"); /* HTTP 1.1 */
		header("Pragma: no-cache"); /* HTTP 1.0 */
		header("Expires: 0"); /* Proxies */
		/* CSV Header Ends Here */
		$output = fopen("php://output", "w"); /* Opens and clears the contents of file; or creates a new file if it doesn't exist */
		$h = "Product_Id, Product_Name, Product_Quantity";
		fputcsv($output, explode(', ', $h)); /* header line */

		$data1 = array();
		/* We don't want to export all the information to be exported so maintain a separate array for the information to be exported */
		foreach ($temp_data as $data) {
			$data1[] = array(
				'product_id' => $data['product_id'],
				'name' => $data['name'],
				'quantity' => $data['quantity'],
			);
		}

		/* Exporting the CSV */
		foreach ($data1 as $row) {
			fputcsv($output, $row); /* here you can change delimiter/enclosure */
		}
		fclose($output); /* Closing the File */
	}

	public function exportXLS()
	{
		$this->load->model('extension/report/low_stock'); // Loading the Model of Products
		$temp_data = $this->model_extension_report_low_stock->getProducts(array('filter_status' => 1));
		require('C:\Users\Dream IT\Downloads\PHPExcel_1.8.0_doc\PHPExcel_1.8.0_doc\Classes\PHPExcel.php');
		$objPHPExcel = new PHPExcel;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objPHPExcel->setActiveSheetIndex(0);

		foreach ($temp_data as $key => $data) {
			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $key, $data['product_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $key, $data['name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $key, $data['quantity']);
		}
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="file.xlsx"');
		$objWriter->save('php://output');
		exit;
	}

}
