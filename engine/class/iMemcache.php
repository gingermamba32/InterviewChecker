<?php
/* MemCacher for webMacht  */    
       
    class iMemcache
        {
           
            private $lt = 1800,$obj;
            protected static $instance;
            
             function __construct() 
             {  
                
                $this->obj = new Memcache;
                $this->obj->connect('localhost');
                
                
             }
            
        
            function Set($key, $value, $lt = 1800)
                {
                   
                   $this->obj->set('skidki_'.$key,$value,false,$lt);
                    
                }
            
            function Remove($key)
                {
                    
                    $this->obj->delete('skidki_'.$key);
                    
                }
                
           function Flush() {
            
            $this->obj->flush();
            
           }
           
                
            function Get($key)
                {
                    return $this->obj->get('skidki_'.$key);            
                }
          
              private function __clone()    {  }  

        public static function getInstance() {    
        if ( is_null(self::$instance) ) {
            self::$instance = new iMemcache;
        }
        return self::$instance;
    }
                
            
        }
    
?>