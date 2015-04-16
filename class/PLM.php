<?php

PHPWS_Core::initModClass('plm', 'Context.php');
PHPWS_Core::initModClass('plm', 'view/PLMNotificationView.php');
// Go ahead and init this class, used in all admin views
PHPWS_Core::initModClass('plm', 'exception/PermissionException.php');

abstract class PLM
{
    /**
     * The default-default view.
     * If you user goes to index.php?module=plm 
     * the Null view will be shown. This is overridden 
     * by AdminPLM, CommitteePLM, and GuestPLM.
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
            PHPWS_Core::initModClass('plm', 'CommandFactory.php');

            $this->context = new Context($_POST);

            $cmdFactory = new CommandFactory();
            $cmd = $cmdFactory->get($this->context['action']);
            try{
                $cmd->execute($this->context);

                if(isset($this->context['after']) && $this->context['after'] instanceof View){
                    $after = $this->context['after']->getURI();
                } else {
                    $after = isset($this->context['after']) ? 'index.php?module=plm&view='.$this->context['after'] : 'index.php';
                }

                NQ::close();
                header("Location: ".$after);
                exit();
            } catch (Exception $e) {
                $this->context['view'] = isset($this->context['after']) ? $this->context['after'] : 'Null';
                NQ::simple('plm', PLM_ERROR, $e->getMessage());
            }
        }
        PHPWS_Core::initModClass('plm', 'ViewFactory.php');

        /* Show any notifications */
        $nv = new PLMNotificationView();
        $nv->popNotifications();

        try{
            $this->context['nq'] = $nv->show();
        } catch (InvalidArgmentException $e){
            PLMNotificationView::immediateError($e->getMessage());
        }
        
        $vFactory = new ViewFactory();

        // If view is not set in context then show the default view 
        $view = isset($this->context['view']) ? $this->context['view'] : $this->defaultView;

        // Get view from factory and show it
        try{
            $theView = $vFactory->get($view);
            $this->content = $theView->display($this->context);
        } catch (Exception $e){
            PLMNotificationView::immediateError($e->getMessage());
        }
    }
}

?>