<?php

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'exception/InvalidSettingsException.php');

class UpdatePeriod extends Command
{
    public function getRequestVars()
    {
        return array('action' => 'UpdatePeriod', 'after' => 'PeriodMaintenance');
    }

    public function execute(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to do that!');
        }

        $settingsMap = array();
        
        try{
            /**
             * Update start/end dates for nomination period
             * Change dates back into Unix time.
             */
            if(!empty($context['nomination_period_start'])){
                // m/d/y
                $date= explode("/", $context['nomination_period_start']);
                if(!$this->isValidDateFormat($date)){
                    throw new InvalidSettingsException('Incorrect formatting on Start Date. (MM/DD/YYYY)');
                }
                $startUnixDate = mktime(0,0,0, $date[0], $date[1], $date[2]);
            } else {
                throw new InvalidSettingsException('Start date for period must be set.');
            }

            if(!empty($context['nomination_period_end'])){
                // m/d/y
                $date= explode("/", $context['nomination_period_end']);
                if(!$this->isValidDateFormat($date)){
                    throw new InvalidSettingsException('Incorrect formatting on End Date. (MM/DD/YYYY)');
                }
                $endUnixDate = mktime(0,0,0, $date[0], $date[1], $date[2]);
            } else {
                throw new InvalidSettingsException('End date for period must be set.');
            }

            // Check that start date is BEFORE end date
            if($startUnixDate >= $endUnixDate){
                throw new InvalidSettingsException('Start date must be before end date.');
            }

            //
            // Save nomination period settings
            //
            PHPWS_Core::initModClass('plm', 'Period.php');
            $year = PHPWS_Settings::get('plm', 'current_period');
            $period = Period::getPeriodByYear($year);
            $period->setStartDate($startUnixDate);
            $period->setEndDate($endUnixDate);
            $period->save();
            
            /**
             * Update receiver of rollover reminder email
             */
            PHPWS_Settings::set('plm', 'rollover_email', $context['rollover_email']);
            PHPWS_Settings::save('plm');

            PHPWS_Core::initModClass('plm', 'PLMRolloverEmailPulse.php');
            $pulse = PLMRolloverEmailPulse::getCurrentPulse();
            $pulse->setExecuteTime($endUnixDate);

        } catch(Exception $e){
            NQ::simple('plm', PLM_ERROR, $e->getMessage());
            return;
        }
        NQ::simple('plm', PLM_SUCCESS, $year.' period updated.');
    }

    /**
     * Date should be an array of 3 elements. (m/d/y)
     * Each element should be numeric
     */
    private function isValidDateFormat($date)
    {
        if(!is_array($date)){
            return false;
        } 
        if(sizeof($date) != 3){
            return false;
        }
        foreach($date as $elem){
            if(!is_numeric($elem)){
                return false;
            }
        }
        // There are only 12 months!
        if($date[0] > 12 || $date[0] < 1){
            return false;
        }
        // I don't want to type in special check for months like February so..
        if($date[1] > 31 || $date[1] < 1){
            return false;
        }
        // No crazy years
        if(strlen($date[2]) > 4 || strlen($date[2]) < 4){
            return false;
        }
        return true;
    }
}

?>