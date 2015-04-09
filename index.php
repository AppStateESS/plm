<?php

if (!defined('PHPWS_SOURCE_DIR')) {
    include '../../config/core/404.html';
    exit();
}



PHPWS_Core::requireInc('plm', 'defines.php');

PHPWS_Core::initModClass('plm', 'Context.php');

PHPWS_Core::initModClass('plm', 'PLMFactory.php');

PHPWS_Core::initModClass('notification', 'NQ.php');

PHPWS_Core::initModClass('plm', 'view/PLMNotificationView.php');


$controller = PLMFactory::getPLM();
$controller->process();
?>