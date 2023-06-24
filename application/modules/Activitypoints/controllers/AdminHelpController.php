<?php

class Activitypoints_AdminHelpController extends Core_Controller_Action_Admin
{


  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activitypoints_admin_main_help');

  }


  public function resetAction()
  {
    
    Engine_Api::_()->getDbtable('points', 'activitypoints')->delete("1=1");
    Engine_Api::_()->getDbtable('counters', 'activitypoints')->delete("1=1");
    
    return $this->_helper->redirector->gotoRoute( array(
                                                        'module'      => 'activitypoints',
                                                        'controller'  => 'help',
                                                        'action'      => 'index'
                                                        )
                                                 );
    
  }
  
}