<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetContent.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_View_Helper_GetContent extends Zend_View_Helper_Abstract {

  protected $_shortFeedConvertTypeArray = array('birthday_post', 'post',
      'post_self', 'siteevent_post','siteevent_post_parent', 'sitepage_post', 'sitepage_post_self',
      'sitestore_post', 'sitestore_post_self', 'sitegroup_post', 'sitegroup_post_self',
      'sitebusiness_post', 'sitebusiness_post_self', 'status');

  /**
   * Assembles action string
   * 
   * @return string
   */
  public function getContent($action, $asAttachment = false, $groupedFeeds = array(), $params = array()) {
    $shortView = false;
    if (isset($params['shortView']) && $params['shortView'])
      $shortView = $params['shortView'];
    $view = Zend_Registry::get('Zend_View');

    $model = Engine_Api::_()->getApi('activity', 'advancedactivity');
    $params = array_merge(
            $action->toArray(), (array) $action->params, array(
        'subject' => $action->getSubject(),
        'object' => $action->getObject()
            )
    );
    //$content = $model->assemble($this->body, $params);
    if ($shortView && in_array($action->type, $this->_shortFeedConvertTypeArray)) {
      $content = $this->assembleShort($action);
    } else {
      $content = $model->assemble($action->getTypeInfo()->body, $params);
    }
    
    if (Engine_Api::_()->hasModuleBootstrap('sitehashtag')) {
        
        if (!Zend_Registry::isRegistered('sitehashtag_activity_content')) {
            $isEnabled = Engine_Api::_()->getDbtable('contents', 'sitehashtag')->getEnable('activity');
            Zend_Registry::set('sitehashtag_activity_content', $isEnabled);
        }
        else {
            $isEnabled = Zend_Registry::get('sitehashtag_activity_content');
        }
        
        if($isEnabled) {
            $this->makeHashLink($content, $action);
        }
    }

    if ($shortView && $content) {
      //rtrim
      if (strrpos($content, ':', -1) === (strlen($content) - 1)) {
        $content = rtrim($content, ':') . '.';
      }

      return $content;
    }
    /* Start Working group feed. */
    if (!$asAttachment) {
        if ($action->type == 'friends' || $action->type == 'tagged') {
          $subject = $action->getObject();
          $id = $action->getSubject()->getIdentity();
        } else {
          $subject = $action->getSubject();
          $id = $action->getObject()->getIdentity();
        }

        $removePattern = '<a '
                . 'class="feed_item_username sea_add_tooltip_link feed_user_title" '
                . 'rel="' . $subject->getType() . ' ' . $subject->getIdentity() . '" '
                . ( $subject->getHref() ? 'href="' . $subject->getHref() . '"' : '' )
                . '>'
                . $subject->getTitle()
                . '</a>';
        $count = count($groupedFeeds);

        $otherids = array();
        $gp = array();
        if (!empty($groupedFeeds)) {
          foreach ($groupedFeeds as $groupedFeed):
            $gp[] = $groupedFeed;
          endforeach;
        }
        for ($i = 0; $i < count($gp) - 1; $i++):
          $otherids[] = $gp[$i]->getIdentity();
        endfor;


        $ids = http_build_query(array("type" => $action->type, "ids" => $otherids), '', '&');

        if ($count == 2) :
          $new_pattern = $view->translate('%1$s and %2$s ', $view->htmlLink($gp['0']->getHref(), $gp['0']->getTitle(), array('class' => 'sea_add_tooltip_link feed_user_title feed_item_username', 'rel' => $subject->getType() . ' ' . $gp['0']->getIdentity())), $view->htmlLink($gp['1']->getHref(), $gp['1']->getTitle(), array('class' => 'sea_add_tooltip_link feed_user_title feed_item_username', 'rel' => $subject->getType() . ' ' . $gp['1']->getIdentity())));
        elseif ($count > 2) :
          $URL = $view->url(array('module' => 'advancedactivity', 'controller' => 'feed', 'action' => 'groupfeed-other-post'), 'default', true) . "?$ids";

          $otherPeoples = '<span class="aaf_feed_show_tooltip_wrapper"><a href=' . $URL . ' class="smoothbox">' . $view->translate('%s others', ($count - 1)) . '</a><span class="aaf_feed_show_tooltip" style="margin-left:-8px;"><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/images/tooltip_arrow.png" />';
          for ($i = 1; $i < count($gp); $i++):
            $otherPeoples.= $gp[$i]->getTitle() . "<br />";
          endfor;
          $otherPeoples.='</span></span>';
          $end = end($gp);
          $new_pattern = $view->translate('%1$s and %2$s ', $view->htmlLink($subject->getHref(), $subject->getTitle(), array('class' => 'sea_add_tooltip_link feed_user_title feed_item_username', 'rel' => $subject->getType() . ' ' . $subject->getIdentity())), $otherPeoples);
        endif;

        if ($count == 2 || $count > 2) {
          if (strpos($action->type, "like_") !== false) {
            $removePattern = $removePattern . $view->translate(' likes');
            $new_pattern = $new_pattern . $view->translate('like ');
          }
          if (strpos($action->type, "follow_") !== false) {
            $removePattern = $removePattern . $view->translate(' is');
            $new_pattern = $new_pattern . $view->translate('are ');
          }
        }

        if (!empty($new_pattern)) {
          $content = str_replace($removePattern, $new_pattern, $content);
        }
    }
    /* End Working group feed. */


    if ((false !== strpos($action->type, 'post')) || (false !== strpos($action->type, 'status')) || $action->type === 'sitetagcheckin_checkin' || $action->type === 'comment_sitereview_listing' || $action->type === 'comment_sitereview_review' || $action->type === 'nestedcomment_sitereview_listing' || $action->type === 'nestedcomment_sitereview_review' || (false !== strpos($action->type, 'photo'))) {
      if (!empty($action->body))
        $content = nl2br($content);

      $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("emotions", "withtags"));
      if (empty($composerOptions))
        $composerOptions = array();
      
      
      if (in_array("emotions", $composerOptions) && Zend_Registry::isRegistered('Zend_View')) {
        $view = Zend_Registry::get('Zend_View');
        $content = $view->smileyToEmoticons($content);
      }
      //$content = Engine_API::_()->seaocore()->smiley2emoticons(nl2br($content));

      $actionParams = (array) $action->params;
      if (isset($actionParams['tags'])) {
        foreach ((array) $actionParams['tags'] as $key => $tagStrValue) {

          $tag = Engine_Api::_()->getItemByGuid($key);
          if (!$tag) {
            continue;
          }
          $replaceStr = '<a class="sea_add_tooltip_link" '
                  . 'href="' . $tag->getHref() . '" '
                  . 'rel="' . $tag->getType() . ' ' . $tag->getIdentity() . '" >'
                  . $tag->getTitle()
                  . '</a>';
          $content = preg_replace("/" . preg_quote($tagStrValue) . "/", $replaceStr, $content);
        }
      }
      if (!$asAttachment && in_array("withtags", $composerOptions)) {
        $tagContent = Engine_Api::_()->advancedactivity()->getTagContent($action);
        $content .=$tagContent;
      }
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin'))
        $content = $this->view->getSitetagCheckin($action, $content);
    } else if (($action->type == 'sitetagcheckin_add_to_map') || ($action->type == 'sitetagcheckin_content') || ($action->type == 'sitetagcheckin_lct_add_to_map')) {
      $tagContent = Engine_Api::_()->advancedactivity()->getTagContent($action);
      $content .=$tagContent;
      $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("emotions", "withtags"));
      if (empty($composerOptions))
        $composerOptions = array();
      if (in_array("emotions", $composerOptions) && Zend_Registry::isRegistered('Zend_View')) {
        $view = Zend_Registry::get('Zend_View');
        $content = $view->smileyToEmoticons($content);
        $content = nl2br($content);
      }
    } else {
      $content = nl2br($content);
    }
    return $content;
  }

  public function assembleShort($action, $content = NULL) {
    $default = 'default';
    $strContent = array(
        'default' => '%s has posted something.',
        'photo' => '%s added a new photo.',
        'video' => '%s added a new video.',
        'link' => '%s added a new link.',
        'music' => '%s added a new song.',
        'post' => '%1$s has posted something on %2$s profile.',
        'post_photo' => '%1$s added a new photo on %2$s profile.',
        'post_video' => '%1$s added a new video on %2$s profile.',
        'post_link' => '%1$s added a new link on %2$s profile.',
        'post_music' => '%1$s added a new song on %2$s profile.',
        'birthday_post' => '%1$s has wish birthday  to %2$s.',
        'sitegroup_post' => '%1$s has posted something in group %2$s.',
        'sitegroup_post_photo' => '%1$s added a new photo in group %2$s.',
        'sitegroup_post_video' => '%1$s added a new video in group %2$s.',
        'sitegroup_post_link' => '%1$s added a new link in group %2$s.',
        'sitegroup_post_music' => '%1$s added a new song in group %2$s.',
        'sitepage_post' => '%1$s has posted something in page %2$s.',
        'sitepage_post_photo' => '%1$s added a new photo in page %2$s.',
        'sitepage_post_video' => '%1$s added a new video in page %2$s.',
        'sitepage_post_link' => '%1$s added a new link in page %2$s.',
        'sitepage_post_music' => '%1$s added a new song in page %2$s.',
        'sitestore_post' => '%1$s has posted something in store %2$s.',
        'sitestore_post_photo' => '%1$s added a new photo in store %2$s.',
        'sitestore_post_video' => '%1$s added a new video in store %2$s.',
        'sitestore_post_link' => '%1$s added a new link in store %2$s.',
        'sitestore_post_music' => '%1$s added a new song in store %2$s.',
        'sitebusiness_post' => '%1$s has posted something in business %2$s.',
        'sitebusiness_post_photo' => '%1$s added a new photo in business %2$s.',
        'sitebusiness_post_video' => '%1$s added a new video in business %2$s.',
        'sitebusiness_post_link' => '%1$s added a new link in business %2$s.',
        'sitebusiness_post_music' => '%1$s added a new song in business %2$s.',
        'siteevent_post' => '%1$s has posted something in event %2$s.',
        'siteevent_post_photo' => '%1$s added a new photo in event %2$s.',
        'siteevent_post_video' => '%1$s added a new video in event %2$s.',
        'siteevent_post_link' => '%1$s added a new link in event %2$s.',
        'siteevent_post_music' => '%1$s added a new song in event %2$s.',
    );
    $attachments = $action->getAttachments();
    $subject = $action->getSubject();
    $subjectLink = $this->view->htmlLink($subject->getHref(), $subject->getTitle());
    $object = $action->getObject();
    $objectLink = $this->view->htmlLink($object->getHref(), $object->getTitle());
    if ($attachments) {
      $type = $attachments[0]->item->getType();
      if (false !== strpos($type, 'photo')) {
        $default = 'photo';
      }
      // Music
      if (false !== strpos($type, 'music') ||
              false !== strpos($type, 'song')) {
        $default = 'music';
      }
      // Video
      if (false !== strpos($type, 'video')) {
        $default = 'video';
      }
      // link
      if (false !== strpos($type, 'link')) {
        $default = 'link';
      }
    }
    if (in_array($action->type, array('post_self', 'status'))) {
      $content = $this->view->translate($strContent[$default], $subjectLink);
    } else if ($action->type == 'birthday_post') {
      $content = $this->view->translate($strContent['birthday_post'], $subjectLink, $objectLink);
    } else {
      $pageSubject = Engine_Api::_()->core()->hasSubject() ? Engine_Api::_()->core()->getSubject() : null;
      if (false !== strpos($action->type, '_self') ||false !== strpos($action->type, 'siteevent_post_parent') || ($pageSubject && $pageSubject->isSelf($object))) {
        $link = $subjectLink;
        $typeInfo = $action->getTypeInfo();
        if ($typeInfo && isset($typeInfo->is_object_thumb) && $typeInfo->is_object_thumb) {
          $link = $objectLink;
        }
        $content = $this->view->translate($strContent[$default], $link);
      } else {
        $typeStr = $action->type;
        if ($default !== 'default') {
          $typeStr .="_" . $default;
        }

        if (!isset($strContent[$typeStr])) {
          $typeStr = $default;
        }
        $content = $this->view->translate($strContent[$typeStr], $subjectLink, $objectLink);
      }
    }

    return $content;
  }

  private function makeHashLink(&$body, $action) {

        preg_match_all("/\B#\w*[a-zA-Z]+\w*/", $body, $hashtags);
        $hashtags = $hashtags[0];
        if (empty($hashtags) || !$action->body) {
            return;
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $newHashTagStr = '';
        $convertStr = $body;
        foreach ($hashtags as $hashtag) {
            $find = strpos($convertStr, $hashtag);
            $substr = $find ? substr($convertStr, 0, $find) : '';
            $newHashTagStr .=$substr . $this->getHashLinkForString($hashtag, $request->getActionName());
            $convertStr = substr($convertStr, $find + strlen($hashtag));
        }
        $newHashTagStr .=$convertStr;
        $body = $newHashTagStr;
    }

    private function getHashLinkForString($hashtag, $action){

        $view = Zend_Registry::get('Zend_View');
        $url = $view->url(array('controller' => 'index', 'action' => 'index'),"sitehashtag_general")."?search=".urlencode($hashtag);

        if ($action == "share-item")
              return "<a href='$url' target='_blank'>" . urldecode($hashtag) . "</a>";
        else
              return "<a href='$url'>" . $hashtag . "</a>";
    }

}