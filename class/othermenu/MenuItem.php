<?php
class MenuItem
{
  protected $tag;
  protected $text;

  public function __construct($text, $tag)
  {
    $this->text = $text;
    $this->tag  = $tag;
  }

  public function show()
  {
    $tpl = array();
    
    $tpl['TAG'] = $this->tag;
    $tpl['TEXT'] = $this->text;

    return PHPWS_Template::process($tpl, 'nomination', 'othermenu/menuitem.tpl');
  }

  public function getText(){
    return $this->text;
  }
  public function getTag(){
    return $this->tag;
  }
}

?>
