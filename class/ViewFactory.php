<?php

  /** 
   * ViewFactory.php
   *
   * ViewFactory stores path to Views directory and contains
   * throws proper exceptions when stuff goes wrong.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'AbstractFactory.php');

class ViewFactory extends AbstractFactory
{
    private $dir = 'view';
   
    // inherited from parent
    public function getDirectory()
    {
        return $this->dir;
    }

    // inherited from parent
    public function throwIllegal($name)
    {
        PHPWS_Core::initModClass('plm', 'exception/IllegalViewException.php');
        throw new IllegalViewException("Illegal characters found in view {$name}");
    }

    // inherited from parent
    public function throwNotFound($name)
    {
        PHPWS_Core::initModClass('plm', 'exception/ViewNotFoundException.php');
        throw new ViewNotFoundException("Could not initialize view {$name}");
    }
}
?>