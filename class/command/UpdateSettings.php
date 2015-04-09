<?php

PHPWS_Core::initModClass('plm', 'Command.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'view/PLMNotificationView.php');
PHPWS_Core::initModClass('plm', 'exception/InvalidSettingsException.php');

class UpdateSettings extends Command {

    public function getRequestVars(){
        return array('action' => 'UpdateSettings', 'after' => 'AdminSettings');
    }

    public function execute(Context $context)
    {
        if(!UserStatus::isAdmin()){
            throw new PermissionException('You are not allowed to see this!');
        }

        try{
            // Store settings in a map
            $settingsMap = array();

            /**
             * Update file storage path
             */
            if(!empty($context['file_dir'])){
                // Check for trailing '/'
                $file_dir = $context['file_dir'];
                if($file_dir[strlen($file_dir)-1] != '/'){
                    // Append '/' if it does not exist
                    $file_dir .= "/";
                }
                $settingsMap['file_dir'] = $file_dir;
            }

            /*
             * Update award title
             */
            if(!empty($context['award_title'])){
                $settingsMap['award_title'] = $context['award_title'];
            }

            /**
             * Update allowed file types for upload
             */
            if(!empty($context['allowed_file_types'])){
                $settingsMap['allowed_file_types'] = $context['allowed_file_types'];
            } else {
                throw new InvalidSettingsException('At least one file type must be set.');
            }
            
            $settingsMap['email_from_address'] = $context['email_from_address'];

            /**
             * Actually perform updates now
             * PHPWS_Settings::save() returns null on success
             */
            foreach($settingsMap as $key=>$value){
                PHPWS_Settings::set('plm', $key, $value);
                $result = PHPWS_Settings::save('plm');

                if(!is_null($result)){
                    throw new Exception('Something bad happened when '.$key.' was being saved.');
                }
            }
        } catch (Exception $e){
            NQ::simple('plm', PLM_ERROR, $e->getMessage());
            return;
        }
        NQ::simple('plm', PLM_SUCCESS, 'Settings saved.');
    }

}
?>
