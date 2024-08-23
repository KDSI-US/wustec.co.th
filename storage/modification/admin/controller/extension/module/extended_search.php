<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionModuleExtendedSearch extends Controller
{

  private $error = array();

  public function index()
  {
    $this->load->language('extension/module/extended_search');

    $this->document->setTitle(strip_tags($this->language->get('heading_title')));
    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('module_extended_search', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
    }

    $temp_es = 'module_extended_search_';

    $config_data = array(
      'status',
      'tag',
      'model',
      'sku',
      'upc',
      'ean',
      'jan',
      'isbn',
      'mpn',
      'location',
      'attr'
    );

    foreach ($config_data as $conf) {
      if (isset($this->request->post[$temp_es . $conf])) {
        $data[$temp_es . $conf] = $this->request->post[$temp_es . $conf];
      } else {
        $data[$temp_es . $conf] = $this->config->get($temp_es . $conf);
      }
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
      'text' => $this->language->get('text_module'),
      'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/module/extended_search', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['action'] = $this->url->link('extension/module/extended_search', 'user_token=' . $this->session->data['user_token'], true);
    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/extended_search', $data));
  }

  protected function validate()
  {
    if (!$this->user->hasPermission('modify', 'extension/module/extended_search')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }
    return !$this->error;
  }
}
