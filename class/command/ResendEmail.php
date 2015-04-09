<?php

  /**
   * ResendEmail
   *
   * Nominators can resend emails to those involved 
   * in their nomination.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'Command.php');

class ResendEmail extends Command
{
    private $unique_id;
    
    public function getRequestVars(){
        return array('action' => 'ResendEmail',
                     'unique_id' => $this->unique_id);
    }

    public function execute(Context $context)
    {
        if(!isset($context['unique_id'])){
            PHPWS_Core::initModClass('plm', 'exception/ContextException.php');
            throw new ContextException('Unique ID not given.');
        }

        PHPWS_Core::initModClass('plm', 'PLM_Email.php');
        PHPWS_Core::initModClass('plm', 'Nomination.php');
        
        // Get the nomination.
        $nominator = Nominator::getByUniqueId($context['unique_id']);
        $nomination = $nominator->getNomination();
        $nominee = $nomination->getNominee();

        // Resend the New Nomination email to selected users.
        $users = $context['users']; 
        foreach($users as $user){
            switch($user){
            case 'nominator':
                PLM_Email::newNominationNominator($nominator, $nominee);
                break;
            case 'ref_1':
                $ref = $nomination->getReference1();
                PLM_Email::newNominationReference($ref, $nominator, $nominee);
                break;
            case 'ref_2':
                $ref = $nomination->getReference2();
                PLM_Email::newNominationReference($ref, $nominator, $nominee);
                break;
            case 'ref_3':
                $ref = $nomination->getReference3();
                PLM_Email::newNominationReference($ref, $nominator, $nominee);
                break;
            }
        }

        $vf = new ViewFactory();
        $nomForm = $vf->get('NominationForm');
        $nomForm->unique_id = $context['unique_id'];
        $context['after'] = $nomForm;

        NQ::simple('plm', PLM_SUCCESS, 'Email(s) sent.');
    }

    public function setUniqueId($unique_id){
        $this->unique_id = $unique_id;
    }
}

?>