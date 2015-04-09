<?php

/**
 * PLM_Doc
 *
 *   Normalizes filenames, and facilitates providing a consistent file
 * upload interface to the user.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */
//Files must be uploaded for a specific nomination
PHPWS_Core::initModClass('plm', 'Nomination.php');
PHPWS_Core::initModClass('plm', 'Nominator.php');
PHPWS_Core::initModClass('plm', 'Reference.php');

class PLM_Doc {
    public $nomination;
    public $allowedTypes;
    public $type; //tells us whose file info to lookup on existing docs

    private static $fileNames = array('txt'=>'Text Files',
                                      'doc'=>'Word 97/2000/XP (.doc)',
                                      'docx'=>'Word 2007 (.docx)',
                                      'odt'=>'Open Document Files',
                                      'pdf'=>'PDF Documents');

    private static $mimeTypes = array('txt'=>'text/plain',
                                      'odt'=>'application/vnd.oasis.opendocument.text',
                                      'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                      'doc'=>'application/msword',
                                      'pdf'=>'application/pdf');


    public function __construct(Nomination $omnom=NULL, $type='nominator')
    {
        $this->nomination = $omnom;
        $this->allowedTypes = PHPWS_Settings::get('plm', 'allowed_file_types');
    }

    /**
     * Get file from client
     */ 
    public function receiveFile($name, $type, $unique_id)
    {
        if(!isset($_FILES[$name]) || !is_uploaded_file($_FILES[$name]['tmp_name'])){
            PHPWS_Core::initModClass('plm', 'exception/FileNotFoundException.php');
            throw new FileNotFoundException('No Such file uploaded');
        }

        $nominee_email = $this->nomination->getNomineeEmail();
        $person_abbrev = $this->nomination->getMember($unique_id);
        $person_abbrev = $person_abbrev->getLastName().$person_abbrev->getEmail();
        $person_abbrev = preg_replace('/[^\w+]/', '', $person_abbrev);

        // Do some sanity checking on file
        if(!in_array($_FILES[$name]['type'], array_values(self::getSupportedMimeTypes()))){
            PHPWS_Core::initModClass('plm', 'exception/IllegalFileException.php');
            if($_FILES[$name]['type'] == 'application/octet-stream'){
                throw new IllegalFileException('Please save and close all word processors then re-submit file.');
            }
            throw new IllegalFileException('Invalid file type '.$_FILES[$name]['type']);
        }
        $matches = array();
        // ../ and stuff like that
        $result = preg_match('/.*\.(?P<ext>\w+)$/', $_FILES[$name]['name'], $matches);
        if($result == 0 || $result == FALSE){
            PHPWS_Core::initModClass('plm', 'exception/IllegalFileException.php');
            throw new IllegalFileException('File has an invalid extension');
        }

        
        $file_ext  = $matches['ext'];
        $file_dir  =  PHPWS_Settings::get('plm', 'file_dir').$nominee_email.'/'.$this->nomination->id.'/';

        if(!is_dir($file_dir)){
            $old = umask(0);
            mkdir($file_dir, 0775, true); //recursively creates directories as needed, file name has no part in this for this reason
            umask($old);
        }
        $file_name = $nominee_email.'-'.$type.'-'.$person_abbrev.'.'.$file_ext;

        if(!move_uploaded_file($_FILES[$name]['tmp_name'], $file_dir.$file_name)){
            PHPWS_Core::initModClass('plm', 'exception/FileException.php');
            throw new FileException('Could not save file!');
        }

        //grab the right person to save the file as
        $omnom = new Nomination;
        $person = $omnom->getMember($unique_id);

        //update the file name
        $db = new PHPWS_DB('plm_doc');
        $db->addValue('name', $file_dir.$file_name);

        //determine whether we should update or insert
        if(isset($person->doc_id)){
            $db->addWhere('id', $person->doc_id);
            $result = $db->update();
        } else {
            $result = $db->insert();
            $person->doc_id = $result;
        }

        //if the file is made of fail then remove it
        if(PHPWS_Error::logIfError($result)){
            unlink($file_dir.$file_name);
            throw new DatabaseException('Unable to associate file to user!');
        }

        //if insert then save else harmless
        $person->save();

        return true;
    }

    public function sendFile($unique_id)
    {
        $omnom = new Nomination;

        $person = $omnom->getMember($unique_id);

        if(is_null($person->doc_id)){
            throw new Exception('No such file!');
        }
        
        $db = new PHPWS_DB('plm_doc');
        $db->addWhere('id', $person->doc_id);
        $result = $db->select('row');

        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException($result->toString());
        }

        $title = explode('/', $result['name']);
        $title = array_pop($title);
        $sploded = explode('.', $title);
        $mimetype = self::$mimeTypes[$sploded[sizeof($sploded)-1]];
        header('Content-type: '.$mimetype);
        header('Content-Disposition: attachment; filename="' . $result['name'] . '"');
        readfile($result['name']);
        exit;
    }

    public function getFileWidget($name="choose_file", $form, $unique_id=NULL)
    {
        $form->setEncode(true);
        $file_desc = $this->getFileDesc();

        $tpl['NAME']      = $name;
        $tpl['TITLE']     = $file_desc->getTitle();
        $tpl['NEW_TITLE'] = 'Letter';
        $tpl['TYPE']      = $file_desc->getType();
        $tpl['SIZE']      = $file_desc->getSize();
        $tpl['IMG']       = "mod/plm/img/tango/mimetypes/x-office-document.png";

        $tpl['ID']       = $form->id.'_'.$name;
        if(!is_null($unique_id))
            $tpl['DOWNLOAD'] = $this->getDownloadLink($unique_id);
        
        javascript('jquery_ui');
        javascriptMod('plm', 'doc', array('PHPWS_SOURCE_HTTP' => PHPWS_SOURCE_HTTP));
        
        return PHPWS_Template::processTemplate($tpl, 'plm', 'plm_doc.tpl');
    }

    public function getDownloadLink($unique_id=NULL, $text='Download')
    {
        if(is_null($unique_id)){
            return "";
        }

        $this->nomination->getMember($unique_id);
        $factory = new ViewFactory();
        $view = $factory->get('DownloadFile');
        $view->unique_id  = $unique_id;
        $view->nomination = $this->nomination->id;
        
        return $view->getLink($text);
    }

    public function getFileDesc($emptyTitle='None')
    {
        if(!isset($this->nomination)){
            return new PLM_FileDescription('No file', 'empty', '0b');
        }
            
        $db = new PHPWS_DB('plm_doc');
        $db->addWhere('id', $this->nomination->doc_id);
        $result = $db->select('row');
        
        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException('No such file');
        }

        if(empty($result)){
            return new PLM_FileDescription('No file', 'empty', '0b');
        }

        $file = $result['name'];
        $type = mime_content_type($file);
        $size = filesize($file);
        return new PLM_FileDescription('Letter', $type, $size);
    }

    public static function getFileNames(){
        return PLM_Doc::$fileNames;
    }

    public static function getMimeTypes()
    {
        return PLM_Doc::$mimeTypes;
    }
    
    public static function getSupportedFileTypes()
    {
        $fileNames = PLM_Doc::$fileNames;
        $supported = PHPWS_Settings::get('plm', 'allowed_file_types');
        $supported = unserialize($supported);
        $types = array();
        foreach($supported as $fileType){
            $types[$fileType] = $fileNames[$fileType];
        }
        
        return $types;
    }

    public static function getSupportedMimeTypes()
    {
        $mimes = PLM_Doc::getMimeTypes();
        $supported = PLM_Doc::getSupportedFileTypes();
        
        $types = array();
        foreach($supported as $file=>$longName){
            $types[] = $mimes[$file];
        }
        return $types;
    }

    public static function delete($unique_id){
        PHPWS_Core::initModClass('plm', 'Nomination.php');
        $person = Nomination::getMember($unique_id);

        $db = new PHPWS_DB('plm_doc');
        $db->addWhere('id', $person->doc_id);
        $result = $db->select('row');

        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }

        // Delete file from file system
        unlink($result['name']);

        // Delete file path from DB
        $db->reset();
        $db->addWhere('id', $result['id']);
        $db->delete();
    }
}

class PLM_FileDescription {
    protected $title;
    protected $type;
    protected $size;

    public function __construct($title, $type, $size)
    {
        $this->title = $title;
        $this->type  = $type;
        $this->size  = $size;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSize()
    {
        return $this->size;
    }
}
?>