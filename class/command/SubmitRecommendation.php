<?php
  /**
   * SubmitRecommendation
   *
   *  Save the letter of recommendation submitted by a Reference.
   *
   * @author Daniel West <dwest at tux dot appstate dot edu>
   * @package plm
   */

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'Nomination.php');
PHPWS_Core::initModClass('plm', 'PLM_Doc.php');
PHPWS_Core::initModClass('plm', 'Reference.php');

class SubmitRecommendation extends Command {

    public function getRequestVars()
    {
        $vars = array('action'=>'SubmitRecommendation', 
                      'after' =>'ThankYouReference');

        return $vars;
    }

    public function execute(Context $context)
    {
        $nomination = Nomination::getByReferenceUnique_Id($context['unique_id']);

        $doc = new PLM_Doc($nomination);

        try{
            $doc->receiveFile('recommendation', 'reference', $context['unique_id']);
        } catch (Exception $e){
            NQ::simple('plm', PLM_ERROR, $e->getMessage());
            $vf = new ViewFactory();
            $view = $vf->get('ReferenceForm');
            $view->unique_id = $context['unique_id'];
            $context['after'] = $view;
            return;
        }
        
        // Check if nomination is completed now...
        $nomination->checkCompletion();
        
        // Send notification email
        $nominee = $nomination->getNominee();
        $ref = Reference::getByUniqueId($context['unique_id']);
        PLM_Email::uploadDocumentReference($ref, $nominee);

        NQ::simple('plm', PLM_SUCCESS, 'Thank you!');
    }
}
?>