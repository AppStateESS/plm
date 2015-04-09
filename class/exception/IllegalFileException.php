<?php

PHPWS_Core::initModClass('plm', 'exception/FileException.php');

class IllegalFileException extends FileException
{
    public function __construct($message, $code = 0){
        parent::__construct($message, $code);
    }
}

?>