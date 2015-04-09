<?php

/**
 * EditEmail
 *
 *   Edit a email message.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'PLM_Email.php');

class EditEmail extends Command {
    public $from;
    public $list;
    public $subject;
    public $message;
    
    public function getRequestVars()
    {
        $vars = array('action'=>'EditEmail');

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
            throw new PermissionException('You are not allowed to see this!');
        }

        //can't session object with serialization witchery...
        $review = array();
        $review['from'] = $context['from'];
        $review['list'] = $context['list'];
        $review['subject'] = $context['subject'];
        $review['message'] = $context['message'];

        $_SESSION['review'] = $review;

        $context['after'] = 'SendEmail';
    }
}
?>