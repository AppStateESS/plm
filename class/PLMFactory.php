<?php
  /**
   * PLMFactory.php
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */
PHPWS_Core::initModClass('plm', 'UserStatus.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'AdminPLM.php');

class PLMFactory
{
    private static $plm;
    
    public static function getPLM()
    {
        if(isset(PLMFactory::$plm)){
            return PLMFactory::$plm;
        }
        else if(UserStatus::isAdmin()){
            PHPWS_Core::initModClass('plm', 'AdminPLM.php');
            PLMFactory::$plm = new AdminPLM();
        } 
        else if(UserStatus::isCommitteeMember()){
            PHPWS_Core::initModClass('plm', 'CommitteePLM.php');
            PLMFactory::$plm = new CommitteePLM();
        } 
        else {
            PHPWS_Core::initModClass('plm', 'GuestPLM.php');
            PLMFactory::$plm = new GuestPLM();
        }

        return PLMFactory::$plm;
    }
}
?>