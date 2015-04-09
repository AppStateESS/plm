<?php

PHPWS_Core::initModClass('plm', 'exception/PLMException.php');

class CommandException extends PLMException {
    
    public function __construct($message, $code = 0){
        parent::__construct($message, $code);
    }
}

?>