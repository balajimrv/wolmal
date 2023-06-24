<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesbasic
 * @package    Sesbasic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-07-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesbasic_Widget_SavedButtonController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $module_name = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    $modul_enable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($module_name);
    if (empty($modul_enable) || empty($module_name))
      return $this->setNoRender();

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    if (empty($viewer_id))
      return $this->setNoRender();

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesevent.event.save', 1))
      return $this->setNoRender();

    $subject = Engine_Api::_()->core()->getSubject();
    $this->view->subject_type = $subject->getType();
    $this->view->subject_id = $subject->getIdentity();

    $resource = Engine_Api::_()->getItem($this->view->subject_type, $this->view->subject_id);
    $this->view->isSave = Engine_Api::_()->getDbtable('saves', 'sesbasic')->isSave($resource, $viewer);
  }

}
