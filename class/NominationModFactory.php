<?php
  /**
   * NominationFactory.php
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */
PHPWS_Core::initModClass('plm', 'UserStatus.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'AdminNomination.php');

class NominationModFactory
{
    private static $nomination;

    public static function getNomination()
    {
        if(isset(NominationModFactory::$nomination)){
            return NominationFactory::$nomination;
        }
        else if(UserStatus::isAdmin()){
            PHPWS_Core::initModClass('plm', 'AdminNomination.php');
            NominationModFactory::$nomination = new AdminNomination();
        }
        else if(UserStatus::isCommitteeMember()){
            PHPWS_Core::initModClass('plm', 'CommitteeNomination.php');
            NominationModFactory::$nomination = new CommitteeNomination();
        }
        else {
            PHPWS_Core::initModClass('plm', 'GuestNomination.php');
            NominationModFactory::$nomination = new GuestNomination();
        }

        return NominationModFactory::$nomination;
    }
}
?>