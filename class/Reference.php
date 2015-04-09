<?php

  /**
   * Reference
   *
   * Represents a single reference for a nomination.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'NominationActor.php');
PHPWS_Core::initModClass('plm', 'PLM_Email.php');

define('REFERENCE_TABLE', 'plm_reference');
define('REFERENCE_COUNT', 3);

class Reference extends NominationActor
{
    public $phone;
    public $department;
    public $unique_id;
    public $doc_id;

    public function getDb()
    {
        return new PHPWS_DB(REFERENCE_TABLE);
    }
    /**
     *  Add new reference to DB.
     *  There is no need to check if this reference already exists;
     *  A reference will always be 1-to-1 with a nomination.
     *
     *  @param *_name     - Reference's name
     *  @param email      - Refernece's email address. Can be from any domain
     *  @param phone      - Refernece's phone number
     *  @param department - Reference's department
     *
     */
    public static function addReference($first_name, $middle_name, $last_name,
                                        $email, $phone, $department, $relationship)
    {
        $ref = new Reference();

        if(empty($relationship)){
            // No relationship was given.
            $relationship = "N/A";
        }
        if(empty($department)){
            $department = "N/A";
        }
            
        $ref->first_name   = $first_name;
        $ref->middle_name  = $middle_name;
        $ref->last_name    = $last_name;
        $ref->email        = $email;
        $ref->phone        = $phone;
        $ref->department   = $department;
        $ref->relationship = $relationship;
        $ref->unique_id    = self::generateUniqueId($ref->getEmail());

        $result = $ref->save();

        if(PHPWS_Error::logIfError($result)){
            throw DatabaseException($result->toString());
        }
            
        return $result;
    }

    /*************
     * Utilities *
     *************/
    /**
     * Get the link for a nominator to edit their nomination
     * @return - URL for submitting letter
     */
    public function getEditLink()
    {
        $unique_id = $this->getUniqueId();
        
        $host = $_SERVER['HTTP_HOST'];
        $extra = $_SERVER['PHP_SELF'].'?module=plm&view=ReferenceForm&unique_id='.$unique_id;
        
        $link = 'http://'.$host.$extra;
        
        return $link;

    }

    /**
     * Get link to view reference. Use refernece's full
     * for text on link
     * @return - HTML link
     */
    public function getLink()
    {
        $name = $this->getFullName();
        
        $vf = new ViewFactory();
        $view = $vf->get('ReferenceView');
        $view->id = $this->getId();
        $link = $view->getLink($name);
        
        return $link;
    }

    /**
     * Getters...
     */
    public function getPhone(){
        return $this->phone;
    }

    public function getDepartment(){
        return $this->department;
    }

    public function getUniqueId(){
        return $this->unique_id;
    }

    /**
     * Setters...
     */
    public function setPhone($phone){
        $this->phone = $phone;
    }
    
    public function setDepartment($department){
        $this->department = $department;
    }

    public function setUniqueId($id){
        $this->unique_id = $id;
    }

    /*******************
     * Factory methods *
     *******************/
    /**
     * Get reference by 
     *
     */
    public static function getByUniqueId($unique_id)
    {
        $ref = new Reference;
        $db = $ref->getDb();
        $db->addWhere('unique_id', $unique_id);
        $result = $db->getObjects('Reference');
        
        if(PHPWS_Error::logIfError($result) || sizeof($result) > 1){
            throw new DatabaseException('Database Error');
        }

        if(sizeof($result) != 0){
            return $result[0];
        }
        return Null;
    }
}
?>