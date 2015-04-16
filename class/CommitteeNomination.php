<?php

PHPWS_Core::initModClass('nomination', 'Nomination.php');
PHPWS_Core::initModClass('nomination', 'ViewFactory.php');

class CommitteeNomination extends Nomination
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