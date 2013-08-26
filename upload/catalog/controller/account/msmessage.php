<?php

class ControllerAccountMSMessage extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/msconversation', '', 'SSL');
			return $this->redirect($this->url->link('account/login', '', 'SSL'));
		}
		
		if ($this->config->get('msconf_enable_private_messaging') != 1) return $this->redirect($this->url->link('account/account', '', 'SSL'));
	}
	
	public function jxSendMessage() {
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$customer_id = $this->customer->getId();
		$customer_name = $this->customer->getFirstname() . ' ' . $this->customer->getLastname();
		$conversation_id = $this->request->post['conversation_id'];
		if (!$conversation_id) return;
		
		$conversation = $this->MsLoader->MsConversation->getConversations(array(
			'conversation_id' => $conversation_id,
			'single' => 1
		));
		
		if (!$conversation) return;
		
		$message_to = $this->MsLoader->MsConversation->getWith($conversation_id, array('participant_id' => $customer_id));
		$message_text = trim($this->request->post['ms-message-text']);
		
		$conversation_with = $this->MsLoader->MsConversation->getWith($conversation_id, array('participant_id' => $customer_id));
		
		$this->load->model('account/customer');
		$customer = $this->model_account_customer->getCustomer($conversation_with);
		$addressee_name = $customer['firstname'] . ' ' . $customer['lastname'];
		
		$recepient_email = $customer['email'];
	
		$json = array();
	
		if (empty($message_text)) {
			$json['errors'][] = $this->language->get('ms_error_empty_message');
			$this->response->setOutput(json_encode($json));
			return;
		}

		if (mb_strlen($message_text) > 2000) {
			$json['errors'][] = $this->language->get('ms_error_contact_text');
		}
		
		if (!isset($json['errors'])) {
			$this->MsLoader->MsMessage->createMessage(
				array(
					'conversation_id' => $conversation_id,
					'from' => $this->customer->getId(),
					'to' => $message_to,
					'message' => $message_text
				)
			);
			
			$mails[] = array(
				'type' => MsMail::SMT_PRIVATE_MESSAGE,
				'data' => array(
					'recipients' => $recepient_email,
					'customer_name' => $customer_name,
					'customer_message' => $message_text,
					'product_id' => $conversation['product_id'],
					'addressee' => $addressee_name
				)
			);	
			
			$json['success'] = $this->language->get('ms_sellercontact_success');
			$json['redirect'] = $this->url->link('account/msmessage&conversation_id=' . $conversation_id, '', 'SSL');
		}
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->document->addScript('catalog/view/javascript/multimerch/account-message.js');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->language->load('account/account');
		$customer_id = $this->customer->getId();
		
		$conversation_id = isset($this->request->get['conversation_id']) ? $this->request->get['conversation_id'] : false;
		if (!$conversation_id || !$this->MsLoader->MsConversation->isParticipant($conversation_id, array('participant_id' => $customer_id)))
			return $this->redirect($this->url->link('account/msconversation', '', 'SSL'));
		
		$this->data['messages'] = $this->MsLoader->MsMessage->getMessages(
			array(
				'conversation_id' => $conversation_id
			),
			array(
				'order_by'  => 'date_created',
				'order_way' => 'DESC',
			)
		);
		
		$this->MsLoader->MsConversation->markRead(
			$conversation_id,
			array(
				'participant_id' => $customer_id
			)
		);
		
		$this->data['conversation'] = $this->MsLoader->MsConversation->getConversations(array(
			'conversation_id' => $conversation_id,
			'single' => 1
		));
		
		
		$this->document->setTitle($this->language->get('ms_account_messages_heading'));
		
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
			),
			array(
				'text' => $this->data['conversation']['title'],
				'href' => $this->url->link('account/msmessage', '&conversation_id=' . $conversation_id, 'SSL'),
			)
		);
		
		if (!$this->MsLoader->MsSeller->isCustomerSeller($customer_id)) {
			unset($breadcrumbs[1]);
		}
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs($breadcrumbs);
		list($this->template, $this->children) = $this->MsLoader->MsHelper->loadTemplate('account-message');
		$this->response->setOutput($this->render());
	}
}

?>
