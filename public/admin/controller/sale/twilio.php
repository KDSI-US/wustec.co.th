<?php

require_once(str_replace("/public", "", $_SERVER['DOCUMENT_ROOT']) . '/storage/vendor/twilio/sdk/src/Twilio/autoload.php');

use Twilio\Rest\Client;

class ControllerSaleTwilio extends Controller {

	public function index() {
    
    $json = array();
    
    $this->load->model('setting/setting');

    $data = array();
    $data['config_twilio_sid'] = $this->model_setting_setting->getSettingValue('config_twilio_sid');
    $data['config_twilio_token'] = $this->model_setting_setting->getSettingValue('config_twilio_token');
    $data['config_twilio_number'] = $this->model_setting_setting->getSettingValue('config_twilio_number');
    
    if (isset($this->request->post['tracking_number'])) {
      $data['tracking_number'] = $this->request->post['tracking_number'];
    } 

    if (isset($this->request->post['telephone'])) {
      $customer_number = $this->request->post['telephone'];
    } else {
      $customer_number = "";
    }

    if (isset($this->request->post['firstname'])) {
      if (trim($this->request->post['firstname']) == "") {
        $data['customer_firstname'] = "there";
      } else {
        $data['customer_firstname'] = $this->request->post['firstname'];
      }
    } else {
      $data['customer_firstname'] = "there";
    }
    

    // format customer_number
    $customer_number = filter_var($customer_number, FILTER_SANITIZE_NUMBER_INT);
    $customer_number = str_replace("+", "", $customer_number);
    $customer_number = str_replace("-", "", $customer_number);
    
    if (strlen($customer_number) == 10) {
      $data['customer_number'] = "+1" . $customer_number;
    }
    elseif (strlen($customer_number) == 11) {
      if (substr($customer_number, 0, 1) == "1") {
        $data['customer_number'] = "+" . $customer_number;
      } else {
        $json['error'] = "Telephone number is not valid";
      }     
    }
    else {
      $json['error'] = "Telephone number is not valid";
    }

    if (isset($data['tracking_number']) && isset($data['customer_number']) && isset($this->request->post['sendSMS'])) {
      if (!$this->sendSMS($data)) {
        $json['error'] = "SMS message failed to send";
      } else {
        $json['success'] = "SMS message has been sent";
      }
    }
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

  public function sendSMS($data)
  {
    // Use the REST API Client to make requests to the Twilio REST API
    // Your Account SID and Auth Token from twilio.com/console
    $sid =  $data['config_twilio_sid'];
    $token = $data['config_twilio_token'];
    // $client = new Client($username = 'hr@decoyinc.com', $password = 'Cap3llaA3288EV$&.', $accountSid = $sid);
    $client = new Client($sid, $token);

    // A Twilio number you own with SMS capabilities
    $twilio_number = $data['config_twilio_number'];

    try {
      $message = $client->messages->create(
        // Where to send a text message (your cell phone?)
        $data['customer_number'],
        array(
          // A Twilio phone number you purchased at twilio.com/console
          'from' => $twilio_number,
          "messagingServiceSid" => "MG5fb7d0e4e65e9b19a8e0806df184fa1c", 
          // the body of the text message you'd like to send
          'body' => "Hi " . $data['customer_firstname'] . "! " . "Your order from Capella Apparel is now ready! Tracking number is " . $data['tracking_number'] . "."
        )
      );
    }
    catch (Exception $e) {
      return false;
    }

    return true;
  }
}