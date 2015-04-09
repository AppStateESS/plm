<?php

/**
 * DownloadFile
 *
 *   Sends a file to the client... special thanks to Jeremy for the 
 * download examples.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'PLM_Doc.php');

class DownloadFile extends PlemmView {
    public $unique_id;
    public $nomination;

    public function getRequestVars()
    {
        $vars = array('view'=>'DownloadFile');

        if(isset($this->unique_id)){
            $vars['unique_id'] = $this->unique_id;
        }

        if(isset($this->nomination)){
            $vars['nomination'] = $this->nomination;
        }

        return $vars;
    }

    public function display(Context $context)
    {
        $omnom = new Nomination;
        $omnom->id = $context['nomination'];
        $omnom->load();

        $doc = new PLM_Doc($omnom);
        $doc->sendFile($context['unique_id']);
    }
}
?>
