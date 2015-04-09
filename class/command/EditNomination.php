<?php

/**
 * EditNomination
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

class EditNomination extends Command
{
    public $unique_id;

    public function getRequestVars()
    {
        $vars = array('action' => 'EditNomination', 'after' => 'ThankYouNominator');

        if(isset($this->unique_id)){
            $vars['unique_id'] = $this->unique_id;
        }

        return $vars;
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

        if(!empty($missing)){
            // Set after view to the form
            $context['after'] = 'NominationForm';

            // Add missing fields to context
            $context['missing'] = $missing;

            // Set form fail
            $context['form_fail'] = True;

            // Throw exception 
            PHPWS_Core::initModClass('plm', 'exception/BadFormException.php');
            throw new BadFormException('Some fields are missing');

        } else {
            PHPWS_Core::initModClass('plm', 'Nomination.php');
            PHPWS_Core::initModClass('plm', 'Nominator.php');
            PHPWS_Core::initModClass('plm', 'Nominee.php');
            PHPWS_Core::initModClass('plm', 'Reference.php');

            $nomination = Nomination::getByNominatorUnique_Id($context['unique_id']);
            //there is a lot of extra crap returned by the above function, and it's not an object...
            $nom_id = $nomination['id'];
            $nomination = new Nomination;
            $nomination->id = $nom_id;
            $nomination->load();

            $savedObjs = array(); //used for cleanup

            /*************
             * Nominator *
             *************/
            $nominator               = $nomination->getMember($context['unique_id']);
            $nominator->first_name   = $context['nominator_first_name'];
            $nominator->middle_name  = $context['nominator_middle_name'];
            $nominator->last_name    = $context['nominator_last_name'];
            $nominator->address      = $context['nominator_address'];
            $nominator->phone        = $context['nominator_phone'];
            $nominator->email        = $context['nominator_email'];
            $nominator->relationship = $context['nominator_relationship'];
            $nominator->save();
            
            /***********
             * Nominee *
             ***********/
            $nominee              = $nominator->getNomination()->getNominee();
            $nominee->first_name  = $context['nominee_first_name']; 
            $nominee->middle_name = $context['nominee_middle_name']; 
            $nominee->last_name   = $context['nominee_last_name']; 
            $nominee->email       = $context['nominee_email'];
            $nominee->position    = $context['nominee_position'];
            $nominee->major = $context['nominee_department_major'];
            $nominee->years = $context['nominee_years'];
            $nominee->save();

            /*************
             * Reference *
             *************
             *
             * @pay-attention
             * Facts:
             *    - References can be edited. 
             *    - Three references are required all the time
             * Only if the email is changed will we create a new reference and delete
             * the old one, creating a new unique_id in the process.
             */
            for($i = 1; $i <= REFERENCE_COUNT; $i++){

                // Build accessor methods
                $get_ref_id = "getReferenceId".$i;
                $set_ref_id = "setReferenceId".$i;

                // Get data from form
                $first_name     = $context['reference_first_name_'.$i];
                $middle_name    = $context['reference_middle_name_'.$i];
                $last_name      = $context['reference_last_name_'.$i];
                $department     = $context['reference_department_'.$i];
                $phone          = $context['reference_phone_'.$i];
                $email          = $context['reference_email_'.$i];
                $relation       = $context['reference_relationship_'.$i];

                // Get matching Reference Object
                $reference = new Reference($nomination->$get_ref_id());
                
                // Check if email has changed...
                if($reference->getEmail() == $email){
                    // If email is same then just Update
                    $reference->setFirstName($first_name);
                    $reference->setMiddleName($middle_name);
                    $reference->setLastName($last_name);
                    $reference->setDepartment($department);
                    $reference->setPhone($phone);
                    $reference->setRelationship($relation);
                    // DO NOT UPDATE ---> $reference->setEmail($email);
                    
                    $reference->save();
                } else {
                    // Delete document then old reference (In that order!)
                    // Also delete all entries in plm_email_log
                    PHPWS_Core::initModClass('plm', 'EmailMessage.php');
                    EmailMessage::deleteMessages($reference, SHORT_Reference);

                    PLM_Doc::delete($reference->getUniqueId());
                    $reference->delete();

                    // Create reference
                    $id = Reference::addReference($first_name, $middle_name,
                                                  $last_name, $email, $phone,
                                                  $department, $relation);
                    $ref = new Reference($id);

                    // Set the reference id
                    $nomination->$set_ref_id($id);

                    // save our new reference id
                    $nomination->save();

                    // Email new reference
                    PLM_Email::updateNominationReference($ref, $nominator, $nominee);
                    NQ::simple('plm', PLM_SUCCESS, 'Email sent to '.$ref->getFullName());
                }

                // Update 'completed' status for nomination
                $nomination->checkCompletion();
            }

            /**************
             * Nomination *
             **************/
            $nomination->category = $context['category'];

            $doc = new PLM_Doc($nomination);
            try{
                // TODO: Remove old file
                $doc->receiveFile('statement', 'nominator', $nominator->unique_id);
            } catch( IllegalFileException $e ){
                NQ::simple('plm', PLM_ERROR, $e->getMessage());
                PHPWS_Core::initModClass('plm', 'ViewFactory.php');
                $vf = new ViewFactory();
                $view = $vf->get('NominationForm');
                $view->unique_id = $context['unique_id'];
                $context['after'] = $view;
                return;
            } catch( FileException $e ){
                //they don't have to upload a file every time, just the first
            }

            $nomination->save(); //save changes

            // Send email
            PLM_Email::updateNominationNominator($nominator, $nominee);
            NQ::simple('plm', PLM_SUCCESS, 'Form successfully updated.');
        }
    }
}
?>