<?php

    include('_base.php');
    error_reporting(8191);
    ini_set('display_errors', 1);
    
    $phone = prepareString($_POST['phone']);    
    $hash = md5($phone);
    $hash2 = md5(GetIP());
    $array = array('code' => 0,'text' => "Error");
    
    if ((strlen($phone) > 6) && (isset($_POST['x'])))
    {
    
        Mailx('info@skidki.today', 'Хочу к Вам подключиться','
        <p>Я очень хочу подключиться к Вашей системе, мой номер <b>'.$phone.'</b>. Жду.</p>');
        $array = array('code' => 1,'text' => "OK");
        
        include($_SERVER['DOCUMENT_ROOT'].'/engine/class/iSMS.php');
        $i = new iSMS();
        if ((!file_exists($_SERVER['DOCUMENT_ROOT'].'/cache/phone/'.$hash.'.phone')) && (!file_exists($_SERVER['DOCUMENT_ROOT'].'/cache/phone/'.$hash.'.ip'))){
            
            $me = array('уважаемый', 'дорогой', 'родной');
            $i->send('+79174000876','Хочу в Skidki.Today, позвони мне, '.$me[rand(0,2)].': '.$phone);
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/cache/phone/'.$hash.'.phone', '1');
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/cache/phone/'.$hash2.'.ip', '1');
        } 
    
    }
    echo json_encode($array);

?>