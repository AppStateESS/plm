<?php

  /**
   * SetWinnerStatus
   *
   * Set a given nomination to winner/loser.
   * Do not allow setting status if nomination isn't 
   * for current period.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'view/AjaxMessageView.php');
PHPWS_Core::initModClass('plm', 'Nomination.php');
PHPWS_Core::initModClass('plm', 'Period.php');


class SetWinnerStatus extends Command
{
    public function getRequestVars()
    {
        return array('action' => 'SetWinnerStatus');
    }
    
    public function execute(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to do this!');
        }

        $context['after'] = new AjaxMessageView();
        $status = $context['status'] == "1";
        $nomination = new Nomination($context['id']);
        $period = $nomination->getPeriod();

        // Check that nomination is for current year
        if($period->getYear() == Period::getCurrentPeriodYear()){
            $nomination->setWinner($status);
        }
        else{
            $context['after']->setMessage(False);
            return;
        }
        try{
            $nomination->save();
            $context['after']->setMessage(True);
        } catch (DatabaseException $e){
            $context['after']->setMessage(False);
        }
    }
}
?>