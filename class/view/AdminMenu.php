<?php

  /**
   * AdminMenu
   *
   * Side menu for administrators
   * 
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'ViewMenu.php');

class AdminMenu extends ViewMenu
{
    public function __construct()
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You do not have permission to look at this!');
        }
        $this->addViewByName('Main Menu', 'AdminMainMenu');
        $this->addViewByName('Nominees', 'NomineeSearch');
        $this->addViewByName('Nominators', 'NominatorSearch');
        $this->addViewByName('Settings', 'AdminSettings');
        $this->addLink('Control Panel', 'index.php?module=controlpanel');
    }
    
    public function getRequestVars()
    {
        return array('view' => 'AdminMenu');
    }
    
    public function display(Context $context)
    {
        return parent::display($context);
    }
}

?>
