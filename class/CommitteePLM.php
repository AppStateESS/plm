<?php

PHPWS_Core::initModClass('plm', 'PLM.php');
PHPWS_Core::initModClass('plm', 'ViewFactory.php');

class CommitteePLM extends PLM
{
    protected $defaultView = 'NomineeSearch';

    public function process()
    {
        parent::process();
        
        $vFactory = new ViewFactory();
        
        $userView = $vFactory->get('UserView');
        $userView->setMain($this->content);
        
        $sideMenu = $vFactory->get('CommitteeMenu');
        $userView->addSideMenu($sideMenu->display($this->context));
        
        Layout::add($userView->display($this->context));
    }
}

?>