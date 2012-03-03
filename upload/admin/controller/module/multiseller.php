<?php

class ControllerModuleMultiseller extends Controller {
	
	private $name = 'multiseller';
	
	private $settings = Array(
		"multiseller_conf_maxlen" => 500		
	);
	
	private $error = array(); 
	
	private function editSettings() {
		$this->load->model("module/{$this->name}");
		
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
	
	private function setTranslations() {
		$text_strings = array(
			'heading_title', 'button_cancel', 'button_save', 'text_test'
		);
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		foreach ($text_strings as $text) {
			$this->data[$text] = $this->language->get($text);
		}		
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
		//$this->settings["{$this->name}_conf_email"] = $this->config->get('config_email');
		//$this->model_setting_setting->editSetting($this->name, $this->settings);
	}

	public function uninstall() {
		$this->load->model("module/{$this->name}");
		$this->model_module_multiseller->dropTable();
	}
	
	public function index() {
		$this->load->language("module/{$this->name}");
		$this->load->model("module/{$this->name}");
		$this->load->model('setting/setting');
		
		foreach($this->settings as $s=>$v) {
			$this->data[$s] = $this->config->get($s);
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			if (isset($this->request->post['saveComment'])) {
				
	        } else if (isset($this->request->post['delComment'])) {
	        	
	        } else if (isset($this->request->post['saveConfig']) || isset($this->request->post['submitPositions'])) {
	        	
        	}
	        $this->session->data['success'] = $this->language->get('text_success');
		}
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->setBreadcrumbs();
		$this->setTranslations();
				
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
