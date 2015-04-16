<?php

PHPWS_Core::initModClass('plm', 'exception/PLMException.php');

class DatabaseException extends PLMException {
    
    public function __construct($message, $code = 0){
        parent::__construct($message, $code);
    }
}