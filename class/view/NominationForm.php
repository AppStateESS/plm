<?php

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'PLM_Doc.php');
PHPWS_Core::initModClass('plm', 'CommandFactory.php');
PHPWS_Core::initModClass('plm', 'FallthroughContext.php');
PHPWS_Core::initModClass('plm', 'CancelQueue.php');

class NominationForm extends PlemmView
{
    public static $required = array('nominee_first_name',
                                       'nominee_last_name',
                                       'nominee_email',
                                       'nominator_first_name',
                                       'nominator_last_name',
                                       'nominator_email',
                                       'nominator_phone',
                                       'nominator_address',
                                       'reference_first_name_1',
                                       'reference_last_name_1',
                                       'reference_email_1',
                                       'reference_phone_1',
                                       'reference_first_name_2',
                                       'reference_last_name_2',
                                       'reference_email_2',
                                       'reference_phone_2',
                                       'reference_first_name_3',
                                       'reference_last_name_3',
                                       'reference_email_3',
                                       'reference_phone_3');

    public function getRequestVars()
    {
        $vars = array('view' => 'NominationForm');
        
        if(isset($this->unique_id)){
            $vars['unique_id'] = $this->unique_id;
        }
        
        return $vars;
    }

    public function display(Context $context)
    {
        $tpl = array();
        $cmdFactory = new CommandFactory();

        // Check if nomination period has ended
        PHPWS_Core::initModClass('plm', 'Period.php');
        if(Period::isOver()){
            $currPeriod = Period::getCurrentPeriod();
            $end = $currPeriod->getReadableEndDate();
            return '<h2>Nomination period ended on '.$end.'.</h2>';
        }
        else if(!Period::hasBegun()){
            $currPeriod = Period::getCurrentPeriod();
            $begin = $currPeriod->getReadableStartDate();
            return '<h2>Nomination period will begin on '.$begin.'.</h2>';
        }
        PHPWS_Core::initCoreClass('Form.php');

        $c = new FallthroughContext(array());
        $c->addFallthrough($context);

        /**
         * These forms are displayed if the Nominator is editing the nomination
         */
        if(isset($context['unique_id'])){
            //setup the fallthrough context
            $nomination = Nomination::getByNominatorUnique_Id($context['unique_id']);
            $c->addFallthrough($nomination);

            //...and add a button for the nominator to cancel their nomination
            // or remove the request to delete their nomination
            $cancelForm = new PHPWS_Form('cancel_nominationForm');

            if(CancelQueue::contains($nomination['id'])){
                $cmd = $cmdFactory->get('WithdrawCancelNomination');
                $cancelForm->addSubmit('Remove Request');
            } else {
                $cmd = $cmdFactory->get('CancelNomination');
                $cancelForm->addSubmit('Submit Request');
            }
            $cmd->unique_id = $context['unique_id'];
            $cmd->initForm($cancelForm);

            $tpl['cancel']['CANCEL_BUTTON'] = $cancelForm->getTemplate();

            // Resend email form
            $resendForm = new PHPWS_Form('resend_email_form');
            $users = array('nominator'=>'Nominator (self)', 'ref_1'=>'Reference 1',
                           'ref_2'=>'Reference 2', 'ref_3'=>'Reference 3');
            $resendForm->addCheckAssoc('users', $users);
            $resendForm->addSubmit('Submit');

            $resendCmd = $cmdFactory->get('ResendEmail');
            $resendCmd->setUniqueId($context['unique_id']);
            $resendCmd->initForm($resendForm);

            $tpl['resend']['RESEND_FORM'] = $resendForm->getTemplate();
        }

        $form = new PHPWS_Form('nomination_form');

        if(!isset($c['unique_id'])){
            $submitCmd = $cmdFactory->get('CreateNomination');
        } else {
            $submitCmd = $cmdFactory->get('EditNomination');
            $submitCmd->unique_id = $c['unique_id'];
        }

        $submitCmd->initForm($form);

        $tpl['AWARD_TITLE'] = PHPWS_Settings::get('plm', 'award_title');

        /****************
         * Nominee Info *
         ****************/  
        $form->addText('nominee_first_name', isset($c['nominee_first_name']) ? $c['nominee_first_name'] : '');
        $form->addText('nominee_middle_name', isset($c['nominee_middle_name']) ? $c['nominee_middle_name'] : '');
        $form->addText('nominee_last_name', isset($c['nominee_last_name']) ? $c['nominee_last_name'] : '');
        $form->addText('nominee_email', isset($c['nominee_email']) ? $c['nominee_email'] : '');
        $form->addText('nominee_position', isset($c['nominee_position']) ? $c['nominee_position'] : '');
        $form->addText('nominee_department_major', isset($c['nominee_major']) ? $c['nominee_major'] : '');
        $form->addText('nominee_years', isset($c['nominee_years']) ? $c['nominee_years'] : '');

        $form->setLabel('nominee_first_name',       'First name: ');
        $form->setLabel('nominee_middle_name',      'Middle name: ');
        $form->setLabel('nominee_last_name',        'Last name: ');
        $form->setLabel('nominee_email',            'ASU Email: ');
        $form->setLabel('nominee_position',         'Position on Campus: ');
        $form->setLabel('nominee_department_major', 'Department/Major: ');
        $form->setLabel('nominee_years',            'Years at Appalachian: ');


        /************
         * Category *
         ************/
        $category_radio = array(PLM_STUDENT_LEADER, PLM_STUDENT_EDUCATOR, PLM_FACULTY_MEMBER, PLM_EMPLOYEE);
        $form->addRadio('category', $category_radio);
        $form->setMatch('category', isset($c['category']) ? $c['category'] : PLM_STUDENT_LEADER);

        /*************
         * Refernces *
         *************/
        for($i = 1; $i <= REFERENCE_COUNT; $i++){
            $form->addText('reference_first_name_'.$i, isset($c['reference_first_name_'.$i]) ? $c['reference_first_name_'.$i] : '');
            $form->addText('reference_middle_name_'.$i, isset($c['reference_middle_name_'.$i]) ? $c['reference_middle_name_'.$i] : '');
            $form->addText('reference_last_name_'.$i, isset($c['reference_last_name_'.$i]) ? $c['reference_last_name_'.$i] : '');
            $form->addText('reference_department_'.$i, isset($c['reference_department_'.$i]) ? $c['reference_department_'.$i] : '');
            $form->addText('reference_email_'.$i, isset($c['reference_email_'.$i]) ? $c['reference_email_'.$i] : '');
            $form->addText('reference_phone_'.$i, isset($c['reference_phone_'.$i]) ? $c['reference_phone_'.$i] : '');
            $form->addText('reference_relationship_'.$i, isset($c['reference_relationship_'.$i]) ? $c['reference_relationship_'.$i] : '');

            $form->setLabel('reference_first_name_'.$i, 'First Name: ');
            $form->setLabel('reference_middle_name_'.$i, 'Middle Name: ');
            $form->setLabel('reference_last_name_'.$i, 'Last Name: ');
            $form->setLabel('reference_department_'.$i, 'Department: ');
            $form->setLabel('reference_email_'.$i, 'Email: ');
            $form->setLabel('reference_phone_'.$i, 'Telephone: ');
            $form->setLabel('reference_relationship_'.$i, 'Relation to Nominee: ');
        }

        /*************
         * Statement *
         *************/
        if(!isset($nomination)){
            $upload = new PLM_Doc();
            $tpl['STATEMENT'] = $upload->getFileWidget('statement', $form);
        } else {
            $omnom = new Nomination;
            $omnom->id = $nomination['id'];
            $omnom->load();
            $upload = new PLM_Doc($omnom);
            $nominator = new Nominator($omnom->nominator_id);
            $omnom->doc_id = $nominator->doc_id;
            $tpl['STATEMENT'] = $upload->getFileWidget('statement', $form, $context['unique_id']);
        }

        /******************
         * Nominator Info *
         ******************/
        $form->addText('nominator_first_name', isset($c['nominator_first_name']) ? $c['nominator_first_name'] : '');
        $form->addText('nominator_middle_name', isset($c['nominator_middle_name']) ? $c['nominator_middle_name'] : '');
        $form->addText('nominator_last_name', isset($c['nominator_last_name']) ? $c['nominator_last_name'] : '');
        $form->addText('nominator_address', isset($c['nominator_address']) ? $c['nominator_address'] : '');
        $form->addText('nominator_phone', isset($c['nominator_phone']) ? $c['nominator_phone'] : '');
        $form->addText('nominator_email', isset($c['nominator_email']) ? $c['nominator_email'] : '');
        $form->addText('nominator_relationship', isset($c['nominator_relationship']) ? $c['nominator_relationship'] : '');

        $form->setLabel('nominator_first_name', 'Nominator\'s first name: ');
        $form->setLabel('nominator_middle_name', 'Nominator\'s middle name: ');
        $form->setLabel('nominator_last_name', 'Nominator\'s last name: ');
        $form->setLabel('nominator_address', 'ASU Address: ');
        $form->setLabel('nominator_phone', 'ASU Telephone: ');
        $form->setLabel('nominator_email', 'ASU E-Mail: ');
        $form->setLabel('nominator_relationship', 'Relation to Nominee: ');


        // Check if we were redirected back to this
        // form because some fields were not entered
        // If form_fail is true then it did fail
        if(isset($c['form_fail']) && $c['form_fail']){
            $vars = array('FIELDS' => json_encode($c['missing']),
                          'PHPWS_SOURCE_HTTP' => PHPWS_SOURCE_HTTP);
            javascript('jquery_ui');
            javascriptMod('plm', 'highlight', $vars);
        }

        $form->addSubmit('submit', 'Submit');

        // Showtime!
        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();

        Layout::addPageTitle('Nomination Form');

        $result = PHPWS_Template::process($tpl, 'plm', 'plm_nomination_form.tpl');

        return $result;
    }
}
?>
