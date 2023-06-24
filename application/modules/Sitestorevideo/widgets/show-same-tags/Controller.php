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
class Sitestorevideo_Widget_ShowSameTagsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
     $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);

    if (empty($sitestorevideo)) {
      return $this->setNoRender();
    }

     //GET SUBJECT
    $subject = Engine_Api::_()->getItem('sitestore_store', $sitestorevideo->store_id);

    //GET TAB ID
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
    $this->view->store_id = $sitestorevideo->store_id;
    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorevideo")) {
        return $this->setNoRender();
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'svcreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->setNoRender();
      }
    }
//     $sitestorevideo_getlike = Zend_Registry::isRegistered('sitestorevideo_getlike') ? Zend_Registry::get('sitestorevideo_getlike') : null;
//     if (empty($sitestorevideo_getlike)) {
//       return $this->setNoRender();
//     }
    // PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //FETCH VIDEOS
    $params = array();
    $widgetType = 'showsametag';
    $params['view_action'] = 1;
    $params['resource_type'] = $sitestorevideo->getType();
    $params['resource_id'] = $sitestorevideo->getIdentity();
    $params['video_id'] = $sitestorevideo->getIdentity();
    $params['limit'] = $this->_getParam('itemCount', 3);

		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->widgetVideosData($params,'',$widgetType);
    $this->view->count_video = Count($paginator);
    $this->view->limit_sitestorevideo = $this->_getParam('itemCount', 3);

    if( Count($paginator) <= 0 ) {
      return $this->setNoRender();
    }
  }
}