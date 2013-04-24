<?php

class ControllerMultisellerComment extends ControllerMultisellerBase {
	public function jxDelete() {
		$this->validate(__FUNCTION__);
		$mails = array();
		
		$comment_ids = isset($this->request->post['selected']) ? $this->request->post['selected'] : (isset($this->request->get['comment_id']) ? array($this->request->get['comment_id']) : false);
		if ($comment_ids && !empty($comment_ids)) {
			foreach ($comment_ids as $id) {
				$this->MsLoader->MsComments->deleteComment($id);
			}
			
			$this->session->data['success'] = $this->language->get('ms_success_comments_deleted');
		}
	}
		
	public function index() {
		$this->load->model('catalog/product');
		$this->validate(__FUNCTION__);
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$sort = array(
			'order_by'  => 'create_time',
			'order_way' => 'DESC',
			'offset' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$comments = $this->MsLoader->MsComments->getComments(array(), $sort);

		foreach ($comments as $result) {
			$product = $this->model_catalog_product->getProductDescriptions($result['product_id']);
			$product = array_shift($product);
			$this->data['comments'][] = array(
				'comment_id' => $result['id'],
				'name' => "{$result['name']} ({$result['email']})",
				'customer_link' => isset($result['customer_id']) ? $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'], 'SSL') : NULL,
				'product_name' => $product['name'],
				'comment' => (mb_strlen($result['comment']) > 80 ? mb_substr($result['comment'], 0, 80) . '...' : $result['comment']),
				'date_created' => date($this->language->get('date_format_short'), $result['create_time']),
				'delete_link' => $this->url->link('multiseller/comment/jxDelete', 'comment_id=' . $result['id'], 'SSL')
			);
		}

		$pagination = new Pagination();
		$pagination->page = $page;
		$pagination->total = $this->MsLoader->MsComments->getTotalComments();		
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link("multiseller/comment", 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
		
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
		$this->data['heading'] = $this->language->get('ms_comments_heading');
		$this->document->setTitle($this->language->get('ms_comments_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multiseller/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_comments_breadcrumbs'),
				'href' => $this->url->link('multiseller/comment', '', 'SSL'),
			)
		));
		
		list($this->template, $this->children) = $this->MsLoader->MsHelper->admLoadTemplate('comment');
		$this->response->setOutput($this->render());
	}
}
?>

