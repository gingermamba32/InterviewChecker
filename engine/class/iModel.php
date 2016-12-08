<?php

/**
 * Default Model
 *
 * @author dandelion <web.dandelion@gmail.com>
 */
class iModel
{
    protected $table;
    protected $instance;
    

    function getById($id)
    {
    
    }

    function getAll()
    {
       
    }

    function add($data)
    {
       
    }

    function edit($data, $id)
    {
    	
    }

    function delete($id)
    {
       
    }
    
    
         
          

        public static function getInstance() {    
            if ( is_null(self::$instance) ) {
                self::$instance = self;
            }
            return self::$instance;
        }
}