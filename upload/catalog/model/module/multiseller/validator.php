<?php
class MsValidator {
	private $errors;
	private $data;
	
	public function __construct(&$data) {
		$this->data =& $data;
	}	

	public function getErrors() {
		return $this->errors;
	}
	
	public function isEmpty($field, $error) {
		$this->data[$field] = trim($this->data[$field]);

		if (empty($this->data[$field]))
			$this->errors[] = array($field => $error);
	}
}
?>