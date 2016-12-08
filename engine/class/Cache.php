<?php

class Cache implements Singleton {
	protected static $instance;
	private $lt  = 3600;
	private $dir = CACHE_DIR;

	private function __construct() {

		if (!is_writable($this->dir)) {
			return true;
			Error(900, 'Cache directory is not writable!');
		} else {
			return false;

		}
	}

	public function CacheIt($name, $value) {

		if (is_array($value)) {$value = EncodeJSON($value);
		}

		$file = fopen($this->dir."$name.cache", "w");
		fwrite($file, $value);
		fclose($file);

	}

	public function Clean() {

		if ($handle = opendir($this->dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					unlink($this->dir.$file);
				}
			}
			closedir($handle);
		}

	}

	public function Check($name) {

		if (file_exists($this->dir.$name.'.cache')) {
			if ((time()-filemtime($this->dir.$name.'.cache')) < $this->lt) {return true;
			} else {
				return false;
			}
		} else {
			return false;

		}
	}

	public function AsArray($name) {
		if (file_exists($this->dir.$name.'.cache')) {

			$m = file_get_contents($this->dir.$name.'.cache');
			if ($m) {

				return DecodeJSON($m);

			} else {
				return false;

			}
		} else {
			return false;
		}
	}

	public function AsString($name) {
		if (file_exists($this->dir.$name.'.cache')) {

			return file_get_contents($this->dir.$name.'.cache');

		} else {
			return false;
		}
	}

	private function __clone() {}

	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new Cache;
		}
		return self::$instance;
	}

}

?>