<?php

  class Replacer_form
    {
        private $db;
        
        function __construct($db)
            {
                
                $this->db = $db;
                
            }
            
            
            
        function Route($do)
            {
               
                
                
                 $regions = explode("\n", file_get_contents('data/regions.txt'));
                $regionx = '';
                for ($i = 0; $i<count($regions); $i++)
                {
                    $regions[$i] = trim($regions[$i]);
                     $regionx .= '<option value="'.$regions[$i].'">'.$regions[$i].'</option>';
            
                }
                
                
                $mo1 = '';
                $ye1 = '';
                $ye2 = '';
                $da1 = '';
                
                for ($i = 1; $i<13; $i++) $mo1 .= '<option value="'.$i.'">'.$i.'</option>';
                for ($i = date('Y'); $i > (date('Y') - 20); $i--) $ye1 .= '<option value="'.$i.'">'.$i.'</option>';
                for ($i = date('Y'); $i<(date('Y')+3); $i++) $ye2 .= '<option value="'.$i.'">'.$i.'</option>';
                for ($i = 1; $i<32; $i++) $da1 .= '<option value="'.$i.'">'.$i.'</option>';
                
                $Q = $this->db->One("SELECT * FROM `WL_Const` WHERE `id` = 1");
                $data = json_decode($Q['data'],true);
                
                return strtr(file_get_contents(INDEX_DIR.'/data/forms/'.$do.'.html'),
                array(
                    '{REGIONS}' => $regionx,
                    '{MONTH}' => $mo1,
                    '{YEAR}' => $ye1,
                    '{YEAR1}' => $ye2,
                    '{DAYS}' => $da1,
                    '{A}' => $data['a'],
                    '{B}' => $data['b'],
                    '{K}' => $data['k'],
                    
                    
                )
                );
                
            }
        
    }

?>