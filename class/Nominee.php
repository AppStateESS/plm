<?php

PHPWS_Core::initModClass('plm', 'NominationActor.php');
PHPWS_Core::initModClass('plm', 'view/NomineeView.php');
PHPWS_Core::initModClass('plm', 'Period.php');

define('NOMINEE_TABLE', 'plm_nominee');

class Nominee extends NominationActor
{
    public $position;
    public $major;
    public $years;

    public function getDb()
    {
        return new PHPWS_DB(NOMINEE_TABLE);
    }


    /**
     * Add a new nominee to plm_nominee table if it does not exist
     *
     * @param *_name - nominee's name
     * @param email - Email address may come in with just username or
     *                with username@domain.  We will handle both.
     * @param position - nominee's position on campus
     * @param department_major - nominee's department or major on campus
     * @param years - Number of years nominee has been at ASU
     *
     * TODO: Check about what exact data and format is needed.
     *                
     */
    public static function addNominee($first_name, $middle_name="", $last_name, $email, $position,
                                      $department_major, $years)
    {
        // Explode on '@' and get username
        // If no domain is given assume that user
        // is giving an ASU email address.
        // If they give a domain, check it.

        $sploded = explode('@', $email);
        if(!isset($sploded[1])){
            $email .= '@appstate.edu';
        }
        if(!self::isValidEmail($email)){
            throw new InvalidArgumentException('Invalid nominee email. Must end with '.NOMINATION_EMAIL_DOMAIN);
        }
        if(self::existsByEmail($email)){
            // Nominee exists
            return false;
        } else {
            $nominee = new Nominee();
            
            $nominee->first_name = $first_name;
            $nominee->middle_name = $middle_name;
            $nominee->last_name = $last_name;
            $nominee->email = $email;
            $nominee->position = $position;
            $nominee->major = $department_major;
            $nominee->years = $years;
            
            $result = $nominee->save();

            return $result;
        }
        return null;
    }

    

    /**
     * Getters...
     */
    public function getMajor(){
        return $this->major;
    }

    public function getYears(){
        return $this->years;
    }

    /**
     * Setters...
     */
    public function setEmail($email){
        $this->email = $email;
    }
    public function setMajor($major){
        $this->major = $major;
    }
    public function setYears($years){
        $this->years = $years;
    }

    /**
     * Utilities
     */
    public function getLink(){
        $name = $this->getFullName();

        $view = new NomineeView;
        $view->nomineeId = $this->id;

        $link = $view->getLink($name);
        return $link;
    }

    public function rowTags(){
        $tpl             = array();
        $tpl['LINK']     = $this->getLink();
        $tpl['EMAIL']    = $this->getEmailLink();
        return $tpl;
    }

    /**
     * Get the count of nomination for this nominee
     */ 
    public function getNominationCount()
    {
        PHPWS_Core::initModClass('plm', 'Nomination.php');

        $db = new PHPWS_DB('plm_nomination');
        $db->addWhere('nominee_id', $this->id);
        return $db->count();
    }

    /**
     * Check if nominee exists by email for the current period.
     */
    public static function existsByEmail($email)
    {
        $db = new PHPWS_DB('plm_nominee');

        $db->addJoin('left', 'plm_nominee', 'plm_nomination', 'id', 'nominee_id');
        
        $db->addWhere('email', $email, '=', 'AND');
        $db->addWhere('plm_nomination.period', Period::getCurrentPeriodYear());

        $count = $db->count();

        # check for db error or zero results
        if(PHPWS_Error::logIfError($count) || $count == 0){
            return false;
        }

        # else they are there
        return true;
    }

    /**
     * Factory Methods
     */
    public static function getNomineeByEmail($email)
    {
        $db = new PHPWS_DB('plm_nominee');
        $db->addWhere('email', $email);
        $result = $db->select();

        $nominee = new Nominee($result[0]['id']);
        
        return $nominee;
    }
}
?>
