<?php

class Timeline_View_Helper_Activity extends Zend_View_Helper_Abstract
{
  public function activity(Activity_Model_Action $action = null, array $data = array())
  {
    if( null === $action )
    {
      return '';
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
        ->getAllowed('user', $viewer->level_id, 'activity');

    $form = new Activity_Form_Comment();
    if (!isset ($data['show_feature_toggle'])) {
        $tableFeature = Engine_Api::_()->getDbtable('features', 'timeline');
        if( Engine_Api::_()->core()->hasSubject() ) {
          // Get subject
          $subject = Engine_Api::_()->core()->getSubject();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !empty($subject) ) {
            $user_id = $subject->getIdentity();
            $data['show_feature_toggle'] = ($user_id == $viewer->getIdentity());
        } else {
            $user_id = $viewer->getIdentity();
            $data['show_feature_toggle'] = true;
        }
        $data['features'] = $tableFeature->fetchAll(array('user_id = ?' => $user_id));
    }
    $data = array_merge($data, array(
      'actions' => array($action),
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' =>$activity_moderate,
    ));

    return $this->view->partial(
      '_activityText.tpl',
      'timeline',
      $data
    );
  }
}