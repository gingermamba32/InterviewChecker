<?php

    include('_base.php');
    
    $ID = intval($_POST['id']);
    $H0 = $_POST['hash'];    
    if ($_SESSION['partner']['parent'] > 0) $AID = $_SESSION['partner']['parent']; 
        else $AID = $_SESSION['partner']['id'];
    
    $PC = $D->One("SELECT * FROM `WL_Promocode` WHERE `id` = $ID");
    if ($PC) {
        
        $H1 = sha1($PC['id'].md5($PC['code']));
        if ($H0 == $H1) {
            
            $D->Query("DELETE FROM `WL_Promocode` WHERE `id` = $ID");
            $D->Query("INSERT INTO `WL_Promolog` VALUES (NULL, UNIX_TIMESTAMP(), $AID, {$_SESSION['partner']['id']}, {$PC['offer']} , {$PC['code']}, {$PC['phone']})");
         
             
            
            echo 'OK';
            
        } else echo "SE1";
        
    }

?>