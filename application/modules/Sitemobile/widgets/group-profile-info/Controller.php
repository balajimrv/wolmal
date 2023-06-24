<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Widget_GroupProfileInfoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('group');
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    // Get staff
    $ids = array();
    $ids[] = $subject->getOwner()->getIdentity();
    $list = $subject->getOfficerList();
    foreach ($list->getAll() as $listiteminfo) {
      $ids[] = $listiteminfo->child_id;
    }

    $staff = array();
    foreach ($ids as $id) {
      $user = Engine_Api::_()->getItem('user', $id);
      $staff[] = array(
          'membership' => $subject->membership()->getMemberInfo($user),
          'user' => $user,
      );
    }

    $this->view->group = $subject;
    $this->view->staff = $staff;
    $defaultCollapsible = 1;
    $defaultgroupInfoCollapsible = 1;
    if (Engine_Api::_()->sitemobile()->isApp()) {
      $defaultCollapsible = 0;
      $defaultgroupInfoCollapsible = 0;
    } 
    $this->view->group_info_collapsible = $this->_getParam("groupInfoCollapsible", $defaultCollapsible);
    $this->view->group_info_collapsible_default = $this->_getParam("groupInfoCollapsibleDefault", $defaultgroupInfoCollapsible);
  }

}