<?php
/* This file is under Git Control by KDSI. */
error_reporting(0);
class ControllerExtensionModuleOcrestapi extends Controller {

    public function index() {
        $this->load->language('extension/module/ocrestapi');

        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        $data = $this->language->all();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

                $this->model_setting_setting->editSetting('module_ocrestapi', $this->request->post);
                $this->model_setting_setting->setOauthClient($this->request->post['module_ocrestapi_client_id'], $this->request->post['module_ocrestapi_client_secret']);

                $this->session->data['success'] = $this->language->get('text_success');
                
                $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            
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
            'href' => $this->url->link('extension/module/ocrestapi', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->request->post['module_ocrestapi_status'])) {
            $data['module_ocrestapi_status'] = $this->request->post['module_ocrestapi_status'];
        } else {
            $data['module_ocrestapi_status'] = $this->config->get('module_ocrestapi_status');
        }

        if (isset($this->request->post['module_ocrestapi_key'])) {
            $data['module_ocrestapi_key'] = $this->request->post['module_ocrestapi_key'];
        } else {
            $data['module_ocrestapi_key'] = $this->config->get('module_ocrestapi_key');
        }

        
        if (isset($this->request->post['module_ocrestapi_client_id'])) {
            $data['module_ocrestapi_client_id'] = $this->request->post['module_ocrestapi_client_id'];
        } else {
            $data['module_ocrestapi_client_id'] = $this->config->get('module_ocrestapi_client_id');
        }

        if (isset($this->request->post['module_ocrestapi_client_secret'])) {
            $data['module_ocrestapi_client_secret'] = $this->request->post['module_ocrestapi_client_secret'];
        } else {
            $data['module_ocrestapi_client_secret'] = $this->config->get('module_ocrestapi_client_secret');
        }

        if (isset($this->request->post['module_ocrestapi_token_ttl'])) {
            $data['module_ocrestapi_token_ttl'] = $this->request->post['module_ocrestapi_token_ttl'];
        } else {
            $data['module_ocrestapi_token_ttl'] = $this->config->get('module_ocrestapi_token_ttl');
        }

        if (!isset($data['module_ocrestapi_token_ttl']) || empty($data['module_ocrestapi_token_ttl'])) {
            $data['module_ocrestapi_token_ttl'] = 2628000;
        }

        if (isset($this->request->post['module_ocrestapi_allowed_ip'])) {
            $data['module_ocrestapi_allowed_ip'] = $this->request->post['module_ocrestapi_allowed_ip'];
        } else {
            $data['module_ocrestapi_allowed_ip'] = $this->config->get('module_ocrestapi_allowed_ip');
        }

        //set default client id
        if (!isset($data['module_ocrestapi_client_id']) || empty($data['module_ocrestapi_client_id'])) {
            $data['module_ocrestapi_client_id'] = 'ocrestapi_oauth_client';
        }

        //set default client secret
        if (!isset($data['module_ocrestapi_client_secret']) || empty($data['module_ocrestapi_client_secret'])) {
            $data['module_ocrestapi_client_secret'] = 'ocrestapi_oauth_secret';
        }

        $data['basic_token'] = '';

        if(!empty($data['module_ocrestapi_client_secret']) && !empty($data['module_ocrestapi_client_id'])) {
            $data['basic_token'] = base64_encode($data['module_ocrestapi_client_id'].':'.$data['module_ocrestapi_client_secret']);
        }


        $data['install_success'] = '';

        if (isset($this->session->data['install_success'])) {
            if (!empty($this->session->data['install_success'])){
                $data['install_success'] = "We successfully installed the .htaccess rewrite rules. Backup file of your original .htaccess: ".DIR_SYSTEM . "../.htaccess_rest_api_backup";
                $this->session->data['install_success'] = "";
            }
        }

        if (isset($error['warning'])) {
            $data['error_warning'] = $error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['action']     = $this->url->link('extension/module/ocrestapi', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/ocrestapi', $data));

    }
    protected function validate() {

        $hasError = false;

        if (!$this->user->hasPermission('modify', 'extension/module/ocrestapi')) {
            $hasError = true;
        }

        return !$hasError;
    }


    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS oauth_clients (client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80) NOT NULL, redirect_uri VARCHAR(2000) NOT NULL, grant_types VARCHAR(80), scope VARCHAR(100), user_id VARCHAR(80), api_version VARCHAR(80), CONSTRAINT clients_client_id_pk PRIMARY KEY (client_id));");

        $this->db->query("CREATE TABLE IF NOT EXISTS oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires DATETIME NOT NULL, scope VARCHAR(2000), session_id VARCHAR(40), data TEXT, CONSTRAINT access_token_pk PRIMARY KEY (access_token));");

        $this->db->query("CREATE TABLE IF NOT EXISTS oauth_scopes (scope TEXT, is_default BOOLEAN);");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mobile_category_image` (`category_id` int(11) NOT NULL,`image` varchar(255) NOT NULL, PRIMARY KEY (`category_id`));");

         $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mobile_banner` (`banner_id` int(11) NOT NULL AUTO_INCREMENT,`image` varchar(255) NOT NULL,`title` varchar(255) NOT NULL,`subtitle` varchar(255) NOT NULL,`button_label` varchar(255) NOT NULL,`type` varchar(255) NOT NULL, `name` varchar(255) NOT NULL,`id` varchar(255) NOT NULL, PRIMARY KEY (`banner_id`));");
        
        $response = $this->installHtaccess();

        if($response !== true){
            $this->session->data['api_install_error'] = $response;
        } else {
            $this->session->data['install_success'] = 1;
        }
    }


    private function installHtaccess() {

        $directory = DIR_SYSTEM . '../';

        $htaccess  = $directory . '.htaccess';

        //if htaccess does not exist or there is no htaccess.txt or the file is not writable return
        if( ! file_exists( $htaccess ) && file_exists( $directory . '.htaccess.txt' ) ) {
            if( ! @ rename( $directory . '.htaccess.txt', $htaccess ) ) {
                return 'Could not rename .htaccess.txt';
            };
        }

        // .htaccess does not exist or directory is not writable
        if( ! file_exists( $htaccess ) ) {
            if (!is_writable($directory)) {
                return  $directory.' is not writable';
            }
            return 'Htaccess file does not exist ('.$htaccess.')';
        }

        $currentHtaccess = file_get_contents($htaccess);

        $pos = strpos($currentHtaccess, "feed/rest_api");

        //rewrite rules are installed
        if ($pos !== false) {
            return true;
        }

        $htaccessFilePermission    = null;

        if( ! is_readable( $htaccess ) || ! is_writable( $htaccess ) ) {
            //backup current file permission
            $htaccessFilePermission = fileperms( $htaccess );

            if( ! @ chmod( $htaccess, 777 ) )
                return 'We could not modify your htaccess file. Set permission to 777 during the install process.';
        }

        $newHtaccess = str_replace("RewriteCond %{REQUEST_FILENAME} !-f" , implode( "\n", array(
            '# Sets the HTTP_AUTHORIZATION header removed by apache',
            'RewriteCond %{HTTP:Authorization} .',
            'RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]',
            'RewriteCond %{REQUEST_FILENAME} !-f',
        )), $currentHtaccess);

        //backup current htaccess file
        @file_put_contents($directory.".htaccess_restapi_backup", $currentHtaccess);

        @file_put_contents($htaccess, $newHtaccess);

        //restore htaccess file permission
        if( $htaccessFilePermission ) {
            @ chmod( $htaccess, $htaccessFilePermission );
        }

        return true;
    }
}
