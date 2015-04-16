<?php

/**
 * NominationDocument
 *
 *   Normalizes filenames, and facilitates providing a consistent file
 * upload interface to the user.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @author Jeremy Booker
 * @package nomination
 */

PHPWS_Core::initModClass('nomination', 'Nomination.php');
PHPWS_Core::initModClass('nomination', 'Nominator.php');
PHPWS_Core::initModClass('nomination', 'Reference.php');

PHPWS_Core::initModClass('nomination', 'exception/IllegalFileException.php');

class NominationDocument {

    private $id;

    private $nomination;

    private $uploadedBy; // Who uploaded it, 'nominator', or 'reference'
    private $description; // A descriptive name ('statement', 'reference')
    private $filePath; // Partial path from the 'doc' root (see Settings) to this file
    private $fileName; // Our name for this file, to avoid possible conflicts.
    private $origFileName; // The name of the file when it was uploaded
    private $mimeType; // Mime type

    private $fileUploadInfo; // Info from the $_FILES array on uploaded. Only used on initial creation; not populated when loaded from DB

    private $allowedTypes;


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


    /**
     * Creates a new Nomination Document from a file upload.
     * @param Nomination $nom The nomination this document corresponds to
     * @param unknown $uploadedBy //TODO
     * @param unknown $description
     * @param array $fileUploadInfo
     */
    public function __construct(Nomination $nom, $uploadedBy, $description, Array $fileUploadInfo)
    {
        $this->nomination     = $nom;
        $this->uploadedBy     = $uploadedBy;
        $this->description    = $description;
        $this->fileUploadInfo = $fileUploadInfo;

        $this->filePath = "";

        $this->origFileName = $fileUploadInfo['name'];
        $this->mimeType     = $fileUploadInfo['type'];


        // Get the allowed types from Settings
        $this->allowedTypes = PHPWS_Settings::get('nomination', 'allowed_file_types');

        // Grab the actual file data and put it in the correct location
        $this->receiveFile();
    }

    /**
     * Receives a file
     */
    private function receiveFile()
    {

        // Check that the file's mime type matches an acceptable type
        if(!$this->isAllowedType()){
            throw new IllegalFileException('Invalid file type: ' . $this->mimeType);
        }

        // Check the file extension
        $fileExt  = self::getFileExtension($this->origFileName);
        if(is_null($fileExt)){
            PHPWS_Core::initModClass('nomination', 'exception/IllegalFileException.php');
            throw new IllegalFileException('File has an invalid extension');
        }

        $nomineeEmail = $this->nomination->getEmail();

        // Clean up the email address, just in case
        $nomineeEmail = preg_replace('/[^\w+]/', '', $nomineeEmail); // Remove any whitespace
        $nomineeEmail = preg_replace('/[^a-z0-9]/i', '', $nomineeEmail); // Remove anything other than letters & numbers


        // Generate the path to save the file at, creating a directory for this nomination, if necessary
        // Generate the full path to this file using the file_dir setting
        $basePath = self::getBasePath();
        $this->filePath = self::getLocalPath();

        $fileDir  =  $basePath.$this->filePath;

        // Make sure the path we've created exists, create it if needed
        if(!is_dir($fileDir)){
            $old = umask(0);
            mkdir($fileDir, 0775, true); //recursively creates directories as needed, file name has no part in this for this reason
            umask($old);
        }

        // Generate a unique file name and save it
        $this->fileName = self::getUniqueFileName();

        // Put together the full path
        $fullPath = $fileDir.$this->fileName;

        // Move the file from it's tmp location to it's final resting place
        if(!move_uploaded_file($this->fileUploadInfo['tmp_name'], $fullPath)){
            PHPWS_Core::initModClass('nomination', 'exception/FileException.php');
            throw new FileException('Could not save file!');
        }
    }

    /**
     * Returns true if a file has an allowed mime type, false otherwise.
     * @return boolean
     */
    private function isAllowedType()
    {
        if(!in_array($this->mimeType, array_values(self::getSupportedMimeTypes()))){
            return false;
        }

        return true;
    }

    /**
     * Returns a uniquley generated file name for this file
     *
     * @return string
     */
    public function getUniqueFileName()
    {
        return $this->description . '-' . uniqid() . '.' . self::getFileExtension($this->origFileName);
    }

    /**
     * Returns the local file path (after the configured basePath
     * to put this file in. This is in the format of "userName-nominationId".
     *
     * @see getBasePath
     * @return string
     */
    public function getLocalPath()
    {
        $email = self::normalizeUsername($this->nomination->getEmail());
        return $email . '-' . $this->nomination->getId() . '/';
    }


    /*********************
     * Getter & Setter Methods
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNomination() {
        return $this->nomination;
    }
    
    public function setNominationById($id) {
        PHPWS_Core::initModClass('nomination', 'NominationFactory.php');
        $nom = new NominationFactory();
        $this->nomination = $nom->getNominationbyId($id);
    }

    public function getUploadedBy() {
        return $this->uploadedBy;
    }

    public function setUploadedBy($uploader) {
        if ($uploader == 'nominator' || $uploader == 'reference') {
            $this->uploadedBy = $uploader;
        } else {
            //TODO Maybe throw an error here?
        }
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($descrip) {
        $this->description = $descrip;
    }

    public function getFilePath() {
        return $this->filePath;
    }

    public function setFilePath($path) {
        $this->filePath = $path;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function setFileName($name) {
        $this->fileName = $name;
    }

    public function getOrigFileName() {
        return $this->origFileName;
    }

    public function setOrigFileName($name) {
        $this->origFileName = $name;
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    public function setMimeType($mime) {
        $oldMime = $this->mimeType; // save the old mimeType in case the new one is disallowed
        $this->mimeType = $mime;
        if (!$this->isAllowedType()) {
            $this->mimeType = $oldMime;
        }
    }

    /**************************
     * Static Utility Methods *
     */


    /**
     * Returns the configured file path for saving files.
     *
     * @return string
     */
    public static function getBasePath()
    {
        return  PHPWS_Settings::get('nomination', 'file_dir');
    }

    /**
     * Cleans up a user name to remove any special characters. Returns
     * the string with only letters and number. No whitespace or special characters.
     *
     * @param string $user
     * @return string
     */
    public static function normalizeUsername($user)
    {
        // Clean up the email address, just in case
        $user = preg_replace('/[^\w+]/', '', $user); // Remove any whitespace
        $user = preg_replace('/[^a-z0-9]/i', '', $user); // Remove anything other than letters & numbers

        return $user;
    }

    /**
     * Returns a string containing the file name's extention, or null if one could not be found.
     *
     * @param String $FileName The file name
     * @return String file's extension
     */
    public static function getFileExtension($fileName)
    {
        $matches = array();
        $result = preg_match('/.*\.(?P<ext>\w+)$/', $fileName, $matches);
        if($result == 0 || $result == FALSE){
        }

        if($matches['ext'] == ''){
            return null;
        }else{
            return $matches['ext'];
        }
    }

    /**
     * Cause the old one don't work.
     *
     * @param $id doc_id from nomination_reference table in DB
     */
    public function newSendFile($id) {
        if (!isset($id)) {
            throw new Exception('No such file!');
        }

        $db = new PHPWS_DB('nomination_document');
        $db->addWhere('id', $id);
        $result = $db->select('row');

        if (PHPWS_Error::logIfError($result)) {
            throw new DatabaseException($result->toString());
        }
        
        header('Content-type: ' . $result['mime_type']);
        header('Content-Disposition: attachment; filename="' . $result['file_name'] . '"');
        $fullPath = PHPWS_Settings::get('nomination', 'file_dir') . $result['file_path'] . $result['file_name'];
        readfile($fullPath);
        exit;
    }


    /*******************
     * OLD STUFF BELOW *
     *******************/



    public function sendFile($unique_id)
    {
        $omnom = new Nomination;

        $person = $omnom->getMember($unique_id);

        if(is_null($person->doc_id)){
            throw new Exception('No such file!');
        }

        $db = new PHPWS_DB('nomination_document');
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

    /**
     * @param $doc NomincationDocument
     */
    public static function getFileWidget($doc=NULL, $name="choose_file", $form)
    {
        $form->setEncode(true);
        $file_desc = self::getFileDesc($doc);

        $tpl['NAME']      = $name;
        $tpl['TITLE']     = $file_desc->getTitle();
        $tpl['NEW_TITLE'] = 'Letter';
        $tpl['TYPE']      = $file_desc->getType();
        $tpl['SIZE']      = $file_desc->getSize();
        $tpl['IMG']       = "mod/nomination/img/tango/mimetypes/x-office-document.png";

        $tpl['ID']       = $form->id.'_'.$name;
        if(!is_null($doc))
            $tpl['DOWNLOAD'] = $doc->getDownloadLink();

        javascript('jquery_ui');
        javascriptMod('nomination', 'doc', array('PHPWS_SOURCE_HTTP' => PHPWS_SOURCE_HTTP));

        return PHPWS_Template::processTemplate($tpl, 'nomination', 'nomination_doc.tpl');
    }

    public function getDownloadLink($unique_id=NULL, $text='Download')
    {
        if(is_null($unique_id)){
            return "";
        }

        //$this->nomination->getMember($unique_id); // I don't even..... getMember() doesn't exist anywhere! What would it do if it did?
        $factory = new ViewFactory();
        $view = $factory->get('DownloadFile');
        $view->unique_id  = $unique_id;
        $view->nomination = $this->nomination->id;

        return $view->getLink($text);
    }

    public static function getFileDesc(NominationDocument $doc = null, $emptyTitle='None')
    {
        if(is_null($doc)){
            return new FileDescription('No file', 'empty', '0b');
        }

        $file = $doc->getFullPath();
        $type = mime_content_type($file);
        $size = filesize($file);
        return new FileDescription('Letter', $type, $size);
    }

    public static function getFileNames(){
        return self::$fileNames;
    }

    public static function getMimeTypes()
    {
        return self::$mimeTypes;
    }

    public static function getSupportedFileTypes()
    {
        $fileNames = self::$fileNames;
        $supported = PHPWS_Settings::get('nomination', 'allowed_file_types');
        $supported = unserialize($supported);
        $types = array();
        foreach($supported as $fileType){
            $types[$fileType] = $fileNames[$fileType];
        }

        return $types;
    }

    public static function getSupportedMimeTypes()
    {
        $mimes = self::getMimeTypes();
        $supported = self::getSupportedFileTypes();

        $types = array();
        foreach($supported as $file=>$longName){
            $types[] = $mimes[$file];
        }
        return $types;
    }

    public static function delete($unique_id){
        PHPWS_Core::initModClass('nomination', 'Nomination.php');
        $person = Nomination::getMember($unique_id);

        $db = new PHPWS_DB('nomination_document');
        $db->addWhere('id', $person->doc_id);
        $result = $db->select('row');

        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('nomination', 'exception/DatabaseException.php');
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

class FileDescription {

    private $title;
    private $type;
    private $size;

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

/**
 * Empty child class for NominationDocument for loading object from the database.
 *
 * @author Chris Coley
 */
class DBNominationDocument extends NominationDocument {
    /**
     * Empty constructor for restoring objects from a database
     * without calling the parent class' constructor.
     */
    public function __construct() {}
}
?>
