<?php

/*
 * NoOp
 *
 *  So we didn't consider all the angles on the only post commands, only get views thing.
 * NoOp allows a view to post and get back another view.  Useful for forms with cancel buttons
 * etc.  Does nothing basically, and lets the controller handle showing the next view.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package plm
 */

class NoOp extends Command {

    public function getRequestVars()
    {
        $vars = array('action'=>'NoOp');

        return $vars;
    }

    public function execute(Context $context)
    {
        return;
    }
}