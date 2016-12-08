<?php

class Console Extends Console_App 
    {
       
        
        
        function index($uri)
            {
            
            
               $text = '';
               
               $w = new CTemplater();
               
               $w->h1('WebMacht');
               $w->insert('Версия',_SYSTEM);
               $w->insert('Дата обновления',date('d.m.Y',filemtime('engine/BootLoader.php')));
               $w->insert('Разработка','<a href="http://www.renua.ru/">Renua</a>');
               $w->br();
               $w->h1('Сервер');
               $w->insert('IP сервера',$_SERVER['SERVER_ADDR']);              
               if ( ini_get('memory_limit'))   $w->insert('PHP огран.памяти',ini_get('memory_limit'));   
               if ( ini_get('post_max_size'))   $w->insert('Макс. размер POST',ini_get('post_max_size')); 
               if ( ini_get('upload_max_filesize'))   $w->insert('Макс. размер файла',ini_get('upload_max_filesize'));     
               $Q = $this->D->One('SELECT VERSION()');  
               $w->insert('Версия MySQL',$Q['VERSION()']); 
               $w->insert('Версия PHP',phpversion());   
               
               $text = $w->proceed();
               
               
               
               $x = array('title'=>'Системная информация','html'=>$text);             
               return $x; 
            }
            
      
            
        
    }
?>