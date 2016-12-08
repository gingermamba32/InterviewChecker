<?php

Class Router {

	private $reg;
	private $path;

	private $args = array();

	function __construct($reg) {
		$this->reg = $reg;
		$path      = APP_DIR;
		if (is_dir($path) == false) {
			Error(102, 'Invalid modules path: `'.$path.'`');
		}
		$this->path = $path;

	}

	private function getController(&$file, &$controller, &$action, &$args) {
		$route                     = (empty($_GET['to']))?'':$_GET['to'];
		if (empty($route)) {$route = 'index';}
		$route                     = trim($route, '/\\');
		$parts                     = explode('/', $route);

		$cmd_path = $this->path;

		foreach ($parts as $part) {
			$fullpath = $cmd_path.$part;
			if (is_dir($fullpath)) {
				$cmd_path .= $part.'/';
				array_shift($parts);
				continue;
			}

			if (is_file($fullpath.'.php')) {
				$controller = $part;
				array_shift($parts);
				break;
			}

		}

		if (empty($controller)) {$controller = 'index';};

		$action = array_shift($parts);

		if (empty($action)) {$action = 'index';}

		$file = $cmd_path.$controller.'.php';

		$args = $parts;

	}

	function Run() {
		$this->getController($file, $controller, $action, $args);

		$action .= 'Action';

		if (!is_readable($file)) {

			Error(404, '404 Not Found');

		}

		include ($file);

		$class      = 'App_'.$controller;
		$controller = new $class($this->reg);

		if (!is_callable(array($controller, $action))) {

			if (!isset($controller->def)) {Error(404, '404 Not Found');
			} else {

				$cds = $controller->def.'Action';

				if (!is_callable(array($controller, $cds))) {Error(404, '404 Not Found');
				} else { $controller->$cds(strtr($action, array('Action' => '')));}
			}

		} else {
			$controller->$action($args);

		}

	}
}

?>