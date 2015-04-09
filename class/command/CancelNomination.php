<?php

/*
 * CancelNomination
 *
 *   Puts the nomination into the 'pending_removal' queue.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'CancelQueue.php');

class CancelNomination extends Command {
    public $unique_id;

    public function getRequestVars()
    {
        $vars = array('action'=>'CancelNomination');
        
        if(isset($this->unique_id)){
            $vars['unique_id'] = $this->unique_id;
        }

        return $vars;
    }

    public function execute(Context $context)
    {
        $nom_id = Nomination::getByNominatorUnique_Id($context['unique_id']);
        $omnom  = new Nomination;
        $omnom->id = $nom_id['id'];
        $omnom->load();

        CancelQueue::add($omnom);

        $vf = new ViewFactory();
        $nomForm = $vf->get('NominationForm');
        $nomForm->unique_id = $context['unique_id'];
        $context['after'] = $nomForm;
    }
}

?>