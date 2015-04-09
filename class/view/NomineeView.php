<?php

  /**
   * NomineeView
   *
   * View all details for nominee and their nominations.
   * All nominations related to this nominee for current term are shown.
   * An administrator can set a nomination's winning status in this view.
   */

PHPWS_Core::initModClass('plm', 'View.php');
PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'Nominee.php');
PHPWS_Core::initModClass('plm', 'Nomination.php');

class NomineeView extends PlemmView {
    public $nomineeId;

    public function display(Context $context)
    {
        if(!(UserStatus::isCommitteeMember() || UserStatus::isAdmin())){
            throw new PermissionException('You are not allowed to see that!');
        }
        
        $tpl = array();

        $nominee = new Nominee;
        $nominee->id = $context['id'];
        $nominee->load();

        $tpl['NAME']        = $nominee->getFullName();
        $tpl['MAJOR']       = $nominee->getMajor();
        $tpl['YEARS']       = $nominee->getYears();
        $tpl['EMAIL']       = $nominee->getEmailLink();

        $db = Nomination::getDb();
        $db->addWhere('nominee_id', $nominee->id);
        $db->addOrder('winner desc');
        $results = $db->getObjects('Nomination');
        
        if(PHPWS_Error::logIfError($results)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException('Database asploded');
        } 
        if(is_null($results) || empty($results)){
            PHPWS_Core::initModClass('plm', 'exception/DatabaseException.php');
            throw new DatabaseException('Invalid Nominee ID');
        }

        $num = 0;
        $jsVars = array();
        $nomIsWinner = False;
        foreach($results as $nomination){
            $num++;
            $context['id'] = $nomination->getId();
            $nominationView = new NominationView();

            $nomination_is_winner = $nomination->isWinner();
            if($nomination_is_winner)$nomIsWinner = True;
            
            if(UserStatus::isAdmin()){
                $icon = $nomination->isWinner() ? 'mod/plm/img/tango/actions/list-remove-red.png':
                    'mod/plm/img/tango/actions/list-add-green.png';
                $award_icon = 'mod/plm/img/tango/mimetypes/application-certificate.png';
            } else {
                // Don't show if nomination is winner to committee members
                $icon = 'images/icons/blank.png';
                $award_icon = 'images/icons/blank.png';
            }
            $tpl['nominations'][] = array('CONTENT' => $nominationView->display($context),
                                          'NUM' => $num,
                                          'ICON' => PHPWS_SOURCE_HTTP.$icon,
                                          'AWARD_ICON' => PHPWS_SOURCE_HTTP.$award_icon,
                                          'DOWN_PNG_HACK' => PHPWS_SOURCE_HTTP."mod/plm/img/arrow_down.png");

            // pass this to javascript
            $jsVars['collapse'][] = array('NUM' => $num, 'ID' => $nomination->getId());
            $jsVars['winner'][] = array('NUM' => $num, 'ID' => $nomination->getId(), 'WINNER' => $nomination->isWinner());
        }


        javascript('jquery');
        // JS Collapse; Admin and Committee
        javascriptMod('plm', 'nomCollapse', 
                      array('noms' => json_encode($jsVars['collapse']),
                            'PHPWS_SOURCE_HTTP' => PHPWS_SOURCE_HTTP));
        // Full path is needed for images
        $tpl['PHPWS_SOURCE_HTTP'] = PHPWS_SOURCE_HTTP;

        Layout::addPageTitle('Nominee View');

        if(UserStatus::isAdmin()){
            // JS set winner; Admin only
            javascriptMod('plm', 'nomWinner', array('noms' => json_encode($jsVars['winner']),
                                                    'PHPWS_SOURCE_HTTP' => PHPWS_SOURCE_HTTP));
            // If nomination is winner then set the winner flag beside the
            // nominee's name in big letters
            if($nomIsWinner) $tpl['WINNER'] = '(Winner)';
            
            return PHPWS_Template::process($tpl, 'plm', 'admin/nominee.tpl');
        }
        return PHPWS_Template::process($tpl, 'plm', 'committee/nominee.tpl');
    }

    public function getRequestVars(){
        $vars = array('id'   => $this->nomineeId,
                      'view' => 'NomineeView');

        return $vars;
    }
}
?>
