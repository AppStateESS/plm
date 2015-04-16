<?php

  /**
   * NominationMainMenu
   *
   * This interfaces with Nomination's ViewFactory
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('nomination', 'othermenu/MainMenu.php');

class NominationMainMenu extends MainMenu
{
    public function addMenuItemByName($name, $text, $tag=null, $parentTag=null)
    {
        $vFactory = new ViewFactory();
        $view = $vFactory->get($name);
        
        $this->addMenuItem($view->getLink($text), $tag, $parentTag);
    }
}
?>
