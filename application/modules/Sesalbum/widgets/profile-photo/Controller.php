<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Widget_ProfilePhotoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
		 if( !Engine_Api::_()->core()->hasSubject('user') ) {
				if( !$viewer->getIdentity() ) {
					return $this->setNoRender();
				}
      $this->view->widgetPlaced = 'home';
		 }else
		 	$this->view->widgetPlaced = 'member';
    // Get subject and check auth
		if( Engine_Api::_()->core()->hasSubject('user') ) {
			$subject = Engine_Api::_()->core()->getSubject('user');
			$this->view->subject = $subject;
			if($subject->authorization()->isAllowed($viewer, 'edit')){
				$this->view->canEdit = true;
				$user_id = $subject->getIdentity();
			}
			$this->view->user_id = $user_id;
		}else{
			$this->view->canEdit = true;
			$this->view->user_id = $user_id;
		}
  }
}