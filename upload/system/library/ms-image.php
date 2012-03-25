<?php
class MsImage {
	private $errors;
	private $fileName;
	
	private function _isNewUpload() {
		if (dirname($this->fileName) == '' || dirname($this->fileName) == '.')
			return true;
			
		return false;
	}
		
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		
		$this->errors = array();
	}
	
	public static function byName($registry, $name) {
		$instance = new self($registry);
        $instance->fileName = $name;
        return $instance;
	}
	
  	public function validate($file) {
  		if (!$file) {
			$POST_MAX_SIZE = ini_get('post_max_size');
			$mul = substr($POST_MAX_SIZE, -1);
			$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
	 		if ($_SERVER['CONTENT_LENGTH'] > $mul * (int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
				$this->errors[] = 'File too big';	 			
	 		} else {
	 			$this->errors[] = 'Unknown upload error';
	 		}
	 		return FALSE;
  		}
		//var_dump($file);
		$allowed_filetypes = $this->config->get('config_upload_allowed');
		//$ms_config_max_filesize = $this->config->get('config_upload_allowed');
		
		$ms_config_max_filesize = 500000;
		
		$filetypes = explode(',', $allowed_filetypes);
		$filetypes = array_map('strtolower', $filetypes);
		$filetypes = array_map('trim', $filetypes);
				
		$size = getimagesize($file["tmp_name"]);

		if(!isset($size) || stripos($file['type'],'image/') === FALSE || stripos($size['mime'],'image/') === FALSE) {
	        $this->errors[] = 'Invalid file type';
		}
		
		
		$ext = explode('.', $file['name']);
		$ext = end($ext);
		
		if (!in_array(strtolower($ext),$filetypes)) {
			 $this->errors[] = 'Invalid extension';
		}
			
		if ($file["size"] > $ms_config_max_filesize
		 || $file["error"] === UPLOAD_ERR_INI_SIZE
		 || $file["error"] === UPLOAD_ERR_FORM_SIZE) {
		 	$this->errors[] = 'File too big';
		}
		
		return empty($this->errors);
  	}

	public function upload($file) {
    	$tmp_name = $file["tmp_name"];
    	$name = time() . '_' . uniqid() . '_' . $file["name"];
    	
    	// TODO temp upload dir
    	move_uploaded_file($tmp_name, DIR_IMAGE .  $name);

		$this->session->data['multiseller']['images'][] = $name;
	
		return $name;	
	}
  	
  	public function getErrors() {
  		return $this->errors;
  	}
  	
  	public function checkImageAgainstSession() {
		if (array_search($this->fileName, $this->session->data['multiseller']['images']) === FALSE) {
			$this->errors[] = 'Image ATTACK!';
			return FALSE;
		}
		
		return TRUE;
  	}
  	
  	public function move() {
		$key = array_search($this->fileName, $this->session->data['multiseller']['images']);  		
		if ($this->_isNewUpload()) {
			$newpath = 'data/' . $this->fileName;
			rename(DIR_IMAGE. $this->fileName,  DIR_IMAGE . $newpath);
			$this->fileName = $newpath;
		}
		unset ($this->session->data['multiseller']['images'][$key]);		
  	}
  	
  	public function getName () {
  		return $this->fileName;
  	}
}
?>