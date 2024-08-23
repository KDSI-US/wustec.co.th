<?php
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleImageChecker extends Controller
{
  public function index()
  {
    $this->load->language("extension/module/image_checker");

    $this->document->setTitle($this->language->get('heading_title'));

    $data['rows'] = $this->getMissingImages();
    $data['session'] = $this->request->get['user_token'];
    $data['heading_title'] = $this->language->get('heading_title');

    $data['token'] = $this->session->data['user_token'];
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    $data['selected_status'] = isset($this->request->post['status']) ?  $this->request->post['status'] : 'status_1';
    $data['selected_stock'] = isset($this->request->post['stock']) ?  $this->request->post['stock'] : '';
    $this->response->setOutput($this->load->view('extension/module/image_checker', $data));
  }

  protected function install()
  {
    $this->load->model('user/user_group');
    $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/image_checker');
    $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/image_checker');
  }

  private function getMissingImages()
  {
    $this->load->model('extension/module/image_checker');

    if (isset($this->request->post['stock'])) {
      $filter_stock = $this->request->post['stock'];
    } else {
      $filter_stock = '';
    }

    if (isset($this->request->post['status'])) {
      $filter_status = $this->request->post['status'];
      if ($filter_status == 'status_99') {
        $filter_status = '';
      }
    } else {
      $filter_status = 'status_1';
    }

    $filter_data = array(
      'filter_stock'  => $filter_stock,
      'filter_status' => $filter_status
    );
    $results = $this->model_extension_module_image_checker->getProductImages($filter_data);

    $rows = array();
    foreach ($results as $result) {
      $image = DIR_IMAGE . $result['image'];
      if (!@is_array(getimagesize($image))) {
        $rows[] = array(
          'product_id' => $result['product_id'],
          'name' => $result['name'],
          'model' => $result['model'],
          'color' => $result['color_name'],
          'image' => $image,
          'stock' => $result['quantity'],
          'status' => $result['status']
        );
      }
    }
    return $rows;
  }

}
