<?php

  class Replacer_main
    {
        private $db;
        
        function __construct($db)
            {
                
                $this->db = $db;
                
            }
            
            
        function rank()
            {
                
                if (!$_COOKIE['rank'])
                return '<div class="RankSite">
				<div class="BG"><div class="m"><i><b>&nbsp;</b></i></div><div class="b"><i><b>&nbsp;</b></i></div></div>
				<div class="in"><table><tbody><tr>
					<td><div class="Descr">
						<big>Оцените работу сайта</big>
						<small>&mdash; помогите сделать его лучше</small>
					</div></td>
					<td><div class="Rank"><table><tbody><tr>
						<td><span><i>&nbsp;</i><u>&nbsp;</u><tt>&nbsp;</tt><em>&nbsp;</em><b>Плохо</b></span></td>
						<td><span><i>&nbsp;</i><u>&nbsp;</u><tt>&nbsp;</tt><em>&nbsp;</em><b>Удовлетворительно</b></span></td>
						<td><span><i>&nbsp;</i><u>&nbsp;</u><tt>&nbsp;</tt><em>&nbsp;</em><b>Хорошо</b></span></td>
						<td><span><i>&nbsp;</i><tt>&nbsp;</tt><em>&nbsp;</em><b>Отлично</b></span></td>
					</tr></tbody></table></div></td>
				</tr></tbody></table></div>
			</div>';
                
            }
            
        function files()
            {
                $files = '';
                $ext = 'docx';
                
                $Q = $this->db->Query("SELECT * FROM `WL_Files` ORDER BY id");
                while ($row = $Q->Parse())
                    {
                        
                        if ($row['id'] == 1)
                            {
                                
                                $ext = explode('.',$row['file']);
                                $ext = end($ext);
                                
                                
                            } else
                            {
                                
                                $e = explode('.',$row['file']);
                                $e = end($e);
                                $files .= '<li><a href="/download/'.$row['id'].'/'.strtr($row['name'], array(' ' => '')).'.'.$e.'">'.$row['name'].'</a></li>';
                                
                            }
                        
                        
                    }
                
                
                
                
                return '
			<div class="DownloadFiles"><div class="in"><!--
		 --><div class="after">
		 	    <div class="BG"><div class="t"><i><b>&nbsp;</b></i></div><div class="m"><i><b>&nbsp;</b></i></div><div class="b"><i><b>&nbsp;</b></i></div></div>
		 	    <div class="in"><ul>
		 	        '.$files.'
		 	    </ul></div>
		    </div><!--
		 --><div class="before"><span><i>Скачать файлы</i></span></div><!--
	 --></div></div>
			<div class="DownloadCart"><div class="in"><small>Скачать</small><big><a href="/download/1/ЭнергоАудитПлюс.КарточкаПредприятия.'.$ext.'" rel="nofollow">Карточку предприятия</a></big></div></div>
		';
                
                
            }
            
        function menu()
            {
                
                $x = '';
                $C = Cache::getInstance();
                
                 if (!$C->Check('mainmenu'))
                 {
                                
                
                
                
                $Q = $this->db->Query("SELECT * FROM `WL_Menu` WHERE `parent` = 0 AND `object` != 'hidden' ORDER BY ord,id");
                while ($row = $Q->Parse())
                    {
                        
                        $x .= '<td><a href="/'.getlink($row).'">'.$row['name'].'</a></td>';
                        
                    }
                    
                    $C->CacheIt('mainmenu', $x);
                    
                } else $x = $C->AsString('mainmenu');
                    
                    
                    
                
                return $x;
            }    
            
        function leftmenu()
            {
                
                 $x = '';
                 $C = Cache::getInstance();
                 
                 
                 
                
                 if (!$C->Check('leftmenu'))
                 {
                
                $Q = $this->db->Query("SELECT * FROM `WL_Menu` WHERE `parent` = 6 AND `object` != 'hidden' ORDER BY ord");
                while ($row = $Q->Parse())
                    {
                        
                       if ((!$row['b']) and (!$row['f'])) $x .= '<div class="e"><div class="in"><a href="/'.getlink($row).'">'.$row['name'].'</a></div></div>';
                       if (($row['b']) and (!$row['f'])) $x .= '<div class="e"><div class="in"><b><a href="/'.getlink($row).'">'.$row['name'].'</a></b></div></div>';
                       if ((!$row['b']) and ($row['f'])) $x .= '<div class="e Featured"><div class="in"><a href="/'.getlink($row).'">'.$row['name'].'</a></div></div>';
                       if (($row['b']) and ($row['f'])) $x .= '<div class="e Featured"><div class="in"><b><a href="/'.getlink($row).'">'.$row['name'].'</a></b></div></div>';
                      
                        
                    }
                    
                    $C->CacheIt('leftmenu', $x); 
                    
                }  else $x = $C->AsString('leftmenu');
                
                return $x;
                
                
            }
            
            
            
        function Route($do)
            {
               
                
                if ($do)
                    {
                        
                        if (is_callable(array($this, $do)))
                            {
                                
                                return $this->$do();
                                
                            } return '';
                        
                    } else return '';
                
            }
        
    }

?>