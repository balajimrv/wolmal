<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_StoreLikeButtonController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

		//GET VIEWER INFO
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $this->view->sitestore_like = $sitestore_like = Zend_Registry::isRegistered('sitestore_like') ? Zend_Registry::get('sitestore_like') : null;
    if (empty($viewer_id) || empty($sitestore_like)) {
      return $this->setNoRender();
    }
  }
}
?>