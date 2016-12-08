<?php

    error_reporting(0);    
    session_start();

    include($_SERVER['DOCUMENT_ROOT'].'/engine/Functions.php');
    

    define('CONFIG_DIR','config/');
    define('APP_DIR',   'engine/app/');
    define('CLASS_DIR', 'engine/class/');
    define('THEME_DIR', 'template/');
    define('CACHE_DIR', 'cache/');
    define('MODEL_DIR', 'engine/models/');
    define('SMART_DIR', 'engine/lib/smart/');
    define('INDEX_DIR', $_SERVER['DOCUMENT_ROOT']);        
    define('_LANG','ru');  
    
    function __autoload($name) {  include(INDEX_DIR.'/'.CLASS_DIR.$name.'.php'); }          
    
    $Config = Config::getInstance()->get();    
    $D = Database::getInstance();

?>