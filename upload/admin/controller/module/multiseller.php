<?php

class ControllerModuleMultiseller extends Controller {
	private $name = 'multiseller';
	
	private $settings;
	
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->config('ms-config');
		
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'));
		
		$this->settings = Array(
			"msconf_seller_validation" => MS_SELLER_VALIDATION_NONE,
			"msconf_product_validation" => MS_PRODUCT_VALIDATION_NONE,
			"msconf_seller_commission" => 5,
		);
	}	
	
	private function _editSettings() {
		$this->load->model("module/{$this->name}");
		$this->load->model('setting/setting');
		
		$set = $this->model_setting_setting->getSetting($this->name);

		foreach($set as $s=>$v) {
			if (isset($this->request->post[$s])) {
				$set[$s] = $this->request->post[$s];
				$this->data[$s] = $this->request->post[$s];
			} elseif ($this->config->get($s)) {
				$this->data[$s] = $this->config->get($s);
			}
		}

		$this->model_setting_setting->editSetting($this->name, $set);
	}
	
	private function _setBreadcrumbs($textVar, $function) {
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('ms_menu_multiseller'),
			'href'      => $this->url->link('module/multiseller', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
   				
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get($textVar),
			'href'      => $this->url->link("module/{$this->name}/" . strtolower($function), 'token=' . $this->session->data['token'], 'SSL'),       	
        	'separator' => $this->language->get('text_separator')
      	);
	}
	
	private function _renderTemplate($templateName) {
		$this->template = "module/multiseller/$templateName.tpl";
		
		$this->children = array(
			'common/footer',
			'common/header'	
		);

		$this->response->setOutput($this->render());
	}	
	
	public function install() {
		$this->load->model("module/{$this->name}");
		$this->load->model('setting/setting');
		$this->model_module_multiseller->createTable();
		$this->model_setting_setting->editSetting($this->name, $this->settings);
	}

	public function uninstall() {
		$this->load->model("module/{$this->name}");
		$this->model_module_multiseller->dropTable();
	}	
	
	
	
	public function sellers() {
		$url = '';
		
		$columns = array(
			'name',
			'nickname',
			'email',
			'total_products',
			'total_sales',
			'total_earnings',	
			'current_balance',
			'seller_status_id',
			'date_created',
		);
		
		
		/*
		foreach ($filters as $filter) {
			if (isset($this->request->get[$filter])) {
				$url .= '&' . $filter . '=' . $this->request->get[$filter];
			}			
		}*/

		//$this->data['approve'] = $this->url->link('sale/customer/approve', 'token=' . $this->session->data['token'] . $url, 'SSL');
		//$this->data['insert'] = $this->url->link('sale/customer/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		//$this->data['delete'] = $this->url->link('sale/customer/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		
		$this->load->model("module/{$this->name}/seller");
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		$orderby = isset($this->request->get['orderby']) && in_array($this->request->get['orderby'], $columns) ? $this->request->get['orderby'] : 'date_created';
		
		$orderway = isset($this->request->get['orderway']) ? $this->request->get['orderway'] : 'DESC';
		
		$sort = array(
			'order_by'  => $orderby,
			'order_way' => $orderway,
			'page' => $page,
			'limit' => $this->config->get('config_admin_limit')
		);
				
		$results = $this->model_module_multiseller_seller->getSellers($sort);
		$total_sellers = $this->model_module_multiseller_seller->getTotalSellers($sort);

    	foreach ($results as &$result) {
    		$result['total_products'] = $this->model_module_multiseller_seller->getTotalSellerProducts($result['seller_id']);
			$result['total_earnings'] = $this->currency->format($this->model_module_multiseller_seller->getEarningsForSeller($result['seller_id']), $this->config->get('config_currency'));
			$result['current_balance'] = $this->currency->format($this->model_module_multiseller_seller->getBalanceForSeller($result['seller_id']), $this->config->get('config_currency'));
			$result['total_sales'] = $this->model_module_multiseller_seller->getSalesForSeller($result['seller_id']);
			$result['status'] = $this->model_module_multiseller_seller->getSellerStatus($result['seller_status_id']);
			$result['action'][] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link("module/{$this->name}/editSeller", 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL')
			);
		}
			
		$this->data['sellers'] = $results;
			
		$pagination = new Pagination();
		$pagination->total = $total_sellers;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("module/{$this->name}/sellers", 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
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

		
		foreach($columns as $column) {
			$this->data["link_sort_$column"] = $this->url->link("module/{$this->name}/sellers", 'token=' . $this->session->data['token'] . "&orderby=$column" . $url, 'SSL');
		}

		$this->data['token'] = $this->session->data['token'];		
		$this->data['heading'] = $this->language->get('ms_seller_heading');
		$this->document->setTitle($this->language->get('ms_seller_heading'));
		$this->_setBreadcrumbs('ms_seller_breadcrumbs', __FUNCTION__);
		$this->_renderTemplate('ms-seller-list');		
	}
	
	public function saveSettings() {
		$this->_editSettings();
		
		$json = array();
		if (strcmp(VERSION,'1.5.1.3') >= 0) {
			$this->response->setOutput(json_encode($json));
		} else {
			$this->load->library('json');
			$this->response->setOutput(Json::encode($json));			
		}		
	}
	
	public function index() {
		$this->load->language("module/{$this->name}");
		$this->load->model("module/{$this->name}");
		
		foreach($this->settings as $s=>$v) {
			$this->data[$s] = $this->config->get($s);
		}

		$this->_setBreadcrumbs();
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'));
				
        $this->data['action'] = $this->url->link("module/{$this->name}", 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];
		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->template = "module/{$this->name}.tpl";
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
}
?>
