<?php
  /**
   * Nomination
   *
   * Nomination is the glue for references, a nominator, and a nominee.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'PLM_Model.php');
PHPWS_Core::initModClass('plm', 'Nominator.php');
PHPWS_Core::initModClass('plm', 'Reference.php');
PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
PHPWS_Core::initModClass('plm', 'view/NominationView.php');

define('NOMINATION_TABLE', 'plm_nomination');

class Nomination extends PLM_Model
{
    public $nominee_id;
    public $nominator_id;
    public $reference_id_1;
    public $reference_id_2;
    public $reference_id_3;
    public $category;
    public $completed;
    public $period;
    public $winner;
    public $added_on;
    public $updated_on;

    public function getDb()
    {
        return new PHPWS_DB(NOMINATION_TABLE);
    }
    
    // Override save from PLM_Model, set teh udpated_on everytime there is a save
    public function save()
    {
        $this->updated_on = time();
        return parent::save();
    }

    /**
     * Add nomination to DB
     *
     * @param *_id - id in DB for each nomination actor
     * @param category - The category for the nomination, see inc/defines.php for listing
     */
    public static function addNomination($nominee_id, $nominator_id, $reference_id_1,
                                         $reference_id_2, $reference_id_3, $category)
    {
        $nom = new Nomination();
        
        $nom->nominee_id     = $nominee_id;
        $nom->nominator_id   = $nominator_id;
        $nom->reference_id_1 = $reference_id_1;
        $nom->reference_id_2 = $reference_id_2;
        $nom->reference_id_3 = $reference_id_3;
        $nom->category       = $category;
        $nom->completed = 0;
        $nom->period = PHPWS_Settings::get('plm', 'current_period');
        /* $nom->winner should be null */
        $now = time();
        $nom->added_on = $now;
        $nom->updated_on = $now;

        $result = $nom->save();

        return $result;
    }

    /** 
     * Getters...
     */
    public function getNomineeId(){
        return $this->nominee_id;
    }
    public function getNominatorId(){
        return $this->nominator_id;
    }
    public function getReferenceId1(){
        return $this->reference_id_1;
    }
    public function getReferenceId2(){
        return $this->reference_id_2;
    }
    public function getReferenceId3(){
        return $this->reference_id_3;
    }
    public function getCategory(){
        switch($this->category)
            {
            case PLM_STUDENT_LEADER:
                return PLM_STUDENT_LEADER_TEXT;
            case PLM_STUDENT_EDUCATOR:
                return PLM_STUDENT_EDUCATOR_TEXT;
            case PLM_FACULTY_MEMBER:
                return PLM_FACULTY_MEMBER_TEXT;
            case PLM_EMPLOYEE:
                return PLM_EMPLOYEE_TEXT;
            default:
                return null;
            }
    }
    public function getPeriod(){
        PHPWS_Core::initModClass('plm', 'Period.php');
        return Period::getPeriodByYear($this->period);
    }
    public function getPeriodYear(){
        return $this->period;
    }
    public function isCompleted(){
        return $this->completed;
    }
    public function getAddedOn(){
        return $this->added_on;
    }
    public function getReadableAddedOn(){
        return strftime("%B %d, %Y", $this->getAddedOn());
    }
    public function getUpdatedOn(){
        return $this->updated_on;
    }
    public function getReadableUpdatedOn(){
        return strftime("%B %d, %Y", $this->getUpdatedOn());
    }
    public function isWinner(){
        if(is_null($this->winner)){
            return False;
        } else {
            return True;
        }
    }
    
    /**
     * Setters...
     */
    public function setNomineeId($x){
        $this->nomineeId = $x;
    }
    public function setNominatorId($x){
        $this->nominatorId = $x;
    }
    public function setReferenceId1($refId1){
        $this->reference_id_1 = $refId1;
    }
    public function setReferenceId2($refId2){
        $this->reference_id_2 = $refId2;
    }
    public function setReferenceId3($refId3){
        $this->reference_id_3 = $refId3;
    }
    public function setCategory($x){
        $this->category = $x;
    }
    public function setCompleted($x){
        $this->completed = $x;
    }
    public function setAddedOn($added){
        $this->added_on = $added;
    }
    public function setUpdatedOn($updated){
        $this->updated_on = $updated;
    }
    public function setWinner($winner){
        if(!$winner){
            $this->winner = Null;
        } else {
            $this->winner = True;
        }
    }

    /**
     * Utilities
     */
    /**
     *  Get link to view nomination
     *  Default text is nominator name and submission date
     */
    public function getLink($text=null){
        $nominator = new Nominator;
        $nominator->id = $this->nominator_id;
        $nominator->load();

        $name = $nominator->getFullName();

        $view = new NominationView;
        $view->nominationId = $this->id;

        if(is_null($text)){
            $link = $view->getLink($name.' - '.strftime("%B %d, %Y", $nominator->getSubmissionDate()));

        } else {
            $link = $view->getLink($text);
        }

        return $link;
    }

    public function deleteForReal()
    {
        PHPWS_Core::initModClass('plm', 'Reference.php');
        PHPWS_Core::initModClass('plm', 'Nominator.php');
        PHPWS_Core::initModClass('plm', 'Nominee.php');
        PHPWS_Core::initModClass('plm', 'EmailMessage.php');
        PHPWS_Core::initModClass('plm', 'PLM_Doc.php');

        // Delete the nominee if needed (Read comments at top of file)
        $nominee = $this->getNominee();

        // Get nomination count INCLUDING THIS ONE
        if($nominee->getNominationCount() < 2){
            // This was the only nomination; it's okay to delete nominee
            EmailMessage::deleteMessages($nominee);
            $nominee->delete();
        }

        // Delete references, their uploaded documents, and logged emails.
        $references = $this->getReferences();
        foreach($references as $reference){
            PLM_Doc::delete($reference->unique_id);
            EmailMessage::deleteMessages($reference);
            $reference->delete();
        }

        // Delete nominator, his supporting statement, and logged emails.
        $nominator = $this->getNominator(); 
        PLM_Doc::delete($nominator->unique_id);
        EmailMessage::deleteMessages($nominator);
        $nominator->delete();

        // Finally, delete the nomination;
        $this->delete();
    }

    /**
     * Check if all references have submitted a 
     * letter of recommendation.
     * If they have then set nomination to completed
     */
    public function checkCompletion()
    {
        $refs = $this->getReferences();
        $terminado = True;
        foreach($refs as $ref){
            if(is_null($ref->doc_id)){
                $this->setCompleted(False);
                $this->save();
                return False;
            }
        }
        $this->setCompleted(True);
        $this->save();
        return True;
    }


    /********
     * Util *
     ********/
    // Row tags for DBPager
    public function rowTags()
    {
        $nominee = $this->getNominee();
        $nominator = $this->getNominator();
        $period = $this->getPeriodYear();
        
        $tpl= array('NOMINEE_LINK' => $nominee->getLink(),
                    'NOMINATOR_LINK' => $nominator->getLink(),
                    'PERIOD' => $period);
                    
        
        return $tpl;
                                  
    }

    /******************************
     * Factory Methods for Actors *
     ******************************/
    public function getNomineeEmail()
    {
        $nominee = $this->getNominee();
        return $nominee->getEmail();
    }

    public function getNominatorEmail()
    {
        $nominator = $this->getNominator();
        return $nominator->getEmail();
    }

    public function getNominator()
    {
        PHPWS_Core::initModClass('plm', 'Nominator.php');
        $nominator = new Nominator();
        $nominator->id = $this->nominator_id;
        $nominator->load();

        return $nominator;
    }

    public function getNominee()
    {
        PHPWS_Core::initModClass('plm', 'Nominee.php');
        $nominee = new Nominee();
        $nominee->id = $this->nominee_id;
        $nominee->load();

        return $nominee;
    }

    public function getNomineeName()
    {
        $omnominee = $this->getNominee();
        return $omnominee->getFullName();
    }

    public function getReferences()
    {
        PHPWS_Core::initModClass('plm', 'Reference.php');
        
        $ref = array();

        for($i = 1; $i <= REFERENCE_COUNT; $i++){
            $func = 'getReferenceId'.$i;
            $ref = new Reference($this->$func());
            $refs[] = $ref;
        }
        return $refs;
    }

    public function getReference1()
    {
        PHPWS_Core::initModClass('plm', 'Reference.php');
        return new Reference($this->reference_id_1);
    }

    public function getReference2()
    {
        PHPWS_Core::initModClass('plm', 'Reference.php');
        return new Reference($this->reference_id_2);
    }

    public function getReference3()
    {
        PHPWS_Core::initModClass('plm', 'Reference.php');
        return new Reference($this->reference_id_3);
    }


    /*******************
     * Factory Methods *
     *******************/
    /**
     * Get a "Nomination" by references/nominator unique_id
     * @return array - Return an array representation of a Nomination
     */
    public static function getByNominatorUnique_Id($unique_id){
        $db = new PHPWS_DB(NOMINATION_TABLE);

        $db->addJoin('left', 'plm_nomination', 'plm_nominator', 'nominator_id', 'id');
        $db->addWhere('plm_nominator.unique_id', $unique_id);

        $results = $db->select('row');

        if(PHPWS_Error::logIfError($results) || sizeof($results) == 0){
            throw new DatabaseException('No results');
        }

        $db = new PHPWS_DB('plm_nominee');
        $db->addWhere('id', $results);
        $nominee = $db->select('row');
        
        if(PHPWS_Error::logIfError($results) || sizeof($results) == 0){
            throw new DatabaseException('Nomination with no nominee?');
        }
        
        foreach($nominee as $key=>$nominee_field){
            $results['nominee_'.$key] = $nominee_field;
        }

        $db = new PHPWS_DB('plm_reference');
        $db->addWhere('id', $results['reference_id_1'], NULL, 'or');
        $db->addWhere('id', $results['reference_id_2'], NULL, 'or');
        $db->addWhere('id', $results['reference_id_3'], NULL, 'or');
        $db->setIndexBy('id');

        $references = $db->select();

        if(PHPWS_Error::logIfError($references)){
            throw new DatabaseException('Insufficient References on file!');
        }

        for($i=0; $i < 3; $i++){ //magic number is the number of references, change iff that changes
            $key = 'reference_id_'.($i+1);
            if(isset($results[$key]) && !is_null($results[$key])){
                foreach($references[$results[$key]] as $field_name=>$field_value){
                    if($field_name != 'id')
                        $results['reference_'.$field_name.'_'.($i+1)] = $field_value;;
                }
            }
        }

        $db = new PHPWS_DB('plm_nominator');
        $db->addWhere('id', $results['nominator_id']);
        
        $nominator = $db->select('row');
        
        if(PHPWS_Error::logIfError($nominator) || sizeof($nominator) == 0){
            throw new DatabaseException('Nomination without nominator?');
        }

        foreach($nominator as $field_name=>$field_value){
            $results['nominator_'.$field_name] = $field_value;
        }

        return $results;
    }

    /**
     * Get Nomination by reference unique_id
     * There should only be one Nomination returned from DB
     *
     * @return Nomination - 
     */
    public static function getByReferenceUnique_Id($unique_id)
    {
        $db = new PHPWS_DB(NOMINATION_TABLE);
        $db->addTable('plm_reference');
        $db->addWhere('reference_id_1', 'plm_reference.id', NULL, 'or', 'ref');
        $db->addWhere('reference_id_2', 'plm_reference.id', NULL, 'or', 'ref');
        $db->addWhere('reference_id_3', 'plm_reference.id', NULL, 'or', 'ref');
        $db->addWhere('plm_reference.unique_id', $unique_id);
        $result = $db->getObjects('Nomination');

        if(PHPWS_Error::logIfError($result) || sizeof($result) > 1 || sizeof($result) == 0){
            throw new DatabaseException('Invalid reference unique_id');
        }

        return $result[0];
    }
    

    /**
     * Return Reference/Nominator that matches the given unique_id.
     * There should only be one Refernce or Nominator for a given unique_id.
     *
     * @param unique_id - unqiue_id of reference or nominator but not both!
     */
    public static function getMember($unique_id)
    {
        $db = new PHPWS_DB('plm_reference');
        $db->addWhere('unique_id', $unique_id);
        $result = $db->getObjects('Reference');

        if(PHPWS_Error::logIfError($result) || sizeof($result) > 1){
            throw new DatabaseException('Invalid unique_id');
        }

        if(sizeof($result) != 0){
            return $result[0];
        } // else check to see if it's the nominator
        $db = new PHPWS_DB('plm_nominator');
        $db->addWhere('unique_id', $unique_id);
        $result = $db->getObjects('Nominator');

        if(PHPWS_Error::logIfError($result) || sizeof($result) > 1 || sizeof($result) == 0){
            throw new DatabaseException('Invalid unique_id');
        }

        return $result[0];
    }

    /**
     * Get the non-winning nominations for current nomination period
     *
     * @return Nomination - Array of non-winning nominations
     */
    public static function getNonWinningNominations()
    {
        $currPeriod = PHPWS_Settings::get('plm', 'current_period');
        return Nomination::getNonWinningNominationsByPeriod($currPeriod);
    }

    /**
     * Get the non-winning nominations for a given nomination period
     *
     * @return Nomination - Array of non-winning nominations
     */
    public static function getNonWinningNominationsByPeriod($period)
    {
        $db = new PHPWS_DB(NOMINATION_TABLE);
        
        $db->addWhere('period', $period);
        $db->addWhere('winner', NULL);

        $result = $db->select();

        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException($result->toString());
        }
        // Plug info into objects
        $noms = array();
        foreach($result as $nom){
            $nomObj = new Nomination();
            PHPWS_Core::plugObject($nomObj, $nom);
            $noms[] = $nomObj;
        }
        return $noms;
    }

    /**
     * Get the winning nominations for current nomination period
     *
     * @return Nomination - Array of winning nominations
     */
    public static function getWinningNominations()
    {
        $currPeriod = PHPWS_Settings::get('plm', 'current_period');
        return Nomination::getWinningNominationsByPeriod($currPeriod);
    }

    /**
     * Get the winning nominations for a given nomination period
     *
     * @return Nomination - Array of winning nominations
     */
    public static function getWinningNominationsByPeriod($period)
    {
        $db = new PHPWS_DB(NOMINATION_TABLE);
        
        $db->addWhere('period', $period);
        $db->addWhere('winner', !NULL);
        $result = $db->select();
        
        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
        // Plug info into objects
        $noms = array();
        foreach($result as $nom){
            $nomObj = new Nomination();
            PHPWS_Core::plugObject($nomObj, $nom);
            $noms[] = $nomObj;
        }
        return $noms;
    }

    /**
     * Get nomination by the nominator id
     */
    public static function getByNominatorId($id)
    {
        $db = new PHPWS_DB(NOMINATION_TABLE);
        
        $db->addWhere('nominator_id', $id);
        $result = $db->getObjects('Nomination');
        
        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
        else if(sizeof($result) != 1){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException('DatabaseException: Should only be one nomination per nominator');
        }
        
        return $result[0];
    }
    /**
     * Get nomination by the reference id
     */
    public static function getByReferenceId($id)
    {
        $db = new PHPWS_DB(NOMINATION_TABLE);
        
        $db->addWhere('reference_id_1', $id, NULL, 'OR');
        $db->addWhere('reference_id_2', $id, NULL, 'OR');
        $db->addWhere('reference_id_3', $id, NULL, 'OR');

        $result = $db->getObjects('Nomination');
        
        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        } 
        else if(sizeof($result) != 1){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException('DatabaseException: Should be one nomination per reference');
        }
        
        // Should only be a single nomination
        return $result[0];
    }

    /**
     * Get Nomination by nominee ID
     * There may be more than one Nomination returned. 
     * Nominations and Nominees are N-to-1
     *
     * @param id - row id in nominee table
     * @return Nomination - 
     */
    public static function getByNomineeId($id)
    {
        PHPWS_Core::initModClass('plm', 'Period.php');

        $db = new PHPWS_DB(NOMINATION_TABLE);
        $db->addWhere('nominee_id', $id);
        $db->addWhere('period', Period::getCurrentPeriodYear());
        
        $result = $db->getObjects('Nomination');
        
        if(PHPWS_Error::logIfError($results)){
            throw new DatabaseException($results->getMessage());
        } else {
            return $results;
        }
    }
}
?>
