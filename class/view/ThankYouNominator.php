<?php

PHPWS_Core::initModClass('plm', 'View.php');

class ThankYouNominator extends PlemmView
{

    public function getRequestVars()
    {
        return array('view' => 'ThankYouNominator');
    }

    public function display(Context $context)
    {
        Layout::addPageTitle('Thank you');
        return "<h3>Your nomination was successfully submitted.</h3>";
    }
}

?>
