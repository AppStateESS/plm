<?php

  /**
   * PLMMainMenu
   *
   * This interfaces with PLM's ViewFactory
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('othermenu', 'MainMenu.php');

class PLMMainMenu extends MainMenu
{
    public function addMenuItemByName($name, $text, $tag=null, $parentTag=null)
    {
        $vFactory = new ViewFactory();
        $view = $vFactory->get($name);
        
        $this->addMenuItem($view->getLink($text), $tag, $parentTag);
    }
}
?>