<?php

function plm_install(&$content)
{
    $today = getdate();
    $thisYear = $today['year'];
    
    // Create period
    PHPWS_Core::initModClass('plm', 'Period.php');
    $period = new Period();
    
    $period->year = $thisYear;

    $period->setDefaultStartDate();
    $period->setDefaultEndDate();
    $period->save();

    // Create pulse for this period
    PHPWS_Core::initModClass('plm', 'PLMRolloverEmailPulse.php');
    $pulse = new PLMRolloverEmailPulse();
    $timeDiff = $period->getEndDate() - mktime();
    $pulse->newFromNow($timeDiff);

    // Create Committee group
    PHPWS_Core::initModClass('users', 'Group.php');
    $group = new PHPWS_Group();
    $group->setName('plm_committee');
    $group->setActive(True);
    $group->save();
    
    return true;
}

?>