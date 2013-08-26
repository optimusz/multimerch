<?php

class ControllerAccountMSConversation extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/msconversation', '', 'SSL');
			return $this->redirect($this->url->link('account/login', '', 'SSL'));
		}
		
		if ($this->config->get('msconf_enable_private_messaging') != 1) return $this->redirect($this->url->link('account/account', '', 'SSL'));		
	}

	public function getTableData() {
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		
		$colMap = array();
		
		$customer_id = $this->customer->getId();
		
		$sorts = array('last_message_date', 'title');
		$filters = array('');
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		//$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$conversations = $this->MsLoader->MsConversation->getConversations(
			array(
				'participant_id' => $customer_id,
			),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				//'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);
		
		$total = isset($conversations[0]) ? $conversations[0]['total_rows'] : 0;

		$columns = array();
		foreach ($conversations as $conversation) {
			// Actions
			$actions = "";
			$actions .= "<a href='" . $this->url->link('account/msmessage', 'conversation_id=' . $conversation['conversation_id'], 'SSL') ."' class='ms-button ms-button-view' title='" . $this->language->get('ms_view') . "'></a>";
			
			// Conversation Status
			$read = "";
			if ($this->MsLoader->MsConversation->isRead($conversation['conversation_id'], array('participant_id' => $customer_id))) {
				$status = "<img src='catalog/view/theme/" . $this->config->get('config_template') . "/image/ms-opened-envelope.png' alt='" . $this->language->get('ms_account_conversations_read') . "' title='" . $this->language->get('ms_account_conversations_read') . "' class='ms-read' />";
			} else {
				$status = "<img src='catalog/view/theme/" . $this->config->get('config_template') . "/image/ms-envelope.png' alt='" . $this->language->get('ms_account_conversations_unread') . "' title='" . $this->language->get('ms_account_conversations_unread') . "' />";
			}
			
			// Get customer name
			$conversation_with = $this->MsLoader->MsConversation->getWith($conversation['conversation_id'], array('participant_id' => $customer_id));
			$this->load->model('account/customer');
			$customer = $this->model_account_customer->getCustomer($conversation_with);
			$customer_name = $customer['firstname'] . ' ' . $customer['lastname'];
			
			$columns[] = array_merge(
				$conversation,
				array(
					'icon' => $status,
					//'last_message_date' => date($this->language->get('date_format_long'), strtotime($conversation['last_message_date'])),
					'with' => $customer_name,
					'title' => (mb_strlen($conversation['title']) > 80 ? mb_substr($conversation['title'], 0, 80) . '...' : $conversation['title']),
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
		$this->document->addStyle('catalog/view/javascript/multimerch/datatables/css/jquery.dataTables.css');
		$this->document->addScript('catalog/view/javascript/multimerch/datatables/js/jquery.dataTables.min.js');
		$this->document->addScript('catalog/view/javascript/multimerch/common.js');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->language->load('account/account');
		
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');
		$this->document->setTitle($this->language->get('ms_account_conversations_heading'));
		$customer_id = $this->customer->getId();
		
		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_conversations_breadcrumbs'),
				'href' => $this->url->link('account/msconversation', '', 'SSL'),
			)
		);
		
		if (!$this->MsLoader->MsSeller->isCustomerSeller($customer_id)) {
			unset($breadcrumbs[1]);
		}
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs($breadcrumbs);
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-conversation');
		$this->response->setOutput($this->render());
	}
}
?>
