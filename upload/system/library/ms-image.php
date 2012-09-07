<?php
class MsImage {
	private $errors;
	private $fileName;
	
	private $tmpPath;
	
	private function _isNewUpload() {
		return file_exists(DIR_IMAGE . $this->tmpPath . $this->fileName);
	}
		
  	public function __construct($registry) {
  		$this->registry = $registry;
  		
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->language = $registry->get('language');
		
		$this->tmpPath = 'tmp/';
		
		$this->errors = array();
		
		require_once(DIR_SYSTEM . 'library/ms-seller.php');
		$this->msSeller = new MsSeller($registry);				
	}
	
	public static function byName($registry, $name) {
		$instance = new self($registry);
        $instance->fileName = $name;
        return $instance;
	}
	
  	public function validate($file, $type) {
  		if ($type == 'I' || $type == 'T') {
  			// images, thumbnails
			$allowed_filetypes = $this->config->get('msconf_allowed_image_types');
  		} else {
  			// downloads
			$allowed_filetypes = $this->config->get('msconf_allowed_download_types');
  		}
  		
		$filetypes = explode(',', $allowed_filetypes);
		$filetypes = array_map('strtolower', $filetypes);
		$filetypes = array_map('trim', $filetypes);
		
		if ($type == 'I' || $type == 'T') {
			// images, thumbnails
			$size = getimagesize($file["tmp_name"]);
			if(!isset($size) || stripos($file['type'],'image/') === FALSE || stripos($size['mime'],'image/') === FALSE) {
		        $this->errors[] = $this->language->get('ms_error_file_type');
			}
		}
		
		$ext = explode('.', $file['name']);
		$ext = end($ext);
		
		if (!in_array(strtolower($ext),$filetypes)) {
			 $this->errors[] = $this->language->get('ms_error_file_extension');
		}
		
		if ($file["error"] != UPLOAD_ERR_OK) {
			if ($file["error"] == UPLOAD_ERR_INI_SIZE || $file["error"] == UPLOAD_ERR_FORM_SIZE) {
		 		$this->errors[] = $this->language->get('ms_error_file_size');
			} else {
				$this->errors[] = $this->language->get('ms_error_file_upload_error');
			}
		}
		
		return empty($this->errors);
  	}

	public function upload($file, $type) {
		if ($type == 'F') {
			$filename =   time() . '_' . md5(rand()) . '.' . $this->msSeller->getNickname() . '_' . $file["name"];	  		
		} else {
			$filename =   time() . '_' . md5(rand()) . '.' . $file["name"];
		}
		move_uploaded_file($file["tmp_name"], DIR_IMAGE . $this->tmpPath .  $filename);
		$this->session->data['multiseller']['files'][] = $filename;
	
		return $filename;
	}
  	
  	public function getErrors() {
  		return $this->errors;
  	}
  	
  	public function checkFileAgainstSession() {
		if (array_search($this->fileName, $this->session->data['multiseller']['files']) === FALSE) {
			$this->errors[] = $this->language->get('ms_error_file_upload_error');
			return FALSE;
		}
		
		return TRUE;
  	}
  	
  	public function move($type) {
  		$key = array_search($this->fileName, $this->session->data['multiseller']['files']);
  		//strip nonce and timestamp
  		$original_file_name = substr($this->getName(),strpos($this->getName(),'.')+1,mb_strlen($this->getName()));
  		
  		if ($type == 'I' || $type == 'T') { 
			if ($this->_isNewUpload()) {
				$newpath = 'data/' . $this->fileName;
				rename(DIR_IMAGE . $this->tmpPath . $this->fileName,  DIR_IMAGE . $newpath);
				$this->fileName = $newpath;
			}
  		} else {
			if ($this->_isNewUpload()) {
				$newpath = $original_file_name . '.' . md5(rand());
				rename(DIR_IMAGE . $this->tmpPath . $this->fileName,  DIR_DOWNLOAD . $newpath);
				$this->fileName = $newpath;
			}
  		}
  		unset ($this->session->data['multiseller']['files'][$key]);
  	}
  	
  	public function delete($type) {
  		if (empty($this->fileName))
  			return false;

		$key = array_search($this->fileName, $this->session->data['multiseller']['files']);
  		
  		if ($type == 'I' || $type == 'T') {
			if (file_exists(DIR_IMAGE. $this->fileName)) {
				//@unlink(DIR_IMAGE. $this->fileName);
				unlink(DIR_IMAGE. $this->fileName);
			}
  		} else {
			if (file_exists(DIR_DOWNLOAD. $this->fileName)) {
				//@unlink(DIR_IMAGE. $this->fileName);
				unlink(DIR_DOWNLOAD. $this->fileName);
			}  			
  		}
		unset ($this->session->data['multiseller']['files'][$key]);
  	}  	
  	
  	public function getName () {
  		return $this->fileName;
  	}
  	
  	public function getTmpPath () {
  		return $this->tmpPath;
  	}  	
  	
  	public function resize($filename, $width, $height) {
  		if (!file_exists(DIR_IMAGE. $filename) || !$filename) {
  			return;
  		}
  		
		$info = pathinfo($filename);
		$extension = $info['extension'];
		
		$new_image = substr($info['basename'], 0, strrpos($info['basename'], '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		$image = new Image(DIR_IMAGE . $filename);
		$image->resize($width, $height);
		$image->save(DIR_IMAGE . $this->tmpPath . $new_image);
		
		
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			return HTTPS_IMAGE . $this->tmpPath . $new_image;
		} else {
			return HTTP_IMAGE . $this->tmpPath . $new_image;
		}			
  	}
}
?>