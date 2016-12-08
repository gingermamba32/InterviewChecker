<?php
  
    class Template
        {
                private $page,$reg;
                private $replace = array(); 
                      
                private function toKey($k) { return '{'.strtoupper($k).'}';}
                private function specialTags()
                    {
                        
                          preg_match_all("/\\{%INCLUDE:[^\\}]+\\%}/s",$this->page , $m);
                         // var_dump($m);
                         if ($m) 
                            {
                        
                            foreach($m[0] as $k => $v)
                            {
                                $vv = strtolower(strtr($v,array('{%INCLUDE:'=>'','%}'=>'')));                               
                                $str = file_get_contents(THEME_DIR.TPL.'/'.$vv.'.html');
                                $this->DirectReplace($v,$str); 
                                
                            }
                            
                             unset($str,$vv,$m);
                            }
                            
                           
                        
                    }
                        
                   
                    
                function __construct() 
                    {
                        $reg = Registry::getInstance();
                        
                        if (!file_exists(THEME_DIR.$reg['Template'].'/template.html')) die('Theme Not Found');                        
                        $this->page = file_get_contents(THEME_DIR.$reg['Template'].'/template.html');
                        $this->replace[$this->toKey('Js')] = array();
                        $this->reg = $reg;
                       
                    
                    }
                
                function Change($theme){$this->page = file_get_contents(THEME_DIR.TPL.'/template_'.$theme.'.html');}
                
                function SetManage($type)
                    {
                        
                        $this->page = file_get_contents(THEME_DIR.'manage/'.$type.'.html');
                        if (!defined('ATPL')) define('ATPL','manage');
                        
                        
                    }
            
                function Replace($key, $value){$this->replace[$this->toKey($key)] = $value;}                
                function DirectReplace($key, $value){$this->page = str_replace($key, $value, $this->page);}                
                function ToReplace($array){foreach ($array as $key => $value){$this->Replace($key, $value);}}                
                function Execute()
                {
                    
                   $this->specialTags();
                    
                    if (strstr($this->replace['{JS}'],'Array')) $this->replace['{JS}'] = str_replace('Array','',$this->replace['{JS}']);
                    
                   foreach ($this->replace as $key => $value)
                    {
                        
                       if ((is_object($this->replace[$key])) or (is_array($this->replace[$key]))) unset($this->replace[$key]);
                    
                        
                    }
                                        
                    $t = strtr($this->page,$this->replace);
                    $tpl = TPL;
                    if (defined('ATPL')) $tpl = ATPL;
                   
                    
                    $this->page = strtr($t, array(
                        '{THEME}' => '/'.THEME_DIR.$tpl.'/',
                        '{YEAR}' => date('Y'),
                    ));
                    
                    return $t;
                    
                }
                
                function Title($title) { $this->replace[$this->toKey('Title')] = $title." / ".$this->replace[$this->toKey('Title')]; }
                        
                function AssignJS($js){
                    for ($i = 0; $i<count($js); $i++)
                        $this->replace[$this->toKey('Js')] .= "<script type=\"text/javascript\" src=\"/static/js/".(string)$js[$i].".js?".filemtime(INDEX_DIR."/static/js/{$js[$i]}.js")."\"></script>\n"; 
                                
                         
                                                     
                }
                
                     
                function Module($index)
                    {                       
                       
                      // $reg = Registry::getInstance();
                        
                        $Mod = Registry::getInstance()['Module'];
                        $filex = THEME_DIR.TPL.'/'.$Mod.'_'.$index.'.html';
                    
                      if (file_exists($filex))
                        {                        
                        
                           return file_get_contents($filex);
                            
                        } else Error(999, 'Template '.$filex.' not found');
                       
                    }
                    
                function Clear(){$this->page = null;return true;}
        }
?>