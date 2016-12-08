<?php

    class iSMS {
            
     
      private $url = 'http://json.gate.iqsms.ru/send/';
      
      function send_iq($to, $msg)
        {
            $ch = curl_init();
            
            $array['login'] = '330ufafa2411';
            $array['password'] = '547227';
            $array['messages'] = array(array('clientId' => time(), 'phone' => $to, 'text' => $msg, 'sender' => 'SkidkiToday'));
            
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_HEADER,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);   
            curl_setopt($ch, CURLOPT_POST, true);        
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($array));   
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, "SKIDKI.TODAY/1.0");
            curl_setopt($ch, CURLOPT_VERBOSE,1);
            return $data = curl_exec($ch);

        }
        
      function send_smsc($to, $msg) {
            
            
            return file_get_contents('http://smsc.ru/sys/send.php?login=synergy@macrox.ru&psw='.md5('GbDZhG').'&phones='.$to.'&sender=SkidkiToday&charset=utf-8&mes='.$msg);
            
        }
        
        
      
      function send($to, $msg)   
            {
                
                return $this->send_iq($to, $msg);
                
                
                
            }
            
    }

?>