<?php

  /**
   * permission.php
   * 
   * @author Robert Bost <bostrt at appstate dot edu>
   */

$use_permissions = TRUE;
$item_permission = TRUE;

// Administration
$permissions['purge_nominations'] = _('Purge non-winning nominations.');
$permissions['rollover_period'] = _('Rollover to new nomination period.');
$permissions['send_notification_emails'] = _('Send notification emails.');
$permissions['maintain_recipients'] = _('Perform maintenance on recipients of award.');

?>
