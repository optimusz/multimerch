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
			$actions = array();
			
			$actions[] = array(
				'text' => $this->language->get('ms_edit'),
				'href' => $this->url->link('multiseller/seller-group/update', 'token=' . $this->session->data['token'] . '&seller_group_id=' . $result['seller_group_id'] . $url, 'SSL')
			);
			
			/*$actions[] = array(
				'text' => $this->language->get('ms_delete'),
				'href' => $this->url->link('multiseller/seller-group/delete', 'token=' . $this->session->data['token'] . '&seller_group_id=' . $result['seller_group_id'] . $url, 'SSL')
			);*/
		
			$this->data['seller_groups'][] = array(
				'seller_group_id' => $result['seller_group_id'],
				'name'              => $result['name'],
				'selected'          => isset($this->request->post['selected']) && in_array($result['customer_group_id'], $this->request->post['selected']),
				'action'            => $actions
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
		$this->document->setTitle($this->language->get('ms_catalog_insert_seller_group_heading'));
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->MsLoader->MsSellerGroup->saveSellerGroup($this->request->post);
			
			$this->session->data['success'] = $this->language->get('ms_success');

			$url = '';

			$url .= isset($this->request->get['sort']) ? '&sort=' . $this->request->get['sort'] : '';
			$url .= isset($this->request->get['order']) ? '&order=' . $this->request->get['order'] : '';
			$url .= isset($this->request->get['page']) ? '&page=' . $this->request->get['page'] : '';
			
			$this->redirect($this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getEditForm();
	}
	
	// Update a seller group
	public function update() {
		$this->validate(__FUNCTION__);
		
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_edit_seller_group_heading');
		$this->document->setTitle($this->language->get('ms_catalog_edit_seller_group_heading'));
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->MsLoader->MsSellerGroup->editSellerGroup($this->request->get['seller_group_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('ms_success');
			
			$url = '';

			$url .= isset($this->request->get['sort']) ? '&sort=' . $this->request->get['sort'] : '';
			$url .= isset($this->request->get['order']) ? '&order=' . $this->request->get['order'] : '';
			$url .= isset($this->request->get['page']) ? '&page=' . $this->request->get['page'] : '';
			
			$this->redirect($this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		$this->getEditForm();
	}
	
	// Bulk delete of seller groups
	public function delete() { 
		$this->load->language('sale/customer_group');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/customer_group');
		
		if ($this->user->hasPermission('modify', 'multiseller/seller-group')) {
			if (isset($this->request->post['selected'])) {
				foreach ($this->request->post['selected'] as $seller_group_id) {
					$this->MsLoader->MsSellerGroup->deleteSellerGroup($seller_group_id);
				}
				
				$this->session->data['success'] = $this->language->get('ms_success');
				
				$url = '';
				
				$url .= isset($this->request->get['sort']) ? '&sort=' . $this->request->get['sort'] : '';
				$url .= isset($this->request->get['order']) ? '&order=' . $this->request->get['order'] : '';
				$url .= isset($this->request->get['page']) ? '&page=' . $this->request->get['page'] : '';
				
				$this->redirect($this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}
		else {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$this->index();
	}
	
	// Get form for adding/editing seller groups
	private function getEditForm() {
		$this->data['heading'] = $this->language->get('ms_catalog_insert_seller_group_heading');
				
		$this->data['entry_name'] = $this->language->get('ms_seller_group_entry_name');
		$this->data['entry_description'] = $this->language->get('ms_seller_group_entry_description');
		$this->data['entry_language'] = $this->language->get('ms_seller_group_entry_language');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
			
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
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
			
		if (!isset($this->request->get['seller_group_id'])) {
			$this->data['action'] = $this->url->link('multiseller/seller-group/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('multiseller/seller-group/update', 'token=' . $this->session->data['token'] . '&seller_group_id=' . $this->request->get['seller_group_id'] . $url, 'SSL');
		}
		  
    	$this->data['cancel'] = $this->url->link('multiseller/seller-group', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		if (isset($this->request->get['seller_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$seller_group_info = $this->MsLoader->MsSellerGroup->getSellerGroup($this->request->get['seller_group_id']);
		}
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['seller_group_description'])) {
			$this->data['seller_group_description'] = $this->request->post['seller_group_description'];
		} elseif (isset($this->request->get['seller_group_id'])) {
			$this->data['seller_group_description'] = $this->MsLoader->MsSellerGroup->getSellerGroupDescriptions($this->request->get['seller_group_id']);
		} else {
			$this->data['seller_group_description'] = array();
		}
		
		$this->template = 'module/multiseller/seller-group-form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validateForm() {
		foreach ($this->request->post['seller_group_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = $this->language->get('ms_error_seller_group_name');
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>
