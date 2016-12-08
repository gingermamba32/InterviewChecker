<?php

//ini_set('display_errors', 1);
//error_reporting(8191);

class App_partner extends App {
    
    
    private $lang = array('ru' => 'Русский', 'en' => 'English');
    
    function checkLogined() {
        
        if (isset($_SESSION['partner'])) {
            $this->tpl->Replace('L_Username', $_SESSION['partner']['username']);
            $class = array('MGR' => 'Менеджер', 'ADM' => 'Администратор');
            $this->tpl->Replace('L_Class', $class[$_SESSION['partner']['class']]);
            return true;
        } 
            else return false;
        
    }
    
     function getAID() {
        
       if ($_SESSION['partner']['parent'] > 0) return $_SESSION['partner']['parent'];
        else return $_SESSION['partner']['id'];
        
    }
    
    
    function getCategory($th = 0) {
        
        $Q = $this->db->Query("SELECT id,lang,name FROM `WL_Category` ORDER BY id");
        $x = '';
        
        while ($row = $Q->Parse()) {            
            if ($row['id'] != $th) $x .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                else $x .= '<option value="'.$row['id'].'" selected="">'.$row['name'].'</option>';            
        }
        
        return $x;
        
    }
    
    function getCategoryName($th = 0) {
        
        return $this->db->One("SELECT id,lang,name FROM `WL_Category` WHERE `id`  = $th");
       
    }
    
    
      
    function indexAction() {
        
        if ($this->checkLogined()) { header('Location: /partner/board/'); die(); }
        
        $this->tpl->Title('Вход для партнеров');
        $this->tpl->Change('partner_login');
    
        
        if (isset($_POST['username'])) {
            
             $p2 = Secure($_POST['username'], $_POST['password']);
             $username = prepareString($_POST['username']);
 ;                         
             $Q = $this->db->One("SELECT * FROM `WL_Partner` WHERE `username` = '$username' AND `password` = '$p2' AND `status` = 1");
             if (!$Q) $this->tpl->DirectReplace("<!--ERROR-->", '<div class="alert alert-warning" role="alert">Неверный логин или пароль.</div>');
             else { 
                
                $_SESSION['partner'] = $Q;
                header('Location: /partner/board/');
                die();
                
             }
            
        }
        
        
        //echo Secure('synergy@macrox.ru', '3032015');
    }
    
    
     function testAddAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('board_test_form');
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');      
        
        $qq = '';
        $q = $this->tpl->Module('q');
        for ($i = 0; $i<MAX_Q;$i++) {
            
            $qq .= strtr($q, array(
                '{ID}' => $i,
                '{UID}' => ($i+1),                
            ));
            
        }
     
        $this->tpl->Replace('Iid','');
        $this->tpl->Replace('Name','');
        $this->tpl->Replace('Lang', '');
        $this->tpl->Replace('Text', '');
        $this->tpl->Replace('Result', 0);
        $this->tpl->Replace('Category', $this->getCategory());
        $this->tpl->Replace('Description', '');
        
        if (isset($_POST['name'])) {
            
            $iid = prepareString($_POST['iid']);       
            $icon   = prepareString($_POST['icon']);            
            $image  = prepareString($_POST['image']);     
            $lang   = prepareString($_POST['lang']);   
            $categ  = prepareString($_POST['category']);  
            $name   = prepareString($_POST['name']);   
            $desc   = prepareString($_POST['description']);  
            $questions = encode_json($_POST['questions']);
            $type = intval($_POST['type']);
            $results = encode_json($_POST['results']);            
                   
            $this->db->Query("INSERT INTO `WL_Test` VALUES (NULL, '$lang', '$categ', '$iid', '$name', '$desc', '$icon', '$image', '$questions', '$results', $type, UNIX_TIMESTAMP(),0);");            
            header('Location: /partner/');
            die();
        }
    }
    
    
    function testEditAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);      
        $this->tpl->Change('board_test_form');      
       
        
        $U = $this->db->One("SELECT * FROM `WL_Test` WHERE `id` = $id");
        $this->tpl->Replace('Iid', htmlspecialchars($U['iid']));
        $this->tpl->Replace('Name', htmlspecialchars($U['title']));
        $this->tpl->Replace('Category', $this->getCategory($U['category']));
        $this->tpl->DirectReplace('"'.$U['lang'].'"', '"'.$U['lang'].'" selected=""');
        $this->tpl->Replace('Description', $U['description']);
        $this->tpl->DirectReplace('<input name="image" type="hidden" value=""  />','<input name="image" type="hidden" value="'.$U['image'].'"  />');
        $this->tpl->DirectReplace('<div class="img-thumb" data-id="image"><img src="/static/no.jpg" alt=""  /></div>','<div class="img-thumb" data-id="image"><img src="/a/'.$U['image'].'" alt=""  /></div>');
        $this->tpl->DirectReplace('<input name="icon" type="hidden" value=""  />','<input name="icon" type="hidden" value="'.$U['icon'].'"  />');
        $this->tpl->DirectReplace('<div class="img-thumb" data-id="icon"><img src="/static/no.jpg" alt="" /></div>','<div class="img-thumb" data-id="icon"><img src="/a/'.$U['icon'].'" alt="" /></div>');
                
       $this->tpl->DirectReplace('"type'.$U['type'].'"', '"type'.$U['type'].'" selected=""');     
                
                        
        $this->tpl->DirectReplace('SRC_Q = 0','SRC_Q = '.prepareJSON($U['question']));
        $this->tpl->DirectReplace('SRC_R = 0','SRC_R = '.prepareJSON($U['results']));
        
        $res = json_decode($U['results'] ,true);
        
      
        $this->tpl->DirectReplace('Добавить тест', 'Изменить тест');
        
        if (isset($_POST['name'])) {
            
            $iid   = prepareString($_POST['iid']); 
            $icon   = prepareString($_POST['icon']);            
            $image  = prepareString($_POST['image']);     
            $lang   = prepareString($_POST['lang']);   
            $categ  = intval($_POST['category']);  
            $name   = prepareString($_POST['name']);   
            $desc   = prepareString($_POST['description']);  
            $questions = encode_json($_POST['questions']);
            $results = encode_json($_POST['results']);   
             $type = intval($_POST['type']);
            $this->db->Query("UPDATE `WL_Test` SET `lang` = '$lang', `iid` = '$iid',  `category` = $categ,`title` = '$name', `description` = '$desc', `icon` = '$icon', `image` = '$image', `question` = '$questions', `results` = '$results', `type` = $type, `updated` = UNIX_TIMESTAMP() WHERE `id` = $id");
            header('Location: /partner/');
            die();
            
        }
        
    }
    
    
    function changeStatusAction($arg) {        
        $uid = prepareString($arg[0]);
        $set = intval($arg[1]);
        setStatus($uid, $set);
        if (substr($uid,0,4) == 'test') {
            $change = 'test';            
            $id = intval(str_replace('test', '', $uid));
        } else {
            $change = 'category';            
            $id = intval(str_replace('category', '', $uid));            
        }
        switch($change) {
            case 'test': { $this->db->Query("UPDATE `WL_Test` SET `updated` = UNIX_TIMESTAMP() WHERE `id` = $id");  } break;           
            case 'category': { $this->db->Query("UPDATE `WL_Category` SET `updated` = UNIX_TIMESTAMP() WHERE `id` = $id"); } break;           
        }
        die();
    }
    
      
    function testRemoveAction($arg) {        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);   
        $this->db->Query("DELETE FROM `WL_Test` WHERE `id` = $id");
        echo 'Тест удален';
        die();        
    }
    
    function ttAction($arg) {
        
        $array[] = 'Включите "немного" юмора';
        $f =  encode_json($array);
        $this->db->Query("INSERT INTO `WL_Configuration` VALUES ('T1', '$f');");
        die();
        
    }
    
    function boardAction($arg) {
        
        $lang = 'ru';
        if (isset($arg[0]) && ($arg[0] == 'en')) $lang = 'en';
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('board');
        $this->tpl->DirectReplace('<li><a href="/partner/board/">','<li class="active"><a href="/partner/board/">');
        $this->tpl->DirectReplace('pr_'.$lang.'"', 'pr_'.$lang.'" class="active"');
        $tpl = $this->tpl->Module('test_item');
        $tplc = $this->tpl->Module('test_category');
        $content = '';        
        $cats = array();
        $Q = $this->db->Query("select * from `WL_Test` WHERE `lang` = '$lang' ORDER BY category,ord,id");
        while ($row = $Q->Parse()) {
            
            if (!isset($cats[$row['category']])) {
                $sw = '';
                $c = $this->getCategoryName($row['category']);
                if (!getStatus('category'.$row['category'])) $sw = ' off';
                $content .= strtr($tplc, array('{ID}' => $c['id'], '{NAME}' => $c['name'], '{SWITCH}' => $sw));
                $cats[$row['category']] = 1;
                
            }
            $sw = '';
            if (!getStatus('test'.$row['id'])) $sw = ' off';
            
            if ($row['iid']) $row['title'] = '<b>['.$row['iid'].']</b> '.$row['title'];
            
            $content .= strtr($tpl, array(
                '{HASH}' => '-',
                '{ID}' => $row['id'],
                '{NAME}' => $row['title'],
                '{LANG}' => $this->lang[$row['lang']],       
                '{SWITCH}' => $sw,
                '{CATEGORY}' => $row['category'],
                  
            ));
            
        }
        
        $this->tpl->DirectReplace('<!--USR-->',$content);
      
      
        
    }
    
 
    function usersAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('board_users');       
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');        
        $this->tpl->DirectReplace('<li><a href="/partner/users/">','<li class="active"><a href="/partner/users/">');
        $content = '';
        $Q = $this->db->Query("select * from `WL_Partner` where `parent` = 4 order by id");
       
        $tpl = $this->tpl->Module('users_item');
        while ($row = $Q->Parse()) {
            $class = '<span class="label label-primary">Администратор</span>';
            $status = '<span class="label label-success">Активен</span>';            
            if ($row['class'] == 'MGR') $class = '<span class="label label-info">Менеджер</span>';
            if ($row['status'] == 0) $status = '<span class="label label-warning">Неактивен</span>';
            if ($row['status'] == -1) $status = '<span class="label label-danger">Заблокирован</span>';
            
            $content .= strtr($tpl, array(
                '{ID}' => $row['id'],
                '{USERNAME}' => $row['username'],
                '{NAME}' => $row['name'],
                '{CLASS}' => $class,
                '{STATUS}' => $status,
                '{HASH}' => sha1(md5($row['id'].'st').$this->getAID()),
            ));
            
        }
        
         
        $this->tpl->DirectReplace('<!--USR-->',$content); 
        
    }
    
    
    function usersRemoveAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);
        $hash = $arg[1];
        $hash0 = sha1(md5($id.'st').$this->getAID());
        if ($hash != $hash0) echo 'Security Error';
            else {
                
                $this->db->Query("DELETE FROM `WL_Partner` WHERE `id` = $id");
                echo 'Пользователь удален';
                
            }
        
            
        die();
        
    }
    
    
    function usersEditAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);
        $hash = $arg[1];
        $hash0 = sha1(md5($id.'st').$this->getAID());
        if ($hash != $hash0) die('Security Error');
        $this->tpl->Change('board_users_form');       
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');    
        
        $U = $this->db->One("SELECT * FROM `WL_Partner` WHERE `id` = $id");
        $this->tpl->Replace('Name', $U['name']);
        $this->tpl->Replace('username', $U['username']);
        $this->tpl->DirectReplace('<option value="'.$U['class'].'">', '<option value="'.$U['class'].'" selected="">');
        $this->tpl->DirectReplace('<input type="email" ', '<input type="email" disabled="1" ');
        $this->tpl->DirectReplace('Добавить пользователя', 'Редактирование пользователя');
        
        if (isset($_POST['name'])) {
            
           
            $name = prepareString($_POST['name']);
            $class = prepareString($_POST['class']);           
            $this->tpl->Replace('Name', $name);
            $this->tpl->DirectReplace('<option value="'.$class.'">', '<option value="'.$class.'" selected="">');
            $this->tpl->DirectReplace("<!--ERROR-->",'<div class="alert alert-success">Данные сохранены.</div>');
            $this->db->Query("UPDATE `WL_Partner` SET `name` = '$name',`class` = '$class' WHERE `id` = $id");
            
        }
        
    }
    
    
    function usersAddAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('board_users_form');       
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra'); 
        $this->tpl->Replace('Username', '');
        $this->tpl->Replace('Name', '');
        
        if (isset($_POST['username'])) {
            
            $username = prepareString($_POST['username']);
            $name = prepareString($_POST['name']);
            $class = prepareString($_POST['class']);  
            $this->tpl->Replace('Username', $username);        
            $this->tpl->Replace('Name', $name);
            $this->tpl->DirectReplace('<option value="'.$class.'">', '<option value="'.$class.'" selected="">');
            
            
            $A = $this->db->One("SELECT * FROM `".Prefix."Partner` WHERE `username` = '$username'");
            if ($A) {
                
                $this->tpl->DirectReplace("<!--ERROR-->",'<div class="alert alert-danger">Данный E-Mail уже зарегистрирован в системе.</div>');
                
            } else { 
                
                $password = GeneratePassword(8);
                $pwd = Secure($username, $password);
                $time = time() + 86400*365;
                
                $A = $this->db->Query("INSERT INTO `".Prefix."Partner` VALUES (NULL, '$class', ".$this->getAID().", '$username', '$pwd', '$name', '[]', $time,1);");
                    if ($A)
                        {
                            
                            $this->tpl->Change('board_success');
                            $this->tpl->Replace('Url', 'users/');
                             
                             Mailx($username,'Приветственное письмо','
                             
                                <p>Мы рады приветствовать Вас в A1Wisdom. Данные входа в панель управления:</p>
                                <p class="callout"><b>Панель управления:</b> http://api.a1wisdom.com/partner/ <br/>
                                    <b>Имя пользователя:</b> '.$username.'<br/>
                                    <b>Пароль:</b> '.$password.'<br/>
                                </p>
                                
                             
                             ');
                             
                            
                        } else {
                            
                            $this->tpl->DirectReplace("<!--ERROR-->",'<div class="alert alert-danger">Внутренняя ошибка.</div>');
                             
                        }
                
            }
            
        }       
        
    }
    
    
    // USERS END
    
      function mediaAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('board_media');        
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');    
        if (!$arg[0]) $arg[0] = 'file';
        $this->tpl->DirectReplace('pr_'.$arg[0].'"', 'pr_'.$arg[0].'" class="active"');$this->tpl->Replace('New', '<a href="/partner/media_add_'.$arg[0].'" class=" pull-right btn btn-success">Добавить</a>');
       
         $tdnd = 'tdnd-user-cat';
      
        $Q = $this->db->Query("SELECT * FROM `WL_Media_Category` ORDER BY ord,id");
        if ($arg[0] == 'file') {  $Q = $this->db->Query("SELECT * FROM `WL_Media_File` ORDER BY ord,id"); $tdnd = 'tdnd-user'; }       
        
        
        $tpl = $this->tpl->Module('media_item');
        $content = '';   
        
      
        
        while ($row = $Q->Parse()) {      
            if ($arg[0] == 'category') $row['image'] = 'be9ec11033e5e1230dafabb5f66ec9ea.jpg';
            $content .= strtr($tpl, array(
                '{NAME_RU}' => $row['name_ru'],
                '{NAME_EN}' => $row['name_en'],               
                '{ID}' => $row['id'],
                '{IMAGE}' => $row['image'],  
                '{TYPE}' => $arg[0],                    
            ));            
        }
        $this->tpl->DirectReplace('<!--USR-->', $content);
        $this->tpl->DirectReplace('tdnd-user', $tdnd);
        
    }
    
    function mediaSaveAction($arg) {
        
        $v = $_POST['media'];
        $k = 0;    
        foreach ($v as $key => $value) {
          $this->db->Query("UPDATE `WL_Media_File` SET `ord` = $k WHERE `id` = $key");
          $k++;       
        } 
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/cache/media.sort', time());
        
    }
    
        function testsSaveAction($arg) {
        
        $v = $_POST['test'];
        $k = 0;    
        foreach ($v as $key => $value) {
          $this->db->Query("UPDATE `WL_Test` SET `ord` = $k WHERE `id` = $key");
          $k++;       
        } 
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/cache/test.sort', time());
        
    }
    
     function mediaCategorySaveAction($arg) {
        
        $v = $_POST['media'];
        $k = 0;    
        foreach ($v as $key => $value) {
          $this->db->Query("UPDATE `WL_Media_Category` SET `ord` = $k WHERE `id` = $key");
          $k++;       
        } 
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/cache/media-category.sort', time());
        
    }
    
    
     function media_add_categoryAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('media_category_form');
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');      
        $this->tpl->Replace('Name_RU','');
        $this->tpl->Replace('Name_En','');
        
        if (isset($_POST['name_ru'])) {            
            $name_ru = prepareString($_POST['name_ru']);            
            $name_en = prepareString($_POST['name_en']);        
            $image = prepareString($_POST['image']);     
            $this->db->Query("INSERT INTO `WL_Media_Category` VALUES (NULL, 0, '$name_ru', '$name_en', '$image',UNIX_TIMESTAMP());");            
            header('Location: /partner/media/category');
            die();
        }
    }
    
    
      function media_add_fileAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('media_file_form');
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');      
        $this->tpl->Replace('Name_RU','');
        $this->tpl->Replace('Name_En','');
        $category = '';
        $Q = $this->db->Query("SELECT * FROM `WL_Media_Category` ORDER BY name_ru");
        while ($row = $Q->Parse()) {
            $category .= '<option value="'.$row['id'].'">'.$row['name_ru'].'</option>';            
        }
        $this->tpl->Replace('Category',$category);
        
        if (isset($_POST['name_ru'])) {            
            $name_ru = prepareString($_POST['name_ru']);            
            $name_en = prepareString($_POST['name_en']);        
            $image = prepareString($_POST['image']);    
            $file  = prepareString($_POST['file']);
            $type  = intval($_POST['type']);
            $cat   = intval($_POST['category']);
            $this->db->Query("INSERT INTO `WL_Media_File` VALUES (NULL, 0, $cat, '$name_ru', '$name_en', '$image', $type, '$file' ,UNIX_TIMESTAMP());");            
            header('Location: /partner/media/');
            die();
        }
    }
    
    function media_edit_categoryAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);        
        $this->tpl->Change('media_category_form');       
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');    
        
        $U = $this->db->One("SELECT * FROM `WL_Media_Category` WHERE `id` = $id");
        $this->tpl->Replace('Name_Ru', htmlspecialchars($U['name_ru']));  
        $this->tpl->Replace('Name_En', htmlspecialchars($U['name_en']));  
        $this->tpl->DirectReplace('<input name="image" type="hidden" value=""  />','<input name="image" type="hidden" value="'.$U['image'].'"  />');
        $this->tpl->DirectReplace('<div class="img-thumb" data-id="image"><img src="/static/no.jpg" alt="" /></div>','<div class="img-thumb" data-id="image"><img src="/a/'.$U['image'].'" alt=""  /></div>');
          
        if (isset($_POST['name_ru'])) {
            $name_ru = prepareString($_POST['name_ru']);            
            $name_en = prepareString($_POST['name_en']);        
            $image = prepareString($_POST['image']);       
            $this->db->Query("UPDATE `WL_Media_Category` SET `name_ru` = '$name_ru', `name_en` = '$name_en', `image` = '$image',`timestamp` = UNIX_TIMESTAMP()  WHERE `id` = $id");
            header('Location: /partner/media/category');
            die();
        }
        
    }
    
     function media_edit_fileAction($arg) {        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);        
        $this->tpl->Change('media_file_form');       
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');    
        
        $U = $this->db->One("SELECT * FROM `WL_Media_File` WHERE `id` = $id");
        $this->tpl->Replace('Name_Ru', htmlspecialchars($U['name_ru']));  
        $this->tpl->Replace('Name_En', htmlspecialchars($U['name_en']));  
        $this->tpl->DirectReplace('<input name="image" type="hidden" value=""  />','<input name="image" type="hidden" value="'.$U['image'].'"  />');
        $this->tpl->DirectReplace('<input name="file" type="hidden" value=""  />', '<input name="file" type="hidden" value="'.$U['file'].'"  />');
        $this->tpl->DirectReplace('<div class="img-thumb" data-id="image"><img src="/static/no.jpg" alt="" /></div>','<div class="img-thumb" data-id="image"><img src="/a/'.$U['image'].'" alt=""  /></div>');
        $this->tpl->DirectReplace('<audio style="width: 256px;" src="" data-id="file" controls></audio>','<audio style="width: 256px;" src="/a/'.$U['file'].'" data-id="file" controls></audio>');
        $this->tpl->DirectReplace('<option value="'.$U['type'].'">','<option value="'.$U['type'].'" selected="">');  
        $category = '';
        $Q = $this->db->Query("SELECT * FROM `WL_Media_Category` ORDER BY name_ru");
        while ($row = $Q->Parse()) {
           if ($row['id'] != $U['category']) $category .= '<option value="'.$row['id'].'">'.$row['name_ru'].'</option>';
            else  $category .= '<option selected="" value="'.$row['id'].'">'.$row['name_ru'].'</option>';            
        }
        $this->tpl->Replace('Category',$category);
          
        if (isset($_POST['name_ru'])) {
            $name_ru = prepareString($_POST['name_ru']);            
            $name_en = prepareString($_POST['name_en']);        
            $image = prepareString($_POST['image']);    
            $file  = prepareString($_POST['file']);
            $type  = intval($_POST['type']);
            $cat   = intval($_POST['category']);
            $this->db->Query("UPDATE `WL_Media_File` SET `name_ru` = '$name_ru', `name_en` = '$name_en', `image` = '$image', `file` = '$file', `category` = $cat, `type` = $type,`timestamp` = UNIX_TIMESTAMP()  WHERE `id` = $id");
            header('Location: /partner/media/');
            die();
        }        
    }
    
    
     function media_remove_categoryAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);
        $hash = $arg[1];
        $hash0 = sha1(md5($id.'stok').$this->getAID());
        $this->db->Query("DELETE FROM `WL_Media_Category` WHERE `id` = $id");               
        echo 'Категория удалена';
        die();        
    }
    
    function media_remove_fileAction($arg) {        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);
        $hash = $arg[1];
        $hash0 = sha1(md5($id.'stok').$this->getAID());
        $this->db->Query("DELETE FROM `WL_Media_File` WHERE `id` = $id");               
        echo 'Запись удалена';
        die();        
    }
    
    function categoryAction($arg) {        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('board_category');        
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');     
        $Q = $this->db->Query("SELECT * FROM `WL_Category` ORDER BY lang,name");
        $tpl = $this->tpl->Module('category_item');
        $content = '';   
        while ($row = $Q->Parse()) {            
            $content .= strtr($tpl, array(
                '{NAME}' => $row['name'],
                '{LANG}' => $this->lang[$row['lang']],
                '{ID}' => $row['id'],
                '{HASH}' =>  sha1(md5($row['id'].'stok'))              
            ));            
        }
        $this->tpl->DirectReplace('<!--USR-->', $content);
    }
    
    
     function categoryAddAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $this->tpl->Change('board_category_form');
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');      
        $this->tpl->Replace('Name','');
        $this->tpl->Replace('Lang', '');
        if (isset($_POST['name'])) {
            
            $name = prepareString($_POST['name']);            
            $lang = prepareString($_POST['lang']);            
            $this->db->Query("INSERT INTO `WL_Category` VALUES (NULL, '$lang', '$name', UNIX_TIMESTAMP());");            
            header('Location: /partner/category');
            die();
        }
    }
    
    
   
    
    
    function categoryEditAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);
        $hash = $arg[1];
        $hash0 = sha1(md5($id.'stok').$this->getAID());
       // if ($hash != $hash0) die('Security Error');
        $this->tpl->Change('board_category_form');       
        if ($_SESSION['partner']['class'] == 'MGR') $this->tpl->Change('board_ra');    
        
        $U = $this->db->One("SELECT * FROM `WL_Category` WHERE `id` = $id");
        $this->tpl->Replace('Name', htmlspecialchars($U['name']));  
        $this->tpl->DirectReplace('"'.$U['lang'].'"', '"'.$U['lang'].'" selected=""');   
 
               
        
        if (isset($_POST['name'])) {
            
         
            $name = prepareString($_POST['name']);            
            $lang = prepareString($_POST['lang']);     
            
            $this->db->Query("UPDATE `WL_Category` SET `name` = '$name', `lang` = '$lang', `updated` = UNIX_TIMESTAMP()  WHERE `id` = $id");
            
           
            header('Location: /partner/category');
            die();
        
        }
        
    }
    
    
    function categoryRemoveAction($arg) {
        
        if (!$this->checkLogined()) { header('Location: /partner/'); die(); }
        $id = intval($arg[0]);
        $hash = $arg[1];
        $hash0 = sha1(md5($id.'stok').$this->getAID());
        $this->db->Query("DELETE FROM `WL_Category` WHERE `id` = $id");               
        echo 'Категория удалена';
        die();
        
    }
    
    
    function exitAction($arg) {
        
        unset($_SESSION['partner']);
        header('Location: /partner/');
        die();
        
        
    }
    
    function restoreAction($arg) {
        
        
        if ($this->checkLogined()) { header('Location: /partner/board/'); die(); }
          
          
        $this->tpl->Title('Восстановление пароля');
        $this->tpl->Change('restore');
        
        if (isset($_POST['username'])) {
            
            $username = prepareString($_POST['username']);
            $Q = $this->db->One("SELECT * FROM `WL_Partner` WHERE `username` = '$username' AND `status` = 1 AND `expires` > UNIX_TIMESTAMP()");
            if (!$Q) {
                
                $this->tpl->DirectReplace("<!--ERROR-->", '<div class="alert alert-warning" role="alert">Пользователь не найден</div>');
                
            } else {
                
                $Hash = GeneratePassword(32);
                $this->db->Query("UPDATE `WL_Partner` SET `data` = '[$Hash]' WHERE `id` = ".$Q['id']);
                Mailx($username,'Восстановление пароля','Кто-то от Вашего имени запросил ссылку для восстановления пароля к партнерскому интерфейсу A1Wisdom.<br/>
                Для сброса пароля пройдите по ссылке: <a href="http://api.a1wisdom.com/partner/clear/'.$Hash.'/">http://api.a1wisdom.com/partner/clear/'.$Hash.'/</a><br/><br/>
                Если Вы не запрашивали сброс - просто проигнорируйте данное письмо.
                ');
                
                $this->tpl->DirectReplace("<!--ERROR-->", '<div class="alert alert-warning" role="alert">Ссылка для сброса пароля отправлена на Ваш электронный адрес.</div>');
            }
            
        }
        
    }
    
    function clearAction($arg) {
        
        if ($this->checkLogined()) { header('Location: /partner/board/'); die(); }
        $this->tpl->Title('Восстановление пароля');
        $this->tpl->Change('message');
        $arg = prepareString($arg[0]);
        $this->tpl->Replace('Name', 'Восстановление пароля');
        
        $Q = $this->db->One("SELECT * FROM `WL_Partner` WHERE `data` = '[$arg]' AND `status` = 1 AND `expires` > UNIX_TIMESTAMP()");
        if (!$Q) $this->tpl->Replace('Text', '<div class="alert alert-danger" role="alert">Ссылка истекла.</div>');
            else {
                
                $p1 = GeneratePassword(8);
                $p2 = Secure($Q['username'], $p1);
                Mailx($Q['username'],'Новый пароль','Для Вас сгенерирован новый пароль: <b>'.$p1.'</b><br/>
                
                Для входа в партнерский интерфейс пройдите по ссылке: <a href="http://api.a1wisdom.com/partner/">http://api.a1wisdom.com/partner/</a><br/><br/>
            
                ');
                
                $this->db->Query("UPDATE `WL_Partner` SET `data` = '[]', `password` = '$p2' WHERE `id` = ".$Q['id']);
                
                $this->tpl->Replace('Text', '<div class="alert alert-success" role="alert">Новый пароль отправлен на Ваш адрес электронной почты.</div><p>&nbsp;</p>
                <p><a href="/partner/">Войти в систему</a></p>');
            }
    }
    
    
    function uploadAction($arg) {        
         if (!$this->checkLogined()) { die('<script type="text/javascript">window.parent.alert("Вы не авторизованы");</script>'); }         
         $ext = strtolower(trim(end(explode('.', $_FILES["imageFile"]["name"]))));         
         $filename = md5_file($_FILES['imageFile']['tmp_name']).'.'.$ext;
         $filepath = $_SERVER['DOCUMENT_ROOT'].'/a/'.$filename;
         if (file_exists($filepath))  die('<script type="text/javascript">window.parent.uploadOver("'.$filename.'");</script>');
         if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $filepath))  die('<script type="text/javascript">window.parent.uploadOver("'.$filename.'");</script>');
         die('<!--D-->');        
    }    
      
}

?>