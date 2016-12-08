<?php

function Memory() {

	if (function_exists('memory_get_usage')) {
		$mem_usage = memory_get_usage(false);

		if ($mem_usage < 1024) {
			$memory_usage = $mem_usage." bytes";
		} elseif ($mem_usage < 1048576) {
			$memory_usage = round($mem_usage/1024, 2)." KBytes [$mem_usage]";
		} else {

			$memory_usage = round($mem_usage/1048576, 2)." MBytes [$mem_usage]";
		}
	} else {
		$memory_usage = 'ERROR';
	}

	return $memory_usage;
}

function prepareString($str) {

	return trim(strip_tags($str));

}

function setStatus($uid, $set) {

	$file = INDEX_DIR.'/cache/lock/'.$uid.'.lock';
	if ($set) {unlink($file);
	} else {
		file_put_contents($file, 1);

	}
}

function getStatus($uid) {
	$file = INDEX_DIR.'/cache/lock/'.$uid.'.lock';
	return !file_exists($file);

}

function prepareJSON($json) {

	$json = str_replace('u0022', '\"', $json);
	//$json = str_replace("'", "\'", $json);
	return preg_replace('/[\r\n]+/', "", $json);

}

function Mailx($to, $title, $message) {

	require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class/phpmailer/PHPMailerAutoload.php';
	$mail          = new PHPMailer();
	$mail->CharSet = "UTF-8";
	/*  $mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host       = "s02.atomsmtp.com"; // SMTP server
	$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Host       = "s02.atomsmtp.com"; // sets the SMTP server
	$mail->Port       = 2525;                    // set the SMTP port for the GMAIL server
	$mail->Username   = "synergy@macrox.ru"; // SMTP account username
	$mail->Password   = "AoYsgZrceNaQk8";        // SMTP account password*/
	$mail->SetFrom('synergy@macrox.ru', "A1 Robot");
	$mail->AddReplyTo('synergy@macrox.ru', "A1 Robot");
	$mail->AddAddress($to, $to);
	$mail->Subject = $title;
	$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";// optional, comment out and test

	$msg = strtr(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/template/mail.html'), array(
			'{TITLE}' => $title,
			'{TEXT}'  => $message,
		));

	$mail->MsgHTML($msg);
	$mail->Send();

}

function DecodeJSON($json) {return json_decode($json, true);}

function geoGet($city = 415, $address) {

	$D    = Database::getInstance();
	$City = $D->One("SELECT name FROM `WL_Cities` WHERE `id` = $city");

	$params = array(
		'geocode' => $City['name'].', '.$address, // адрес
		'format'  => 'json', // формат ответа
		'results' => 1, // количество выводимых результатов
	);

	$response = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?'.http_build_query($params, '', '&')));
	if ($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0) {
		$data  = explode(' ', $response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
		$coord = $data[1].','.$data[0];
		return $coord;
	} else {
		return '1.0,1.0';

	}
}

function getlink($row) {

	$x = '';

	if ($row['object'] == '/') {$x = '';
	} else {

		$alias = explode(':', $row['object']);
		$alias = end($alias);

		$Q = Database::getInstance()->One("SELECT alias,parent FROM `WL_Content` WHERE `alias` = '$alias'");

		if ($Q['parent'] == '0') {$x = $Q['alias'].'/';
		} else {
			$x = $Q['parent'].'/'.$Q['alias'].'/';
		}

		if ($Q['alias'] == 'system') {$x = '';

		}
	}

	return $x;

}

function EncodeJSON($array) {return json_encode($array, JSON_UNESCAPED_UNICODE);}

function Secure($username, $password) {
	return sha1(md5($password).md5($username).strlen($password.$username));
}

function encode_json($array) {
	return addslashes(json_encode($array, JSON_UNESCAPED_UNICODE));
}

function GetIP() {
	if (getenv("HTTP_CLIENT_IP")) {
		$ip = getenv("HTTP_CLIENT_IP");
	} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	} else {
		$ip = getenv("REMOTE_ADDR");
	}

	return $ip;
}

function GeneratePassword($length = 8) {
	$sym  = array("a", "A", "b", "B", "d", "D", "e", "E", "F", "f", "G", "g", "J", "j", "K", "k", "M", "m", "P", "p", "Q", "q", "r", "R", "S", "s", "t", "T", "U", "u", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9");
	$pass = null;
	for ($i = 0; $i < $length; $i++) {
		$pass .= $sym[rand(0, sizeof($sym)-1)];
	}
	return $pass;
}

function Error($code, $text) {
	if ($code == 404) {header("HTTP/1.0 404 Not Found");
	}

	$error = file_get_contents('System/Library/Templates/error.html');
	$error = str_replace('#E', '#'.$code, $error);
	$error = str_replace('{TEXT}', $text, $error);
	die($error);

}

function ValidSess($status) {

	if ((isset($_SESSION['admID'])) and ($_SESSION['admID']['status'] == $status)) {return true;
	} else {
		return false;

	}
}

function getCity($cid) {

	$M = iMemcache::getInstance();

	$value = $M->Get('city'.$cid);
	if (!$value) {

		$D = Database::getInstance();
		$Q = $D->One("SELECT name,name_en FROM `WL_Cities` WHERE `id` = $cid");

		$value = array('ru' => $Q['name'], 'en' => $Q['name_en']);
		$M->Set('city'.$cid, $value);

	}

	return $value;
}

?>