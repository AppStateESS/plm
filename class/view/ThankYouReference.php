<?php

  /**
   * ThankYouReference
   *
   * Tell the reference 'thanks' for uploading their letter
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');

class ThankYouReference extends PlemmView
{

    public function getRequestVars()
    {
        return array('view' => 'ThankYouReference');
    }
    
    public function display(Context $context)
    {
        Layout::addPageTitle('Thank you');
        return "<h3>Your letter of recommendation was successfully submitted.</h3>";
    }
}

?>
