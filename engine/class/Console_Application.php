<?php

Abstract Class Console_App {

	protected $D, $options;
	function __construct($D) {$this->D = $D;}
	abstract function index($uri);

}

class CTemplater {

	private $template;

	function h1($name) {

		$this->template .= '<h1>'.$name.'</h1>';

	}
	function br() {

		$this->template .= '<br />';
	}

	function insert($name, $value, $type = 'info', $edit = false) {

		switch ($type) {

			case "info":$this->template .= '<div class="info">
                        <div class="lx">'	.$name.'</div>
                        <div class="vx">'	.$value.'</div>
                    </div>'	;break;

		}

	}

	function proceed() {

		return $this->template;

	}
}

?>