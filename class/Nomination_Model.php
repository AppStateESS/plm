<?php

  /**
   * Nomination_Model
   *
   * Basic model class. Mostly just provides
   * access to database.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

abstract class Nomination_Model
{
    public $id;

    abstract function getDb();

    public function __construct($id = 0)
    {
        if(!is_null($id) && is_numeric($id)){
            $this->id = $id;
            
            $result = $this->load();

            if(!$result){
                $this->id = 0;
            }
        } else {
            $this->id = 0;
        }
    }
    
    public function getId(){
        return $this->id;
    }

    public function load()
    {
        if(is_null($this->id) || !is_numeric($this->id))
            return false;

        $db = $this->getDb();
        $db->addWhere('id', $this->id);
        $result = $db->loadObject($this);
        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('nomination', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
        
        return $result;
    }

    public function save()
    {
        $db = $this->getDb();
        $result = $db->saveObject($this);

        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('nomination', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }

        // return new id
        return $result;
    }

    public function delete()
    {
        $db = $this->getDb();
        $db->addWhere('id', $this->id);
        $result = $db->delete();
        
        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException($result->toString());
        }
        return true;
    }
}
?>
