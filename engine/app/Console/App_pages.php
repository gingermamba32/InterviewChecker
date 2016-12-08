<?php

class Console Extends Console_App 
    {
       
        
        
        function index($uri)
            {
                if (!isset($uri[0])) $uri[0] = NULL;
                switch ($uri[0])
                    {
                        
                        case NULL: $this->options = $this->main(); break;
                        case 'new': $this->options = $this->add(); break;
                        
                    }
                 return $this->options; 
            }
            
        function main()
            {                
               $x = array(
                'title'=>'Статические страницы',  
                   
                'links'=> array(
                        0 => array('Добавить страницу','Add','new')
                       
                ),
                'list' => array('type' => 'sub','items' => array())
                
                
                );
                
                $Q = $this->D->Query('SELECT alias,name,edited FROM `'.Prefix.'Content` ORDER BY name;');
                $k = 0;
               
                $x['list']['items']['head'] = array('name'=>'Заголовок','update'=>'Изменено');
               
                while ($row = $Q->Parse())
                    {
                        
                        $x['list']['items']['body'][$row['alias']]['name'] = $row['name']; 
                        $x['list']['items']['body'][$row['alias']]['update'] = date('d.m.Y H:i',$row['edited']);
                    }
               
               return $x; 
            } 
            
            
        function add()
            {
                
               
                $x = array(
                'title'=>'Добавить новую страницу',  
                   
                'links'=> array(
                        0 => array('Отмена','View',NULL)
                       
                ),
                'list' => array('type' => 'sub','items' => array())
                
                
                );
                
             
               
               return $x; 
               
            }
            
            
        
    }
?>