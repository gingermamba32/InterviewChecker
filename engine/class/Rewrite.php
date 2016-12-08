<?php

class Rewrite
    {
        private $a = array();
        private $modules = array('index','api','partner');
        
        function __construct()
            {
                
                 $C = Cache::getInstance();    
                 $D = Database::getInstance();
    
    
                  if (!$C->Check('Rewrite'))
                    {
   
                        $Q = $D->Query('SELECT * FROM `'.Prefix.'Rewrite` ORDER BY `id`');
                        if (($Q->Link()) and ($Q->Records()))
                        {            
                            while ($list = $Q->Parse())
                            {                                    
                                $this->a[$list['to']] = $list['from'];                    
                            }   
                               
                        }
                        $C->CacheIt('Rewrite',$this->a);
                        } else $this->a = $C->AsArray('Rewrite');
                
            }
            
        function find()
            {
                $A = Registry::getInstance();
                $Z = $this->a;
                
                if (!isset($_GET['to'])) 
                    {
                        $_GET['to'] = $A['Default'];
                        $e = explode('/',$_GET['to']);         
                        $A['Module'] = $e[0];
           
                    } else  {            
                        $e = explode('/',$_GET['to']);          
                        if (isset($Z[$_GET['to']]))
                            {
                                $_GET['to'] = $Z[$_GET['to']];
                                $e = explode('/',$_GET['to']);                    
                            }
                      
                        $A['Module'] = $e[0];
                    }
                    
                    
                 if (!in_array($A['Module'], $this->modules))
                    {
                        
                        $_GET['to'] = 'page/structure/'.$_GET['to'];
                        $A['Module'] = 'page';
                        
                    }
                
            }
        
    }

?>