<?php


class App_api extends App {
    
    public $def = 'def';
    private $user, $action, $args, $phone;
    private $url = DOMAIN_CDN;
    private $expires = 86400;
    
    function indexAction() { die(); }
    
    function calcHashAction() {
        
        $H1 = $this->args['hash'];
        
        $URL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $Data = explode('&hash=', $_SERVER['REQUEST_URI']);
        if (count($Data) !=2) $Data = explode('?hash=', $_SERVER['REQUEST_URI']); 
       
        $H2 = md5($this->user.'.'.$this->action);
        if ($H1 != $H2) $this->out(array('status' => -3, 'error_text' => 'Invalid hash', 'NeedHash' => $H2));  
        
    }
    
    function api_check() {         
        $MAX = 0;        
        $lang = '';
        if (isset($this->args['lang'])) $lang = "WHERE `lang` = '{$this->args['lang']}'";        
        $Q = $this->db->One("SELECT MAX(updated) as max FROM `WL_Category` $lang");
        if ($MAX < $Q['max']) $MAX = $Q['max'];
        $Q = $this->db->One("SELECT MAX(updated) as max FROM `WL_Test` $lang");
        if ($MAX < $Q['max']) $MAX = $Q['max'];  
       // $TS = (int)file_get_contents('cache/test.sort');
       // if ($TS > $MAX) $MAX = $TS;      
        return array('updated' => $MAX);        
    }  
    
     
    function api_check_new() {         
        $MAX = 0;        
        $lang = '';
        if (isset($this->args['lang'])) $lang = "WHERE `lang` = '{$this->args['lang']}'";        
        $Q = $this->db->One("SELECT MAX(updated) as max FROM `WL_Category` $lang");
        if ($MAX < $Q['max']) $MAX = $Q['max'];
        $Q = $this->db->One("SELECT MAX(updated) as max FROM `WL_Test` $lang");
        if ($MAX < $Q['max']) $MAX = $Q['max'];  
        $TS = (int)file_get_contents('cache/test.sort');
        if ($TS > $MAX) $MAX = $TS;      
        return array('updated' => $MAX);        
    }  
    
    function zipsAction($arg) {
        $id = intval($arg[0]).'.';
        $A = scandir($_SERVER['DOCUMENT_ROOT'].'/z/');
        array_shift($A);
        array_shift($A);
        $files = array();
        foreach ($A as $file)             
            if (substr($file, 0, strlen($id)) == $id) unlink($_SERVER['DOCUMENT_ROOT'].'/z/'.$file);           
        
        
    }
    
    function api_images() {
        
        $array['URL_PREFIX'] = 'http://'.$_SERVER['HTTP_HOST'].'/z/';
        $id = intval($this->args['id']);
        $timestamp = intval($this->args['timestamp']);
        $file = INDEX_DIR.'/z/'.$id.'.'.$timestamp.'.zip';
        
        if (!file_exists($file)) {     
            $this->zipsAction(array($id));        
            $Q = $this->db->One("SELECT * FROM `WL_Test` WHERE `id` = $id");       
            $q1 = json_decode($Q['question'], true);
            $q2 = json_decode($Q['results'], true);
            $images = array();
            $images[] = $Q['icon'];
            $images[] = $Q['image'];
            foreach ($q1 as $q) foreach ($q['answers'] as $row) if ($row['image']) $images[] = $row['image'];
            foreach ($q2 as $row) if ($row['image']) $images[] = $row['image'];
            $zip = new ZipArchive;
            $zip->open($file, ZipArchive::CREATE);
            foreach ($images as $image)  $zip->addFile(INDEX_DIR.'/a/'.$image, $image);
            $zip->close();
               
        }
        
        header('Content-Type: application/zip');
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename="'.$id.'.'.$timestamp.'.zip"');
        readfile($file);
        die();
        return $array;
        
    }
 
    function api_get() {
        
        $array = array();        
        $array['URL_PREFIX'] = $this->url;        
        $lang = '';
        if (isset($this->args['lang'])) $lang = "WHERE `lang` = '{$this->args['lang']}'";           
        
        $Q = $this->db->Query("SELECT id,lang,name FROM `WL_Category` $lang ORDER BY lang,name");
        while ($row = $Q->Parse()) if (getStatus('category'.$row['id'])) $array['categories'][] = $row;
            
        $Q = $this->db->Query("SELECT * FROM `WL_Test` $lang ORDER BY lang,id");
        while ($row = $Q->Parse()) {            
            $testline = array();
            $testline['id'] = intval($row['id']);
            $testline['number'] = intval($row['iid']);
            $testline['lang'] = $row['lang'];
            $testline['categoryId'] = intval($row['category']);
            $testline['title'] = $row['title'];
            $testline['description'] = $row['description'];
            $testline['icon'] = $row['icon'];
            $testline['image'] = $row['image'];
            $testline['timestamp'] = (int)$row['updated'];
            $testline['type'] = intval($row['type']);
            $testline['questions'] = array();
            $que = json_decode($row['question'], true);
            foreach ($que as $key => $value) {                
                $arr = array();
                $arr['question'] = $value['question'];
                $arr['answers'] = array();                
                foreach ($value['answers'] as $answer) {                    
                   if ($answer['text'] && $answer['result'] != '') $arr['answers'][] = array(
                    'text' => prepareJSON($answer['text']),
                    'result' => intval($answer['result']),
                    'image' => $answer['image']
                   );
                    
                }              
                
                if ($value['question'] && count($value['answers'])) $testline['questions'][] = $arr;
                
            }            
            $testline['results'] = array();
            $res = json_decode($row['results'], true);
            foreach ($res as $result) {
                
                $testline['results'][] = array(
                'to' => intval($result['result']), 
                'text' => prepareJSON($result['text']),               
                'image' => $result['image']);
                
            }
            
            if ((getStatus('category'.$row['category'])) && (getStatus('test'.$row['id']))) $array['tests'][] = $testline;
            
        }
        
              
        return $array;        
    }
    
    
    function api_get_new() {
        
        $array = array();        
        $array['URL_PREFIX'] = $this->url;        
        $lang = '';
        if (isset($this->args['lang'])) $lang = "WHERE `lang` = '{$this->args['lang']}'";           
        
        $Q = $this->db->Query("SELECT id,lang,name FROM `WL_Category` $lang ORDER BY lang,name");
        while ($row = $Q->Parse()) if (getStatus('category'.$row['id'])) $array['categories'][] = $row;
            
        $Q = $this->db->Query("SELECT * FROM `WL_Test` $lang ORDER BY lang,ord,id");
        while ($row = $Q->Parse()) {            
            $testline = array();
            $testline['id'] = intval($row['id']);
            $testline['number'] = intval($row['iid']);
            $testline['lang'] = $row['lang'];
            $testline['categoryId'] = intval($row['category']);
            $testline['title'] = $row['title'];
            $testline['description'] = $row['description'];
            $testline['icon'] = $row['icon'];
            $testline['image'] = $row['image'];
            $testline['timestamp'] = (int)$row['updated'];
            $testline['type'] = intval($row['type']);
            $testline['questions'] = array();
            $que = json_decode($row['question'], true);
            foreach ($que as $key => $value) {                
                $arr = array();
                $arr['question'] = $value['question'];
                $arr['answers'] = array();                
                foreach ($value['answers'] as $answer) {                    
                   if ($answer['text'] && $answer['result'] != '') $arr['answers'][] = array(
                    'text' => prepareJSON($answer['text']),
                    'result' => intval($answer['result']),
                    'image' => $answer['image']
                   );
                    
                }              
                
                if ($value['question'] && count($value['answers'])) $testline['questions'][] = $arr;
                
            }            
            $testline['results'] = array();
            $res = json_decode($row['results'], true);
            foreach ($res as $result) {
                
                $testline['results'][] = array(
                'to' => intval($result['result']), 
                'text' => prepareJSON($result['text']),               
                'image' => $result['image']);
                
            }
            
            if ((getStatus('category'.$row['category'])) && (getStatus('test'.$row['id']))) $array['tests'][] = $testline;
            
        }
        
              
        return $array;        
    }
    
     function api_media() {
        
        $array = array();        
        $array['URL_PREFIX'] = $this->url;        
       
        $Q = $this->db->Query("SELECT * FROM `WL_Media_Category` ORDER BY ord,id");
        while ($row = $Q->Parse()) $array['categories'][] = $row;
        
        $Q = $this->db->Query("SELECT * FROM `WL_Media_File` ORDER BY ord,id");
        while ($row = $Q->Parse()) $array['audio'][] = $row;
        
        return $array;        
    }
 
    
    
    function defAction($string) {
        $this->user = $string;        
        if (!$this->user) $this->out(array('status' => -1, 'error_text' => 'Empty DeviceID'));  
              
        $S1 = end(explode($string.'/', $_SERVER['REQUEST_URI']));
        $this->action = reset(explode('?', $S1));
        parse_str(end(explode('?', $S1)), $this->args);                
        $this->calcHashAction();        
        switch ($this->action) {            
            case 'check': { $this->out($this->api_check()); } break;     
            case 'check_new': { $this->out($this->api_check_new()); } break;         
            case 'get': { $this->out($this->api_get()); } break;
             case 'get_new': { $this->out($this->api_get_new()); } break;
            case 'images': { $this->out($this->api_images()); } break;
            case 'media': { $this->out($this->api_media()); } break;
        }
                
    }
    
    
    function out($array) {
        
        header("Content-type: text/plain; charset=utf-8");
        
        if (!isset($this->args['format']))  echo json_encode($array,  JSON_UNESCAPED_UNICODE);
            else print_r($array);
            
        
        die();
        
        
    }
    
}

?>