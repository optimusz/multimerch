<?php
class ModelModuleMultisellerSeller extends Model {
	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->rows;
	}

	public function getProductDownloads($product_id) {
		$sql = "SELECT 	*
				FROM `" . DB_PREFIX . "download` d
				LEFT JOIN `" . DB_PREFIX . "product_to_download` pd
					USING(download_id)
				WHERE pd.product_id = " . (int)$product_id;
		$res = $this->db->query($sql);
				
		return $res->rows;
	}

	public function getProductThumbnail($product_id) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->row;
	}

	public function getSellerAvatar($seller_id) {
		$query = $this->db->query("SELECT avatar_path as avatar FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
		
		return $query->row;
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
						ptc.category_id,
						mp.review_status_id
				FROM `" . DB_PREFIX . "product` p
				INNER JOIN `" . DB_PREFIX . "product_to_category` ptc
					ON p.product_id = ptc.product_id
				INNER JOIN `" . DB_PREFIX . "ms_product` mp
					ON ptc.product_id = mp.product_id
				WHERE p.product_id = " . (int)$product_id . " 
				AND mp.seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);

		
		$sql = "SELECT pd.*,
					   group_concat(pt.tag separator ', ') as tags
				FROM " . DB_PREFIX . "product_description pd
				LEFT JOIN `" . DB_PREFIX . "product_tag` pt
					ON pd.product_id = pt.product_id
					AND pd.language_id = pt.language_id
				WHERE pd.product_id = " . (int)$product_id . "
				GROUP BY language_id";

		$descriptions = $this->db->query($sql);
		$product_description_data = array();
		foreach ($descriptions->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'tags'      => $result['tags'],
				//'meta_keyword'     => $result['meta_keyword'],
				//'meta_description' => $result['meta_description']
			);
		}

		$res->row['languages'] = $product_description_data;

		return $res->row;
	}
		
	public function getBalanceForSeller($seller_id) {
		$sql = "SELECT SUM(amount - (amount*commission/100)) as total
				FROM `" . DB_PREFIX . "ms_transaction`
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];
	}
	
	public function getReservedAmount($seller_id) {
		$sql = "SELECT SUM(amount - (amount*commission/100)) as total
				FROM `" . DB_PREFIX . "ms_transaction`
				WHERE seller_id = " . (int)$seller_id . "
				AND type = " . MS_TRANSACTION_WITHDRAWAL . ";
				AND transaction_status_id = " . MS_TRANSACTION_STATUS_PENDING;
		
		$res = $this->db->query($sql);
		
		return $res->row['total'];
	}	
		
	public function getSellerIdByProduct($product_id) {
		$sql = "SELECT seller_id FROM " . DB_PREFIX . "ms_product
				WHERE product_id = " . (int)$product_id;
				
		$res = $this->db->query($sql);
		return $res->row['seller_id'];
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
				AND a.language_id = " . $this->config->get('config_language_id'). "
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
	
	public function getSellerData($seller_id) {
		$sql = "SELECT * 
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row;
	}	
	
	public function editProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$product_id = $data['product_id'];

		$old_thumbnail = $this->getProductThumbnail($product_id);
		
		if (!isset($data['product_thumbnail_name']) || ($old_thumbnail['image'] != $data['product_thumbnail_name'])) {
			$image = MsImage::byName($this->registry, $old_thumbnail['image']);
			$image->delete('I');				
		}
		
		if (isset($data['product_thumbnail_name'])) {
			$image = MsImage::byName($this->registry, $data['product_thumbnail_name']);
			$image->move('I');
			$thumbnail = $image->getName();
		} else {
			$thumbnail = '';
		}

		$sql = "UPDATE " . DB_PREFIX . "product
				SET price = " . (float)$data['product_price'] . ",
					status = " . (int)$data['enabled'] . ",
					image = '" . $this->db->escape($thumbnail) . "',
					date_modified = NOW()
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);


		$sql = "DELETE FROM " . DB_PREFIX . "product_tag
				WHERE product_id = " . (int)$product_id;
		$this->db->query($sql);

		foreach ($data['languages'] as $language_id => $language) {
			$sql = "UPDATE " . DB_PREFIX . "product_description
					SET name = '". $this->db->escape($language['product_name']) ."',
						description = '". $this->db->escape($language['product_description']) ."'
					WHERE product_id = " . (int)$product_id . "
					AND language_id = " . (int)$language_id;
					
			$this->db->query($sql);
			
			if ($language['product_tags']) {
				$tags = explode(',', $language['product_tags']);
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
				}
			}
		}		
		
		$sql = "UPDATE " . DB_PREFIX . "ms_product
				SET review_status_id = " . (int)$data['review_status_id'] . "
				WHERE product_id = " . (int)$product_id; 
		
		$this->db->query($sql);
		
		$sql = "UPDATE " . DB_PREFIX . "product_to_category
				SET category_id = " . (int)$data['product_category'] . "
				WHERE product_id = " . (int)$product_id;
		
		$this->db->query($sql);		

		// delete old images		
		$old_images = $this->getProductImages($product_id);
		foreach($old_images as $old_image) {
			if (!isset($data['product_images']) || array_search($old_image['image'], $data['product_images']) === FALSE) {
				$image = MsImage::byName($this->registry, $old_image['image']);
				$image->delete('I');				
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		
		// add new images
		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $key => $product_image) {
				$image = MsImage::byName($this->registry, $product_image);
				$image->move('I');				
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($image->getName(), ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}		

		$old_downloads = $this->getProductDownloads($product_id);
		foreach($old_downloads as $old_download) {
			if (!isset($data['product_downloads']) || array_search($old_download['filename'], $data['product_downloads']) === FALSE) {
				$file = MsImage::byName($this->registry, $old_download['filename']);
				$file->delete('F');
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id IN (SELECT download_id FROM " . DB_PREFIX . "product_to_download WHERE product_id ='" . (int)$product_id . "')");
		$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id IN (SELECT download_id FROM " . DB_PREFIX . "product_to_download WHERE product_id ='" . (int)$product_id . "')");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");		
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				$image = MsImage::byName($this->registry, $dl);
				$image->move('F');
				$this->db->query("INSERT INTO " . DB_PREFIX . "download SET filename = '" . $this->db->escape($image->getName()) . "', mask = '" . $this->db->escape(substr($image->getName(),0,strrpos($image->getName(),'.'))) . "'");
				$download_id = $this->db->getLastId();
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				
				foreach ($data['languages'] as $language_id => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape($image->getName()) . "', language_id = '" . (int)$language_id . "'");
				}
			}
		}
		
		$this->cache->delete('product');
		
		return $product_id;		
	}	
	
	public function saveProduct($data) {
		reset($data['languages']); $first = key($data['languages']);
		$store_id = $this->config->get('config_store_id');

		if (isset($data['product_thumbnail_name'])) {
			$image = MsImage::byName($this->registry, $data['product_thumbnail_name']);
			$image->move('I');
			$thumbnail = $image->getName();
		} else {
			$thumbnail = '';
		}

		$sql = "INSERT INTO " . DB_PREFIX . "product
				SET price = " . (float)$data['product_price'] . ",
					model = '".$this->db->escape($data['languages'][$first]['product_name']) ."',
					image = '" .  $this->db->escape($thumbnail)  . "',
					subtract = 0,
					quantity = 1,
					shipping = 0,
					status = " . (int)$data['enabled'] . ",
					date_available = NOW(),				
					date_added = NOW(),
					date_modified = NOW()";
		
		$this->db->query($sql);
		$product_id = $this->db->getLastId();

		foreach ($data['languages'] as $language_id => $language) {
			$sql = "INSERT INTO " . DB_PREFIX . "product_description
					SET product_id = " . (int)$product_id . ",
						name = '". $this->db->escape($language['product_name']) ."',
						description = '". $this->db->escape($language['product_description']) ."',
						language_id = " . (int)$language_id;
			$this->db->query($sql);
			
			if ($language['product_tags']) {
				$tags = explode(',', $language['product_tags']);
					
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
				}
			}
		}
		
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


		if (isset($data['product_images'])) {
			foreach ($data['product_images'] as $key => $img) {
				$image = MsImage::byName($this->registry, $img);
				$image->move('I');
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($image->getName(), ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$key . "'");
			}
		}
		
		if (isset($data['product_downloads'])) {
			foreach ($data['product_downloads'] as $key => $dl) {
				$image = MsImage::byName($this->registry, $dl);
				$image->move('F');
				$this->db->query("INSERT INTO " . DB_PREFIX . "download SET filename = '" . $this->db->escape($image->getName()) . "', mask = '" . $this->db->escape(substr($image->getName(),0,strrpos($image->getName(),'.'))) . "'");
				$download_id = $this->db->getLastId();
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
				
				foreach ($data['languages'] as $language_id => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', name = '" . $this->db->escape(substr($image->getName(),0,strrpos($image->getName(),'.'))) . "', language_id = '" . (int)$language_id . "'");
				}
			}
		}
		
		$this->cache->delete('product');
		
		return $product_id;
	}
	
	public function createSeller($data) {
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
	
	public function editSeller($data) {
		$seller_id = (int)$data['seller_id'];

		$old_avatar = $this->getSellerAvatar($seller_id);
		
		if (!isset($data['sellerinfo_avatar_name']) || ($old_avatar['avatar'] != $data['sellerinfo_avatar_name'])) {
			$image = MsImage::byName($this->registry, $old_avatar['avatar']);
			$image->delete('I');				
		}
		
		if (isset($data['sellerinfo_avatar_name'])) {
			$image = MsImage::byName($this->registry, $data['sellerinfo_avatar_name']);
			$image->move('I');
			$avatar = $image->getName();
		} else {
			$avatar = '';
		}

		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET description = '" . $this->db->escape($data['sellerinfo_description']) . "',
					company = '" . $this->db->escape($data['sellerinfo_company']) . "',
					country_id = " . (int)$data['sellerinfo_country'] . ",
					paypal = '" . $this->db->escape($data['sellerinfo_paypal']) . "',
					avatar_path = '" . $avatar . "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);	
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