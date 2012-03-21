<?php
class ModelModuleMultisellerSeller extends Model {
	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->rows;
	}	
	
	public function getStatsForProduct($product_id) {
		$sql = "SELECT 	p.date_added,
						mp.seller_id,
						mp.number_sold as sales,
						ms.nickname,
						ms.country_id,
						ms.avatar_path
				FROM `" . DB_PREFIX . "product` p
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON p.product_id = mp.product_id
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON mp.seller_id = ms.seller_id
				WHERE p.product_id = " . (int)$product_id; 

		$res = $this->db->query($sql);

		return $res->row;		
	}
	
	public function getCommissionForSeller($seller_id) {
		$sql = "SELECT 	commission
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id; 

		$res = $this->db->query($sql);

		return $res->row['commission'];		
	}	
	
	public function getProduct($product_id, $seller_id) {
		$sql = "SELECT 	p.price,
						p.product_id,
						p.status as enabled,
						p.image as thumbnail,
						pd.name as name,
						pd.description as description,
						ptc.category_id,
						mp.review_status_id,
						group_concat(pt.tag separator ', ') as tags
				FROM `" . DB_PREFIX . "product` p
				INNER JOIN `" . DB_PREFIX . "product_description` pd
					ON p.product_id = pd.product_id
				INNER JOIN `" . DB_PREFIX . "product_to_category` ptc
					ON pd.product_id = ptc.product_id
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON ptc.product_id = mp.product_id
				LEFT JOIN `" . DB_PREFIX . "product_tag` pt
					ON mp.product_id = pt.product_id
				WHERE p.product_id = " . (int)$product_id . " 
				AND mp.seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);

		return $res->row;
	}
		
	public function getBalanceForSeller($seller_id) {
		$sql = "SELECT SUM(amount) as total
				FROM `" . DB_PREFIX . "ms_transaction`
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];
	}
		
	public function getSellerIdByProduct($product_id) {
		$sql = "SELECT seller_id FROM " . DB_PREFIX . "ms_product
				WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);
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

	public function getOrderStatusArray($language_id) {
		$order_statuses = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = " . (int)$language_id);

		$result = array();		
		foreach ($order_statuses->rows as $status) {
			$result[$status['order_status_id']] = $status['name'];
			
		}
		
		return $result;
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
		
		
		$review_statuses = $this->getProductStatusArray();
		foreach ($res->rows as &$row) {
			$row['review_status'] = $review_statuses[$row['review_status_id']];
			$row['status'] = $row['status_id'] ? $this->language->get('text_yes') : $this->language->get('text_no');
		}
		
		return $res->rows;
	}
	
	public function editProduct($data) {
		$language_id = 1;		
		$product_id = $data['product_id'];

		$sql = "UPDATE " . DB_PREFIX . "product
				SET price = " . (float)$data['product_price'] . ",
					status = " . (int)$data['enabled'] . ",
					image = '" . $this->db->escape($data['product_thumbnail_path']) . "',
					date_modified = NOW()
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);

		$sql = "UPDATE " . DB_PREFIX . "product_description
				SET name = '". $this->db->escape($data['product_name']) ."',
					description = '". $this->db->escape($data['product_description']) ."'
				WHERE product_id = " . (int)$product_id . "
				AND language_id = " . (int)$language_id;
				
		$this->db->query($sql);
		
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET review_status_id = " . (int)$data['review_status_id'] . "
				WHERE product_id = " . (int)$product_id; 
		
		$this->db->query($sql);
		
		$sql = "UPDATE " . DB_PREFIX . "product_to_category
				SET category_id = " . (int)$data['product_category'] . "
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);		

		$sql = "DELETE FROM " . DB_PREFIX . "product_tag
				WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);

		if ($data['product_tags']) {
			$tags = explode(',', $data['product_tags']);
				
			foreach ($tags as $tag) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
			}
		}
		
		$this->cache->delete('product');
	}	
	
	public function saveProduct($data) {
		$language_id = 1;		
		$store_id = $this->config->get('config_store_id');


		$sql = "INSERT INTO " . DB_PREFIX . "product
				SET price = " . (float)$data['product_price'] . ",
					model = '".$this->db->escape($data['product_name']) ."',
					image = '" . $this->db->escape($data['product_thumbnail_path']) . "',
					subtract = 0,
					quantity = 1,
					shipping = 0,
					status = " . (int)$data['enabled'] . ",
					date_available = NOW(),				
					date_added = NOW(),
					date_modified = NOW()";
		
		$this->db->query($sql);
		$product_id = $this->db->getLastId();

		$sql = "INSERT INTO " . DB_PREFIX . "product_description
				SET product_id = " . (int)$product_id . ",
					name = '". $this->db->escape($data['product_name']) ."',
					description = '". $this->db->escape($data['product_description']) ."',
					language_id = " . (int)$language_id;
		$this->db->query($sql);
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_product
				SET product_id = " . (int)$product_id . ",
					seller_id = " . (int)$this->customer->getId() . ",
					review_status_id = " . (int)$data['review_status_id'];
		
		$this->db->query($sql);
		
		$sql = "INSERT INTO " . DB_PREFIX . "product_to_category
				SET product_id = " . (int)$product_id . ",
					category_id = " . (int)$data['product_category'];
		
		$this->db->query($sql);		

		$sql = "INSERT INTO " . DB_PREFIX . "product_to_store
				SET product_id = " . (int)$product_id . ",
					store_id = " . (int)$store_id;
		
		$this->db->query($sql);

		if ($data['product_tags']) {
			$tags = explode(',', $data['product_tags']);
				
			foreach ($tags as $tag) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $key => $image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($image['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}
		
		$this->cache->delete('product');
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
		
		$this->db->query($sql);
		
		
		$message = sprintf($this->language->get('ms_mail_greeting'), $this->customer->getFirstName()) . "\n\n";
		$message .= sprintf($this->language->get('ms_account_sellerinfo_mail_account_thankyou'), $this->config->get('config_name')) . "\n\n";		
		
		$v = $this->config->get('msconf_seller_validation');

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
	
	public function productOwnedBySeller($product_id, $seller_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_product`
				WHERE seller_id = " . (int)$seller_id . " 
				AND product_id = " . (int)$product_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];			
	}
	
	public function deleteProduct($product_id) {
		
		$sql = "DELETE FROM " . DB_PREFIX . "ms_product
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);

		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id='" . (int)$product_id. "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
		
		$this->cache->delete('product');		

		/*
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
		*/
	}	
	
}