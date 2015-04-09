<?php

  /**
   * AdminResendEmail
   *
   * Resend an email to reference, nominator, or nominee. 
   * Admins can resend to anyone. 
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */ 

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'view/AjaxMessageView.php');
PHPWS_Core::initModClass('plm', 'PLM_Email.php');
PHPWS_Core::initModClass('plm', 'EmailMessage.php');

class AdminResendEmail extends Command
{

    public function getRequestVars()
    {
        $vars = array('action' => 'ResendEmail');

        return $vars;
    }

    public function execute(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to do that!');
        }
        
        if(!isset($context['id'])){
            PHPWS_Core::initModClass('plm', 'exception/ContextException.php');
            throw new ContextException('ID expected.');
        }

        // Load the email that needs to be resent
        $message = new EmailMessage($context['id']);
        
        if($message->id == 0 || $message == null){
            PHPWS_Core::initModClass('plm', 'expcetion/DatabaseException.php');
            throw new DatabaseException('Error occured loading email message from database.');
        }
        
        PLM_Email::sendMessageObj($message);
        
        
        if(isset($context['ajax'])){
            $context['after'] = new AjaxMessageView();
            $context['after']->setMessage(true);
        }
        
        NQ::simple('plm', PLM_SUCCESS, 'Email sent.');
    }
}
?>