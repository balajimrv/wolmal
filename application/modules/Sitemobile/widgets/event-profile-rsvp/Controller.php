<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Widget_EventProfileRsvpController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('event');
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    // Must be a member
    if (!$subject->membership()->isMember($viewer, true)) {
      return $this->setNoRender();
    }

    // Build form
    $this->view->form = new Event_Form_Rsvp();
    $row = $subject->membership()->getRow($viewer);
    $this->view->viewer_id = $viewer->getIdentity();

    if (!$row) {
      return $this->setNoRender();
    }

    $this->view->rsvp = $row->rsvp;

    // @todo - make this work
    /*
      if( $this->getRequest()->isPost() )
      {
      $option_id = $this->getRequest()->getParam('option_id');

      $row->rsvp = $option_id;
      $row->save();
      }
     */
  }

}