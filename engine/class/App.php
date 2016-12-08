<?php

abstract Class App {

	protected $conf, $tpl, $db, $cache, $model;

	function __construct() {
		$r           = Registry::getInstance();
		$this->conf  = $r;
		$this->db    = $r['Database'];
		$this->tpl   = $r['Template'];
		$this->cache = $r['Cache'];
		$this->model = $r['Model'];

	}

	abstract function indexAction();

}

?>