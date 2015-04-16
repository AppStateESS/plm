<?php

/**
 * Menu
 *
 * This represents the top level menu.
 *
 * @author Robert Bost <bostrt at gmail dot com>
 */

PHPWS_Core::initModClass('nomination', 'othermenu/SubMenu.php');

class OtherMenu extends SubMenu
{
  public function addMenuItem($text, $tag=null, $parentTag=null)
  {
    // find the menu they want to add an item to
    $menu = $this->getMenuByTag($parentTag);
    
    // If tag isn't given then make one for them
    if(is_null($tag)){
      $tag = $menu->getTag() . '-item-' . $menu->getMenuItemCount();
    }

    // Create the item
    $item = new MenuItem($text, $tag);
    
    $menu[$tag] = $item;
  }

  /**
   * Insert new sub menu at index $tag
   */
  public function addSubMenu($text, $tag=null, $parentTag=null)
  {
    // find the menu they want to add another menu to
    $menu = $this->getMenuByTag($parentTag);

    // If tag isn't given then make one for them
    if(is_null($tag)){
      $tag = $menu->getTag() . '-submenu-' . $menu->getSubMenuCount();
    }
    // Create sub menu
    $subMenu = new SubMenu($text, $tag);

    // Add the menu
    $menu[$tag] = $subMenu;
  }

  public function show()
  {
    $tpl = array();
    
    $tpl['TOP_MENU_TAG']  = $this->tag;
    $tpl['TOP_MENU_TEXT'] = $this->text;

    foreach($this->container as $item){
      $tpl['menu_items'][] = array('CONTENT' => $item->show());
    }
    
    return PHPWS_Template::process($tpl, 'nomination', 'othermenu/menu.tpl');
  }
}

?>
