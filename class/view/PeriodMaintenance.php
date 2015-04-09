<?php

  /**
   * Period Maintenance
   *
   * View for period settings
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'CommandFactory.php');
PHPWS_Core::initModClass('plm', 'Period.php');

class PeriodMaintenance extends PlemmView
{
    public function getRequestVars()
    {
        return array('view' => 'PeriodMaintenance');
    }

    public function display(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to see that!');
        }
        $tpl = array();

        $form = new PHPWS_Form('period_');

        $cmdFactory = new CommandFactory();
        $updateCmd = $cmdFactory->get('UpdatePeriod');

        $updateCmd->initForm($form);

        // Begin and end dates for nomination period
        // Make dates readable by user
        $period = Period::getCurrentPeriod();

        if(is_null($period)){
            // This shouldn't happen
            $tpl['NOMINATION_PERIOD_START'] = '<b class="error-text">No period set.</b>';
        } else {
            $start = $period->getReadableStartDate();
            $end = $period->getReadableEndDate();

            $form->addText('nomination_period_start', $start);
            $form->setLabel('nomination_period_start', 'Period Start Date');
            $form->addText('nomination_period_end', $end);
            $form->setLabel('nomination_period_end', 'Period End Date');
        }

        // Display period information
        $currYear = PHPWS_Settings::get('plm', 'current_period');
        $tpl['CURRENT_PERIOD_YEAR'] = $currYear;

        // Link to rollover view
        $vFactory = new ViewFactory();
        $rolloverView = $vFactory->get('RolloverView');
        $tpl['NEXT_PERIOD'] = $period->getNextPeriodYear();
        $tpl['ROLLOVER_LINK'] = $rolloverView->getLink('Rollover');

        $form->addText('rollover_email', PHPWS_Settings::get('plm', 'rollover_email'));
        $form->setLabel('rollover_email', 'Rollover Reminder');

        // For use with JQuery datepicker and start/end dates
        $tpl['START_DATE_ID'] = $form->getFormId().'_nomination_period_start';
        $tpl['END_DATE_ID'] = $form->getFormId().'_nomination_period_end';

        $tpl['HELP_ICON'] = PHPWS_SOURCE_HTTP."mod/plm/img/tango/apps/help-browser.png";

        $form->addSubmit('Update Period');

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();

        Layout::addPageTitle('Period Settings');

        return PHPWS_Template::process($tpl, 'plm', 'admin/period_maintenance.tpl');
    }
}

?>
