<?php

  /**
   * RolloverView
   *
   * Show information about rollover with button to perform rollover.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'Period.php');

class RolloverView extends PlemmView
{
    public function getRequestVars()
    {
        return array('view' => 'RolloverView');
    }

    public function display(Context $context)
    {
        if(!UserStatus::isAdmin() && Current_User::allow('plm', 'rollover_period')){
            throw new PermissionException('You are not allowed to see that!');
        }

        PHPWS_Core::initCoreClass('Form.php');
        PHPWS_Core::initModClass('plm', 'CommandFactory.php');

        $form = new PHPWS_Form('rollover');
        
        // Get submit command
        $cmdFactory = new CommandFactory();
        $rolloverCmd = $cmdFactory->get('Rollover');
        $rolloverCmd->initForm($form);

        $tpl = array();
        
        $period = Period::getCurrentPeriod();
        $tpl['CURRENT_PERIOD'] = $period->getYear();
        $tpl['NEXT_PERIOD'] = $period->getNextPeriodYear();
        
        $form->addSubmit('submit', 'Perform Rollover');

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();

        Layout::addPageTitle('Rollover');
        
        return PHPWS_Template::process($tpl, 'plm', 'admin/rollover.tpl');
    }
}
?>
