<?php
/* This file is under Git Control by KDSI. */

class ControllerExtensionModuleRegistermanager extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/registermanager');

		$this->document->setTitle($this->language->get('tmdheading_title'));

		$this->load->model('setting/setting');

		// if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
		// 	$status=0;
		// 	if(isset($this->request->post['registermanager_status']))
		// 	{
		// 		$status=$this->request->post['registermanager_status'];
		// 	}
		// 	$status = 1;
		// 	$postdata['module_registermanager_status']=$status;
			
		// 	$this->model_setting_setting->editSetting('module_registermanager',$postdata);
			
		// 	$this->model_setting_setting->editSetting('registermanager', $this->request->post);

		// 	$this->session->data['success'] = $this->language->get('text_success');
			
		// 	/* update work */
		// 	if(isset($this->request->get['status'])) {
		// 	$this->response->redirect($this->url->link('extension/module/registermanager', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		// 	} else {
				
		// 	$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		// 	}
		// 	/* update work */
			
			

		// }

		$data['tmdheading_title'] = $this->language->get('tmdheading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_fieldname'] = $this->language->get('entry_fieldname');
		$data['entry_label'] = $this->language->get('entry_label');
		$data['entry_error'] = $this->language->get('entry_error');
		$data['entry_required'] = $this->language->get('entry_required');
		$data['entry_sort'] = $this->language->get('entry_sort');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_firstname_missing'] = $this->language->get('entry_firstname_missing');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_lastname_missing'] = $this->language->get('entry_lastname_missing');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_email_missing'] = $this->language->get('entry_email_missing');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_address1'] = $this->language->get('entry_address1');
		$data['entry_address2'] = $this->language->get('entry_address2');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_password_error'] = $this->language->get('entry_password_error');
		$data['entry_confirm_password'] = $this->language->get('entry_confirm_password');
		$data['entry_tax_id'] = $this->language->get('entry_tax_id'); //added by jake
		$data['entry_tax_id_missing'] = $this->language->get('entry_tax_id_missing'); //added by jake
		$data['entry_seller_permit'] = $this->language->get('entry_seller_permit'); //added by jake
		$data['entry_seller_permit_missing'] = $this->language->get('entry_seller_permit_missing'); //added by jake
		$data['entry_privacy'] = $this->language->get('entry_privacy');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_submit_button'] = $this->language->get('entry_submit_button');
		$data['entry_email_warning'] = $this->language->get('entry_email_warning');
		$data['entry_email_exists'] = $this->language->get('entry_email_exists');
		$data['entry_privacyautochk'] = $this->language->get('entry_privacyautochk');
		$data['entry_success'] = $this->language->get('entry_success');
		$data['entry_emailtemplate'] = $this->language->get('entry_emailtemplate');
		$data['entry_already'] = $this->language->get('entry_already');
		$data['entry_personal'] = $this->language->get('entry_personal');
		$data['entry_addtitle'] = $this->language->get('entry_addtitle');
		$data['entry_passtitle'] = $this->language->get('entry_passtitle');
		$data['entry_newsletter'] = $this->language->get('entry_newsletter');
		$data['entry_subscribe'] = $this->language->get('entry_subscribe');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_approvedcustomer'] = $this->language->get('entry_approvedcustomer');
		$data['entry_unapprovesuccess'] = $this->language->get('entry_unapprovesuccess');
		$data['entry_enable_disable'] = $this->language->get('entry_enable_disable');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_checkregister'] = $this->language->get('text_checkregister');
		$data['text_guestregister'] = $this->language->get('text_guestregister');
		$data['text_default'] = $this->language->get('text_default');
		$data['button_stay'] = $this->language->get('button_stay');
		
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_language'] = $this->language->get('tab_language');
		$data['tab_success'] = $this->language->get('tab_success');
		$data['tab_email'] = $this->language->get('tab_email');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['error_keynotfound'])) {
				$data['error_keynotfound'] = $this->error['error_keynotfound'];
			} else {
				$data['error_keynotfound'] = '';
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
			'text' => $this->language->get('heading_title1'),
			'href' => $this->url->link('extension/module/registermanager', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/registermanager', 'user_token=' . $this->session->data['user_token'], true);
		/* updatework */
		$data['staysave'] = $this->url->link('extension/module/registermanager', '&status=1&user_token=' . $this->session->data['user_token'], true);
		/* updatework */

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		
		
		$postdata = array();
		
		if(isset($this->request->post['registermanager_register'])){
			$data['registermanager_register'] = $this->request->post['registermanager_register'];
			$postdata['registermanager_register'] = $data['registermanager_register'];
		}else{
			//$data['registermanager_register'] = json_decode($this->config->get('registermanager_register'), true);
			$data['registermanager_register'] = $this->config->get('registermanager_register');
			$postdata['registermanager_register'] = $data['registermanager_register'];

		}
		
		//Register Firstname
		
		if(isset($this->request->post['registermanager_fnamerequired'])){
			$data['registermanager_fnamerequired'] = $this->request->post['registermanager_fnamerequired'];
			$postdata['registermanager_fnamerequired'] = $data['registermanager_fnamerequired'];
		}else{
			$data['registermanager_fnamerequired'] = $this->config->get('registermanager_fnamerequired');
			$postdata['registermanager_fnamerequired'] = $data['registermanager_fnamerequired'];
		}
		
		if(isset($this->request->post['registermanager_fnamesortorder'])){
			$data['registermanager_fnamesortorder'] = $this->request->post['registermanager_fnamesortorder'];
			$postdata['registermanager_fnamesortorder'] = $data['registermanager_fnamesortorder'];
		}else{
			$data['registermanager_fnamesortorder'] = $this->config->get('registermanager_fnamesortorder');
			$postdata['registermanager_fnamesortorder'] = $data['registermanager_fnamesortorder'];
		}
		
		if(isset($this->request->post['registermanager_fnamestatus'])){
			$data['registermanager_fnamestatus'] = $this->request->post['registermanager_fnamestatus'];
			$postdata['registermanager_fnamestatus'] = $data['registermanager_fnamestatus'];
		}else{
			$data['registermanager_fnamestatus'] = $this->config->get('registermanager_fnamestatus');
			$postdata['registermanager_fnamestatus'] = $data['registermanager_fnamestatus'];
		}
		
		
		//Register LastName
		
				
		if(isset($this->request->post['registermanager_lastnamerequired'])){
			$data['registermanager_lastnamerequired'] = $this->request->post['registermanager_lastnamerequired'];
			$postdata['registermanager_lastnamerequired'] = $data['registermanager_lastnamerequired'];
		}else{
			$data['registermanager_lastnamerequired'] = $this->config->get('registermanager_lastnamerequired');
			$postdata['registermanager_lastnamerequired'] = $data['registermanager_lastnamerequired'];
		}
		
		if(isset($this->request->post['registermanager_lastnamesortorder'])){
			$data['registermanager_lastnamesortorder'] = $this->request->post['registermanager_lastnamesortorder'];
			$postdata['registermanager_lastnamesortorder'] = $data['registermanager_lastnamesortorder'];
		}else{
			$data['registermanager_lastnamesortorder'] = $this->config->get('registermanager_lastnamesortorder');
			$postdata['registermanager_lastnamesortorder'] = $data['registermanager_lastnamesortorder'];
		}
		
		if(isset($this->request->post['registermanager_lastnamestatus'])){
			$data['registermanager_lastnamestatus'] = $this->request->post['registermanager_lastnamestatus'];
			$postdata['registermanager_lastnamestatus'] = $data['registermanager_lastnamestatus'];
		}else{
			$data['registermanager_lastnamestatus'] = $this->config->get('registermanager_lastnamestatus');
			$postdata['registermanager_lastnamestatus'] = $data['registermanager_lastnamestatus'];
		}
		
		//Register E-mail Address
		
		
		if(isset($this->request->post['registermanager_emailrequired'])){
			$data['registermanager_emailrequired'] = $this->request->post['registermanager_emailrequired'];
			$postdata['registermanager_emailrequired'] = $data['registermanager_emailrequired'];
		}else{
			$data['registermanager_emailrequired'] = $this->config->get('registermanager_emailrequired');
			$postdata['registermanager_emailrequired'] = $data['registermanager_emailrequired'];
		}
		
		if(isset($this->request->post['registermanager_emailsortorder'])){
			$data['registermanager_emailsortorder'] = $this->request->post['registermanager_emailsortorder'];
			$postdata['registermanager_emailsortorder'] = $data['registermanager_emailsortorder'];
		}else{
			$data['registermanager_emailsortorder'] = $this->config->get('registermanager_emailsortorder');
			$postdata['registermanager_emailsortorder'] = $data['registermanager_emailsortorder'];
		}
		
		if(isset($this->request->post['registermanager_emailstatus'])){
			$data['registermanager_emailstatus'] = $this->request->post['registermanager_emailstatus'];
			$postdata['registermanager_emailstatus'] = $data['registermanager_emailstatus'];
		}else{
			$data['registermanager_emailstatus'] = $this->config->get('registermanager_emailstatus');
			$postdata['registermanager_emailstatus'] = $data['registermanager_emailstatus'];
		}
		
		//Register Telephone
		
		
		if(isset($this->request->post['registermanager_phonerequired'])){
			$data['registermanager_phonerequired'] = $this->request->post['registermanager_phonerequired'];
			$postdata['registermanager_phonerequired'] = $data['registermanager_phonerequired'];
		}else{
			$data['registermanager_phonerequired'] = $this->config->get('registermanager_phonerequired');
			$postdata['registermanager_phonerequired'] = $data['registermanager_phonerequired'];
		}
		
		if(isset($this->request->post['registermanager_phonesortorder'])){
			$data['registermanager_phonesortorder'] = $this->request->post['registermanager_phonesortorder'];
			$postdata['registermanager_phonesortorder'] = $data['registermanager_phonesortorder'];
		}else{
			$data['registermanager_phonesortorder'] = $this->config->get('registermanager_phonesortorder');
			$postdata['registermanager_phonesortorder'] = $data['registermanager_phonesortorder'];
		}
		
		if(isset($this->request->post['registermanager_phonestatus'])){
			$data['registermanager_phonestatus'] = $this->request->post['registermanager_phonestatus'];
			$postdata['registermanager_phonestatus'] = $data['registermanager_phonestatus'];
		}else{
			$data['registermanager_phonestatus'] = $this->config->get('registermanager_phonestatus');
			$postdata['registermanager_phonestatus'] = $data['registermanager_phonestatus'];
		}
		
		
		
		//Register Address 1
		
		
		
		if(isset($this->request->post['registermanager_add1required'])){
			$data['registermanager_add1required'] = $this->request->post['registermanager_add1required'];
			$postdata['registermanager_add1required'] = $data['registermanager_add1required'];
		}else{
			$data['registermanager_add1required'] = $this->config->get('registermanager_add1required');
			$postdata['registermanager_add1required'] = $data['registermanager_add1required'];
		}
		
		if(isset($this->request->post['registermanager_add1sortorder'])){
			$data['registermanager_add1sortorder'] = $this->request->post['registermanager_add1sortorder'];
			$postdata['registermanager_add1sortorder'] = $data['registermanager_add1sortorder'];
		}else{
			$data['registermanager_add1sortorder'] = $this->config->get('registermanager_add1sortorder');
			$postdata['registermanager_add1sortorder'] = $data['registermanager_add1sortorder'];
		}
		
		if(isset($this->request->post['registermanager_add1status'])){
			$data['registermanager_add1status'] = $this->request->post['registermanager_add1status'];
			$postdata['registermanager_add1status'] = $data['registermanager_add1status'];
		}else{
			$data['registermanager_add1status'] = $this->config->get('registermanager_add1status');
			$postdata['registermanager_add1status'] = $data['registermanager_add1status'];
		}
		
		//Register Address 2
		
		
		if(isset($this->request->post['registermanager_add2required'])){
			$data['registermanager_add2required'] = $this->request->post['registermanager_add2required'];
			$postdata['registermanager_add2required'] = $data['registermanager_add2required'];
		}else{
			$data['registermanager_add2required'] = $this->config->get('registermanager_add2required');
			$postdata['registermanager_add2required'] = $data['registermanager_add2required'];
		}
		
		if(isset($this->request->post['registermanager_add2sortorder'])){
			$data['registermanager_add2sortorder'] = $this->request->post['registermanager_add2sortorder'];
			$postdata['registermanager_add2sortorder'] = $data['registermanager_add2sortorder'];
		}else{
			$data['registermanager_add2sortorder'] = $this->config->get('registermanager_add2sortorder');
			$postdata['registermanager_add2sortorder'] = $data['registermanager_add2sortorder'];
		}
		
		if(isset($this->request->post['registermanager_add2status'])){
			$data['registermanager_add2status'] = $this->request->post['registermanager_add2status'];
			$postdata['registermanager_add2status'] = $data['registermanager_add2status'];
		}else{
			$data['registermanager_add2status'] = $this->config->get('registermanager_add2status');
			$postdata['registermanager_add2status'] = $data['registermanager_add2status'];
		}
		
		//Register City
		
		
		if(isset($this->request->post['registermanager_cityrequired'])){
			$data['registermanager_cityrequired'] = $this->request->post['registermanager_cityrequired'];
			$postdata['registermanager_cityrequired'] = $data['registermanager_cityrequired'];
		}else{
			$data['registermanager_cityrequired'] = $this->config->get('registermanager_cityrequired');
			$postdata['registermanager_cityrequired'] = $data['registermanager_cityrequired'];
		}
		
		if(isset($this->request->post['registermanager_citysortorder'])){
			$data['registermanager_citysortorder'] = $this->request->post['registermanager_citysortorder'];
			$postdata['registermanager_citysortorder'] = $data['registermanager_citysortorder'];
		}else{
			$data['registermanager_citysortorder'] = $this->config->get('registermanager_citysortorder');
			$postdata['registermanager_citysortorder'] = $data['registermanager_citysortorder'];
		}
		
		if(isset($this->request->post['registermanager_citystatus'])){
			$data['registermanager_citystatus'] = $this->request->post['registermanager_citystatus'];
			$postdata['registermanager_citystatus'] = $data['registermanager_citystatus'];
		}else{
			$data['registermanager_citystatus'] = $this->config->get('registermanager_citystatus');
			$postdata['registermanager_citystatus'] = $data['registermanager_citystatus'];
		}
		
		//Register Postcode
		
		if(isset($this->request->post['registermanager_postcodrequired'])){
			$data['registermanager_postcodrequired'] = $this->request->post['registermanager_postcodrequired'];
			$postdata['registermanager_postcodrequired'] = $data['registermanager_postcodrequired'];
		}else{
			$data['registermanager_postcodrequired'] = $this->config->get('registermanager_postcodrequired');
			$postdata['registermanager_postcodrequired'] = $data['registermanager_postcodrequired'];
		}
		
		if(isset($this->request->post['registermanager_postcodsortorder'])){
			$data['registermanager_postcodsortorder'] = $this->request->post['registermanager_postcodsortorder'];
			$postdata['registermanager_postcodsortorder'] = $data['registermanager_postcodsortorder'];
		}else{
			$data['registermanager_postcodsortorder'] = $this->config->get('registermanager_postcodsortorder');
			$postdata['registermanager_postcodsortorder'] = $data['registermanager_postcodsortorder'];
		}
		
		if(isset($this->request->post['registermanager_postcodstatus'])){
			$data['registermanager_postcodstatus'] = $this->request->post['registermanager_postcodstatus'];
			$postdata['registermanager_postcodstatus'] = $data['registermanager_postcodstatus'];
		}else{
			$data['registermanager_postcodstatus'] = $this->config->get('registermanager_postcodstatus');
			$postdata['registermanager_postcodstatus'] = $data['registermanager_postcodstatus'];
		}
		
		//Register Country
		
		if(isset($this->request->post['registermanager_countryrequired'])){
			$data['registermanager_countryrequired'] = $this->request->post['registermanager_countryrequired'];
			$postdata['registermanager_countryrequired'] = $data['registermanager_countryrequired'];
		}else{
			$data['registermanager_countryrequired'] = $this->config->get('registermanager_countryrequired');
			$postdata['registermanager_countryrequired'] = $data['registermanager_countryrequired'];
		}
		
		if(isset($this->request->post['registermanager_countrysortorder'])){
			$data['registermanager_countrysortorder'] = $this->request->post['registermanager_countrysortorder'];
			$postdata['registermanager_countrysortorder'] = $data['registermanager_countrysortorder'];
		}else{
			$data['registermanager_countrysortorder'] = $this->config->get('registermanager_countrysortorder');
			$postdata['registermanager_countrysortorder'] = $data['registermanager_countrysortorder'];
		}
		
		if(isset($this->request->post['registermanager_countrystatus'])){
			$data['registermanager_countrystatus'] = $this->request->post['registermanager_countrystatus'];
			$postdata['registermanager_countrystatus'] = $data['registermanager_countrystatus'];
		}else{
			$data['registermanager_countrystatus'] = $this->config->get('registermanager_countrystatus');
			$postdata['registermanager_countrystatus'] = $data['registermanager_countrystatus'];
		}
		
		//Register Zone
		
		
		if(isset($this->request->post['registermanager_zonerequired'])){
			$data['registermanager_zonerequired'] = $this->request->post['registermanager_zonerequired'];
			$postdata['registermanager_zonerequired'] = $data['registermanager_zonerequired'];
		}else{
			$data['registermanager_zonerequired'] = $this->config->get('registermanager_zonerequired');
			$postdata['registermanager_zonerequired'] = $data['registermanager_zonerequired'];
		}
		
		if(isset($this->request->post['registermanager_zonesortorder'])){
			$data['registermanager_zonesortorder'] = $this->request->post['registermanager_zonesortorder'];
			$postdata['registermanager_zonesortorder'] = $data['registermanager_zonesortorder'];
		}else{
			$data['registermanager_zonesortorder'] = $this->config->get('registermanager_zonesortorder');
			$postdata['registermanager_zonesortorder'] = $data['registermanager_zonesortorder'];
		}
		
		if(isset($this->request->post['registermanager_zonestatus'])){
			$data['registermanager_zonestatus'] = $this->request->post['registermanager_zonestatus'];
			$postdata['registermanager_zonestatus'] = $data['registermanager_zonestatus'];
		}else{
			$data['registermanager_zonestatus'] = $this->config->get('registermanager_zonestatus');
			$postdata['registermanager_zonestatus'] = $data['registermanager_zonestatus'];
		}
		
		//Register Password
		
		
		if(isset($this->request->post['registermanager_pwdrequired'])){
			$data['registermanager_pwdrequired'] = $this->request->post['registermanager_pwdrequired'];
			$postdata['registermanager_pwdrequired'] = $data['registermanager_pwdrequired'];
		}else{
			$data['registermanager_pwdrequired'] = $this->config->get('registermanager_pwdrequired');
			$postdata['registermanager_pwdrequired'] = $data['registermanager_pwdrequired'];
		}
		
		if(isset($this->request->post['registermanager_pwdsortorder'])){
			$data['registermanager_pwdsortorder'] = $this->request->post['registermanager_pwdsortorder'];
			$postdata['registermanager_pwdsortorder'] = $data['registermanager_pwdsortorder'];
		}else{
			$data['registermanager_pwdsortorder'] = $this->config->get('registermanager_pwdsortorder');
			$postdata['registermanager_pwdsortorder'] = $data['registermanager_pwdsortorder'];
		}
		
		if(isset($this->request->post['registermanager_pwdstatus'])){
			$data['registermanager_pwdstatus'] = $this->request->post['registermanager_pwdstatus'];
			$postdata['registermanager_pwdstatus'] = $data['registermanager_pwdstatus'];
		}else{
			$data['registermanager_pwdstatus'] = $this->config->get('registermanager_pwdstatus');
			$postdata['registermanager_pwdstatus'] = $data['registermanager_pwdstatus'];
		}
		
		//Register Confirm Password
		
		if(isset($this->request->post['registermanager_cpwdrequired'])){
			$data['registermanager_cpwdrequired'] = $this->request->post['registermanager_cpwdrequired'];
			$postdata['registermanager_cpwdrequired'] = $data['registermanager_cpwdrequired'];
		}else{
			$data['registermanager_cpwdrequired'] = $this->config->get('registermanager_cpwdrequired');
			$postdata['registermanager_cpwdrequired'] = $data['registermanager_cpwdrequired'];
		}
		
		if(isset($this->request->post['registermanager_cpwdsortorder'])){
			$data['registermanager_cpwdsortorder'] = $this->request->post['registermanager_cpwdsortorder'];
			$postdata['registermanager_cpwdsortorder'] = $data['registermanager_cpwdsortorder'];
		}else{
			$data['registermanager_cpwdsortorder'] = $this->config->get('registermanager_cpwdsortorder');
			$postdata['registermanager_cpwdsortorder'] = $data['registermanager_cpwdsortorder'];
		}
		
		if(isset($this->request->post['registermanager_cpwdstatus'])){
			$data['registermanager_cpwdstatus'] = $this->request->post['registermanager_cpwdstatus'];
			$postdata['registermanager_cpwdstatus'] = $data['registermanager_cpwdstatus'];
		}else{
			$data['registermanager_cpwdstatus'] = $this->config->get('registermanager_cpwdstatus');
			$postdata['registermanager_cpwdstatus'] = $data['registermanager_cpwdstatus'];
		}

		//Register Tax Id Added by Jake

		if(isset($this->request->post['registermanager_taxid_required'])){
			$data['registermanager_taxid_required'] = $this->request->post['registermanager_taxid_required'];
			$postdata['registermanager_taxid_required'] = $data['registermanager_taxid_required'];
		}else{
			$data['registermanager_taxid_required'] = $this->config->get('registermanager_taxid_required');
			$postdata['registermanager_taxid_required'] = $data['registermanager_taxid_required'];
		}
		
		if(isset($this->request->post['registermanager_taxidorder'])){
			$data['registermanager_taxidorder'] = $this->request->post['registermanager_taxidorder'];
			$postdata['registermanager_taxidorder'] = $data['registermanager_taxidorder'];
		}else{
			$data['registermanager_taxidorder'] = $this->config->get('registermanager_taxidorder');
			$postdata['registermanager_taxidorder'] = $data['registermanager_taxidorder'];
		}
		
		if(isset($this->request->post['registermanager_taxidstatus'])){
			$data['registermanager_taxidstatus'] = $this->request->post['registermanager_taxidstatus'];
			$postdata['registermanager_taxidstatus'] = $data['registermanager_taxidstatus'];
		}else{
			$data['registermanager_taxidstatus'] = $this->config->get('registermanager_taxidstatus');
			$postdata['registermanager_taxidstatus'] = $data['registermanager_taxidstatus'];
		}

		//Seller Permit Added by Jake

		if(isset($this->request->post['registermanager_sellerpermit_required'])){
			$data['registermanager_sellerpermit_required'] = $this->request->post['registermanager_sellerpermit_required'];
			$postdata['registermanager_sellerpermit_required'] = $data['registermanager_sellerpermit_required'];
		}else{
			$data['registermanager_sellerpermit_required'] = $this->config->get('registermanager_sellerpermit_required');
			$postdata['registermanager_sellerpermit_required'] = $data['registermanager_sellerpermit_required'];
		}
		
		if(isset($this->request->post['registermanager_sellerpermitorder'])){
			$data['registermanager_sellerpermitorder'] = $this->request->post['registermanager_sellerpermitorder'];
			$postdata['registermanager_sellerpermitorder'] = $data['registermanager_sellerpermitorder'];
		}else{
			$data['registermanager_sellerpermitorder'] = $this->config->get('registermanager_sellerpermitorder');
			$postdata['registermanager_sellerpermitorder'] = $data['registermanager_sellerpermitorder'];
		}
		
		if(isset($this->request->post['registermanager_sellerpermitstatus'])){
			$data['registermanager_sellerpermitstatus'] = $this->request->post['registermanager_sellerpermitstatus'];
			$postdata['registermanager_sellerpermitstatus'] = $data['registermanager_sellerpermitstatus'];
		}else{
			$data['registermanager_sellerpermitstatus'] = $this->config->get('registermanager_sellerpermitstatus');
			$postdata['registermanager_sellerpermitstatus'] = $data['registermanager_sellerpermitstatus'];
		}
		
		//Register Privacy Policy
		
		if(isset($this->request->post['registermanager_privacyautochk'])){
			$data['registermanager_privacyautochk'] = $this->request->post['registermanager_privacyautochk'];
			$postdata['registermanager_privacyautochk'] = $data['registermanager_privacyautochk'];
		}else{
			$data['registermanager_privacyautochk'] = $this->config->get('registermanager_privacyautochk');
			$postdata['registermanager_privacyautochk'] = $data['registermanager_privacyautochk'];
		}
		
		if(isset($this->request->post['registermanager_privacyrequired'])){
			$data['registermanager_privacyrequired'] = $this->request->post['registermanager_privacyrequired'];
			$postdata['registermanager_privacyrequired'] = $data['registermanager_privacyrequired'];
		}else{
			$data['registermanager_privacyrequired'] = $this->config->get('registermanager_privacyrequired');
			$postdata['registermanager_privacyrequired'] = $data['registermanager_privacyrequired'];
		}
		
		if(isset($this->request->post['registermanager_privacysortorder'])){
			$data['registermanager_privacysortorder'] = $this->request->post['registermanager_privacysortorder'];
			$postdata['registermanager_privacysortorder'] = $data['registermanager_privacysortorder'];
		}else{
			$data['registermanager_privacysortorder'] = $this->config->get('registermanager_privacysortorder');
			$postdata['registermanager_privacysortorder'] = $data['registermanager_privacysortorder'];
		}
		
		if(isset($this->request->post['registermanager_privacystatus'])){
			$data['registermanager_privacystatus'] = $this->request->post['registermanager_privacystatus'];
			$postdata['registermanager_privacystatus'] = $data['registermanager_privacystatus'];
		}else{
			$data['registermanager_privacystatus'] = $this->config->get('registermanager_privacystatus');
			$postdata['registermanager_privacystatus'] = $data['registermanager_privacystatus'];
		}
		
		//Register subscribers
		
		
		if(isset($this->request->post['registermanager_subscribesortorder'])){
			$data['registermanager_subscribesortorder'] = $this->request->post['registermanager_subscribesortorder'];
			$postdata['registermanager_subscribesortorder'] = $data['registermanager_subscribesortorder'];
		}else{
			$data['registermanager_subscribesortorder'] = $this->config->get('registermanager_subscribesortorder');
			$postdata['registermanager_subscribesortorder'] = $data['registermanager_subscribesortorder'];
		}
		
		if(isset($this->request->post['registermanager_subscribestatus'])){
			$data['registermanager_subscribestatus'] = $this->request->post['registermanager_subscribestatus'];
			$postdata['registermanager_subscribestatus'] = $data['registermanager_subscribestatus'];
		}else{
			$data['registermanager_subscribestatus'] = $this->config->get('registermanager_subscribestatus');
			$postdata['registermanager_subscribestatus'] = $data['registermanager_subscribestatus'];
		}
        
        //Register captcha
		
		if(isset($this->request->post['registermanager_captcharequired'])){
			$data['registermanager_captcharequired'] = $this->request->post['registermanager_captcharequired'];
			$postdata['registermanager_captcharequired'] = $data['registermanager_captcharequired'];
		}else{
			$data['registermanager_captcharequired'] = $this->config->get('registermanager_captcharequired');
			$postdata['registermanager_captcharequired'] = $data['registermanager_captcharequired'];
		}
		
		if(isset($this->request->post['registermanager_captchasortorder'])){
			$data['registermanager_captchasortorder'] = $this->request->post['registermanager_captchasortorder'];
			$postdata['registermanager_captchasortorder'] = $data['registermanager_captchasortorder'];
		}else{
			$data['registermanager_captchasortorder'] = $this->config->get('registermanager_captchasortorder');
			$postdata['registermanager_captchasortorder'] = $data['registermanager_captchasortorder'];
		}
		
		if(isset($this->request->post['registermanager_captchastatus'])){
			$data['registermanager_captchastatus'] = $this->request->post['registermanager_captchastatus'];
			$postdata['registermanager_captchastatus'] = $data['registermanager_captchastatus'];
		}else{
			$data['registermanager_captchastatus'] = $this->config->get('registermanager_captchastatus');
			$postdata['registermanager_captchastatus'] = $data['registermanager_captchastatus'];
		}
		
		if(isset($this->request->post['registermanager_success'])){
			$data['registermanager_success'] = $this->request->post['registermanager_success'];
			$postdata['registermanager_success'] = $data['registermanager_success'];
		}else{
			$data['registermanager_success'] = $this->config->get('registermanager_success');
			$postdata['registermanager_success'] = $data['registermanager_success'];
		}
		
		if(isset($this->request->post['registermanager_title'])){
			$data['registermanager_title'] = $this->request->post['registermanager_title'];
			$postdata['registermanager_title'] = $data['registermanager_title'];
		}else{
			$data['registermanager_title'] = $this->config->get('registermanager_title');
			$postdata['registermanager_title'] = $data['registermanager_title'];
		}
				
		if(isset($this->request->post['registermanager_unapprovesuccess'])){
			$data['registermanager_unapprovesuccess'] = $this->request->post['registermanager_unapprovesuccess'];
			$postdata['registermanager_unapprovesuccess'] = $data['registermanager_unapprovesuccess'];
		}else{
			$data['registermanager_unapprovesuccess'] = $this->config->get('registermanager_unapprovesuccess');
			$postdata['registermanager_unapprovesuccess'] = $data['registermanager_unapprovesuccess'];
		}
		
		if(isset($this->request->post['registermanager_email'])){
			$data['registermanager_email'] = $this->request->post['registermanager_email'];
			$postdata['registermanager_email'] = $data['registermanager_email'];
		}else{
			$data['registermanager_email'] = $this->config->get('registermanager_email');
			$postdata['registermanager_email'] = $data['registermanager_email'];
		}
		
		if(isset($this->request->post['registermanager_subject'])){
			$data['registermanager_subject'] = $this->request->post['registermanager_subject'];
			$postdata['registermanager_subject'] = $data['registermanager_subject'];
		}else{
			$data['registermanager_subject'] = $this->config->get('registermanager_subject');
			$postdata['registermanager_subject'] = $data['registermanager_subject'];
		}
		

		if (isset($this->request->post['registermanager_status'])) {
			$data['registermanager_status'] = $this->request->post['registermanager_status'];
			$postdata['registermanager_status'] = $data['registermanager_status'];
		} else {
			$data['registermanager_status'] = $this->config->get('registermanager_status');
			$postdata['registermanager_status'] = $data['registermanager_status'];
		}
		
		if (isset($this->request->post['registermanager_arstatus'])) {
			$data['registermanager_arstatus'] = $this->request->post['registermanager_arstatus'];
			$postdata['registermanager_arstatus'] = $data['registermanager_arstatus'];
		} else {
			$data['registermanager_arstatus'] = $this->config->get('registermanager_arstatus');
			$postdata['registermanager_arstatus'] = $data['registermanager_arstatus'];
		}
		
		if (isset($this->request->post['registermanager_checkregister'])) {
			$data['registermanager_checkregister'] = $this->request->post['registermanager_checkregister'];
			$postdata['registermanager_checkregister'] = $data['registermanager_checkregister'];
		} else {
			$data['registermanager_checkregister'] = $this->config->get('registermanager_checkregister');
			$postdata['registermanager_checkregister'] = $data['registermanager_checkregister'];
		}
		
		if (isset($this->request->post['registermanager_guestregister'])) {
			$data['registermanager_guestregister'] = $this->request->post['registermanager_guestregister'];
			$postdata['registermanager_guestregister'] = $data['registermanager_guestregister'];
		} else {
			$data['registermanager_guestregister'] = $this->config->get('registermanager_guestregister');
			$postdata['registermanager_guestregister'] = $data['registermanager_guestregister'];
		}

		$this->model_setting_setting->editSetting('registermanager', $postdata);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/registermanager', $data));
	}

	// protected function validate() {
	// 	if (!$this->user->hasPermission('modify', 'extension/module/registermanager')) {
	// 		$this->error['warning'] = $this->language->get('error_permission');
	// 	}
		
	// 	$key=$this->config->get('moduledata_registermanager_key');
	// 		if (empty(trim($key))) {			
	// 			 $this->error['warning'] ='Module will Work after add License key!';
	// 		}

	// 	return !$this->error;
	// }
}
