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
			  
		$this->db->query($sql);			  
    }    
    

}