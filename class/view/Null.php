<?php

  /**
   * Null
   *
   * This was the first View for PLM.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');

class Null extends PlemmView
{
    public function getRequestVars()
    {
        return array('view'=>'Null');
    }
    public function display(Context $context)
    {
        return ("<h2>Do you know what's going on? <br/>
                         Maybe it's another drill.</h2>");
    }
}

?>
