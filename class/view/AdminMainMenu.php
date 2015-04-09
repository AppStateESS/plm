<?php

PHPWS_Core::initModClass('plm', 'View.php');

class AdminMainMenu extends PlemmView
{
    public function getRequestVars()
    {
        return array('view' => 'AdminMainMenu');
    }

    public function display(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to see that!');
        }

        PHPWS_Core::initModClass('plm', 'Period.php');
        $vFactory = new ViewFactory();
        
        $topMenu = $vFactory->get('PLMMainMenu');

        /** Search menu **/
        // (menu_title, tag)
        $topMenu->addMenu('Search', 'search');
        // (view_class, item_title, tag, parent_tag)
        $topMenu->addMenuItemByname('NomineeSearch', 'Nominees',
                                    'nominee_search', 'search');
        $topMenu->addMenuItemByname('NominatorSearch', 'Nominators',
                                    'nominator_search', 'search');

        /** Period **/
        $topMenu->addMenu('Period', 'period');
        $topMenu->addMenuItemByName('WinnersView', 'Winners',
                                    'award_winners', 'period');
        $topMenu->addMenuItemByName('PeriodMaintenance', 'Period Settings',
                                    'period_maintenance', 'period');
        $topMenu->addMenuItemByName('RolloverView', 'Rollover',
                                    'rollover_period', 'period');

        $topMenu->insertNewColumn();

        /** Forms **/
        $topMenu->addMenu('User Forms', 'forms');
        $topMenu->addMenuItemByName('NominationForm', 'Nomination Form',
                                    'nomination_form', 'forms');

        /** Administration **/
        $topMenu->addMenu('Administration', 'administration');
        $topMenu->addMenuItemByName('AdminSettings', 'Settings',
                                    'admin_settings', 'administration');
        $topMenu->addMenuItemByName('SendEmail', 'Send Email',
                                    'send_email', 'administration');
        $topMenu->addMenuItemByName('EmailLogView', 'Email Log',
                                    'view_log', 'administration');
        $topMenu->addMenuItemByName('CancelQueuePager', 'Removal Requests',
                                    'nom_removal', 'administration');

        Layout::addPageTitle('Admin Main Menu');

        return $topMenu->show();
    }
}

?>
