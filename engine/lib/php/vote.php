<?php

    include('_base.php');
    
    $ip = GetIP();
    $vote = intval($_POST['vote']);
    
    if ($vote < 0) $vote = 0;
    if ($vote > 3) $vote = 3;
    
    $name = mysql_escape_string(strip_tags($_POST['name']));
    $text = mysql_escape_string(strip_tags($_POST['text']));
    
    $start = time() - 3600 * 24 * 30;
    
    
    $Q = $D->One("SELECT id FROM `WL_Votes` WHERE `date` > $start AND `ip` = '$ip'");
    
    if (!$Q)  
    {
    
        $D->Query("INSERT INTO `WL_Votes` VALUES(NULL, UNIX_TIMESTAMP(), $vote, '$name', '$text', '$ip');");
    
    }
    
     
   
?>