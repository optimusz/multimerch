<?php

class MsHelper extends Model {
	public function getSortParams($sorts, $colMap, $defCol = false, $defWay = false) {
		if (isset($this->request->get['iSortCol_0'])) {
			if (isset($this->request->get['mDataProp_' . $this->request->get['iSortCol_0']])) {
				$sortCol = $this->request->get['mDataProp_' . $this->request->get['iSortCol_0']];
			} else {
				$sortCol = $defCol ? $defCol : $sorts[0];
			}
		} else {
			$sortCol = $defCol ? $defCol : $sorts[0];
		}
		
		if (!in_array($sortCol, $sorts)) {
			$sortCol = $defCol ? $defCol : $sorts[0];
		}
		
		$sortCol = isset($colMap[$sortCol]) ? $colMap[$sortCol] : $sortCol; 
		
		if (isset($this->request->get['sSortDir_0'])) {
			$sortDir = $this->request->get['sSortDir_0'] == 'desc' ? "DESC" : "ASC";
		} else {
			$sortDir = $defWay ? $defWay : "ASC";
		}
		
		return array($sortCol, $sortDir);
	}
	
	public function getFilterParams($filters, $colMap) {
		$filterParams = array();
		for ($col=0; $col < $this->request->get['iColumns']; $col++) {
			if (isset($this->request->get['sSearch_' .$col])) {
				$colName = $this->request->get['mDataProp_' . $col];
				$filterVal = $this->request->get['sSearch_' .$col];
				if (!empty($filterVal) && in_array($colName, $filters)) {
					$colName = isset($colMap[$colName]) ? $colMap[$colName] : $colName;
					$filterParams[$colName] = $this->request->get['sSearch_' .$col];
				}
			}
		}
		
		return $filterParams;
	}	
	
	public function setBreadcrumbs($data) {
		$breadcrumbs = array();
		
		$breadcrumbs[] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'SSL'),
        	'separator' => false
      	);
		
		foreach ($data as $breadcrumb) {
	      	$breadcrumbs[] = array(
	        	'text'      => $breadcrumb['text'],
				'href'      => $breadcrumb['href'],
	        	'separator' => $this->language->get('text_separator')
	      	);
		}
		
		return $breadcrumbs;
	}
	
	public function admSetBreadcrumbs($data) {
		$breadcrumbs = array();
		
		$breadcrumbs[] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        	'separator' => false
      	);
		
		foreach ($data as $breadcrumb) {
	      	$breadcrumbs[] = array(
	        	'text'      => $breadcrumb['text'],
				'href'      => $breadcrumb['href'] . '&token=' . $this->session->data['token'],
	        	'separator' => $this->language->get('text_separator')
	      	);
		}
		
		return $breadcrumbs;
	}	
	
	public function loadTemplate($templateName, $children = FALSE) {
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . "/template/multiseller/$templateName.tpl")) {
			$template = $this->config->get('config_template') . "/template/multiseller/$templateName.tpl";
		} else {
			$template = "default/template/multiseller/$templateName.tpl";
		}
		
		if ($children === FALSE) {
			$children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
		}
	
		return array($template, $children);
	}

	public function admLoadTemplate($templateName, $children = FALSE) {
		// ugly
		if(strpos($templateName, '/') !== false)
			$template = "$templateName.tpl";
		else
			$template = "multiseller/$templateName.tpl";
		
		if ($children === FALSE) {
			$children = array(
				'common/footer',
				'common/header'
			);
		}
	
		return array($template, $children);
	}
	
	public function addStyle($style) {
		if (file_exists("catalog/view/theme/" . $this->config->get('config_template') . "/stylesheet/{$style}.css")) {
			$this->document->addStyle("catalog/view/theme/" . $this->config->get('config_template') . "/stylesheet/{$style}.css");
		} else {
			$this->document->addStyle("catalog/view/theme/default/stylesheet/{$style}.css");
		}
	}
	
	public function getLanguageId($code) {
		$res = $this->db->query("SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = '" . $code . "'");
		
		return $res->row['language_id'];
	}

    public function getStockStatuses($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

            $sql .= " ORDER BY name";

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $stock_status_data = $this->cache->get('stock_status.' . (int)$this->config->get('config_language_id'));

            if (!$stock_status_data) {
                $query = $this->db->query("SELECT stock_status_id, name FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

                $stock_status_data = $query->rows;

                $this->cache->set('stock_status.' . (int)$this->config->get('config_language_id'), $stock_status_data);
            }

            return $stock_status_data;
        }
    }

    public function getTaxClasses($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "tax_class";

            $sql .= " ORDER BY title";

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $tax_class_data = $this->cache->get('tax_class');

            if (!$tax_class_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_class");

                $tax_class_data = $query->rows;

                $this->cache->set('tax_class', $tax_class_data);
            }

            return $tax_class_data;
        }
    }
}

?>