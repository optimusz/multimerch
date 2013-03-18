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

		if (($data['attribute_type'] == MsAttribute::TYPE_SELECT || $data['attribute_type'] == MsAttribute::TYPE_RADIO || $data['attribute_type'] == MsAttribute::TYPE_CHECKBOX)) {
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
				$this->session->data['success'] = $this->language->get('ms_success_product_updated');
			} else {
				$attribute_id = $this->MsLoader->MsAttribute->createAttribute($data);
				$this->session->data['success'] = $this->language->get('ms_success_product_created');
			}
			
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('multiseller/attribute', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/attribute')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($data['attribute_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 128)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if (($this->request->post['type'] == 'select' || $this->request->post['type'] == 'radio' || $this->request->post['type'] == 'checkbox') && !isset($this->request->post['attribute_value'])) {
			$this->error['warning'] = $this->language->get('error_type');
		}

		if (isset($this->request->post['attribute_value'])) {
			foreach ($this->request->post['attribute_value'] as $attribute_value_id => $attribute_value) {
				foreach ($attribute_value['attribute_value_description'] as $language_id => $attribute_value_description) {
					if ((utf8_strlen($attribute_value_description['name']) < 1) || (utf8_strlen($attribute_value_description['name']) > 128)) {
						$this->error['attribute_value'][$attribute_value_id][$language_id] = $this->language->get('error_attribute_value'); 
					}					
				}
			}	
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
		
	
/*














	public function insert() {
		$this->language->load('catalog/option');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/option');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_option->addOption($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

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
			
			$this->redirect($this->url->link('catalog/option', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->language->load('catalog/option');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/option');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_option->editOption($this->request->get['option_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

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
			
			$this->redirect($this->url->link('catalog/option', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->language->load('catalog/option');

		$this->document->setTitle($this->language->get('heading_title'));
 		
		$this->load->model('catalog/option');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $option_id) {
				$this->model_catalog_option->deleteOption($option_id);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			
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
			
			$this->redirect($this->url->link('catalog/option', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'od.name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
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

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/option', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['insert'] = $this->url->link('catalog/option/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('catalog/option/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		 
		$this->data['options'] = array();
		
		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$option_total = $this->model_catalog_option->getTotalOptions();
		
		$results = $this->model_catalog_option->getOptions($data);
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/option/update', 'token=' . $this->session->data['token'] . '&option_id=' . $result['option_id'] . $url, 'SSL')
			);

			$this->data['options'][] = array(
				'option_id'  => $result['option_id'],
				'name'       => $result['name'],
				'sort_order' => $result['sort_order'],
				'selected'   => isset($this->request->post['selected']) && in_array($result['option_id'], $this->request->post['selected']),
				'action'     => $action
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_action'] = $this->language->get('column_action');	

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['sort_name'] = $this->url->link('catalog/option', 'token=' . $this->session->data['token'] . '&sort=od.name' . $url, 'SSL');
		$this->data['sort_sort_order'] = $this->url->link('catalog/option', 'token=' . $this->session->data['token'] . '&sort=o.sort_order' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $option_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/option', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'catalog/option_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function getForm() {
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_choose'] = $this->language->get('text_choose');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_radio'] = $this->language->get('text_radio');
		$this->data['text_checkbox'] = $this->language->get('text_checkbox');
		$this->data['text_image'] = $this->language->get('text_image');
		$this->data['text_input'] = $this->language->get('text_input');
		$this->data['text_text'] = $this->language->get('text_text');
		$this->data['text_textarea'] = $this->language->get('text_textarea');
		$this->data['text_file'] = $this->language->get('text_file');
		$this->data['text_date'] = $this->language->get('text_date');
		$this->data['text_datetime'] = $this->language->get('text_datetime');
		$this->data['text_time'] = $this->language->get('text_time');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');	
		
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_type'] = $this->language->get('entry_type');
		$this->data['entry_option_value'] = $this->language->get('entry_option_value');
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_option_value'] = $this->language->get('button_add_option_value');
		$this->data['button_remove'] = $this->language->get('button_remove');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}	
				
 		if (isset($this->error['option_value'])) {
			$this->data['error_option_value'] = $this->error['option_value'];
		} else {
			$this->data['error_option_value'] = array();
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

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/option', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['option_id'])) {
			$this->data['action'] = $this->url->link('catalog/option/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else { 
			$this->data['action'] = $this->url->link('catalog/option/update', 'token=' . $this->session->data['token'] . '&option_id=' . $this->request->get['option_id'] . $url, 'SSL');
		}

		$this->data['cancel'] = $this->url->link('catalog/option', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['option_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$option_info = $this->model_catalog_option->getOption($this->request->get['option_id']);
    	}
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['option_description'])) {
			$this->data['option_description'] = $this->request->post['option_description'];
		} elseif (isset($this->request->get['option_id'])) {
			$this->data['option_description'] = $this->model_catalog_option->getOptionDescriptions($this->request->get['option_id']);
		} else {
			$this->data['option_description'] = array();
		}	

		if (isset($this->request->post['type'])) {
			$this->data['type'] = $this->request->post['type'];
		} elseif (!empty($option_info)) {
			$this->data['type'] = $option_info['type'];
		} else {
			$this->data['type'] = '';
		}
		
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($option_info)) {
			$this->data['sort_order'] = $option_info['sort_order'];
		} else {
			$this->data['sort_order'] = '';
		}
		
		if (isset($this->request->post['option_value'])) {
			$option_values = $this->request->post['option_value'];
		} elseif (isset($this->request->get['option_id'])) {
			$option_values = $this->model_catalog_option->getOptionValueDescriptions($this->request->get['option_id']);
		} else {
			$option_values = array();
		}
		
		$this->load->model('tool/image');
		
		$this->data['option_values'] = array();
		 
		foreach ($option_values as $option_value) {
			if ($option_value['image'] && file_exists(DIR_IMAGE . $option_value['image'])) {
				$image = $option_value['image'];
			} else {
				$image = 'no_image.jpg';
			}
			
			$this->data['option_values'][] = array(
				'option_value_id'          => $option_value['option_value_id'],
				'option_value_description' => $option_value['option_value_description'],
				'image'                    => $image,
				'thumb'                    => $this->model_tool_image->resize($image, 100, 100),
				'sort_order'               => $option_value['sort_order']
			);
		}

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		$this->template = 'catalog/option_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/option')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['option_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 128)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if (($this->request->post['type'] == 'select' || $this->request->post['type'] == 'radio' || $this->request->post['type'] == 'checkbox') && !isset($this->request->post['option_value'])) {
			$this->error['warning'] = $this->language->get('error_type');
		}

		if (isset($this->request->post['option_value'])) {
			foreach ($this->request->post['option_value'] as $option_value_id => $option_value) {
				foreach ($option_value['option_value_description'] as $language_id => $option_value_description) {
					if ((utf8_strlen($option_value_description['name']) < 1) || (utf8_strlen($option_value_description['name']) > 128)) {
						$this->error['option_value'][$option_value_id][$language_id] = $this->language->get('error_option_value'); 
					}					
				}
			}	
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/option')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$this->load->model('catalog/product');
		
		foreach ($this->request->post['selected'] as $option_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByOptionId($option_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}	
	
	public function autocomplete() {
		$json = array();
		
		if (isset($this->request->get['filter_name'])) {
			$this->language->load('catalog/option');
			
			$this->load->model('catalog/option');
			
			$this->load->model('tool/image');
			
			$data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 20
			);
			
			$options = $this->model_catalog_option->getOptions($data);
			
			foreach ($options as $option) {
				$option_value_data = array();
				
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_values = $this->model_catalog_option->getOptionValues($option['option_id']);
					
					foreach ($option_values as $option_value) {
						if ($option_value['image'] && file_exists(DIR_IMAGE . $option_value['image'])) {
							$image = $this->model_tool_image->resize($option_value['image'], 50, 50);
						} else {
							$image = '';
						}
													
						$option_value_data[] = array(
							'option_value_id' => $option_value['option_value_id'],
							'name'            => html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8'),
							'image'           => $image					
						);
					}
					
					$sort_order = array();
				  
					foreach ($option_value_data as $key => $value) {
						$sort_order[$key] = $value['name'];
					}
			
					array_multisort($sort_order, SORT_ASC, $option_value_data);					
				}
				
				$type = '';
				
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$type = $this->language->get('text_choose');
				}
				
				if ($option['type'] == 'text' || $option['type'] == 'textarea') {
					$type = $this->language->get('text_input');
				}
				
				if ($option['type'] == 'file') {
					$type = $this->language->get('text_file');
				}
				
				if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$type = $this->language->get('text_date');
				}
												
				$json[] = array(
					'option_id'    => $option['option_id'],
					'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
					'category'     => $type,
					'type'         => $option['type'],
					'option_value' => $option_value_data
				);
			}
		}

		$sort_order = array();
	  
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);
				
		$this->response->setOutput(json_encode($json));
	}
	*/
}
?>