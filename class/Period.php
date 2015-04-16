<?php
  /**
   * Period
   *
   * Represents the current nomination period/year.
   * Handles start and end dates for nominations.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('plm', 'PLM_Model.php');

define('PERIOD_TABLE', 'plm_period');

class Period extends PLM_Model
{
    public $year;
    public $start_date;
    public $end_date;

    public function getDb()
    {
        return new PHPWS_DB(PERIOD_TABLE);
    }

    /**
     * Getters and Setters
     */
    public function getYear()
    {
        return $this->year;
    }
    public function getStartDate()
    {
        return $this->start_date;
    }
    public function getReadableStartDate()
    {
        return strftime("%m/%d/%Y", $this->getStartDate());
    }
    public function getEndDate()
    {
        return $this->end_date;
    }
    public function getReadableEndDate()
    {
        return strftime("%m/%d/%Y", $this->getEndDate());
    }
    public function setYear($year)
    {
        $this->year = $year;
    }
    public function setStartDate($date)
    {
        $this->start_date = $date;
    }
    public function setEndDate($date)
    {
        $this->end_date = $date;
    }

    /********
     * Util *
     ********/
    // Determine if this period is over
    public static function isOver()
    {
        $now = time();
        $currPeriod = Period::getCurrentPeriod();
        if($now > $currPeriod->getEndDate()){
            return true;
        } else {
            return false;
        }
    }
    // Determine if this period has begun yet
    public static function hasBegun()
    {
        $now = time();
        $currPeriod = Period::getCurrentPeriod();
        if($now > $currPeriod->getStartDate()){
            return true;
        } else {
            return false;
        }
    }

    // Start date will default to October 1st
    public function setDefaultStartDate()
    {

        $this->start_date = mktime(0,0,0, $month=10, $day=1, $year=$this->year);
    }
    // End date will be June 30th of next year
    public function setDefaultEndDate()
    {

        $this->end_date = mktime(0,0,0, $month=6, $day=30, $year=$this->year+1);
    }


    /*******************
     * Factory Methods *
     *******************/
    /**
     * Get a Period object for current year
     * @return period - Period object
     */
    public static function getPeriodByYear($year)
    {
        $db = new PHPWS_DB(PERIOD_TABLE);
        
        $db->addWhere('year', $year);
        
        $result = $db->select();
        
        // if null results return null
        if(empty($result) || is_null($result)){
            return null;
        }

        // Create the Period object
        $period = new Period($result[0]['id']);
        
        return $period;
    }

    /**
     * @return period - Return current Period object
     */
    public static function getCurrentPeriod()
    {
        $year = PHPWS_Settings::get('plm', 'current_period');
        return Period::getPeriodByYear($year);
    }
    /**
     * @return - current period stored in settings
     */
    public static function getCurrentPeriodYear()
    {
        return PHPWS_Settings::get('plm', 'current_period');
    }
    /**
     * @return year - The next period 
     */
    public static function getNextPeriodYear()
    {
        $currPeriod = Period::getCurrentPeriod();
        return $currPeriod->getYear()+1;
    }
}
?>
