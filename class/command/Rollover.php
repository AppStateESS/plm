<?php

  /**
   * Rollover
   *
   * Change current period to new period.  Delete all 
   * non-winning nominations.
   * 
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'Command.php');

class Rollover extends Command
{

    public function getRequestVars()
    {
        return array('action' => 'Rollover', 'after' => 'PeriodMaintenance');
    }

    public function execute(Context $context)
    {
        if(!UserStatus::isAdmin() || !Current_User::allow('plm', 'rollover_period')){
            throw new PermissionException('You are not allowed to do this!');
        }

        PHPWS_Core::initModClass('plm', 'Nomination.php');
        PHPWS_Core::initModClass('plm', 'PLM_Email.php');
        PHPWS_Core::initModClass('plm', 'Period.php');
        PHPWS_Core::initModClass('plm', 'EmailMessage.php');

        // Delete all non-winning nominations and its participants
        $losers = Nomination::getNonWinningNominations();
        // DO NOT delete any nominees that have winning nominations
        $winners = Nomination::getWinningNominations();
        
        $results = array();

        foreach($losers as $loser){

            $deleteNom = True;
            foreach($winners as $winner){
                if($winner->getNomineeId() == $loser->getNomineeId()){
                    $deleteNom = False;
                }
            }
            if($deleteNom){
                // Delete nominee
                $nominee = new Nominee($loser->getNomineeId());
                EmailMessage::deleteMessages($loser->getNominee());
                $results[$loser->getId()]['nominee'] = $nominee->delete();
            }
            $loser->deleteForReal();
        }

        // Check for errors when deleting
        foreach($results as $actor=>$result){
            if(in_array(False, $result)){
                PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
                throw new DatabaseException('Error occured deleting '.$actor. ' for nomination ');
            }
        }

        // Change period
        $newYear = Period::getNextPeriodYear();
        
        $newPeriod = new Period();
        $newPeriod->setYear($newYear);
        $newPeriod->setDefaultStartDate();
        $newPeriod->setDefaultEndDate();
        $newPeriod->save();

        PHPWS_Settings::set('plm', 'current_period', $newYear);
        PHPWS_Settings::save('plm');

        // Add new pulse
        PHPWS_Core::initModClass('plm', 'PLMRolloverEmailPulse.php');
        $pulse = new PLMRolloverEmailPulse();
        $timeDiff = mktime() - $newPeriod->getEndDate();
        $pulse->newFromNow($timeDiff);


        NQ::simple('plm', PLM_SUCCESS, 'Current period is now '.$newYear);
    }
}

?>