<?php
PHPWS_Core::requireInc('nomination', 'defines.php');
  /**
   * NominationRolloverEmailPulse
   *
   * Email rollover_admin, from settings, that the period has ended.
   * Let them know they need to rollover to the new nomination period.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('pulse', 'ScheduledPulse.php');

class NominationRolloverEmailPulse extends ScheduledPulse
{
    public function __construct($id = NULL)
    {
        $this->id = $id;
        $this->module = 'nomination';
        $this->class_file = 'NominationRolloverEmailPulse.php';
        $this->class = 'NominationRolloverEmailPulse';
        $this->name = 'time_to_rollover';
        $this->username = 'bostrt';
    }

    public function execute()
    {
        PHPWS_Core::initModClass('nomination', 'NominationEmail.php');

        // This is the worst hack ever
        $rollover_receiver[] = new RolloverReceiver();
        $subject = PHPWS_Settings::get('nomination', 'award_title').' | Rollover';
        $message = "It is time to rollover to the next nomination period!";
        $mail = new Nomination_Email($rollover_receiver, $subject, $message);
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
     * There should only be one Pulse scheduled at one time for Nomination
     */
    public static function getCurrentPulse()
    {
        $db = new PHPWS_DB('pulse_schedule');
        $db->addWhere('status', PULSE_STATUS_SCHEDULED);

        $result = $db->select();

        $pulse = new NominationRolloverEmailPulse($result[0]['id']);
        $pulse->load();

        return $pulse;
    }
}

class RolloverReceiver
{
    public function getEmail()
    {
        return PHPWS_Settings::get('nomination', 'rollover_email');
    }
}

?>