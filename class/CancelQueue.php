<?php

/*
 * CancelQueue
 *
 *   Manages a queue of items which users have requested be canceled, 
 * but which are still awaiting administrator approval.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

PHPWS_Core::initModClass('plm', 'Nomination.php');
PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');

class CancelQueue {
    public $nomination;

    public static function add(Nomination $n)
    {
        $db = new PHPWS_DB('plm_cancel_queue');
        $db->addValue('nomination', $n->id);
        $result = $db->insert();

        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException($result->toString());
        }

        return true;
    }

    public static function approve(Nomination $n)
    {
        //TODO: delete from nomination table

        CancelQueue::remove($n);
    }

    public static function deny(Nomination $n)
    {
        //TODO: email appropriate people

        CancelQueue::remove($n);
    }

    public static function remove(Nomination $n)
    {
        $db = new PHPWS_DB('plm_cancel_queue');
        $db->addWhere('nomination', $n->id);
        $result = $db->delete();

        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException($result->toString());
        }

        return true;
    }

    public function rowTags()
    {
        PHPWS_Core::initModClass('plm', 'CommandFactory.php');

        $tpl = array();

        $omnom = new Nomination($this->nomination);
        $vf = new ViewFactory;
        $cf = new CommandFactory();

        $tpl['NOMINATION']  = $this->nomination;
        $tpl['APPROVE'] = 'Approve';
        $tpl['DENY'] = 'Deny';
        $tpl['NAME'] = $omnom->getNomineeName();

        //get link to view nomination
        $view = $vf->get('NominationView');
        $view->nominationId = $omnom->id;

        // Approval form
        $approveForm = new PHPWS_Form('approve');
        $approve = $cf->get('DeleteNomination');
        $approve->nominationId = $omnom->id;
        $approve->initForm($approveForm);
        $apptpl = $approveForm->getTemplate();
        $tpl['START_APPRV_FORM'] = $apptpl['START_FORM'];
        $tpl['END_APPRV_FORM'] = $apptpl['END_FORM'];

        // Denial form
        $denyForm = new PHPWS_Form('deny');
        $deny = $cf->get('AdminDenyCancel');
        $deny->nominationId = $omnom->id;
        $deny->initForm($denyForm);
        $denytpl = $denyForm->getTemplate();
        $tpl['START_DENY_FORM'] = $denytpl['START_FORM'];
        $tpl['END_DENY_FORM'] = $denytpl['END_FORM'];

        $tpl['PHPWS_SOURCE_HTTP'] = PHPWS_SOURCE_HTTP;

        return $tpl;
    }

    public function contains($id){
        $db = new PHPWS_DB('plm_cancel_queue');
        $db->addWhere('nomination', $id);
        $result = $db->select();

        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException($result->toString());
        }

        return sizeof($result) > 0;
    }
}

?>