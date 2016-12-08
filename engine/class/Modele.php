<?

class Modele {

	function __construct() {}

	function __call($name, $args) {

		$mdl = MODEL_DIR.$name.'.php';

		if (!file_exists($mdl)) {return false;
		} else {

			include_once ($mdl);

			$mdxn = $name.'Model';
			$mdx  = new $mdxn();
			return $mdx;

		}

	}

}

?>