<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: ProfileController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_MobileProfileController extends Seaocore_Controller_Action_Standard {

  public function init() {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $id = $this->_getParam('id');

      // use viewer ID if not specified
      //if( is_null($id) )
      //  $id = Engine_Api::_()->user()->getViewer()->getIdentity();

      if (null !== $id) {
        $subject = Engine_Api::_()->user()->getUser($id);
        if ($subject->getIdentity()) {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('user');
    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
            $subject, Engine_Api::_()->user()->getViewer(), 'view'
    );
  }

  public function indexAction() {
 
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $isCoverPhoto = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteusercoverphoto');

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
    if (!$require_check && !$this->_helper->requireUser()->isValid()) {
      return;
    }

    // Check enabled
    if (!$subject->enabled && !$viewer->isAdmin()) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    // Check block
    if ($viewer->isBlockedBy($subject) && !$viewer->isAdmin()) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if ($this->renderWidgetCustom('user_profile_index'))     
      return;
    // Increment view count
    if (!$subject->isSelf($viewer)) {
      $subject->view_count++;
      $subject->save();
    }

    if (!empty($isCoverPhoto)) {
      $isCoverAttempt = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.isattempt', false);
      $getCoverType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.get.type', false);
      $coverphotoAttemptLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteusercoverphoto.attempt.ltype', false);

      if (empty($isCoverAttempt) && $getCoverType != $coverphotoAttemptLtype) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.getalbumtype', 0);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.webcamtype', 0);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.elementinfo', 0);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteusercoverphoto.album.set', 0);
      }
    }


    // Check to see if profile styles is allowed
    $style_perm = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $subject->level_id, 'style');
    if ($style_perm) {
      // Get styles
      $table = Engine_Api::_()->getDbtable('styles', 'core');
      $select = $table->select()
              ->where('type = ?', $subject->getType())
              ->where('id = ?', $subject->getIdentity())
              ->limit();

      $row = $table->fetchRow($select);
      if (null !== $row && !empty($row->style)) {
        $this->view->headStyle()->appendStyle($row->style);
      }
    }

    // Render
    $this->_helper->content
            ->setContentName('user_profile_index')
            ->setNoRender()
            ->setEnabled()
    ;
  }

}