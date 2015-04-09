<?php

  /**
   * WithdrawCancelNomination
   *
   * Withdraw a request to remove a nomination.
   * Removes from CancelQueue.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */ 

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'CancelQueue.php');
PHPWS_Core::initModClass('plm', 'Nomination.php');

class WithdrawCancelNomination extends Command
{
    public $unique_id;
    
    public function getRequestVars()
    {
        $vars = array('action' => 'WithdrawCancelNomination');
        
        if(isset($this->unique_id)){
            $vars['unique_id'] = $this->unique_id;
        }
        
        return $vars;
    }

    public function execute(Context $context)
    {
        $nom_id = Nomination::getByNominatorUnique_Id($context['unique_id']);
        $omnom  = new Nomination($nom_id['id']);

        CancelQueue::remove($omnom);

        $vf = new ViewFactory();
        $nomForm = $vf->get('NominationForm');
        $nomForm->unique_id = $context['unique_id'];
        $context['after'] = $nomForm;
    }
}



?>