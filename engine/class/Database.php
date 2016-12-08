<?php

class QueryObject {

	private $link;
	function __construct($c) {$this->link = $c;}
	function Records() {return mysqli_num_rows($this->link);}
	function Parse() {return $this->link->fetch_assoc();}
	function Close() {$this->link->close();return true;}
	function Link() {return $this->link;}

}

class Database implements Singleton {
	private $db;
	var $querys = 0;
	var $mysql  = '';
	protected static $instance;

	private function __construct() {

		$c        = Config::getInstance()->get();
		$this->db = new mysqli($c['Database']['Host'], $c['Database']['Username'], $c['Database']['Password'], $c['Database']['Name']);
		if (!$this->db) {Error(103, 'Error estabilishing database connection');
		} else { $this->db->query("SET NAMES 'utf8'");return true;}

	}

	public function Query($query) {

		if (Debug) {
			$this->querys++;
			$this->mysql .= $query."<br/>\n";
		}

		return new QueryObject($this->db->query($query));

	}

	public function getOne($table, $array) {

		$where = array();

		foreach ($array as $key => $value) {

			if (is_string($value)) {$value = "'$value'";
			}

			$where[] = '`'.$key.'` = '.$value;

		}

		$where = implode(' AND ', $where);

		return $this->One("SELECT * FROM `".Prefix."$table` WHERE $where");

	}

	public function One($query) {
		if (Debug) {
			$this->querys++;
			$this->mysql .= $query."<br/>\n";
		}

		return mysqli_fetch_array($this->db->query($query.' LIMIT 1;'));

	}

	public function Disconnect() {$this->db->close();return true;}

	private function __clone() {}

	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new Database;
		}
		return self::$instance;
	}

}

?>