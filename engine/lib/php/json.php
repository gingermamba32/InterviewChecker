<?php

    include('_base.php');
    $array = array();
    
    $array['address'] = prepareString(trim($_POST['address']));
    $array['map'] = geoGet(intval($_POST['city']), $array['address']);
    $array['days'] = implode(',',array(
        implode('-', $_POST['day'][0]),
        implode('-', $_POST['day'][1]),
        implode('-', $_POST['day'][2]),
        implode('-', $_POST['day'][3]),
        implode('-', $_POST['day'][4]),
        implode('-', $_POST['day'][5]),
        implode('-', $_POST['day'][6])));
    $array['phone'] = prepareString(trim($_POST['phone']));
    $array['phone2'] = prepareString(trim($_POST['phone2']));
    $array['mail'] = prepareString(trim($_POST['mail']));
    $array['site'] = prepareString(trim($_POST['site']));
    
    echo json_encode($array, JSON_UNESCAPED_UNICODE);

?>