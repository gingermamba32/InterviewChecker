<?php


class Registry implements ArrayAccess
    {
        private $a = array();  
        protected static $instance;
        
        function __construct()
            {
                 $C = Cache::getInstance();    
                 $D = Database::getInstance();
                 define('PREFS', 'preferences');
    
               
                 
    
                        $Q = $D->Query('SELECT * FROM `'.Prefix.'Configuration` ORDER BY `Key`');
                        if (($Q->Link()) and ($Q->Records()))
                            {
                           
                                    while ($row = $Q->Parse())
                                        {
                                            $this->a[$row['Key']] = $row['Value'];                   
                                          
                                        }
            
                            }
                        $C->CacheIt(PREFS,$this->AsArray());
            
                    
                    
                    
                define('TPL', $this->a['Template']);
                
            }
        
        function offsetSet($key, $value) {
            $this->a[$key] = $value;
                         
        }
        
        function offsetGet($key) {
            if ( array_key_exists($key, $this->a) ) return $this->a[$key];
        
        }
        
        function offsetUnset($key) {
            if ( array_key_exists($key, $this->a))  unset($this->a[$key]);        
        }
        
        function offsetExists($offset) {
            return array_key_exists($offset, $this->a);
        }
        
        function asArray() {              
            return $this->a;                
        }
        
        private function __clone() {  
            
        }  

        public static function getInstance() {    
            if ( is_null(self::$instance) ) {
                self::$instance = new Registry;
            }
            return self::$instance;
        }
      
    }

?>