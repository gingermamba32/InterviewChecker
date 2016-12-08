<?php

    include('_base.php');    
    $array = array('code' => 0, 'invalid' => null);    
    $type = $_POST['type'];
    
    
    $strings = explode("\n", file_get_contents('forms/'.$type.'.csv'));
    $allow = 1;
    
    for ($i = 0;$i<count($strings);$i++)
        {
            
            if ($strings[$i])
                {
                    
                    
                    $data = explode(',',$strings[$i]);
                    
                    switch ($data[1])
                        {
                            
                            case 'string': { if (strlen($_POST[$data[0]]) < $data[2]) { $allow = 0; $array['invalid'][] = $data[0]; }   break;}
                            case 'int': { if (!is_numeric($_POST[$data[0]])) { $allow = 0; $array['invalid'][] = $data[0]; }   break; }
                            case 'email': { if(!filter_var($_POST[$data[0]], FILTER_VALIDATE_EMAIL))  { $allow = 0; $array['invalid'][] = $data[0]; }   } break;
                            
                        }
                    
                    
                }
            
        }
    
    
    
    if ($allow) {
        
        $array['code'] = 1;
        
        $names = array('expert' => 'ЗАЯВКА-ПРЕДЛОЖЕНИЕ СУБПОДРЯДА', 'zea' => 'ЗАЯВКА НА ЭНЕРГОАУДИТ', 'epzv' => 'ЗАЯВКА-ПРЕДЛОЖЕНИЕ СУБПОДРЯДА');
      
         
        include('../../class/Smtp.php');
        
        $tpl = file_get_contents('forms/'.$type.'.html');
        
        foreach ($_POST as $key => $value)
            {
                
                
                $tpl = str_replace('{'.strtoupper($key).'}', $value, $tpl);
                
                
            }
            
        
        
        $pref = json_decode(file_get_contents('../../../cache/preferences.cache'),true);        
        $pref = explode(',', urldecode($pref['Notify']));
        
        
        for ($i = 0; $i<count($pref); $i++)
            {
                
                smtpmail($pref[$i], $names[$type], $tpl);
                
            }
        
        
        
        
    }
    
    
    echo json_encode($array);
    
    

?>