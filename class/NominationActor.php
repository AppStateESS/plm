<?php
  /**
   * NominationActor
   *
   * A higher level model class.  
   * Stores names, email and UniqueId utility 
   * functions for Nominator and Reference.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'PLM_Model.php');

abstract class NominationActor extends PLM_Model
{
    public $first_name;
    public $middle_name;
    public $last_name;
    public $email;
    public $relationship;

    // Combine first, middle, and last name
    public function getFullName()
    {
        $first = $this->getFirstName();
        $middle= (!empty($this->middle_name)) ? $this->getMiddleName() : '';
        $last  = $this->getLastName();
        
        return $first.' '.$middle.' '.$last;
    }

    // Get MailTo link for email
    public function getEmailLink(){
        $email = $this->getEmail();
        return "<a href='mailto:$email'>$email</a>";
    }

    /**
     * Username acts as salt.
     * Useranme is prepended to a unique id based on
     * current time in microseconds.
     *
     * @return - unique_id
     */
    public static function generateUniqueId($username)
    {
        $uniqueId = md5(uniqid($username));

        if(self::uniqueIdExists($uniqueId)){
            PHPWS_Core::initModClass('plm', 'exception/UniqueIdException.php');
            throw new UniqueIdException('Problem occured while generating unique ID.');
        }
        
        return $uniqueId;
    }

    /**
     * Check if a unique id exists in nominator or reference table.
     * 
     * @return boolean - if the combined SELECT count is greater than zero
     */
    private static function uniqueIdExists($id)
    {
        PHPWS_Core::initModClass('plm', 'Nominator.php');
        PHPWS_Core::initModClass('plm', 'Reference.php');

        $nom_db = Nominator::getDb();
        $ref_db = Reference::getDb();
        
        $nom_db->addWhere('unique_id', $id);
        $ref_db->addWhere('unique_id', $id);

        return ($nom_db->count() + $ref_db->count()) > 0;
    }

    public static function isValidEmail($email)
    {
        return preg_match("/".NOMINATION_EMAIL_DOMAIN."\z/i", $email);
    }
    
    /**
     * Getters and Setters
     */
    public function getFirstName(){
        return $this->first_name;
    }
    public function getMiddleName(){
        return $this->middle_name;
    }
    public function getLastName(){
        return $this->last_name;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getRelationship(){
        return $this->relationship;
    }
    public function setFirstName($firstName){
        $this->first_name = $firstName;
    }
    public function setMiddleName($middleName){
        $this->middle_name = $middleName;
    }
    public function setLastName($lastName){
        $this->last_name = $lastName;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function setRelationship($relation){
        $this->relationship = $relation;
    }
}

?>