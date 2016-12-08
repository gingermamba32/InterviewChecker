<?php

class Console Extends Console_App 
    {
       
        
        
        function index($uri)
            {
                if (!isset($uri[0])) $uri[0] = NULL;
                switch ($uri[0])
                    {
                        
                        case NULL: $this->options = $this->main(); break;
                        
                    }
                 return $this->options; 
            }
            
        function main()
            {                
               $x = array(
                'title'=>'Новости',              
                'links'=> array(
                        0 => array('Добавить новость','Add','add')
                ));
               
               
               return $x; 
            }   
            
            
        
    }
?>