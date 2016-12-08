<?php

class Config implements Singleton {
	protected static $instance;
	protected $c;

	private function __construct() {

		$this->c = json_decode(file_get_contents(INDEX_DIR.'/'.CONFIG_DIR.'this.configuration'), true);
		if (!$this->c) {error(100, 'Configuration Error');
		}

		if ($this->c['System']['Debug'] == 'Yes') {
			define('Debug', true);
			ini_set('display_errors', 1);
			error_reporting(8191);

		} else {
			define('Debug', false);
		}

		define('_SYSTEM', '3.0');
		define('Prefix', $this->c['Database']['Prefix']);

	}
	private function __clone() {}

	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new Config;
		}
		return self::$instance;
	}
	public function get() {return $this->c;}
}

?>