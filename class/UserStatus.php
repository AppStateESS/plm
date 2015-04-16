<?php

/**
 * PLM User Status
 * Used to quickly determine proper permissioning and displaying the login
 * stuff at the top.  Also used for admins that are masquerading as other
 * user types.
 * 
 * This is a stripped down version of SDR's UserStatus.php
 *
 * @author Jeff Tickle <jtickle at tux dot appstate dot edu>
 */

define('PLM_USERSTATUS_GUEST', 'guest');
define('PLM_USERSTATUS_USER',  'user');
define('PLM_USERSTATUS_COMMITTEE_MEMBER', 'committee_member');
define('PLM_USERSTATUS_ADMIN', 'admin');

class UserStatus
{

	private final function __construct() { }

	public static function isAdmin()
	{
		return Current_User::isLogged() &&
		Current_User::isUnrestricted('plm');
	}

    public static function isCommitteeMember()
    {
        if(!Current_User::isLogged()){
        // If users isn't even logged in then they can't be committee member
            return False;
        }

        $groups = Current_User::getGroups();

        if(is_null($groups)){
            // If not a member of any group then NO!
            return False;
        } else {
            // Check member's groups for plm_committee
            foreach($groups as $group_id){
                PHPWS_Core::initModClass('users', 'Group.php');
                $group = new PHPWS_Group($group_id);
                if($group->getName() == 'plm_committee'){
                    return True;
                }
            }
        }
        return False;
    }

	public static function isUser()
	{
		return (Current_User::isLogged() &&
		!Current_User::isUnrestricted('plm'));
	}

	public static function isGuest()
	{
		return !Current_User::isLogged();
	}

	public static function getDisplay()
	{
		$vars = array();
		$user = Current_User::getDisplayName();

		if(UserStatus::isGuest()) {
			$vars['LOGGED_IN_AS'] = dgettext('plm', 'Viewing as Guest');
            $vars['LOGIN_LINK']   = UserStatus::getLoginLink();
		} else {
			$vars['LOGGED_IN_AS'] = sprintf(dgettext('plm', 'Welcome, %s!'), $user);
			$vars['LOGOUT_LINK']  = UserStatus::getLogoutLink();
		}

		return PHPWS_Template::process($vars, 'plm', 'UserStatus.tpl');
	}

    public static function getLoginLink()
    {
        // Support for co-sign
        $loginLink = "/login";
        return '<a href="'.$loginLink.'"><img class="login-icon" src="'.PHPWS_SOURCE_HTTP.'mod/plm/img/tango/actions/edit-redo.png"/>Member Login</a>';
    }

	public static function getLogoutLink()
	{
        // TODO: Find out why the code below doesn't work in production
		//$auth = Current_User::getAuthorization();
		return '<a href="'.PHPWS_SOURCE_HTTP.'index.php?module=users&action=user&command=logout">Logout</a>';
	}
}

?>
