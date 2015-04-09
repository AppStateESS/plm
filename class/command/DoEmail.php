<?php

/**
 * DoEmail
 *
 *   Actually send emails.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'PLM_Email.php');

class DoEmail extends Command {
    public $from;
    public $list;
    public $subject;
    public $message;
    
    public function getRequestVars()
    {
        $vars = array('action'=>'DoEmail');
        
        if(isset($this->from)){
            $vars['from'] = $this->from;
        }
        if(isset($this->list)){
            $vars['list'] = $this->list;
        }
        if(isset($this->subject)){
            $vars['subject'] = $this->subject;
        }
        if(isset($this->message)){
            $vars['message'] = $this->message;
        }

        return $vars;
    }

    public function execute(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to do this!');
        }

        try{
            $msgType = $context['list'];
            $mail = new PLM_Email(PLM_Email::getListMembers($msgType), $context['subject'], $context['message'], $msgType);
            $mail->send();
            NQ::simple('plm', PLM_SUCCESS, 'Emails sent');
        } catch(DatabaseException $e){
            NQ::simple('plm', PLM_ERROR, $e->getMessage());
        }
    }
}
?>