<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionModuleFooterSocialIcons extends Controller
{
  private $error = array();

  public function index()
  {
    $this->load->language('extension/module/footer_social_icons');
    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('module_footer_social_icons', $this->request->post);
      $this->session->data['success'] = $this->language->get('text_success');
      if (isset($this->request->get['goedit']) && isset($this->request->get['goedit'])) {
        $this->response->redirect($this->url->link('extension/module/footer_social_icons', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));;
      }
      $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
    }

    $data['heading_title'] = $this->language->get('heading_title');
    $data['button_save_changes'] = $this->language->get('button_save_changes');
    $data['button_save'] = $this->language->get('button_save');
    $data['button_cancel'] = $this->language->get('button_cancel');
    $data['text_enabled'] = $this->language->get('text_enabled');
    $data['text_disabled'] = $this->language->get('text_disabled');
    $data['text_extension'] = $this->language->get('text_extension');
    $data['text_module'] = $this->language->get('text_module');
    $data['text_success'] = $this->language->get('text_success');
    $data['text_edit'] = $this->language->get('text_edit');
    $data['entry_status'] = $this->language->get('entry_status');

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
      'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/module/footer_social_icons', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['action'] = $this->url->link('extension/module/footer_social_icons', 'user_token=' . $this->session->data['user_token'], true);
    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

    if (isset($this->request->post['module_footer_social_icons_status'])) {
      $data['module_footer_social_icons_status'] = $this->request->post['module_footer_social_icons_status'];
    } else {
      $data['module_footer_social_icons_status'] = $this->config->get('module_footer_social_icons_status');
    }

    if (isset($this->request->post['module_footer_social_icons_facebook_link'])) {
      $data['module_footer_social_icons_facebook_link'] = $this->request->post['module_footer_social_icons_facebook_link'];
    } else {
      $data['module_footer_social_icons_facebook_link'] = $this->config->get('module_footer_social_icons_facebook_link');
    }

    if (isset($this->request->post['module_footer_social_icons_youtube_link'])) {
      $data['module_footer_social_icons_youtube_link'] = $this->request->post['module_footer_social_icons_youtube_link'];
    } else {
      $data['module_footer_social_icons_youtube_link'] = $this->config->get('module_footer_social_icons_youtube_link');
    }

    if (isset($this->request->post['module_footer_social_icons_blog_link'])) {
      $data['module_footer_social_icons_blog_link'] = $this->request->post['module_footer_social_icons_blog_link'];
    } else {
      $data['module_footer_social_icons_blog_link'] = $this->config->get('module_footer_social_icons_blog_link');
    }

    if (isset($this->request->post['module_footer_social_icons_twitter_link'])) {
      $data['module_footer_social_icons_twitter_link'] = $this->request->post['module_footer_social_icons_twitter_link'];
    } else {
      $data['module_footer_social_icons_twitter_link'] = $this->config->get('module_footer_social_icons_twitter_link');
    }

    if (isset($this->request->post['module_footer_social_icons_pinterest_link'])) {
      $data['module_footer_social_icons_pinterest_link'] = $this->request->post['module_footer_social_icons_pinterest_link'];
    } else {
      $data['module_footer_social_icons_pinterest_link'] = $this->config->get('module_footer_social_icons_pinterest_link');
    }

    if (isset($this->request->post['module_footer_social_icons_googlep_link'])) {
      $data['module_footer_social_icons_googlep_link'] = $this->request->post['module_footer_social_icons_googlep_link'];
    } else {
      $data['module_footer_social_icons_googlep_link'] = $this->config->get('module_footer_social_icons_googlep_link');
    }

    if (isset($this->request->post['module_footer_social_icons_linkedin_link'])) {
      $data['module_footer_social_icons_linkedin_link'] = $this->request->post['module_footer_social_icons_linkedin_link'];
    } else {
      $data['module_footer_social_icons_linkedin_link'] = $this->config->get('module_footer_social_icons_linkedin_link');
    }

    if (isset($this->request->post['module_footer_social_icons_instagram_link'])) {
      $data['module_footer_social_icons_instagram_link'] = $this->request->post['module_footer_social_icons_instagram_link'];
    } else {
      $data['module_footer_social_icons_instagram_link'] = $this->config->get('module_footer_social_icons_instagram_link');
    }

    if (isset($this->request->post['module_footer_social_icons_whatsapp_link'])) {
      $data['module_footer_social_icons_whatsapp_link'] = $this->request->post['module_footer_social_icons_whatsapp_link'];
    } else {
      $data['module_footer_social_icons_whatsapp_link'] = $this->config->get('module_footer_social_icons_whatsapp_link');
    }

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/footer_social_icons', $data));
  }

  protected function getForm()
  {
    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }
  }

  protected function validate()
  {
    if (!$this->user->hasPermission('modify', 'extension/module/footer_social_icons')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }
    return !$this->error;
  }

  public function install()
  {
    $this->load->model('setting/setting');
    $this->model_setting_setting->editSetting('module_footer_social_icons', ['module_footer_social_icons_status' => 1]);
  }

  public function uninstall()
  {
    $this->load->model('setting/setting');
    $this->model_setting_setting->deleteSetting('module_footer_social_icons');
  }
}
