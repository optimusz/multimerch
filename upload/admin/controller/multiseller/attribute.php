<?php

class ControllerMultisellerAttribute extends ControllerMultisellerBase {
	public function index() {
		$this->validate(__FUNCTION__);
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'ad.name',
			'order_way' => 'ASC',
			'page' => $page,
			'limit' => $this->config->get('config_admin_limit')
		);

		$attributes = $this->MsLoader->MsAttribute->getAttributes($sort);

		foreach ($attributes as $result) {
			$this->data['attributes'][] = array(
				'attribute_id' => $result['attribute_id'],
				'name' => $result['mad.name'],
				'type' => $this->MsLoader->MsAttribute->getTypeText($result['ma.attribute_type']),
				'sort_order' => $result['ma.sort_order'],
				'enabled' => $result['ma.enabled'] ? 1  : 0,
				'actions' => array(
					array(
						'text' => $this->language->get('text_edit'),
						'href' => $this->url->link('multiseller/attribute/update', 'token=' . $this->session->data['token'] . '&attribute_id=' . $result['attribute_id'], 'SSL')
					)
				)
			);
		}
		
		$pagination = new Pagination();
		$pagination->page = $page;
		$pagination->total = $this->MsLoader->MsAttribute->getTotalAttributes();		
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("multiseller/attribute", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
		
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
		
		$this->data['heading'] = $this->language->get('ms_attribute_heading');
		$this->document->setTitle($this->language->get('ms_attribute_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_attribute_breadcrumbs'),
				'href' => $this->url->link('multiseller/attribute', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('attribute');
		$this->response->setOutput($this->render());
	}
	
	public function create() {
		$this->load->model('localisation/language');
		$this->load->model('tool/image');
		
		$this->data['attribute'] = FALSE;
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$this->data['cancel'] = $this->url->link('multiseller/attribute', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_attribute_create');
		$this->document->setTitle($this->language->get('ms_attribute_create'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_attribute_breadcrumbs'),
				'href' => $this->url->link('multiseller/attribute', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('attribute-form');
		$this->response->setOutput($this->render());		
	}

	public function update() {
		$this->load->model('localisation/language');
		$this->load->model('tool/image');
		$attribute_id = $this->request->get['attribute_id'];
		
		$this->data['attribute'] = $this->MsLoader->MsAttribute->getAttribute($attribute_id);
		$this->data['attribute']['attribute_description'] = $this->MsLoader->MsAttribute->getAttributeDescriptions($attribute_id);

		if (in_array($this->data['attribute']['attribute_type'], array(MsAttribute::TYPE_SELECT, MsAttribute::TYPE_RADIO, MsAttribute::TYPE_IMAGE, MsAttribute::TYPE_CHECKBOX))) {
			$this->data['attribute']['attribute_values'] = $this->MsLoader->MsAttribute->getAttributeValues($attribute_id);
			
			foreach ($this->data['attribute']['attribute_values'] as  &$value) {
				$value['attribute_value_description'] = $this->MsLoader->MsAttribute->getAttributeValueDescriptions($value['attribute_value_id']);
				$value['thumb'] = (!empty($value['image']) ? $this->model_tool_image->resize($value['image'], 100, 100) : $this->model_tool_image->resize('no_image.jpg', 100, 100));
			}			
		}
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$this->data['cancel'] = $this->url->link('multiseller/attribute', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_attribute_edit');
		$this->document->setTitle($this->language->get('ms_attribute_edit'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_attribute_breadcrumbs'),
				'href' => $this->url->link('multiseller/attribute', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('attribute-form');
		$this->response->setOutput($this->render());		
	}


	public function jxDeleteAttribute() {
		$this->validate(__FUNCTION__);
		$mails = array();
		
		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $attribute_id) {
				$this->MsLoader->MsAttribute->deleteAttribute($attribute_id);
			}
		}
	}

	public function jxSubmitAttribute() {
		$json = array();
		$data = $this->request->post; 
		unset($data['attribute_value'][0]);
		
		foreach ($data['attribute_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 128)) {
				$json['errors']["attribute_description[$language_id][name]"] = $this->language->get('ms_error_attribute_name');
			}
		}

		if (($data['attribute_type'] == MsAttribute::TYPE_SELECT || $data['attribute_type'] == MsAttribute::TYPE_RADIO || $data['attribute_type'] == MsAttribute::TYPE_CHECKBOX || $data['attribute_type'] == MsAttribute::TYPE_IMAGE)) {
			if (empty($data['attribute_value'])) {
				$json['errors']['attribute_type'] = $this->language->get('ms_error_attribute_type');
			}
		} else if (($data['attribute_type'] != MsAttribute::TYPE_TEXT && $data['attribute_type'] != MsAttribute::TYPE_TEXTAREA)) {
			unset($data['text_type']);
			unset($data['attribute_value']);
		}

		if (isset($data['attribute_value'])) {
			foreach ($data['attribute_value'] as $attribute_value_id => $attribute_value) {
				foreach ($attribute_value['attribute_value_description'] as $language_id => $attribute_value_description) {
					if ((utf8_strlen($attribute_value_description['name']) < 1) || (utf8_strlen($attribute_value_description['name']) > 128)) {
						$json['errors']["attribute_value[$attribute_value_id][attribute_value_description][$language_id][name]"] = $this->language->get('ms_error_attribute_value_name'); 
					}					
				}
			}	
		}		
		
		if (empty($json['errors'])) {
			if (isset($data['attribute_id']) && !empty($data['attribute_id'])) {
				$attribute_id = $this->MsLoader->MsAttribute->updateAttribute($data['attribute_id'], $data);
				$this->session->data['success'] = $this->language->get('ms_success_attribute_updated');
			} else {
				$attribute_id = $this->MsLoader->MsAttribute->createAttribute($data);
				$this->session->data['success'] = $this->language->get('ms_success_attribute_created');
			}
			
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('multiseller/attribute', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->response->setOutput(json_encode($json));
	}
}
?>