<?php

PHPWS_Core::initModClass('nomination', 'NominationMod.php');
PHPWS_Core::initModClass('nomination', 'ViewFactory.php');

class AdminNomination extends NominationMod
{
    protected $defaultView = 'AdminMainMenu';
    
    public function process()
    {
        parent::process();
        
        $vFactory = new ViewFactory();

        $userView = $vFactory->get('UserView');
        $userView->setMain($this->content);

        $sideMenu = $vFactory->get('AdminMenu');
        $userView->addSideMenu($sideMenu->display($this->context));
        Layout::add($userView->display($this->context));
    }
    
}

?>