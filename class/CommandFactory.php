<?php

  /**
   * CommandFactory.php
   *
   * CommandFactory stores path to Commands directory and contains
   * throws proper exceptions when stuff goes wrong.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'AbstractFactory.php');

class CommandFactory extends AbstractFactory
{
    private $dir = 'command';
   
    // inherited from parent
    public function getDirectory()
    {
        return $this->dir;
    }

    // inherited from parent
    public function throwIllegal($name)
    {
        PHPWS_Core::initModClass('plm', 'exception/IllegalCommandException.php');
        throw new IllegalCommandException("Illegal characters found in command {$name}");
    }

    // inherited from parent
    public function throwNotFound($name)
    {
        PHPWS_Core::initModClass('plm', 'exception/CommandNotFoundException.php');
        throw new CommandNotFoundException("Could not initialize command {$name}");
    }
}
?>