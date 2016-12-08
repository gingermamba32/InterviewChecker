<?php

Class Error {

	function __construct($r) {}
	static function Call($code, $desc, $file = '', $line = '', $context = array()) {

		echo '<div class="sysError" style="margin: 5px; padding: 5px; background: maroon; color:white !important;">
                <b>#'.$code.'</b> : '.$desc.'<br />
                <b>File : '.$file.'</b> : line '.$line.'
            </div>';

	}

}

?>