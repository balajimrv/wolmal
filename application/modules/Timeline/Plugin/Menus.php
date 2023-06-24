<?php

class Timeline_Plugin_Menus {
    
  public function canSelect() {
    // Check subject
    if( !Engine_Api::_()->core()->hasSubject('user') ) {
      return false;
    }
    $subject = Engine_Api::_()->core()->getSubject('user');

    // Check viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    if( $viewer->getIdentity() != $subject->getIdentity() ) {
      return false;
    }
    if (!Zend_Controller_Action_HelperBroker::getStaticHelper('RequireAuth')->setAuthParams('timeline', $subject, 'timeline_profile')->setNoForward()->checkRequire()) {
      return false;
    }
    return (bool) Zend_Controller_Action_HelperBroker::getStaticHelper('RequireAuth')->setAuthParams('timeline', $subject, 'user_can_select')->setNoForward()->checkRequire();

  }

}