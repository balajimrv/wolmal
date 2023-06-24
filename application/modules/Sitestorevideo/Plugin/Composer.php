<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Composer.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Plugin_Composer extends Core_Plugin_Abstract {

  public function onAttachSitestorevideo($data) {
    if (!is_array($data) || empty($data['video_id'])) {
      return;
    }

    $video = Engine_Api::_()->getItem('sitestorevideo_video', $data['video_id']);
    $filter = new Zend_Filter();
    $filter->addFilter(new Engine_Filter_Censor());
    $filter->addFilter(new Zend_Filter_StripTags());
    // update $video with new title and description
    $video->title = $filter->filter($data['title']);
    $video->description = $filter->filter($data['description']);

    // Set parents of the video
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
      if (in_array($subject->getType(), array('sitestoreevent_event'))):
        $subject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
      endif;
      $subject_type = $subject->getType();
      $subject_id = $subject->getIdentity();

      //$video->parent_type = $subject_type;
      $video->store_id = $subject_id;
    }
    $video->search = 1;
    $video->save();

    if (!($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity()) {
      return;
    }

    return $video;
  }

}