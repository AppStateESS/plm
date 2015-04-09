<?php
  /**
   * NominationNotificationView
   *
   *   Handles rendering NQ messages for this application.
   *
   * @author Daniel West <dwest at tux dot appstate dot edu>
   * @inspired-by Jeff Tickle's NotificationView in hms
   * @package nomination
   */

define('NOMINATION_SUCCESS', 0);
define('NOMINATION_ERROR',   1);
define('NOMINATION_WARNING', 2);

class NominationNotificationView
{
    private $notifications = array();

    public function popNotifications()
    {
        $this->notifications = NQ::popAll('nomination');
    }

    public function immediateError($message)
    {
        NQ::simple('nomination', NOMINATION_ERROR, $message);
        NQ::close();
        //header("Location: index.php?module=nomination"); // This is not smart. It causes redirect loops if *anything* goes wrong.
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
        $content = PHPWS_Template::process($tpl, 'nomination', 'NotificationView.tpl');

        javascript('jquery');
        javascriptMod('nomination', 'jsnotification');

        return $content;
    }
    
    public function resolveType(Notification $notification)
    {
        switch($notification->getType()){
            case NOMINATION_SUCCESS:
                return 'SUCCESS';
            case NOMINATION_ERROR:
                return 'ERROR';
            case NOMINATION_WARNING:
                return 'WARNING';
            default:
                return 'UNKNOWN';
        }
    }
}
?>
