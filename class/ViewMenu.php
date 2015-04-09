<?php

PHPWS_Core::initModClass('plm', 'View.php');

abstract class ViewMenu extends PlemmView
{
    private $views = array();

    public function addViewByName($text, $view)
    {
        $vFactory = new ViewFactory();
        
        $viewObj = $vFactory->get($view);
        
        $this->addView($text, $viewObj);
    }

    public function addView($text, $view)
    {
        $this->views[] = $view->getLink($text);
    }

    public function addLink($text, $link)
    {
        $this->views[] = "<a href=$link>$text</a>";
    }

    public function buildTemplate()
    {
        $tpl = array();

        foreach($this->views as $view){
            $tpl['view_links'][] = array('LINK' => $view);
        }
        return $tpl;
    }
    
    public function display(Context $context)
    {
        $tpl = $this->buildTemplate();
        return PHPWS_Template::process($tpl, 'plm', 'admin/menu.tpl');
    }
}

?>
