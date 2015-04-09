<?php

/**
 * SubmitReviewEmail
 *
 *   ***Hack*** to get around the post/get restriction on commands/view,
 * someday when we have time we'll fix the controller.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

PHPWS_Core::initModClass('plm', 'Command.php');

class SubmitReviewEmail extends Command {
    
    public function getRequestVars()
    {
        $vars = array('action'=>'SubmitReviewEmail');

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

        //pass this forward to the view
        $_SESSION['review'] = $review;

        $context['after'] = 'ReviewEmail';
    }
}
?>