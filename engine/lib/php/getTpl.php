<?php

    $ID = intval($_POST['id']);
    $file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/template/st/partner_offers_address.html');
    echo strtr($file, array('{ID}' => $ID));

?>