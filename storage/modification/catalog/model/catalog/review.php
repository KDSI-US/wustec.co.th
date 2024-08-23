<?php
/* This file is under Git Control by KDSI. */
class ModelCatalogReview extends Model {
	public function addReview($product_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['name']) . "', customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', text = '" . $this->db->escape($data['text']) . "', rating = '" . (int)$data['rating'] . "', date_added = NOW()");

		$review_id = $this->db->getLastId();

		if (in_array('review', (array)$this->config->get('config_mail_alert'))) {
			$this->load->language('mail/review');
			$this->load->model('catalog/product');
			
			$product_info = $this->model_catalog_product->getProduct($product_id);

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$message  = $this->language->get('text_waiting') . "\n";
			$message .= sprintf($this->language->get('text_product'), html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_reviewer'), html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_rating'), $data['rating']) . "\n";
			$message .= $this->language->get('text_review') . "\n";
			$message .= html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";

			// Prepare mail: product.review
			$this->load->model('extension/module/emailtemplate');

			$template_load = array('key' => 'product.review');

			$template = $this->model_extension_module_emailtemplate->load($template_load);

            if ($template) {
                $template->addData($data, 'review');

                $template->addData($product_info, 'product');

                if (defined('HTTP_ADMIN')) {
                    $admin_url = HTTP_ADMIN;
                } elseif ($this->config->get('config_ssl')) {
                    $admin_url = $this->config->get('config_ssl') . 'admin/';
                } else {
                    $admin_url = $this->config->get('config_url') . 'admin/';
                }

                $template->data['review_approve'] = $admin_url . 'index.php?route=catalog/review/edit&review_id=' . $review_id;

				if ($this->language->get('button_review_approve') && $this->language->get('button_review_approve') != 'button_review_approve') {
					$template->data['review_approve_text'] =  $this->language->get('button_review_approve');
				} else {
					$template->data['review_approve_text'] =  $template->data['review_approve'];
				}

                $template->data['customer_link'] = $admin_url . 'index.php?route=customer/customer/edit&filter_name=' . urlencode($data['name']);

                if ($this->language->get('button_customer_link') && $this->language->get('button_customer_link') != 'button_customer_link') {
                    $template->data['customer_link_text'] =  $this->language->get('button_customer_link');
                } else {
                    $template->data['customer_link_text'] =  $template->data['customer_link'];
                }

				$template->data['product_link'] = $this->url->link('product/product', 'product_id=' . $product_id);
			    // Prepared mail: product.review
		
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($message);
			// Send mail: product.review
			    if ($template && $template->check()) {
			    	$template->build();
			    	$template->hook($mail);

					if ($this->customer && $this->customer->isLogged()) {
						$mail->setReplyTo($this->customer->getEmail());
					}

					$mail->send();

					$this->model_extension_module_emailtemplate->sent();
                }
            }

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_mail_alert_email'));

			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}
	}

	public function getReviewsByProductId($product_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReviewsByProductId($product_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
}