<?php

  /**
   * If this page is displayed then the user probably typed 
   * something in wrong.
   * 
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');

class FourOhFour extends PlemmView
{
    public function getRequestVars()
    {
        return array('view' => 'FourOhFour');
    }

    public function display(Context $context)
    {
        return 'Your request could not be processed. <br />
 Please <a href="mailto:bostrt@tux.appstate.edu">contact</a> us.';
    }
}

?>
