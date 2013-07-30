<?php
class ControllerMultisellerBadge extends ControllerMultisellerBase {
	private $error = array();

	public function getTableData() {
		$colMap = array(
			'id' => 'mb.badge_id'
		);

		$this->load->model('tool/image');
		$sorts = array('id', 'name', 'description');
		$filters = $sorts;
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsBadge->getBadges(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			// image
			$image = $this->model_tool_image->resize($result['image'], 50, 50);
			
			// actions
			$actions = "";
			$actions .= "<a class='ms-button ms-button-edit' href='" . $this->url->link('multiseller/badge/update', 'token=' . $this->session->data['token'] . '&badge_id=' . $result['badge_id'], 'SSL') . "' title='".$this->language->get('text_edit')."'></a>";
			$actions .= "<a class='ms-button ms-button-delete' href='" . $this->url->link('multiseller/badge/delete', 'token=' . $this->session->data['token'] . '&badge_id=' . $result['badge_id'], 'SSL') . "' title='".$this->language->get('text_delete')."'></a>";

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['mb.badge_id']}' />",
					'id' => $result['badge_id'],
					'name' => $result['name'],
					'description' => (mb_strlen($result['description']) > 80 ? mb_substr($result['description'], 0, 80) . '...' : $result['description']),
					'image' => "<img src='$image' />",
					'actions' => $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}
	
	public function index() {
		$this->validate(__FUNCTION__);
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_badges_breadcrumbs'),
				'href' => $this->url->link('multiseller/badge', '', 'SSL'),
			)
		));
		
		$this->data['insert'] = $this->url->link('multiseller/badge/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('multiseller/badge/delete', 'token=' . $this->session->data['token'], 'SSL');
	
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
		$this->data['heading'] = $this->language->get('ms_catalog_badges_heading');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->document->setTitle($this->language->get('ms_catalog_badges_heading'));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('badge'); 
		$this->response->setOutput($this->render());
	}
	
	// Insert a new badge
	public function insert() {
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_insert_badge_heading');
		$this->document->setTitle($this->language->get('ms_catalog_insert_badge_heading'));
		
		$this->load->model('tool/image');
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$this->data['cancel'] = $this->url->link('multiseller/badge', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$this->data['badge'] = NULL;
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_badges_breadcrumbs'),
				'href' => $this->url->link('multiseller/badge', '', 'SSL'),
			)
		));		
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('badge-form'); 
		$this->response->setOutput($this->render());
	}
	
	// Update badge
	public function update() {
		$this->validate(__FUNCTION__);
		
		$this->load->model('tool/image');
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_catalog_edit_badge_heading');
		$this->document->setTitle($this->language->get('ms_catalog_edit_badge_heading'));
		
		$this->data['cancel'] = $this->url->link('multiseller/badge', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();		
		
		$badge = $this->MsLoader->MsBadge->getBadges(array('single' => 1), array());
		
		$this->data['badge'] = array(
			'badge_id' => $badge['badge_id'],
			'description' => $this->MsLoader->MsBadge->getBadgeDescriptions($this->request->get['badge_id']),
			'image' => $this->model_tool_image->resize($badge['image'], 50, 50),
		);
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_badges_breadcrumbs'),
				'href' => $this->url->link('multiseller/badge', '', 'SSL'),
			)
		));		
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('badge-form'); 
		$this->response->setOutput($this->render());		
	}
	
	// Bulk delete of badges
	public function delete() { 
		if (isset($this->request->get['badge_id'])) $this->request->post['selected'] = array($this->request->get['badge_id']);
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $badge_id) {
				$this->MsLoader->MsBadge->deleteBadge($badge_id);
			}
			
			$this->session->data['success'] = $this->language->get('ms_success');
		}
		
		$this->redirect($this->url->link('multiseller/badge', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	// Get form for adding/editing badges
	private function getEditForm() {
		$this->data['heading'] = $this->language->get('ms_catalog_insert_badge_heading');
		
		if (!isset($this->request->get['badge_id'])) {
			$this->data['action'] = $this->url->link('multiseller/badge/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('multiseller/badge/update', 'token=' . $this->session->data['token'] . '&badge_id=' . $this->request->get['badge_id'] . $url, 'SSL');
		}
		  
    	$this->data['cancel'] = $this->url->link('multiseller/badge', 'token=' . $this->session->data['token'] . $url, 'SSL');

		
		if (isset($this->request->post['badge_description'])) {
			$this->data['badge_description'] = $this->request->post['badge_description'];
		} elseif (isset($this->request->get['badge_id'])) {
			$this->data['badge_description'] = 'a';
		} else {
			$this->data['badge_description'] = array();
		}
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('badge-form'); 
		$this->response->setOutput($this->render());
	}
	
	private function validateForm() {
	}
	
	// Validate delete of the badge
	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'multiseller/badge')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function jxSave() {
		$data = $this->request->post['badge'];
		$json = array();

		foreach ($data['description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$json['errors']['name_' . $language_id] = $this->language->get('ms_error_badge_name');
			}
		}
		
		if (empty($json['errors'])) {
			if (empty($data['badge_id'])) {
				$this->MsLoader->MsBadge->createBadge($data);
				$this->session->data['success'] = $this->language->get('ms_success_badge_created');
			} else {
				$this->MsLoader->MsBadge->editBadge($data['badge_id'], $data);
				$this->session->data['success'] = $this->language->get('ms_success_badge_updated');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>