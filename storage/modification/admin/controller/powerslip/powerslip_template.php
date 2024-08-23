<?php
/* This file is under Git Control by KDSI. */

class ControllerpowerslipPowerslipTemplate extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('powerslip/powerslip_template');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('powerslip/powerslip_template');

        $this->getList();
    }

    public function add() {
        $this->load->language('powerslip/powerslip_template');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('powerslip/powerslip_template');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_powerslip_powerslip_template->addPowerslipTemplate($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('powerslip/powerslip_template');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('powerslip/powerslip_template');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_powerslip_powerslip_template->editPowerslipTemplate($this->request->get['powerslip_template_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('powerslip/powerslip_template');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('powerslip/powerslip_template');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $powerslip_template_id) {
                $this->model_powerslip_powerslip_template->deletePowerslipTemplate($powerslip_template_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'template_name';
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

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add']    = $this->url->link('powerslip/powerslip_template/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('powerslip/powerslip_template/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['powerslip_templates'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $powerslip_template_total = $this->model_powerslip_powerslip_template->getTotalPowerslipTemplates();

        $results = $this->model_powerslip_powerslip_template->getPowerslipTemplates($filter_data);

        foreach ($results as $result) {
            $data['powerslip_templates'][] = array(
                'powerslip_template_id' => $result['id'],
                'template_name'         => $result['template_name'] . (($result['id'] == $this->config->get('config_powerslip_template_id')) ? $this->language->get('text_default') : null),
                'edit'                  => $this->url->link('powerslip/powerslip_template/edit', 'user_token=' . $this->session->data['user_token'] . '&powerslip_template_id=' . $result['id'] . $url, true)
            );
        }

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

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination        = new Pagination();
        $pagination->total = $powerslip_template_total;
        $pagination->page  = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url   = $this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($powerslip_template_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($powerslip_template_total - $this->config->get('config_limit_admin'))) ? $powerslip_template_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $powerslip_template_total, ceil($powerslip_template_total / $this->config->get('config_limit_admin')));

        $data['sort']  = $sort;
        $data['order'] = $order;

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('powerslip/powerslip_template_list', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['powerslip_template_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['template_name'])) {
            $data['error_name'] = $this->error['template_name'];
        } else {
            $data['error_name'] = array();
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['powerslip_template_id'])) {
            $data['action'] = $this->url->link('powerslip/powerslip_template/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('powerslip/powerslip_template/edit', 'user_token=' . $this->session->data['user_token'] . '&powerslip_template_id=' . $this->request->get['powerslip_template_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('powerslip/powerslip_template', 'user_token=' . $this->session->data['user_token'] . $url, true);


        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['template_name'])) {
            $data['powerslip_template']['template_name'] = $this->request->post['template_name'];
            $data['powerslip_template']['raw']           = $this->request->post['raw'];

        } elseif (isset($this->request->get['powerslip_template_id'])) {
            $data['powerslip_template']             = $this->model_powerslip_powerslip_template->getPowerslipTemplate($this->request->get['powerslip_template_id']);
            $data['powerslip_template']['raw']      = json_decode($data['powerslip_template']['raw']);
            $data['powerslip_template']['raw_json'] = json_encode($data['powerslip_template']['raw'], JSON_PRETTY_PRINT);
            if ($this->config->get('powerslip_cfg_log')) $this->log->write("[powerslip] raw_json: " . var_export($data['powerslip_template']['raw_json'], true));

        } else {
            $data['powerslip_template']['raw_json'] = json_encode(array(
                "width" => "21",
                "height" => "29.7",
            ));
        }

        $data['debug_powerslip'] = false;
        $data['powerslip_template_form_drag_board'] = $this->load->view('powerslip/powerslip_template_form_drag_board', $data);
        $data['layout_vue']                         = $this->load->view('powerslip/powerslip_template_form_1_layout_vue', $data);
        $data['product_loop_vue']                   = $this->load->view('powerslip/powerslip_template_form_2_product_loop_vue', $data);
        $data['free_text_vue']                      = $this->load->view('powerslip/powerslip_template_form_3_free_text_vue', $data);

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('powerslip/powerslip_template_form', $data));
    }

    protected function validateForm() {
        return !$this->error;
    }

    protected function validateDelete() {
        return !$this->error;
    }


}
