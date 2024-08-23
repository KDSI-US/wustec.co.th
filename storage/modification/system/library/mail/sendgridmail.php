<?php
/* This file is under Git Control by KDSI. */
namespace Mail;

class SendGridMail {
    
	public $smtp_password;
	
    public function send() {
        
		$to = array();
		
		if (is_array($this->to)) {
			$this_to = $this->to;
		} else {
			$this_to = explode(",",$this->to);		
		}

		foreach ($this_to as $mail_to){
			$to[] = array('email' => trim($mail_to));
		}	
		
        $textorder = array("\r\n", "\n", "\r", PHP_EOL);

        $mailtext = str_replace($textorder, "<br/>", $this->text);

        $message = isset($this->html) ? $this->html : $mailtext;
        
		if (!$this->reply_to) {
			$reply_to = $this->from;
		} else {
			$reply_to = $this->reply_to;
		}		
		
		$personalizations = Array(
            "personalizations" => Array(
                0 => Array(
                    "to" => $to,
                    "subject" => $this->subject),
            ),
            "from" => Array(
                "email" => $this->from,
                "name" => $this->sender
            ),
            "reply_to" => Array(
                "email" => $reply_to
            ),
            "subject" => $this->subject,
            "content" => Array(
                0 => Array(
                    "type" => "text/html",
                    "value" => $message
                ))
        );
		
		if (is_array($this->attachments) && count($this->attachments) > 0){
			$personalizations['attachments'] = array();
			foreach ($this->attachments as $attachment) {
				if (file_exists($attachment)) {
					
					$attach_me = array();
					
					$handle = fopen($attachment, 'r');

					$content = fread($handle, filesize($attachment));

					fclose($handle);

					$attach_me['content'] = base64_encode($content);
					$attach_me['filename'] = basename($attachment);
					
					$personalizations['attachments'][] = $attach_me;

				}
			}
		}
		
		//For debug
		//echo json_encode($personalizations, JSON_PRETTY_PRINT);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com/v3/mail/send");
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($personalizations));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		
		//If problems with the site's CA (like in a dev environment) you can uncomment this: Fixes curl SSL error.
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
		
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: Bearer ". $this->smtp_password,"content-type: application/json"));
		
		$postResponse = curl_exec($ch);
        $err = curl_error($ch);
		
        curl_close($ch);
        
        if ($err) {
            echo "cURL Error #: " . $err;
        } else {
            echo $postResponse;
        }
    }
}
?>