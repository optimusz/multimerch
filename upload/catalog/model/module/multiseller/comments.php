<?php
class ModelModuleMultisellerComments extends Model {
    public function getComments($prodId, $displayedOnly = false, $p = false, $n = false) {
        $sql = '
        	SELECT * FROM `' . DB_PREFIX . 'ms_comments` q'
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
        	FROM ' . DB_PREFIX . 'ms_comments q'
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
        	INSERT INTO `' . DB_PREFIX . 'ms_comments`
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
              
        /*
		if ($res = $this->db->query($sql)) {
        	if ($this->config->get('ms_comments_conf_email') != '') {
    		    $comment['comment'] = htmlspecialchars_decode($comment['comment']);
		                		
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: ' . $this->config->get('config_email') . "\r\n";
				$headers .= 'Reply-To: ' . $comment["email"];
				
				$to = $this->config->get('productcommentsconf_email');
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
        */
    }    
    

}