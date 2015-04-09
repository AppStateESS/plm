<?php

/**
 * CreateNominationForm
 * 
 * Check that all required fields in form are completed, if not
 * redirect back to form with neglected fields highlighted and
 * Notification at top of form.
 * If form checks out then create nominator, references,
 * nominee, and nomination.
 *
 * @author Robert Bost <bostrt at tux dot appstate dot edu>
 */

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'PLM_Doc.php');
PHPWS_Core::initModClass('plm', 'view/NominationForm.php');

class CreateNomination extends Command
{
    public function getRequestVars()
    {
        return array('action' => 'CreateNomination', 'after' => 'ThankYouNominator');
    }

    public function execute(Context $context)
    {
        $required_fields = NominationForm::$required;
        $missing = array();
        $entered = array();

        /*****************
         * Check  fields *
         *****************/
        foreach($required_fields as $key=>$value){
            if(!isset($context[$value]) || $context[$value] == ""){
                $missing[] = $value;
            } else {
                $entered[$key] = $context[$value];
            }
        }

        // Check if nominator uploaded a statement
        if($_FILES['statement']['error'] != UPLOAD_ERR_OK){
            PHPWS_Core::initModClass('plm', 'exception/BadFormException.php');

            $context['after'] = 'NominationForm';// Set after view to the form
            $context['form_fail'] = True;// Set form fail

            $msg = 'Missing statement';
            
            if(!empty($missing)){
                // There are other fields missing
                $msg .= ' and some other fields';
            }

            $missing[] = 'statement';                
            $context['missing'] = $missing;// Add missing fields to context
            throw new BadFormException($msg);
        }
        // Check for missing required fields
        else if(!empty($missing)){
            //notify the user that they must reselect their file
            $missing[] = 'statement';
            
            $context['after'] = 'NominationForm';// Set after view to the form
            $context['missing'] = $missing;// Add missing fields to context
            $context['form_fail'] = True;// Set form fail

            // Throw exception 
            PHPWS_Core::initModClass('plm', 'exception/BadFormException.php');
            throw new BadFormException('Some fields are missing');

        } else {
            PHPWS_Core::initModClass('plm', 'Nomination.php');
            PHPWS_Core::initModClass('plm', 'Nominator.php');
            PHPWS_Core::initModClass('plm', 'Nominee.php');
            PHPWS_Core::initModClass('plm', 'Reference.php');

            $savedObjs = array(); //used for cleanup

            try{
                /*************
                 * Nominator *
                 *************/
                $first_name   = $context['nominator_first_name'];
                $middle_name  = $context['nominator_middle_name'];
                $last_name    = $context['nominator_last_name'];
                $address      = $context['nominator_address'];
                $phone        = $context['nominator_phone'];
                $email        = $context['nominator_email'];
                $relationship = $context['nominator_relationship'];

                // Create nominator 
                $nominator_id = Nominator::addNominator($first_name, $middle_name, $last_name,
                                                        $email, $phone, $address, $relationship);

                $savedObjs[] = new Nominator($nominator_id);
            
                /***********
                 * Nominee *
                 ***********/
                $first_name  = $context['nominee_first_name']; 
                $middle_name = $context['nominee_middle_name']; 
                $last_name   = $context['nominee_last_name']; 
                $email       = $context['nominee_email'];

                $sploded = explode('@', $email);
                if(!isset($sploded[1])){
                    $email .= '@appstate.edu';
                }
                $position    = $context['nominee_position'];
                $nominee_major = $context['nominee_department_major'];
                $nominee_years = $context['nominee_years'];

                $nominee_id = Nominee::addNominee($first_name, $middle_name, 
                                                  $last_name, $email, $position,
                                                  $nominee_major, $nominee_years);

                if(!$nominee_id){
                    $nominee = Nominee::getNomineeByEmail($email);
                    $nominee_id = $nominee->getId();
                }

                $savedObjs[] = new Nominee($nominee_id);

                /*************
                 * Reference *
                 *************/
                $ref_id = Null;
                for($i = 1; $i <= REFERENCE_COUNT; $i++){
                    $first_name     = $context['reference_first_name_'.$i];
                    $middle_name    = $context['reference_middle_name_'.$i];
                    $last_name      = $context['reference_last_name_'.$i];
                    $department     = $context['reference_department_'.$i];
                    $phone          = $context['reference_phone_'.$i];
                    $email          = $context['reference_email_'.$i];
                    $relationship   = $context['reference_relationship_'.$i];
                    // Create reference
                    $id = Reference::addReference($first_name, $middle_name,
                                                  $last_name, $email, $phone,
                                                  $department, $relationship);
                    $savedObjs[] = new Reference($id);
                    $ref_id[] = $id;
                }

                /**************
                 * Nomination *
                 **************/
                $category = $context['category'];

                $nomination_id = Nomination::addNomination($nominee_id, $nominator_id,
                                                           $ref_id[0], $ref_id[1], 
                                                           $ref_id[2], $category);

                $nomination = new Nomination();
                $nomination->id = $nomination_id;
                $nomination->load();

                $savedObjs[] = $nomination;

                $nominator = new Nominator($nomination->getNominatorId());

                $savedObjs[] = $nominator;

                $doc = new PLM_Doc($nomination);
                try{ 
                    $doc->receiveFile('statement', 'nominator', $nominator->unique_id);
                } catch( FileNotFoundException $e){
                    // Throw exception 
                    PHPWS_Core::initModClass('plm', 'exception/BadFormException.php');
                    throw new BadFormException('Missing statement');
                } catch( IllegalFileException $e ){
                    throw $e;
                }

                /***************
                 * Send Emails *
                 ***************/
                // Get data needed for notification emails
                $refs = $nomination->getReferences();
                $nominee = $nomination->getNominee();
                // Send emails to references
                foreach($refs as $ref){
                    PLM_Email::newNominationReference($ref, $nominator, $nominee);
                }
                // Send email to nominator
                PLM_Email::newNominationNominator($nominator, $nominee);

            } catch(Exception $e){
                // set after view
                $context['after'] = 'NominationForm';
                // set form fail flag
                $context['form_fail'] = True;
                //cleanup
                foreach($savedObjs as $obj){
                    $obj->delete();
                }
                throw $e;
            }
            
            NQ::simple('plm', PLM_SUCCESS, 'Form successfully submitted. Email sent.');
        }
    }
}
