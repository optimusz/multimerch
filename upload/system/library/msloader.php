<?php

class MsLoader {
   	public function __construct($registry) {
		$this->registry = $registry;		
		spl_autoload_register(array('MsLoader', '_autoloadLibrary'));
		spl_autoload_register(array('MsLoader', '_autoloadController'));
		//require_once(DIR_APPLICATION . 'controller/seller/account-controller.php');
   	}

	public function __get($class) {
		if (!isset($this->$class)){
			$this->$class = new $class($this->registry);
		}

		return $this->$class;		
	}

	/*
	public function __set($class) {
		$this->$class = new $class($this->registry);
		return $this->$class;		
	}
	*/
	private function _autoloadLibrary($class) {
	    $file = DIR_SYSTEM . 'library/' . strtolower($class) . '.php';
	    if (file_exists($file)) {
	    	require($file);
	    }
	}

	private function _autoloadController($class) {
		preg_match_all('/((?:^|[A-Z])[a-z]+)/',$class,$matches);
	    $file = DIR_APPLICATION . 'controller/' . strtolower($matches[0][1]) . '/' . strtolower($matches[0][2]) . '.php';
	    if (file_exists($file)) {
	    	require($file);
	    }
	}
/*
	public function get($class) {
		if (!isset($this->$class)){
			$this->$class = new $class($this->registry);
		}

		return $this->$class;		
   	}

	public function create($class) {
		return new $class($this->registry);
   	}
   */
}

?>