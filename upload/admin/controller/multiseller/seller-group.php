<?php

class ControllerMultisellerSellerGroup extends ControllerMultisellerBase {
	
	private $error = array();
	
	// List all the seller groups
	public function index() {
		$this->validate(__FUNCTION__);
		
		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'msgd.name';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		$url = '';
		
		$url .= isset($this->request->get['sort']) ? '&sort=' . $this->request->get['sort'] : '';
		$url .= isset($this->request->get['order']) ? '&order=' . $this->request->get['order'] : '';
		$url .= isset($this->request->get['page']) ? '&page=' . $this->request->get['page'] : '';
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_seller_groups_breadcrumbs'),
				'href' => $this->url->link('multiseller/seller-group', '', 'SSL'),
			)
		));
		
		$this->data['insert'] = $this->url->link('multiseller/seller-group/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('multiseller/seller-group/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
	
		$this->data['seller_groups'] = array();
		
		$sort_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$total_seller_groups = $this->MsLoader->MsSellerGroup->getTotalSellerGroups();
		$results = $this->MsLoader->MsSellerGroup->getSellerGroups($sort_data);

		
		
		foreach ($results as $result) {
			/*
			$rates = $this->MsLoader->MsCommission->getCommissionRates($result['commission_id']);
			$own_fees = '';
			foreach ($rates as $rate) {
				$own_fees .= '<span class="fee-rate-' . $rate['rate_type'] . '"><b>' . $this->language->get('ms_commission_short_' . $rate['rate_type']) . ':</b>' . ($rate['percent'] ? $rate['percent'] . '%' : '') . ($rate['percent'] && $rate['flat'] ? '+' : '') .  ($rate['flat'] ? $this->currency->getSymbolLeft() .  $this->currency->format($rate['flat'], $this->config->get('config_currency'), '', FALSE) . $this->currency->getSymbolRight() : '') . '&nbsp;&nbsp;</span>';
			}
			*/
			$rates = $this->MsLoader->MsCommission->calculateCommission(array('seller_group_id' => $result['seller_group_id']));
			$actual_fees = '';
			foreach ($rates as $rate) {
				$actual_fees .= '<span class="fee-rate-' . $rate['rate_type'] . '"><b>' . $this->language->get('ms_commission_short_' . $rate['rate_type']) . ':</b>' . $rate['percent'] . '%+' . $this->currency->getSymbolLeft() .  $this->currency->format($rate['flat'], $this->config->get('config_currency'), '', FALSE) . $this->currency->getSymbolRight() . '&nbsp;&nbsp;';
			}
			
			$this->data['seller_groups'][] = array(
				'seller_group_id' => $result['seller_group_id'],
				'name'              => $result['name'],
				'description' => (mb_strlen($result['description']) > 80 ? mb_substr($result['description'], 0, 80) . '...' : $result['description']),
				'selected'          => isset($this->request->post['selected']) && in_array($result['seller_group_id'], $this->request->post['selected']),
				//'own_fees' => $own_fees,
				'actual_fees' => $actual_fees
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $total_seller_groups;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("multiseller/seller-group", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_seller_groups_heading');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->document->setTitle($this->language->get('ms_catalog_seller_groups_heading'));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('seller-group'); 
		$this->response->setOutput($this->render());
	}
	
	// Insert a new seller group
	public function insert() {
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_insert_seller_group_heading');
		$this->document->setTitle($this->language->get('ms_catalog_insert_seller_group_heading'));
		
		$this->data['cancel'] = $this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$this->data['seller_group'] = NULL;
		/*
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->MsLoader->MsSellerGroup->saveSellerGroup($this->request->post);
			
			$this->session->data['success'] = $this->language->get('ms_success');

			$this->redirect($this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		*/
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_seller_groups_breadcrumbs'),
				'href' => $this->url->link('multiseller/seller-group', '', 'SSL'),
			)
		));		
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('seller-group-form'); 
		$this->response->setOutput($this->render());
	}
	
	// Update a seller group
	public function update() {
		$this->validate(__FUNCTION__);
		
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_edit_seller_group_heading');
		$this->document->setTitle($this->language->get('ms_catalog_edit_seller_group_heading'));
		
		$this->data['cancel'] = $this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'], 'SSL'); //'multiseller/seller-group';
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$seller_group = $this->MsLoader->MsSellerGroup->getSellerGroup($this->request->get['seller_group_id']);

		if (is_null($seller_group['commission_id']))
			$rates = NULL;
		else
			$rates = $this->MsLoader->MsCommission->getCommissionRates($seller_group['msg.commission_id']);
		
		$this->data['seller_group'] = array(
			'seller_group_id' => $seller_group['seller_group_id'],
			'description' => $this->MsLoader->MsSellerGroup->getSellerGroupDescriptions($this->request->get['seller_group_id']),
			'commission_id' => $seller_group['commission_id'],
			'commission_rates' => $rates
		);
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_seller_groups_breadcrumbs'),
				'href' => $this->url->link('multiseller/seller-group', '', 'SSL'),
			)
		));		
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('seller-group-form'); 
		$this->response->setOutput($this->render());		
		
		/*
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->MsLoader->MsSellerGroup->editSellerGroup($this->request->get['seller_group_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('ms_success');
			
			$this->redirect($this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		$this->getEditForm();
		*/
	}
	
	// Bulk delete of seller groups
	public function delete() { 
		if (isset($this->request->get['seller_group_id'])) $this->request->post['selected'] = array($this->request->get['seller_group_id']);
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $seller_group_id) {
				$this->MsLoader->MsSellerGroup->deleteSellerGroup($seller_group_id);
			}
			
			$this->session->data['success'] = $this->language->get('ms_success');
		}
		
		$this->redirect($this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	// Get form for adding/editing seller groups
	private function getEditForm() {
		$this->data['heading'] = $this->language->get('ms_catalog_insert_seller_group_heading');
		
		if (!isset($this->request->get['seller_group_id'])) {
			$this->data['action'] = $this->url->link('multiseller/seller-group/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('multiseller/seller-group/update', 'token=' . $this->session->data['token'] . '&seller_group_id=' . $this->request->get['seller_group_id'] . $url, 'SSL');
		}
		  
    	$this->data['cancel'] = $this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		if (isset($this->request->get['seller_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			//$seller_group_info = $this->MsLoader->MsSellerGroup->getSellerGroup($this->request->get['seller_group_id']);
		}
		
		//$this->MsLoader->MsSellerGroup->getSellerGroupDescriptions($this->request->get['seller_group_id']);

		
		if (isset($this->request->post['seller_group_description'])) {
			$this->data['seller_group_description'] = $this->request->post['seller_group_description'];
		} elseif (isset($this->request->get['seller_group_id'])) {
			$this->data['seller_group_description'] = 'a';
		} else {
			$this->data['seller_group_description'] = array();
		}
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('seller-group-form'); 
		$this->response->setOutput($this->render());
	}
	
	private function validateForm() {
	}
	
	// Validate delete of the seller group
	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'multiseller/seller-group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
      	
		foreach ($this->request->post['selected'] as $seller_group_id) {
    		if ($this->config->get('msconf_default_seller_group_id') == $seller_group_id) {
	  			$this->error['warning'] = $this->language->get('ms_error_seller_group_default');
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function jxSave() {
		$data = $this->request->post['seller_group'];
		$json = array();

		foreach ($data['description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$json['errors']['name_' . $language_id] = $this->language->get('ms_error_seller_group_name');
			}
		}

		if (!empty($data['seller_group_id']) && $this->config->get('msconf_default_seller_group_id') == $data['seller_group_id']) {
			foreach ($data['commission_rates'] as &$rate) {
				if (empty($rate['flat'])) $rate['flat'] = 0;
				if (empty($rate['percent'])) $rate['percent'] = 0;
				if (!isset($rate['payment_method']) || (int)$rate['payment_method'] == 0) $rate['payment_method'] = 1;
			}
			unset($rate);
		}

		if (empty($json['errors'])) {
			if (empty($data['seller_group_id'])) {
				$this->MsLoader->MsSellerGroup->createSellerGroup($data);
				$this->session->data['success'] = $this->language->get('ms_success_seller_group_created');
			} else {
				$this->MsLoader->MsSellerGroup->editSellerGroup($data['seller_group_id'], $data);
				$this->session->data['success'] = $this->language->get('ms_success_seller_group_updated');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>
