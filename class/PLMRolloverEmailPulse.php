<?php
PHPWS_Core::requireInc('plm', 'defines.php');
  /** 
   * PLMRolloverEmailPulse
   *
   * Email rollover_admin, from settings, that the period has ended.
   * Let them know they need to rollover to the new nomination period.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('pulse', 'ScheduledPulse.php');

class PLMRolloverEmailPulse extends ScheduledPulse
{
    public function __construct($id = NULL)
    {
        $this->id = $id;
        $this->module = 'plm';
        $this->class_file = 'PLMRolloverEmailPulse.php';
        $this->class = 'PLMRolloverEmailPulse';
        $this->name = 'time_to_rollover';
        $this->username = 'bostrt';
    }
    
    public function execute()
    {
        PHPWS_Core::initModClass('plm', 'PLM_Email.php');

        // This is the worst hack ever
        $rollover_receiver[] = new RolloverReceiver();
        $subject = PHPWS_Settings::get('plm', 'award_title').' | Rollover';
        $message = "It is time to rollover to the next nomination period!";
        $mail = new PLM_Email($rollover_receiver, $subject, $message);
        $mail->send();
        return True;
    }

    public function newFromNow($seconds)
    {
        parent::newFromNow($seconds);
    }

    public function setExecuteTime($seconds)
    {
        $this->execute_at = $seconds;
        $this->scheduled_at = $seconds;
        $this->save();
    }

    /** 
     * There should only be one Pulse scheduled at one time for PLM
     */
    public static function getCurrentPulse()
    {
        $db = new PHPWS_DB('pulse_schedule');
        $db->addWhere('status', PULSE_STATUS_SCHEDULED);
        
        $result = $db->select();

        $pulse = new PLMRolloverEmailPulse($result[0]['id']);
        $pulse->load();

        return $pulse;
    }
}

class RolloverReceiver
{
    public function getEmail()
    {
        return PHPWS_Settings::get('plm', 'rollover_email');
    }
}

?>