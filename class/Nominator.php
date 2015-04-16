<?php

  /**
   * Nominator 
   * 
   * Represents a nominator for a nomination.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'NominationActor.php');
PHPWS_Core::initModClass('plm', 'ViewFactory.php');

define('NOMINATOR_TABLE', 'plm_nominator');

class Nominator extends NominationActor
{
    public $phone;
    public $address;
    public $unique_id;
    public $doc_id;
    
    // Inherited from PLM_Model
    public function getDb()
    {
        return new PHPWS_DB(NOMINATOR_TABLE);
    }

    /**
     * Add a new nominator to plm_nominator table
     *
     * @param *_name - nominator's name
     * @param email - Email address must be from *.appstate.edu
     * @param phone - This is a free-style string yet again.
     * @param address - Address is free-style until we know if only ASUBox 
     *                  is wanted.
     *
     */
    public static function addNominator($first_name, $middle_name, $last_name,
                                        $email, $phone, $address, $relationship)
    {
        // Explode on '@' and get username
        // If no domain is given assume that user
        // is giving an ASU email address.
        $sploded = explode('@', $email);
        if(!isset($sploded[1])){
            $email .= '@appstate.edu';
        }
        // Validate the email address
        // Check it ends with {something}.appstate.edu
        if(!self::isValidEmail($email)){
            throw new InvalidArgumentException('Invalid nominator email. Must end with '.NOMINATION_EMAIL_DOMAIN);
        }

        if(empty($relationship)){
            // No relationship was given.
            $relationship = "N/A";
        }
        if(empty($department)){
            $department = "N/A";
        }

        // Create a new nominator
        $nominator = new Nominator();
            
        $nominator->first_name = $first_name;
        $nominator->middle_name = $middle_name;
        $nominator->last_name = $last_name;
        $nominator->email = $email;
        $nominator->phone = $phone;
        $nominator->address = $address;
        $nominator->relationship = $relationship;
        $nominator->unique_id = self::generateUniqueId($nominator->getEmail());

        $result = $nominator->save();
            
        return $result;
    }

    /**
     * Getters...
     */
    public function getPhone(){
        return $this->phone;
    }
    
    public function getAddress(){
        return $this->address;
    }
    
    public function getUniqueId(){
        return $this->unique_id;
    }

    /**
     * Setters...
     */
    public function setEmail($email){
        $this->email = $email;
    }
    
    public function setPhone($phone){
        $this->phone = $phone;
    }
    public function setAddress($address){
        $this->address = $address;
    }

    /*************
     * Utilities *
     *************/
    /**
     * Get link to view nominator. Use nominator's full
     * for text on link
     * @return - HTML link
     */
    public function getLink(){
        $name = $this->getFullName();

        $vf = new ViewFactory();
        $view = $vf->get('NominatorView');
        $view->nominatorId = $this->id;

        $link = $view->getLink($name);
        return $link;
    }

    /**
     * Get the link for a nominator to edit their nomination
     * @return - URL for editting nomination
     */
    public function getEditLink()
    {
        $unique_id = $this->getUniqueId();
        
        $host = $_SERVER['HTTP_HOST'];
        $extra = $_SERVER['PHP_SELF'].'?module=plm&view=NominationForm&unique_id='.$unique_id;
        
        $link = 'http://'.$host.$extra;
        
        return $link;
    }

    // Row tags for DBPager
    public function rowTags(){
        $tpl = array();

        $vf = new ViewFactory;
        $view = $vf->get('NominatorView');
        $view->nominatorId = $this->id;

        $tpl['LINK']     = $this->getLink();
        $tpl['EMAIL']    = $this->getEmailLink();
        $tpl['ADDED_ON'] = strftime("%B %d, %Y", $this->getSubmissionDate());
        $nomination = $this->getNomination();
        $nominee = $nomination->getNominee();
        $tpl['NOMINEE_LINK'] = $nominee->getLink();

        return $tpl;
    }

    /**
     * Get the date of submission for a nomination that 
     * the nominator submitted
     * @return - Unix time stamp 
     */
    public function getSubmissionDate()
    {
        $db = new PHPWS_DB('plm_nominator');
        $db->addTable('plm_nomination');
        $db->addWhere('id', $this->id);
        $db->addWhere('plm_nomination.nominator_id', 'plm_nominator.id');
        $db->addColumn('plm_nomination.added_on');
        $result = $db->select('row');

        if(PHPWS_Error::logIfError($result) || sizeof($result) == 0){
            throw new DatabaseException('Database is broken, please try again');
        }

        return $result['added_on'];
    }

    /**
     * Get the matching nomination for this nominator
     * @return - Nomination
     */
    public function getNomination()
    {
        $db = new PHPWS_DB('plm_nomination');
        $db->addWhere('nominator_id', $this->id);
        $result = $db->getObjects('Nomination');

        // Check for DB Error
        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException('Database is broken: '.$result->toString());
        }
        if(sizeof($result) > 1){
            throw new DatabaseException('Database Error: same nominator for multiple nominations');
        }
        if(sizeof($result) < 1){
            throw new DatabaseException('Database Error: no nominations for nominator');
        }

        return $result[0];
    }

    /*******************
     * Factory Methods *
     *******************/
    /**
     * Get nominator by a unique_id
     * @return - Nominator
     */
    public static function getByUniqueId($unique_id)
    {
        $db = self::getDb();
        $db->addWhere('unique_id', $unique_id);
        $result = $db->getObjects('Nominator');

        if(PHPWS_Error::logIfError($result) || sizeof($result) > 1){
            throw new DatabaseException('Database Error: Multiple nominators with same unique_id');
        }
        if(sizeof($result) != 0){
            return $result[0];
        }
        return Null;
    }

}

?>