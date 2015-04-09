<?php

  /**
   * FallthroughContext
   *
   *   Takes n objects and attempts to find the requested field in each 
   * in the order they were added (FIFE).  Thus higher priority values
   * will override lower priority values.  Useful for figuring out whether
   * or not previously entered form data or database stored object state
   * should be used to fill out the default values for a form.
   *
   * @author Daniel West <dwest at tux dot appstate dot edu>
   * @package plm
   */
PHPWS_Core::initModClass('plm', 'Context.php');

class FallthroughContext extends Context {
    protected $others = array();

    public function offsetExists($offset)
    {
        if(!parent::offsetExists($offset)){
            foreach($this->others as $container){
                if(isset($container[$offset]) || isset($container->$offset)){
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    public function offsetGet($offset)
    {
        if(parent::offsetExists($offset)){
            return parent::offsetGet($offset);
        }

        foreach($this->others as $container){
            if(isset($container[$offset])){
                return $container[$offset];
            } elseif(isset($container->$offset)){
                return $container->$offset;
            }
        }

        return null;
    }

    public function addFallthrough($thing)
    {
        $this->others[] = $thing;
    }
}
?>
