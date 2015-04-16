<?php

PHPWS_Core::initModClass('nomination', 'Context.php');
PHPWS_Core::initModClass('nomination', 'view/NominationNotificationView.php');
// Go ahead and init this class, used in all admin views
PHPWS_Core::initModClass('nomination', 'exception/PermissionException.php');

abstract class NominationMod
{
    /**
     * The default-default view.
     * If you user goes to index.php?module=nomination
     * the Null view will be shown. This is overridden
     * by AdminNomination, CommitteeNomination, and GuestNomination.
     * Any new userviews should probably override this too.
     */
    protected $defaultView = 'Null';

    protected $context;
    protected $content;

    public function process()
    {
        // check_overpost is a thing with forms. if it is set then
        // we are most likely trying to redirect the user back to
        // their form
        if(!empty($_GET) && !isset($_GET['check_overpost'])){
            $this->context = new Context($_GET);
        }
        // Execute a command and redirect to it's after view
        else if(!empty($_POST)){
            PHPWS_Core::initModClass('nomination', 'CommandFactory.php');

            $this->context = new Context($_POST);

            $cmdFactory = new CommandFactory();
            $cmd = $cmdFactory->get($this->context['action']);
            try{
                $cmd->execute($this->context);

                if(isset($this->context['after']) && $this->context['after'] instanceof View){
                    $after = $this->context['after']->getURI();
                } else {
                    $after = isset($this->context['after']) ? 'index.php?module=nomination&view='.$this->context['after'] : 'index.php';
                }

                NQ::close();
                header("Location: ".$after);
                exit();
            } catch (Exception $e) {
                $this->context['view'] = isset($this->context['after']) ? $this->context['after'] : 'Null';
                NQ::simple('nomination', NOMINATION_ERROR, $e->getMessage());
            }
        }
        PHPWS_Core::initModClass('nomination', 'ViewFactory.php');

        /* Show any notifications */
        $nv = new NominationNotificationView();
        $nv->popNotifications();

        try{
            $this->context['nq'] = $nv->show();
        } catch (InvalidArgmentException $e){
            NominationNotificationView::immediateError($e->getMessage());
        }

        $vFactory = new ViewFactory();

        // If view is not set in context then show the default view
        $view = isset($this->context['view']) ? $this->context['view'] : $this->defaultView;

        // Get view from factory and show it
        //try{
            $theView = $vFactory->get($view);
            $this->content = $theView->display($this->context);
        //} catch (Exception $e){
            //NominationNotificationView::immediateError($e->getMessage());
        //}
    }
}

?>
