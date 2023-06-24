<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Widget_SitestoreSponsoredvideoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $getPackageVideo = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestorevideo');

    //NUMBER OF VIDEOS IN LISTING
    $totalVideos = $this->_getParam('itemCount', 3);

//     $sitestorevideo_sponsoredvideo = Zend_Registry::isRegistered('sitestorevideo_sponsoredvideo') ? Zend_Registry::get('sitestorevideo_sponsoredvideo') : null;

    //GET VIDEO DATAS
    $params = array();
    $params['limit'] = $totalVideos;
    $params['category_id'] = $this->_getParam('category_id',0);
    $videoType = 'sponsored';
    $this->view->recentlyview = $row = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->widgetVideosData($params,$videoType);
     $sitestorePackageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
    if ( ( Count($row) <= 0 ) || empty($getPackageVideo) || empty($sitestorePackageEnable) ) {
      return $this->setNoRender();
    }
  }

}
?>