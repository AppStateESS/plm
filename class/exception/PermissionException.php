<?php

  /**
   * PermissionException
   *
   * This right here is a permission exception.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'exception/PLMException.php');

class PermissionException extends PLMException
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}

?>