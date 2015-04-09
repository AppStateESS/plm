<?php
  /**
   * AjaxDBPager
   *
   *   Allows you to remove values from the link created by DBPager
   *  so that a pager created by an ajax call wont redirect to the
   *  ajax view.
   *
   * @author Daniel West <dwest at tux dot appstate dot edu>
   * @package plm
   */

PHPWS_Core::initCoreClass('DBPager.php');

class AjaxDBPager extends DBPager {
    protected $exclusionMap;

    public function __construct(Array $exclusionMap, $table, $class=null){
        $this->exclusionMap = $exclusionMap;

        parent::__construct($table, $class);
    }

    public function getLinkValues(){
        return array_diff_key(parent::getLinkValues(), $this->exclusionMap);
    }
}
?>