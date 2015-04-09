<?php

  /**
   * NominatorView
   * 
   * See details about a nominator. 
   * Supports Ajax
   */ 

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'Nominator.php');
PHPWS_Core::initModClass('plm', 'Nomination.php');

class NominatorView extends PlemmView 
{
    public $nominatorId;

    public function getRequestVars(){
        $vars = array('id'   => $this->nominatorId,
                      'view' => 'NominatorView');

        return $vars;
    }

    public function display(Context $context)
    {
        if(!(UserStatus::isCommitteeMember() || UserStatus::isAdmin())){
            throw new PermissionException('You are not allowed to see that!');
        }

        $tpl = array();

        $nominator = new Nominator($context['id']);

        $tpl['NAME']    = $nominator->getFullName();
        $tpl['EMAIL']   = $nominator->getEmailLink();
        $tpl['PHONE']   = $nominator->getPhone();
        $tpl['ADDRESS'] = $nominator->getAddress();
        $tpl['RELATIONSHIP'] = $nominator->getRelationship();

        if(isset($context['ajax'])){
            echo PHPWS_Template::process($tpl, 'plm', 'admin/nominator.tpl');
            exit();
        } else {
            Layout::addPageTitle('Nominator View');
            return PHPWS_Template::process($tpl, 'plm', 'admin/nominator.tpl');
        }
    }
}
?>
