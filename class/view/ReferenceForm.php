<?php

  /**
   * ReferenceForm
   *
   *   Allows References to submit their letter of recommendation.
   *
   * @author Daniel West <dwest at tux dot appstate dot edu>
   * @package plm
   */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'PLM_Doc.php');
PHPWS_Core::initModClass('plm', 'CommandFactory.php');

class ReferenceForm extends PlemmView {

    public function getRequestVars()
    {
        $vars = array('view'=>'ReferenceForm');

        if(isset($this->unique_id)){
            $vars['unique_id'] = $this->unique_id;
        }

        return $vars;
    }

    public function display(Context $context)
    {
        $factory = new CommandFactory;
        $submitCmd = $factory->get('SubmitRecommendation');
        $form = new PHPWS_Form('reference_form');
        $submitCmd->initForm($form);

        // Check if unique_id is in context
        if(isset($context['unique_id'])){
            $form->addHidden('unique_id', $context['unique_id']);
        } else {
            NQ::simple('plm', PLM_ERROR, 'Missing ID in link');
            $vFactory = new ViewFactory();
            $fof = $vFactory->get('FourOhFour');
            $fof->redirect();
        }

        $form->addSubmit('submit', 'Submit');

        $ref = Reference::getByUniqueId($context['unique_id']);
        
        // Check that we got a reference obj back
        if(is_null($ref)){
            NQ::simple('plm', PLM_ERROR, 'Invalid ID');
            $vFactory = new ViewFactory();
            $fof = $vFactory->get('FourOhFour');
            $fof->redirect();
        }

        $upload = new PLM_Doc();
        $upload->nomination = $ref; //should probably be an uploadable interface instead of this hack
        $tpl['RECOMMENDATION'] = $upload->getFileWidget('recommendation', $form);

        $form->mergeTemplate($tpl);
        
        Layout::addPageTitle('Reference Form');

        return PHPWS_Template::process($form->getTemplate(), 'plm', 'reference_form.tpl');
    }
}
?>
