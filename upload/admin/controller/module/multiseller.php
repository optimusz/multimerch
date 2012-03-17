<?php

class ControllerModuleMultiseller extends Controller {
	private $name = 'multiseller';
	
	private $settings;
	
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->config('ms-config');
		
		$this->settings = Array(
			"msconf_seller_validation" => MS_SELLER_VALIDATION_NONE,
			"msconf_product_validation" => MS_PRODUCT_VALIDATION_NONE,
			"msconf_seller_commission" => 5,
		);
	}	
	
	private function editSettings() {
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
	
	private function setBreadcrumbs() {
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link("module/{$this->name}", 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
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
	
	public function saveSettings() {
		$this->editSettings();
		
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

		$this->setBreadcrumbs();
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
