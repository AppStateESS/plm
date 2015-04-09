<?php

  /**
   * NomineeSearch
   *
   * Nominee searching with AJAX
   *
   * @author Daniel West <dwest at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'Nominee.php');
PHPWS_Core::initModClass('plm', 'AjaxDBPager.php');

class NomineeSearch extends PlemmView
{
    public $query;
    public $pg;
    public $limit;

    public function display(Context $context)
    {
        if(!UserStatus::isAdmin() && !UserStatus::isCommitteeMember()){
            throw new PermissionException('You are not allowed to look at that!');
        }
        $ajax = !is_null($context['ajax']);
        $searchString = !is_null($context['query']) ? $context['query'] : '';

        $this->query = $searchString;

        if(!is_null($context['pg'])){
            $this->pg = $context['pg'];
        }
        
        if(!is_null($context['limit'])){
            $this->limit = $context['limit'];
        }

        if($ajax){
            echo $this->getPager($searchString);
            exit;
        } else {
            javascript('jquery_ui');
            javascriptMod('plm', 'search', array('PHPWS_SOURCE_HTTP' => PHPWS_SOURCE_HTTP));
            $form = new PHPWS_Form('search');
            $form->setMethod('get');
            $form->addText('query', $searchString);
            if(!is_null($this->pg)){
                $form->addHidden('pg', $this->pg);
            }

            if(!is_null($this->limit)){
                $form->addHidden('limit', $this->limit);
            }

            $form->addHidden('module', 'plm');
            $form->addHidden('view', 'NomineeSearch');
            $form->addSubmit('Search');
            $tpl = $form->getTemplate();

            $tpl['PAGER'] = $this->getPager($searchString);
            $tpl['TITLE'] = 'Nominee Search';

            Layout::addPageTitle('Nominee Search');

            return PHPWS_Template::process($tpl, 'plm', 'admin/search.tpl');
        }
    }

    public function getPager($searchString="")
    {
        PHPWS_Core::initModClass('plm', 'Period.php');

        $pager = new AjaxDBPager(array('ajax'=>0), NOMINEE_TABLE, 'Nominee');
        $pager->setModule('plm');
        $pager->setTemplate('admin/nominee_search_results.tpl');
        $pager->setEmptyMessage('No matching nominees found');

        $pager->db->addWhere('first_name', "%".$searchString."%", "like", 'or', 'search');
        $pager->db->addWhere('middle_name', "%".$searchString."%", "like", 'or', 'search');
        $pager->db->addWhere('last_name', "%".$searchString."%", "like", 'or', 'search');
        
        // Committee members should only see completed nominations.
        if(UserStatus::isCommitteeMember() && !Current_User::isDeity()){
            $pager->db->addWhere('plm_nomination.completed', TRUE);
        }

        $pager->db->addJoin('left', 'plm_nominee', 'plm_nomination', 'id', 'nominee_id');
        $pager->db->addWhere('plm_nomination.period', Period::getCurrentPeriodYear());

        $pager->addSortHeader('first_name', 'First');
        $pager->addSortHeader('middle_name', 'Middle');
        $pager->addSortHeader('last_name', 'Last');
        $pager->addRowTags('rowTags');

        return $pager->get();
    }

    public function getRequestVars(){
        $vars = array('view'=>'NomineeSearch');

        if(isset($this->query)){
            $vars['query'] = $this->query;
        }

        if(isset($this->pg)){
            $vars['pg'] = $this->pg;
        }

        if(isset($this->limit)){
            $vars['limit'] = $this->limit;
        }

        return $vars;
    }
}
?>
