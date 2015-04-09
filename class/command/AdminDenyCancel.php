<?php

  /**
   * AdminWithdraw Cancel
   * 
   * This command is for an admin to deny the request of 
   * a nominator to delete their nomination.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'Command.php');

class AdminDenyCancel extends Command
{
    public $nominationId;

    public function getRequestVars()
    {
        $vars = array('action' => 'AdminDenyCancel',
                      'after' => 'CancelQueuePager');
        
        if(isset($this->nominationId)){
            $vars['nominationId'] = $this->nominationId;
        }
        
        return $vars;
    }

    public function execute(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to do that!');
        }
        PHPWS_Core::initModClass('plm', 'Nomination.php');
        PHPWS_Core::initModClass('plm', 'CancelQueue.php');

        $nomination = new Nomination($context['nominationId']);
        CancelQueue::remove($nomination);
    }
}
?>