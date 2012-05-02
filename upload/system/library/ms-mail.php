<?php
class MsMail extends Mail {
	const SMT_SELLER_ACCOUNT_CREATED = 1;
	const SMT_SELLER_ACCOUNT_AWAITING_MODERATION = 2;
	const SMT_SELLER_ACCOUNT_APPROVED = 3;
	const SMT_SELLER_ACCOUNT_DECLINED = 4;
	const SMT_SELLER_ACCOUNT_DISABLED = 17;
	
	const SMT_PRODUCT_CREATED = 5;
	const SMT_PRODUCT_AWAITING_MODERATION = 6;
	const SMT_PRODUCT_APPROVED = 7;
	const SMT_PRODUCT_DECLINED = 8;
	const SMT_PRODUCT_ENABLED = 9;
	const SMT_PRODUCT_DISABLED = 10;
	
	const SMT_PRODUCT_PURCHASED = 11;
	
	const SMT_WITHDRAW_REQUEST_SUBMITTED = 12;
	const SMT_WITHDRAW_REQUEST_COMPLETED = 13;
	const SMT_WITHDRAW_REQUEST_DECLINED = 14;
	const SMT_WITHDRAW_PERFORMED = 15;
	
	const SMT_TRANSACTION_PERFORMED = 16;
	
	//
	
	const AMT_SELLER_ACCOUNT_CREATED = 101;
	const AMT_SELLER_ACCOUNT_AWAITING_MODERATION = 102;
	
	const AMT_PRODUCT_CREATED = 103;
	const AMT_NEW_PRODUCT_AWAITING_MODERATION = 104;
	const AMT_EDIT_PRODUCT_AWAITING_MODERATION = 105;
	
	const AMT_PRODUCT_PURCHASED = 106;
	
	const AMT_WITHDRAW_REQUEST_SUBMITTED = 107;
	const AMT_WITHDRAW_REQUEST_COMPLETED = 108;
	
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->registry = $registry;
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->language = $registry->get('language');
		$this->errors = array();
		
		require_once(DIR_SYSTEM . 'library/ms-product.php');
		require_once(DIR_SYSTEM . 'library/ms-seller.php');
		$this->msSeller = new MsSeller($registry);
		$this->msProduct = new MsProduct($registry);		
	}

	private function _getRecipients($mail_type) {
		if ($mail_type < 100)
			return $this->registry->get('customer')->getEmail();
		else
			return $this->config->get('config_email');
	}

	//TODO
	private function _getAddressee($mail_type) {
		if ($mail_type < 100)
			return $this->registry->get('customer')->getFirstname();
		else
			return '';//$this->registry->get('customer')->getFirstname();
	}
	
	private function _getOrderProducts($order_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order_product
				WHERE order_id = " . (int)$order_id;
		
		$res = $this->db->query($sql);

		return $res->rows;
	}

	public function sendOrderMails($order_id) {
		$order_products = $this->_getOrderProducts($order_id);
		
		if (!$order_products)
			return false;
			
		$mails = array();
		foreach ($order_products as $product) {
			$seller_id = $this->msProduct->getSellerId($product['product_id']);
			
			if ($seller_id) {
				$mails[] = array(
					'type' => MsMail::SMT_PRODUCT_PURCHASED,
					'data' => array(
						'recipients' => $this->msSeller->getSellerEmail($seller_id),
						'addressee' => $this->msSeller->getSellerName($seller_id),
						'product_id' => $product['product_id']
					)
				);
			}
		}
		
		$this->sendMails($mails);
	}
	
	public function sendMails($mails) {
		foreach ($mails as $mail) {
			if (!isset($mail['data'])) {
				$this->sendMail($mail['type']);
			} else {
				$this->sendMail($mail['type'], $mail['data']);
			}
		}
	}
	
	public function sendMail($mail_type, $data = array()) {
		if (isset($data['product_id'])) {
			$product = $this->msProduct->getProduct($data['product_id']);
			$n = reset($product['languages']);
			$product['name'] = $n['name'];
		}
		
		//$message .= sprintf($this->language->get('ms_mail_regards'), HTTP_SERVER) . "\n" . $this->config->get('config_name');

		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');
						
		if (!isset($data['recipients'])) {
			$mail->setTo($this->_getRecipients($mail_type));
		} else {
			$mail->setTo($data['recipients']);
		}
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		
		if (!isset($data['addressee'])) {
			$mail_text = 	sprintf($this->language->get('ms_mail_greeting'), $this->_getAddressee($mail_type));
		} else {
			$mail_text = 	sprintf($this->language->get('ms_mail_greeting'), $data['addressee']);			
		}
		$mail_subject = '['.$this->config->get('config_name').'] ';
		
		//$mail_type = self::SMT_TRANSACTION_PERFORMED;
		
		// main switch
		switch($mail_type) {
			// seller
			case self::SMT_SELLER_ACCOUNT_CREATED:
				$mail_subject .= $this->language->get('ms_mail_subject_seller_account_created');
				$mail_text .= sprintf($this->language->get('ms_mail_seller_account_created'), $this->config->get('config_name'));
				break;
			case self::SMT_SELLER_ACCOUNT_AWAITING_MODERATION:
				$mail_subject .= $this->language->get('ms_mail_subject_seller_account_awaiting_moderation');
				$mail_text .= sprintf($this->language->get('ms_mail_seller_account_awaiting_moderation'), $this->config->get('config_name'));
				break;
			case self::SMT_SELLER_ACCOUNT_APPROVED:
				$mail_subject .= $this->language->get('ms_mail_subject_seller_account_approved');
				$mail_text .= sprintf($this->language->get('ms_mail_seller_account_approved'), $this->config->get('config_name'));
				break;
			case self::SMT_SELLER_ACCOUNT_DECLINED:
				$mail_subject .= $this->language->get('ms_mail_subject_seller_account_declined');
				$mail_text .= sprintf($this->language->get('ms_mail_seller_account_declined'), $this->config->get('config_name'), $data['message']);
				break;
			case self::SMT_SELLER_ACCOUNT_DISABLED:
				$mail_subject .= $this->language->get('ms_mail_subject_seller_account_disabled');
				$mail_text .= sprintf($this->language->get('ms_mail_seller_account_disabled'), $this->config->get('config_name'), $data['message']);
				break;

				
			case self::SMT_PRODUCT_AWAITING_MODERATION:
				$mail_subject .= $this->language->get('ms_mail_subject_product_awaiting_moderation');
				$mail_text .= sprintf($this->language->get('ms_mail_product_awaiting_moderation'), $product['name'], $this->config->get('config_name'));
				break;
			case self::SMT_PRODUCT_APPROVED:
				$mail_subject .= $this->language->get('ms_mail_subject_product_approved');
				$mail_text .= sprintf($this->language->get('ms_mail_product_approved'), $product['name'], $this->config->get('config_name'));
				break;
			case self::SMT_PRODUCT_DECLINED:
				$mail_subject .= $this->language->get('ms_mail_subject_product_declined');
				$mail_text .= sprintf($this->language->get('ms_mail_product_declined'), $product['name'], $this->config->get('config_name'), $data['message']);
				break;
			case self::SMT_PRODUCT_ENABLED:
				$mail_subject .= $this->language->get('ms_mail_subject_product_enabled');
				$mail_text .= sprintf($this->language->get('ms_mail_product_enabled'), $product['name'], $this->config->get('config_name'), $data['message']);
				break;
			case self::SMT_PRODUCT_DISABLED:
				$mail_subject .= $this->language->get('ms_mail_subject_product_disabled');
				$mail_text .= sprintf($this->language->get('ms_mail_product_disabled'), $product['name'], $this->config->get('config_name'), $data['message']);
				break;
			
			case self::SMT_PRODUCT_PURCHASED:
				$mail_subject .= $this->language->get('ms_mail_subject_product_purchased');
				$mail_text .= sprintf($this->language->get('ms_mail_product_purchased'), $product['name'], $this->config->get('config_name'));
				break;				
			
			case self::SMT_WITHDRAW_REQUEST_SUBMITTED:
				$mail_subject .= $this->language->get('ms_mail_subject_withdraw_request_submitted');
				$mail_text .= sprintf($this->language->get('ms_mail_withdraw_request_submitted'));
				break;
			case self::SMT_WITHDRAW_REQUEST_COMPLETED:
				$mail_subject .= $this->language->get('ms_mail_subject_withdraw_request_completed');
				$mail_text .= sprintf($this->language->get('ms_mail_withdraw_request_completed'));
				break;
			case self::SMT_WITHDRAW_REQUEST_DECLINED:
				$mail_subject .= $this->language->get('ms_mail_subject_withdraw_request_declined');
				$mail_text .= sprintf($this->language->get('ms_mail_withdraw_request_declined'), $this->config->get('config_name'), $data['message']);
				break;
			/*
			case self::SMT_WITHDRAW_PERFORMED:
				$mail_subject .= $this->language->get('ms_mail_subject_withdraw_performed');
				$mail_text .= sprintf($this->language->get('ms_mail_withdraw_performed'), $this->config->get('config_name'), $data['message']);
				break;
			*/
			case self::SMT_TRANSACTION_PERFORMED:
				$mail_subject .= $this->language->get('ms_mail_subject_transaction_performed');
				$mail_text .= sprintf($this->language->get('ms_mail_transaction_performed'), $this->config->get('config_name'));
				break;
				
			// admin
			case self::AMT_PRODUCT_CREATED:
				$mail_subject .= $this->language->get('ms_mail_admin_subject_product_created');
				$mail_text .= sprintf($this->language->get('ms_mail_admin_product_created'), $product['name'], $this->config->get('config_name'));
				break;
			
			case self::AMT_SELLER_ACCOUNT_CREATED:
				$mail_subject .= $this->language->get('ms_mail_admin_subject_seller_account_created');
				$mail_text .= sprintf($this->language->get('ms_mail_admin_seller_account_created'), $this->config->get('config_name'));
				break;
			case self::AMT_SELLER_ACCOUNT_AWAITING_MODERATION:
				$mail_subject .= $this->language->get('ms_mail_admin_subject_seller_account_awaiting_moderation');
				$mail_text .= sprintf($this->language->get('ms_mail_admin_seller_account_awaiting_moderation'), $this->config->get('config_name'));
				break;
				
			case self::AMT_NEW_PRODUCT_AWAITING_MODERATION:
				$mail_subject .= $this->language->get('ms_mail_admin_subject_new_product_awaiting_moderation');
				$mail_text .= sprintf($this->language->get('ms_mail_admin_new_product_awaiting_moderation'), $product['name'], $this->config->get('config_name'), $data['message']);
				break;

			case self::AMT_EDIT_PRODUCT_AWAITING_MODERATION:
				$mail_subject .= $this->language->get('ms_mail_admin_subject_edit_product_awaiting_moderation');
				$mail_text .= sprintf($this->language->get('ms_mail_admin_edit_product_awaiting_moderation'), $product['name'], $this->config->get('config_name'), $data['message']);
				break;
			
			case self::AMT_WITHDRAW_REQUEST_SUBMITTED:
				$mail_subject .= $this->language->get('ms_mail_admin_subject_withdraw_request_submitted');
				$mail_text .= sprintf($this->language->get('ms_mail_admin_withdraw_request_submitted'));
				break;

			default:
				break;
		}

		$mail_text .= sprintf($this->language->get('ms_mail_ending'), $this->config->get('config_name'));

		$mail->setSubject($mail_subject);
		$mail->setText($mail_text);
		$mail->send();
	}
}
?>