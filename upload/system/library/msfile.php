<?php
class MsFile extends Model {
	private $tmpPath;
	
	private function _isNewUpload($fileName) {
		return file_exists(DIR_IMAGE . $this->tmpPath . $fileName);
	}
		
  	public function __construct($registry) {
  		parent::__construct($registry);
		$this->tmpPath = 'tmp/';
	}
	
	public function checkPostMax($post, $files) {
		$errors = array();
		
		if (empty($post) || empty($files)) {
			$POST_MAX_SIZE = ini_get('post_max_size');
			$mul = substr($POST_MAX_SIZE, -1);
			$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
	 		if ($_SERVER['CONTENT_LENGTH'] > $mul * (int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
				$errors[] = $this->language->get('ms_error_file_size');
	 		} else {
	 			$errors[] = $this->language->get('ms_error_file_upload_error');
	 		}
		}
		
	 	return $errors;
	}
	
	public function checkFile($file, $allowed_filetypes) {
		$errors = array();
		
		$filetypes = explode(',', $allowed_filetypes);
		$filetypes = array_map('strtolower', $filetypes);
		$filetypes = array_map('trim', $filetypes);
		
		$ext = explode('.', $file['name']);
		$ext = end($ext);

		if (!in_array(strtolower($ext),$filetypes)) {
			 $errors[] = $this->language->get('ms_error_file_extension');
		}
		
		if ($file["error"] != UPLOAD_ERR_OK) {
			if ($file["error"] == UPLOAD_ERR_INI_SIZE || $file["error"] == UPLOAD_ERR_FORM_SIZE) {
		 		$errors[] = $this->language->get('ms_error_file_size');
			} else {
				$errors[] = $this->language->get('ms_error_file_upload_error');
			}
		}
		
		return $errors;		
	}
	
	public function checkDownload($file) {
		return $this->checkFile($file, $this->config->get('msconf_allowed_download_types'));
	}
		
	public function checkImage($file) {
		$errors = $this->checkFile($file, $this->config->get('msconf_allowed_image_types'));
		
		if (!$errors) {
			$size = getimagesize($file["tmp_name"]);
			//@TODO? Flash reports all files as octet-stream
			//if(!isset($size) || stripos($file['type'],'image/') === FALSE || stripos($size['mime'],'image/') === FALSE) {
			if(!isset($size)) {
				var_dump('error');
		        $errors[] = $this->language->get('ms_error_file_type');
			}		
		}
		
		return $errors;
	}
	
	public function uploadImage($file) {
		$filename =   time() . '_' . md5(rand()) . '.' . $file["name"];
		move_uploaded_file($file["tmp_name"], DIR_IMAGE . $this->tmpPath .  $filename);

		if (!in_array($filename, $this->session->data['multiseller']['files'])) {
			$this->session->data['multiseller']['files'][] = $filename;
		}
		return $filename;
	}
	
	public function uploadDownload($file) {
		$filename =   time() . '_' . md5(rand()) . '.' . $this->MsLoader->MsSeller->getNickname() . '_' . $file["name"];
		move_uploaded_file($file["tmp_name"], DIR_IMAGE . $this->tmpPath .  $filename);
		
		if (!in_array($filename, $this->session->data['multiseller']['files']))
			$this->session->data['multiseller']['files'][] = $filename;
	
		return array(
			'fileName' => $filename,
			'fileMask' => $file['name']
		);
	}	
	
  	public function checkFileAgainstSession($fileName) {
		if (array_search($fileName, $this->session->data['multiseller']['files']) === FALSE) {
			return FALSE;
		}
		
		return TRUE;
  	}	
	
  	public function moveDownload($fileName) {
  		$newpath = $fileName;  		

  		$key = array_search($fileName, $this->session->data['multiseller']['files']);
  		//strip nonce and timestamp
  		$original_file_name = substr($fileName,strpos($fileName,'.')+1,mb_strlen($fileName));
  		
		if ($this->_isNewUpload($fileName)) {
			$newpath = $original_file_name . '.' . md5(rand());
			rename(DIR_IMAGE . $this->tmpPath . $fileName,  DIR_DOWNLOAD . $newpath);
		}
		
  		unset ($this->session->data['multiseller']['files'][$key]);
  		
  		return $newpath;
  	}
	
  	public function moveImage($fileName) {
  		$newpath = $fileName;
		$imageDir = $this->config->get('msconf_product_image_directory');
		
		// Check if folder exists and create if not
		if (!is_dir(DIR_IMAGE . $imageDir . "/" . $this->customer->getId() . "/")) {
			mkdir(DIR_IMAGE . $imageDir . "/" . $this->customer->getId() . "/", 0755);
		}
  		
  		$key = array_search($fileName, $this->session->data['multiseller']['files']);
  		//strip nonce and timestamp
  		$original_file_name = substr($fileName,strpos($fileName,'.')+1,mb_strlen($fileName));

		if ($this->_isNewUpload($fileName)) {
			$newpath = $imageDir . "/" . $this->customer->getId() . "/" . $fileName;
			rename(DIR_IMAGE . $this->tmpPath . $fileName,  DIR_IMAGE . $newpath);
		}
		
  		unset ($this->session->data['multiseller']['files'][$key]);
  		
  		return $newpath;
  	}	
	
  	public function deleteDownload($fileName) {
  		if (empty($fileName))
  			return false;

		$key = array_search($fileName, $this->session->data['multiseller']['files']);
  		
		if (file_exists(DIR_DOWNLOAD . $fileName)) {
			unlink(DIR_DOWNLOAD. $fileName);
		}
		
		unset ($this->session->data['multiseller']['files'][$key]);
  	}

  	public function deleteImage($fileName) {
  		if (empty($fileName))
  			return false;

		$key = array_search($fileName, $this->session->data['multiseller']['files']);
  		
		if (file_exists(DIR_IMAGE. $fileName)) {
			unlink(DIR_IMAGE. $fileName);
		}
		
		unset ($this->session->data['multiseller']['files'][$key]);  		
  	}

  	public function getPdfPages($fileName) {
  		$pages = 0;
  		
  		//var_dump(DIR_DOWNLOAD . $fileName);
  		//var_dump(file_exists(DIR_DOWNLOAD . $fileName));
  		if (file_exists(DIR_IMAGE . $this->tmpPath . $fileName)) {
  			$filePath = DIR_IMAGE . $this->tmpPath . $fileName;
  		} else if (file_exists(DIR_DOWNLOAD . $fileName)) {
  			$filePath = DIR_DOWNLOAD . $fileName;
  		} else {
  			return;
  		}
		//$ext = explode('.', $fileName); $ext = end($ext);

		//if (strtolower($ext) == 'pdf') {
			$im = new imagick($filePath);
			$pages = $im->getNumberImages() - 1;
  		//} else {
  		//}
  		
  		return $pages;
  	}
	
  	public function generatePdfImages($fileName, $filePages) {
  		$pages = 0;
  		$json = array();
  		
  		if (file_exists(DIR_IMAGE . $this->tmpPath . $fileName)) {
			if (preg_match('/[^-0-9,]/', $filePages)) {
				$json['errors'][] = $this->language->get('ms_error_product_invalid_pdf_range');
			} else {
				$offsets = explode(',',$filePages);
				foreach ($offsets as $offset) {
					if (!preg_match('/^[0-9]+(-[0-9]+)?$/', $offset)) {
						$json['errors'][] = $this->language->get('ms_error_product_invalid_pdf_range');
						break;
					}
				}
			}

			if (!empty($json['errors'])) {
				return $json;
			}
			
			$pathinfo = pathinfo(DIR_IMAGE . $this->tmpPath . $fileName);
			$list = glob(DIR_IMAGE . $this->tmpPath . $pathinfo['filename'] . '*\.png');
			//var_dump($list);
			foreach ($list as $pagePreview) {
				//var_dump('unlinking ' . $pagePreview);
				@unlink($pagePreview);
			}

			$name = DIR_IMAGE . $this->tmpPath . $fileName . "[" . $filePages . "]";
			$im = new imagick($name);
			$pages = $im->getNumberImages();

			$im->setImageFormat( "png" );
			$im->setImageCompressionQuality(100);

			$pathinfo = pathinfo(DIR_IMAGE . $this->tmpPath . $fileName);
			$json['token'] = substr($pathinfo['basename'], 0, strrpos($pathinfo['basename'], '.'));
	
			if ($im->writeImages(DIR_IMAGE . $this->tmpPath . $pathinfo['filename'] . '.png', false)) {
				$list = glob(DIR_IMAGE . $this->tmpPath . $pathinfo['filename'] . '*\.png');
				foreach ($list as $pagePreview) {
					$pathinfo = pathinfo($pagePreview);
					$this->session->data['multiseller']['files'][] = $pathinfo['basename'];
					
					$thumb = $this->resizeImage($this->tmpPath . $pathinfo['basename'], $this->config->get('msconf_image_preview_width'), $this->config->get('msconf_image_preview_height'));
					$json['images'][] = array(
						'name' => $pathinfo['basename'],
						'thumb' => $thumb
					);
				}
				//var_dump($this->session->data['multiseller']['files']);
				return $json;
			}
		}
  	}
	
  	public function resizeImage($filename, $width, $height) {
  		if (!file_exists(DIR_IMAGE . $filename) || !$filename) {
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
	
  	public function getTmpPath () {
  		return $this->tmpPath;
  	}  	
}
?>