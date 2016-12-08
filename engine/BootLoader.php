<?php
    /*
    
        Product : reCMS[S]
        Author  : Synergy [Renua LLC]       
        File    : BoolLoader.php    
    
    */  
    
    error_reporting(0);    
    session_start();
    header('Content-Type: text/html; charset=utf-8');
    $startime = microtime(true);  
    
    
    $_GET['to'] = substr($_SERVER['REQUEST_URI'],1);
    
    
    
    function __autoload($name) {  include(CLASS_DIR.$name.'.php'); }            
    include_once('Functions.php');   
    $Config = Config::getInstance()->get();    
   
    $C = Cache::getInstance();    
    $D = Database::getInstance();
    $A = Registry::getInstance();
  
    $R = new Router($A); 
    $T = new Template($A);
    $A['Template'] = $T;
    $A['Cache'] = $C;
    $A['Database'] = $D;  
    $A['Model'] = new Modele; 
    $T->ToReplace($A->AsArray()); 
     
    $W = new Rewrite();
    $W->find();
    $T->AssignJS(array('JQuery','Functions'));
    $R->Run();      
    $D->Disconnect();
    $T->Replace('This',date('Y'));
    if (Debug) {
                    echo "<div id=\"wm_debug\" style=\"position: fixed; bottom:0;left:0;background:white;font-size:10px;padding:10px;opacity: 0.8;\">";
                    echo "Used memory:".  Memory()."<br />";
                    echo "Database Querys:". $D->querys."<br />";
                    echo "Database Querys Log:". $D->mysql."<br />";
                    $time_end = microtime(true);
                    $time = $time_end - $startime;
                    echo "Runned time:".round($time,5)." sec <br />";
                    echo "</div>";
                } 
    echo $T->Execute(); 
      
      
      
     
      
      
?>