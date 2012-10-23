<?php

class MsLoader {
   	public function __construct($registry) {
		$this->registry = $registry;
   	}

	private function _getClass($class) {
		switch ($class){
			case '';
				$path = 'catalog/controller/';
			default:
		        $path = DIR_SYSTEM . 'library/';//;//'system/library/';
		}
		return $path . strtolower($class) . '.php';
	}

	public function get($class) {
		//echo memory_get_usage() . "<br \>";		
		$file = $this->_getClass($class);
		//var_dump($file);
		if (!class_exists($class))
			include_once($file);
		
		$a = new $class($this->registry);
		//echo memory_get_usage() . "<br \><br \>";
		return $a;
		
		
		return new $class($this->registry);
   	}
}

?>