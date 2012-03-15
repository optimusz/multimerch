<?php
class ModelModuleMultisellerSeller extends Model {
	public function getProduct($product_id, $seller_id) {
		$sql = "SELECT 	p.price,
						p.product_id,
						pd.name as name,
						pd.description as description,
						ptc.category_id,
						group_concat(pt.tag separator ', ') as tags
				FROM `" . DB_PREFIX . "product` p
				INNER JOIN `" . DB_PREFIX . "product_description` pd
					ON p.product_id = pd.product_id
				INNER JOIN `" . DB_PREFIX . "product_to_category` ptc
					ON p.product_id = ptc.product_id
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON pd.product_id = mp.product_id
				LEFT JOIN `" . DB_PREFIX . "product_tag` pt
					ON mp.product_id = pt.product_id
				WHERE p.product_id = " . (int)$product_id . " 
				AND mp.seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		var_dump($sql);
		return $res->row;
	}
		
	public function getProductStatusArray() {
		$this->load->language('module/multiseller');
		return array(
			MS_PRODUCT_STATUS_DRAFT => $this->language->get('ms_product_review_status_draft'),
			MS_PRODUCT_STATUS_PENDING => $this->language->get('ms_product_review_status_pending'),
			MS_PRODUCT_STATUS_APPROVED => $this->language->get('ms_product_review_status_approved'),
			MS_PRODUCT_STATUS_DECLINED => $this->language->get('ms_product_review_status_declined'),
		);		
	}
	
	public function nicknameTaken($nickname) {
		$sql = "SELECT nickname
				FROM `" . DB_PREFIX . "ms_seller` p
				WHERE p.nickname = '" . $this->db->escape($nickname) . "'";
		
		$res = $this->db->query($sql);
		
		return $res->num_rows;
	}
	
	public function getCategories($parent_id = 0) {
		//$category_data = $this->cache->get('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id);
		$category_data = FALSE;
		
		if (!$category_data) {
			$category_data = array();
		
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
			foreach ($query->rows as $result) {
				$category_data[] = array(
					'category_id' => $result['category_id'],
					'name'        => $result['name'],
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				);
			
				$category_data = array_merge($category_data, $this->getCategories($result['category_id']));
			}
	
			$this->cache->set('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$parent_id, $category_data);
		}
		
		return $category_data;
	}

	public function getTotalSellerProducts($seller_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_product` p
				WHERE p.seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];		
	}
	
	public function getSellerProducts($seller_id, $sort) {
		$sql = "SELECT c.product_id, name, date_added, status as status_id, number_sold, review_status_id 
				FROM `" . DB_PREFIX . "product_description` a
				INNER JOIN `" . DB_PREFIX . "product` b
					ON a.product_id = b.product_id 
				INNER JOIN `" . DB_PREFIX . "ms_product` c
					ON b.product_id = c.product_id
				WHERE c.seller_id = " . (int)$seller_id . "
        		ORDER BY {$sort['order_by']} {$sort['order_way']}" 
        		. ($sort['limit'] ? " LIMIT ".(int)(($sort['page'] - 1) * $sort['limit']).', '.(int)($sort['limit']) : '');				
		
		$res = $this->db->query($sql);
		
		
		//TODO
		$review_statuses = $this->getProductStatusArray();
		
		foreach ($res->rows as &$row) {
			$row['review_status'] = $review_statuses[$row['review_status_id']];
			$row['status'] = $row['status_id'] ? $this->language->get('text_yes') : $this->language->get('text_no');
		}
		
		return $res->rows;
	}
	
	public function saveProduct($product_id) {
	}
	
	public function saveSellerData($data) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_seller
				SET seller_id = " . (int)$this->customer->getId() . ",
					seller_status_id = " . (int)$data['seller_status_id'] . ",
					commission = " . (float)$this->config->get('msconf_seller_commission') . ",
					nickname = '" . $this->db->escape($data['sellerinfo_nickname']) . "',
					description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					avatar_path = '" . $this->db->escape($data['avatar_path']) . "',
					date_created = NOW()";
		
		//$this->db->query($sql);
		
		
		$message = sprintf($this->language->get('ms_mail_greeting'), $this->customer->getFirstName()) . "\n\n";
		$message .= sprintf($this->language->get('ms_account_sellerinfo_mail_account_thankyou'), $this->config->get('config_name')) . "\n\n";		
		
		$v = $this->config->get('msconf_seller_validation');
		$v = 2;
		switch ($v) {
			// activation link
			case MS_SELLER_VALIDATION_ACTIVATION:
				$subject = sprintf($this->language->get('ms_account_sellerinfo_mail_account_pleaseactivate_subject'), $this->config->get('config_name'));
				$message .= sprintf($this->language->get('ms_account_sellerinfo_mail_account_pleaseactivate_message'), $this->config->get('config_name')) . "\n\n";
				$message .= 'http://dolboeb.eu/' . "\n\n";
				break;
				
			// manual approval
			case MS_SELLER_VALIDATION_APPROVAL:
				$subject = sprintf($this->language->get('ms_account_sellerinfo_mail_account_needsapproval_subject'), $this->config->get('config_name'));
				$message .= sprintf($this->language->get('ms_account_sellerinfo_mail_account_needsapproval_message'), $this->config->get('config_name')) . "\n\n";			
				break;
				
			// no validation
			case MS_SELLER_VALIDATION_NONE:
			default:
				$subject = sprintf($this->language->get('ms_account_sellerinfo_mail_account_created_subject'), $this->config->get('config_name'));
				$message .= sprintf($this->language->get('ms_account_sellerinfo_mail_account_created_message'), $this->config->get('config_name')) . "\n\n";
				break;								
		}
		
		$message .= sprintf($this->language->get('ms_mail_regards'), HTTP_SERVER) . "\n" . $this->config->get('config_name');

		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');				
		$mail->setTo($this->customer->getEmail());
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject($subject);
		$mail->setText($message);
		$mail->send();
		
		/*
		// Send to main admin email if new account email is enabled
		if ($this->config->get('config_account_mail')) {
			$mail->setTo($this->config->get('config_email'));
			$mail->send();
			
			// Send to additional alert emails if new account email is enabled
			$emails = explode(',', $this->config->get('config_alert_emails'));
			
			foreach ($emails as $email) {
				if (strlen($email) > 0 && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}
		*/
	}
}