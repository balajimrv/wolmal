<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_BackgroundImageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET SETTING
    $this->view->imageUrl = $this->_getParam('backgroundImage');
    if (!$this->view->imageUrl) {
      return $this->setNoRender();
    }

    if (isset($this->view->layout()->siteinfo['identity'])) {
      $identity = $this->view->layout()->siteinfo['identity'];
    } else {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $identity = $request->getModuleName() . '-' .
              $request->getControllerName() . '-' .
              $request->getActionName();
      if ($identity == 'activity-notifications-index' && $request->getParam('showrequest')):
        $identity .='-showrequest';
      endif;
    }
    $this->view->pageId = 'jqm_page_'.$identity;
  }

}

?>
