<?php

/*
    Smart Replacer for webMacht
    weblime.limetech.ru
*/    

    class Smarter
        {
        private $tpl, $db;
        private $dir = SMART_DIR;
        
        function FindExp()
            {
                
                $tpl = $this->tpl->Execute();
                $raz[0] = "{";
                $raz[1] = "}";
                preg_match_all("/\\{[^\\}]+\\}/s",$tpl , $m);
                if ($m) 
                    {
                        
                        foreach($m[0] as $k => $v)
                            {
                                $v = strtolower(strtr($v,array('{'=>'','}'=>'')));                               
                               
                                $R = explode('=',$v);
                                $name = $R[0];                              
                               
                                if (file_exists($this->dir.$name.'.php'))
                                    {
                                   
                                        include_once($this->dir.$name.'.php');                                       
                                        $cl = 'Replacer_'.$name;
                                        $class = new $cl($this->db);                                        
                                        $this->tpl->Replace(strtoupper($v),$class->Route($R[1]));
                                        
                                        
                                    }
                              //  unset($R,$class);
                            }
                        
                    }
              
              
                
            }
        
        function __construct() 
             {                
                $this->tpl = Registry::getInstance()->offsetGet('Template');
                $this->db = Database::getInstance();             
               
             }
             
             
        
            
            
        }



?>