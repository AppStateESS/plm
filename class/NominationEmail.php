<?php

/**
 * NominationEmail
 *
 * Handles sending emails to various people for the nomination module.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @author Jeremy Booker
 * @package nomination
 */

PHPWS_Core::initModClass('nomination', 'exception/DatabaseException.php');
PHPWS_Core::initModClass('nomination', 'Period.php');
PHPWS_Core::initCoreClass('Mail.php');

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


class NominationEmail {

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
        $this->from    = PHPWS_Settings::get('nomination', 'email_from_address');
    }

    public function send()
    {
        $mail = new PHPWS_Mail;
        $mail->sendIndividually(true);

        foreach($this->list as $recipient){
            $mail->addSendTo($recipient);
        }

        $mail->setFrom($this->from);
        $mail->setSubject($this->subject);
        $mail->setMessageBody($this->message);

        self::logEmail($this);

        if(!EMAIL_TEST_FLAG){
            $mail->send();
        }
    }

    // Build NominationEmail from EmailMessage and send it.
    public static function sendMessageObj(EmailMessage $msg)
    {
        switch($msg->receiver_type)
        {
            case SHORT_Reference:
                PHPWS_Core::initModClass('nomination', 'Reference.php');
                $db = new PHPWS_DB('nomination_reference');
                $obj = new Reference();
                break;
            case SHORT_Nominator:
                PHPWS_Core::initModClass('nomination', 'Nominator.php');
                $db = new PHPWS_DB('nomination_nominator');
                $obj = new Nominator();
                break;
            case SHORT_Nominee:
                PHPWS_Core::initModClass('nomination', 'Nominee.php');
                $db = new PHPWS_DB('nomination_nominee');
                $obj = new Nominee();
                break;
        }

        // Get the email address.
        $db->addWhere('id', $msg->receiver_id);
        $result = $db->loadObject($obj);

        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('nomination', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
        $obj = array($obj);
        $nominationEmail = new NominationEmail($obj, $msg->subject, $msg->message, $msg->message_type);
        $nominationEmail->send();
    }

    public static function logEmail(NominationEmail $email)
    {
        // This is all kinds of messed up. Just write it to a log file for now...

        // Log the message to a text file
        $fd = fopen(PHPWS_SOURCE_DIR . 'logs/email.log',"a");
        fprintf($fd, "=======================\n");

        foreach($email->list as $recipient){
            fprintf($fd, "To: %s\n", $recipient);
        }

        fprintf($fd, "From: %s\n", $email->from);
        fprintf($fd, "Subject: %s\n", $email->subject);
        fprintf($fd, "Content: \n");
        fprintf($fd, "%s\n\n", $email->message);

        fclose($fd);

        /*
        PHPWS_Core::initModClass('nomination', 'Nominee.php');
        PHPWS_Core::initModClass('nomination', 'Nominator.php');
        PHPWS_Core::initModClass('nomination', 'Reference.php');
        PHPWS_Core::initModClass('nomination', 'EmailMessage.php');

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
        */

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

        return $lists;
    }

    //hardcoded to use the indices of the array from the above function
    // This method returns a list of email addresses, but as of right now 
    // we can't rely on the 'complete' column of the DB as an accurate 
    // determinator of if a nomination is truly complete. Once we fix that 
    // issue, this method should work perfectly.
    public static function getListMembers($list)
    {
        switch($list){
            case 'ALLNOM':  // All Nominators
                //PHPWS_Core::initModClass('nomination', 'Nominator.php');
                //$db = new PHPWS_DB('nomination_nominator');
                //$results = $db->getObjects('Nominator');
                $db = new PHPWS_DB('nomination_nomination');
                $db->addColumn('nominator_email');
                $results = $db->select('col');
                break;
            case 'NOMCPL':  // Nominators with complete nomination
                //PHPWS_Core::initModClass('nomination', 'Nominator.php');
                //$db = new PHPWS_DB('nomination_nominator');
                //$db->addTable('nomination_nomination');
                //$db->addWhere('nomination_nomination.nominator_id', 'nomination_nominator.id');
                //$db->addWhere('nomination_nomination.completed', 1);
                //$results = $db->getObjects('Nominator');
                $db = new PHPWS_DB('nomination_nomination');
                $db->addColumn('nominator_email');
                $db->addWhere('complete', 1);
                $results = $db->select('col');
                break;
            case 'NOMINC':  // Nominators with incomplete nomination
                //PHPWS_Core::initModClass('nomination', 'Nominator.php');
                //$db = new PHPWS_DB('nomination_nominator');
                //$db->addTable('nomination_nomination');
                //$db->addWhere('nomination_nomination.nominator_id', 'nomination_nominator.id');
                //$db->addWhere('nomination_nomination.completed', 0);
                //$results = $db->getObjects('Nominator');
                $db = new PHPWS_DB('nomination_nomination');
                $db->addColumn('nominator_email');
                $db->addWhere('complete', 0);
                $results = $db->select('col');
                break;
            case 'REFNON':  // References that need to upload a statement
                //PHPWS_Core::initModClass('nomination', 'Reference.php');
                //$db = new PHPWS_DB('nomination_reference');
                //$db->addWhere('doc_id', NULL);
                //$results = $db->getObjects('Reference');
                $db = new PHPWS_DB('nomination_nomination');
                $db->addTable('nomination_reference');
                $db->addColumn('nomination_reference.email');
                $db->addWhere('nomination_nomination.complete', 0);
                $db->addWhere('nomination_nomination.id', 'nomination_reference.nomination_id');
                $db->addWhere('nomination_reference.doc_id', 'NULL');
                $results = $db->select('col');
                break;
            case 'NOMINE':  // Nominees with complete nominations
                //PHPWS_Core::initModClass('nomination', 'Nominee.php');
                //$db = new PHPWS_DB('nomination_nominee');
                //$db->addTable('nomination_nomination');
                //$db->addWhere('nomination_nomination.nominee_id', 'nomination_nominee.id');
                //$db->addWhere('nomination_nomination.completed', 1);
                //$results = $db->getObjects('Nominee');
                $db = new PHPWS_DB('nomination_nomination');
                $db->addColumn('nomination_nomination.email');
                $db->addWhere('complete', 1);
                $results = $db->select('col');
                break;
        }

        if(PHPWS_Error::logIfError($results) || is_null($results)){
            throw new DatabaseException('Could not retrieve requested mailing list');
        }

        //test($results, 1);
        return $results;
    }

    /**
     * Sends a message to the nominator of a new nomination
     *
     * @param $nom Nomination
     */
    public static function newNominationNominator(Nomination $nom)
    {
        //test('got here',1);
        $vars = array();

        $vars['CURRENT_DATE'] = date('F j, Y');
        $vars['NOMINATOR_NAME'] = $nom->getNominatorFullName(); // NB: This could be an empty string for self-nominations
        $vars['NOMINEE_NAME'] = $nom->getFullName();
        $vars['AWARD_NAME'] = PHPWS_Settings::get('nomination', 'award_title');
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        //$vars['EDIT_LINK'] = $nominator->getEditLink(); //TODO nominator editing

        $vars['SIGNATURE'] = PHPWS_Settings::get('nomination', 'signature');
        $vars['SIG_POSITION'] = PHPWS_Settings::get('nomination', 'sig_position');

        $list = array($nom->getNominatorEmail());
        $subject = $vars['AWARD_NAME'];
        $msg = PHPWS_Template::process($vars, 'nomination', 'email/nominator_new_nomination.tpl');
        $msgType = 'NEWNOM';

        $email = new NominationEmail($list, $subject, $msg, $msgType);
        $email->send();
    }

    public static function updateNominationNominator(Nominator $nominator, Nominee $nominee)
    {
        $vars = array();

        $vars['NAME'] = $nominator->getFullName();
        $vars['AWARD_NAME'] = PHPWS_Settings::get('nomination', 'award_title');
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        $vars['EDIT_LINK'] = $nominator->getEditLink();

        $list = array($nominator);
        $subject = $vars['AWARD_NAME']. ' | Updated';
        $msg = PHPWS_Template::process($vars, 'nomination', 'email/nominator_update_nomination.tpl');
        $msgType = 'UPDNOM';

        $email = new NominationEmail($list, $subject, $msg, $msgType);
        $email->send();
    }

    public static function removeNominationNominator(Nominator $nominator, Nominee $nominee)
    {
        $vars = array();

        $vars['NAME'] = $nominator->getFullname();
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $vars['AWARD_NAME'] = PHPWS_Settings::get('nomination', 'award_title');

        $list = array($nominator);
        $subject = 'Nomination Removal Request Approved';
        $msg = PHPWS_Template::process($vars, 'nomination', 'email/removal_request_approved.tpl');
        $msgType = 'NOMDEL';

        $email = new NominationEmail($list, $subject, $msg, $msgType);
        $email->send();
    }

    /**
     * Sends an email to a reference listed on a new nomination
     *
     * @param $reference Reference
     * @param $nom Nomination
     */
    public static function newNominationReference(Reference $reference, Nomination $nom)
    {
        $vars = array();

        $vars['CURRENT_DATE'] = date('F j, Y');
        $vars['REF_EMAIL'] = $reference->getEmail();
        $vars['REF_NAME'] = $reference->getFullName();
        $vars['REF_PHONE'] = $reference->getPhone();
        $vars['REF_DEPARTMENT'] = $reference->getDepartment();
        $vars['REF_RELATION'] = $reference->getRelationship();
        $vars['NOMINEE_NAME'] = $nom->getFullName();
        $vars['NOMINATOR_NAME'] = $nom->getNominatorFullName();
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        $vars['REF_EDIT_LINK'] = $reference->getEditLink();
        $vars['AWARD_TITLE'] = PHPWS_Settings::get('nomination', 'award_title');

        // These have really stupid defaults, and don't need to be in settings.
        //$vars['SIGNATURE'] = PHPWS_Settings::get('nomination', 'signature');
        //$vars['SIG_POSITION'] = PHPWS_Settings::get('nomination', 'sig_position');

        $list = array($reference->getEmail());
        $subject = 'Reference Request: ' . PHPWS_Settings::get('nomination', 'award_title');
        $msg = PHPWS_Template::process($vars, 'nomination', 'email/reference_new_nomination.tpl');
        $msgType = 'NEWNOM';
        $email = new NominationEmail($list, $subject, $msg, $msgType);
        $email->send();
    }

    public static function uploadDocumentReference(Reference $reference, Nominee $nominee)
    {
        $vars = array();

        $vars['CURRENT_DATE'] = date('F j, Y');
        $vars['NAME'] = $reference->getFullName();
        $vars['NOMINEE_NAME'] = $nominee->getFullName();
        $vars['AWARD_NAME'] = PHPWS_Settings::get('nomination', 'award_title');
        $period = Period::getCurrentPeriod();
        $vars['END_DATE'] = $period->getReadableEndDate();
        $vars['EDIT_LINK'] = $reference->getEditLink();

        $list = array($reference);
        $subject = PHPWS_Settings::get('nomination', 'award_title');
        $msg = PHPWS_Template::process($vars, 'nomination', 'email/reference_letter_submit.tpl');
        $msgType = 'REFUPL';

        $email = new NominationEmail($list, $subject, $msg, $msgType);
        $email->send();
    }

    public static function updateNominationReference(Reference $reference, Nominator $nominator, Nominee $nominee)
    {
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
        $subject = PHPWS_Settings::get('nomination', 'award_title');
        $msg = PHPWS_Template::process($vars, 'nomination', 'email/reference_new_nomination.tpl');
        $msgType = 'UPDNOM';

        $email = new NominationEmail($list, $subject, $msg, $msgType);
        $email->send();
    }

}
?>
