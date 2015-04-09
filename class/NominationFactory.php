<?php

PHPWS_Core::initModClass('plm', 'Nomination.php');

/**
 * NominationFactory - Static methods for loading a nomination object
 *
 * @author jbooker
 * @package nomination
 */
class NominationFactory {

    /**
     * Returns a Nomination object loaded from the DB using the nomination's ID,
     * or returns null if that id does not exist in the database.
     *
     * @param $id Nomination's database id
     * @return Nomination The corresponding Nomination object, or null if the id doesn't exist
     */
    public static function getNominationbyId($id)
    {
        if(!isset($id)){
            throw new InvalidArgumentException('Missing id.');
        }

        $db = new PHPWS_DB('plm_nomination');
        $db->addWhere('id', $id);

        $result = $db->select('row');

        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }

        if(count($result) == 0){
            return null;
        }

        $nom = new DBNomination();
        $nom->setId($result['id']);
        $nom->setBannerId($result['banner_id']);
        $nom->setFirstName($result['first_name']);
        $nom->setMiddleName($result['middle_name']);
        $nom->setLastName($result['last_name']);
        $nom->setEmail($result['email']);
        $nom->setAsuBox($result['asubox']);
        $nom->setPosition($result['position']);
        $nom->setDeptMajor($result['department_major']);
        $nom->setYearsAtASU($result['years_at_asu']);
        $nom->setPhone($result['phone']);
        $nom->setGpa($result['gpa']);
        $nom->setClass($result['class']);
        $nom->setResponsibility($result['responsibility']);
        $nom->setCategory($result['category']);
        $nom->setNominatorFirstName($result['nominator_first_name']);
        $nom->setNominatorMiddleName($result['nominator_middle_name']);
        $nom->setNominatorLastName($result['nominator_last_name']);
        $nom->setNominatorEmail($result['nominator_email']);
        $nom->setNominatorPhone($result['nominator_phone']);
        $nom->setNominatorAddress($result['nominator_address']);
        //$nom->setNominatorUniqueId($result['nominator_unique_id']);
        $nom->setNominatorRelation($result['nominator_relation']);
        $nom->setComplete($result['complete']);
        $nom->setPeriod($result['period']);
        $nom->setAddedOn($result['added_on']);
        $nom->setUpdatedOn($result['updated_on']);
        $nom->setWinner($result['winner']);

        return $nom;
    }


    public static function save(Nomination $nom)
    {
        $db = new PHPWS_DB('plm_nomination');

        $db->addValue('banner_id', $nom->getBannerId());
        $db->addValue('first_name', $nom->getFirstName());
        $db->addValue('middle_name', $nom->getMiddleName());
        $db->addValue('last_name', $nom->getLastName());
        $db->addValue('email', $nom->getEmail());
        $db->addValue('asubox', $nom->getAsubox());
        $db->addValue('position', $nom->getPosition());
        $db->addValue('department_major', $nom->getDeptMajor());
        $db->addValue('years_at_asu', $nom->getYearsAtASU());
        $db->addValue('phone', $nom->getPhone());
        $db->addValue('gpa', $nom->getGPA());
        $db->addValue('class', $nom->getClass());
        $db->addValue('responsibility', $nom->getResponsibility());

        $db->addvalue('nominator_first_name', $nom->getNominatorFirstName());
        $db->addValue('nominator_middle_name', $nom->getNominatorMiddleName());
        $db->addValue('nominator_last_name', $nom->getNominatorLastName());
        $db->addValue('nominator_address', $nom->getNominatorAddress());
        $db->addValue('nominator_phone', $nom->getNominatorPhone());
        $db->addValue('nominator_email', $nom->getNominatorEmail());
        $db->addValue('nominator_relation', $nom->getNominatorRelation());

        $db->addValue('category', $nom->getCategory());
        $db->addValue('period', $nom->getPeriod());
        $db->addValue('complete', $nom->getComplete());
        $db->addValue('winner', $nom->getWinner());
        $db->addValue('added_on', $nom->getAddedOn());
        $db->addValue('updated_on', time());

        $id = $nom->getId();
        if(!isset($id) || is_null($id)) {
            $result = $db->insert();
            if(!PHPWS_Error::isError($result)){
                // If everything worked, insert() will return the new database id,
                // So, we need to set that on the object for later
                $nom->setId($result);
            }
        }else{
            $db->addWhere('id', $nom->getId());
            $result = $db->update();
        }

        if(PHPWS_Error::logIfError($result)){
            throw new Exception('DatabaseException: Failed to save nomnation.' . $result->toString());
        }
    }

    /************************
     * LOOKOUT: OLD STUFF BELOW
     */



    /**
     * Get a "Nomination" by references/nominator unique_id
     * @return array - Return an array representation of a Nomination
     */
    public static function getByNominatorUniqueId($unique_id){
        $db = Nomination::getDb();

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
    public static function getByReferenceUniqueId($unique_id)
    {
        $db = self::getDb();
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
        $db = Nomination::getDb();

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
        $db = Nomination::getDb();

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

}

?>
