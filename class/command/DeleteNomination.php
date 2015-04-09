<?php
  /**
   * DeleteNomination
   *
   * Delete a nomination that has been placed in removal_request_queue.
   * Also, delete the related references, nominator, all
   * uploaded documents, and MAYBE nominee.
   * Only delete the nominee if this was the only nomination 
   * for them. 
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'Command.php');

class DeleteNomination extends Command
{

    public $nominationId;
    
    public function getRequestVars()
    {
        return array('action' => 'DeleteNomination', 'after' => 'CancelQueuePager',
                     'nominationId' => $this->nominationId);
    }

    public function execute(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowd to do that!');
        }
        // A nomination ID must be set.
        if(!isset($context['nominationId']) || $context['nominationId'] == ''){
            PHPWS_Core::initModClass('plm', 'exception/ContextException.php');
            throw new ContextException('Nomination ID is required');
        }

        PHPWS_Core::initModClass('plm', 'Nomination.php');
        PHPWS_Core::initModClass('plm', 'CancelQueue.php');
        PHPWS_Core::initModClass('plm', 'Nominator.php');
        PHPWS_Core::initModClass('plm', 'PLM_Email.php');
        
        $nomination = new Nomination($context['nominationId']);

        // Delete removal request from queue
        CancelQueue::approve($nomination);
        
        // Send an email
        $nominator = $nomination->getNominator();
        PLM_Email::removeNominationNominator($nominator, $nomination->getNominee());

        $nomination->deleteForReal();
        NQ::simple('plm', PLM_SUCCESS, 'Nomination deleted. Email sent.');
    }
}
?>
