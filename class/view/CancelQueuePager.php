<?php
/*
 * CancelQueuePager
 *
 *   Provides admins with a pager to manage requests to cancel a nomination.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'CancelQueue.php');
PHPWS_Core::initCoreClass('DBPager.php');

class CancelQueuePager extends PlemmView {
    
    public function getRequestVars()
    {
        return array('view'=>'CancelQueuePager');
    }

    public function display(Context $form)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to see that!');
        }
        $pager = new DBPager('plm_cancel_queue', 'CancelQueue');
        $pager->setModule('plm');
        $pager->setTemplate('admin/approve_remove.tpl');
        $pager->setEmptyMessage('No nominators are requesting nomination removal at this time.');
        
        javascript('jquery');

        $pager->addRowTags('rowTags');

        return $pager->get();
    }
}

?>
