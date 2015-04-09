<?php
  /**
   * PLMNotificationView
   *
   *   Handles rendering NQ messages for this application.
   *
   * @author Daniel West <dwest at tux dot appstate dot edu>
   * @inspired-by Jeff Tickle's NotificationView in hms
   * @package plm
   */

define('PLM_SUCCESS', 0);
define('PLM_ERROR',   1);
define('PLM_WARNING', 2);

class PLMNotificationView
{
    private $notifications = array();

    public function popNotifications()
    {
        $this->notifications = NQ::popAll('plm');
    }

    public function immediateError($message)
    {
        NQ::simple('plm', PLM_ERROR, $message);
        NQ::close();
        header("Location: index.php?module=plm");
        exit();
    }

    public function show()
    {
		if(empty($this->notifications)) {
			return '';
		}
		$tpl = array();
		$tpl['NOTIFICATIONS'] = array();
		foreach($this->notifications as $notification) {
		    
			if(!$notification instanceof Notification) {
				throw new InvalidArgumentException('Something was pushed onto the NQ that was not a Notification.');
			}
			$type = self::resolveType($notification);
			$tpl['NOTIFICATIONS'][][$type] = $notification->toString();
		}
        $content = PHPWS_Template::process($tpl, 'plm', 'NotificationView.tpl');

        javascript('jquery');
        javascriptMod('plm', 'jsnotification');

        return $content;
    }
    
    public function resolveType(Notification $notification)
    {
        switch($notification->getType()){
            case PLM_SUCCESS:
                return 'SUCCESS';
            case PLM_ERROR:
                return 'ERROR';
            case PLM_WARNING:
                return 'WARNING';
            default:
                return 'UNKNOWN';
        }
    }
}
?>
