<?php
  /**
   * ReferenceView
   *
   * Look at some reference details...not much though
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'Reference.php');

class ReferenceView extends PlemmView
{
    public $id;

    public function getRequestVars()
    {
        return array('view' => 'ReferenceView', 'id' => $this->id);
    }
    
    public function display(Context $context)
    {
        $tpl = array();
        
        $ref = new Reference($context['id']);
        
        $tpl['NAME'] = $ref->getFullName();
        $tpl['EMAIL'] = $ref->getEmailLink();
        $tpl['PHONE'] = $ref->getPhone();
        $tpl['DEPARTMENT'] = $ref->getDepartment();
        $tpl['RELATIONSHIP'] = $ref->getRelationship();
        
        if(isset($context['ajax'])){
            echo PHPWS_Template::process($tpl, 'plm', 'admin/reference.tpl');
            exit();
        } else {
            Layout::addPageTitle('Reference View');
            return PHPWS_Template::process($tpl, 'plm', 'admin/reference.tpl');
        }
    }
}
?>
