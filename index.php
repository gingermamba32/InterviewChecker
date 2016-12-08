<?php
   
    define('CONFIG_DIR','config/');
    define('APP_DIR',   'engine/app/');
    define('CLASS_DIR', 'engine/class/');
    define('THEME_DIR', 'template/');
    define('CACHE_DIR', 'cache/');
    define('MODEL_DIR', 'engine/models/');
    define('SMART_DIR', 'engine/lib/smart/');
    define('INDEX_DIR', $_SERVER['DOCUMENT_ROOT']);        
    define('_LANG','ru');    
   
    define('DOMAIN_CDN', 'http://'.$_SERVER['HTTP_HOST'].'/a/');
    
    include_once('engine/BootLoader.php');        
   
?>