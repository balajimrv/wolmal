<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdvancedActivitySM.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_View_Helper_AdvancedActivitySM extends Zend_View_Helper_Abstract {

  public function advancedActivitySM(Activity_Model_Action $action = null, array $data = array(), $method = null, $show_all_comments = false) {
    if (null === $action) {
      return '';
    }


    $viewer = Engine_Api::_()->user()->getViewer();
//    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
//            ->getAllowed('user', $viewer->level_id, 'activity');
    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $allowEdit = 0;
    $privacyDropdownList = null;
    $activity_moderate = "";
    $add_saved_feed = false;
    $is_owner = false;
    if ($method == 'getcomment') {
      $data = array_merge($data, array(
          'actions' => array($action)
              ));
     // if (Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity')) {
       // return $this->view->partial(
                     //   'application/modules/Sitemobile/modules/Advancedactivity/views/scripts/index/getcommentsm.tpl', 'advancedactivity', $data
        //);
      //} else {
          return $this->view->partial(
                        'application/modules/Sitemobile/modules/Advancedactivity/views/scripts/index/getcomment.tpl', 'advancedactivity', $data);
     // }
    } elseif ($method == 'getreply') {
      $data = array_merge($data, array(
          'actions' => array($action)
              ));
      return $this->view->partial(
                      'application/modules/Sitemobile/modules/Advancedactivity/views/scripts/index/getreply.tpl', 'advancedactivity', $data
      );
    }
    elseif ($method == 'preloadcomments') {
      $data = array_merge($data,array(
          'actions' => array($action),
          
              ));
      return $this->view->partial(
                      'application/modules/Sitemobile/modules/Advancedactivity/views/scripts/_preloadactivityComments.tpl', 'advancedactivity', $data
      );
    }
    
    if ($viewer->getIdentity()) {
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
      if (Engine_Api::_()->core()->hasSubject() && $viewer->isSelf(Engine_Api::_()->core()->getSubject())) {
        $allowEdit = $coreSettingsApi->getSetting('advancedactivity.post.canedit', 1);
        if ($allowEdit)
          $privacyDropdownList = $this->getPrivacyDropdownList();
      }
      if (!Engine_Api::_()->core()->hasSubject()) {
        $add_saved_feed_row = Engine_Api::_()->getDbtable('contents', 'advancedactivity')->getContentList(array('content_tab' => 1, 'filter_type' => 'user_saved'));
        $add_saved_feed = !empty($add_saved_feed_row) ? true : false;
      } else {
        $subject = Engine_Api::_()->core()->getSubject();
        switch ($subject->getType()) {
          case 'user':
            $is_owner = $viewer->isSelf($subject);
            break;
          case 'sitepage_page':
          case 'sitebusiness_business':
          case 'sitegroup_group':
            $is_owner = $subject->isOwner($viewer);
            break;
          case 'sitepageevent_event':
          case 'sitebusinessevent_event':
          case 'sitegroupevent_event':
            $is_owner = $viewer->isSelf($subject);
            if (empty($is_owner)) {
              $is_owner = $subject->getParent()->isOwner($viewer);
            }
            break;
          default :
            $is_owner = $viewer->isSelf($subject->getOwner());
            break;
        }
      }
    }
    $form = new Activity_Form_Comment();
    $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("emotions", "withtags"));
    $data = array_merge($data, array(
        'actions' => array($action),
        'commentForm' => $form,
        'user_limit' => $coreSettingsApi->getSetting('activity_userlength'),
        'allow_delete' => $coreSettingsApi->getSetting('activity_userdelete'),
        'commentShowBottomPost' => $coreSettingsApi->getSetting('advancedactivity.comment.show.bottom.post', 1),
        'isMobile' => Engine_Api::_()->advancedactivity()->isMobile(),
        'allowEdit' => $allowEdit,
        'activity_moderate' => $activity_moderate,
        'onlyactivity' => 1,
        'allowEdit' => $allowEdit,
        'privacyDropdownList' => $privacyDropdownList,
        'allowEmotionsIcon' => in_array("emotions", $composerOptions),
        'allowSaveFeed' => $add_saved_feed,
        'viewAllComments' => $show_all_comments,
        'is_owner' => $is_owner,
        'showLargePhoto' => $coreSettingsApi->getSetting('aaf.largephoto.enable', 1)
            ));
    if (Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity')) {
        if ($method == 'update') {
          return $this->view->partial(
                          'application/modules/Nestedcomment/views/sitemobile/scripts/_activityComments.tpl', 'nestedcomment', $data
          );
        } else {
          return $this->view->partial(
                          'application/modules/Nestedcomment/views/sitemobile/scripts/_activityText.tpl', 'nestedcomment', $data
          );
        }
    } else {
        if ($method == 'update') {
          return $this->view->partial(
                          'application/modules/Sitemobile/modules/Advancedactivity/views/scripts/_activityComments.tpl', 'advancedactivity', $data
          );
        } else {
          return $this->view->partial(
                          'application/modules/Sitemobile/modules/Advancedactivity/views/scripts/_activityText.tpl', 'advancedactivity', $data
          );
        }
    }
  }

  protected function getPrivacyDropdownList() {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    $showPrivacyDropdown = in_array('userprivacy', $settings->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")));
    if (!$showPrivacyDropdown)
      return;

    $availableLabels = array('everyone' => 'Everyone', 'networks' => 'Friends &amp; Networks', 'friends' => 'Friends Only', 'onlyme' => 'Only Me');

    $userFriendListEnable = $settings->getSetting('user.friends.lists');
    if ($userFriendListEnable) {
      $listTable = Engine_Api::_()->getItemTable('user_list');
      $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
      $countList = count($lists);
      if ($countList > 0) {
        $availableLabels[] = "separator";
        foreach ($lists as $list) {
          $availableLabels[$list->list_id] = $list->title;
        }
      }
    }

    $enableNetworkList = $settings->getSetting('advancedactivity.networklist.privacy', 0);
    if ($enableNetworkList) {
      $this->view->network_lists = $networkLists = Engine_Api::_()->advancedactivity()->getNetworks($enableNetworkList, $viewer);
      $this->view->enableNetworkList = count($networkLists);

      if ($this->view->enableNetworkList) {
        $availableLabels[] = "separator";
        foreach ($networkLists as $network) {
          $availableLabels["network_" . $network->getIdentity()] = $network->getTitle();
        }
      }
    }
//    if ($this->view->enableNetworkList > 1) {
//      $availableLabels[] = "separator";
//      $availableLabels["network_custom"] = "Multiple Networks";
//    }
//    if ($userFriendListEnable) {
//      if ($this->view->enableNetworkList <= 1)
//        $availableLabels[] = "separator";
//      $lable = $this->view->enableNetworkList <= 1 ? "Custom" : "Multiple Friend Lists";
//      if ($countList == 1)
//        $availableLabels["custom_1"] = $lable;
//      else if ($countList > 1)
//        $availableLabels["custom_2"] = $lable;
//      else
//        $availableLabels["custom_0"] = $lable;
//    }

    return $availableLabels;
  }

}