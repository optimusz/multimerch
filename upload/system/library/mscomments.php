<?php
class MsComments extends Model {
	public function getTotalComments($data = array()) {
		$sql = "SELECT count(*) AS total
				FROM `" . DB_PREFIX . "ms_comments` mc
				WHERE 1 = 1"
				. (isset($data['displayed']) ? " AND mc.display = 1" : '')
				. (isset($data['seller_id']) ? " AND mc.seller_id = " . (int)$data['seller_id'] : '')
				. (isset($data['product_id']) ? " AND mc.product_id = " . (int)$data['product_id'] : '');

		$res = $this->db->query($sql);
		return $res->row['total'];
	}	

	public function getComments($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*,"
					
					// additional columns
					. (isset($cols['product_name']) ? "
						(SELECT name FROM " . DB_PREFIX . "product_description pd
						WHERE product_id = mc.product_id
						AND language_id = " . (int)$this->config->get('config_language_id') . ") as product_name,
					" : "")
					
					."1
				FROM " . DB_PREFIX . "ms_comments mc
				WHERE 1 = 1 "

			. (isset($data['displayed']) ? " AND mc.display = 1" : '')
			. (isset($data['product_id']) ? " AND mc.product_id = " . (int)$data['product_id'] : '')
			. (isset($data['seller_id']) ? " AND mc.seller_id = " . (int)$data['seller_id'] : '')
			
			. $wFilters
			
			. " GROUP BY mc.id HAVING 1 = 1 "
			
			. $hFilters
			
			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		foreach ($res->rows as &$row)  {
			$row['comment'] = htmlspecialchars_decode($row['comment']);
		}

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		return $res->rows;
	}

	public function getSellerProductComments($data = array(), $sort = array()) {
		$sql = "SELECT  *
				FROM `" . DB_PREFIX . "ms_comments` mc
				WHERE 1 = 1 "
				. (isset($data['displayed']) ? " AND mc.display = 1" : '') . "
				AND mc.product_id IN (
					SELECT product_id FROM `" . DB_PREFIX . "ms_product`
                    WHERE seller_id = " . (int)$data['seller_id'] . "
                    AND product_status = " . MsProduct::STATUS_ACTIVE . "
				) OR (mc.product_id = 0 AND seller_id = " . (int)$data['seller_id'] . ")"
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		foreach ($res->rows as &$row)  {
			$row['comment'] = htmlspecialchars_decode($row['comment']);
		}

		return $res->rows;
	}
	
	public function addComment($comment) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_comments`
				(customer_id, user_id, parent_id, product_id, seller_id, name, email, comment, display, create_time)
		  		VALUES(" 
			  		. (isset($comment["customer_id"]) ?  (int)$comment["customer_id"] : 'NULL') . ','
			  		. (isset($comment["user_id"]) ?  (int)$comment["user_id"] : 'NULL') . ','
			  		. (isset($comment["parent_id"]) ?  (int)$comment["parent_id"] : 'NULL') . ','
			  		. (isset($comment["product_id"]) ?  (int)$comment["product_id"] : 'NULL') . ','
			  		. (isset($comment["seller_id"]) ?  (int)$comment["seller_id"] : 'NULL') . ','
			  		. "'" . $this->db->escape($comment["name"]) . "',"
			  		. "'" . $this->db->escape($comment["email"]) . "',"
			  		. "'" . $this->db->escape($comment["comment"]) . "',
					1,
			  		UNIX_TIMESTAMP(NOW())
				)";

		if ($res = $this->db->query($sql)) {
			if (filter_var($this->config->get('pcconf_email'), FILTER_VALIDATE_EMAIL)) {
				$this->load->model('catalog/product');
				$prod = $this->model_catalog_product->getProduct($comment["product_id"]);
				$product_name = strip_tags(htmlspecialchars_decode($prod['name']));
				$comment['comment'] = htmlspecialchars_decode($comment['comment']);

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: ' . $this->config->get('config_email') . "\r\n";
				$headers .= 'Reply-To: ' . $comment["email"];
				
				$to = $this->config->get('pcconf_email');
				$baseurl = $this->config->get('config_url');
				$subject = sprintf($this->language->get('pc_mail_subject'), $this->config->get('config_name'), $product_name);
				$message = sprintf($this->language->get('pc_mail'), "<a href='" . $this->url->link('catalog/product', 'product_id=' . $prod['product_id'], 'SSL') . "'>$product_name</a>", $comment['name'], $comment['email'], nl2br($comment['comment']), $this->url->link("module/ms_comments", 'SSL'));
				return mail($to, $subject, nl2br($message),$headers);
			}
		};
	}
	
	public function deleteComment($comment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_comments WHERE id = " . (int)$comment_id);
	}
}
?>