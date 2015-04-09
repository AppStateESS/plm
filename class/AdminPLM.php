<?php

PHPWS_Core::initModClass('plm', 'PLM.php');
PHPWS_Core::initModClass('plm', 'ViewFactory.php');

class AdminPLM extends PLM
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