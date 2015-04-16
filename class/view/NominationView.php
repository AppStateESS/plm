<?php
  /**
   * NominationView
   *
   * Shows details of nominations.
   * This is plugged inside on the NomineeView
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'Nominee.php');
PHPWS_Core::initModClass('plm', 'Nominator.php');
PHPWS_Core::initModClass('plm', 'Nomination.php');
PHPWS_Core::initModClass('plm', 'PLM_Doc.php');
PHPWS_Core::initModClass('filecabinet', 'Cabinet.php');

class NominationView extends PlemmView {
    public $nominationId;

    public function getRequestVars(){
        $vars = array('id'   => $this->nominationId,
                      'view' => 'NominationView');

        return $vars;
    }


    public function display(Context $context){
        $tpl = array();

        $nomination = new Nomination;
        $nomination->id = $context['id'];
        $nomination->load();
        $doc = new PLM_Doc($nomination);

        $nominee = new Nominee;
        $nominee->id = $nomination->nominee_id;
        $nominee->load();
        
        $nominator = new Nominator;
        $nominator->id = $nomination->nominator_id;
        $nominator->load();

        $tpl['NOMINEE'] = $nominee->getLink();
        $tpl['NOMINATOR'] = $nominator->getFullName();
        $tpl['NOMINATOR_ID'] = $nominator->getId();
        $tpl['NOMINATOR_RELATION'] = $nominator->getRelationship();

        $nominator = new Nominator;
        $nominator->id = $nomination->nominator_id;
        $nominator->load();

        //this should not happen, but it's not a db error...
        if(is_null($nominator->doc_id))
            $tpl['STATEMENT'] = 'No file uploaded';
        else 
            $tpl['STATEMENT'] = $doc->getDownloadLink($nominator->unique_id, 'Statement');

        $tpl['CATEGORY'] = $nomination->getCategory();
        $tpl['ADDED_ON'] = $nomination->getReadableAddedOn();
        $tpl['UPDATED_ON'] = $nomination->getReadableUpdatedOn();
        
        // Reference info
        for($i = 1; $i <= REFERENCE_COUNT; $i++){
            $ref_id = 'reference_id_'.$i;

            $ref = new Reference($nomination->$ref_id);
            $tpl['ID_'.$i] = $ref->getId();
            $tpl['REFERENCE_'.$i] = $ref->getFullName();
            $tpl['REFERENCE_RELATION_'.$i] = $ref->getRelationship();
            if(is_null($ref->doc_id))
                $tpl['REFERENCE_DOWNLOAD_'.$i] = 'No file uploaded';
            else
                $tpl['REFERENCE_DOWNLOAD_'.$i] = $doc->getDownloadLink($ref->unique_id);
        }
        
        $tpl['COMPLETED'] = $nomination->completed != 0 ? 'Complete' : 'Incomplete';

        javascript('jquery');
        javascriptMod('plm', 'details', array('PHPWS_SOURCE_HTTP' => PHPWS_SOURCE_HTTP));
        
        if(isset($context['ajax'])){
            echo PHPWS_Template::processTemplate($tpl, 'plm', 'admin/nomination.tpl');
            exit();
        } else {
            return PHPWS_Template::processTemplate($tpl, 'plm', 'admin/nomination.tpl'); 
        }
    }
}
?>
