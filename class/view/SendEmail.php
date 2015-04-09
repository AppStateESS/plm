<?php

/**
 * SendEmail
 *
 *   Admin interface for sending email messages to different groups of individuals of interest in PLM.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'PLM_Email.php');
PHPWS_Core::initModClass('plm', 'CommandFactory.php');

class SendEmail extends PlemmView {

    public function getRequestVars()
    {
        $vars = array('view'=>'SendEmail');

        return $vars;
    }

    public function display(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to see this!');
        }

        $cf = new CommandFactory;
        $cmd = $cf->get('SubmitReviewEmail');

        $form = new PHPWS_Form('email');
        $cmd->initForm($form);

        $form->addDropBox('list', PLM_Email::getLists());
        $form->setLabel('list', 'Recipients');
        $form->addText('subject');
        $form->setLabel('subject', 'Subject');
        $form->addTextArea('message');
        $form->setLabel('message', 'Message');
        $form->addSubmit('Submit');

        if(isset($_SESSION['review'])){
            $form->plugIn($_SESSION['review']);
            unset($_SESSION['review']);
        }

        Layout::addPageTitle('Send Email');

        return PHPWS_Template::process($form->getTemplate(), 'plm', 'admin/email_form.tpl');
    }
}
?>
