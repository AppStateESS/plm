<?php

  /**
   * AbstractFactory.php
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

abstract class AbstractFactory
{
    abstract function getDirectory();
    abstract function throwIllegal($name);
    abstract function throwNotFound($name);	

    public function get($name=Null)
    {
        if(is_null($name)){
            $name = 'Null';
        }

        $class = $this->init($name);

        $instance = new $class();
        return $instance;
    }

    private function init($name)
    {
        $dir = $this->getDirectory();

        if(preg_match('/\W/', $name)) {
            $this->throwIllegal($name);
        }

        $path = "{$dir}/{$name}.php";
        
        //try{
            PHPWS_Core::initModClass('plm', $path);
            /*
        } catch(Exception $e){
            $this->throwNotFound($path);
        }
        */
        return $name;
    }
}
?>