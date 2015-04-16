<?php

PHPWS_Core::initModClass('plm', 'PLM_Model.php');

// DB Table
define('EMAIL_MESSAGE_TABLE', 'plm_email_log');

class EmailMessage extends PLM_Model
{
    public $nominee_id;
    public $message;
    public $message_type;
    public $subject;
    public $receiver_id;
    public $receiver_type;
    public $sent_on;
    
    public function getDb()
    {
        return new PHPWS_DB(EMAIL_MESSAGE_TABLE);
    }

    // Delete all emails for the nominator, reference, or nominee
    public static function deleteMessages(NominationActor $actor)
    {
        $id = $actor->getId();
        $type = PLM_Email::getAbbrevName(get_class($actor));

        $db = self::getDb();
        $db->addWhere('receiver_type', $type);
        $db->addWhere('receiver_id', $id);
        
        $db->delete();
    }

    // For EmailLogView
    public function rowTags()
    {
        PHPWS_Core::initModClass('plm', 'Nominee.php');
        
        $tpl      = array();        
        $nominee  = new Nominee($this->nominee_id);

        PHPWS_Core::initModClass('plm', 'PLM_Email.php');
        $recvr;
        $recvr_type;

        // Receiver is Reference
        if($this->receiver_type == SHORT_Reference){
            PHPWS_Core::initModClass('plm', 'Reference.php');
            $recvr = new Reference($this->receiver_id);
            $recvr_type = 'Reference';
        }
        // Recevier is Nominator
        else if($this->receiver_type == SHORT_Nominator){
            PHPWS_Core::initModClass('plm', 'Nominator.php');
            $recvr = new Nominator($this->receiver_id);
            $recvr_type = 'Nominator';
        } 
        // Receiver is Nominee
        else if($this->receiver_type == SHORT_Nominee){
            PHPWS_Core::initModClass('plm', 'Nominee.php');
            $recvr = new Nominee($this->receiver_id);
            $recvr_type = 'Nominee';
        } 
        
        $tpl['RECEIVER']     = $recvr->getLink();
        $tpl['RECEIVER_TYPE']= $recvr_type;
        $tpl['RECEIVER_TYPE_ABBREV'] = $this->receiver_type;
        $tpl['MESSAGE_TYPE'] = PLM_Email::getLongMessageType($this->message_type);
        $tpl['SENT_ON']      = strftime("%m/%d/%Y %r", $this->sent_on);

        $tpl['NOMINEE'] = $nominee->getLink();
        $tpl['ACTION']  = PHPWS_SOURCE_HTTP.'mod/plm/img/tango/actions/system-search.png';
        $tpl['RESEND']  = PHPWS_SOURCE_HTTP.'mod/plm/img/tango/actions/mail-resend.png';
        $tpl['ID']      = $this->id;

        return $tpl;
    }

    /**
     * Getters...
     */
    public function getNomineeId()
    {
        return $this->nominee_id;
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function getMessageType()
    {
        return $this->message_type;
    }
    public function getReceiverId()
    {
        return $this->message;
    }
    public function getReceiverType()
    {
        return $this->message;
    }
}
?>