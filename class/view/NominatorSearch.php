<?php

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'Nominator.php');
PHPWS_Core::initCoreClass('DBPager.php');

class NominatorSearch extends PlemmView
{
    public function getRequestVars()
    {
        return array('view'=>'NominatorSearch');
    }

    public function display(Context $context)
    {
        if(!UserStatus::isAdmin() && !UserStatus::isCommitteeMember()){
            throw new PermissionException('You are not allowed to look at that!');
        }

        $ajax = !is_null($context['ajax']);
        $searchString = !is_null($context['query']) ? $context['query'] : '';

        if($ajax){
            echo $this->getPager($searchString);
            exit;
        } else {
            javascript('jquery_ui');
            javascriptMod('plm', 'search', array('PHPWS_SOURCE_HTTP' => PHPWS_SOURCE_HTTP));
            $form = new PHPWS_Form('search');
            $form->setMethod('get');
            $form->addText('query', $searchString);
            $form->addHidden('module', 'plm');
            $form->addHidden('view', 'NominatorSearch');
            $form->addSubmit('Search');
            $tpl = $form->getTemplate();

            $tpl['PAGER'] = $this->getPager($searchString);
            $tpl['TITLE'] = 'Nominator Search';
            
            Layout::addPageTitle('Nominator Search');

            return PHPWS_Template::process($tpl, 'plm', 'admin/search.tpl');
        }
    }

    public function getPager($searchString="")
    {
        PHPWS_Core::initModClass('plm', 'Period.php');

        $pager = new DBPager(NOMINATOR_TABLE, 'Nominator');
        $pager->setModule('plm');
        $pager->setTemplate('admin/nominator_search_results.tpl');
        $pager->setEmptyMessage('No matching nominees found');

        $pager->db->addWhere('first_name', "%".$searchString."%", "like", 'or', 'search');
        $pager->db->addWhere('middle_name', "%".$searchString."%", "like", 'or', 'search');
        $pager->db->addWhere('last_name', "%".$searchString."%", "like", 'or', 'search');
        $pager->db->addWhere('email', "%".$searchString."%", "like", 'or', 'search');

        // Committee members should only see completed nominations.
        if(UserStatus::isCommitteeMember() && !Current_User::isDeity()){
            $pager->db->addWhere('plm_nomination.completed', TRUE);
        }
        
        $pager->db->addJoin('left', 'plm_nominator', 'plm_nomination', 'id', 'nominator_id');
        $pager->db->addWhere('plm_nomination.period', Period::getCurrentPeriodYear());

        $pager->joinResult('id', 'plm_nomination', 'nominator_id', 'added_on', 'added_on');

        $pager->addSortHeader('first_name', 'First');
        $pager->addSortHeader('middle_name', 'Middle');
        $pager->addSortHeader('last_name', 'Last');
        $pager->addSortHeader('added_on', 'Submission Date');
        $pager->addSortHeader('nominee_link', 'Nominee');
        $pager->addRowTags('rowTags');

        return $pager->get();
    }
}
?>
