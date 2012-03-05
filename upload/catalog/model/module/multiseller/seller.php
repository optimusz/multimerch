<?php
class ModelModuleMultisellerSeller extends Model {
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
		
	public function getSellerProducts($seller_id) {
		$sql = "SELECT name, date_added, status, number_sold, review_status 
				FROM `" . DB_PREFIX . "product_description` a
				INNER JOIN `" . DB_PREFIX . "product` b
					ON a.product_id = b.product_id 
				INNER JOIN `" . DB_PREFIX . "ms_product` c
					ON b.product_id = c.product_id
				WHERE c.seller_id = " . (int)$seller_id;
		
		return $res->rows;
	}
	
	public function saveProduct($product_id) {
	}
	
    public function getComments($prodId, $displayedOnly = false, $p = false, $n = false) {
        $sql = '
        	SELECT * FROM `' . DB_PREFIX . 'productcomments` q'
        	. ' WHERE id_product = ' . (int)$prodId
        	. ($displayedOnly ? ' AND q.`display` = 1' : '')
        	. ' ORDER BY create_time DESC '        	
        	. ($n ? ' LIMIT '.(int)(($p - 1) * $n).', '.(int)($n) : '');

        if (!$res = $this->db->query($sql))
        	return $this->showErrors();
        
        // decode needed since oc encodes requests by default 
        foreach ($res->rows as &$row)  {
        	$row['comment'] = htmlspecialchars_decode($row['comment']);        	
        }
        
        return $res->rows;
    }

    public function getCommentsCount($prodId) {
        $sql = '
        	SELECT count(*) as cnt
        	FROM ' . DB_PREFIX . 'productcomments q'
        	. ' WHERE id_product = ' . (int)$prodId
        	. ' AND q.`display` = 1';

        if (!$res = $this->db->query($sql))
        	return $this->showErrors();
        
        return $res->row['cnt'];
    }

    public function showErrors() {
        $sql = 'SHOW ERRORS';
        $err = $this->db->query($sql);
        
        foreach ($err as $e) {
			echo "<div class='error'>Error: {$e['Message']}</div>";
		}
		return false;
    }
    
    public function addComment($comment) {
        $sql = '
        	INSERT INTO `' . DB_PREFIX . 'productcomments`
            (`id_customer`, `name`, `email`, `comment`, `create_time`, `display`, `id_product`)
          	VALUES("'
			  . (int)($comment["id_customer"])
			  . '","' . $this->db->escape($comment["name"])
			  . '","' . $this->db->escape($comment["email"])
			  . '","' . $this->db->escape($comment["comment"])
              . '",UNIX_TIMESTAMP(NOW()) ,
              1,'
			  . (int)($comment["id_product"]) . ')';
              
		$this->load->model('catalog/product');
		$prod = $this->model_catalog_product->getProduct($comment["id_product"]);              
              
		if ($res = $this->db->query($sql)) {
        	if ($this->config->get('productcomments_conf_email') != '') {
    		    $comment['comment'] = htmlspecialchars_decode($comment['comment']);
		                		
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: ' . $this->config->get('config_email') . "\r\n";
				$headers .= 'Reply-To: ' . $comment["email"];
				
				$to = $this->config->get('productcomments_conf_email');
				$baseurl = $this->config->get('config_url');
				$subject = "OpenCart: New comment has been added!";
				$message = "New comment has been submitted through Product Comments module for product <a href='{$baseurl}index.php?route=product/product&product_id={$comment['id_product']}'>{$prod['name']}</a>:";
				$message .= "<br /><br />Name:<br />";
				$message .= htmlspecialchars($comment["name"]);
				$message .= "<br /><br />Email:<br />";
				$message .= htmlspecialchars($comment["email"]);								
				$message .= "<br /><br />Comment:<br />";
				$message .= nl2br(htmlspecialchars($comment["comment"]));
				$message .= "<br /><br />You can view all comments for this product here: <a href='{$baseurl}index.php?route=product/product&product_id={$comment['id_product']}'>{$baseurl}index.php?route=product/product&product_id={$comment['id_product']}</a>";
				$message .= "<br /><br />Comments can be edited or deleted through your store's back-end: <a href='" . HTTP_ADMIN . "'>" . HTTP_ADMIN . "</a>";

				return mail($to, $subject, $message,$headers);
        	}
        };
    }    
    

}