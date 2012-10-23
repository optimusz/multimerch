<?php

class MsLoader {
   	public function __construct($registry) {
		$this->registry = $registry;
		spl_autoload_register(array('MsLoader', '_autoload'));
   	}

	private function _autoload($class)
	{
	    $file = DIR_SYSTEM . 'library/' . strtolower($class) . '.php';
	    if (file_exists($file)) {
	    	require($file);
	    }
	}

	public function get($class) {
		if (!isset($this->$class)){
			$this->$class = new $class($this->registry);
		}

		return $this->$class;		
   	}
}

?>