<?php

class MsHelper extends Model {
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
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . "/template/module/multiseller/$templateName.tpl")) {
			$template = $this->config->get('config_template') . "/template/module/multiseller/$templateName.tpl";
		} else {
			$template = "default/template/module/multiseller/$templateName.tpl";
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
		$template = "module/multiseller/$templateName.tpl";
		
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
}

?>