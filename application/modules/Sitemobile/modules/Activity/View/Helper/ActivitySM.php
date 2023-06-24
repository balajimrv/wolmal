<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: ActivitySM.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Activity_View_Helper_ActivitySM extends Zend_View_Helper_Abstract {

  public function activitySM(Activity_Model_Action $action = null, array $data = array(), $method = null, $show_all_comments = false) {
    if (null === $action) {
      return '';
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
            ->getAllowed('user', $viewer->level_id, 'activity');

    $form = new Activity_Form_Comment();
    $data = array_merge($data, array(
        'actions' => array($action),
        'commentForm' => $form,
        'onlyactivity' => 1,
        'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
        'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
        'activity_moderate' => $activity_moderate,
        'viewAllComments' => $show_all_comments,
            ));
    Engine_Api::_()->sitemobile()->setModuleDirectory('activity');
    if ($method == 'update') {
      return $this->view->partial(
                      '_activityComments.tpl', 'activity', $data
      );
    } else {
      return $this->view->partial(
                      '_activityText.tpl', 'activity', $data
      );
    }
  }

}