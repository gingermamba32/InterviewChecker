<?

class pageModel extends iModel
    {
        
        
        function __construct()
            {
                
                $this->table = 'Content';
                
            }
            
        function getByAlias($alias)
            {
                
                
                return Database::getInstance()->getOne($this->table, array('alias' => $alias));
                
            }
        
    }

?>