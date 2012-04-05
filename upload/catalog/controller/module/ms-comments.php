<?php
class ControllerModuleMsComments extends Controller {
	public function submitComment() {
		$this->load->model('module/multiseller/comments');
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'));

	    $comment['name'] = trim(strip_tags(htmlspecialchars_decode($this->request->post['pcName'])));
	    $comment['email'] = trim(strip_tags(htmlspecialchars_decode($this->request->post['pcEmail'])));
	    $comment['comment'] = trim(strip_tags(htmlspecialchars_decode($this->request->post['pcText'])));
	    $comment['id_customer'] = ($this->customer->isLogged()) ? $this->customer->getId() : 0;		
		$comment['id_product'] = $this->request->get['product_id'];		

		$json = array();
		
		if (!$this->customer->isLogged()) {
			if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
				$json['error'] = $this->language->get('ms_comments_error_captcha');
			}
		}

		if (strlen($comment['comment']) > $this->config->get('msconf_comments_maxlen')
		&& $this->config->get('msconf_comments_maxlen') > 0) {
			$json['error'] = $this->language->get('ms_comments_error_text_long');
		}

		if (strlen($comment['comment']) == '') {
			$json['error'] = $this->language->get('ms_comments_error_text_empty');
		}
		
		if ((strlen($comment['email']) > 128)) {
			$json['error'] = $this->language->get('ms_comments_error_email_long');
		}
				
		if ((strlen($comment['name']) < 3) || (strlen($comment['name']) > 25)) {
			$json['error'] = $this->language->get('ms_comments_error_name');
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !isset($json['error'])) {
			$this->model_module_multiseller_comments->addComment($comment);
			
			$json['success'] = $this->language->get('ms_comments_success');
		}
		
		if (strcmp(VERSION,'1.5.1.3') >= 0) {
			$this->response->setOutput(json_encode($json));
		} else {
			$this->load->library('json');
			$this->response->setOutput(Json::encode($json));			
		}
	}
	
	public function loadComments() {
		$this->load->model('module/multiseller/comments');		
		$this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/multiseller.css');		
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'));

		$comments = $this->model_module_multiseller_comments->getComments($this->request->get['product_id'], 1);
		
		$return = '';
		if (!empty($comments)) {
			$return .= '<div id="pcComments">';
			foreach ($comments as $comment) {
				$return .= '
				<div class="content">
					<div class="comment-header">
				    	<span class="comment-name">' . htmlspecialchars($comment['name']) . ' </span>
				    	<span class="comment-date">' . date("d/m/Y",$comment['create_time']) . '</span>
				    </div>
				    <div class="comment-content">' . nl2br($comment['comment']) . '</div>
				</div>';		
			}
		}
		echo $return;
	}
	
	public function getComments() {
		$this->load->model('module/multiseller/comments');
		$this->data = array_merge($this->data, $this->load->language('module/multiseller'));
		
		$this->data['product_id'] = $this->request->get['product_id'];
		$this->data['pcComments'] = $this->model_module_multiseller_comments->getComments($this->request->get['product_id'], 1);
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/multiseller/ms-comments.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/multiseller/ms-comments.tpl';
		} else {
			$this->template = 'default/template/module/multiseller/ms-comments.tpl';
		}			
		
		$this->data['msconf_comments_maxlen'] = $this->config->get('msconf_comments_maxlen');
		$this->data['pcName'] = $this->customer->getFirstname();
		$this->data['pcEmail'] = $this->customer->getEmail();
		$this->data['pcLogged'] = $this->customer->isLogged();
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));		
	}
}
?>