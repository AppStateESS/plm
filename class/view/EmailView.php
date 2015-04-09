<?php

PHPWS_Core::initModClass("plm", "View.php");

/**
 * EmailView
 *
 * View a logged email by id.
 *
 * @author Robert Bost <bostrt at tux dot appstate dot edu>
 */

class EmailView extends PlemmView
{
    public function getRequestVars()
    {
        return array('view' => 'EmailView');
    }

    public function display(Context $context)
    {
        // Admins only
        if(!UserStatus::isAdmin()){
            PHPWS_Core::initModClass('plm', 'exception/PermissionException.php');
            throw new PermissionException('You are not allowed to see that!');
        }

        // ID must be set
        if(!isset($context['id'])){
            PHPWS_Core::initModClass('plm', 'exception/ContextException.php');
            throw new ContextException('ID required');
        }

        PHPWS_Core::initModClass('plm', 'EmailMessage.php');

        // Get DB and select where id = ...
        $db = EmailMessage::getDb();

        $db->addWhere('id', $context['id']);
        $db->addColumn('message');

        $result = $db->select();

        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }

        // AJAX support. 
        // @see EmailLogView and javascript/email_log
        if(isset($context['ajax'])){
            echo nl2br($result[0]['message']);
            exit();
        }else{
            return nl2br($result[0]['message']);
        }
    }
}

?>
