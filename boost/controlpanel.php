<?php
  /**
   * controlpanel.php
   *
   * TODO: Create an icon for PLM. Don't just use teh stupid tango one.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */
$link[] = array('label'       => _('Nomination Module'),
                'restricted'  => TRUE,
                'url'         => 'index.php?module=plm',
                'description' => _('Manage nominations.'),
                'image'       => 'tango/mimetypes/application-certificate.png',
                'tab'         => 'admin'
                );
?>
