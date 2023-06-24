<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_Widget_SitestoreSponsoredalbumController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //NUMBER OF ALBUMS IN LISTING
    $totalAlbums = $this->_getParam('itemCount', 3);

    //GET ALBUM DATAS
    $params = array();
    $params['limit'] = $totalAlbums;
    $params['category_id'] = $this->_getParam('category_id',0);
    $albumType = 'sponsored';
    $this->view->recentlyview = $row = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($params,$albumType);
    $sitestorePackageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
    if ( ( Count($row) <= 0 ) || empty($sitestorePackageEnable)) {
      return $this->setNoRender();
    }
  }

}
?>