<?php

/**
 * PLM_Email
 *
 *   Handles sending emails to various people for the plm module.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */
PHPWS_Core::initCoreClass('Mail.php');
PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
// Abbreviated NominationActor names
// Used in DB
define('SHORT_Nominator', 'NMR');
define('SHORT_Nominee',   'NME');
define('SHORT_Reference', 'REF');

// Message types
define('NEWNOM', 'New Nomination');
define('UPDNOM', 'Updated nomination');
define('REFUPL', 'Reference document upload');
define('NOMDEL', 'Removal request approved');

define('ALLNOM', 'All nominators');
define('NOMCPL', 'Nominators with complete nomination');
define('NOMINC', 'Nominators with incomplete nomination');
define('REFNON', 'References that need to upload');
define('NOMINE', 'Nominees with complete nominations');
define('ALLREFS', 'All References');


class PLM_Email {

    public $from;
    public $list;
    public $subject;
    public $message;
    public $messageType;

    public function __construct(Array $list, $subject, $message, $msgType)
    {
        $this->list    = $list;
        $this->subject = $subject;
        $this->message = $message;
        $this->messageType = $msgType;
        $this->from    = PHPWS_Settings::get('plm', 'email_from_address');
    }

    public function send()
    {
        $mail = new PHPWS_Mail;
        $mail->sendIndividually(true);

        foreach($this->list as $index => $recipient){
            $mail->addSendTo($recipient->getEmail());
        }

        $mail->setFrom($this->from);
        $mail->setSubject($this->subject);
        $mail->setMessageBody($this->message);
        
        self::logEmail($this);
        $mail->send();
    }

    // Build PLM_Email from EmailMessage and send it.
    public static function sendMessageObj(EmailMessage $msg)
    {
        switch($msg->receiver_type)
            {
            case SHORT_Reference:
                PHPWS_Core::initModClass('plm', 'Reference.php');
                $db = new PHPWS_DB('plm_reference');
                $obj = new Reference();
                break;
            case SHORT_Nominator:
                PHPWS_Core::initModClass('plm', 'Nominator.php');
                $db = new PHPWS_DB('plm_nominator');
                $obj = new Nominator();
                break;
            case SHORT_Nominee:
                PHPWS_Core::initModClass('plm', 'Nominee.php');
                $db = new PHPWS_DB('plm_nominee');
                $obj = new Nominee();
                break;
            }

        // Get the email address.
        $db->addWhere('id', $msg->receiver_id);
        $result = $db->loadObject($obj);
        
        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
        $obj = array($obj);
        $plmEmail = new PLM_Email($obj, $msg->subject, $msg->message, $msg->message_type);
        $plmEmail->send();
    }
    
    public static function logEmail(PLM_Email $email)
    {
        PHPWS_Core::initModClass('plm', 'Nominee.php');
        PHPWS_Core::initModClass('plm', 'Nominator.php');
        PHPWS_Core::initModClass('plm', 'Reference.php');
        PHPWS_Core::initModClass('plm', 'EmailMessage.php');

        $now = mktime();

        foreach($email->list as $recipient){
            $class_name = get_class($recipient);
            $func_name = 'getBy'.$class_name.'Id';
            $nomination = Nomination::$func_name($recipient->getId());

            // Create email message object
            $message = new EmailMessage();

            $message->nominee_id    = $nomination->getNomineeId();
            $message->message       = $email->message;
            $message->message_type  = $email->messageType;
            $message->subject       = $email->subject;
            $message->receiver_id   = $recipient->getId();
            $message->receiver_type = self::getAbbrevName($class_name);
            $message->sent_on       = $now;

            $message->save();
        }
        
    }

    public static function getLongMessageType($type)
    {
        switch($type){
        case 'NEWNOM':
            return NEWNOM;
        case 'UPDNOM':
            return UPDNOM;
        case 'REFUPL':
            return REFUPL;
        case 'ALLNOM':
            return ALLNOM;
        case 'NOMCPL':
            return NOMCPL;
        case 'NOMINC':
            return NOMINC;
        case 'REFNON':
            return REFNON;
        case 'NOMINE':
            return NOMINE;
        case 'ALLREFS':
            return ALLREFS;
        default:
            return null;
        }
    }

    // TODO: Is there a way to build a constant's name with string
    public static function getAbbrevName($class)
    {
        switch($class){
        case 'Nominator':
            return SHORT_Nominator;
        case 'Reference':
            return SHORT_Reference;
        case 'Nominee':
            return SHORT_Nominee;
        }
    }

    public static function getLists()
    {
        //if you change anything about this array update the below function
        //yes it's hackish but we're "sure" that there will only be 5 lists...
        $lists = array();
        $lists['ALLNOM'] = ALLNOM;
        $lists['NOMCPL'] = NOMCPL;
        $lists['NOMINC'] = NOMINC;
        $lists['REFNON'] = REFNON;
        $lists['NOMINE'] = NOMINE;
        $lists['ALLREFS'] = ALLREFS;
        
        return $lists;
    }

    //hardcoded to use the indices of the array from the above function
    public static function getListMembers($list)
    {
        switch($list){
        case 'ALLNOM':
            PHPWS_Core::initModClass('plm', 'Nominator.php');
            $db = new PHPWS_DB('plm_nominator');
            $results = $db->getObjects('Nominator');
            break;
        case 'NOMCPL':
            PHPWS_Core::initModClass('plm', 'Nominator.php');
            $db = new PHPWS_DB('plm_nominator');
            $db->addTable('plm_nomination');
            $db->addWhere('plm_nomination.nominator_id', 'plm_nominator.id');
            $db->addWhere('plm_nomination.completed', 1);
            $results = $db->getObjects('Nominator');
            break;
        case 'NOMINC':
            PHPWS_Core::initModClass('plm', 'Nominator.php');
            $db = new PHPWS_DB('plm_nominator');
            $db->addTable('plm_nomination');
            $db->addWhere('plm_nomination.nominator_id', 'plm_nominator.id');
            $db->addWhere('plm_nomination.completed', 0);
            $results = $db->getObjects('Nominator');
            break;
        case 'REFNON':
            PHPWS_Core::initModClass('plm', 'Reference.php');
            $db = new PHPWS_DB('plm_reference');
            $db->addWhere('doc_id', NULL);
            $results = $db->getObjects('Reference');
            break;
        case 'NOMINE':
            PHPWS_Core::initModClass('plm', 'Nominee.php');
            $db = new PHPWS_DB('plm_nominee');
            $db->addTable('plm_nomination');
            $db->addWhere('plm_nomination.nominee_id', 'plm_nominee.id');
            $db->addWhere('plm_nomination.completed', 1);
            $results = $db->getObjects('Nominee');
            break;
        case 'ALLREFS':
            PHPWS_Core::initModClass('plm', 'Reference.php');
            $db = new PHPWS_DB('plm_reference');
            $db->addWhere('doc_id', NULL, '!=');
            $results = $db->getObjects('Reference');
            break;
        }

        if(PHPWS_Error::logIfError($results) || is_null($results)){
            throw new DatabaseException('Could not retrieve requested mailing list');
        }

        return $results;
    }

    /**
     * Nominator emails
     */
    public static function newNominationNominator(Nominator $nominator, Nominee $nominee)
    {
        PHPWS_Core::initModClass('plm', 'Period.php');

        $vars = array();
        
        $vars['CURRENT_DATE'] = date('F j, Y');
        $vars['NOMINATOR_NAME'] = $nominator->getFullName();
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $vars['AWARD_NAME'] = PHPWS_Settings::get('plm', 'award_title');
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        $vars['EDIT_LINK'] = $nominator->getEditLink();
        
        $list = array($nominator);
        $subject = $vars['AWARD_NAME'];
        $msg = PHPWS_Template::process($vars, 'plm', 'email/nominator_new_nomination.tpl');
        $msgType = 'NEWNOM';
        
        $email = new PLM_Email($list, $subject, $msg, $msgType);
        $email->send();
    }
    
    public static function updateNominationNominator(Nominator $nominator, Nominee $nominee)
    {
        PHPWS_Core::initModClass('plm', 'Period.php');

        $vars = array();
        
        $vars['NAME'] = $nominator->getFullName();
        $vars['AWARD_NAME'] = PHPWS_Settings::get('plm', 'award_title');
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        $vars['EDIT_LINK'] = $nominator->getEditLink();

        $list = array($nominator);
        $subject = $vars['AWARD_NAME']. ' | Updated';
        $msg = PHPWS_Template::process($vars, 'plm', 'email/nominator_update_nomination.tpl');
        $msgType = 'UPDNOM';
        
        $email = new PLM_Email($list, $subject, $msg, $msgType);
        $email->send();
    }

    public static function removeNominationNominator(Nominator $nominator, Nominee $nominee)
    {
        $vars = array();
        
        $vars['NAME'] = $nominator->getFullname();
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $vars['AWARD_NAME'] = PHPWS_Settings::get('plm', 'award_title');

        $list = array($nominator);
        $subject = 'Nomination Removal Request Approved';
        $msg = PHPWS_Template::process($vars, 'plm', 'email/removal_request_approved.tpl');
        $msgType = 'NOMDEL';
        
        $email = new PLM_Email($list, $subject, $msg, $msgType);
        $email->send();
    }

    /**
     * Reference emails
     */
    public static function newNominationReference(Reference $reference, Nominator $nominator, Nominee $nominee)
    {
        PHPWS_Core::initModClass('plm', 'Period.php');

        $vars = array();

        $vars['CURRENT_DATE'] = date('F j, Y');
        $vars['REF_EMAIL'] = $reference->getEmail();
        $vars['REF_NAME'] = $reference->getFullName();
        $vars['REF_PHONE'] = $reference->getPhone();
        $vars['REF_DEPARTMENT'] = $reference->getDepartment();
        $vars['REF_RELATION'] = $reference->getRelationship();
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $vars['NOMINATOR_NAME'] = $nominator->getFullName();
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        $vars['REF_EDIT_LINK'] = $reference->getEditLink();
        
        $list = array($reference);
        $subject = PHPWS_Settings::get('plm', 'award_title');
        $msg = PHPWS_Template::process($vars, 'plm', 'email/reference_new_nomination.tpl');
        $msgType = 'NEWNOM';
        
        $email = new PLM_Email($list, $subject, $msg, $msgType);
        $email->send();
    }
    
    public static function uploadDocumentReference(Reference $reference, Nominee $nominee)
    {
        $vars = array();
        
        $vars['CURRENT_DATE'] = date('F j, Y');
        $vars['NAME'] = $reference->getFullName();
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $vars['AWARD_NAME'] = PHPWS_Settings::get('plm', 'award_title');
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        $vars['EDIT_LINK'] = $reference->getEditLink();
        
        $list = array($reference);
        $subject = PHPWS_Settings::get('plm', 'award_title');
        $msg = PHPWS_Template::process($vars, 'plm', 'email/reference_letter_submit.tpl');
        $msgType = 'REFUPL';
        
        $email = new PLM_Email($list, $subject, $msg, $msgType);
        $email->send();
    }
    
    public static function updateNominationReference(Reference $reference, Nominator $nominator, Nominee $nominee)
    {
        PHPWS_Core::initModClass('plm', 'Period.php');

        $vars = array();

        $vars['CURRENT_DATE'] = date('F j, Y');
        $vars['REF_EMAIL'] = $reference->getEmail();
        $vars['REF_NAME'] = $reference->getFullName();
        $vars['REF_PHONE'] = $reference->getPhone();
        $vars['REF_DEPARTMENT'] = $reference->getDepartment();
        $vars['REF_RELATION'] = $reference->getRelationship();
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $vars['NOMINATOR_NAME'] = $nominator->getFullName();
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        $vars['REF_EDIT_LINK'] = $reference->getEditLink();
        
        $list = array($reference);
        $subject = PHPWS_Settings::get('plm', 'award_title');
        $msg = PHPWS_Template::process($vars, 'plm', 'email/reference_new_nomination.tpl');
        $msgType = 'UPDNOM';
        
        $email = new PLM_Email($list, $subject, $msg, $msgType);
        $email->send();
    }

}
?>
