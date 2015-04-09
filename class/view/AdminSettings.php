<?php
  /**
   * AdminSettings
   *
   * View for administrative settings
   *
   * @author Daniel West <dwest at tux dot appstate dot edu>
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'CommandFactory.php');
PHPWS_Core::initModClass('plm', 'ViewFactory.php');
PHPWS_Core::initModClass('plm', 'PLM_Doc.php');

class AdminSettings extends PlemmView {

    public function getRequestVars()
    {
        return array('view' => 'AdminSettings');
    }

    public function display(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to see this!');
        }
        $tpl = array();

        // Create factories
        $cmdFactory = new CommandFactory();
        $vFactory = new ViewFactory();

        // Initialize form submit command
        $updateCmd = $cmdFactory->get('UpdateSettings');
        $form = new PHPWS_Form('admin_settings');
        $updateCmd->initForm($form);

        // File storage path
        $form->addText('file_dir', PHPWS_Settings::get('plm', 'file_dir'));
        $form->setLabel('file_dir', 'File Directory:');
        
        // Award title 
        $form->addText('award_title', PHPWS_Settings::get('plm', 'award_title'));
        $form->setLabel('award_title', 'Award Title:');

        // Allowed file types
        $types = PLM_Doc::getFileNames();
        $enabled = PHPWS_Settings::get('plm', 'allowed_file_types');
        $enabled = unserialize($enabled);
        $form->addCheckAssoc('allowed_file_types', $types);
        $form->setMatch('allowed_file_types', $enabled);
        $form->useRowRepeat();

        // Email from address
        $form->addText('email_from_address', PHPWS_Settings::get('plm', 'email_from_address'));
        $form->setLabel('email_from_address', 'Email From Address');

        $form->addSubmit('Update');

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();

        Layout::addPageTitle('Admin Settings');

        return PHPWS_Template::process($tpl, 'plm', 'admin/settings.tpl');
    }
}
?>
