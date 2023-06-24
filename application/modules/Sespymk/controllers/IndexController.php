<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sespymk
 * @package    Sespymk
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: IndexController.php 2017-03-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sespymk_IndexController extends Core_Controller_Action_Standard {

  public function requestsAction() {
  
    // Check for users only
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    $this->_helper->content->setEnabled();
  
  }
  
  public function friendrequestssentAction() {
  
    // Check for users only
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    $this->_helper->content->setEnabled();
  
  }
  
  public function inviteAction() {
    
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // Check if admins only
    if( $settings->getSetting('user.signup.inviteonly') == 1 ) {
      if( !$this->_helper->requireAdmin()->isValid() ) {
        return;
      }
    }

    // Check for users only
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    if (isset($_POST['params']) && $_POST['params'])
      parse_str($_POST['params'], $searchArray);
      
    $viewer = Engine_Api::_()->user()->getViewer();
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $db = $inviteTable->getAdapter();
    $db->beginTransaction();

    try {
      $emailsSent = $inviteTable->sendInvites($viewer, $searchArray['recipients'], @$searchArray['message'],$searchArray['friendship']);
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      if( APPLICATION_ENV == 'development' ) {
        throw $e;
      }
    }
    echo json_encode(array('emails_sent' => $emailsSent));
    //$this->view->alreadyMembers = $alreadyMembers;
    //$this->view->emails_sent = $emailsSent;
    die;
    //return $this->render('sent');
  }
}
