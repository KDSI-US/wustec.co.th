<?php
class ControllerExtensionCaptchaGoogleRecaptchaV3 extends Controller
{
  private $error = array();

  public function index()
  {
    $this->language->load('extension/captcha/google_recaptcha_v3');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('captcha_google_recaptcha_v3', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=captcha', true));
    }

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['secretkey'])) {
      $data['error_secretkey'] = $this->error['secretkey'];
    } else {
      $data['error_secretkey'] = '';
    }

    if (isset($this->error['sitekey'])) {
      $data['error_sitekey'] = $this->error['sitekey'];
    } else {
      $data['error_sitekey'] = '';
    }

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text'      => $this->language->get('text_home'),
      'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
      'separator' => false
    );

    $data['breadcrumbs'][] = array(
      'text'      => $this->language->get('text_captcha'),
      'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true),
    );

    $data['breadcrumbs'][] = array(
      'text'      => $this->language->get('heading_title'),
      'href'      => $this->url->link('extension/captcha/google_recaptcha_v3', 'user_token=' . $this->session->data['user_token'], true),

    );

    $data['action'] = $this->url->link('extension/captcha/google_recaptcha_v3', 'user_token=' . $this->session->data['user_token'] . '&type=captcha', true);

    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=captcha', true);

    if (isset($this->request->post['captcha_google_recaptcha_v3_secretkey'])) {
      $data['secretkey'] = $this->request->post['captcha_google_recaptcha_v3_secretkey'];
    } else {
      $data['secretkey'] = $this->config->get('captcha_google_recaptcha_v3_secretkey');
    }

    if (isset($this->request->post['captcha_google_recaptcha_v3_status'])) {
      $data['status'] = $this->request->post['captcha_google_recaptcha_v3_status'];
    } else {
      $data['status'] = $this->config->get('captcha_google_recaptcha_v3_status');
    }

    if (isset($this->request->post['captcha_google_recaptcha_v3_sitekey'])) {
      $data['sitekey'] = $this->request->post['captcha_google_recaptcha_v3_sitekey'];
    } else {
      $data['sitekey'] = $this->config->get('captcha_google_recaptcha_v3_sitekey');
    }

    if (isset($this->request->post['captcha_google_recaptcha_v3_score'])) {
      $data['score'] = $this->request->post['captcha_google_recaptcha_v3_score'];
    } else {
      $data['score'] = $this->config->get('captcha_google_recaptcha_v3_score');
    }

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/captcha/google_recaptcha_v3', $data));
  }

  public function install()
  {
    $enable = 1;
    $this->load->model('setting/setting');
    $this->model_setting_setting->editSetting('captcha_google_recaptcha_v3', array('captcha_google_recaptcha_v3_status' => $enable, 'captcha_google_recaptcha_v3_score' => 0.5));
  }

  public function uninstall()
  {

    $this->load->model('setting/setting');
    $this->model_setting_setting->deleteSetting('captcha_google_recaptcha_v3');
  }

  private function validate()
  {

    if (!$this->user->hasPermission('modify', 'extension/captcha/google_recaptcha_v3')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (!$this->request->post['captcha_google_recaptcha_v3_secretkey']) {
      $this->error['secretkey'] = $this->language->get('error_secretkey');
    }

    if (!$this->request->post['captcha_google_recaptcha_v3_sitekey']) {
      $this->error['sitekey'] = $this->language->get('error_sitekey');
    }

    if (!$this->error) {
      return true;
    } else {
      return false;
    }
  }
}
