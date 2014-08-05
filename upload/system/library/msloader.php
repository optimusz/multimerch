<?php

class MsLoader {
	public $appVer = "6.0";
	public $dbVer = "1.0.0.0";
	
	public function __construct($registry) {
		$this->registry = $registry;
		spl_autoload_register(array('MsLoader', '_autoloadLibrary'));
		spl_autoload_register(array('MsLoader', '_autoloadController'));
	}

	public function __get($class) {
		if (!isset($this->$class)){
			$this->$class = new $class($this->registry);
		}

		return $this->$class;
	}

	private static function _autoloadLibrary($class) {
	 	$file = DIR_SYSTEM . 'library/' . strtolower($class) . '.php';
		if (file_exists($file)) {
			require_once(VQMod::modCheck($file));
		}
	}

	private static function _autoloadController($class) {
		preg_match_all('/((?:^|[A-Z])[a-z]+)/',$class,$matches);
		
		if (isset($matches[0][1]) && isset($matches[0][2])) {
			$file = DIR_APPLICATION . 'controller/' . strtolower($matches[0][1]) . '/' . strtolower($matches[0][2]) . '.php';
			if (file_exists($file)) {
				require_once(VQMod::modCheck($file));
			}
		}
	}
}

?>
